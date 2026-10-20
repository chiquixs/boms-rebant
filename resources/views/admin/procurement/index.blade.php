@extends('layouts.admin')

@section('title', 'Procurement')

@section('content')

<div class="procurement-page">

    {{-- ========================================
        PAGE HEADER
    ========================================= --}}
    <div class="procurement-page-header">

        <div>
            <h1>Procurement</h1>
            <p>
                Kelola kebutuhan restock bahan baku dan purchase order supplier.
            </p>
        </div>

    </div>


    {{-- ========================================
        SUMMARY CARDS
    ========================================= --}}
    <div class="procurement-summary">

        <div class="procurement-summary-card">

            <div class="procurement-summary-icon warning">
                <svg
                    width="20"
                    height="20"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="M12 9v4"/>
                    <path d="M12 17h.01"/>
                    <path d="M10.3 3.8 2.6 17.2A2 2 0 0 0 4.3 20h15.4a2 2 0 0 0 1.7-2.8L13.7 3.8a2 2 0 0 0-3.4 0Z"/>
                </svg>
            </div>

            <div>
                <span class="procurement-summary-label">
                    Perlu Restock
                </span>

                <strong class="procurement-summary-value">
                    2
                </strong>

                <span class="procurement-summary-caption">
                    bahan di bawah safety stock
                </span>
            </div>

        </div>


        <div class="procurement-summary-card">

            <div class="procurement-summary-icon order">
                <svg
                    width="20"
                    height="20"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="M6 2h12v20l-3-2-3 2-3-2-3 2V2Z"/>
                    <path d="M9 7h6"/>
                    <path d="M9 11h6"/>
                </svg>
            </div>

            <div>
                <span class="procurement-summary-label">
                    PO Aktif
                </span>

                <strong class="procurement-summary-value">
                    2
                </strong>

                <span class="procurement-summary-caption">
                    sedang dalam proses
                </span>
            </div>

        </div>


        <div class="procurement-summary-card">

            <div class="procurement-summary-icon waiting">
                <svg
                    width="20"
                    height="20"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 7v5l3 2"/>
                </svg>
            </div>

            <div>
                <span class="procurement-summary-label">
                    Menunggu Barang
                </span>

                <strong class="procurement-summary-value">
                    1
                </strong>

                <span class="procurement-summary-caption">
                    purchase order
                </span>
            </div>

        </div>


        <div class="procurement-summary-card">

            <div class="procurement-summary-icon received">
                <svg
                    width="20"
                    height="20"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="m5 12 4 4L19 6"/>
                </svg>
            </div>

            <div>
                <span class="procurement-summary-label">
                    Diterima Bulan Ini
                </span>

                <strong class="procurement-summary-value">
                    4
                </strong>

                <span class="procurement-summary-caption">
                    purchase order selesai
                </span>
            </div>

        </div>

    </div>


    {{-- ========================================
        MAIN CARD
    ========================================= --}}
    <div class="procurement-card">

        {{-- TABS --}}
        <div class="procurement-tabs">

            <button
                type="button"
                class="procurement-tab active"
                data-tab="restock"
            >
                Perlu Restock

                <span class="procurement-tab-count">
                    2
                </span>
            </button>


            <button
                type="button"
                class="procurement-tab"
                data-tab="purchase-orders"
            >
                Purchase Order
            </button>

        </div>


        {{-- ========================================
            TAB: PERLU RESTOCK
        ========================================= --}}
        <div
            class="procurement-tab-content active"
            id="restockTab"
        >

            <div class="procurement-section-header">

                <div>
                    <h2>Rekomendasi Restock</h2>

                    <p>
                        Bahan baku yang telah mencapai atau berada di bawah safety stock.
                    </p>
                </div>

            </div>


            {{-- FILTER --}}
            <div class="procurement-toolbar">

                <div class="procurement-search">

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
                        id="restockSearch"
                        placeholder="Cari bahan baku..."
                    >

                </div>

            </div>


            {{-- TABLE --}}
            <div class="procurement-table-wrapper">

                <table class="procurement-table">

                    <thead>
                        <tr>
                            <th>Bahan Baku</th>
                            <th>Tipe</th>
                            <th>Stok Saat Ini</th>
                            <th>Safety Stock</th>
                            <th>Target Stock</th>
                            <th>Rekomendasi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>


                    <tbody id="restockTableBody">

                        {{-- CAT HITAM --}}
                        <tr
                            data-name="Cat Hitam"
                            data-type="NON-SHEET"
                            data-unit="lt"
                            data-current="2"
                            data-safety="3"
                            data-target="15"
                            data-recommendation="13"
                        >

                            <td>
                                <div class="procurement-material">
                                    <strong>Cat Hitam</strong>
                                    <span>Satuan: lt</span>
                                </div>
                            </td>

                            <td>
                                <span class="procurement-type non-sheet">
                                    NON-SHEET
                                </span>
                            </td>

                            <td class="stock-number">
                                2 lt
                            </td>

                            <td class="stock-number">
                                3 lt
                            </td>

                            <td class="stock-number">
                                15 lt
                            </td>

                            <td>
                                <strong class="procurement-recommendation">
                                    13 lt
                                </strong>
                            </td>

                            <td>
                                <span class="procurement-stock-status low">
                                    <span></span>
                                    Rendah
                                </span>
                            </td>

                            <td>
                                <button
                                    type="button"
                                    class="procurement-action-button review-restock-button"
                                >
                                    Review
                                </button>
                            </td>

                        </tr>


                        {{-- MDF --}}
                        <tr
                            data-name="MDF"
                            data-type="SHEET"
                            data-unit="lembar"
                            data-current="4"
                            data-safety="5"
                            data-target="30"
                            data-recommendation="26"
                        >

                            <td>
                                <div class="procurement-material">
                                    <strong>MDF</strong>
                                    <span>Satuan: lembar</span>
                                </div>
                            </td>

                            <td>
                                <span class="procurement-type sheet">
                                    SHEET
                                </span>
                            </td>

                            <td class="stock-number">
                                4 lembar
                            </td>

                            <td class="stock-number">
                                5 lembar
                            </td>

                            <td class="stock-number">
                                30 lembar
                            </td>

                            <td>
                                <strong class="procurement-recommendation">
                                    26 lembar
                                </strong>
                            </td>

                            <td>
                                <span class="procurement-stock-status low">
                                    <span></span>
                                    Rendah
                                </span>
                            </td>

                            <td>
                                <button
                                    type="button"
                                    class="procurement-action-button review-restock-button"
                                >
                                    Review
                                </button>
                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>


            {{-- EMPTY STATE --}}
            <div
                class="procurement-empty"
                id="restockEmpty"
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

                <strong>Bahan tidak ditemukan</strong>

                <span>
                    Tidak ada bahan baku yang sesuai dengan pencarian.
                </span>
            </div>

        </div>


        {{-- ========================================
            TAB: PURCHASE ORDER
        ========================================= --}}
        <div
            class="procurement-tab-content"
            id="purchaseOrdersTab"
        >

            <div class="procurement-section-header">

                <div>
                    <h2>Purchase Order</h2>

                    <p>
                        Daftar pemesanan bahan baku yang telah dikonfirmasi.
                    </p>
                </div>

            </div>


            {{-- FILTER --}}
            <div class="procurement-toolbar">

                <div class="procurement-search">

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
                        id="poSearch"
                        placeholder="Cari PO atau supplier..."
                    >

                </div>


                <select
                    class="procurement-filter"
                    id="poStatusFilter"
                >
                    <option value="all">
                        Semua Status
                    </option>

                    <option value="ordered">
                        Ordered
                    </option>

                    <option value="received">
                        Received
                    </option>
                </select>

            </div>


            {{-- PO TABLE --}}
            <div class="procurement-table-wrapper">

                <table class="procurement-table">

                    <thead>
                        <tr>
                            <th>No. PO</th>
                            <th>Tanggal</th>
                            <th>Supplier</th>
                            <th>Total Item</th>
                            <th>Total</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>


                    <tbody id="poTableBody">

                        <tr
                            data-search="PO-0012 PT Kayu Jaya"
                            data-status="ordered"
                        >

                            <td>
                                <strong class="procurement-po-number">
                                    PO-0012
                                </strong>
                            </td>

                            <td>
                                13 Okt 2026
                            </td>

                            <td>
                                <div class="procurement-supplier">
                                    <strong>PT Kayu Jaya</strong>
                                    <span>supplier@kayujaya.com</span>
                                </div>
                            </td>

                            <td>
                                1 bahan
                            </td>

                            <td>
                                Rp 850.000
                            </td>

                            <td>
                                <span class="procurement-email-status sent">
                                    <svg
                                        width="13"
                                        height="13"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path d="m5 12 4 4L19 6"/>
                                    </svg>

                                    Terkirim
                                </span>
                            </td>

                            <td>
                                <span class="procurement-po-status ordered">
                                    Ordered
                                </span>
                            </td>

                            <td>
                                <a
                                    href="/admin/procurement/PO-0012"
                                    class="procurement-detail-button"
                                >
                                    Detail
                                </a>
                            </td>

                        </tr>


                        <tr
                            data-search="PO-0011 CV MDF Malang"
                            data-status="received"
                        >

                            <td>
                                <strong class="procurement-po-number">
                                    PO-0011
                                </strong>
                            </td>

                            <td>
                                10 Okt 2026
                            </td>

                            <td>
                                <div class="procurement-supplier">
                                    <strong>CV MDF Malang</strong>
                                    <span>order@mdfmalang.com</span>
                                </div>
                            </td>

                            <td>
                                1 bahan
                            </td>

                            <td>
                                Rp 1.250.000
                            </td>

                            <td>
                                <span class="procurement-email-status sent">
                                    <svg
                                        width="13"
                                        height="13"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path d="m5 12 4 4L19 6"/>
                                    </svg>

                                    Terkirim
                                </span>
                            </td>

                            <td>
                                <span class="procurement-po-status received">
                                    Received
                                </span>
                            </td>

                            <td>
                                <a
                                    href="/admin/procurement/PO-0011"
                                    class="procurement-detail-button"
                                >
                                    Detail
                                </a>
                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>


            {{-- EMPTY STATE --}}
            <div
                class="procurement-empty"
                id="poEmpty"
            >
                <svg
                    width="32"
                    height="32"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                >
                    <path d="M6 2h12v20l-3-2-3 2-3-2-3 2V2Z"/>
                    <path d="M9 8h6"/>
                </svg>

                <strong>Purchase order tidak ditemukan</strong>

                <span>
                    Tidak ada purchase order yang sesuai dengan filter.
                </span>
            </div>

        </div>

    </div>

