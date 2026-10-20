@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

<div class="dashboard-page">

    {{-- ========================================
        PAGE HEADER
    ========================================= --}}
    <div class="dashboard-header">
        <div>
            <h1>Dashboard</h1>
            <p>
                Ringkasan aktivitas dan kondisi operasional REBANT BOMS.
            </p>
        </div>

        <div class="dashboard-date">
            <svg
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <rect x="3" y="5" width="18" height="16" rx="2"/>
                <path d="M16 3v4"/>
                <path d="M8 3v4"/>
                <path d="M3 10h18"/>
            </svg>

            <span>18 Oktober 2026</span>
        </div>
    </div>


    {{-- ========================================
        SUMMARY CARDS
    ========================================= --}}
    <div class="dashboard-summary">

        {{-- ORDER BARU --}}
        <a href="/admin/orders" class="dashboard-summary-card">
            <div class="dashboard-summary-icon orders">
                <svg
                    width="19"
                    height="19"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M6 2h12l1 5H5l1-5Z"/>
                    <path d="M5 7v15h14V7"/>
                    <path d="M9 11h6"/>
                </svg>
            </div>

            <div class="dashboard-summary-content">
                <span class="dashboard-summary-label">
                    Order Baru
                </span>

                <strong class="dashboard-summary-value">
                    3
                </strong>

                <span class="dashboard-summary-caption">
                    pesanan perlu diproses
                </span>
            </div>

            <svg
                class="dashboard-summary-arrow"
                width="15"
                height="15"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >
                <path d="m9 18 6-6-6-6"/>
            </svg>
        </a>


        {{-- MENUNGGU PRODUKSI --}}
        <a href="/admin/orders" class="dashboard-summary-card">
            <div class="dashboard-summary-icon production">
                <svg
                    width="19"
                    height="19"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M4 21V10l5 3V9l5 3V5l6 4v12Z"/>
                    <path d="M8 21v-4"/>
                    <path d="M12 21v-4"/>
                    <path d="M16 21v-4"/>
                </svg>
            </div>

            <div class="dashboard-summary-content">
                <span class="dashboard-summary-label">
                    Menunggu Produksi
                </span>

                <strong class="dashboard-summary-value">
                    2
                </strong>

                <span class="dashboard-summary-caption">
                    order menunggu produksi
                </span>
            </div>

            <svg
                class="dashboard-summary-arrow"
                width="15"
                height="15"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >
                <path d="m9 18 6-6-6-6"/>
            </svg>
        </a>


        {{-- STOK PRODUK RENDAH --}}
        <a href="/admin/inventory" class="dashboard-summary-card">
            <div class="dashboard-summary-icon stock">
                <svg
                    width="19"
                    height="19"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
                    <path d="m3.3 7 8.7 5 8.7-5"/>
                    <path d="M12 22V12"/>
                </svg>
            </div>

            <div class="dashboard-summary-content">
                <span class="dashboard-summary-label">
                    Stok Rendah
                </span>

                <strong class="dashboard-summary-value">
                    2
                </strong>

                <span class="dashboard-summary-caption">
                    SKU perlu diperhatikan
                </span>
            </div>

            <svg
                class="dashboard-summary-arrow"
                width="15"
                height="15"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >
                <path d="m9 18 6-6-6-6"/>
            </svg>
        </a>


        {{-- PERLU RESTOCK --}}
        <a href="/admin/procurement" class="dashboard-summary-card">
            <div class="dashboard-summary-icon procurement">
                <svg
                    width="19"
                    height="19"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <circle cx="9" cy="20" r="1"/>
                    <circle cx="19" cy="20" r="1"/>
                    <path d="M3 4h2l2.4 10.4a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.6L21 7H6"/>
                </svg>
            </div>

            <div class="dashboard-summary-content">
                <span class="dashboard-summary-label">
                    Perlu Restock
                </span>

                <strong class="dashboard-summary-value">
                    1
                </strong>

                <span class="dashboard-summary-caption">
                    bahan baku di bawah safety stock
                </span>
            </div>

            <svg
                class="dashboard-summary-arrow"
                width="15"
                height="15"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >
                <path d="m9 18 6-6-6-6"/>
            </svg>
        </a>

    </div>


    {{-- ========================================
        MAIN DASHBOARD GRID
    ========================================= --}}
    <div class="dashboard-grid">

        {{-- ========================================
            RECENT ORDERS
        ========================================= --}}
        <div class="dashboard-card dashboard-orders-card">

            <div class="dashboard-card-header">
                <div>
                    <h2>Pesanan Terbaru</h2>
                    <p>Pesanan yang baru masuk dan sedang diproses.</p>
                </div>

                <a href="/admin/orders" class="dashboard-view-all">
                    Lihat Semua

                    <svg
                        width="13"
                        height="13"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                </a>
            </div>


            <div class="dashboard-table-wrapper">
                <table class="dashboard-orders-table">

                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Platform</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <td>
                                <a href="/admin/orders/ORD-001" class="dashboard-order-id">
                                    ORD-001
                                </a>
                            </td>

                            <td>
                                <span class="dashboard-platform tiktok">
                                    TikTok Shop
                                </span>
                            </td>

                            <td>
                                Rp120.000
                            </td>

                            <td>
                                <span class="dashboard-status ready">
                                    Ready to Pack
                                </span>
                            </td>
                        </tr>


                        <tr>
                            <td>
                                <a href="/admin/orders/ORD-002" class="dashboard-order-id">
                                    ORD-002
                                </a>
                            </td>

                            <td>
                                <span class="dashboard-platform shopee">
                                    Shopee
                                </span>
                            </td>

                            <td>
                                Rp240.000
                            </td>

                            <td>
                                <span class="dashboard-status new">
                                    New
                                </span>
                            </td>
                        </tr>


                        <tr>
                            <td>
                                <a href="/admin/orders/ORD-003" class="dashboard-order-id">
                                    ORD-003
                                </a>
                            </td>

                            <td>
                                <span class="dashboard-platform tiktok">
                                    TikTok Shop
                                </span>
                            </td>

                            <td>
                                Rp180.000
                            </td>

                            <td>
                                <span class="dashboard-status waiting">
                                    Waiting Production
                                </span>
                            </td>
                        </tr>


                        <tr>
                            <td>
                                <a href="/admin/orders/ORD-004" class="dashboard-order-id">
                                    ORD-004
                                </a>
                            </td>

                            <td>
                                <span class="dashboard-platform shopee">
                                    Shopee
                                </span>
                            </td>

                            <td>
                                Rp96.000
                            </td>

                            <td>
                                <span class="dashboard-status new">
                                    New
                                </span>
                            </td>
                        </tr>

                    </tbody>

                </table>
            </div>

        </div>


        {{-- ========================================
            STOCK ALERT
        ========================================= --}}
        <div class="dashboard-card dashboard-stock-card">

            <div class="dashboard-card-header">
                <div>
                    <h2>Peringatan Stok</h2>
                    <p>Item yang membutuhkan perhatian.</p>
                </div>

                <a href="/admin/inventory" class="dashboard-view-all">
                    Inventory

                    <svg
                        width="13"
                        height="13"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                </a>
            </div>


            <div class="dashboard-stock-list">

                {{-- D6A-5 --}}
                <div class="dashboard-stock-item">

                    <div class="dashboard-stock-info">

                        <div class="dashboard-stock-icon product">
                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
                                <path d="m3.3 7 8.7 5 8.7-5"/>
                            </svg>
                        </div>

                        <div>
                            <strong>Jagalah Kebersihan</strong>
                            <span>D6A-5 · Produk Jadi</span>
                        </div>

                    </div>

                    <div class="dashboard-stock-value">
                        <strong>20 pcs</strong>
                        <span>Min. 30 pcs</span>
                    </div>

                    <span class="dashboard-stock-status low">
                        Rendah
                    </span>

                </div>


                {{-- D6A-3 --}}
                <div class="dashboard-stock-item">

                    <div class="dashboard-stock-info">

                        <div class="dashboard-stock-icon product">
                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
                                <path d="m3.3 7 8.7 5 8.7-5"/>
                            </svg>
                        </div>

                        <div>
                            <strong>Area Merokok</strong>
                            <span>D6A-3 · Produk Jadi</span>
                        </div>

                    </div>

                    <div class="dashboard-stock-value">
                        <strong>30 pcs</strong>
                        <span>Min. 30 pcs</span>
                    </div>

                    <span class="dashboard-stock-status warning">
                        Perhatian
                    </span>

                </div>


                {{-- CAT HITAM --}}
                <div class="dashboard-stock-item">

                    <div class="dashboard-stock-info">

                        <div class="dashboard-stock-icon material">
                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M12 2 5 6v12l7 4 7-4V6Z"/>
                                <path d="m5 6 7 4 7-4"/>
                                <path d="M12 10v12"/>
                            </svg>
                        </div>

                        <div>
                            <strong>Cat Hitam</strong>
                            <span>Bahan Mentah · NON-SHEET</span>
                        </div>

                    </div>

                    <div class="dashboard-stock-value">
                        <strong>2 lt</strong>
                        <span>Safety 3 lt</span>
                    </div>

                    <span class="dashboard-stock-status low">
                        Rendah
                    </span>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================
        SECOND GRID
    ========================================= --}}
    <div class="dashboard-grid dashboard-grid-bottom">

        {{-- ========================================
            PROCUREMENT
        ========================================= --}}
        <div class="dashboard-card">

            <div class="dashboard-card-header">
                <div>
                    <h2>Procurement</h2>
                    <p>Rekomendasi restock dan purchase order aktif.</p>
                </div>

                <a href="/admin/procurement" class="dashboard-view-all">
                    Lihat Semua

                    <svg
                        width="13"
                        height="13"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                </a>
            </div>


            <div class="dashboard-procurement-list">

                {{-- RESTOCK --}}
                <div class="dashboard-procurement-item">

                    <div class="dashboard-procurement-left">
                        <div class="dashboard-procurement-icon restock">
                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M12 9v4"/>
                                <path d="M12 17h.01"/>
                                <path d="M10.3 3.7 2.2 18a2 2 0 0 0 1.7 3h16.2a2 2 0 0 0 1.7-3L13.7 3.7a2 2 0 0 0-3.4 0Z"/>
                            </svg>
                        </div>

                        <div>
                            <strong>Cat Hitam perlu restock</strong>
                            <span>Stok 2 lt · Safety 3 lt · Target 15 lt</span>
                        </div>
                    </div>

                    <div class="dashboard-procurement-right">
                        <span class="dashboard-restock-qty">
                            +13 lt
                        </span>

                        <span class="dashboard-procurement-status recommendation">
                            Rekomendasi
                        </span>
                    </div>

                </div>


                {{-- PO ORDERED --}}
                <div class="dashboard-procurement-item">

                    <div class="dashboard-procurement-left">
                        <div class="dashboard-procurement-icon po">
                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M6 2h9l5 5v15H6Z"/>
                                <path d="M14 2v6h6"/>
                                <path d="M9 13h6"/>
                                <path d="M9 17h6"/>
                            </svg>
                        </div>

                        <div>
                            <strong>PO-0012</strong>
                            <span>PT Kayu Jaya · Rp850.000</span>
                        </div>
                    </div>

                    <span class="dashboard-procurement-status ordered">
                        Ordered
                    </span>

                </div>


                {{-- PO RECEIVED --}}
                <div class="dashboard-procurement-item">

                    <div class="dashboard-procurement-left">
                        <div class="dashboard-procurement-icon received">
                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                        </div>

                        <div>
                            <strong>PO-0011</strong>
                            <span>CV MDF Malang · Rp1.250.000</span>
                        </div>
                    </div>

                    <span class="dashboard-procurement-status received">
                        Received
                    </span>

                </div>

            </div>

        </div>


        {{-- ========================================
            RECENT ACTIVITY
        ========================================= --}}
        <div class="dashboard-card">

            <div class="dashboard-card-header">
                <div>
                    <h2>Aktivitas Terbaru</h2>
                    <p>Aktivitas terakhir yang tercatat di sistem.</p>
                </div>

                <a href="/admin/activity-logs" class="dashboard-view-all">
                    Lihat Semua

                    <svg
                        width="13"
                        height="13"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                </a>
            </div>


            <div class="dashboard-activity-list">

                {{-- ACTIVITY 1 --}}
                <div class="dashboard-activity-item">

                    <div class="dashboard-activity-dot procurement"></div>

                    <div class="dashboard-activity-content">
                        <div class="dashboard-activity-top">
                            <strong>Mengirim Purchase Order</strong>
                            <span>10:32</span>
                        </div>

                        <p>
                            Admin mengirim PO-0012 ke PT Kayu Jaya.
                        </p>
                    </div>

                </div>


                {{-- ACTIVITY 2 --}}
                <div class="dashboard-activity-item">

                    <div class="dashboard-activity-dot product"></div>

                    <div class="dashboard-activity-content">
                        <div class="dashboard-activity-top">
                            <strong>Mengubah Data Produk</strong>
                            <span>10:05</span>
                        </div>

                        <p>
                            Admin memperbarui produk D6A.
                        </p>
                    </div>

                </div>


                {{-- ACTIVITY 3 --}}
                <div class="dashboard-activity-item">

                    <div class="dashboard-activity-dot production"></div>

                    <div class="dashboard-activity-content">
                        <div class="dashboard-activity-top">
                            <strong>Konfirmasi Produksi Selesai</strong>
                            <span>09:20</span>
                        </div>

                        <p>
                            Produksi menyelesaikan pesanan ORD-001.
                        </p>
                    </div>

                </div>


                {{-- ACTIVITY 4 --}}
                <div class="dashboard-activity-item">

                    <div class="dashboard-activity-dot inventory"></div>

                    <div class="dashboard-activity-content">
                        <div class="dashboard-activity-top">
                            <strong>Penyesuaian Stok</strong>
                            <span>08:45</span>
                        </div>

                        <p>
                            Admin melakukan penyesuaian stok D6A-1.
                        </p>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection