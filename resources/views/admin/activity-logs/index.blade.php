@extends('layouts.admin')

@section('title', 'Log Aktivitas')

@section('content')

<div class="activity-logs-page">

    {{-- ========================================
        PAGE HEADER
    ========================================= --}}
    <div class="activity-logs-header">

        <div>
            <h1>Log Aktivitas</h1>

            <p>
                Pantau riwayat aktivitas pengguna dan perubahan yang terjadi di dalam sistem.
            </p>
        </div>

    </div>


    {{-- ========================================
        SUMMARY
    ========================================= --}}
    <div class="activity-summary">

        {{-- TOTAL AKTIVITAS --}}
        <div class="activity-summary-card">

            <div class="activity-summary-icon total">
                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M3 12a9 9 0 1 0 3-6.7"/>
                    <path d="M3 4v5h5"/>
                    <path d="M12 7v5l3 2"/>
                </svg>
            </div>

            <div>
                <span class="activity-summary-label">
                    Total Aktivitas
                </span>

                <strong
                    class="activity-summary-value"
                    id="totalActivities"
                >
                    10
                </strong>

                <span class="activity-summary-caption">
                    aktivitas tercatat
                </span>
            </div>

        </div>


        {{-- AKTIVITAS HARI INI --}}
        <div class="activity-summary-card">

            <div class="activity-summary-icon today">
                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <rect
                        x="3"
                        y="5"
                        width="18"
                        height="16"
                        rx="2"
                    />
                    <path d="M16 3v4"/>
                    <path d="M8 3v4"/>
                    <path d="M3 10h18"/>
                    <path d="m9 16 2 2 4-4"/>
                </svg>
            </div>

            <div>
                <span class="activity-summary-label">
                    Aktivitas Hari Ini
                </span>

                <strong class="activity-summary-value">
                    5
                </strong>

                <span class="activity-summary-caption">
                    aktivitas hari ini
                </span>
            </div>

        </div>


        {{-- ADMIN --}}
        <div class="activity-summary-card">

            <div class="activity-summary-icon admin">
                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <circle cx="12" cy="8" r="4"/>
                    <path d="M4 21a8 8 0 0 1 16 0"/>
                </svg>
            </div>

            <div>
                <span class="activity-summary-label">
                    Aktivitas Admin
                </span>

                <strong class="activity-summary-value">
                    8
                </strong>

                <span class="activity-summary-caption">
                    dilakukan oleh admin
                </span>
            </div>

        </div>


        {{-- PRODUKSI --}}
        <div class="activity-summary-card">

            <div class="activity-summary-icon production">
                <svg
                    width="18"
                    height="18"
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

            <div>
                <span class="activity-summary-label">
                    Aktivitas Produksi
                </span>

                <strong class="activity-summary-value">
                    2
                </strong>

                <span class="activity-summary-caption">
                    dilakukan oleh produksi
                </span>
            </div>

        </div>

    </div>


    {{-- ========================================
        MAIN CARD
    ========================================= --}}
    <div class="activity-logs-card">

        {{-- CARD HEADER --}}
        <div class="activity-logs-card-header">

            <div>
                <h2>Riwayat Aktivitas</h2>

                <p>
                    Daftar aktivitas pengguna yang tercatat di dalam sistem.
                </p>
            </div>

        </div>


        {{-- ========================================
            FILTERS
        ========================================= --}}
        <div class="activity-logs-toolbar">

            {{-- SEARCH --}}
            <div class="activity-logs-search">

                <svg
                    width="17"
                    height="17"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <circle cx="11" cy="11" r="7"/>
                    <path d="m20 20-3.5-3.5"/>
                </svg>

                <input
                    type="text"
                    id="activitySearch"
                    placeholder="Cari aktivitas..."
                >

            </div>


            <div class="activity-logs-filters">

                {{-- MODULE FILTER --}}
                <select
                    class="activity-logs-filter"
                    id="moduleFilter"
                >
                    <option value="all">
                        Semua Modul
                    </option>

                    <option value="order">
                        Order
                    </option>

                    <option value="product">
                        Produk
                    </option>

                    <option value="category">
                        Kategori
                    </option>

                    <option value="inventory">
                        Inventory
                    </option>

                    <option value="material">
                        Bahan Mentah
                    </option>

                    <option value="procurement">
                        Procurement
                    </option>

                    <option value="supplier">
                        Supplier
                    </option>

                    <option value="production">
                        Produksi
                    </option>
                </select>


                {{-- USER FILTER --}}
                <select
                    class="activity-logs-filter"
                    id="userFilter"
                >
                    <option value="all">
                        Semua User
                    </option>

                    <option value="admin">
                        Admin
                    </option>

                    <option value="production">
                        Produksi
                    </option>
                </select>


                {{-- PERIOD FILTER --}}
                <select
                    class="activity-logs-filter"
                    id="periodFilter"
                >
                    <option value="all">
                        Semua Periode
                    </option>

                    <option value="today">
                        Hari Ini
                    </option>

                    <option value="7days">
                        7 Hari Terakhir
                    </option>

                    <option value="30days">
                        30 Hari Terakhir
                    </option>
                </select>

            </div>

        </div>


        {{-- ========================================
            TABLE
        ========================================= --}}
        <div class="activity-logs-table-wrapper">

            <table class="activity-logs-table">

                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Aktivitas</th>
                        <th>Modul</th>
                        <th>Detail</th>
                    </tr>
                </thead>


                <tbody id="activityTableBody">

                    {{-- ========================================
                        1 - PROCUREMENT
                    ========================================= --}}
                    <tr
                        data-module="procurement"
                        data-user="admin"
                        data-period="today"
                        data-search="admin mengirim purchase order procurement po-0012 pt kayu jaya"
                    >
                        <td>
                            <div class="activity-time">
                                <strong>18 Okt 2026</strong>
                                <span>10:32</span>
                            </div>
                        </td>

                        <td>
                            <div class="activity-user">

                                <div class="activity-user-avatar admin">
                                    A
                                </div>

                                <div>
                                    <strong>Admin</strong>
                                    <span>Administrator</span>
                                </div>

                            </div>
                        </td>

                        <td>
                            <div class="activity-description">
                                <strong>Mengirim Purchase Order</strong>
                                <span>
                                    Purchase Order berhasil dikirim ke supplier.
                                </span>
                            </div>
                        </td>

                        <td>
                            <span class="activity-module procurement">
                                Procurement
                            </span>
                        </td>

                        <td>
                            <div class="activity-detail">
                                <strong>PO-0012</strong>
                                <span>PT Kayu Jaya</span>
                            </div>
                        </td>
                    </tr>


                    {{-- ========================================
                        2 - PRODUCT
                    ========================================= --}}
                    <tr
                        data-module="product"
                        data-user="admin"
                        data-period="today"
                        data-search="admin mengubah data produk d6a sign peringatan hitam 9x30"
                    >
                        <td>
                            <div class="activity-time">
                                <strong>18 Okt 2026</strong>
                                <span>10:05</span>
                            </div>
                        </td>

                        <td>
                            <div class="activity-user">

                                <div class="activity-user-avatar admin">
                                    A
                                </div>

                                <div>
                                    <strong>Admin</strong>
                                    <span>Administrator</span>
                                </div>

                            </div>
                        </td>

                        <td>
                            <div class="activity-description">
                                <strong>Mengubah Data Produk</strong>
                                <span>
                                    Informasi produk berhasil diperbarui.
                                </span>
                            </div>
                        </td>

                        <td>
                            <span class="activity-module product">
                                Produk
                            </span>
                        </td>

                        <td>
                            <div class="activity-detail">
                                <strong>D6A</strong>
                                <span>Sign Peringatan Hitam (9x30)</span>
                            </div>
                        </td>
                    </tr>


                    {{-- ========================================
                        3 - PRODUCTION
                    ========================================= --}}
                    <tr
                        data-module="production"
                        data-user="production"
                        data-period="today"
                        data-search="produksi konfirmasi produksi selesai order ord-001"
                    >
                        <td>
                            <div class="activity-time">
                                <strong>18 Okt 2026</strong>
                                <span>09:20</span>
                            </div>
                        </td>

                        <td>
                            <div class="activity-user">

                                <div class="activity-user-avatar production">
                                    P
                                </div>

                                <div>
                                    <strong>Produksi</strong>
                                    <span>Tim Produksi</span>
                                </div>

                            </div>
                        </td>

                        <td>
                            <div class="activity-description">
                                <strong>Konfirmasi Produksi Selesai</strong>
                                <span>
                                    Pesanan selesai diproduksi dan siap dikemas.
                                </span>
                            </div>
                        </td>

                        <td>
                            <span class="activity-module production">
                                Produksi
                            </span>
                        </td>

                        <td>
                            <div class="activity-detail">
                                <strong>ORD-001</strong>
                                <span>Status menjadi Ready to Pack</span>
                            </div>
                        </td>
                    </tr>


                    {{-- ========================================
                        4 - INVENTORY
                    ========================================= --}}
                    <tr
                        data-module="inventory"
                        data-user="admin"
                        data-period="today"
                        data-search="admin penyesuaian stok inventory d6a-1 cctv"
                    >
                        <td>
                            <div class="activity-time">
                                <strong>18 Okt 2026</strong>
                                <span>08:45</span>
                            </div>
                        </td>

                        <td>
                            <div class="activity-user">

                                <div class="activity-user-avatar admin">
                                    A
                                </div>

                                <div>
                                    <strong>Admin</strong>
                                    <span>Administrator</span>
                                </div>

                            </div>
                        </td>

                        <td>
                            <div class="activity-description">
                                <strong>Penyesuaian Stok</strong>
                                <span>
                                    Stok produk jadi diperbarui melalui penyesuaian.
                                </span>
                            </div>
                        </td>

                        <td>
                            <span class="activity-module inventory">
                                Inventory
                            </span>
                        </td>

                        <td>
                            <div class="activity-detail">
                                <strong>D6A-1</strong>
                                <span>CCTV</span>
                            </div>
                        </td>
                    </tr>


                    {{-- ========================================
                        5 - ORDER
                    ========================================= --}}
                    <tr
                        data-module="order"
                        data-user="admin"
                        data-period="today"
                        data-search="admin menyelesaikan order order management ord-002 completed"
                    >
                        <td>
                            <div class="activity-time">
                                <strong>18 Okt 2026</strong>
                                <span>08:15</span>
                            </div>
                        </td>

                        <td>
                            <div class="activity-user">

                                <div class="activity-user-avatar admin">
                                    A
                                </div>

                                <div>
                                    <strong>Admin</strong>
                                    <span>Administrator</span>
                                </div>

                            </div>
                        </td>

                        <td>
                            <div class="activity-description">
                                <strong>Menyelesaikan Order</strong>
                                <span>
                                    Pesanan dikonfirmasi selesai oleh admin.
                                </span>
                            </div>
                        </td>

                        <td>
                            <span class="activity-module order">
                                Order
                            </span>
                        </td>

                        <td>
                            <div class="activity-detail">
                                <strong>ORD-002</strong>
                                <span>Status menjadi Completed</span>
                            </div>
                        </td>
                    </tr>


                    {{-- ========================================
                        6 - PROCUREMENT RECEIVED
                    ========================================= --}}
                    <tr
                        data-module="procurement"
                        data-user="admin"
                        data-period="7days"
                        data-search="admin menerima barang procurement po-0011 cv mdf malang"
                    >
                        <td>
                            <div class="activity-time">
                                <strong>17 Okt 2026</strong>
                                <span>16:10</span>
                            </div>
                        </td>

                        <td>
                            <div class="activity-user">

                                <div class="activity-user-avatar admin">
                                    A
                                </div>

                                <div>
                                    <strong>Admin</strong>
                                    <span>Administrator</span>
                                </div>

                            </div>
                        </td>

                        <td>
                            <div class="activity-description">
                                <strong>Menerima Barang</strong>
                                <span>
                                    Barang dari supplier dikonfirmasi telah diterima.
                                </span>
                            </div>
                        </td>

                        <td>
                            <span class="activity-module procurement">
                                Procurement
                            </span>
                        </td>

                        <td>
                            <div class="activity-detail">
                                <strong>PO-0011</strong>
                                <span>CV MDF Malang</span>
                            </div>
                        </td>
                    </tr>


                    {{-- ========================================
                        7 - SUPPLIER
                    ========================================= --}}
                    <tr
                        data-module="supplier"
                        data-user="admin"
                        data-period="7days"
                        data-search="admin menambah supplier pt warna nusantara supplier"
                    >
                        <td>
                            <div class="activity-time">
                                <strong>17 Okt 2026</strong>
                                <span>14:05</span>
                            </div>
                        </td>

                        <td>
                            <div class="activity-user">

                                <div class="activity-user-avatar admin">
                                    A
                                </div>

                                <div>
                                    <strong>Admin</strong>
                                    <span>Administrator</span>
                                </div>

                            </div>
                        </td>

                        <td>
                            <div class="activity-description">
                                <strong>Menambah Supplier</strong>
                                <span>
                                    Supplier baru ditambahkan ke master supplier.
                                </span>
                            </div>
                        </td>

                        <td>
                            <span class="activity-module supplier">
                                Supplier
                            </span>
                        </td>

                        <td>
                            <div class="activity-detail">
                                <strong>PT Warna Nusantara</strong>
                                <span>Supplier Cat Hitam</span>
                            </div>
                        </td>
                    </tr>


                    {{-- ========================================
                        8 - MATERIAL
                    ========================================= --}}
                    <tr
                        data-module="material"
                        data-user="admin"
                        data-period="7days"
                        data-search="admin mengubah bahan mentah cat hitam safety stock target stock"
                    >
                        <td>
                            <div class="activity-time">
                                <strong>16 Okt 2026</strong>
                                <span>13:42</span>
                            </div>
                        </td>

                        <td>
                            <div class="activity-user">

                                <div class="activity-user-avatar admin">
                                    A
                                </div>

                                <div>
                                    <strong>Admin</strong>
                                    <span>Administrator</span>
                                </div>

                            </div>
                        </td>

                        <td>
                            <div class="activity-description">
                                <strong>Mengubah Bahan Mentah</strong>
                                <span>
                                    Data safety stock dan target stock diperbarui.
                                </span>
                            </div>
                        </td>

                        <td>
                            <span class="activity-module material">
                                Bahan Mentah
                            </span>
                        </td>

                        <td>
                            <div class="activity-detail">
                                <strong>Cat Hitam</strong>
                                <span>Safety 3 lt · Target 15 lt</span>
                            </div>
                        </td>
                    </tr>


                    {{-- ========================================
                        9 - CATEGORY
                    ========================================= --}}
                    <tr
                        data-module="category"
                        data-user="admin"
                        data-period="30days"
                        data-search="admin menambah kategori sign peringatan kategori"
                    >
                        <td>
                            <div class="activity-time">
                                <strong>14 Okt 2026</strong>
                                <span>11:20</span>
                            </div>
                        </td>

                        <td>
                            <div class="activity-user">

                                <div class="activity-user-avatar admin">
                                    A
                                </div>

                                <div>
                                    <strong>Admin</strong>
                                    <span>Administrator</span>
                                </div>

                            </div>
                        </td>

                        <td>
                            <div class="activity-description">
                                <strong>Menambah Kategori</strong>
                                <span>
                                    Kategori produk baru berhasil ditambahkan.
                                </span>
                            </div>
                        </td>

                        <td>
                            <span class="activity-module category">
                                Kategori
                            </span>
                        </td>

                        <td>
                            <div class="activity-detail">
                                <strong>SIGN PERINGATAN</strong>
                                <span>Kategori produk</span>
                            </div>
                        </td>
                    </tr>


                    {{-- ========================================
                        10 - PRODUCTION
                    ========================================= --}}
                    <tr
                        data-module="production"
                        data-user="production"
                        data-period="30days"
                        data-search="produksi memulai produksi ord-003 waiting production"
                    >
                        <td>
                            <div class="activity-time">
                                <strong>13 Okt 2026</strong>
                                <span>09:15</span>
                            </div>
                        </td>

                        <td>
                            <div class="activity-user">

                                <div class="activity-user-avatar production">
                                    P
                                </div>

                                <div>
                                    <strong>Produksi</strong>
                                    <span>Tim Produksi</span>
                                </div>

                            </div>
                        </td>

                        <td>
                            <div class="activity-description">
                                <strong>Memproses Produksi</strong>
                                <span>
                                    Pesanan mulai diproses oleh tim produksi.
                                </span>
                            </div>
                        </td>

                        <td>
                            <span class="activity-module production">
                                Produksi
                            </span>
                        </td>

                        <td>
                            <div class="activity-detail">
                                <strong>ORD-003</strong>
                                <span>Waiting Production</span>
                            </div>
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>


        {{-- ========================================
            EMPTY STATE
        ========================================= --}}
        <div
            class="activity-logs-empty"
            id="activityEmpty"
        >
            <svg
                width="32"
                height="32"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
            >
                <circle cx="11" cy="11" r="7"/>
                <path d="m20 20-3.5-3.5"/>
            </svg>

            <strong>
                Aktivitas tidak ditemukan
            </strong>

            <span>
                Tidak ada aktivitas yang sesuai dengan pencarian atau filter.
            </span>
        </div>

    </div>

