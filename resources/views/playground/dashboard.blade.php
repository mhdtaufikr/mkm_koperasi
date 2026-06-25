@extends('layouts.playground')

@section('title', 'Dashboard Rasio Keuangan')
@section('fullscreen', true)

@section('content')
@php
    $percent = fn ($value) => number_format((float) $value, 2, ',', '.') . '%';
    $shortMoney = function ($value, $digits = 3) {
        $value = (float) $value;
        if (abs($value) >= 1000000000) {
            return number_format($value / 1000000000, 3, ',', '.') . ' M';
        }
        if (abs($value) >= 1000000) {
            return number_format($value / 1000000, 1, ',', '.') . ' Jt';
        }
        return number_format($value, 0, ',', '.');
    };
    $moneyChange = function ($row) {
        $base = (float) ($row['value_2024'] ?? 0);
        return $base != 0 ? (($row['value_2025'] - $row['value_2024']) / $base) * 100 : 0;
    };
    $findRatio = function ($needle) use ($ratios) {
        foreach ($ratios as $row) {
            if (stripos($row['ratio'], $needle) !== false) {
                return $row;
            }
        }
        return ['category' => '', 'ratio' => $needle, 'value_2025' => 0, 'value_2024' => 0, 'diff' => 0];
    };
    $findMoney = function ($needle) use ($financials) {
        foreach ($financials as $row) {
            if (stripos($row['label'], $needle) !== false) {
                return $row;
            }
        }
        return ['label' => $needle, 'value_2025' => 0, 'value_2024' => 0];
    };
    $currentRatio = $findRatio('Current Ratio');
    $acidRatio = $findRatio('Acid Test Ratio');
    $derRatio = $findRatio('Debt to Equity');
    $assetTurnover = $findRatio('Total Assets Turn Over');
    $gpm = $findRatio('Gross Profit');
    $oir = $findRatio('Operating Income');
    $or = $findRatio('Operating Ratio');
    $npm = $findRatio('Net Profit');
    $revenue = $findMoney('Pendapatan');
    $asset = $findMoney('Total Aset');
    $liability = $findMoney('Total Kewajiban');
    $equity = $findMoney('Total Ekuitas');
    $operatingProfit = $findMoney('Laba Usaha');
    $netProfit = $findMoney('Laba Bersih');
    $revenueChange = $moneyChange($revenue);
    $profitChange = $moneyChange($netProfit);
    $assetChange = $moneyChange($asset);
    $operatingProfitChange = $moneyChange($operatingProfit);
    $mainRows = [
        ['icon' => 'bar-chart-3', 'param' => 'TOTAL BISNIS (PENDAPATAN)', 'row' => $revenue, 'change' => $revenueChange, 'analysis' => 'Skala bisnis menurun'],
        ['icon' => 'settings', 'param' => 'EFISIENSI OPERASI (OPERATING RATIO)', 'ratio' => $or, 'analysis' => 'Biaya operasi terkendali'],
        ['icon' => 'circle-dollar-sign', 'param' => 'LABA USAHA (OPERATING PROFIT)', 'row' => $operatingProfit, 'change' => $operatingProfitChange, 'analysis' => 'Penurunan lebih kecil dari pendapatan'],
        ['icon' => 'trending-up', 'param' => 'LABA BERSIH (NET PROFIT)', 'row' => $netProfit, 'change' => $profitChange, 'analysis' => 'Turun lebih kecil dibanding pendapatan'],
        ['icon' => 'percent', 'param' => 'NET PROFIT MARGIN', 'ratio' => $npm, 'analysis' => 'Kualitas laba meningkat'],
    ];
    $categoryMeta = [
        'Liquidity' => ['title' => 'I. LIKUIDITAS', 'color' => 'blue', 'icon' => 'droplets'],
        'Leverage' => ['title' => 'II. LEVERAGE', 'color' => 'green', 'icon' => 'scale'],
        'Activity' => ['title' => 'III. AKTIVITAS', 'color' => 'purple', 'icon' => 'refresh-cw'],
        'Profitability' => ['title' => 'IV. PROFITABILITAS', 'color' => 'orange', 'icon' => 'chart-no-axes-combined'],
    ];
    $ratioGroups = collect($ratios)->groupBy('category');
@endphp

