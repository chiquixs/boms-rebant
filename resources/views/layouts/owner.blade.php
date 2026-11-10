
<!DOCTYPE html>
<html lang="en">
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

        <!-- SIDEBAR -->
        <aside class="sidebar owner-sidebar">

            <div class="sidebar-brand">
                <div class="sidebar-brand-name">
                    <strong>REBANT BOMS</strong>
                    <span>Business Owner</span>
                </div>
            </div>

            <nav class="sidebar-nav">

                <a href="{{ route('owner.dashboard') }}"
                   class="nav-item {{ request()->is('owner/dashboard') ? 'active' : '' }}">
                    <span>Dashboard</span>
                </a>

                <a href="#" class="nav-item">
                    <span>Laporan Penjualan</span>
                </a>

                <a href="#" class="nav-item">
                    <span>Master Data</span>
                </a>

                <a href="#" class="nav-item">
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

                </div>
            </div>

        </aside>


        <!-- MAIN CONTENT -->
        <main class="admin-main owner-main">

            <!-- TOPBAR -->
            <header class="topbar owner-topbar">

                <div class="owner-search">
                    <span>⌕</span>
                    <input type="text" placeholder="Search anything...">
                </div>

                <div class="topbar-actions">

                    <button type="button" class="notification-button">
                        ♧
                        <span class="notification-dot"></span>
                    </button>

                    <div class="owner-topbar-avatar">
                        O
                    </div>

                </div>

            </header>


            <!-- PAGE CONTENT -->
            <section class="admin-content owner-content">
                @yield('content')
            </section>

        </main>

    </div>

    @stack('scripts')

</body>
</html>
