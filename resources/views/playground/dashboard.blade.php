@extends('layouts.playground')

@section('title', 'Futuristic Financial Report')
@section('fullscreen', true)

@section('content')
@php
    $percent = fn ($value) => number_format((float) $value, 2, ',', '.') . '%';
    $shortMoney = function ($value) {
        $value = (float) $value;
        if (abs($value) >= 1000000000) {
            return number_format($value / 1000000000, 2, ',', '.') . ' M';
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
    $revenue = $findMoney('Pendapatan');
    $assets = $findMoney('Total Aset');
    $equity = $findMoney('Total Ekuitas');
    $liabilities = $findMoney('Total Kewajiban');
    $grossProfit = $findMoney('Laba Kotor');
    $operatingProfit = $findMoney('Laba Usaha');
    $netProfit = $findMoney('Laba Bersih');
    $expense = $findMoney('Beban Pokok');
    $currentRatio = $findRatio('Current Ratio');
    $acidRatio = $findRatio('Acid Test Ratio');
    $derRatio = $findRatio('Debt to Equity');
    $assetTurnover = $findRatio('Total Assets Turn Over');
    $gpm = $findRatio('Gross Profit');
    $oir = $findRatio('Operating Income');
    $operatingRatio = $findRatio('Operating Ratio');
    $npm = $findRatio('Net Profit');
    $revenueChange = $moneyChange($revenue);
    $assetChange = $moneyChange($assets);
    $operatingProfitChange = $moneyChange($operatingProfit);
    $netProfitChange = $moneyChange($netProfit);
    $healthScore = min(100, max(0,
        28 + ($currentRatio['value_2025'] >= 200 ? 18 : 8)
        + ($derRatio['diff'] < 0 ? 18 : 6)
        + ($npm['diff'] > 0 ? 18 : 7)
        + ($gpm['diff'] > 0 ? 12 : 4)
        + ($netProfitChange > $revenueChange ? 6 : 0)
    ));
    $riskLevel = $healthScore >= 78 ? 'LOW RISK' : ($healthScore >= 60 ? 'WATCHLIST' : 'HIGH RISK');
    $categoryAccent = [
        'Liquidity' => 'cyan',
        'Leverage' => 'emerald',
        'Activity' => 'violet',
        'Profitability' => 'amber',
    ];
@endphp

<style>
    .dash-shell {
        min-height: calc(100vh - 1rem);
        background:
            linear-gradient(90deg, rgba(14, 165, 233, .07) 1px, transparent 1px),
            linear-gradient(180deg, rgba(14, 165, 233, .06) 1px, transparent 1px),
            radial-gradient(circle at 12% 10%, rgba(34, 211, 238, .22), transparent 30%),
            radial-gradient(circle at 86% 20%, rgba(16, 185, 129, .18), transparent 26%),
            radial-gradient(circle at 48% 95%, rgba(245, 158, 11, .14), transparent 34%),
            #020617;
        background-size: 44px 44px, 44px 44px, auto, auto, auto, auto;
    }
    .glass {
        border: 1px solid rgba(148, 163, 184, .22);
        background: linear-gradient(145deg, rgba(15, 23, 42, .86), rgba(2, 6, 23, .72));
        box-shadow: 0 18px 50px rgba(0, 0, 0, .32), inset 0 1px 0 rgba(255,255,255,.06);
        backdrop-filter: blur(18px);
    }
    .glass-soft {
        border: 1px solid rgba(148, 163, 184, .18);
        background: rgba(15, 23, 42, .64);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.05);
        backdrop-filter: blur(14px);
    }
    .neon-cyan { box-shadow: 0 0 0 1px rgba(34,211,238,.22), 0 0 34px rgba(34,211,238,.12); }
    .neon-emerald { box-shadow: 0 0 0 1px rgba(52,211,153,.20), 0 0 34px rgba(52,211,153,.10); }
    .neon-amber { box-shadow: 0 0 0 1px rgba(251,191,36,.20), 0 0 34px rgba(251,191,36,.10); }
    .chart-lg { height: 310px; position: relative; }
    .chart-md { height: 245px; position: relative; }
    .chart-sm { height: 164px; position: relative; }
    .hud-line { height: 1px; background: linear-gradient(90deg, transparent, rgba(34,211,238,.7), transparent); }
    .scan-card::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(110deg, transparent 0%, rgba(255,255,255,.07) 46%, transparent 58%);
        transform: translateX(-130%);
        animation: sweep 7s infinite;
        pointer-events: none;
    }
    @keyframes sweep {
        0%, 42% { transform: translateX(-130%); }
        58%, 100% { transform: translateX(130%); }
    }
    @media (max-width: 1024px) {
        .chart-lg, .chart-md { height: 280px; }
        .dash-shell { min-height: auto; }
    }
