<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'REBANT BOMS')</title>

    @vite([
        'resources/css/admin.css',
        'resources/css/produksi.css',
        'resources/css/production-queue.css',
        'resources/css/production-execution-modal.css',
        'resources/css/stock-planning.css',
        'resources/css/production-history.css',
    ])



    @stack('styles')
</head>

<body>

<div class="admin-layout">

    {{-- =========================
        SIDEBAR
    ========================== --}}
    <aside class="sidebar">

        <div class="sidebar-brand">
            <img
                src="{{ asset('images/rebant-logo.png') }}"
                alt="REBANT Logo"
                class="sidebar-logo"
            >

            <div class="sidebar-brand-name">
                <strong>REBANT</strong>
                <span>BOMS</span>
            </div>
        </div>


        <nav class="sidebar-nav">

            {{-- Dashboard --}}
            <a href="{{ route('produksi.dashboard') }}"
               class="nav-item {{ request()->routeIs('produksi.dashboard') ? 'active' : '' }}">

                <svg width="20" height="20" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="3" width="7" height="7" rx="2"/>
                    <rect x="14" y="3" width="7" height="7" rx="2"/>
                    <rect x="3" y="14" width="7" height="7" rx="2"/>
                    <rect x="14" y="14" width="7" height="7" rx="2"/>
                </svg>

                <span>Dashboard</span>
            </a>


            {{-- Production Queue --}}
            <a href="{{ route('produksi.queue.index') }}"
               class="nav-item {{ request()->routeIs('produksi.queue.*') ? 'active' : '' }}">

                <svg width="20" height="20" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M8 6h13"/>
                    <path d="M8 12h13"/>
                    <path d="M8 18h13"/>
                    <path d="M3 6h.01"/>
                    <path d="M3 12h.01"/>
                    <path d="M3 18h.01"/>
                </svg>

                <span>Production Queue</span>
            </a>


            {{-- Stock & Production Planning --}}
            <a href="{{ route('produksi.planning.index') }}"
               class="nav-item {{ request()->routeIs('produksi.planning.*') ? 'active' : '' }}">

                <svg width="20" height="20" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M3 3v18h18"/>
                    <path d="m7 15 4-4 3 3 5-6"/>
                </svg>

                <span>Stock & Production Planning</span>
            </a>


            {{-- Riwayat Produksi --}}
            <a href="{{ route('produksi.history.index') }}"
               class="nav-item {{ request()->routeIs('produksi.history.*') ? 'active' : '' }}">

                <svg width="20" height="20" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 7v5l3 2"/>
                </svg>

                <span>Riwayat Produksi</span>
            </a>

        </nav>


        {{-- Bottom sidebar --}}
        <div class="sidebar-bottom">

            <div class="sidebar-user">

                <div class="user-avatar">
                    {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                </div>

                <div class="user-info">
                    <strong>{{ auth()->user()->name }}</strong>
                    <span>Production</span>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="sidebar-logout-form">
                    @csrf
                    <button type="submit" class="sidebar-logout" aria-label="Logout" title="Logout">
                        <svg width="18" height="18" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <path d="m16 17 5-5-5-5"/>
                            <path d="M21 12H9"/>
                        </svg>
                    </button>
                </form>

            </div>

        </div>

    </aside>


    {{-- =========================
        MAIN CONTENT
    ========================== --}}
    <main class="admin-main">

        <header class="topbar">

            <div class="topbar-title">
                <span>Business Operation Management System</span>
            </div>


            <div class="topbar-actions">

                <button class="notification-button" type="button" aria-label="Notifikasi">

                    <svg width="20" height="20"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="1.8">

                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                        <path d="M13.7 21a2 2 0 0 1-3.4 0"/>

                    </svg>

                    <span class="notification-dot"></span>

                </button>
            </div>

        </header>


        <section class="admin-content">

            @yield('content')

        </section>

    </main>

</div>


@stack('scripts')


</body>
</html>