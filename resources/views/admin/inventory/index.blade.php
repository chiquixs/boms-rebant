@extends('layouts.admin')

@section('title', 'Inventory')

@section('content')

<div class="inventory-page">

    {{-- =========================
        PAGE HEADER
    ========================== --}}
    <div class="inventory-page-header">
        <div>
            <h1>Inventory</h1>
            <p>Pantau stok produk jadi, bahan baku, dan pergerakan persediaan.</p>
        </div>

        <div class="inventory-header-icon">
            <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
            >
                <path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5v-9Z"/>
                <path d="m4 7.5 8 4.5 8-4.5"/>
                <path d="M12 12v9"/>
            </svg>
        </div>
    </div>


    {{-- =========================
        SUMMARY CARDS
    ========================== --}}
    <div class="inventory-summary-grid">

        <div class="inventory-summary-card">
            <div class="summary-label">
                SKU Stok Rendah
            </div>

            <div class="summary-value danger">
                2
            </div>

            <div class="summary-caption">
                Variant di bawah safety stock
            </div>
        </div>


        <div class="inventory-summary-card">
            <div class="summary-label">
                Bahan Baku Menipis
            </div>

            <div class="summary-value warning">
                1
            </div>

            <div class="summary-caption">
                Bahan di bawah safety stock
            </div>
        </div>


        <div class="inventory-summary-card">
            <div class="summary-label">
                Stok Masuk Hari Ini
            </div>

            <div class="summary-value">
                12
            </div>

            <div class="summary-caption">
                Transaksi stok masuk
            </div>
        </div>


        <div class="inventory-summary-card">
            <div class="summary-label">
                Stok Keluar Hari Ini
            </div>

            <div class="summary-value">
                8
            </div>

            <div class="summary-caption">
                Transaksi stok keluar
            </div>
        </div>

    </div>


    {{-- =========================
        INVENTORY CONTENT
    ========================== --}}
    <div class="inventory-card">

        {{-- TABS --}}
        <div class="inventory-tabs">

            <button
                type="button"
                class="inventory-tab active"
                data-tab="finishedGoods"
            >
                Produk Jadi
            </button>

            <button
                type="button"
                class="inventory-tab"
                data-tab="rawMaterials"
            >
                Bahan Baku
            </button>

            <button
                type="button"
                class="inventory-tab"
                data-tab="stockHistory"
            >
                Riwayat
            </button>

        </div>


        {{-- ========================================
             TAB 1 — PRODUK JADI
        ========================================= --}}
        <div
            class="inventory-tab-content active"
            id="finishedGoods"
        >

            <div class="inventory-section-header">
                <div>
                    <h2>Stok Produk Jadi</h2>

                    <p>
                        Stok produk MTS berdasarkan SKU Variant.
                    </p>
                </div>
            </div>


            <div class="inventory-table-wrapper">

                <table class="inventory-table finished-goods-table">

                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Varian</th>
                            <th>SKU Variant</th>
                            <th>Stok Saat Ini</th>
                            <th>Safety Stock</th>
                            <th>Target Stock</th>
                            <th>Status</th>
                            <th class="inventory-action-column">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody>

                        {{-- D6A-1 --}}
                        <tr>
                            <td>
                                <div class="inventory-product-info">
                                    <strong>
                                        Sign Peringatan Hitam (9x30)
                                    </strong>

                                    <span>
                                        SKU Product: D6A
                                    </span>
                                </div>
                            </td>

                            <td>CCTV</td>

                            <td>
                                <span class="inventory-sku">
                                    D6A-1
                                </span>
                            </td>

                            <td class="inventory-stock-number">
                                50
                            </td>

                            <td class="inventory-stock-number">
                                30
                            </td>

                            <td class="inventory-stock-number">
                                80
                            </td>

                            <td>
                                <span class="stock-status safe">
                                    Aman
                                </span>
                            </td>

                            <td>
                                <a
                                    href="/admin/inventory/D6A-1/history"
                                    class="history-button"
                                >
                                    <svg
                                        width="14"
                                        height="14"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M3 12a9 9 0 1 0 3-6.7"/>
                                        <path d="M3 4v5h5"/>
                                        <path d="M12 7v5l3 2"/>
                                    </svg>

                                    Riwayat
                                </a>
                            </td>
                        </tr>


                        {{-- D6A-2 --}}
                        <tr>
                            <td>
                                <div class="inventory-product-info">
                                    <strong>
                                        Sign Peringatan Hitam (9x30)
                                    </strong>

                                    <span>
                                        SKU Product: D6A
                                    </span>
                                </div>
                            </td>

                            <td>Dilarang Merokok</td>

                            <td>
                                <span class="inventory-sku">
                                    D6A-2
                                </span>
                            </td>

                            <td class="inventory-stock-number">
                                75
                            </td>

                            <td class="inventory-stock-number">
                                30
                            </td>

                            <td class="inventory-stock-number">
                                80
                            </td>

                            <td>
                                <span class="stock-status safe">
                                    Aman
                                </span>
                            </td>

                            <td>
                                <a
                                    href="/admin/inventory/D6A-2/history"
                                    class="history-button"
                                >
                                    <svg
                                        width="14"
                                        height="14"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M3 12a9 9 0 1 0 3-6.7"/>
                                        <path d="M3 4v5h5"/>
                                        <path d="M12 7v5l3 2"/>
                                    </svg>

                                    Riwayat
                                </a>
                            </td>
                        </tr>


                        {{-- D6A-3 --}}
                        <tr>
                            <td>
                                <div class="inventory-product-info">
                                    <strong>
                                        Sign Peringatan Hitam (9x30)
                                    </strong>

                                    <span>
                                        SKU Product: D6A
                                    </span>
                                </div>
                            </td>

                            <td>Area Merokok</td>

                            <td>
                                <span class="inventory-sku">
                                    D6A-3
                                </span>
                            </td>

                            <td class="inventory-stock-number">
                                30
                            </td>

                            <td class="inventory-stock-number">
                                30
                            </td>

                            <td class="inventory-stock-number">
                                80
                            </td>

                            <td>
                                <span class="stock-status warning">
                                    Perhatian
                                </span>
                            </td>

                            <td>
                                <a
                                    href="/admin/inventory/D6A-3/history"
                                    class="history-button"
                                >
                                    <svg
                                        width="14"
                                        height="14"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M3 12a9 9 0 1 0 3-6.7"/>
                                        <path d="M3 4v5h5"/>
                                        <path d="M12 7v5l3 2"/>
                                    </svg>

                                    Riwayat
                                </a>
                            </td>
                        </tr>


                        {{-- D6A-4 --}}
                        <tr>
                            <td>
                                <div class="inventory-product-info">
                                    <strong>
                                        Sign Peringatan Hitam (9x30)
                                    </strong>

                                    <span>
                                        SKU Product: D6A
                                    </span>
                                </div>
                            </td>

                            <td>Arah Kiblat</td>

                            <td>
                                <span class="inventory-sku">
                                    D6A-4
                                </span>
                            </td>

                            <td class="inventory-stock-number">
                                34
                            </td>

                            <td class="inventory-stock-number">
                                30
                            </td>

                            <td class="inventory-stock-number">
                                80
                            </td>

                            <td>
                                <span class="stock-status safe">
                                    Aman
                                </span>
                            </td>

                            <td>
                                <a
                                    href="/admin/inventory/D6A-4/history"
                                    class="history-button"
                                >
                                    <svg
                                        width="14"
                                        height="14"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M3 12a9 9 0 1 0 3-6.7"/>
                                        <path d="M3 4v5h5"/>
                                        <path d="M12 7v5l3 2"/>
                                    </svg>

                                    Riwayat
                                </a>
                            </td>
                        </tr>


                        {{-- D6A-5 --}}
                        <tr>
                            <td>
                                <div class="inventory-product-info">
                                    <strong>
                                        Sign Peringatan Hitam (9x30)
                                    </strong>

                                    <span>
                                        SKU Product: D6A
                                    </span>
                                </div>
                            </td>

                            <td>Jagalah Kebersihan</td>

                            <td>
                                <span class="inventory-sku">
                                    D6A-5
                                </span>
                            </td>

                            <td class="inventory-stock-number low-stock">
                                20
                            </td>

                            <td class="inventory-stock-number">
                                30
                            </td>

                            <td class="inventory-stock-number">
                                80
                            </td>

                            <td>
                                <span class="stock-status low">
                                    Rendah
                                </span>
                            </td>

                            <td>
                                <a
                                    href="/admin/inventory/D6A-5/history"
                                    class="history-button"
                                >
                                    <svg
                                        width="14"
                                        height="14"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M3 12a9 9 0 1 0 3-6.7"/>
                                        <path d="M3 4v5h5"/>
                                        <path d="M12 7v5l3 2"/>
                                    </svg>

                                    Riwayat
                                </a>
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>

        </div>


        {{-- ========================================
             TAB 2 — BAHAN BAKU
        ========================================= --}}
        <div
            class="inventory-tab-content"
            id="rawMaterials"
        >

            <div class="inventory-section-header">
                <div>
                    <h2>Stok Bahan Baku</h2>

                    <p>
                        Satuan mengikuti master data bahan mentah.
                    </p>
                </div>
            </div>


            <div class="inventory-table-wrapper">

                <table class="inventory-table">

                    <thead>
                        <tr>
                            <th>Bahan</th>
                            <th>Tipe</th>
                            <th>Satuan</th>
                            <th>Stok Saat Ini</th>
                            <th>Safety Stock</th>
                            <th>Target Stock</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        {{-- MDF --}}
                        <tr>
                            <td>
                                <strong>MDF</strong>
                            </td>

                            <td>
                                <span class="inventory-material-type sheet">
                                    SHEET
                                </span>
                            </td>

                            <td>lembar</td>

                            <td class="inventory-stock-number">
                                20
                            </td>

                            <td class="inventory-stock-number">
                                5
                            </td>

                            <td class="inventory-stock-number">
                                30
                            </td>

                            <td>
                                <span class="stock-status safe">
                                    Aman
                                </span>
                            </td>
                        </tr>


                        {{-- HANGER --}}
                        <tr>
                            <td>
                                <strong>Hanger</strong>
                            </td>

                            <td>
                                <span class="inventory-material-type non-sheet">
                                    NON-SHEET
                                </span>
                            </td>

                            <td>pcs</td>

                            <td class="inventory-stock-number">
                                150
                            </td>

                            <td class="inventory-stock-number">
                                30
                            </td>

                            <td class="inventory-stock-number">
                                200
                            </td>

                            <td>
                                <span class="stock-status safe">
                                    Aman
                                </span>
                            </td>
                        </tr>


                        {{-- CAT HITAM --}}
                        <tr>
                            <td>
                                <strong>Cat Hitam</strong>
                            </td>

                            <td>
                                <span class="inventory-material-type non-sheet">
                                    NON-SHEET
                                </span>
                            </td>

                            <td>lt</td>

                            <td class="inventory-stock-number low-stock">
                                2
                            </td>

                            <td class="inventory-stock-number">
                                3
                            </td>

                            <td class="inventory-stock-number">
                                15
                            </td>

                            <td>
                                <span class="stock-status low">
                                    Rendah
                                </span>
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>

        </div>


        {{-- ========================================
             TAB 3 — RIWAYAT
        ========================================= --}}
        <div
            class="inventory-tab-content"
            id="stockHistory"
        >

            <div class="inventory-section-header">
                <div>
                    <h2>Riwayat Pergerakan Stok</h2>

                    <p>
                        Catatan perubahan stok produk jadi dan bahan baku.
                    </p>
                </div>
            </div>


            <div class="inventory-table-wrapper">

                <table class="inventory-table history-table">

                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Item</th>
                            <th>Aktivitas</th>
                            <th>Perubahan</th>
                            <th>Sumber</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <td>13 Okt 2026</td>

                            <td>
                                <div class="history-item">
                                    <strong>D6A-5</strong>
                                    <span>Jagalah Kebersihan</span>
                                </div>
                            </td>

                            <td>
                                Pesanan siap dikemas
                            </td>

                            <td>
                                <span class="stock-change out">
                                    −3 pcs
                                </span>
                            </td>

                            <td>
                                Order
                            </td>
                        </tr>


                        <tr>
                            <td>12 Okt 2026</td>

                            <td>
                                <div class="history-item">
                                    <strong>D6A-1</strong>
                                    <span>CCTV</span>
                                </div>
                            </td>

                            <td>
                                Produksi selesai
                            </td>

                            <td>
                                <span class="stock-change in">
                                    +10 pcs
                                </span>
                            </td>

                            <td>
                                Produksi
                            </td>
                        </tr>


                        <tr>
                            <td>12 Okt 2026</td>

                            <td>
                                <div class="history-item">
                                    <strong>MDF</strong>
                                    <span>Bahan Baku</span>
                                </div>
                            </td>

                            <td>
                                Penerimaan bahan
                            </td>

                            <td>
                                <span class="stock-change in">
                                    +5 lembar
                                </span>
                            </td>

                            <td>
                                Procurement
                            </td>
                        </tr>


                        <tr>
                            <td>11 Okt 2026</td>

                            <td>
                                <div class="history-item">
                                    <strong>D6A-2</strong>
                                    <span>Dilarang Merokok</span>
                                </div>
                            </td>

                            <td>
                                Pesanan siap dikemas
                            </td>

                            <td>
                                <span class="stock-change out">
                                    −5 pcs
                                </span>
                            </td>

                            <td>
                                Order
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const tabs =
        document.querySelectorAll('.inventory-tab');

    const tabContents =
        document.querySelectorAll('.inventory-tab-content');


    tabs.forEach(function (tab) {

        tab.addEventListener('click', function () {

            const targetId =
                tab.dataset.tab;


            tabs.forEach(function (item) {
                item.classList.remove('active');
            });


            tabContents.forEach(function (content) {
                content.classList.remove('active');
            });


            tab.classList.add('active');


            const targetContent =
                document.getElementById(targetId);


            if (targetContent) {
                targetContent.classList.add('active');
            }

        });

    });

});
</script>

@endpush