</div>



{{-- ========================================
    RESTOCK REVIEW MODAL
======================================== --}}
<div
    class="procurement-modal-overlay"
    id="restockModal"
>

    <div class="procurement-modal">

        {{-- MODAL HEADER --}}
        <div class="procurement-modal-header">

            <div>
                <h2>Konfirmasi Pemesanan</h2>

                <p>
                    Review kebutuhan bahan sebelum purchase order dibuat.
                </p>
            </div>


            <button
                type="button"
                class="procurement-modal-close"
                id="closeRestockModal"
                aria-label="Tutup modal"
            >
                <svg
                    width="20"
                    height="20"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="M18 6 6 18"/>
                    <path d="m6 6 12 12"/>
                </svg>
            </button>

        </div>


        {{-- MODAL BODY --}}
        <div class="procurement-modal-body">

            {{-- MATERIAL INFORMATION --}}
            <div class="procurement-modal-material">

                <div>
                    <span class="procurement-modal-label">
                        Bahan Baku
                    </span>

                    <strong id="modalMaterialName">
                        -
                    </strong>

                    <span id="modalMaterialType">
                        -
                    </span>
                </div>


                <div class="procurement-modal-stock-grid">

                    <div>
                        <span>Stok Saat Ini</span>
                        <strong id="modalCurrentStock">-</strong>
                    </div>

                    <div>
                        <span>Safety Stock</span>
                        <strong id="modalSafetyStock">-</strong>
                    </div>

                    <div>
                        <span>Target Stock</span>
                        <strong id="modalTargetStock">-</strong>
                    </div>

                </div>

            </div>


            {{-- FORM --}}
            <form id="restockForm">

                <div class="procurement-form-group">

                    <label for="orderQuantity">
                        Jumlah Pesan
                    </label>

                    <div class="procurement-input-unit">

                        <input
                            type="number"
                            id="orderQuantity"
                            min="1"
                            required
                        >

                        <span id="orderUnit">
                            -
                        </span>

                    </div>

                </div>


                <div class="procurement-form-group">

                    <label for="supplierSelect">
                        Supplier
                    </label>

                    <select
                        id="supplierSelect"
                        required
                    >
                        <option
                            value=""
                            data-email=""
                        >
                            Pilih supplier
                        </option>

                        <option
                            value="PT Kayu Jaya"
                            data-email="supplier@kayujaya.com"
                        >
                            PT Kayu Jaya
                        </option>

                        <option
                            value="CV MDF Malang"
                            data-email="order@mdfmalang.com"
                        >
                            CV MDF Malang
                        </option>

                        <option
                            value="PT Warna Nusantara"
                            data-email="order@warnanusantara.com"
                        >
                            PT Warna Nusantara
                        </option>
                    </select>

                </div>


                <div class="procurement-form-group">

                    <label for="supplierEmail">
                        Email Supplier
                    </label>

                    <input
                        type="email"
                        id="supplierEmail"
                        placeholder="Email supplier"
                        readonly
                    >

                    <span class="procurement-form-hint">
                        Email akan mengikuti supplier yang dipilih.
                    </span>

                </div>


                <div class="procurement-form-group">

                    <label for="unitPrice">
                        Harga per Satuan
                    </label>

                    <div class="procurement-price-input">

                        <span>Rp</span>

                        <input
                            type="number"
                            id="unitPrice"
                            min="0"
                            placeholder="0"
                            required
                        >

                    </div>

                </div>


                <div class="procurement-form-group">

                    <label for="procurementNotes">
                        Catatan
                        <span>(Opsional)</span>
                    </label>

                    <textarea
                        id="procurementNotes"
                        rows="3"
                        placeholder="Tambahkan catatan untuk supplier..."
                    ></textarea>

                </div>


                {{-- EMAIL INFORMATION --}}
                <div class="procurement-email-info">

                    <div class="procurement-email-info-icon">
                        <svg
                            width="18"
                            height="18"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <rect
                                x="3"
                                y="5"
                                width="18"
                                height="14"
                                rx="2"
                            />

                            <path d="m3 7 9 6 9-6"/>
                        </svg>
                    </div>


                    <div>
                        <strong>
                            Purchase Order akan dikirim melalui email
                        </strong>

                        <span>
                            Setelah dikonfirmasi, sistem akan membuat PO
                            dan mengirimkannya ke email supplier.
                        </span>
                    </div>

                </div>

            </form>

        </div>


        {{-- MODAL FOOTER --}}
        <div class="procurement-modal-footer">

            <button
                type="button"
                class="procurement-button secondary"
                id="cancelRestockButton"
            >
                Batal
            </button>


            <button
                type="submit"
                form="restockForm"
                class="procurement-button primary"
            >
                <svg
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="M22 2 11 13"/>
                    <path d="m22 2-7 20-4-9-9-4Z"/>
                </svg>

                Konfirmasi & Kirim
            </button>

        </div>

    </div>

