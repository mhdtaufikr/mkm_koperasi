<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Playground Dashboard Rasio')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: Inter, sans-serif; }
        [x-cloak] { display: none !important; }
        .chart-box { position: relative; height: 320px; width: 100%; }
        .mini-chart { position: relative; height: 150px; width: 100%; }
    </style>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <div class="min-h-screen">
        @hasSection('fullscreen')
        @else
        <nav class="border-b border-slate-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                <a href="{{ route('playground.upload') }}" class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-blue-900 text-white">
                        <i data-lucide="layout-dashboard" class="h-5 w-5"></i>
                    </span>
                    <span>
                        <span class="block text-sm font-semibold uppercase tracking-wide text-blue-700">Playground</span>
                        <span class="block text-xl font-extrabold tracking-tight">Dashboard Rasio Keuangan</span>
                    </span>
                </a>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('playground.upload') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:border-blue-300 hover:text-blue-800">
                        <i data-lucide="upload-cloud" class="h-4 w-4"></i>
                        Upload Data
                    </a>
                    <a href="{{ route('playground.dashboard') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-800">
                        <i data-lucide="bar-chart-3" class="h-4 w-4"></i>
                        Dashboard
                    </a>
                </div>
            </div>
        </nav>
        @endif

        <main class="@hasSection('fullscreen') w-full p-2 @else mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 @endif">
            @if(request()->query('saved'))
                <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                    Data raw berhasil diproses.
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    <div class="font-bold">Data belum bisa diproses:</div>
                    <ul class="mt-1 list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
