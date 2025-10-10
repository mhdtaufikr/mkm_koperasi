<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Koperasi Karyawan PT MKM — Dashboard</title>

    {{-- Tailwind & Chart.js --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc; /* slate-50 */
        }
        .chart-container {
            position: relative;
            height: 350px;
            width: 100%;
        }
        .card {
            background: #fff;
            padding: 1.5rem;
            border-radius: 1.5rem; /* rounded-3xl */
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.07), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            transition: all .3s ease-in-out;
            width: 100%;
        }
        .card:hover {
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            transform: translateY(-5px);
        }
        /* Spinner */
        .spinner {
            border-top-color: #3498db;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Carousel Tab Styles */
        .carousel-btn {
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            color: #64748b; /* slate-500 */
            margin-bottom: -2px; /* Overlap with container border */
        }
        .active-carousel-btn {
            color: #0f172a; /* slate-900 */
            border-bottom-color: #2563eb; /* blue-600 */
        }
        .carousel-btn:hover {
            color: #0f172a; /* slate-900 */
        }

    </style>
</head>
<body class="text-slate-800">

<div class="px-4 sm:px-6 lg:px-8 pt-4 pb-8">

    {{-- Flash Messages for Laravel --}}
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-rose-50 text-rose-700 border border-rose-200">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Header --}}
    <header class="text-center mb-6">
        <h1 class="text-3xl sm:text-4xl font-extrabold bg-gradient-to-r from-blue-600 to-teal-400 bg-clip-text text-transparent">
            Koperasi Karyawan PT MKM
        </h1>
        <p class="text-slate-500 text-lg">Dashboard Monitor</p>
    </header>

    <main>
        <!-- Carousel Tab Buttons -->
        <div class="flex justify-center mb-4 border-b-2 border-slate-200">
            <button id="btn-carousel-1" class="carousel-btn active-carousel-btn">Ringkasan Utama</button>
            <button id="btn-carousel-2" class="carousel-btn">Analisis Keuangan</button>
            <button id="btn-carousel-3" class="carousel-btn">Laporan Bulanan</button>
        </div>

        <!-- Carousel Panels Container -->
        <div>
            <!-- Panel 1: Main Summary -->
            <div id="carousel-1" class="carousel-panel grid grid-cols-1 gap-6">
                {{-- SECTION: Kondisi & Partisipasi --}}
                <div class="card overflow-x-auto">
                    <table class="w-full text-sm text-left align-middle">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-slate-100 text-xs text-slate-700 uppercase font-semibold rounded-l-lg">
                                    Kondisi & Partisipasi
                                </th>
                                <th class="px-6 py-3 bg-slate-100 text-xs text-slate-700 uppercase font-semibold">
                                    Deskripsi
                                </th>
                                <th class="px-6 py-3 bg-slate-100 text-xs text-slate-700 uppercase font-semibold text-center" colspan="2">
                                    Simpan Pinjam
                                </th>
                                <th class="px-6 py-3 bg-slate-100 text-xs text-slate-700 uppercase font-semibold text-center" colspan="2">
                                    Pertokoan
                                </th>
                                <th class="px-6 py-3 bg-slate-100 text-xs text-slate-700 uppercase font-semibold text-center rounded-r-lg" colspan="2">
                                    Perdagangan & Jasa
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            {{-- Baris 1: Kondisi Keuangan --}}
                            <tr class="border-b border-slate-100">
                                <td class="px-6 py-4 font-semibold text-green-700 bg-green-50 align-top rounded-l-xl">
                                    KONDISI KEUANGAN KOPERASI
                                    <div class="mt-2 w-full text-center font-bold text-xl bg-slate-50 border border-green-200 rounded-xl py-1.5 text-green-700">
                                        {{ strtoupper($header['kondisi_keuangan'] ?? 'BAIK') }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 font-medium">Partisipasi Anggota (30 hari)</td>

                                {{-- Simpan Pinjam --}}
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <span class="block w-full max-w-[6rem] bg-slate-50 border border-slate-200 rounded-lg py-1 font-semibold text-slate-700">
                                            {{ number_format($header['partisipasi']['simpan_pinjam']['active'] ?? 0, 0, ',', '.') }}
                                        </span>
                                        <span class="font-semibold">/</span>
                                        <span class="block w-full max-w-[6rem] bg-slate-50 border border-slate-200 rounded-lg py-1 font-semibold text-slate-700">
                                            {{ number_format($header['partisipasi']['simpan_pinjam']['total'] ?? 0, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center font-semibold text-slate-700">
                                    {{ number_format($header['partisipasi']['simpan_pinjam']['rate'] ?? 0, 0) }}%
                                </td>

                                {{-- Pertokoan --}}
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <span class="block w-full max-w-[6rem] bg-slate-50 border border-slate-200 rounded-lg py-1 font-semibold text-slate-700">
                                            {{ number_format($header['partisipasi']['pertokoan']['active'] ?? 0, 0, ',', '.') }}
                                        </span>
                                        <span class="font-semibold">/</span>
                                        <span class="block w-full max-w-[6rem] bg-slate-50 border border-slate-200 rounded-lg py-1 font-semibold text-slate-700">
                                            {{ number_format($header['partisipasi']['pertokoan']['total'] ?? 0, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center font-semibold text-slate-700">
                                    {{ number_format($header['partisipasi']['pertokoan']['rate'] ?? 0, 0) }}%
                                </td>

                                {{-- Perdagangan & Jasa --}}
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <span class="block w-full max-w-[6rem] bg-slate-50 border border-slate-200 rounded-lg py-1 font-semibold text-slate-700">
                                            {{ number_format($header['partisipasi']['perdagangan_jasa']['active'] ?? 0, 0, ',', '.') }}
                                        </span>
                                        <span class="font-semibold">/</span>
                                        <span class="block w-full max-w-[6rem] bg-slate-50 border border-slate-200 rounded-lg py-1 font-semibold text-slate-700">
                                            {{ number_format($header['partisipasi']['perdagangan_jasa']['total'] ?? 0, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center font-semibold text-slate-700 rounded-r-xl">
                                    {{ number_format($header['partisipasi']['perdagangan_jasa']['rate'] ?? 0, 0) }}%
                                </td>
                            </tr>

                            {{-- Baris 2: Tingkat Partisipasi --}}
                            <tr>
                                <td class="px-6 py-4 font-semibold text-green-700 bg-green-50 align-top rounded-l-xl">
                                    TINGKAT PARTISIPASI ANGGOTA
                                    <span class="block text-xl font-bold">
                                        {{ strtoupper($header['partisipasi']['tingkat_partisipasi']['kategori'] ?? 'BAIK') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-medium">Transaksi Partisipasi Anggota</td>

                                <td class="px-6 py-4 text-center font-semibold" colspan="2">
                                    {{ number_format($header['partisipasi']['tingkat_partisipasi']['sp_rate'] ?? 0, 0) }}%
                                </td>
                                <td class="px-6 py-4 text-center font-semibold" colspan="2">
                                    {{ number_format($header['partisipasi']['tingkat_partisipasi']['toko_rate'] ?? 0, 0) }}%
                                </td>
                                <td class="px-6 py-4 text-center font-semibold rounded-r-xl" colspan="2">
                                    {{ number_format($header['partisipasi']['tingkat_partisipasi']['jasa_rate'] ?? 0, 0) }}%
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>


             {{-- SECTION: Anggota & Proyeksi Forms --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">

    {{-- Card: Anggota --}}
    <div class="card md:col-span-2 lg:col-span-2">
        <div class="grid grid-cols-3 gap-4 items-start">
            <div class="text-center">
                <label class="text-sm text-slate-500 block mb-1">Anggota Awal</label>
                <input type="text"
                    value="{{ number_format($members['initial'] ?? 0, 0, ',', '.') }}"
                    readonly
                    class="w-full px-3 py-2 border rounded-xl bg-slate-50 text-center font-bold text-2xl text-slate-700 cursor-not-allowed" />
            </div>
            <div class="text-center">
                <label class="text-sm text-slate-500 block mb-1">Penambahan</label>
                <input type="text"
                    value="{{ number_format($members['new'] ?? 0, 0, ',', '.') }}"
                    readonly
                    class="w-full px-3 py-2 border rounded-xl bg-slate-50 text-center font-bold text-2xl text-slate-700 cursor-not-allowed" />
                <p class="text-xs text-green-600 font-semibold mt-1">
                    +{{ number_format($members['growth_pct'] ?? 0, 2, ',', '.') }}%
                </p>
            </div>
            <div class="text-center">
                <label class="text-sm text-slate-500 block mb-1">Anggota Akhir</label>
                <input type="text"
                    value="{{ number_format($members['final'] ?? 0, 0, ',', '.') }}"
                    readonly
                    class="w-full px-3 py-2 border rounded-xl bg-blue-50 text-center font-bold text-2xl text-blue-600 cursor-not-allowed" />
            </div>
        </div>
    </div>

    {{-- Card: Proyeksi SHU 2024 --}}
    <div class="card text-center flex flex-col justify-center">
        <div class="space-y-3">
            <div class="bg-blue-100 text-blue-600 rounded-full p-3 mx-auto w-12 h-12 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <line x1="12" y1="19" x2="12" y2="5"></line>
                    <polyline points="5 12 12 5 19 12"></polyline>
                </svg>
            </div>
            <div>
                <label class="text-sm text-slate-500">Proyeksi SHU 2024</label>
                <input type="text"
                    value="Rp {{ number_format($projections['shu_2024'] ?? 0, 0, ',', '.') }}"
                    readonly
                    class="w-full px-3 py-2 border rounded-xl bg-slate-50 text-center font-bold text-xl mt-1 cursor-not-allowed text-slate-700" />
            </div>
        </div>
    </div>

    {{-- Card: Proyeksi SHU 2025 --}}
    <div class="card text-center flex flex-col justify-center">
        <div class="space-y-3">
            <div class="bg-teal-100 text-teal-600 rounded-full p-3 mx-auto w-12 h-12 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <div>
                <label class="text-sm text-slate-500">Proyeksi SHU 2025</label>
                <input type="text"
                    value="Rp {{ number_format($projections['shu_2025'] ?? 0, 0, ',', '.') }}"
                    readonly
                    class="w-full px-3 py-2 border rounded-xl bg-slate-50 text-center font-bold text-xl mt-1 cursor-not-allowed text-slate-700" />
            </div>
        </div>
    </div>

    {{-- Card: Alokasi Pinjaman --}}
    <div class="card text-center flex flex-col justify-center">
        <div class="space-y-3">
            <div class="bg-amber-100 text-amber-600 rounded-full p-3 mx-auto w-12 h-12 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <line x1="12" y1="1" x2="12" y2="23"></line>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
            </div>
            <div>
                <label class="text-sm text-slate-500">Alokasi Pinjaman</label>
                <input type="text"
                    value="Rp {{ number_format($projections['loan_allocation'] ?? 0, 0, ',', '.') }}"
                    readonly
                    class="w-full px-3 py-2 border rounded-xl bg-slate-50 text-center font-bold text-xl mt-1 cursor-not-allowed text-slate-700" />
            </div>
        </div>
    </div>
</div>

            </div>

            <!-- Panel 2: Financial Analysis -->
            <div id="carousel-2" class="carousel-panel hidden grid grid-cols-1 gap-6">
                 {{-- SECTION: Rasio, Chart, Komposisi --}}
                <div class="grid grid-cols-1 lg:grid-cols-6 gap-6">
                    <div class="lg:col-span-1">
                        <div class="card h-full flex flex-col">
                            <h3 class="font-bold text-lg mb-4">Rasio Keuangan</h3>
                            <div class="space-y-5 flex-grow">
                                <div class="flex items-start gap-4">
                                    <div class="bg-green-100 text-green-600 rounded-lg p-2"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h6"></path><path d="M3 10h18"></path><path d="m16 20 2-2-2-2"></path><path d="M18 18h-5"></path></svg></div>
                                    <div>
                                        <p class="font-semibold leading-tight">Net Profit Margin</p><span class="text-sm font-bold text-green-600">{{ $ratios['npm_percent'] !== null ? $ratios['npm_percent'].'%' : '-' }}</span>
                                    </div>
                                </div>
                                <div class="flex items-start gap-4">
                                    <div class="bg-yellow-100 text-yellow-600 rounded-lg p-2"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16.5 13.4a1 1 0 0 0-1 0l-2.5 1.4a1 1 0 0 0 0 1.8l2.5 1.4a1 1 0 0 0 1 0l2.5-1.4a1 1 0 0 0 0-1.8l-2.5-1.4z"></path><path d="m20.5 17.8-2.5 1.4a1 1 0 0 1-1 0l-2.5-1.4a1 1 0 0 1 0-1.8l2.5-1.4a1 1 0 0 1 1 0l2.5 1.4a1 1 0 0 1 0 1.8z"></path></svg></div>
                                    <div>
                                        <p class="font-semibold leading-tight">Debt Ratio</p><span class="text-sm font-bold text-yellow-600">{{ $ratios['dr_percent'] !== null ? $ratios['dr_percent'].'%' : '-' }}</span>
                                    </div>
                                </div>
                                <div class="flex items-start gap-4">
                                    <div class="bg-blue-100 text-blue-600 rounded-lg p-2"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 5 4 4-10 10-4 1 1-4Z"></path><path d="M14.5 6.5 17.5 9.5"></path></svg></div>
                                    <div>
                                        <p class="font-semibold leading-tight">Current Ratio</p><span class="text-sm font-bold text-blue-600">{{ $ratios['cr_times'] !== null ? number_format($ratios['cr_times'], 2).'x' : '-' }}</span>
                                    </div>
                                </div>
                                <div class="flex items-start gap-4">
                                    <div class="bg-indigo-100 text-indigo-600 rounded-lg p-2"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"></path><path d="m19 9-5 5-4-4-3 3"></path></svg></div>
                                    <div>
                                        <p class="font-semibold leading-tight">Return on Equity</p><span class="text-sm font-bold text-indigo-600">{{ $ratios['roe_percent'] !== null ? $ratios['roe_percent'].'%' : '-' }}</span>
                                    </div>
                                </div>
                                <div class="flex items-start gap-4">
                                    <div class="bg-fuchsia-100 text-fuchsia-600 rounded-lg p-2"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V10"></path><path d="M18 20V4"></path><path d="M6 20v-6"></path></svg></div>
                                    <div>
                                        <p class="font-semibold leading-tight">Equity vs Total Simpanan</p><span class="text-sm font-bold text-fuchsia-600">{{ isset($ratios['equity_vs_deposits_percent']) && $ratios['equity_vs_deposits_percent'] !== null ? $ratios['equity_vs_deposits_percent'].'%' : '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>{{--
                    <div class="lg:col-span-3">
                        <div class="card h-full flex flex-col p-6">
                            <h4 class="font-semibold mb-4">Update Snapshot (Neraca)</h4>
                            <form method="POST" action="{{ route('coop.dashboard.update.balance') }}" class="space-y-4">
                                @csrf @method('PUT')
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    <div><label class="text-xs text-slate-500">Aset</label> <input type="number" name="total_assets" class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-400" value="{{ $composition['assets'] ?? 0 }}" min="0" step="1" required></div>
                                    <div><label class="text-xs text-slate-500">Kewajiban</label> <input type="number" name="total_liabilities" class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-400" value="{{ $composition['liabilities'] ?? 0 }}" min="0" step="1" required></div>
                                    <div><label class="text-xs text-slate-500">Ekuitas</label> <input type="number" name="total_equity" class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-400" value="{{ $composition['equity'] ?? 0 }}" min="0" step="1" required></div>
                                    <div><label class="text-xs text-slate-500">Current Assets</label> <input type="number" name="current_assets" class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-400" value="{{ old('current_assets', $snapshot['current_assets'] ?? '') }}" placeholder="mis. 420000000"></div>
                                    <div><label class="text-xs text-slate-500">Current Liabilities</label> <input type="number" name="current_liabilities" class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-400" value="{{ old('current_liabilities', $snapshot['current_liabilities'] ?? '') }}" placeholder="mis. 200000000"></div>
                                    <div><label class="text-xs text-slate-500">Net Income TTM</label> <input type="number" name="net_income_ttm" class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-400" value="{{ old('net_income_ttm', $snapshot['net_income_ttm'] ?? '') }}" placeholder="mis. 168000000"></div>
                                    <div class="md:col-span-3 col-span-2"><label class="text-xs text-slate-500">Total Simpanan</label> <input type="number" name="total_deposits" class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-400" value="{{ old('total_deposits', $snapshot['total_deposits'] ?? '') }}" placeholder="mis. 750000000"></div>
                                </div>
                                <div class="text-right"><button class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl font-semibold bg-amber-500 text-white hover:bg-amber-600">Save Snapshot</button></div>
                            </form>
                        </div>
                    </div> --}}
                    <div class="lg:col-span-5 card">
                        <h3 class="font-bold text-lg mb-4">Neraca Koperasi</h3>
                        <div class="chart-container"><canvas id="lineChart"></canvas></div>
                    </div>
                    <div class="lg:col-span-6 card grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                        <div class="md:col-span-2">
                            <h3 class="font-bold text-lg mb-4">Komposisi Aset, Kewajiban, & Ekuitas</h3>
                            @php
                                $assets = $composition['assets'] ?: 1;
                                $pctL = round(($composition['liabilities'] ?? 0) / $assets * 100);
                                $pctE = round(($composition['equity'] ?? 0) / $assets * 100);
                            @endphp
                            <div class="space-y-4">
                                <div class="flex items-center gap-4"><span class="w-28 text-sm font-medium text-right text-slate-600">ASET</span>
                                    <div class="w-full bg-slate-200 rounded-full h-6 overflow-hidden"><div class="bg-blue-600 h-6" style="width: 100%"></div></div><span class="w-40 text-sm font-bold text-left">Rp {{ number_format($composition['assets'] ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center gap-4"><span class="w-28 text-sm font-medium text-right text-slate-600">KEWAJIBAN</span>
                                    <div class="w-full bg-slate-200 rounded-full h-6 overflow-hidden"><div class="bg-violet-500 h-6" style="width: {{ $pctL }}%"></div></div><span class="w-40 text-sm font-bold text-left">Rp {{ number_format($composition['liabilities'] ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center gap-4"><span class="w-28 text-sm font-medium text-right text-slate-600">EKUITAS</span>
                                    <div class="w-full bg-slate-200 rounded-full h-6 overflow-hidden"><div class="bg-teal-500 h-6" style="width: {{ $pctE }}%"></div></div><span class="w-40 text-sm font-bold text-left">Rp {{ number_format($composition['equity'] ?? 0, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="md:col-span-1 chart-container" style="height: 250px;"><canvas id="doughnutChart"></canvas></div>
                    </div>
                </div>
            </div>

            <!-- Panel 3: Monthly Reports -->
            <div id="carousel-3" class="carousel-panel hidden grid grid-cols-1 gap-6">
                {{-- Form: Edit Financial Monthlies --}}
                <div class="card">
                    <form method="POST" action="{{ route('coop.dashboard.update.monthlies') }}">
                        @csrf
                        @method('PUT')
                        <h3 class="font-bold text-lg mb-4">Update Financial Monthlies (Tahun Berjalan)</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 bg-slate-100 text-xs text-slate-700 uppercase font-semibold">Bulan</th>
                                        <th class="px-6 py-3 bg-slate-100 text-xs text-slate-700 uppercase font-semibold">Pendapatan</th>
                                        <th class="px-6 py-3 bg-slate-100 text-xs text-slate-700 uppercase font-semibold">HPP</th>
                                        <th class="px-6 py-3 bg-slate-100 text-xs text-slate-700 uppercase font-semibold">Pengeluaran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($line['labels'] as $i => $lbl)
                                    @php
                                        $monthNum = $i + 1;
                                        $rev = $line['revenue'][$i] ?? 0;
                                        $cogs = $line['cogs'][$i] ?? 0;
                                        $opex = $line['opex'][$i] ?? 0;
                                    @endphp
                                    <tr class="border-b border-slate-100">
                                        <td class="px-6 py-4 font-semibold">{{ $lbl }}</td>
                                        <td class="px-6 py-4"><input type="number" class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-400" name="rows[{{ $monthNum }}][revenue]" value="{{ $rev }}" step="1" min="0"></td>
                                        <td class="px-6 py-4"><input type="number" class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-400" name="rows[{{ $monthNum }}][cogs]" value="{{ $cogs }}" step="1" min="0"></td>
                                        <td class="px-6 py-4"><input type="number" class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-400" name="rows[{{ $monthNum }}][opex]" value="{{ $opex }}" step="1" min="0"></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 text-right">
                            <button class="inline-flex items-center justify-center px-4 py-2 rounded-xl font-semibold transition bg-slate-800 text-white hover:bg-slate-900">Save Monthlies</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </main>
</div>

{{-- Chart.js Initialization & Carousel Logic --}}
<script>
    // Pass PHP data to JavaScript
    const lineLabels = @json($line['labels'] ?? []);
    const lineRevenue = @json($line['revenue'] ?? []);
    const lineCogs = @json($line['cogs'] ?? []);
    const lineOpex = @json($line['opex'] ?? []);

    // Doughnut chart data needs to be an array of numbers
    const doughnutData = [
        {{ $composition['equity'] ?? 0 }},
        {{ $composition['liabilities'] ?? 0 }}
    ];

    document.addEventListener('DOMContentLoaded', () => {
        // Carousel Logic
        const buttons = document.querySelectorAll('.carousel-btn');
        const panels = document.querySelectorAll('.carousel-panel');
        let carouselInterval;
        let currentIndex = 0;

        function switchToTab(index) {
            // Hide all panels and remove active class from all buttons
            panels.forEach(panel => panel.classList.add('hidden'));
            buttons.forEach(btn => btn.classList.remove('active-carousel-btn'));

            // Show the target panel and set active class on the target button
            const targetPanelId = buttons[index].id.replace('btn-', '');
            document.getElementById(targetPanelId).classList.remove('hidden');
            buttons[index].classList.add('active-carousel-btn');

            currentIndex = index;
        }

        function startAutoSlide() {
            // Clear any existing interval to prevent duplicates
            clearInterval(carouselInterval);
            carouselInterval = setInterval(() => {
                const nextIndex = (currentIndex + 1) % buttons.length;
                switchToTab(nextIndex);
            }, 5000); // 5 seconds
        }

        // Manual click handling
        buttons.forEach((button, index) => {
            button.addEventListener('click', () => {
                switchToTab(index);
                // Restart the auto-slide timer after a manual click
                startAutoSlide();
            });
        });

        // Initial start of the auto-slide
        startAutoSlide();


        // Initialize Line Chart
if (document.getElementById('lineChart')) {
    const ctxLine = document.getElementById('lineChart').getContext('2d');
    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: lineLabels,
            datasets: [
                {
                    label: 'Pendapatan',
                    data: lineRevenue,
                    borderColor: 'rgb(96, 165, 250)',          // 💙 Aset (blue-400)
                    backgroundColor: 'rgba(96, 165, 250, 0.15)',
                    tension: 0.4, fill: true, pointRadius: 3
                },
                {
                    label: 'HPP',
                    data: lineCogs,
                    borderColor: 'rgb(45, 212, 191)',          // 🩵 Ekuitas (teal-400)
                    backgroundColor: 'rgba(45, 212, 191, 0.15)',
                    tension: 0.4, fill: true, pointRadius: 3
                },
                {
                    label: 'Pengeluaran',
                    data: lineOpex,
                    borderColor: 'rgb(167, 139, 250)',         // 💜 Kewajiban (violet-400)
                    backgroundColor: 'rgba(167, 139, 250, 0.15)',
                    tension: 0.4, fill: true, pointRadius: 3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#e5e7eb' },
                    ticks: {
                        callback: (value) =>
                            (value).toLocaleString('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                maximumFractionDigits: 0,
                                notation: 'compact'
                            })
                    }
                },
                x: { grid: { display: false } }
            },
            plugins: {
                legend: {
                    position: 'top',
                    align: 'end',
                    labels: { usePointStyle: true, boxWidth: 8 }
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: (context) => {
                            let label = context.dataset.label ? context.dataset.label + ': ' : '';
                            if (context.parsed.y != null)
                                label += context.parsed.y.toLocaleString('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR'
                                });
                            return label;
                        }
                    }
                }
            }
        }
    });
}


        // Initialize Doughnut Chart
        if (document.getElementById('doughnutChart')) {
            const ctxDoughnut = document.getElementById('doughnutChart').getContext('2d');
            new Chart(ctxDoughnut, {
                type: 'doughnut',
                data: {
                    labels: ['Ekuitas', 'Kewajiban'],
                    datasets: [{
                        label: 'Komposisi Neraca',
                        data: doughnutData,
                        backgroundColor: ['rgb(20, 184, 166)','rgb(139, 92, 246)'],
                        hoverOffset: 8,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, cutout: '70%',
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            callbacks: {
                                label: (context) => {
                                    let label = context.label ? context.label + ': ' : '';
                                    if (context.parsed != null) label += context.parsed.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' });
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>

</body>
</html>