</div>

@endsection



@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | Tabs
    |--------------------------------------------------------------------------
    */

    const tabButtons =
        document.querySelectorAll('.procurement-tab');

    const tabContents =
        document.querySelectorAll('.procurement-tab-content');


    tabButtons.forEach(function (button) {

        button.addEventListener('click', function () {

            const target =
                button.dataset.tab;


            tabButtons.forEach(function (item) {
                item.classList.remove('active');
            });


            tabContents.forEach(function (content) {
                content.classList.remove('active');
            });


            button.classList.add('active');


            if (target === 'restock') {

                document
                    .getElementById('restockTab')
                    .classList.add('active');

            } else {

                document
                    .getElementById('purchaseOrdersTab')
                    .classList.add('active');

            }

        });

    });



    /*
    |--------------------------------------------------------------------------
    | Restock Search
    |--------------------------------------------------------------------------
    */

    const restockSearch =
        document.getElementById('restockSearch');

    const restockRows =
        document.querySelectorAll(
            '#restockTableBody tr'
        );

    const restockEmpty =
        document.getElementById('restockEmpty');


    function filterRestock() {

        const keyword =
            restockSearch.value
                .toLowerCase()
                .trim();

        let visibleRows = 0;


        restockRows.forEach(function (row) {

            const materialName =
                row.dataset.name
                    .toLowerCase();

            const show =
                materialName.includes(keyword);


            row.style.display =
                show ? '' : 'none';


            if (show) {
                visibleRows++;
            }

        });


        restockEmpty.style.display =
            visibleRows === 0
                ? 'flex'
                : 'none';

    }


    restockSearch.addEventListener(
        'input',
        filterRestock
    );



    /*
    |--------------------------------------------------------------------------
    | Purchase Order Filter
    |--------------------------------------------------------------------------
    */

    const poSearch =
        document.getElementById('poSearch');

    const poStatusFilter =
        document.getElementById('poStatusFilter');

    const poRows =
        document.querySelectorAll(
            '#poTableBody tr'
        );

    const poEmpty =
        document.getElementById('poEmpty');


    function filterPurchaseOrders() {

        const keyword =
            poSearch.value
                .toLowerCase()
                .trim();

        const status =
            poStatusFilter.value;

        let visibleRows = 0;


        poRows.forEach(function (row) {

            const searchText =
                row.dataset.search
                    .toLowerCase();

            const rowStatus =
                row.dataset.status;


            const searchMatch =
                searchText.includes(keyword);

            const statusMatch =
                status === 'all' ||
                status === rowStatus;


            const show =
                searchMatch &&
                statusMatch;


            row.style.display =
                show ? '' : 'none';


            if (show) {
                visibleRows++;
            }

        });


        poEmpty.style.display =
            visibleRows === 0
                ? 'flex'
                : 'none';

    }


    poSearch.addEventListener(
        'input',
        filterPurchaseOrders
    );


    poStatusFilter.addEventListener(
        'change',
        filterPurchaseOrders
    );



    /*
    |--------------------------------------------------------------------------
    | Restock Modal
    |--------------------------------------------------------------------------
    */

    const restockModal =
        document.getElementById('restockModal');

    const reviewButtons =
        document.querySelectorAll(
            '.review-restock-button'
        );

    const closeRestockModal =
        document.getElementById(
            'closeRestockModal'
        );

    const cancelRestockButton =
        document.getElementById(
            'cancelRestockButton'
        );


    const modalMaterialName =
        document.getElementById(
            'modalMaterialName'
        );

    const modalMaterialType =
        document.getElementById(
            'modalMaterialType'
        );

    const modalCurrentStock =
        document.getElementById(
            'modalCurrentStock'
        );

    const modalSafetyStock =
        document.getElementById(
            'modalSafetyStock'
        );

    const modalTargetStock =
        document.getElementById(
            'modalTargetStock'
        );

    const orderQuantity =
        document.getElementById(
            'orderQuantity'
        );

    const orderUnit =
        document.getElementById(
            'orderUnit'
        );


    function openRestockModal(row) {

        const name =
            row.dataset.name;

        const type =
            row.dataset.type;

        const unit =
            row.dataset.unit;

        const current =
            row.dataset.current;

        const safety =
            row.dataset.safety;

        const target =
            row.dataset.target;

        const recommendation =
            row.dataset.recommendation;


        modalMaterialName.textContent =
            name;

        modalMaterialType.textContent =
            type;

        modalCurrentStock.textContent =
            current + ' ' + unit;

        modalSafetyStock.textContent =
            safety + ' ' + unit;

        modalTargetStock.textContent =
            target + ' ' + unit;

        orderQuantity.value =
            recommendation;

        orderUnit.textContent =
            unit;


        restockModal.classList.add('show');

        document.body.classList.add(
            'modal-open'
        );

    }


    function closeModal() {

        restockModal.classList.remove('show');

        document.body.classList.remove(
            'modal-open'
        );

    }


    reviewButtons.forEach(function (button) {

        button.addEventListener(
            'click',
            function () {

                const row =
                    button.closest('tr');

                openRestockModal(row);

            }
        );

    });


    closeRestockModal.addEventListener(
        'click',
        closeModal
    );


    cancelRestockButton.addEventListener(
        'click',
        closeModal
    );


    restockModal.addEventListener(
        'click',
        function (event) {

            if (event.target === restockModal) {
                closeModal();
            }

        }
    );



    /*
    |--------------------------------------------------------------------------
    | Supplier Email
    |--------------------------------------------------------------------------
    */

    const supplierSelect =
        document.getElementById(
            'supplierSelect'
        );

    const supplierEmail =
        document.getElementById(
            'supplierEmail'
        );


    supplierSelect.addEventListener(
        'change',
        function () {

            const selectedOption =
                supplierSelect.options[
                    supplierSelect.selectedIndex
                ];

            supplierEmail.value =
                selectedOption.dataset.email || '';

        }
    );



    /*
    |--------------------------------------------------------------------------
    | Dummy Submit
    |--------------------------------------------------------------------------
    |
    | Frontend only.
    | Nanti backend akan:
    | 1. Membuat Purchase Order
    | 2. Menyimpan PO
    | 3. Memanggil Email API
    | 4. Mengirim PO ke supplier
    |
    */

    const restockForm =
        document.getElementById(
            'restockForm'
        );


    restockForm.addEventListener(
        'submit',
        function (event) {

            event.preventDefault();


            if (!supplierSelect.value) {

                supplierSelect.focus();
                return;

            }


            alert(
                'Purchase Order berhasil dikonfirmasi.\n\n' +
                'Simulasi frontend: email PO akan dikirim ke ' +
                supplierEmail.value
            );


            closeModal();

            restockForm.reset();

            supplierEmail.value = '';

        }
    );



    /*
    |--------------------------------------------------------------------------
    | Initial State
    |--------------------------------------------------------------------------
    */

    filterRestock();
    filterPurchaseOrders();

});
</script>

@endpush