</style>

<section class="dash-shell overflow-hidden rounded-xl p-3 text-slate-100">
    <div class="grid gap-3 xl:grid-cols-[1.05fr_1.45fr_1fr]">
        <header class="glass relative overflow-hidden rounded-xl p-5 xl:col-span-3 scan-card">
            <div class="relative z-10 grid gap-5 xl:grid-cols-[1.15fr_0.78fr_0.72fr] xl:items-center">
                <div>
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="rounded-full border border-cyan-300/30 bg-cyan-300/10 px-3 py-1 text-xs font-black uppercase tracking-[.28em] text-cyan-200">MKM Financial Core</span>
                        <span class="rounded-full border border-emerald-300/30 bg-emerald-300/10 px-3 py-1 text-xs font-black text-emerald-200">{{ $riskLevel }}</span>
                    </div>
                    <h1 class="mt-4 text-4xl font-black leading-none tracking-tight text-white md:text-6xl">FUTURISTIC<br>FINANCIAL REPORT</h1>
                    <p class="mt-4 max-w-3xl text-sm font-semibold leading-6 text-slate-300">Executive cockpit rasio keuangan {{ $current_year }} vs {{ $previous_year }}. Semua panel membaca data dari database playground dan siap berubah setelah import Excel.</p>
                </div>

                <div class="glass-soft rounded-xl p-4 neon-cyan">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[.18em] text-cyan-200">Financial Health</p>
                            <p class="mt-2 text-5xl font-black text-white">{{ $healthScore }}</p>
                        </div>
                        <div class="flex h-20 w-20 items-center justify-center rounded-full border border-cyan-300/40 bg-cyan-300/10 text-cyan-200">
                            <i data-lucide="activity" class="h-10 w-10"></i>
                        </div>
                    </div>
                    <div class="mt-4 h-3 overflow-hidden rounded-full bg-slate-800">
                        <div class="h-3 rounded-full bg-gradient-to-r from-cyan-300 via-emerald-300 to-amber-300" style="width: {{ $healthScore }}%"></div>
                    </div>
                    <p class="mt-3 text-xs font-semibold text-slate-300">Likuiditas kuat, leverage membaik, profit margin naik.</p>
                </div>

                <div class="grid gap-3">
                    <a href="{{ route('playground.upload') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-cyan-300 px-4 py-3 text-sm font-black text-slate-950 shadow-lg shadow-cyan-950/30 hover:bg-cyan-200">
                        <i data-lucide="upload-cloud" class="h-4 w-4"></i>
                        Upload / Import Excel
                    </a>
                    <a href="{{ route('playground.template') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-sm font-black text-white hover:bg-white/15">
                        <i data-lucide="download" class="h-4 w-4"></i>
                        Download Template
                    </a>
                </div>
            </div>
        </header>

        <section class="xl:col-span-3 grid gap-3 md:grid-cols-2 xl:grid-cols-5">
            @foreach([
                ['icon' => 'radio-tower', 'label' => 'Revenue Signal', 'value' => $shortMoney($revenue['value_2025']), 'change' => $revenueChange, 'sub' => $previous_year . ': ' . $shortMoney($revenue['value_2024']), 'tone' => 'cyan'],
                ['icon' => 'landmark', 'label' => 'Asset Base', 'value' => $shortMoney($assets['value_2025']), 'change' => $assetChange, 'sub' => $previous_year . ': ' . $shortMoney($assets['value_2024']), 'tone' => 'blue'],
                ['icon' => 'circle-dollar-sign', 'label' => 'Operating Profit', 'value' => $shortMoney($operatingProfit['value_2025']), 'change' => $operatingProfitChange, 'sub' => $previous_year . ': ' . $shortMoney($operatingProfit['value_2024']), 'tone' => 'emerald'],
                ['icon' => 'orbit', 'label' => 'Net Profit', 'value' => $shortMoney($netProfit['value_2025']), 'change' => $netProfitChange, 'sub' => $previous_year . ': ' . $shortMoney($netProfit['value_2024']), 'tone' => 'amber'],
                ['icon' => 'percent', 'label' => 'Net Margin', 'value' => $percent($npm['value_2025']), 'change' => $npm['diff'], 'sub' => $previous_year . ': ' . $percent($npm['value_2024']), 'tone' => 'violet'],
            ] as $metric)
                <article class="glass relative overflow-hidden rounded-xl p-4 scan-card">
                    <div class="relative z-10 flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[.18em] text-slate-400">{{ $metric['label'] }}</p>
                            <p class="mt-3 text-3xl font-black text-white">{{ $metric['value'] }}</p>
                            <p class="{{ $metric['change'] >= 0 ? 'text-emerald-300' : 'text-rose-300' }} mt-1 text-sm font-black">{{ $metric['change'] >= 0 ? '+' : '' }}{{ $percent($metric['change']) }}</p>
                            <p class="mt-1 text-xs font-semibold text-slate-400">{{ $metric['sub'] }}</p>
                        </div>
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-cyan-300/20 bg-cyan-300/10 text-cyan-200">
                            <i data-lucide="{{ $metric['icon'] }}" class="h-6 w-6"></i>
                        </span>
                    </div>
                </article>
            @endforeach
        </section>

        <aside class="space-y-3">
            <section class="glass rounded-xl p-4 neon-cyan">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[.2em] text-cyan-200">Liquidity Shield</p>
                        <h2 class="text-xl font-black text-white">Ketahanan Likuiditas</h2>
                    </div>
                    <i data-lucide="shield-check" class="h-8 w-8 text-cyan-200"></i>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    @foreach([$currentRatio, $acidRatio] as $ratio)
                        <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                            <p class="text-xs font-black text-slate-400">{{ $ratio['ratio'] }}</p>
                            <p class="mt-2 text-3xl font-black text-white">{{ $percent($ratio['value_2025']) }}</p>
                            <p class="{{ $ratio['diff'] >= 0 ? 'text-emerald-300' : 'text-rose-300' }} text-xs font-black">{{ $ratio['diff'] >= 0 ? '+' : '' }}{{ $percent($ratio['diff']) }} p.p</p>
                            <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-800">
                                <div class="h-2 rounded-full bg-cyan-300" style="width: {{ min(100, max(5, $ratio['value_2025'] / 3.5)) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="glass rounded-xl p-4 neon-emerald">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[.2em] text-emerald-200">Member Pulse</p>
                        <h2 class="text-xl font-black text-white">Aktivitas Anggota</h2>
                    </div>
                    <i data-lucide="users-round" class="h-8 w-8 text-emerald-200"></i>
                </div>
                <div class="grid grid-cols-[1fr_150px] gap-4">
                    <div class="space-y-3">
                        @foreach($participation as $item)
                            <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-black text-white">{{ strtoupper($item['label']) }}</p>
                                    <p class="text-sm font-black text-emerald-300">{{ $percent($item['rate']) }}</p>
                                </div>
                                <p class="mt-1 text-xs font-semibold text-slate-400">{{ number_format($item['active'], 0, ',', '.') }} aktif / {{ number_format($item['total'], 0, ',', '.') }} anggota</p>
                                <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-800">
                                    <div class="h-2 rounded-full bg-emerald-300" style="width: {{ min(100, $item['rate']) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="chart-sm"><canvas id="memberChart"></canvas></div>
                </div>
            </section>
        </aside>

        <main class="space-y-3">
            <section class="glass rounded-xl p-4">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[.2em] text-cyan-200">Core Performance</p>
                        <h2 class="text-2xl font-black text-white">Business vs Profit Engine</h2>
                    </div>
                    <span class="rounded-full border border-amber-300/20 bg-amber-300/10 px-3 py-1 text-xs font-black text-amber-200">
                        Pendapatan {{ $percent($revenueChange) }} | Net Profit {{ $percent($netProfitChange) }}
                    </span>
                </div>
                <div class="chart-lg"><canvas id="financialChart"></canvas></div>
                <div id="barDetail" class="mt-4 rounded-xl border border-cyan-300/20 bg-cyan-300/10 p-4">
                    <div class="flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-cyan-300 text-slate-950"><i data-lucide="mouse-pointer-click" class="h-5 w-5"></i></span>
                        <div>
                            <p class="text-sm font-black text-cyan-100">Interactive Detail Layer</p>
                            <p class="text-xs font-semibold text-slate-300">Klik bar chart untuk melihat detail angka dan perubahan antar tahun.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="glass rounded-xl p-4">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[.2em] text-violet-200">Ratio Navigation</p>
                        <h2 class="text-2xl font-black text-white">Trend Matrix {{ $previous_year }} → {{ $current_year }}</h2>
                    </div>
                    <i data-lucide="scan-line" class="h-8 w-8 text-violet-200"></i>
                </div>
                <div class="chart-md"><canvas id="ratioTrendChart"></canvas></div>
            </section>
        </main>

        <aside class="space-y-3">
            <section class="glass rounded-xl p-4 neon-amber">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[.2em] text-amber-200">Capital Structure</p>
                        <h2 class="text-xl font-black text-white">Leverage & Assets</h2>
                    </div>
                    <i data-lucide="scale" class="h-8 w-8 text-amber-200"></i>
                </div>
                <div class="chart-sm"><canvas id="capitalChart"></canvas></div>
                <div class="mt-4 grid grid-cols-3 gap-2">
                    @foreach([
                        ['label' => 'Aset', 'value' => $assets],
                        ['label' => 'Kewajiban', 'value' => $liabilities],
                        ['label' => 'Ekuitas', 'value' => $equity],
                    ] as $item)
                        <div class="rounded-xl border border-white/10 bg-white/5 p-3 text-center">
                            <p class="text-xs font-black text-slate-400">{{ $item['label'] }}</p>
                            <p class="mt-1 text-sm font-black text-white">{{ $shortMoney($item['value']['value_2025']) }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="glass rounded-xl p-4">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[.2em] text-rose-200">Executive Signals</p>
                        <h2 class="text-xl font-black text-white">Insight Utama</h2>
                    </div>
                    <i data-lucide="brain-circuit" class="h-8 w-8 text-rose-200"></i>
                </div>
                <div class="space-y-3">
                    @foreach($insights as $index => $insight)
                        <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                            <div class="flex gap-3">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/10 text-cyan-200">{{ $index + 1 }}</span>
                                <p class="text-xs font-semibold leading-5 text-slate-300">{{ $insight }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </aside>

        <section class="glass rounded-xl p-4 xl:col-span-3">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-black uppercase tracking-[.2em] text-cyan-200">Complete Ratio Ledger</p>
                    <h2 class="text-2xl font-black text-white">Financial Ratio Grid</h2>
                </div>
                <div class="flex gap-2 text-xs font-black">
                    <span class="rounded-full bg-emerald-300/10 px-3 py-1 text-emerald-200">▲ Meningkat / Membaik</span>
                    <span class="rounded-full bg-rose-300/10 px-3 py-1 text-rose-200">▼ Menurun / Memburuk</span>
                </div>
            </div>
            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                @foreach(collect($ratios)->groupBy('category') as $category => $rows)
                    <div class="rounded-xl border border-white/10 bg-white/[.04] p-4">
                        <div class="mb-3 flex items-center justify-between">
                            <p class="text-sm font-black text-white">{{ strtoupper($category) }}</p>
                            <span class="h-2 w-2 rounded-full bg-cyan-300"></span>
                        </div>
                        <div class="space-y-3">
                            @foreach($rows as $row)
                                <div class="rounded-lg bg-slate-950/45 p-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <p class="text-xs font-bold leading-5 text-slate-300">{{ $row['ratio'] }}</p>
                                        <span class="{{ $row['diff'] >= 0 ? 'text-emerald-300' : 'text-rose-300' }} text-xs font-black">{{ $row['diff'] >= 0 ? '▲' : '▼' }} {{ $percent(abs($row['diff'])) }}</span>
                                    </div>
                                    <div class="mt-3 grid grid-cols-2 gap-2 text-xs">
                                        <div>
                                            <p class="font-black text-slate-500">{{ $current_year }}</p>
                                            <p class="text-lg font-black text-white">{{ $percent($row['value_2025']) }}</p>
                                        </div>
                                        <div>
                                            <p class="font-black text-slate-500">{{ $previous_year }}</p>
                                            <p class="text-lg font-black text-slate-300">{{ $percent($row['value_2024']) }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</section>
@endsection

@push('scripts')
<script>
    const ratioRows = @json($ratios);
    const financialRows = @json($financials);
    const participationRows = @json(array_values($participation));

    const neonGrid = '#1e293b';
    const formatIDR = (value) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(Number(value || 0));
    const compactNumber = (value) => new Intl.NumberFormat('id-ID', { notation: 'compact', maximumFractionDigits: 2 }).format(Number(value || 0));
    const formatPercent = (value) => `${Number(value || 0).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}%`;

    Chart.defaults.color = '#cbd5e1';
    Chart.defaults.font.family = 'Inter, sans-serif';

    document.addEventListener('DOMContentLoaded', () => {
        const mainRows = financialRows.filter((row) => ['Pendapatan', 'Laba Kotor', 'Laba Usaha', 'Laba Bersih Tahun Berjalan'].includes(row.label));

        new Chart(document.getElementById('financialChart'), {
            type: 'bar',
            data: {
                labels: mainRows.map((row) => row.label.replace(' Tahun Berjalan', '')),
                datasets: [
                    { label: '{{ $current_year }}', data: mainRows.map((row) => row.value_2025), backgroundColor: 'rgba(34, 211, 238, .78)', borderColor: '#67e8f9', borderWidth: 1, borderRadius: 7 },
                    { label: '{{ $previous_year }}', data: mainRows.map((row) => row.value_2024), backgroundColor: 'rgba(148, 163, 184, .35)', borderColor: '#94a3b8', borderWidth: 1, borderRadius: 7 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                onClick: (event, elements) => {
                    if (!elements.length) return;
                    const item = elements[0];
                    const row = mainRows[item.index];
                    const year = item.datasetIndex === 0 ? '{{ $current_year }}' : '{{ $previous_year }}';
                    const value = year === '{{ $current_year }}' ? row.value_2025 : row.value_2024;
                    const previous = year === '{{ $current_year }}' ? row.value_2024 : row.value_2025;
                    const change = previous ? ((value - previous) / previous) * 100 : 0;
                    document.getElementById('barDetail').innerHTML = `
                        <div class="flex items-center gap-3">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-cyan-300 text-slate-950"><i data-lucide="crosshair" class="h-5 w-5"></i></span>
                            <div>
                                <p class="text-sm font-black text-cyan-100">${row.label} ${year}</p>
                                <p class="text-2xl font-black text-white">${formatIDR(value)}</p>
                                <p class="${change >= 0 ? 'text-emerald-300' : 'text-rose-300'} text-xs font-black">${formatPercent(change)} dibanding ${year === '{{ $current_year }}' ? '{{ $previous_year }}' : '{{ $current_year }}'}</p>
                            </div>
                        </div>`;
                    if (window.lucide) window.lucide.createIcons();
                },
                plugins: {
                    legend: { labels: { usePointStyle: true, pointStyle: 'rectRounded', font: { weight: 'bold' } } },
                    tooltip: { callbacks: { label: (ctx) => `${ctx.dataset.label}: ${formatIDR(ctx.parsed.y)}` } }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: (v) => compactNumber(v) }, grid: { color: neonGrid } },
                    x: { grid: { display: false }, ticks: { font: { weight: 'bold' } } }
                }
            }
        });

        new Chart(document.getElementById('ratioTrendChart'), {
            type: 'radar',
            data: {
                labels: ratioRows.map((row) => row.ratio),
                datasets: [
                    { label: '{{ $current_year }}', data: ratioRows.map((row) => row.value_2025), borderColor: '#22d3ee', backgroundColor: 'rgba(34, 211, 238, .16)', pointBackgroundColor: '#67e8f9', pointRadius: 3 },
                    { label: '{{ $previous_year }}', data: ratioRows.map((row) => row.value_2024), borderColor: '#fbbf24', backgroundColor: 'rgba(251, 191, 36, .10)', pointBackgroundColor: '#fde68a', pointRadius: 3 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { labels: { usePointStyle: true, font: { weight: 'bold' } } },
                    tooltip: { callbacks: { label: (ctx) => `${ctx.dataset.label}: ${formatPercent(ctx.parsed.r)}` } }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        angleLines: { color: '#1e293b' },
                        grid: { color: '#1e293b' },
                        pointLabels: { color: '#cbd5e1', font: { size: 10, weight: 'bold' } },
                        ticks: { backdropColor: 'transparent', color: '#64748b', callback: (v) => `${v}%` }
                    }
                }
            }
        });

        new Chart(document.getElementById('memberChart'), {
            type: 'doughnut',
            data: {
                labels: participationRows.map((row) => row.label),
                datasets: [{ data: participationRows.map((row) => row.rate), backgroundColor: ['#22d3ee', '#fb923c'], borderColor: '#020617', borderWidth: 3 }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '64%',
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10, weight: 'bold' } } },
                    tooltip: { callbacks: { label: (ctx) => {
                        const row = participationRows[ctx.dataIndex];
                        return `${row.label}: ${formatPercent(row.rate)} (${row.active}/${row.total})`;
                    }}}
                }
            }
        });

        new Chart(document.getElementById('capitalChart'), {
            type: 'bar',
            data: {
                labels: ['Aset', 'Kewajiban', 'Ekuitas'],
                datasets: [{
                    data: [{{ $assets['value_2025'] }}, {{ $liabilities['value_2025'] }}, {{ $equity['value_2025'] }}],
                    backgroundColor: ['#22d3ee', '#fb7185', '#34d399'],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: (ctx) => formatIDR(ctx.parsed.y) } }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: (v) => compactNumber(v), font: { size: 10 } }, grid: { color: neonGrid } },
                    x: { grid: { display: false }, ticks: { font: { size: 10, weight: 'bold' } } }
                }
            }
        });
    });
</script>
@endpush
