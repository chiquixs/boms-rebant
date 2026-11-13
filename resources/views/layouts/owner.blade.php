<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'REBANT BOMS - Owner')</title>

    @vite([
        'resources/css/admin.css',
        'resources/css/owner.css',
        'resources/css/owner-dashboard.css'
    ])

    @stack('styles')
</head>

<body>

<div class="admin-layout owner-layout">

    {{-- SIDEBAR --}}
    <aside class="sidebar owner-sidebar">

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

            <a href="{{ route('owner.dashboard') }}"
               class="nav-item {{ request()->is('owner/dashboard') ? 'active' : '' }}">
                <svg width="19" height="19" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="1.8"
                     stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" rx="1"/>
                    <rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="3" y="14" width="7" height="7" rx="1"/>
                    <rect x="14" y="14" width="7" height="7" rx="1"/>
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('owner.sales-report') }}"
                class="nav-item {{ request()->is('owner/sales-report') ? 'active' : '' }}">
                <svg width="19" height="19" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="1.8"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 3v18h18"/>
                    <path d="m7 14 4-4 4 3 6-7"/>
                </svg>
                <span>Laporan Penjualan</span>
            </a>

            <div class="nav-group owner-nav-group">

                <div class="nav-parent">
                    <a href="#"
                       class="nav-item nav-parent-link">
                        <svg width="19" height="19" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="1.8"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="m12 3 9 5-9 5-9-5 9-5Z"/>
                            <path d="m3 8 9 5 9-5"/>
                            <path d="M3 8v9l9 5 9-5V8"/>
                        </svg>
                        <span>Master Data</span>
                    </a>

                    <button type="button" class="nav-toggle" aria-label="Toggle Master Data">
                        <svg width="14" height="14" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </button>
                </div>

                <div class="nav-submenu">
                    <a href="#" class="nav-subitem">Produk</a>
                    <a href="#" class="nav-subitem">Kategori</a>
                    <a href="#" class="nav-subitem">Bahan Baku</a>
                    <a href="#" class="nav-subitem">Supplier</a>
                </div>

            </div>

            <a href="#"
               class="nav-item">
                <svg width="19" height="19" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="1.8"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 12a9 9 0 1 0 3-6.7"/>
                    <path d="M3 3v6h6"/>
                    <path d="M12 7v5l3 2"/>
                </svg>
                <span>Log Aktivitas</span>
            </a>

        </nav>

        <div class="sidebar-bottom">
            <div class="sidebar-user">
                <div class="user-avatar">O</div>

                <div class="user-info">
                    <strong>Business Owner</strong>
                    <span>Owner</span>
                </div>

                <a href="#" class="owner-logout" title="Logout">
                    <svg width="18" height="18" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="1.8"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 17l5-5-5-5"/>
                        <path d="M15 12H3"/>
                        <path d="M12 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6"/>
                    </svg>
                </a>
            </div>
        </div>

    </aside>


    {{-- MAIN CONTENT --}}
    <main class="admin-main owner-main">

        <header class="topbar owner-topbar">

            <div class="topbar-title">
                <span>Business Operation Management System</span>
            </div>

            <div class="owner-topbar-actions">

                <div class="owner-search">
                    <svg width="17" height="17" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="1.8"
                         stroke-linecap="round">
                        <circle cx="11" cy="11" r="7"/>
                        <path d="m20 20-4-4"/>
                    </svg>

                    <input type="text" placeholder="Search anything...">
                </div>

                <button type="button" class="notification-button"
                        aria-label="Notifications">
                    <svg width="20" height="20" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="1.8"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                        <path d="M13.7 21a2 2 0 0 1-3.4 0"/>
                    </svg>
                    <span class="notification-dot"></span>
                </button>

                <div class="owner-topbar-avatar">O</div>

            </div>

        </header>

        <section class="admin-content owner-content">
            @yield('content')
        </section>

    </main>

</div>

@stack('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.owner-nav-group .nav-toggle')
        .forEach(function (toggle) {
            toggle.addEventListener('click', function () {
                toggle.closest('.nav-group').classList.toggle('open');
            });
        });
});
</script>

</body>
</html>