<style>
    .sheet { min-height: calc(100vh - 1rem); }
    .panel { border: 1px solid #d9e2ef; background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(15, 23, 42, .08); }
    .blue-title { color: #073574; }
    .tiny { font-size: 10px; line-height: 1.25; }
    .micro { font-size: 9px; line-height: 1.2; }
    .kpi-value { font-size: clamp(17px, 1.55vw, 26px); }
    .ratio-value { font-size: clamp(18px, 1.65vw, 28px); }
    .mini-chart { height: 82px; position: relative; }
    .trend-chart { height: 210px; position: relative; }
    .compare-chart { height: 205px; position: relative; }
    .detail-drawer { transition: all .2s ease; }
    @media (max-width: 1100px) {
        .sheet { min-height: auto; }
        .mini-chart { height: 105px; }
        .trend-chart, .compare-chart { height: 260px; }
    }
</style>

<section class="sheet w-full bg-white">
    <div class="grid gap-2 xl:grid-cols-[1.06fr_1.48fr_1.06fr]">
        <header class="panel xl:col-span-3 overflow-hidden">
            <div class="grid gap-2 p-3 xl:grid-cols-[1.15fr_0.82fr_0.72fr] xl:items-center">
                <div>
                    <h1 class="text-3xl font-black leading-none tracking-tight text-blue-950 md:text-4xl">DASHBOARD RASIO KEUANGAN</h1>
                    <p class="mt-1 text-lg font-semibold text-slate-600">Perbandingan Kinerja Keuangan 2025 vs 2024</p>
                </div>
                <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-2">
                    <div class="flex items-center gap-3">
                        <i data-lucide="scale" class="h-10 w-10 text-blue-950"></i>
                        <div>
                            <p class="text-sm font-black text-blue-950">PERBEDAAN PENTING PERBANDINGAN</p>
                            <p class="tiny text-slate-700">2024 belum membebankan Pajak Badan.</p>
                            <p class="tiny font-bold text-red-600">2025 sudah membebankan Pajak Badan termasuk beban pajak tahun 2024.</p>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div class="rounded-lg bg-blue-950 px-4 py-2 text-center text-white">
                        <p class="text-2xl font-black">2025</p>
                        <p class="micro">(Sudah termasuk Pajak Badan & Beban Pajak 2024)</p>
                    </div>
                    <div class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-center">
                        <p class="text-2xl font-black text-slate-700">2024</p>
                        <p class="micro text-slate-600">(Belum termasuk Pajak Badan)</p>
                    </div>
                </div>
            </div>
        </header>

        <section class="xl:col-span-3 grid gap-2 xl:grid-cols-[160px_repeat(5,minmax(0,1fr))]">
            <div class="rounded-lg bg-blue-950 p-4 text-white shadow-sm">
                <div class="flex h-full items-center gap-3">
                    <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full border-2 border-white/80">
                        <i data-lucide="bar-chart-3" class="h-8 w-8"></i>
                    </span>
                    <p class="text-base font-black leading-tight">RINGKASAN<br>KINERJA<br>UTAMA</p>
                </div>
            </div>
            @foreach([
                ['icon' => 'bar-chart-3', 'label' => 'Total Pendapatan', 'value' => $shortMoney($revenue['value_2025']), 'change' => $revenueChange, 'sub' => 'vs 2024: ' . $shortMoney($revenue['value_2024'])],
                ['icon' => 'building-2', 'label' => 'Total Aset', 'value' => $shortMoney($asset['value_2025']), 'change' => $assetChange, 'sub' => 'vs 2024: ' . $shortMoney($asset['value_2024'])],
                ['icon' => 'circle-dollar-sign', 'label' => 'Laba (Rugi) Usaha', 'value' => $shortMoney($operatingProfit['value_2025']), 'change' => $operatingProfitChange, 'sub' => 'vs 2024: ' . $shortMoney($operatingProfit['value_2024'])],
                ['icon' => 'trending-up', 'label' => 'Laba (Rugi) Bersih', 'value' => $shortMoney($netProfit['value_2025']), 'change' => $profitChange, 'sub' => 'vs 2024: ' . $shortMoney($netProfit['value_2024'])],
                ['icon' => 'percent', 'label' => 'Net Profit Margin', 'value' => $percent($npm['value_2025']), 'change' => $npm['diff'], 'sub' => 'vs 2024: ' . $percent($npm['value_2024'])],
            ] as $item)
                <div class="panel p-3">
                    <div class="flex items-center gap-3">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-blue-700 text-white">
                            <i data-lucide="{{ $item['icon'] }}" class="h-6 w-6"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="tiny font-black text-slate-600">{{ $item['label'] }}</p>
                            <p class="kpi-value font-black text-blue-900">{{ $item['value'] }}</p>
                            <p class="{{ $item['change'] >= 0 ? 'text-emerald-700' : 'text-red-600' }} text-sm font-black">{{ $item['change'] >= 0 ? '+' : '' }}{{ $percent($item['change']) }}</p>
                            <p class="tiny font-bold text-slate-600">{{ $item['sub'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </section>

        <div class="space-y-2">
            <section class="panel p-3">
                <div class="mb-2 flex items-center gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-700 text-white"><i data-lucide="droplets" class="h-5 w-5"></i></span>
                    <h2 class="font-black text-blue-800">I. LIKUIDITAS</h2>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    @foreach([$currentRatio, $acidRatio] as $idx => $row)
                        <div class="border-r border-slate-200 pr-2 last:border-r-0">
                            <p class="tiny text-center font-black text-slate-700">{{ $row['ratio'] }}</p>
                            <p class="ratio-value text-center font-black text-blue-900">{{ $percent($row['value_2025']) }}</p>
                            <p class="{{ $row['diff'] >= 0 ? 'text-emerald-700' : 'text-red-600' }} text-center text-sm font-black">{{ $row['diff'] >= 0 ? '+' : '' }}{{ $percent($row['diff']) }} p.p</p>
                            <p class="tiny text-center font-bold text-slate-600">vs 2024: {{ $percent($row['value_2024']) }}</p>
                            <div class="mini-chart"><canvas id="liq{{ $idx }}"></canvas></div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="panel p-3">
                <div class="mb-2 flex items-center gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-green-700 text-white"><i data-lucide="scale" class="h-5 w-5"></i></span>
                    <h2 class="font-black text-green-800">II. LEVERAGE</h2>
                </div>
                <div class="grid grid-cols-[1fr_0.9fr] gap-3">
                    <div>
                        <p class="tiny text-center font-black text-slate-700">Total Debt to Equity Ratio</p>
                        <p class="ratio-value text-center font-black text-blue-900">{{ $percent($derRatio['value_2025']) }}</p>
                        <p class="text-center text-sm font-black text-red-600">{{ $percent($derRatio['diff']) }} p.p</p>
                        <p class="tiny text-center font-bold text-slate-600">vs 2024: {{ $percent($derRatio['value_2024']) }}</p>
                        <div class="mini-chart"><canvas id="leverageChart"></canvas></div>
                    </div>
                    <div class="space-y-2 border-l border-slate-200 pl-3">
                        <div>
                            <p class="tiny font-black text-slate-600">Total Kewajiban (2025)</p>
                            <p class="text-lg font-black text-blue-900">{{ $shortMoney($liability['value_2025']) }}</p>
                            <p class="tiny text-red-600">vs 2024: {{ $shortMoney($liability['value_2024']) }}</p>
                        </div>
                        <div class="border-t border-slate-200 pt-2">
                            <p class="tiny font-black text-slate-600">Total Ekuitas (2025)</p>
                            <p class="text-lg font-black text-blue-900">{{ $shortMoney($equity['value_2025']) }}</p>
                            <p class="tiny text-emerald-700">vs 2024: {{ $shortMoney($equity['value_2024']) }}</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <main class="space-y-2">
            <div class="rounded-lg bg-blue-950 px-4 py-3 text-center text-yellow-300 shadow-sm">
                <div class="flex items-center justify-center gap-3">
                    <i data-lucide="star" class="h-9 w-9 fill-yellow-300"></i>
                    <p class="text-lg font-black leading-tight">PENDAPATAN TURUN {{ $percent(abs($revenueChange)) }}, LABA BERSIH HANYA TURUN {{ $percent(abs($profitChange)) }}<br>DENGAN BEBAN PAJAK BADAN TERMASUK PAJAK 2024 YANG DIBEBANKAN DI TAHUN 2025</p>
                </div>
            </div>

            <section class="panel overflow-hidden">
                <h2 class="border-b border-slate-200 py-2 text-center text-sm font-black text-blue-800">PERBANDINGAN KINERJA UTAMA</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-center text-xs">
                        <thead class="bg-slate-50 text-[10px] font-black text-slate-700">
                            <tr>
                                <th class="px-2 py-2 text-left">PARAMETER</th>
                                <th class="px-2 py-2">2024<br><span class="font-semibold">(Belum Pajak Badan)</span></th>
                                <th class="px-2 py-2"></th>
                                <th class="px-2 py-2">2025<br><span class="font-semibold">(Sudah Pajak Badan)</span></th>
                                <th class="px-2 py-2">PERUBAHAN</th>
                                <th class="px-2 py-2 text-left">ANALISIS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($mainRows as $row)
                                @php
                                    $isRatio = isset($row['ratio']);
                                    $change = $isRatio ? $row['ratio']['diff'] : $row['change'];
                                @endphp
                                <tr class="{{ $loop->last ? 'bg-emerald-50' : '' }}">
                                    <td class="px-2 py-2 text-left font-black text-blue-900">
                                        <div class="flex items-center gap-2">
                                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-700 text-white"><i data-lucide="{{ $row['icon'] }}" class="h-4 w-4"></i></span>
                                            <span>{{ $row['param'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-2 py-2 font-black">{{ $isRatio ? $percent($row['ratio']['value_2024']) : $shortMoney($row['row']['value_2024']) }}</td>
                                    <td class="px-2 py-2 text-xl font-black text-blue-900">→</td>
                                    <td class="px-2 py-2 font-black text-blue-900">{{ $isRatio ? $percent($row['ratio']['value_2025']) : $shortMoney($row['row']['value_2025']) }}</td>
                                    <td class="{{ $change >= 0 ? 'text-emerald-700' : 'text-red-600' }} px-2 py-2 font-black">{{ $change >= 0 ? '+' : '' }}{{ $percent($change) }}</td>
                                    <td class="px-2 py-2 text-left font-semibold text-slate-700">{{ $row['analysis'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="barDetail" class="detail-drawer panel border-blue-300 p-3">
                <div class="flex items-center gap-3">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-blue-900 text-white"><i data-lucide="trophy" class="h-6 w-6"></i></span>
                    <div>
                        <p class="text-sm font-black text-blue-900">KESIMPULAN UTAMA</p>
                        <p class="text-xs font-semibold leading-5 text-slate-700">Walaupun pendapatan turun {{ $percent(abs($revenueChange)) }}, perusahaan tetap mampu menjaga efisiensi dan Net Profit Margin naik menjadi {{ $percent($npm['value_2025']) }}.</p>
                    </div>
                </div>
            </section>
        </main>

        <div class="space-y-2">
            <section class="panel p-3">
                <div class="mb-2 flex items-center gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-green-700 text-white"><i data-lucide="refresh-cw" class="h-5 w-5"></i></span>
                    <h2 class="font-black text-blue-800">III. AKTIVITAS</h2>
                </div>
                <div class="grid grid-cols-[0.95fr_1fr] gap-3">
                    <div>
                        <p class="tiny text-center font-black text-slate-700">{{ $assetTurnover['ratio'] }}</p>
                        <p class="ratio-value text-center font-black text-blue-900">{{ $percent($assetTurnover['value_2025']) }}</p>
                        <p class="text-center text-sm font-black text-red-600">{{ $percent($assetTurnover['diff']) }} p.p</p>
                        <p class="tiny text-center font-bold text-slate-600">vs 2024: {{ $percent($assetTurnover['value_2024']) }}</p>
                    </div>
                    <div class="mini-chart"><canvas id="activityChart"></canvas></div>
                </div>
            </section>

            <section class="panel p-3">
                <div class="mb-2 flex items-center gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-green-700 text-white"><i data-lucide="chart-no-axes-combined" class="h-5 w-5"></i></span>
                    <h2 class="font-black text-blue-800">IV. PROFITABILITAS</h2>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    @foreach([$gpm, $oir, $or, $npm] as $idx => $row)
                        <div class="border-b border-r border-slate-200 p-1 even:border-r-0 [&:nth-last-child(-n+2)]:border-b-0">
                            <p class="tiny text-center font-black text-slate-700">{{ $row['ratio'] }}</p>
                            <p class="text-center text-xl font-black text-blue-900">{{ $percent($row['value_2025']) }}</p>
                            <p class="{{ $row['diff'] >= 0 ? 'text-emerald-700' : 'text-red-600' }} text-center tiny font-black">{{ $row['diff'] >= 0 ? '+' : '' }}{{ $percent($row['diff']) }} p.p</p>
                            <p class="micro text-center font-bold text-slate-600">vs 2024: {{ $percent($row['value_2024']) }}</p>
                            <div class="mini-chart"><canvas id="profit{{ $idx }}"></canvas></div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <section class="panel overflow-hidden">
            <h2 class="bg-blue-950 px-3 py-2 text-sm font-black text-white">RINGKASAN RASIO KEUANGAN</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-blue-900 text-white">
                        <tr>
                            <th class="px-2 py-2 text-left">Kategori</th>
                            <th class="px-2 py-2 text-left">Rasio</th>
                            <th class="px-2 py-2 text-right">2025</th>
                            <th class="px-2 py-2 text-right">2024</th>
                            <th class="px-2 py-2 text-right">Selisih</th>
                            <th class="px-2 py-2 text-center">Trend</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($ratios as $row)
                            <tr>
                                <td class="px-2 py-2 font-black text-blue-800">{{ strtoupper($row['category']) }}</td>
                                <td class="px-2 py-2 font-semibold">{{ $row['ratio'] }}</td>
                                <td class="px-2 py-2 text-right font-bold">{{ $percent($row['value_2025']) }}</td>
                                <td class="px-2 py-2 text-right">{{ $percent($row['value_2024']) }}</td>
                                <td class="{{ $row['diff'] >= 0 ? 'text-emerald-700' : 'text-red-600' }} px-2 py-2 text-right font-black">{{ $row['diff'] >= 0 ? '+' : '' }}{{ $percent($row['diff']) }}</td>
                                <td class="px-2 py-2 text-center"><i data-lucide="{{ $row['diff'] >= 0 ? 'arrow-up' : 'arrow-down' }}" class="{{ $row['diff'] >= 0 ? 'text-emerald-700' : 'text-red-600' }} mx-auto h-4 w-4"></i></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="panel p-3">
            <h2 class="text-center text-sm font-black text-blue-800">PERBANDINGAN TOTAL BISNIS & LABA BERSIH</h2>
            <div class="compare-chart"><canvas id="financialChart"></canvas></div>
        </section>

        <section class="panel overflow-hidden">
            <h2 class="bg-blue-950 px-3 py-2 text-sm font-black text-white">INSIGHT UTAMA</h2>
            <div class="space-y-2 p-3">
                @foreach($insights as $idx => $insight)
                    <div class="flex gap-2">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ ['bg-blue-700','bg-green-700','bg-purple-700','bg-orange-600','bg-emerald-700'][$idx % 5] }} text-white">
                            <i data-lucide="{{ ['droplets','scale','refresh-cw','chart-no-axes-combined','check'][$idx % 5] }}" class="h-4 w-4"></i>
                        </span>
                        <p class="text-xs font-semibold leading-5 text-slate-700">{{ $insight }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="panel xl:col-span-2 p-3">
            <h2 class="text-center text-sm font-black text-blue-800">TREND RASIO UTAMA (2024 vs 2025)</h2>
            <div class="trend-chart"><canvas id="ratioTrendChart"></canvas></div>
        </section>

        <section class="panel overflow-hidden">
            <div class="grid h-full grid-cols-[90px_1fr]">
                <div class="flex items-center justify-center bg-blue-950 p-3 text-center text-sm font-black text-white">RINGKASAN<br>RASIO</div>
                <div class="grid grid-cols-2 gap-0 text-[10px] md:grid-cols-4">
                    @foreach($ratioGroups as $category => $rows)
                        <div class="border-r border-slate-200 p-2 last:border-r-0">
                            <p class="mb-1 text-center font-black text-blue-800">{{ $categoryMeta[$category]['title'] ?? strtoupper($category) }}</p>
                            @foreach($rows as $row)
                                <div class="grid grid-cols-[1fr_auto] gap-1 border-t border-slate-100 py-1">
                                    <span class="font-semibold">{{ $row['ratio'] }}</span>
                                    <span class="{{ $row['diff'] >= 0 ? 'text-emerald-700' : 'text-red-600' }} font-black">{{ $row['diff'] >= 0 ? '+' : '' }}{{ number_format($row['diff'], 2, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
</section>
@endsection

@push('scripts')
<script>
    const ratioRows = @json($ratios);
    const financialRows = @json($financials);

    const formatIDR = (value) => new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
    }).format(Number(value || 0));

    const formatPercent = (value) => `${Number(value || 0).toLocaleString('id-ID', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    })}%`;

    function tinyBar(canvasId, values, colors = ['#b7d3e8', '#003b88']) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;
        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: ['2024', '2025'],
                datasets: [{ data: [values[0], values[1]], backgroundColor: colors, borderRadius: 2, barPercentage: .62 }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => formatPercent(ctx.parsed.y) } } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 9, weight: 'bold' } } },
                    y: { display: false, beginAtZero: true }
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        tinyBar('liq0', [{{ $currentRatio['value_2024'] }}, {{ $currentRatio['value_2025'] }}]);
        tinyBar('liq1', [{{ $acidRatio['value_2024'] }}, {{ $acidRatio['value_2025'] }}]);
        tinyBar('leverageChart', [{{ $derRatio['value_2024'] }}, {{ $derRatio['value_2025'] }}]);
        tinyBar('activityChart', [{{ $assetTurnover['value_2024'] }}, {{ $assetTurnover['value_2025'] }}], ['#bfe6ce', '#00833e']);
        @foreach([$gpm, $oir, $or, $npm] as $idx => $row)
            tinyBar('profit{{ $idx }}', [{{ $row['value_2024'] }}, {{ $row['value_2025'] }}], ['#cfe9d8', '#00833e']);
        @endforeach

        const mainRows = financialRows.filter((row) => ['Pendapatan', 'Laba Bersih Tahun Berjalan'].includes(row.label));
        new Chart(document.getElementById('financialChart'), {
            type: 'bar',
            data: {
                labels: mainRows.map((row) => row.label === 'Pendapatan' ? 'Pendapatan (Miliar Rupiah)' : 'Laba Bersih (Juta Rupiah)'),
                datasets: [
                    { label: '2025', data: mainRows.map((row) => row.value_2025), backgroundColor: '#0057b8', borderRadius: 3 },
                    { label: '2024', data: mainRows.map((row) => row.value_2024), backgroundColor: '#c8c8c8', borderRadius: 3 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                onClick: function (event, elements) {
                    if (!elements.length) return;
                    const item = elements[0];
                    const row = mainRows[item.index];
                    const year = item.datasetIndex === 0 ? '2025' : '2024';
                    const value = year === '2025' ? row.value_2025 : row.value_2024;
                    const previous = year === '2025' ? row.value_2024 : row.value_2025;
                    const change = previous ? ((value - previous) / previous) * 100 : 0;
                    document.getElementById('barDetail').innerHTML = `
                        <div class="flex items-center gap-3">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-blue-900 text-white"><i data-lucide="mouse-pointer-click" class="h-6 w-6"></i></span>
                            <div>
                                <p class="text-sm font-black text-blue-900">${row.label} ${year}</p>
                                <p class="text-xl font-black text-slate-900">${formatIDR(value)} <span class="${change >= 0 ? 'text-emerald-700' : 'text-red-600'} text-sm">${formatPercent(change)}</span></p>
                                <p class="text-xs font-semibold text-slate-600">Detail ini muncul dari bar chart yang diklik.</p>
                            </div>
                        </div>`;
                    if (window.lucide) window.lucide.createIcons();
                },
                plugins: {
                    legend: { position: 'top', labels: { boxWidth: 10, font: { size: 10, weight: 'bold' } } },
                    tooltip: { callbacks: { label: (ctx) => `${ctx.dataset.label}: ${formatIDR(ctx.parsed.y)}` } }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: (value) => new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(value), font: { size: 9 } },
                        grid: { color: '#e5e7eb' }
                    },
                    x: { grid: { display: false }, ticks: { font: { size: 10, weight: 'bold' } } }
                }
            }
        });

        new Chart(document.getElementById('ratioTrendChart'), {
            type: 'line',
            data: {
                labels: ratioRows.map((row) => row.ratio.replaceAll(' ', '\n')),
                datasets: [
                    { label: '2025', data: ratioRows.map((row) => row.value_2025), borderColor: '#0057b8', backgroundColor: '#0057b8', tension: .28, pointRadius: 3 },
                    { label: '2024', data: ratioRows.map((row) => row.value_2024), borderColor: '#9ca3af', backgroundColor: '#9ca3af', tension: .28, pointRadius: 3 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { boxWidth: 10, font: { size: 10, weight: 'bold' } } },
                    tooltip: { callbacks: { label: (ctx) => `${ctx.dataset.label}: ${formatPercent(ctx.parsed.y)}` } }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: (value) => `${value}%`, font: { size: 9 } }, grid: { color: '#e5e7eb' } },
                    x: { grid: { display: false }, ticks: { font: { size: 9, weight: 'bold' } } }
                }
            }
        });
    });
</script>
@endpush