</div>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | Elements
    |--------------------------------------------------------------------------
    */

    const activitySearch =
        document.getElementById('activitySearch');

    const moduleFilter =
        document.getElementById('moduleFilter');

    const userFilter =
        document.getElementById('userFilter');

    const periodFilter =
        document.getElementById('periodFilter');

    const activityTableBody =
        document.getElementById('activityTableBody');

    const activityEmpty =
        document.getElementById('activityEmpty');

    const totalActivities =
        document.getElementById('totalActivities');


    /*
    |--------------------------------------------------------------------------
    | Filter Activity Logs
    |--------------------------------------------------------------------------
    */

    function filterActivities() {

        const keyword =
            activitySearch.value
                .toLowerCase()
                .trim();

        const selectedModule =
            moduleFilter.value;

        const selectedUser =
            userFilter.value;

        const selectedPeriod =
            periodFilter.value;


        const rows =
            activityTableBody.querySelectorAll('tr');


        let visibleRows = 0;


        rows.forEach(function (row) {

            /*
            | Search
            */

            const searchableText =
                (
                    row.dataset.search ||
                    row.textContent
                )
                .toLowerCase();


            const searchMatch =
                searchableText.includes(keyword);


            /*
            | Module
            */

            const moduleMatch =
                selectedModule === 'all' ||
                row.dataset.module === selectedModule;


            /*
            | User
            */

            const userMatch =
                selectedUser === 'all' ||
                row.dataset.user === selectedUser;


            /*
            | Period
            |
            | Dummy frontend:
            | today  -> hanya data today
            | 7days  -> today + 7days
            | 30days -> semua data dalam contoh ini
            */

            let periodMatch = true;


            if (selectedPeriod === 'today') {

                periodMatch =
                    row.dataset.period === 'today';

            }


            if (selectedPeriod === '7days') {

                periodMatch =
                    row.dataset.period === 'today' ||
                    row.dataset.period === '7days';

            }


            if (selectedPeriod === '30days') {

                periodMatch =
                    row.dataset.period === 'today' ||
                    row.dataset.period === '7days' ||
                    row.dataset.period === '30days';

            }


            /*
            | Final
            */

            const show =
                searchMatch &&
                moduleMatch &&
                userMatch &&
                periodMatch;


            row.style.display =
                show ? '' : 'none';


            if (show) {
                visibleRows++;
            }

        });


        /*
        | Empty State
        */

        activityEmpty.style.display =
            visibleRows === 0
                ? 'flex'
                : 'none';

    }


    /*
    |--------------------------------------------------------------------------
    | Event Listeners
    |--------------------------------------------------------------------------
    */

    activitySearch.addEventListener(
        'input',
        filterActivities
    );


    moduleFilter.addEventListener(
        'change',
        filterActivities
    );


    userFilter.addEventListener(
        'change',
        filterActivities
    );


    periodFilter.addEventListener(
        'change',
        filterActivities
    );


    /*
    |--------------------------------------------------------------------------
    | Initial Summary
    |--------------------------------------------------------------------------
    */

    const totalRows =
        activityTableBody.querySelectorAll('tr').length;

    totalActivities.textContent =
        totalRows;


    /*
    |--------------------------------------------------------------------------
    | Initial Filter
    |--------------------------------------------------------------------------
    */

    filterActivities();

});
</script>

@endpush