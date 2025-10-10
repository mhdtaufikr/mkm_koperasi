<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema; // ✅ tambahkan baris ini

class CoopDashboardUpdateController extends Controller
{
    /**
     * Update members (coop_settings.key_name = 'dashboard_members')
     * Fields:
     *  - initial_members (int >= 0)
     *  - new_members (int >= 0)
     */
    public function updateMembers(Request $request)
    {
        $validated = $request->validate([
            'initial_members' => ['required','integer','min:0'],
            'new_members'     => ['required','integer','min:0'],
        ]);

        DB::transaction(function () use ($validated) {
            $payload = json_encode([
                'initial_members' => (int)$validated['initial_members'],
                'new_members'     => (int)$validated['new_members'],
            ], JSON_UNESCAPED_UNICODE);

            // upsert coop_settings
            $exists = DB::table('coop_settings')
                ->where('key_name', 'dashboard_members')
                ->exists();

            if ($exists) {
                DB::table('coop_settings')
                    ->where('key_name', 'dashboard_members')
                    ->update(['value_json' => $payload]);
            } else {
                DB::table('coop_settings')->insert([
                    'id'        => 1, // optional; kalau PK auto, hapus kolom ini
                    'key_name'  => 'dashboard_members',
                    'value_json'=> $payload,
                ]);
            }
        });

        return back()->with('success', 'Members updated successfully.');
    }

    /**
     * Update projections:
     *  - projection: SHU_2024 | SHU_2025 | LOAN_ALLOCATION
     *  - amount: integer >= 0
     *
     * Mapping:
     *  SHU_2024 -> coop_projections (projection_key = 'SHU', year=2024)
     *  SHU_2025 -> coop_projections (projection_key = 'SHU', year=2025)
     *  LOAN_ALLOCATION -> coop_projections (projection_key = 'LOAN_ALLOCATION', year=null)
     */
    public function updateProjections(Request $request)
    {
        $validated = $request->validate([
            'projection' => ['required', Rule::in(['SHU_2024','SHU_2025','LOAN_ALLOCATION'])],
            'amount'     => ['required','integer','min:0'],
        ]);

        $amount = (int)$validated['amount'];

        DB::transaction(function () use ($validated, $amount) {
            switch ($validated['projection']) {
                case 'SHU_2024':
                    DB::table('coop_projections')->updateOrInsert(
                        ['projection_key' => 'SHU', 'year' => 2024],
                        ['amount' => $amount]
                    );
                    break;

                case 'SHU_2025':
                    DB::table('coop_projections')->updateOrInsert(
                        ['projection_key' => 'SHU', 'year' => 2025],
                        ['amount' => $amount]
                    );
                    break;

                case 'LOAN_ALLOCATION':
                    // year null disamakan dengan NULL di DB
                    $row = DB::table('coop_projections')
                        ->where('projection_key', 'LOAN_ALLOCATION')
                        ->whereNull('year')
                        ->first();

                    if ($row) {
                        DB::table('coop_projections')
                            ->where('id', $row->id)
                            ->update(['amount' => $amount]);
                    } else {
                        DB::table('coop_projections')->insert([
                            'projection_key' => 'LOAN_ALLOCATION',
                            'year'           => null,
                            'amount'         => $amount,
                        ]);
                    }
                    break;
            }
        });

        return back()->with('success', 'Projections updated successfully.');
    }

