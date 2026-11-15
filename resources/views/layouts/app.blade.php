<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Aplikasi Pinjaman') — {{ config('app.name') }}</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('styles')
</head>
<body>

<div class="dashboard-app-shell">
    <aside class="dashboard-sidebar">
        <div class="sidebar-profile">
            <div class="profile-avatar">{{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}</div>
            <div class="profile-meta">
                <div class="profile-name">{{ auth()->user()?->name ?? 'User' }}</div>
                <div class="profile-email">{{ auth()->user()?->email ?? 'user@example.com' }}</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('karyawan.index') }}" class="sidebar-link {{ request()->routeIs('karyawan.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>Karyawan</span>
            </a>
            <a href="{{ route('pinjaman.index') }}" class="sidebar-link {{ request()->routeIs('pinjaman.*') ? 'active' : '' }}">
                <i class="bi bi-credit-card"></i>
                <span>Pinjaman</span>
            </a>
            <a href="{{ route('kasbon.index') }}" class="sidebar-link {{ request()->routeIs('kasbon.*') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i>
                <span>Kasbon</span>
            </a>
            <a href="{{ route('simpanan.index') }}" class="sidebar-link {{ request()->routeIs('simpanan.*') ? 'active' : '' }}">
                <i class="bi bi-piggy-bank"></i>
                <span>Simpanan</span>
            </a>
            <a href="{{ route('periode.index') }}" class="sidebar-link {{ request()->routeIs('periode.*') ? 'active' : '' }}">
                <i class="bi bi-calendar3"></i>
                <span>Periode</span>
            </a>
            <a href="{{ route('potongan.index') }}" class="sidebar-link {{ request()->routeIs('potongan.*') ? 'active' : '' }}">
                <i class="bi bi-scissors"></i>
                <span>Potongan</span>
            </a>
            <a href="{{ route('slip.index') }}" class="sidebar-link {{ request()->routeIs('slip.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i>
                <span>Rincian Potongan</span>
            </a>
            <a href="{{ route('laporan.index') }}" class="sidebar-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line"></i>
                <span>Laporan</span>
            </a>
            <a href="{{ route('company-settings.edit') }}" class="sidebar-link {{ request()->routeIs('company-settings.*') ? 'active' : '' }}">
                <i class="bi bi-building"></i>
                <span>Identitas Perusahaan</span>
            </a>
            <a href="{{ route('whatsapp.index') }}" class="sidebar-link {{ request()->routeIs('whatsapp.*') ? 'active' : '' }}">
                <i class="bi bi-send-check"></i>
                <span>Kirim Rincian Potongan</span>
            </a>

            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="bi bi-person-gear"></i>
                        <span>Pengguna</span>
                    </a>
                @endif
            @endauth
        </nav>

        <div class="sidebar-footer">
            @auth
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="bi bi-box-arrow-left"></i>
                        <span>Logout</span>
                    </button>
                </form>
            @else
                <a href="{{ route('login.show') }}" class="logout-btn">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span>Login</span>
                </a>
            @endauth
        </div>
    </aside>

    <main class="dashboard-main">
        <header class="topbar">
            <div class="topbar-title">@yield('title', 'Dashboard')</div>
            <div class="topbar-year">{{ date('Y') }}</div>
        </header>

        @unless(request()->routeIs('dashboard'))
            <div class="page-title">@yield('title', 'Dashboard')</div>
        @endunless

        <div class="surface-panel {{ request()->routeIs('dashboard') ? 'dashboard-surface' : '' }}">
            @hasSection('breadcrumb')
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    @yield('breadcrumb')
                </ol>
            </nav>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(isset($errors) && $errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Terdapat kesalahan:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/script.js') }}"></script>
@stack('scripts')
</body>
</html>
