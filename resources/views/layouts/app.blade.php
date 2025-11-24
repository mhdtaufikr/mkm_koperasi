<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'MKMS'))</title>

    {{-- Bootstrap 5 CSS (CDN) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">

    {{-- Font Awesome (opsional) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @if(file_exists(public_path('css/app.css')))
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @endif

    <style>
        body { background: #f7f9fc; }
        .card { border-radius: 12px; }
        .btn-rounded { border-radius: 999px; }
    </style>

    @stack('head')
</head>
<body>
    <div id="app">
        {{-- Navbar sederhana tanpa auth --}}
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                    <img src="{{ asset('logo.png') }}" alt="logo" style="height:28px; margin-right:8px;" onerror="this.style.display='none'">
                    <span class="fw-bold">{{ config('app.name', 'MKMS') }}</span>
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="mainNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link" href="{{ route('invoice.index') }}">Invoice</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Beranda</a></li>
                    </ul>

                    <ul class="navbar-nav ms-auto">
                        {{-- Jika ingin menaruh tombol publik lain, tambah di sini --}}
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="alert('Tidak ada login'); return false;">User</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="mb-0">@yield('page_title', 'Dashboard')</h4>
                        @hasSection('page_subtitle')
                            <small class="text-muted">@yield('page_subtitle')</small>
                        @endif
                    </div>

                    <div>
                        @yield('header_actions')
                    </div>
                </div>

                {{-- Flash messages --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Konten halaman --}}
                @yield('content')
            </div>
        </main>

        <footer class="bg-white border-top py-3 mt-auto">
            <div class="container text-center text-muted small">
                &copy; {{ date('Y') }} {{ config('app.name', 'MKMS') }}.
            </div>
        </footer>
    </div>

    {{-- Bootstrap JS bundle --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    {{-- jQuery opsional --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>

    @if(file_exists(public_path('js/app.js')))
        <script src="{{ asset('js/app.js') }}"></script>
    @endif

    @stack('scripts')
    @yield('scripts')
</body>
</html>
