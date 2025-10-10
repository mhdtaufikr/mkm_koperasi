<?php
// app/Http/Controllers/CoopDashboardController.php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class CoopDashboardController extends Controller
{
    public function index()
{
    $totalMembers = DB::table('vw_coop_total_members')->value('total_members') ?? 0;
    $parts = DB::table('vw_coop_participation_30d')->get()->keyBy('unit_id');

    $ratios    = DB::table('vw_coop_latest_ratios')->first();
    $latestBal = DB::table('vw_coop_latest_balance')->first(); // total assets/liabs/equity (dari view)
    $latestSnap = DB::table('coop_balance_snapshots')->orderByDesc('as_of_date')->first();

    $crFromSnap = null;
    if ($latestSnap && ($latestSnap->current_liabilities ?? 0) > 0) {
        $crFromSnap = round(($latestSnap->current_assets ?? 0) / $latestSnap->current_liabilities, 2);
    }

    $equityVal = (int)($latestBal->total_equity ?? $latestSnap->total_equity ?? 0);
    $deposits  = (int)($latestSnap->total_deposits ?? 0);
    $equityVsDeposits = $deposits > 0 ? round($equityVal / $deposits * 100, 2) : null; // ✅ NEW

    $line      = DB::table('vw_coop_linechart_ytd')->orderBy('month')->get();

    $proj2024  = DB::table('coop_projections')->where('projection_key','SHU')->where('year',2024)->value('amount') ?? 0;
    $proj2025  = DB::table('coop_projections')->where('projection_key','SHU')->where('year',2025)->value('amount') ?? 0;
    $loanAlloc = DB::table('coop_projections')->where('projection_key','LOAN_ALLOCATION')->value('amount') ?? 0;

    $memberCfg = DB::table('coop_settings')->where('key_name','dashboard_members')->value('value_json');
    $memberJson = $memberCfg ? json_decode($memberCfg, true) : ['initial_members'=>0,'new_members'=>0];

    $headerCfg = DB::table('coop_participations')->where('section_key','header')->first();

    // fallback ke data 30 hari jika tidak ada konfigurasi manual
    $spActive = isset($headerCfg->sp_active) ? (int)$headerCfg->sp_active : (int)($parts[1]->active_members_30d ?? 0);
    $spTotal  = isset($headerCfg->sp_total)  ? (int)$headerCfg->sp_total  : (int)$totalMembers;

    $tokoActive = isset($headerCfg->toko_active) ? (int)$headerCfg->toko_active : (int)($parts[2]->active_members_30d ?? 0);
    $tokoTotal  = isset($headerCfg->toko_total)  ? (int)$headerCfg->toko_total  : (int)$totalMembers;

    $jasaActive = isset($headerCfg->jasa_active) ? (int)$headerCfg->jasa_active : (int)($parts[3]->active_members_30d ?? 0);
    $jasaTotal  = isset($headerCfg->jasa_total)  ? (int)$headerCfg->jasa_total  : (int)$totalMembers;

    // hitung rate
    $spRate   = $spTotal   ? round($spActive   / $spTotal   * 100, 2) : 0;
    $tokoRate = $tokoTotal ? round($tokoActive / $tokoTotal * 100, 2) : 0;
    $jasaRate = $jasaTotal ? round($jasaActive / $jasaTotal * 100, 2) : 0;

    // label kondisi keuangan (pakai input manual jika ada; else derive dari rasio)
    $deriveStatus = function($ratios) {
        // aturan sederhana; silakan sesuaikan
        $dr  = $ratios->dr_percent  ?? null;  // lebih kecil lebih baik
        $cr  = $ratios->cr_times    ?? null;  // >= 1.5 baik
        $roe = $ratios->roe_percent ?? null;  // >= 10% baik
        $npm = $ratios->npm_percent ?? null;  // >= 10% baik

        $score = 0;
        if ($dr !== null && $dr < 60) $score++;
        if ($cr !== null && $cr >= 1.5) $score++;
        if ($roe !== null && $roe >= 10) $score++;
        if ($npm !== null && $npm >= 10) $score++;

        return $score >= 3 ? 'BAIK' : ($score == 2 ? 'CUKUP' : 'KURANG');
    };

    $kondisiKeuangan = $headerCfg->kondisi_keuangan ?? $deriveStatus($ratios);

    // susun payload ke Blade
    $data = [
        'header' => [
            'kondisi_keuangan' => $kondisiKeuangan,
            'partisipasi' => [
                'simpan_pinjam' => [
                    'active' => $spActive,
                    'total'  => $spTotal,
                    'rate'   => $spRate,
                ],
                'pertokoan' => [
                    'active' => $tokoActive,
                    'total'  => $tokoTotal,
                    'rate'   => $tokoRate,
                ],
                'perdagangan_jasa' => [
                    'active' => $jasaActive,
                    'total'  => $jasaTotal,
                    'rate'   => $jasaRate,
                ],
                // rata-rata/ikhtisar tingkat partisipasi per unit (opsional)
                'tingkat_partisipasi' => [
                    'sp_rate'   => $spRate,
                    'toko_rate' => $tokoRate,
                    'jasa_rate' => $jasaRate,
                ],
            ],
        ],
        'members' => [
            'initial' => (int)$memberJson['initial_members'],
            'new'     => (int)$memberJson['new_members'],
            'final'   => (int)$memberJson['initial_members'] + (int)$memberJson['new_members'],
            'growth_pct' => (int)$memberJson['initial_members'] ? round($memberJson['new_members'] / $memberJson['initial_members'] * 100, 2) : 0
        ],
        'projections' => [
            'shu_2024' => (int)$proj2024,
            'shu_2025' => (int)$proj2025,
            'loan_allocation' => (int)$loanAlloc
        ],
       'ratios' =>[
        'npm_percent' => $ratios->npm_percent ?? null,
        'dr_percent'  => $ratios->dr_percent ?? null,
        // pakai yang paling “segar”: dari snapshot jika ada; kalau tidak, ambil dari view lama
        'cr_times'    => $crFromSnap ?? ($ratios->cr_times ?? null),
        'roe_percent' => $ratios->roe_percent ?? null,
        'equity_vs_deposits_percent' => $equityVsDeposits ?? null,
    ],
        'snapshot' => [
            'as_of_date'          => $latestSnap->as_of_date ?? null,
            'current_assets'      => (int)($latestSnap->current_assets ?? 0),
            'current_liabilities' => (int)($latestSnap->current_liabilities ?? 0),
            'net_income_ttm'      => (int)($latestSnap->net_income_ttm ?? 0),
            'total_deposits'      => (int)($latestSnap->total_deposits ?? 0), // ✅ NEW
        ],

        'line' => [
            'labels'   => $line->pluck('month')->map(fn($m)=> date('M', mktime(0,0,0,$m,1)))->values(),
            'revenue'  => $line->pluck('revenue')->values(),
            'cogs'     => $line->pluck('cogs')->values(),
            'opex'     => $line->pluck('opex')->values(),
        ],
        'composition' => [
            'assets'      => (int)($latestBal->total_assets ?? 0),
            'liabilities' => (int)($latestBal->total_liabilities ?? 0),
            'equity'      => (int)($latestBal->total_equity ?? 0),
        ],

    ];

    return view('coop.dashboard', $data);
}

public function indexView()
{
    $totalMembers = DB::table('vw_coop_total_members')->value('total_members') ?? 0;
    $parts = DB::table('vw_coop_participation_30d')->get()->keyBy('unit_id');

    $ratios    = DB::table('vw_coop_latest_ratios')->first();
    $latestBal = DB::table('vw_coop_latest_balance')->first(); // total assets/liabs/equity (dari view)
    $latestSnap = DB::table('coop_balance_snapshots')->orderByDesc('as_of_date')->first();

    $crFromSnap = null;
    if ($latestSnap && ($latestSnap->current_liabilities ?? 0) > 0) {
        $crFromSnap = round(($latestSnap->current_assets ?? 0) / $latestSnap->current_liabilities, 2);
    }

    $equityVal = (int)($latestBal->total_equity ?? $latestSnap->total_equity ?? 0);
    $deposits  = (int)($latestSnap->total_deposits ?? 0);
    $equityVsDeposits = $deposits > 0 ? round($equityVal / $deposits * 100, 2) : null; // ✅ NEW

    $line      = DB::table('vw_coop_linechart_ytd')->orderBy('month')->get();

    $proj2024  = DB::table('coop_projections')->where('projection_key','SHU')->where('year',2024)->value('amount') ?? 0;
    $proj2025  = DB::table('coop_projections')->where('projection_key','SHU')->where('year',2025)->value('amount') ?? 0;
    $loanAlloc = DB::table('coop_projections')->where('projection_key','LOAN_ALLOCATION')->value('amount') ?? 0;

    $memberCfg = DB::table('coop_settings')->where('key_name','dashboard_members')->value('value_json');
    $memberJson = $memberCfg ? json_decode($memberCfg, true) : ['initial_members'=>0,'new_members'=>0];

    $headerCfg = DB::table('coop_participations')->where('section_key','header')->first();

    // fallback ke data 30 hari jika tidak ada konfigurasi manual
    $spActive = isset($headerCfg->sp_active) ? (int)$headerCfg->sp_active : (int)($parts[1]->active_members_30d ?? 0);
    $spTotal  = isset($headerCfg->sp_total)  ? (int)$headerCfg->sp_total  : (int)$totalMembers;

    $tokoActive = isset($headerCfg->toko_active) ? (int)$headerCfg->toko_active : (int)($parts[2]->active_members_30d ?? 0);
    $tokoTotal  = isset($headerCfg->toko_total)  ? (int)$headerCfg->toko_total  : (int)$totalMembers;

    $jasaActive = isset($headerCfg->jasa_active) ? (int)$headerCfg->jasa_active : (int)($parts[3]->active_members_30d ?? 0);
    $jasaTotal  = isset($headerCfg->jasa_total)  ? (int)$headerCfg->jasa_total  : (int)$totalMembers;

    // hitung rate
    $spRate   = $spTotal   ? round($spActive   / $spTotal   * 100, 2) : 0;
    $tokoRate = $tokoTotal ? round($tokoActive / $tokoTotal * 100, 2) : 0;
    $jasaRate = $jasaTotal ? round($jasaActive / $jasaTotal * 100, 2) : 0;

    // label kondisi keuangan (pakai input manual jika ada; else derive dari rasio)
    $deriveStatus = function($ratios) {
        // aturan sederhana; silakan sesuaikan
        $dr  = $ratios->dr_percent  ?? null;  // lebih kecil lebih baik
        $cr  = $ratios->cr_times    ?? null;  // >= 1.5 baik
        $roe = $ratios->roe_percent ?? null;  // >= 10% baik
        $npm = $ratios->npm_percent ?? null;  // >= 10% baik

        $score = 0;
        if ($dr !== null && $dr < 60) $score++;
        if ($cr !== null && $cr >= 1.5) $score++;
        if ($roe !== null && $roe >= 10) $score++;
        if ($npm !== null && $npm >= 10) $score++;

        return $score >= 3 ? 'BAIK' : ($score == 2 ? 'CUKUP' : 'KURANG');
    };

    $kondisiKeuangan = $headerCfg->kondisi_keuangan ?? $deriveStatus($ratios);

    // susun payload ke Blade
    $data = [
        'header' => [
            'kondisi_keuangan' => $kondisiKeuangan,
            'partisipasi' => [
                'simpan_pinjam' => [
                    'active' => $spActive,
                    'total'  => $spTotal,
                    'rate'   => $spRate,
                ],
                'pertokoan' => [
                    'active' => $tokoActive,
                    'total'  => $tokoTotal,
                    'rate'   => $tokoRate,
                ],
                'perdagangan_jasa' => [
                    'active' => $jasaActive,
                    'total'  => $jasaTotal,
                    'rate'   => $jasaRate,
                ],
                // rata-rata/ikhtisar tingkat partisipasi per unit (opsional)
                'tingkat_partisipasi' => [
                    'sp_rate'   => $spRate,
                    'toko_rate' => $tokoRate,
                    'jasa_rate' => $jasaRate,
                ],
            ],
        ],
        'members' => [
            'initial' => (int)$memberJson['initial_members'],
            'new'     => (int)$memberJson['new_members'],
            'final'   => (int)$memberJson['initial_members'] + (int)$memberJson['new_members'],
            'growth_pct' => (int)$memberJson['initial_members'] ? round($memberJson['new_members'] / $memberJson['initial_members'] * 100, 2) : 0
        ],
        'projections' => [
            'shu_2024' => (int)$proj2024,
            'shu_2025' => (int)$proj2025,
            'loan_allocation' => (int)$loanAlloc
        ],
       'ratios' =>[
        'npm_percent' => $ratios->npm_percent ?? null,
        'dr_percent'  => $ratios->dr_percent ?? null,
        // pakai yang paling “segar”: dari snapshot jika ada; kalau tidak, ambil dari view lama
        'cr_times'    => $crFromSnap ?? ($ratios->cr_times ?? null),
        'roe_percent' => $ratios->roe_percent ?? null,
        'equity_vs_deposits_percent' => $equityVsDeposits ?? null,
    ],
        'snapshot' => [
            'as_of_date'          => $latestSnap->as_of_date ?? null,
            'current_assets'      => (int)($latestSnap->current_assets ?? 0),
            'current_liabilities' => (int)($latestSnap->current_liabilities ?? 0),
            'net_income_ttm'      => (int)($latestSnap->net_income_ttm ?? 0),
            'total_deposits'      => (int)($latestSnap->total_deposits ?? 0), // ✅ NEW
        ],

        'line' => [
            'labels'   => $line->pluck('month')->map(fn($m)=> date('M', mktime(0,0,0,$m,1)))->values(),
            'revenue'  => $line->pluck('revenue')->values(),
            'cogs'     => $line->pluck('cogs')->values(),
            'opex'     => $line->pluck('opex')->values(),
        ],
        'composition' => [
            'assets'      => (int)($latestBal->total_assets ?? 0),
            'liabilities' => (int)($latestBal->total_liabilities ?? 0),
            'equity'      => (int)($latestBal->total_equity ?? 0),
        ],

    ];

    return view('coop.dashboardView', $data);
}

}