    /**
     * Update latest balance snapshot (coop_balance_snapshots).
     * Fields:
     *  - total_assets, total_liabilities, total_equity (required, int >=0)
     *  - current_assets, current_liabilities, net_income_ttm (optional, int >=0)
     *
     * Behavior:
     *  - Jika ada snapshot terbaru (max as_of_date), update row tsb.
     *  - Jika belum ada, insert row baru dengan as_of_date = today().
     */
    public function updateBalance(Request $request)
    {
        $validated = $request->validate([
            'total_assets'        => ['required','integer','min:0'],
            'total_liabilities'   => ['required','integer','min:0'],
            'total_equity'        => ['required','integer','min:0'],
            'current_assets'      => ['nullable','integer','min:0'],
            'current_liabilities' => ['nullable','integer','min:0'],
            'net_income_ttm'      => ['nullable','integer','min:0'],
            'total_deposits'      => ['nullable','integer','min:0'], // ✅ NEW
        ]);

        $today = now()->toDateString();

        DB::transaction(function () use ($validated, $today) {
            // --- Hitung rasio otomatis ---
            $assets      = (float) $validated['total_assets'];
            $liabilities = (float) $validated['total_liabilities'];
            $equity      = (float) $validated['total_equity'];
            $currA       = (float) ($validated['current_assets'] ?? 0);
            $currL       = (float) ($validated['current_liabilities'] ?? 0);
            $netIncome   = (float) ($validated['net_income_ttm'] ?? 0);
            $deposits    = (float) ($validated['total_deposits'] ?? 0); // ✅ NEW

            // safe division
            $dr  = $assets   > 0 ? round(($liabilities / $assets) * 100, 2) : null;     // %
            $cr  = $currL    > 0 ? round($currA / $currL, 2) : null;                    // x
            $roe = $equity   > 0 ? round(($netIncome / $equity) * 100, 2) : null;       // %
            $evd = $deposits > 0 ? round(($equity   / $deposits) * 100, 2) : null;      // %  ✅ NEW: Equity vs Deposits

            // simpan snapshot neraca (update baris terbaru atau insert baru)
            $latest = DB::table('coop_balance_snapshots')->orderByDesc('as_of_date')->first();

            $payload = [
                'total_assets'        => (int)$assets,
                'total_liabilities'   => (int)$liabilities,
                'total_equity'        => (int)$equity,
                'current_assets'      => (int)$currA,
                'current_liabilities' => (int)$currL,
                'net_income_ttm'      => (int)$netIncome,
                'total_deposits'      => (int)$deposits,   // ✅ NEW
                'as_of_date'          => $today,
            ];

            if ($latest) {
                DB::table('coop_balance_snapshots')->where('id', $latest->id)->update($payload);
            } else {
                DB::table('coop_balance_snapshots')->insert($payload);
            }

            // Opsional: simpan ke tabel rasio kalau memang ada
            if (Schema::hasTable('coop_financial_ratios')) {
                // kalau kolom baru ada, isi; kalau tidak, tetap simpan kolom lama saja
                $update = [
                    'npm_percent' => DB::raw('npm_percent'), // biarkan nilai lama utk NPM jika kamu ambil dari tempat lain
                    'dr_percent'  => $dr,
                    'cr_times'    => $cr,
                    'roe_percent' => $roe,
                ];
                if (Schema::hasColumn('coop_financial_ratios','equity_vs_deposits_percent')) {
                    $update['equity_vs_deposits_percent'] = $evd; // ✅ NEW
                }

                DB::table('coop_financial_ratios')->updateOrInsert(
                    ['as_of_date' => $today],
                    $update
                );
            }
        });

        return back()->with('success', 'Snapshot & ratios updated successfully.');
    }

    /**
     * Update monthlies for current year (coop_financial_monthlies).
     * Request payload:
     *  rows[1..12][revenue|cogs|opex] (integer >= 0)
     *
     * Behavior:
     *  - Loop keys yang ada di request->rows.
     *  - Validasi month (1..12); upsert per (year, month).
     */
    public function updateMonthlies(Request $request)
    {
        $rows = $request->input('rows', []);
        if (!is_array($rows) || empty($rows)) {
            return back()->withErrors(['rows' => 'No monthly rows submitted.']);
        }

        // Build validation rules dynamically
        $rules = [];
        foreach ($rows as $month => $vals) {
            $rules["rows.$month.revenue"] = ['required','integer','min:0'];
            $rules["rows.$month.cogs"]    = ['required','integer','min:0'];
            $rules["rows.$month.opex"]    = ['required','integer','min:0'];
        }
        $validated = $request->validate($rules);

        $year = (int) date('Y');

        DB::transaction(function () use ($rows, $year) {
            foreach ($rows as $monthStr => $val) {
                // Safeguard: only 1..12
                $month = (int)$monthStr;
                if ($month < 1 || $month > 12) {
                    continue;
                }

                $revenue = (int)($val['revenue'] ?? 0);
                $cogs    = (int)($val['cogs'] ?? 0);
                $opex    = (int)($val['opex'] ?? 0);

                DB::table('coop_financial_monthlies')->updateOrInsert(
                    ['year' => $year, 'month' => $month],
                    ['revenue' => $revenue, 'cogs' => $cogs, 'opex' => $opex]
                );
            }
        });

        return back()->with('success', 'Financial monthlies updated successfully.');
    }

    public function updateParticipation(Request $request)
{
    $validated = $request->validate([
        'kondisi_keuangan' => 'required|string|max:50',
        'sp_active' => 'required|integer|min:0',
        'sp_total'  => 'required|integer|min:0',
        'toko_active' => 'required|integer|min:0',
        'toko_total'  => 'required|integer|min:0',
        'jasa_active' => 'required|integer|min:0',
        'jasa_total'  => 'required|integer|min:0',
    ]);

    DB::transaction(function () use ($validated) {
        DB::table('coop_participations')->updateOrInsert(
            ['section_key' => 'header'],
            [
                'kondisi_keuangan' => strtoupper($validated['kondisi_keuangan']),
                'sp_active'  => $validated['sp_active'],
                'sp_total'   => $validated['sp_total'],
                'toko_active'=> $validated['toko_active'],
                'toko_total' => $validated['toko_total'],
                'jasa_active'=> $validated['jasa_active'],
                'jasa_total' => $validated['jasa_total'],
                'updated_at' => now(),
            ]
        );
    });

    return back()->with('success', 'Kondisi & Partisipasi updated successfully.');
}

}
