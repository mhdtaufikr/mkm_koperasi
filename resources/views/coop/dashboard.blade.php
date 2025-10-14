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
        /* Spinner for loading state (if needed for future JS interactions) */
        .spinner {
            border-top-color: #3498db;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="text-slate-800">

<div class="container mx-auto p-4 sm:p-6 lg:p-8">

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
    <header class="text-center mb-10">
        <h1 class="text-4xl font-extrabold bg-gradient-to-r from-blue-600 to-teal-400 bg-clip-text text-transparent pb-2">
            Koperasi Karyawan PT MKM
        </h1>
        <p class="text-slate-500 mt-1 text-lg">Dashboard Monitor</p>
    </header>

    <main class="grid grid-cols-1 gap-6">

        {{-- SECTION: Kondisi & Partisipasi --}}
        <div class="card overflow-x-auto">
            <form method="POST" action="{{ route('coop.dashboard.update.participation') }}" class="space-y-2">
                @csrf
                @method('PUT')

                <table class="w-full text-sm text-left align-middle">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-slate-100 text-xs text-slate-700 uppercase font-semibold rounded-l-lg">Kondisi & Partisipasi</th>
                            <th class="px-6 py-3 bg-slate-100 text-xs text-slate-700 uppercase font-semibold">Deskripsi</th>
                            <th class="px-6 py-3 bg-slate-100 text-xs text-slate-700 uppercase font-semibold text-center" colspan="2">Simpan Pinjam</th>
                            <th class="px-6 py-3 bg-slate-100 text-xs text-slate-700 uppercase font-semibold text-center" colspan="2">Pertokoan</th>
                            <th class="px-6 py-3 bg-slate-100 text-xs text-slate-700 uppercase font-semibold text-center rounded-r-lg" colspan="2">Perdagangan & Jasa</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Row 1: Kondisi Keuangan --}}
                        <tr class="border-b border-slate-100">
                            <td class="px-6 py-4 font-semibold text-green-700 bg-green-50 align-top rounded-l-xl">
                                KONDISI KEUANGAN KOPERASI
                                <input type="text" name="kondisi_keuangan" value="{{ $header['kondisi_keuangan'] ?? 'BAIK' }}" class="mt-2 w-full text-center font-bold text-xl border border-green-300 focus:ring-2 focus:ring-green-400 rounded-xl py-1.5" />
                            </td>
                            <td class="px-6 py-4 font-medium">Partisipasi Anggota (30 hari)</td>
                            {{-- Simpan Pinjam --}}
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <input type="number" name="sp_active" class="w-full max-w-[6rem] text-center border border-slate-300 rounded-lg focus:ring-1 focus:ring-slate-400" value="{{ $header['partisipasi']['simpan_pinjam']['active'] ?? 0 }}" min="0">
                                    <span class="font-semibold">/</span>
                                    <input type="number" name="sp_total" class="w-full max-w-[6rem] text-center border border-slate-300 rounded-lg focus:ring-1 focus:ring-slate-400" value="{{ $header['partisipasi']['simpan_pinjam']['total'] ?? 0 }}" min="0">
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center font-semibold text-slate-700">
                                {{ number_format($header['partisipasi']['simpan_pinjam']['rate'] ?? 0, 0) }}%
                            </td>
                            {{-- Pertokoan --}}
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <input type="number" name="toko_active" class="w-full max-w-[6rem] text-center border border-slate-300 rounded-lg focus:ring-1 focus:ring-slate-400" value="{{ $header['partisipasi']['pertokoan']['active'] ?? 0 }}" min="0">
                                    <span class="font-semibold">/</span>
                                    <input type="number" name="toko_total" class="w-full max-w-[6rem] text-center border border-slate-300 rounded-lg focus:ring-1 focus:ring-slate-400" value="{{ $header['partisipasi']['pertokoan']['total'] ?? 0 }}" min="0">
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center font-semibold text-slate-700">
                                {{ number_format($header['partisipasi']['pertokoan']['rate'] ?? 0, 0) }}%
                            </td>
                            {{-- Perdagangan & Jasa --}}
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <input type="number" name="jasa_active" class="w-full max-w-[6rem] text-center border border-slate-300 rounded-lg focus:ring-1 focus:ring-slate-400" value="{{ $header['partisipasi']['perdagangan_jasa']['active'] ?? 0 }}" min="0">
                                    <span class="font-semibold">/</span>
                                    <input type="number" name="jasa_total" class="w-full max-w-[6rem] text-center border border-slate-300 rounded-lg focus:ring-1 focus:ring-slate-400" value="{{ $header['partisipasi']['perdagangan_jasa']['total'] ?? 0 }}" min="0">
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center font-semibold text-slate-700 rounded-r-xl">
                                {{ number_format($header['partisipasi']['perdagangan_jasa']['rate'] ?? 0, 0) }}%
                            </td>
                        </tr>
                        {{-- Row 2: Tingkat Partisipasi --}}
                        <tr>
                            <td class="px-6 py-4 font-semibold text-green-700 bg-green-50 align-top rounded-l-xl">
                                TINGKAT PARTISIPASI ANGGOTA
                                <span class="block text-xl font-bold">BAIK</span>
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
                <div class="text-right p-4 border-t border-slate-100">
                    <button type="submit" class="inline-flex items-center gap-2 bg-emerald-600 text-white font-semibold px-5 py-2.5 rounded-lg hover:bg-emerald-700 shadow-sm transition-all">
                        💾 Save Kondisi & Partisipasi
                    </button>
                </div>
            </form>
        </div>

        {{-- SECTION: Anggota & Proyeksi Forms --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <!-- Form: Edit Members -->
            <div class="card md:col-span-2 lg:col-span-2">
                <form method="POST" action="{{ route('coop.dashboard.update.members') }}" class="grid grid-cols-3 gap-4 items-start">
                    @csrf
                    @method('PUT')
                    <div class="text-center">
                        <label for="initial_members" class="text-sm text-slate-500 block mb-1">Anggota Awal</label>
                        <input id="initial_members" type="number" name="initial_members" value="{{ $members['initial'] ?? 0 }}" min="0" required class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-400 text-center font-bold text-2xl" />
                    </div>
                    <div class="text-center">
                        <label for="new_members" class="text-sm text-slate-500 block mb-1">Penambahan</label>
                        <input id="new_members" type="number" name="new_members" value="{{ $members['new'] ?? 0 }}" min="0" required class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-400 text-center font-bold text-2xl" />
                        <p class="text-xs text-green-600 font-semibold mt-1">+{{ number_format($members['growth_pct'] ?? 0, 2) }}%</p>
                    </div>
                    <div class="text-center">
                        <label for="final_members" class="text-sm text-slate-500 block mb-1">Anggota Akhir</label>
                        <input id="final_members" type="text" value="{{ $members['final'] ?? 0 }}" disabled class="w-full px-3 py-2 border rounded-xl bg-slate-50 text-center font-bold text-2xl text-blue-600 cursor-not-allowed" />
                    </div>
                    <div class="col-span-3 text-right">
                        <button class="inline-flex items-center justify-center px-4 py-2 rounded-xl font-semibold transition bg-emerald-600 text-white hover:bg-emerald-700">Save Members</button>
                    </div>
                </form>
            </div>
            <!-- Form: Proyeksi SHU 2024 -->
            <div class="card text-center flex flex-col justify-center">
                <form method="POST" action="{{ route('coop.dashboard.update.projections') }}" class="space-y-3">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="projection" value="SHU_2024" />
                    <div class="bg-blue-100 text-blue-600 rounded-full p-3 mx-auto w-12 h-12 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg>
                    </div>
                    <div>
                        <label for="shu_2024" class="text-sm text-slate-500">Proyeksi SHU 2024</label>
                        <input id="shu_2024" type="number" name="amount" value="{{ $projections['shu_2024'] ?? 0 }}" min="0" step="1" class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-400 text-center font-bold text-xl mt-1" />
                    </div>
                    <button class="w-full inline-flex items-center justify-center px-4 py-2 rounded-xl font-semibold transition bg-slate-800 text-white hover:bg-slate-900">Save</button>
                </form>
            </div>
            <!-- Form: Proyeksi SHU 2025 -->
            <div class="card text-center flex flex-col justify-center">
                <form method="POST" action="{{ route('coop.dashboard.update.projections') }}" class="space-y-3">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="projection" value="SHU_2025" />
                    <div class="bg-teal-100 text-teal-600 rounded-full p-3 mx-auto w-12 h-12 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </div>
                    <div>
                        <label for="shu_2025" class="text-sm text-slate-500">Proyeksi SHU 2025</label>
                        <input id="shu_2025" type="number" name="amount" value="{{ $projections['shu_2025'] ?? 0 }}" min="0" step="1" class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-400 text-center font-bold text-xl mt-1" />
                    </div>
                    <button class="w-full inline-flex items-center justify-center px-4 py-2 rounded-xl font-semibold transition bg-slate-800 text-white hover:bg-slate-900">Save</button>
                </form>
            </div>
            <!-- Form: Alokasi Pinjaman -->
            <div class="card text-center flex flex-col justify-center">
                <form method="POST" action="{{ route('coop.dashboard.update.projections') }}" class="space-y-3">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="projection" value="LOAN_ALLOCATION" />
                    <div class="bg-amber-100 text-amber-600 rounded-full p-3 mx-auto w-12 h-12 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    </div>
                    <div>
                        <label for="loan_allocation" class="text-sm text-slate-500">Alokasi Pinjaman</label>
                        <input id="loan_allocation" type="number" name="amount" value="{{ $projections['loan_allocation'] ?? 0 }}" min="0" step="1" class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-400 text-center font-bold text-xl mt-1" />
                    </div>
                    <button class="w-full inline-flex items-center justify-center px-4 py-2 rounded-xl font-semibold transition bg-slate-800 text-white hover:bg-slate-900">Save</button>
                </form>
            </div>
        </div>

{{-- SECTION: Rasio + Form Snapshot + Chart --}}
<div class="grid grid-cols-1 lg:grid-cols-6 gap-6">

            {{-- Kolom Rasio Keuangan (2 kolom) --}}
            <div class="lg:col-span-1">
            <div class="card h-full flex flex-col">
                <h3 class="font-bold text-lg mb-4">Rasio Keuangan</h3>

                <div class="space-y-5 flex-grow">
                {{-- Net Profit Margin --}}
                <div class="flex items-start gap-4">
                    <div class="bg-green-100 text-green-600 rounded-lg p-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h6"></path><path d="M3 10h18"></path><path d="m16 20 2-2-2-2"></path><path d="M18 18h-5"></path></svg>
                    </div>
                    <div>
                    <p class="font-semibold leading-tight">Net Profit Margin</p>
                    <span class="text-sm font-bold text-green-600">
                        {{ $ratios['npm_percent'] !== null ? $ratios['npm_percent'].'%' : '-' }}
                    </span>
                    </div>
                </div>

                {{-- Debt Ratio --}}
                <div class="flex items-start gap-4">
                    <div class="bg-yellow-100 text-yellow-600 rounded-lg p-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16.5 13.4a1 1 0 0 0-1 0l-2.5 1.4a1 1 0 0 0 0 1.8l2.5 1.4a1 1 0 0 0 1 0l2.5-1.4a1 1 0 0 0 0-1.8l-2.5-1.4z"></path><path d="m20.5 17.8-2.5 1.4a1 1 0 0 1-1 0l-2.5-1.4a1 1 0 0 1 0-1.8l2.5-1.4a1 1 0 0 1 1 0l2.5 1.4a1 1 0 0 1 0 1.8z"></path></svg>
                    </div>
                    <div>
                    <p class="font-semibold leading-tight">Debt Ratio</p>
                    <span class="text-sm font-bold text-yellow-600">
                        {{ $ratios['dr_percent'] !== null ? $ratios['dr_percent'].'%' : '-' }}
                    </span>
                    </div>
                </div>

                {{-- Current Ratio --}}
                <div class="flex items-start gap-4">
                    <div class="bg-blue-100 text-blue-600 rounded-lg p-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 5 4 4-10 10-4 1 1-4Z"></path><path d="M14.5 6.5 17.5 9.5"></path></svg>
                    </div>
                    <div>
                    <p class="font-semibold leading-tight">Current Ratio</p>
                    <span class="text-sm font-bold text-blue-600">
                        {{ $ratios['cr_times'] !== null ? number_format($ratios['cr_times'], 2).'x' : '-' }}
                    </span>
                    </div>
                </div>

                {{-- Return on Equity --}}
                <div class="flex items-start gap-4">
                    <div class="bg-indigo-100 text-indigo-600 rounded-lg p-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"></path><path d="m19 9-5 5-4-4-3 3"></path></svg>
                    </div>
                    <div>
                    <p class="font-semibold leading-tight">Return on Equity</p>
                    <span class="text-sm font-bold text-indigo-600">
                        {{ $ratios['roe_percent'] !== null ? $ratios['roe_percent'].'%' : '-' }}
                    </span>
                    </div>
                </div>

                {{-- Equity vs Total Simpanan --}}
                <div class="flex items-start gap-4">
                    <div class="bg-fuchsia-100 text-fuchsia-600 rounded-lg p-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V10"></path><path d="M18 20V4"></path><path d="M6 20v-6"></path></svg>
                    </div>
                    <div>
                    <p class="font-semibold leading-tight">Equity vs Total Simpanan</p>
                    <span class="text-sm font-bold text-fuchsia-600">
                        {{ isset($ratios['equity_vs_deposits_percent']) && $ratios['equity_vs_deposits_percent'] !== null
                            ? $ratios['equity_vs_deposits_percent'].'%' : '-' }}
                    </span>
                    </div>
                </div>
                </div>
            </div>
            </div>

            {{-- Kolom Form Snapshot (dibesarkan jadi 2 kolom) --}}
            <div class="lg:col-span-3">
            <div class="card h-full flex flex-col p-6">
                <h4 class="font-semibold mb-4">Update Snapshot (Neraca)</h4>
                <form method="POST" action="{{ route('coop.dashboard.update.balance') }}" class="space-y-4">
                @csrf
                @method('PUT')

                {{-- lebih lega: 3 kolom di lg, 2 kolom di md --}}
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                    <label class="text-xs text-slate-500">Aset</label>
                    <input type="number" name="total_assets"
                            class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-400"
                            value="{{ $composition['assets'] ?? 0 }}" min="0" step="1" required>
                    </div>

                    <div>
                    <label class="text-xs text-slate-500">Kewajiban</label>
                    <input type="number" name="total_liabilities"
                            class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-400"
                            value="{{ $composition['liabilities'] ?? 0 }}" min="0" step="1" required>
                    </div>

                    <div>
                    <label class="text-xs text-slate-500">Ekuitas</label>
                    <input type="number" name="total_equity"
                            class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-400"
                            value="{{ $composition['equity'] ?? 0 }}" min="0" step="1" required>
                    </div>

                    <div>
                    <label class="text-xs text-slate-500">Current Assets</label>
                    <input type="number" name="current_assets"
                            class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-400"
                            value="{{ old('current_assets', $snapshot['current_assets'] ?? '') }}"
                            placeholder="mis. 420000000">
                    </div>

                    <div>
                    <label class="text-xs text-slate-500">Current Liabilities</label>
                    <input type="number" name="current_liabilities"
                            class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-400"
                            value="{{ old('current_liabilities', $snapshot['current_liabilities'] ?? '') }}"
                            placeholder="mis. 200000000">
                    </div>

                    <div>
                    <label class="text-xs text-slate-500">Net Income TTM</label>
                    <input type="number" name="net_income_ttm"
                            class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-400"
                            value="{{ old('net_income_ttm', $snapshot['net_income_ttm'] ?? '') }}"
                            placeholder="mis. 168000000">
                    </div>

                    <div class="md:col-span-3 col-span-2">
                    <label class="text-xs text-slate-500">Total Simpanan</label>
                    <input type="number" name="total_deposits"
                            class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-400"
                            value="{{ old('total_deposits', $snapshot['total_deposits'] ?? '') }}"
                            placeholder="mis. 750000000">
                    </div>
                </div>

                <div class="text-right">
                    <button class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl font-semibold bg-amber-500 text-white hover:bg-amber-600">
                    Save Snapshot
                    </button>
                </div>
                </form>
            </div>
            </div>

            {{-- Kolom Chart (2 kolom) --}}
            <div class="lg:col-span-2 card">
            <h3 class="font-bold text-lg mb-4">Analisis Sisa Hasil Usaha (SHU)</h3>
            <div class="chart-container">
                <canvas id="lineChart"></canvas>
            </div>
            </div>

            {{-- Komposisi Aset, Kewajiban, & Ekuitas --}}
            <div class="lg:col-span-6 card grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                <div class="md:col-span-2">
                <h3 class="font-bold text-lg mb-4">Komposisi Aset, Kewajiban, & Ekuitas</h3>
                @php
                    $assets = $composition['assets'] ?: 1;
                    $pctL = round(($composition['liabilities'] ?? 0) / $assets * 100);
                    $pctE = round(($composition['equity'] ?? 0) / $assets * 100);
                @endphp
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                    <span class="w-28 text-sm font-medium text-right text-slate-600">ASET</span>
                    <div class="w-full bg-slate-200 rounded-full h-6 overflow-hidden">
                        <div class="bg-blue-600 h-6" style="width: 100%"></div>
                    </div>
                    <span class="w-40 text-sm font-bold text-left">
                        Rp {{ number_format($composition['assets'] ?? 0, 0, ',', '.') }}
                    </span>
                    </div>

                    <div class="flex items-center gap-4">
                    <span class="w-28 text-sm font-medium text-right text-slate-600">KEWAJIBAN</span>
                    <div class="w-full bg-slate-200 rounded-full h-6 overflow-hidden">
                        <div class="bg-violet-500 h-6" style="width: {{ $pctL }}%"></div>
                    </div>
                    <span class="w-40 text-sm font-bold text-left">
                        Rp {{ number_format($composition['liabilities'] ?? 0, 0, ',', '.') }}
                    </span>
                    </div>

                    <div class="flex items-center gap-4">
                    <span class="w-28 text-sm font-medium text-right text-slate-600">EKUITAS</span>
                    <div class="w-full bg-slate-200 rounded-full h-6 overflow-hidden">
                        <div class="bg-teal-500 h-6" style="width: {{ $pctE }}%"></div>
                    </div>
                    <span class="w-40 text-sm font-bold text-left">
                        Rp {{ number_format($composition['equity'] ?? 0, 0, ',', '.') }}
                    </span>
                    </div>
                </div>
                </div>

                <div class="md:col-span-1 chart-container" style="height: 250px;">
                <canvas id="doughnutChart"></canvas>
                </div>
            </div>
</div>


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

          {{-- SECTION: Perbandingan Aset vs Beban --}}
<div class="card">
    <h3 class="font-bold text-lg mb-4">Perbandingan Aset vs Beban</h3>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      {{-- Form Inputs --}}
      <div class="lg:col-span-1 space-y-3">
        <div>
          <label class="text-xs text-slate-500">Aset (Rp)</label>
          <input id="inpAset"  type="number" … value="{{ $compare_avb['assets'] ?? ($composition['assets'] ?? 0) }}">
        </div>
        <div>
          <label class="text-xs text-slate-500">Beban (Rp)</label>
          <input id="inpBeban" type="number" … value="{{ $compare_avb['expenses'] ?? array_sum($line['opex'] ?? []) }}">
        </div>

        <div class="pt-2">
          <button id="btnHitung"
                  class="w-full inline-flex items-center justify-center px-4 py-2 rounded-xl font-semibold transition bg-slate-800 text-white hover:bg-slate-900">
            Hitung & Perbarui Grafik
          </button>
        </div>
      </div>

      {{-- Langkah Perhitungan --}}
      <div class="lg:col-span-1">
        <p class="font-semibold mb-2">Langkah perhitungan:</p>
        <ol class="list-decimal pl-5 space-y-1 text-sm">
          <li>
            Aset − Beban =
            <span id="stepSelisih" class="font-mono"></span>
          </li>
          <li>
            Rasio = (<span id="stepPembilang" class="font-mono"></span> ÷
            <span id="stepPenyebut" class="font-mono"></span>) × 100%
          </li>
          <li>
            Rasio = <span id="stepAkhir" class="font-mono"></span>
          </li>
        </ol>

        <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200 text-sm">
          <p class="font-semibold mb-1">✅ Hasil:</p>
          <p id="hasilTeks"></p>
        </div>
      </div>

      {{-- Chart --}}
      <div class="lg:col-span-1">
        <div class="chart-container" style="height: 260px;">
          <canvas id="asetBebanChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <script>
    (function () {
      const fmtIDR = (n) =>
        (Number(n) || 0).toLocaleString('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 });

      const $aset   = document.getElementById('inpAset');
      const $beban  = document.getElementById('inpBeban');
      const $btn    = document.getElementById('btnHitung');

      const $stepSelisih  = document.getElementById('stepSelisih');
      const $stepPembilang= document.getElementById('stepPembilang');
      const $stepPenyebut = document.getElementById('stepPenyebut');
      const $stepAkhir    = document.getElementById('stepAkhir');
      const $hasilTeks    = document.getElementById('hasilTeks');

      // Chart setup (hindari warna merah)
      let abChart;
      function initChart(aset, beban) {
        const ctx = document.getElementById('asetBebanChart').getContext('2d');
        if (abChart) abChart.destroy();
        abChart = new Chart(ctx, {
          type: 'bar',
          data: {
            labels: ['Aset', 'Beban'],
            datasets: [{
              label: 'Jumlah (Rp)',
              data: [aset, beban],
              backgroundColor: [
                'rgba(16, 185, 129, 0.8)',   // 💚 green-500 (Aset)
                'rgba(139, 92, 246, 0.8)'    // 💜 violet-500 (Beban)
              ],
              borderWidth: 0
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: { display: false },
              tooltip: {
                backgroundColor: '#1e293b',
                callbacks: {
                  label: (ctx) => fmtIDR(ctx.parsed.y)
                }
              }
            },
            scales: {
              y: {
                beginAtZero: true,
                grid: { color: '#e5e7eb' },
                ticks: {
                  callback: (v) => (v).toLocaleString('id-ID', {
                    style: 'currency', currency: 'IDR', maximumFractionDigits: 0, notation: 'compact'
                  })
                }
              },
              x: { grid: { display: false } }
            }
          }
        });
      }

      function hitungDanRender() {
        const aset  = Number($aset.value || 0);
        const beban = Number($beban.value || 0);

        const selisih = aset - beban;                       // langkah 1
        const rasio   = beban > 0 ? (selisih / beban) * 100 : 0;  // langkah 2

        // Update langkah
        $stepSelisih.textContent   = `${fmtIDR(aset)} − ${fmtIDR(beban)} = ${fmtIDR(selisih)}`;
        $stepPembilang.textContent = selisih.toLocaleString('id-ID');
        $stepPenyebut.textContent  = beban.toLocaleString('id-ID');
        $stepAkhir.textContent     = `${rasio.toFixed(2)}%`;

        // Hasil narasi
        const kata = rasio >= 0 ? 'lebih tinggi' : 'lebih rendah';
        $hasilTeks.innerHTML =
          `Rasio selisih antara <b>Aset</b> dan <b>Beban</b> adalah <b>${rasio.toFixed(2)}%</b>, ` +
          `artinya <b>Aset ${Math.abs(rasio).toFixed(2)}% ${kata}</b> dibanding Beban.`;

        // Update chart
        initChart(aset, beban);
      }

      // Inisialisasi pertama kali
      hitungDanRender();

      // Re-calc saat klik / ubah input
      $btn.addEventListener('click', (e) => { e.preventDefault(); hitungDanRender(); });
      $aset.addEventListener('input', hitungDanRender);
      $beban.addEventListener('input', hitungDanRender);
    })();
    </script>

  </div>


{{-- Chart.js Initialization from PHP variables --}}
<script>
    // Pass PHP data to JavaScript
    const lineLabels = @json($line['labels'] ?? []);
    const lineRevenue = @json($line['revenue'] ?? []);
    const lineCogs = @json($line['cogs'] ?? []);
    const lineOpex = @json($line['opex'] ?? []);
    const doughnutData = [@json($composition['equity'] ?? 0), @json($composition['liabilities'] ?? 0)];

    document.addEventListener('DOMContentLoaded', () => {
        // Initialize Line Chart
        if (document.getElementById('lineChart')) {
            const ctxLine = document.getElementById('lineChart').getContext('2d');
            new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: lineLabels,
                    datasets: [{
                        label: 'Pendapatan',
                        data: lineRevenue,
                        borderColor: 'rgb(79, 70, 229)',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        tension: 0.4, fill: true, pointRadius: 3
                    }, {
                        label: 'HPP',
                        data: lineCogs,
                        borderColor: 'rgb(249, 115, 22)',
                        backgroundColor: 'rgba(249, 115, 22, 0.1)',
                        tension: 0.4, fill: true, pointRadius: 3
                    }, {
                        label: 'Pengeluaran',
                        data: lineOpex,
                        borderColor: 'rgb(239, 68, 68)',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4, fill: true, pointRadius: 3
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#e5e7eb' },
                            ticks: {
                                callback: (value) => (value).toLocaleString('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0, notation: 'compact' })
                            }
                        },
                        x: { grid: { display: false } }
                    },
                    plugins: {
                        legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 8 } },
                        tooltip: {
                            backgroundColor: '#1e293b', padding: 12, cornerRadius: 8,
                            callbacks: {
                                label: (context) => {
                                    let label = context.dataset.label ? context.dataset.label + ': ' : '';
                                    if (context.parsed.y != null) label += context.parsed.y.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' });
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

            // Data urut: Aset, Ekuitas, Kewajiban
            const doughnutData = [
                {{ $composition['assets'] ?? 0 }},
                {{ $composition['equity'] ?? 0 }},
                {{ $composition['liabilities'] ?? 0 }}
            ];

            new Chart(ctxDoughnut, {
                type: 'doughnut',
                data: {
                    labels: ['Aset', 'Ekuitas', 'Kewajiban'],
                    datasets: [{
                        label: 'Komposisi Neraca',
                        data: doughnutData,
                        backgroundColor: [
                            'rgb(59, 130, 246)',   // Biru untuk Aset
                            'rgb(20, 184, 166)',   // Teal untuk Ekuitas
                            'rgb(139, 92, 246)'    // Ungu untuk Kewajiban
                        ],
                        hoverOffset: 8,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { usePointStyle: true, boxWidth: 8 }
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            callbacks: {
                                label: (context) => {
                                    let label = context.label ? context.label + ': ' : '';
                                    if (context.parsed != null)
                                        label += context.parsed.toLocaleString('id-ID', {
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

    });
</script>

</body>
</html>

