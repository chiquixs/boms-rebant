<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'REBANT BOMS')</title>

    @vite([
        'resources/css/admin.css',
        'resources/css/orders.css',
        'resources/css/order-detail.css',
        'resources/css/products.css',
        'resources/css/product-bom.css',
        'resources/css/categories.css'
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


        {{-- Navigation --}}
        <nav class="sidebar-nav">

    {{-- Dashboard --}}
    <a href="#"
       class="nav-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">

        <svg width="20" height="20" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="1.8">
            <rect x="3" y="3" width="7" height="7" rx="2"/>
            <rect x="14" y="3" width="7" height="7" rx="2"/>
            <rect x="3" y="14" width="7" height="7" rx="2"/>
            <rect x="14" y="14" width="7" height="7" rx="2"/>
        </svg>

        <span>Dashboard</span>
    </a>


    {{-- Order Management --}}
    <a href="/admin/orders"
       class="nav-item {{ request()->is('admin/orders*') ? 'active' : '' }}">

        <svg width="20" height="20" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M6 2h12v20l-3-2-3 2-3-2-3 2V2Z"/>
            <path d="M9 7h6"/>
            <path d="M9 11h6"/>
            <path d="M9 15h3"/>
        </svg>

        <span>Order Management</span>
    </a>


    {{-- Master Produk & SKU --}}
<div class="nav-group {{ request()->is('admin/products*') || request()->is('admin/categories*') ? 'open' : '' }}">

    <div class="nav-parent">

        <a
            href="/admin/products"
            class="nav-item nav-parent-link {{ request()->is('admin/products*') ? 'active' : '' }}"
        >

            <svg width="20" height="20" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M21 8l-9 5-9-5"/>
                <path d="M3 8l9-5 9 5v8l-9 5-9-5V8Z"/>
                <path d="M12 13v8"/>
            </svg>

            <span>Master Produk & SKU</span>

        </a>


        <button
            type="button"
            class="nav-toggle"
            aria-label="Toggle submenu"
        >
            <svg
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                class="nav-chevron"
            >
                <path d="m6 9 6 6 6-6"/>
            </svg>
        </button>

    </div>


    <div class="nav-submenu">

        <a
            href="/admin/categories"
            class="nav-subitem {{ request()->is('admin/categories*') ? 'active' : '' }}"
        >
            <span class="nav-subitem-dot"></span>
            <span>Kategori</span>
        </a>

    </div>

</div>


    {{-- Inventory --}}
    <a href="#" class="nav-item">

        <svg width="20" height="20" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="1.8">
            <rect x="3" y="5" width="18" height="16" rx="2"/>
            <path d="M3 10h18"/>
            <path d="M9 14h6"/>
        </svg>

        <span>Inventory</span>
    </a>


    {{-- Procurement --}}
    <a href="#" class="nav-item">

        <svg width="20" height="20" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="9" cy="20" r="1"/>
            <circle cx="18" cy="20" r="1"/>
            <path d="M3 4h2l2.5 11h10l2-7H7"/>
        </svg>

        <span>Procurement</span>
    </a>


    {{-- Log Aktivitas --}}
    <a href="#" class="nav-item">

        <svg width="20" height="20" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="12" cy="12" r="9"/>
            <path d="M12 7v5l3 2"/>
        </svg>

        <span>Log Aktivitas</span>
    </a>

</nav>


        {{-- Bottom sidebar --}}
        <div class="sidebar-bottom">

            <a href="#" class="nav-item">

                <svg width="20" height="20" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor"
                     stroke-width="1.8">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-2.12 2.12-.06-.06a1.7 1.7 0 0 0-1.88-.34 1.7 1.7 0 0 0-1 1.55V20h-3v-.09a1.7 1.7 0 0 0-1-1.55 1.7 1.7 0 0 0-1.88.34l-.06.06-2.12-2.12.06-.06A1.7 1.7 0 0 0 7 14.7a1.7 1.7 0 0 0-1.55-1H5v-3h.09a1.7 1.7 0 0 0 1.55-1"/>
                </svg>

                <span>Settings</span>
            </a>


            <div class="sidebar-user">

                <div class="user-avatar">
                    A
                </div>

                <div class="user-info">
                    <strong>Admin</strong>
                    <span>Administrator</span>
                </div>

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

                <button class="notification-button">

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

<script>
document.addEventListener('DOMContentLoaded', function () {

    const navGroups =
        document.querySelectorAll('.nav-group');

    navGroups.forEach(function (group) {

        const toggle =
            group.querySelector('.nav-toggle');

        if (!toggle) {
            return;
        }

        toggle.addEventListener('click', function () {

            group.classList.toggle('open');

        });

    });

});
</script>

</body>
</html>