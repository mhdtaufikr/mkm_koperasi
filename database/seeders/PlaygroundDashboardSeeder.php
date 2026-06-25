<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlaygroundDashboardSeeder extends Seeder
{
    public function run()
    {
        $now = now();

        DB::table('playground_dashboard_inputs')->updateOrInsert(
            ['id' => 1],
            [
                'raw_data' => $this->rawData(),
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $ratios = [
            ['Liquidity', 'Current Ratio', 316.18, 289.57, 26.61],
            ['Liquidity', 'Acid Test Ratio', 10.58, 22.23, -11.65],
            ['Leverage', 'Debt to Equity Ratio', 121.58, 159.15, -37.57],
            ['Activity', 'Total Assets Turn Over', 27.99, 37.91, -9.92],
            ['Profitability', 'Gross Profit Margin', 26.93, 23.70, 3.23],
            ['Profitability', 'Operating Income Ratio', 10.03, 9.94, 0.09],
            ['Profitability', 'Operating Ratio', 16.90, 13.76, 3.14],
            ['Profitability', 'Net Profit Margin', 10.43, 9.00, 1.43],
        ];

        foreach ($ratios as $index => $ratio) {
            DB::table('playground_financial_ratios')->updateOrInsert(
                ['ratio' => $ratio[1]],
                [
                    'category' => $ratio[0],
                    'value_2025' => $ratio[2],
                    'value_2024' => $ratio[3],
                    'diff' => $ratio[4],
                    'sort_order' => $index + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        $financialItems = [
            ['Aset Lancar', 20569298877, 20130969673],
            ['Kewajiban Jangka Pendek', 6505518557, 6951906805],
            ['Total Kewajiban', 6505518557, 6951906805],
            ['Total Ekuitas', 5350855568, 4368104808],
            ['Pendapatan', 5777378020, 7631595408],
            ['Total Aset', 20641300389, 20130969673],
            ['Laba Kotor', 1555561976, 1808817977],
            ['Laba Usaha', 579434671, 758730805],
            ['Beban Pokok + Operasional', 976127304, 1050087172],
            ['Laba Bersih Tahun Berjalan', 602841058, 686721098],
        ];

        foreach ($financialItems as $index => $item) {
            DB::table('playground_financial_items')->updateOrInsert(
                ['label' => $item[0]],
                [
                    'value_2025' => $item[1],
                    'value_2024' => $item[2],
                    'sort_order' => $index + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        $participations = [
            ['Pertokoan', 145, 220],
            ['Pinjaman', 128, 220],
        ];

        foreach ($participations as $index => $participation) {
            DB::table('playground_participations')->updateOrInsert(
                ['category' => $participation[0]],
                [
                    'active_members' => $participation[1],
                    'total_members' => $participation[2],
                    'sort_order' => $index + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    private function rawData()
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
}
