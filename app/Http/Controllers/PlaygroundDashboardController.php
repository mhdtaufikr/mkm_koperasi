<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PlaygroundDashboardController extends Controller
{
    public function upload()
    {
        $payload = $this->loadPayload();

        return view('playground.upload', [
            'rawData' => request()->input('raw_data', $payload['raw_data']),
            'participation' => $payload['participation'],
        ]);
    }

    public function store(Request $request)
    {
        $rawData = trim((string) $request->input('raw_data', '')) ?: $this->defaultRawData();
        $payload = $this->parseRawData($rawData);

        $payload['participation'] = [
            'pertokoan' => [
                'label' => 'Pertokoan',
                'active' => $this->positiveInt($request->input('pertokoan_active')),
                'total' => $this->positiveInt($request->input('pertokoan_total')),
            ],
            'pinjaman' => [
                'label' => 'Pinjaman',
                'active' => $this->positiveInt($request->input('pinjaman_active')),
                'total' => $this->positiveInt($request->input('pinjaman_total')),
            ],
        ];

        foreach ($payload['participation'] as $key => $item) {
            $payload['participation'][$key]['rate'] = $item['total'] > 0
                ? round($item['active'] / $item['total'] * 100, 2)
                : 0;
        }

        $this->savePayload($payload);

        return redirect()->route('playground.dashboard', ['saved' => 1]);
    }

    public function dashboard()
    {
        $payload = $this->loadPayload();

        return view('playground.dashboard', $payload);
    }

    private function loadPayload(): array
    {
        if (!$this->hasPlaygroundTables()) {
            return $this->defaultPayload();
        }

        $rawData = DB::table('playground_dashboard_inputs')->where('id', 1)->value('raw_data') ?: $this->defaultRawData();
        $ratios = DB::table('playground_financial_ratios')
            ->orderBy('sort_order')
            ->get()
            ->map(function ($row) {
                return [
                    'category' => $row->category,
                    'ratio' => $row->ratio,
                    'value_2025' => (float) $row->value_2025,
                    'value_2024' => (float) $row->value_2024,
                    'diff' => (float) $row->diff,
                ];
            })
            ->all();

        $financials = DB::table('playground_financial_items')
            ->orderBy('sort_order')
            ->get()
            ->map(function ($row) {
                return [
                    'label' => $row->label,
                    'value_2025' => (float) $row->value_2025,
                    'value_2024' => (float) $row->value_2024,
                ];
            })
            ->all();

        $participation = DB::table('playground_participations')
            ->orderBy('sort_order')
            ->get()
            ->mapWithKeys(function ($row) {
                $key = strtolower($row->category) === 'pinjaman' ? 'pinjaman' : 'pertokoan';
                $total = (int) $row->total_members;

                return [
                    $key => [
                        'label' => $row->category,
                        'active' => (int) $row->active_members,
                        'total' => $total,
                        'rate' => $total > 0 ? round($row->active_members / $total * 100, 2) : 0,
                    ],
                ];
            })
            ->all();

        if (empty($ratios) || empty($financials)) {
            return $this->defaultPayload();
        }

        return [
            'raw_data' => $rawData,
            'ratios' => $ratios,
            'financials' => $financials,
            'participation' => array_replace($this->defaultPayload()['participation'], $participation),
            'summary' => $this->buildSummary($ratios, $financials),
            'insights' => $this->buildInsights($ratios, $financials),
        ];
    }

    private function savePayload(array $payload): void
    {
        if (!$this->hasPlaygroundTables()) {
            return;
        }

        DB::transaction(function () use ($payload) {
            $now = now();

            DB::table('playground_dashboard_inputs')->updateOrInsert(
                ['id' => 1],
                [
                    'raw_data' => $payload['raw_data'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            foreach ($payload['ratios'] as $index => $ratio) {
                DB::table('playground_financial_ratios')->updateOrInsert(
                    ['ratio' => $ratio['ratio']],
                    [
                        'category' => $ratio['category'],
                        'value_2025' => $ratio['value_2025'],
                        'value_2024' => $ratio['value_2024'],
                        'diff' => $ratio['diff'],
                        'sort_order' => $index + 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }

            foreach ($payload['financials'] as $index => $item) {
                DB::table('playground_financial_items')->updateOrInsert(
                    ['label' => $item['label']],
                    [
                        'value_2025' => $item['value_2025'],
                        'value_2024' => $item['value_2024'],
                        'sort_order' => $index + 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }

            foreach ($payload['participation'] as $index => $item) {
                DB::table('playground_participations')->updateOrInsert(
                    ['category' => $item['label']],
                    [
                        'active_members' => $item['active'],
                        'total_members' => $item['total'],
                        'sort_order' => $index + 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
        });
    }

    private function hasPlaygroundTables(): bool
    {
        return Schema::hasTable('playground_dashboard_inputs')
            && Schema::hasTable('playground_financial_ratios')
            && Schema::hasTable('playground_financial_items')
            && Schema::hasTable('playground_participations');
    }

    private function positiveInt($value): int
    {
        return max(0, (int) $value);
    }

    private function parseRawData(string $rawData): array
    {
        $tables = $this->extractTables($rawData);
        $ratios = [];
        $financials = [];

        foreach ($tables as $table) {
            if (empty($table)) {
                continue;
            }

            $header = array_map(fn ($value) => strtolower(trim($value)), $table[0]);
            $headerText = implode(' ', $header);

            if (strpos($headerText, 'kategori') !== false && strpos($headerText, 'rasio') !== false) {
                $ratios = $this->parseRatios($table);
            }

            if (strpos($headerText, 'keterangan') !== false) {
                $financials = $this->parseFinancials($table);
            }
        }

        $payload = [
            'raw_data' => $rawData,
            'ratios' => $ratios,
            'financials' => $financials,
            'participation' => [
                'pertokoan' => ['label' => 'Pertokoan', 'active' => 0, 'total' => 0, 'rate' => 0],
                'pinjaman' => ['label' => 'Pinjaman', 'active' => 0, 'total' => 0, 'rate' => 0],
            ],
        ];

        $payload['summary'] = $this->buildSummary($payload['ratios'], $payload['financials']);
        $payload['insights'] = $this->buildInsights($payload['ratios'], $payload['financials']);

        return $payload;
    }

    private function extractTables(string $rawData): array
    {
        $tables = [];
        $current = [];

        foreach (preg_split('/\R/', $rawData) as $line) {
            $line = trim($line);

            if ($line === '') {
                if (!empty($current)) {
                    $tables[] = $current;
                    $current = [];
                }
                continue;
            }

            if (preg_match('/^\|?\s*-{2,}/', $line)) {
                continue;
            }

            if (strpos($line, '|') !== false) {
                $cells = array_map('trim', array_filter(explode('|', trim($line, '|')), fn ($cell) => trim($cell) !== ''));
            } elseif (strpos($line, "\t") !== false) {
                $cells = array_map('trim', explode("\t", $line));
            } else {
                continue;
            }

            if (count($cells) >= 3) {
                $current[] = $cells;
            }
        }

        if (!empty($current)) {
            $tables[] = $current;
        }

        return $tables;
    }

    private function parseRatios(array $table): array
    {
        $rows = [];

        foreach (array_slice($table, 1) as $row) {
            if (count($row) < 5 || stripos($row[0], 'kategori') !== false) {
                continue;
            }

            $rows[] = [
                'category' => $row[0],
                'ratio' => $row[1],
                'value_2025' => $this->parsePercent($row[2]),
                'value_2024' => $this->parsePercent($row[3]),
                'diff' => $this->parsePercent($row[4]),
            ];
        }

        return $rows;
    }

    private function parseFinancials(array $table): array
    {
        $rows = [];

        foreach (array_slice($table, 1) as $row) {
            if (count($row) < 3 || stripos($row[0], 'keterangan') !== false) {
                continue;
            }

            $rows[] = [
                'label' => $row[0],
                'value_2025' => $this->parseNumber($row[1]),
                'value_2024' => $this->parseNumber($row[2]),
            ];
        }

        return $rows;
    }

    private function parsePercent(string $value): float
    {
        return (float) str_replace(',', '.', preg_replace('/[^0-9,\.\-]/', '', $value));
    }

    private function parseNumber(string $value): int
    {
        return (int) preg_replace('/[^0-9\-]/', '', $value);
    }

    private function buildSummary(array $ratios, array $financials): array
    {
        $findMoney = function (string $needle) use ($financials) {
            foreach ($financials as $row) {
                if (stripos($row['label'], $needle) !== false) {
                    return $row;
                }
            }

            return ['value_2025' => 0, 'value_2024' => 0];
        };

        $findRatio = function (string $needle) use ($ratios) {
            foreach ($ratios as $row) {
                if (stripos($row['ratio'], $needle) !== false) {
                    return $row;
                }
            }

            return ['value_2025' => 0, 'value_2024' => 0, 'diff' => 0];
        };

        return [
            'revenue' => $findMoney('Pendapatan'),
            'assets' => $findMoney('Total Aset'),
            'operating_profit' => $findMoney('Laba Usaha'),
            'net_profit' => $findMoney('Laba Bersih'),
            'net_margin' => $findRatio('Net Profit Margin'),
        ];
    }

    private function buildInsights(array $ratios, array $financials): array
    {
        $insights = [];

        foreach ($ratios as $row) {
            if ($row['category'] === 'Liquidity' && stripos($row['ratio'], 'Current') !== false) {
                $insights[] = 'Likuiditas sangat kuat dengan Current Ratio ' . $this->formatPercent($row['value_2025']) . '.';
            }

            if ($row['category'] === 'Leverage') {
                $insights[] = 'Struktur pendanaan membaik karena Debt to Equity Ratio turun ' . $this->formatPercent(abs($row['diff'])) . ' poin.';
            }

            if ($row['category'] === 'Activity') {
                $insights[] = 'Efisiensi aset perlu perhatian karena Total Assets Turn Over menurun.';
            }

            if (stripos($row['ratio'], 'Net Profit Margin') !== false) {
                $insights[] = 'Kualitas laba membaik, Net Profit Margin naik menjadi ' . $this->formatPercent($row['value_2025']) . '.';
            }
        }

        $revenue = $this->buildSummary($ratios, $financials)['revenue'];
        $netProfit = $this->buildSummary($ratios, $financials)['net_profit'];
        if (($revenue['value_2024'] ?? 0) > 0 && ($netProfit['value_2024'] ?? 0) > 0) {
            $revenueDrop = (($revenue['value_2025'] - $revenue['value_2024']) / $revenue['value_2024']) * 100;
            $profitDrop = (($netProfit['value_2025'] - $netProfit['value_2024']) / $netProfit['value_2024']) * 100;
            $insights[] = 'Pendapatan turun ' . $this->formatPercent(abs($revenueDrop)) . ', namun laba bersih hanya turun ' . $this->formatPercent(abs($profitDrop)) . '.';
        }

        return array_slice($insights, 0, 5);
    }

    private function defaultPayload(): array
    {
        $payload = $this->parseRawData($this->defaultRawDataWithoutRecursion());
        $payload['participation'] = [
            'pertokoan' => ['label' => 'Pertokoan', 'active' => 145, 'total' => 220, 'rate' => 65.91],
            'pinjaman' => ['label' => 'Pinjaman', 'active' => 128, 'total' => 220, 'rate' => 58.18],
        ];

        return $payload;
    }

    private function defaultRawData(): string
    {
        return $this->defaultRawDataWithoutRecursion();
    }

    private function defaultRawDataWithoutRecursion(): string
    {
        return <<<'RAW'
Kategori	Rasio	2025	2024	Selisih
Liquidity	Current Ratio	316.18%	289.57%	26.61%
Liquidity	Acid Test Ratio	10.58%	22.23%	-11.65%
Leverage	Debt to Equity Ratio	121.58%	159.15%	-37.57%
Activity	Total Assets Turn Over	27.99%	37.91%	-9.92%
Profitability	Gross Profit Margin	26.93%	23.70%	3.23%
Profitability	Operating Income Ratio	10.03%	9.94%	0.09%
Profitability	Operating Ratio	16.90%	13.76%	3.14%
Profitability	Net Profit Margin	10.43%	9.00%	1.43%

Keterangan	2025	2024
Aset Lancar	20.569.298.877	20.130.969.673
Kewajiban Jangka Pendek	6.505.518.557	6.951.906.805
Total Kewajiban	6.505.518.557	6.951.906.805
Total Ekuitas	5.350.855.568	4.368.104.808
Pendapatan	5.777.378.020	7.631.595.408
Total Aset	20.641.300.389	20.130.969.673
Laba Kotor	1.555.561.976	1.808.817.977
Laba Usaha	579.434.671	758.730.805
Beban Pokok + Operasional	976.127.304	1.050.087.172
Laba Bersih Tahun Berjalan	602.841.058	686.721.098
RAW;
    }

    private function formatPercent(float $value): string
    {
        return number_format($value, 2, ',', '.') . '%';
    }
}
