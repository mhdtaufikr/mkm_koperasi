@extends('layouts.playground')

@section('title', 'Import Excel Dashboard')

@section('content')
<section class="relative overflow-hidden rounded-xl bg-slate-950 p-6 text-white shadow-xl">
    <div class="absolute inset-0 opacity-40" style="background: radial-gradient(circle at 15% 20%, #0ea5e9 0, transparent 28%), radial-gradient(circle at 90% 10%, #22c55e 0, transparent 24%), linear-gradient(135deg, #020617 0%, #0f172a 45%, #082f49 100%);"></div>
    <div class="relative grid gap-8 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
        <div>
            <p class="text-sm font-black uppercase tracking-[0.28em] text-cyan-200">Financial Intelligence Import</p>
            <h1 class="mt-3 text-4xl font-black tracking-tight sm:text-5xl">Import Excel Dashboard</h1>
            <p class="mt-4 max-w-xl text-sm leading-7 text-slate-200">
                Download template, isi rasio keuangan dan aktivitas anggota, lalu upload kembali. Dashboard akan langsung mengikuti data terbaru.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('playground.template') }}" class="inline-flex items-center gap-2 rounded-lg bg-cyan-400 px-5 py-3 text-sm font-black text-slate-950 shadow-lg shadow-cyan-950/30 hover:bg-cyan-300">
                    <i data-lucide="download" class="h-4 w-4"></i>
                    Download Template Excel
                </a>
                <a href="{{ route('playground.dashboard') }}" class="inline-flex items-center gap-2 rounded-lg border border-white/20 bg-white/10 px-5 py-3 text-sm font-black text-white backdrop-blur hover:bg-white/15">
                    <i data-lucide="layout-dashboard" class="h-4 w-4"></i>
                    Lihat Dashboard
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('playground.store') }}" enctype="multipart/form-data" class="rounded-xl border border-white/15 bg-white/10 p-5 shadow-2xl backdrop-blur">
            @csrf
            <div class="rounded-lg border border-dashed border-cyan-300/60 bg-slate-950/50 p-6 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-cyan-400 text-slate-950 shadow-lg shadow-cyan-950/40">
                    <i data-lucide="file-up" class="h-8 w-8"></i>
                </div>
                <h2 class="mt-4 text-2xl font-black">Upload file Excel</h2>
                <p class="mt-2 text-sm text-slate-300">Support template `.xls`, `.csv`, `.tsv`, dan `.xlsx` sederhana.</p>
                <input type="file" name="dashboard_file" accept=".xls,.xlsx,.csv,.tsv,.txt" required class="mt-5 block w-full cursor-pointer rounded-lg border border-white/10 bg-white/10 text-sm text-slate-200 file:mr-4 file:border-0 file:bg-cyan-400 file:px-4 file:py-3 file:text-sm file:font-black file:text-slate-950 hover:file:bg-cyan-300">
                <button type="submit" class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-emerald-400 px-5 py-3 text-sm font-black text-slate-950 shadow-lg shadow-emerald-950/30 hover:bg-emerald-300">
                    <i data-lucide="upload-cloud" class="h-4 w-4"></i>
                    Import & Generate Dashboard
                </button>
            </div>
        </form>
    </div>
</section>

<section class="mt-6 grid gap-5 lg:grid-cols-3">
    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-950 text-white">1</span>
            <div>
                <h3 class="font-black">Download Template</h3>
                <p class="text-sm text-slate-600">Template sudah berisi contoh data rasio, nominal, dan partisipasi anggota.</p>
            </div>
        </div>
    </div>
    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-950 text-white">2</span>
            <div>
                <h3 class="font-black">Isi Aktivitas Anggota</h3>
                <p class="text-sm text-slate-600">Kategori aktivitas hanya `Pertokoan` dan `Pinjaman`.</p>
            </div>
        </div>
    </div>
    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-950 text-white">3</span>
            <div>
                <h3 class="font-black">Upload Balik</h3>
                <p class="text-sm text-slate-600">Data akan disimpan ke database dan dashboard langsung berubah.</p>
            </div>
        </div>
    </div>
</section>

<details class="mt-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
    <summary class="cursor-pointer text-sm font-black text-slate-800">Input manual cadangan</summary>
    <form method="POST" action="{{ route('playground.store') }}" class="mt-4 space-y-4">
        @csrf
        <textarea name="raw_data" rows="12" class="w-full rounded-lg border border-slate-300 bg-slate-50 p-4 font-mono text-sm leading-6 text-slate-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100">{{ $rawData }}</textarea>
        <div class="grid gap-4 md:grid-cols-2">
            @foreach(['pertokoan' => 'Pertokoan', 'pinjaman' => 'Pinjaman'] as $key => $label)
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <div class="mb-3 flex items-center gap-2 font-bold text-slate-900">
                        <i data-lucide="{{ $key === 'pertokoan' ? 'store' : 'hand-coins' }}" class="h-5 w-5 text-blue-700"></i>
                        Partisipasi {{ $label }}
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="block">
                            <span class="text-xs font-semibold uppercase text-slate-500">Aktif</span>
                            <input type="number" name="{{ $key }}_active" min="0" value="{{ request()->input($key . '_active', $participation[$key]['active'] ?? 0) }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-bold">
                        </label>
                        <label class="block">
                            <span class="text-xs font-semibold uppercase text-slate-500">Total</span>
                            <input type="number" name="{{ $key }}_total" min="0" value="{{ request()->input($key . '_total', $participation[$key]['total'] ?? 0) }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-bold">
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-900 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-blue-800">
            <i data-lucide="wand-sparkles" class="h-4 w-4"></i>
            Generate Dashboard
        </button>
    </form>
</details>
@endsection
