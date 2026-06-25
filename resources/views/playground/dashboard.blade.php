@extends('layouts.playground')

@section('title', 'Dashboard Rasio - Playground')

@section('content')
@php
    $money = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
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
    $percent = fn ($value) => number_format((float) $value, 2, ',', '.') . '%';
    $moneyChange = function ($row) {
        $base = (float) ($row['value_2024'] ?? 0);
        return $base != 0 ? (($row['value_2025'] - $row['value_2024']) / $base) * 100 : 0;
    };
    $ratioGroups = collect($ratios)->groupBy('category');
    $revenueChange = $moneyChange($summary['revenue']);
    $profitChange = $moneyChange($summary['net_profit']);
@endphp

<section class="space-y-6">
    <div class="overflow-hidden rounded-lg bg-blue-950 shadow-sm">
        <div class="grid gap-6 p-6 text-white lg:grid-cols-[1.4fr_1fr] lg:items-center">
            <div>
                <p class="text-sm font-bold uppercase tracking-wide text-blue-200">Perbandingan Kinerja Keuangan 2025 vs 2024</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">Dashboard Rasio Keuangan</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-blue-100">
                    Pendapatan turun {{ $percent(abs($revenueChange)) }}, sementara laba bersih berubah {{ $percent($profitChange) }}. Dashboard ini memakai data raw terakhir dari halaman upload.
                </p>
            </div>
            <div class="grid gap-3 sm:grid-cols-2">
                <div class="rounded-lg bg-white p-4 text-slate-900 shadow-lg">
                    <p class="text-xs font-bold uppercase text-slate-500">Total Pendapatan</p>
                    <div class="mt-2 flex items-end justify-between gap-3">
                        <span class="text-2xl font-black text-blue-900">{{ $shortMoney($summary['revenue']['value_2025']) }}</span>
                        <span class="{{ $revenueChange >= 0 ? 'text-emerald-600' : 'text-rose-600' }} text-sm font-black">{{ $percent($revenueChange) }}</span>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">2024: {{ $shortMoney($summary['revenue']['value_2024']) }}</p>
                </div>
                <div class="rounded-lg bg-white p-4 text-slate-900 shadow-lg">
                    <p class="text-xs font-bold uppercase text-slate-500">Laba Bersih</p>
                    <div class="mt-2 flex items-end justify-between gap-3">
                        <span class="text-2xl font-black text-blue-900">{{ $shortMoney($summary['net_profit']['value_2025']) }}</span>
                        <span class="{{ $profitChange >= 0 ? 'text-emerald-600' : 'text-rose-600' }} text-sm font-black">{{ $percent($profitChange) }}</span>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">2024: {{ $shortMoney($summary['net_profit']['value_2024']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="rounded-lg bg-blue-100 p-3 text-blue-800"><i data-lucide="landmark" class="h-5 w-5"></i></span>
                <div>
                    <p class="text-xs font-bold uppercase text-slate-500">Total Aset</p>
                    <p class="text-2xl font-black text-slate-950">{{ $shortMoney($summary['assets']['value_2025']) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="rounded-lg bg-emerald-100 p-3 text-emerald-700"><i data-lucide="circle-dollar-sign" class="h-5 w-5"></i></span>
                <div>
                    <p class="text-xs font-bold uppercase text-slate-500">Laba Usaha</p>
                    <p class="text-2xl font-black text-slate-950">{{ $shortMoney($summary['operating_profit']['value_2025']) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="rounded-lg bg-orange-100 p-3 text-orange-700"><i data-lucide="percent" class="h-5 w-5"></i></span>
                <div>
                    <p class="text-xs font-bold uppercase text-slate-500">Net Profit Margin</p>
                    <p class="text-2xl font-black text-slate-950">{{ $percent($summary['net_margin']['value_2025']) }}</p>
                </div>
            </div>
        </div>
        @foreach($participation as $item)
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="rounded-lg bg-slate-100 p-3 text-slate-800"><i data-lucide="{{ $item['label'] === 'Pertokoan' ? 'store' : 'hand-coins' }}" class="h-5 w-5"></i></span>
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-500">{{ $item['label'] }}</p>
                        <p class="text-2xl font-black text-slate-950">{{ $percent($item['rate']) }}</p>
                        <p class="text-xs text-slate-500">{{ number_format($item['active'], 0, ',', '.') }} dari {{ number_format($item['total'], 0, ',', '.') }} anggota</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-4 flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-black">Perbandingan Kinerja Utama</h2>
                    <p class="text-sm text-slate-500">Klik bar untuk melihat detail data di panel bawah.</p>
                </div>
                <i data-lucide="mouse-pointer-click" class="h-5 w-5 text-blue-700"></i>
            </div>
            <div class="chart-box"><canvas id="financialChart"></canvas></div>
            <div id="barDetail" class="mt-4 rounded-lg border border-blue-100 bg-blue-50 p-4">
                <p class="text-sm font-bold text-blue-950">Detail data akan muncul di sini saat bar diklik.</p>
                <p class="mt-1 text-sm text-blue-800">Contoh: klik Pendapatan 2025 atau Laba Bersih 2024.</p>
            </div>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-black">Insight Utama</h2>
            <div class="mt-4 space-y-3">
                @foreach($insights as $insight)
                    <div class="flex gap-3 rounded-lg border border-slate-100 bg-slate-50 p-3">
                        <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-900 text-white">
                            <i data-lucide="lightbulb" class="h-4 w-4"></i>
                        </span>
                        <p class="text-sm leading-6 text-slate-700">{{ $insight }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-black">Trend Rasio Utama</h2>
            <div class="chart-box"><canvas id="ratioTrendChart"></canvas></div>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-black">Partisipasi Anggota</h2>
            <p class="text-sm text-slate-500">Hanya 2 kategori: Pertokoan dan Pinjaman.</p>
            <div class="chart-box"><canvas id="participationChart"></canvas></div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-black">Kartu Rasio</h2>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                @foreach($ratioGroups as $category => $rows)
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-black uppercase tracking-wide text-blue-700">{{ $category }}</p>
                        <div class="mt-3 space-y-3">
                            @foreach($rows as $row)
                                <div class="rounded-lg bg-white p-3 shadow-sm">
                                    <div class="flex items-start justify-between gap-3">
                                        <p class="text-sm font-bold leading-5">{{ $row['ratio'] }}</p>
                                        <span class="{{ $row['diff'] >= 0 ? 'text-emerald-700 bg-emerald-50' : 'text-rose-700 bg-rose-50' }} rounded-md px-2 py-1 text-xs font-black">{{ $row['diff'] >= 0 ? '+' : '' }}{{ $percent($row['diff']) }}</span>
                                    </div>
                                    <p class="mt-2 text-2xl font-black text-blue-900">{{ $percent($row['value_2025']) }}</p>
                                    <p class="text-xs text-slate-500">vs 2024: {{ $percent($row['value_2024']) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-black">Ringkasan Rasio Keuangan</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-blue-950 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left font-bold">Kategori</th>
                            <th class="px-4 py-3 text-left font-bold">Rasio</th>
                            <th class="px-4 py-3 text-right font-bold">2025</th>
                            <th class="px-4 py-3 text-right font-bold">2024</th>
                            <th class="px-4 py-3 text-right font-bold">Selisih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($ratios as $row)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-semibold text-slate-700">{{ $row['category'] }}</td>
                                <td class="px-4 py-3">{{ $row['ratio'] }}</td>
                                <td class="px-4 py-3 text-right font-bold">{{ $percent($row['value_2025']) }}</td>
                                <td class="px-4 py-3 text-right">{{ $percent($row['value_2024']) }}</td>
                                <td class="{{ $row['diff'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }} px-4 py-3 text-right font-black">{{ $row['diff'] >= 0 ? '+' : '' }}{{ $percent($row['diff']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    const financialRows = @json($financials);
    const ratioRows = @json($ratios);
    const participationRows = @json(array_values($participation));

    const formatIDR = (value) => new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
    }).format(Number(value || 0));

    const formatPercent = (value) => `${Number(value || 0).toLocaleString('id-ID', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    })}%`;

    const compactMoney = (value) => new Intl.NumberFormat('id-ID', {
        notation: 'compact',
        maximumFractionDigits: 2
    }).format(Number(value || 0));

    document.addEventListener('DOMContentLoaded', function () {
        const mainRows = financialRows.filter((row) => [
            'Pendapatan',
            'Total Aset',
            'Laba Usaha',
            'Laba Bersih Tahun Berjalan'
        ].includes(row.label));

        const financialChart = new Chart(document.getElementById('financialChart'), {
            type: 'bar',
            data: {
                labels: mainRows.map((row) => row.label),
                datasets: [
                    {
                        label: '2025',
                        data: mainRows.map((row) => row.value_2025),
                        backgroundColor: '#0f4c81',
                        borderRadius: 8
                    },
                    {
                        label: '2024',
                        data: mainRows.map((row) => row.value_2024),
                        backgroundColor: '#9cc7df',
                        borderRadius: 8
                    }
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
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-xs font-black uppercase tracking-wide text-blue-700">${row.label} ${year}</p>
                                <p class="text-2xl font-black text-blue-950">${formatIDR(value)}</p>
                            </div>
                            <div class="rounded-lg bg-white px-4 py-3 text-sm shadow-sm">
                                <span class="font-bold text-slate-500">Perbandingan:</span>
                                <span class="${change >= 0 ? 'text-emerald-700' : 'text-rose-700'} font-black">${formatPercent(change)}</span>
                            </div>
                        </div>
                    `;
                },
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true } },
                    tooltip: { callbacks: { label: (ctx) => `${ctx.dataset.label}: ${formatIDR(ctx.parsed.y)}` } }
                },
                scales: {
                    y: { ticks: { callback: (value) => compactMoney(value) }, grid: { color: '#e2e8f0' } },
                    x: { grid: { display: false } }
                }
            }
        });

        new Chart(document.getElementById('ratioTrendChart'), {
            type: 'line',
            data: {
                labels: ratioRows.map((row) => row.ratio),
                datasets: [
                    {
                        label: '2025',
                        data: ratioRows.map((row) => row.value_2025),
                        borderColor: '#0f4c81',
                        backgroundColor: 'rgba(15, 76, 129, 0.12)',
                        tension: 0.35,
                        fill: true,
                        pointRadius: 4
                    },
                    {
                        label: '2024',
                        data: ratioRows.map((row) => row.value_2024),
                        borderColor: '#64748b',
                        backgroundColor: 'rgba(100, 116, 139, 0.08)',
                        tension: 0.35,
                        fill: true,
                        pointRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true } },
                    tooltip: { callbacks: { label: (ctx) => `${ctx.dataset.label}: ${formatPercent(ctx.parsed.y)}` } }
                },
                scales: {
                    y: { ticks: { callback: (value) => `${value}%` }, grid: { color: '#e2e8f0' } },
                    x: { ticks: { maxRotation: 45, minRotation: 25 }, grid: { display: false } }
                }
            }
        });

        new Chart(document.getElementById('participationChart'), {
            type: 'bar',
            data: {
                labels: participationRows.map((row) => row.label),
                datasets: [
                    {
                        label: 'Tingkat partisipasi',
                        data: participationRows.map((row) => row.rate),
                        backgroundColor: ['#0f766e', '#ea580c'],
                        borderRadius: 8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                const row = participationRows[ctx.dataIndex];
                                return `${formatPercent(row.rate)} (${row.active}/${row.total} anggota)`;
                            }
                        }
                    }
                },
                scales: {
                    y: { max: 100, ticks: { callback: (value) => `${value}%` }, grid: { color: '#e2e8f0' } },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>
@endpush
