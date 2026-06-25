@extends('layouts.playground')

@section('title', 'Upload Raw Data - Playground')

@section('content')
<section class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-5 flex items-start justify-between gap-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-blue-700">Input Data</p>
                <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-950">Upload raw data rasio</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">
                    Paste data dari Excel, tabel Markdown, atau TSV. Sistem akan membaca tabel rasio dan tabel nominal 2025 vs 2024.
                </p>
            </div>
            <span class="hidden rounded-lg bg-blue-50 p-3 text-blue-800 sm:inline-flex">
                <i data-lucide="file-spreadsheet" class="h-6 w-6"></i>
            </span>
        </div>

        <form method="POST" action="{{ route('playground.store') }}" class="space-y-5">
            @csrf
            <textarea name="raw_data" rows="19" class="w-full rounded-lg border border-slate-300 bg-slate-50 p-4 font-mono text-sm leading-6 text-slate-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100">{{ $rawData }}</textarea>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <div class="mb-3 flex items-center gap-2 font-bold text-slate-900">
                        <i data-lucide="store" class="h-5 w-5 text-blue-700"></i>
                        Partisipasi Pertokoan
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="block">
                            <span class="text-xs font-semibold uppercase text-slate-500">Aktif</span>
                            <input type="number" name="pertokoan_active" min="0" value="{{ request()->input('pertokoan_active', $participation['pertokoan']['active'] ?? 0) }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-bold focus:border-blue-500 focus:ring-blue-100">
                        </label>
                        <label class="block">
                            <span class="text-xs font-semibold uppercase text-slate-500">Total</span>
                            <input type="number" name="pertokoan_total" min="0" value="{{ request()->input('pertokoan_total', $participation['pertokoan']['total'] ?? 0) }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-bold focus:border-blue-500 focus:ring-blue-100">
                        </label>
                    </div>
                </div>

                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <div class="mb-3 flex items-center gap-2 font-bold text-slate-900">
                        <i data-lucide="hand-coins" class="h-5 w-5 text-emerald-700"></i>
                        Partisipasi Pinjaman
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="block">
                            <span class="text-xs font-semibold uppercase text-slate-500">Aktif</span>
                            <input type="number" name="pinjaman_active" min="0" value="{{ request()->input('pinjaman_active', $participation['pinjaman']['active'] ?? 0) }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-bold focus:border-blue-500 focus:ring-blue-100">
                        </label>
                        <label class="block">
                            <span class="text-xs font-semibold uppercase text-slate-500">Total</span>
                            <input type="number" name="pinjaman_total" min="0" value="{{ request()->input('pinjaman_total', $participation['pinjaman']['total'] ?? 0) }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-bold focus:border-blue-500 focus:ring-blue-100">
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-xs text-slate-500">Partisipasi anggota dibatasi menjadi 2 kategori: Pertokoan dan Pinjaman.</p>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-900 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-blue-800">
                    <i data-lucide="wand-sparkles" class="h-4 w-4"></i>
                    Generate Dashboard
                </button>
            </div>
        </form>
    </div>

    <aside class="space-y-4">
        <div class="rounded-lg bg-blue-950 p-6 text-white shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-wide text-blue-200">Format yang dibaca</p>
            <h2 class="mt-2 text-2xl font-black">Rasio dan nominal 2 tahun</h2>
            <div class="mt-5 space-y-3 text-sm text-blue-50">
                <div class="flex gap-3">
                    <i data-lucide="check-circle-2" class="mt-0.5 h-5 w-5 shrink-0 text-emerald-300"></i>
                    <span>Tabel rasio: Kategori, Rasio, 2025, 2024, Selisih.</span>
                </div>
                <div class="flex gap-3">
                    <i data-lucide="check-circle-2" class="mt-0.5 h-5 w-5 shrink-0 text-emerald-300"></i>
                    <span>Tabel nominal: Keterangan, 2025, 2024.</span>
                </div>
                <div class="flex gap-3">
                    <i data-lucide="check-circle-2" class="mt-0.5 h-5 w-5 shrink-0 text-emerald-300"></i>
                    <span>Angka bisa memakai format Indonesia atau persen seperti 316,18%.</span>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-black">Alur halaman</h3>
            <div class="mt-4 space-y-4">
                <div class="flex gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-900 text-sm font-black text-white">1</span>
                    <div>
                        <p class="font-bold">Upload Data</p>
                        <p class="text-sm text-slate-600">Paste data mentah dan isi partisipasi anggota.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-900 text-sm font-black text-white">2</span>
                    <div>
                        <p class="font-bold">Dashboard</p>
                        <p class="text-sm text-slate-600">Grafik, ringkasan, insight, dan detail bar yang bisa diklik.</p>
                    </div>
                </div>
            </div>
        </div>
    </aside>
</section>
@endsection
