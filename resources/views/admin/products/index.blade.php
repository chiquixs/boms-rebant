@extends('layouts.admin')

@section('title', 'Master Produk & SKU - REBANT BOMS')

@section('content')

<div class="products-page">

    {{-- =========================
        PAGE HEADER
    ========================= --}}
    <div class="page-header">

        <div>
            <span class="page-eyebrow">
                PRODUCT MANAGEMENT
            </span>

            <h1>Master Data Produk & SKU</h1>

            <p>
                Kelola katalog produk, stok, metode produksi,
                dan resep bahan baku.
            </p>
        </div>

        <button
            type="button"
            class="add-product-button"
        >
            <span>+</span>
            Tambah Produk Baru
        </button>

    </div>


    {{-- =========================
        SEARCH & FILTER
    ========================= --}}
    <div class="product-filter-card">

        <div class="product-search">

            <svg
                width="15"
                height="15"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >
                <circle cx="11" cy="11" r="7"/>
                <path d="m20 20-3.5-3.5"/>
            </svg>

            <input
                type="text"
                placeholder="Cari berdasarkan Nama Produk atau SKU..."
            >

        </div>


        <select class="product-filter-select">
            <option value="">Semua Kategori</option>
            <option value="wall-decor">Wall Decor</option>
            <option value="rak-dinding">Rak Dinding</option>
            <option value="vas-bunga">Vas Bunga</option>
        </select>


        <select class="product-filter-select">
            <option value="">Semua Tipe</option>
            <option value="mts">MTS — Make to Stock</option>
            <option value="mto">MTO — Make to Order</option>
        </select>


        <select class="product-filter-select">
            <option value="">Semua Status</option>
            <option value="safe">Aman</option>
            <option value="low">Low Stock</option>
            <option value="mto">MTO / On Demand</option>
        </select>

    </div>


    {{-- =========================
        PRODUCT TABLE
    ========================= --}}
    <div class="product-table-card">

        <div class="product-table-header">

            <div>
                <h2>Daftar Produk</h2>
                <p>
                    Kelola seluruh produk dan SKU yang tersedia.
                </p>
            </div>

            <span class="product-count">
                5 Produk
            </span>

        </div>


        <div class="product-table-wrapper">

            <table class="product-table">

                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>SKU</th>
                        <th>Kategori</th>
                        <th>Tipe</th>
                        <th>Stok Saat Ini</th>
                        <th>Min & Target</th>
                        <th>Status Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    {{-- =========================
                        PRODUCT 1
                    ========================= --}}
                    <tr>

                        <td>
                            <div class="product-info">

                                <img
                                    src="{{ asset('images/login-illustration.png') }}"
                                    alt="Hexagon Wall Decor"
                                >

                                <div>
                                    <strong>
                                        Hexagon Wall Decor Set of 3
                                    </strong>

                                    <span>
                                        Rp150.000
                                    </span>
                                </div>

                            </div>
                        </td>


                        <td>
                            <span class="sku-code">
                                WD-HEX-001
                            </span>
                        </td>


                        <td>
                            Wall Decor
                        </td>


                        <td>
                            <span class="production-badge mts">
                                MTS
                            </span>
                        </td>


                        <td>
                            <strong class="stock-value">
                                32 pcs
                            </strong>
                        </td>


                        <td>
                            <div class="stock-target">
                                <span>Min: 10</span>
                                <span>Target: 50</span>
                            </div>
                        </td>


                        <td>
                            <span class="stock-status safe">
                                <span></span>
                                Aman
                            </span>
                        </td>


                        <td>
                            <div class="product-actions">

                                <button
                                    type="button"
                                    class="product-action bom"
                                    title="Resep BOM"
                                >
                                    BOM
                                </button>

                                <button
                                    type="button"
                                    class="product-action edit"
                                    title="Edit produk"
                                >
                                    Edit
                                </button>

                                <button
                                    type="button"
                                    class="product-action delete"
                                    title="Hapus produk"
                                >
                                    <svg
                                        width="13"
                                        height="13"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M3 6h18"/>
                                        <path d="M8 6V4h8v2"/>
                                        <path d="M19 6l-1 14H6L5 6"/>
                                        <path d="M10 11v5"/>
                                        <path d="M14 11v5"/>
                                    </svg>
                                </button>

                            </div>
                        </td>

                    </tr>


                    {{-- =========================
                        PRODUCT 2
                    ========================= --}}
                    <tr>

                        <td>
                            <div class="product-info">

                                <img
                                    src="{{ asset('images/login-illustration.png') }}"
                                    alt="Minimalist Wall Shelf"
                                >

                                <div>
                                    <strong>
                                        Minimalist Wall Shelf
                                    </strong>

                                    <span>
                                        Rp125.000
                                    </span>
                                </div>

                            </div>
                        </td>


                        <td>
                            <span class="sku-code">
                                RAK-MIN-002
                            </span>
                        </td>


                        <td>
                            Rak Dinding
                        </td>


                        <td>
                            <span class="production-badge mts">
                                MTS
                            </span>
                        </td>


                        <td>
                            <strong class="stock-value low">
                                8 pcs
                            </strong>
                        </td>


                        <td>
                            <div class="stock-target">
                                <span>Min: 10</span>
                                <span>Target: 40</span>
                            </div>
                        </td>


                        <td>
                            <span class="stock-status low">
                                <span></span>
                                Low Stock
                            </span>
                        </td>


                        <td>
                            <div class="product-actions">

                                <button
                                    type="button"
                                    class="product-action bom"
                                    title="Resep BOM"
                                >
                                    BOM
                                </button>

                                <button
                                    type="button"
                                    class="product-action edit"
                                    title="Edit produk"
                                >
                                    Edit
                                </button>

                                <button
                                    type="button"
                                    class="product-action delete"
                                    title="Hapus produk"
                                >
                                    <svg
                                        width="13"
                                        height="13"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M3 6h18"/>
                                        <path d="M8 6V4h8v2"/>
                                        <path d="M19 6l-1 14H6L5 6"/>
                                        <path d="M10 11v5"/>
                                        <path d="M14 11v5"/>
                                    </svg>
                                </button>

                            </div>
                        </td>

                    </tr>


                    {{-- =========================
                        PRODUCT 3
                    ========================= --}}
                    <tr>

                        <td>
                            <div class="product-info">

                                <img
                                    src="{{ asset('images/login-illustration.png') }}"
                                    alt="Custom Wooden Name Decor"
                                >

                                <div>
                                    <strong>
                                        Custom Wooden Name Decor
                                    </strong>

                                    <span>
                                        Rp175.000
                                    </span>
                                </div>

                            </div>
                        </td>


                        <td>
                            <span class="sku-code">
                                WD-CUS-003
                            </span>
                        </td>


                        <td>
                            Wall Decor
                        </td>


                        <td>
                            <span class="production-badge mto">
                                MTO
                            </span>
                        </td>


                        <td>
                            <span class="stock-muted">
                                —
                            </span>
                        </td>


                        <td>
                            <span class="stock-muted">
                                Custom
                            </span>
                        </td>


                        <td>
                            <span class="stock-status ondemand">
                                <span></span>
                                On Demand
                            </span>
                        </td>


                        <td>
                            <div class="product-actions">

                                <button
                                    type="button"
                                    class="product-action bom"
                                    title="Resep BOM"
                                >
                                    BOM
                                </button>

                                <button
                                    type="button"
                                    class="product-action edit"
                                    title="Edit produk"
                                >
                                    Edit
                                </button>

                                <button
                                    type="button"
                                    class="product-action delete"
                                    title="Hapus produk"
                                >
                                    <svg
                                        width="13"
                                        height="13"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M3 6h18"/>
                                        <path d="M8 6V4h8v2"/>
                                        <path d="M19 6l-1 14H6L5 6"/>
                                        <path d="M10 11v5"/>
                                        <path d="M14 11v5"/>
                                    </svg>
                                </button>

                            </div>
                        </td>

                    </tr>


                    {{-- =========================
                        PRODUCT 4
                    ========================= --}}
                    <tr>

                        <td>
                            <div class="product-info">

                                <img
                                    src="{{ asset('images/login-illustration.png') }}"
                                    alt="Nordic Flower Vase"
                                >

                                <div>
                                    <strong>
                                        Nordic Flower Vase
                                    </strong>

                                    <span>
                                        Rp95.000
                                    </span>
                                </div>

                            </div>
                        </td>


                        <td>
                            <span class="sku-code">
                                VAS-NOR-004
                            </span>
                        </td>


                        <td>
                            Vas Bunga
                        </td>


                        <td>
                            <span class="production-badge mts">
                                MTS
                            </span>
                        </td>


                        <td>
                            <strong class="stock-value">
                                24 pcs
                            </strong>
                        </td>


                        <td>
                            <div class="stock-target">
                                <span>Min: 8</span>
                                <span>Target: 30</span>
                            </div>
                        </td>


                        <td>
                            <span class="stock-status safe">
                                <span></span>
                                Aman
                            </span>
                        </td>


                        <td>
                            <div class="product-actions">

                                <button
                                    type="button"
                                    class="product-action bom"
                                    title="Resep BOM"
                                >
                                    BOM
                                </button>

                                <button
                                    type="button"
                                    class="product-action edit"
                                    title="Edit produk"
                                >
                                    Edit
                                </button>

                                <button
                                    type="button"
                                    class="product-action delete"
                                    title="Hapus produk"
                                >
                                    <svg
                                        width="13"
                                        height="13"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M3 6h18"/>
                                        <path d="M8 6V4h8v2"/>
                                        <path d="M19 6l-1 14H6L5 6"/>
                                        <path d="M10 11v5"/>
                                        <path d="M14 11v5"/>
                                    </svg>
                                </button>

                            </div>
                        </td>

                    </tr>


                    {{-- =========================
                        PRODUCT 5
                    ========================= --}}
                    <tr>

                        <td>
                            <div class="product-info">

                                <img
                                    src="{{ asset('images/login-illustration.png') }}"
                                    alt="Floating Wooden Shelf"
                                >

                                <div>
                                    <strong>
                                        Floating Wooden Shelf
                                    </strong>

                                    <span>
                                        Rp135.000
                                    </span>
                                </div>

                            </div>
                        </td>


                        <td>
                            <span class="sku-code">
                                RAK-FLT-005
                            </span>
                        </td>


                        <td>
                            Rak Dinding
                        </td>


                        <td>
                            <span class="production-badge mts">
                                MTS
                            </span>
                        </td>


                        <td>
                            <strong class="stock-value low">
                                5 pcs
                            </strong>
                        </td>


                        <td>
                            <div class="stock-target">
                                <span>Min: 7</span>
                                <span>Target: 35</span>
                            </div>
                        </td>


                        <td>
                            <span class="stock-status low">
                                <span></span>
                                Low Stock
                            </span>
                        </td>


                        <td>
                            <div class="product-actions">

                                <button
                                    type="button"
                                    class="product-action bom"
                                    title="Resep BOM"
                                >
                                    BOM
                                </button>

                                <button
                                    type="button"
                                    class="product-action edit"
                                    title="Edit produk"
                                >
                                    Edit
                                </button>

                                <button
                                    type="button"
                                    class="product-action delete"
                                    title="Hapus produk"
                                >
                                    <svg
                                        width="13"
                                        height="13"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M3 6h18"/>
                                        <path d="M8 6V4h8v2"/>
                                        <path d="M19 6l-1 14H6L5 6"/>
                                        <path d="M10 11v5"/>
                                        <path d="M14 11v5"/>
                                    </svg>
                                </button>

                            </div>
                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- =========================================================
    ADD / EDIT PRODUCT MODAL
========================================================= --}}
<div
    class="product-modal-overlay"
    id="productModal"
>

    <div class="product-modal">


        {{-- =========================
            MODAL HEADER
        ========================= --}}
        <div class="product-modal-header">

            <div>

                <span class="product-modal-eyebrow">
                    PRODUCT MASTER
                </span>

                <h2 id="productModalTitle">
                    Tambah Produk Baru
                </h2>

                <p id="productModalDescription">
                    Tambahkan informasi produk dan metode produksinya.
                </p>

            </div>


            <button
                type="button"
                class="product-modal-close"
                id="closeProductModal"
                aria-label="Tutup modal"
            >
                ×
            </button>

        </div>


        {{-- =========================
            PRODUCT FORM
        ========================= --}}
        <form
            id="productForm"
            onsubmit="return false;"
        >

            <div class="product-modal-body">


                {{-- =========================
                    BASIC INFORMATION
                ========================= --}}
                <div class="product-form-section">

                    <div class="product-form-section-title">

                        <h3>
                            Informasi Dasar
                        </h3>

                        <p>
                            Informasi utama produk yang akan dijual.
                        </p>

                    </div>


                    <div class="product-form-grid">


                        {{-- SKU --}}
                        <div class="product-form-group">

                            <label for="productSku">
                                Kode SKU
                            </label>

                            <input
                                type="text"
                                id="productSku"
                                placeholder="Contoh: WD-HEX-001"
                            >

                        </div>


                        {{-- PRODUCT NAME --}}
                        <div class="product-form-group">

                            <label for="productName">
                                Nama Produk
                            </label>

                            <input
                                type="text"
                                id="productName"
                                placeholder="Masukkan nama produk"
                            >

                        </div>


                        {{-- CATEGORY --}}
                        <div class="product-form-group">

                            <label for="productCategory">
                                Kategori Produk
                            </label>

                            <select id="productCategory">

                                <option value="">
                                    Pilih kategori
                                </option>

                                <option value="wall-decor">
                                    Wall Decor
                                </option>

                                <option value="rak-dinding">
                                    Rak Dinding
                                </option>

                                <option value="vas-bunga">
                                    Vas Bunga
                                </option>

                            </select>

                        </div>


                        {{-- PRICE --}}
                        <div class="product-form-group">

                            <label for="productPrice">
                                Harga Jual / Unit
                            </label>

                            <div class="price-input-wrapper">

                                <span>
                                    Rp
                                </span>

                                <input
                                    type="number"
                                    id="productPrice"
                                    min="0"
                                    placeholder="0"
                                >

                            </div>

                        </div>


                        {{-- LENGTH --}}
                        <div class="product-form-group">

                            <label for="productLength">
                                Panjang Produk
                            </label>

                            <div class="stock-input-wrapper">

                                <input
                                    type="number"
                                    id="productLength"
                                    min="0"
                                    step="0.01"
                                    placeholder="Contoh: 30"
                                >

                                <span>
                                    cm
                                </span>

                            </div>

                        </div>


                        {{-- WIDTH --}}
                        <div class="product-form-group">

                            <label for="productWidth">
                                Lebar Produk
                            </label>

                            <div class="stock-input-wrapper">

                                <input
                                    type="number"
                                    id="productWidth"
                                    min="0"
                                    step="0.01"
                                    placeholder="Contoh: 20"
                                >

                                <span>
                                    cm
                                </span>

                            </div>

                        </div>

                    </div>


                    {{-- =========================
                        PRODUCT IMAGE
                    ========================= --}}
                    <div class="product-form-group product-image-group">

                        <label for="productImage">
                            Foto Produk
                        </label>


                        <label
                            for="productImage"
                            class="product-image-upload"
                            id="productImageUpload"
                        >


                            {{-- DEFAULT UPLOAD --}}
                            <div
                                class="product-image-default"
                                id="productImageDefault"
                            >

                                <div class="product-upload-icon">

                                    <svg
                                        width="18"
                                        height="18"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M12 16V4"/>
                                        <path d="m7 9 5-5 5 5"/>
                                        <path d="M5 20h14"/>
                                    </svg>

                                </div>


                                <div class="product-upload-text">

                                    <strong>
                                        Upload foto produk
                                    </strong>

                                    <span>
                                        Klik untuk memilih gambar produk
                                    </span>

                                </div>

                            </div>


                            {{-- IMAGE PREVIEW --}}
                            <div
                                class="product-image-preview"
                                id="productImagePreview"
                            >

                                <img
                                    src=""
                                    alt="Preview foto produk"
                                    id="productPreviewImage"
                                >


                                <div class="product-preview-info">

                                    <strong id="productImageName">
                                        product-image.jpg
                                    </strong>

                                    <span>
                                        Klik untuk mengganti foto
                                    </span>

                                </div>


                                <div class="product-preview-change">

                                    <svg
                                        width="15"
                                        height="15"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M21 12a9 9 0 1 1-2.64-6.36"/>
                                        <path d="M21 3v6h-6"/>
                                    </svg>

                                </div>

                            </div>

                        </label>


                        <input
                            type="file"
                            id="productImage"
                            accept="image/*"
                            hidden
                        >

                    </div>

                </div>


                {{-- =========================
                    PRODUCTION METHOD
                ========================= --}}
                <div class="product-form-section">

                    <div class="product-form-section-title">

                        <h3>
                            Metode Produksi
                        </h3>

                        <p>
                            Tentukan bagaimana produk diproduksi.
                        </p>

                    </div>


                    <div class="production-method-options">


                        {{-- MTS --}}
                        <label class="production-method-card active">

                            <input
                                type="radio"
                                name="productionMethod"
                                value="mts"
                                checked
                            >

                            <div>

                                <strong>
                                    Make-to-Stock (MTS)
                                </strong>

                                <span>
                                    Produk disimpan sebagai stok barang jadi.
                                </span>

                            </div>

                        </label>


                        {{-- MTO --}}
                        <label class="production-method-card">

                            <input
                                type="radio"
                                name="productionMethod"
                                value="mto"
                            >

                            <div>

                                <strong>
                                    Make-to-Order (MTO)
                                </strong>

                                <span>
                                    Produk dibuat saat ada pesanan masuk.
                                </span>

                            </div>

                        </label>

                    </div>


                    {{-- =========================
                        MTS STOCK SETTINGS
                    ========================= --}}
                    <div
                        class="mts-stock-settings"
                        id="mtsStockSettings"
                    >


                        {{-- CURRENT STOCK --}}
                        <div class="product-form-group">

                            <label for="currentStock">
                                Current Stock
                            </label>

                            <div class="stock-input-wrapper">

                                <input
                                    type="number"
                                    id="currentStock"
                                    min="0"
                                    placeholder="0"
                                >

                                <span>
                                    pcs
                                </span>

                            </div>

                        </div>


                        {{-- MINIMUM STOCK --}}
                        <div class="product-form-group">

                            <label for="minimumStock">
                                Minimum Stock Alert Level
                            </label>

                            <div class="stock-input-wrapper">

                                <input
                                    type="number"
                                    id="minimumStock"
                                    min="0"
                                    placeholder="10"
                                >

                                <span>
                                    pcs
                                </span>

                            </div>

                        </div>


                        {{-- TARGET STOCK --}}
                        <div class="product-form-group">

                            <label for="targetStock">
                                Target Buffer Stock
                            </label>

                            <div class="stock-input-wrapper">

                                <input
                                    type="number"
                                    id="targetStock"
                                    min="0"
                                    placeholder="50"
                                >

                                <span>
                                    pcs
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- =========================
                MODAL FOOTER
            ========================= --}}
            <div class="product-modal-footer">

                <button
                    type="button"
                    class="product-cancel-button"
                    id="cancelProductModal"
                >
                    Batal
                </button>


                <button
                    type="submit"
                    class="product-save-button"
                >
                    Simpan Produk
                </button>

            </div>

        </form>

    </div>

</div>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* =========================================================
       ELEMENTS
    ========================================================= */

    const productModal =
        document.getElementById('productModal');

    const productForm =
        document.getElementById('productForm');

    const openProductModal =
        document.querySelector('.add-product-button');

    const closeProductModal =
        document.getElementById('closeProductModal');

    const cancelProductModal =
        document.getElementById('cancelProductModal');


    /* PRODUCTION METHOD */

    const productionMethods =
        document.querySelectorAll(
            'input[name="productionMethod"]'
        );

    const productionCards =
        document.querySelectorAll(
            '.production-method-card'
        );

    const mtsStockSettings =
        document.getElementById('mtsStockSettings');

    const currentStock =
        document.getElementById('currentStock');

    const minimumStock =
        document.getElementById('minimumStock');

    const targetStock =
        document.getElementById('targetStock');


    /* PRODUCT IMAGE */

    const productImage =
        document.getElementById('productImage');

    const productImageDefault =
        document.getElementById('productImageDefault');

    const productImagePreview =
        document.getElementById('productImagePreview');

    const productPreviewImage =
        document.getElementById('productPreviewImage');

    const productImageName =
        document.getElementById('productImageName');


    /* =========================================================
       RESET PRODUCT FORM
    ========================================================= */

    function resetProductForm() {

        /* RESET ALL FORM INPUTS */

        productForm.reset();


        /* RESET IMAGE INPUT */

        productImage.value = '';

        productPreviewImage.src = '';

        productImageName.textContent = '';


        /* SHOW DEFAULT IMAGE UPLOAD */

        productImageDefault
            .classList.remove('hidden');

        productImagePreview
            .classList.remove('active');


        /* RESET PRODUCTION CARD */

        productionCards.forEach(function (card) {

            card.classList.remove('active');

        });


        /* SET MTS AS DEFAULT */

        const mtsRadio =
            document.querySelector(
                'input[name="productionMethod"][value="mts"]'
            );

        mtsRadio.checked = true;

        mtsRadio
            .closest('.production-method-card')
            .classList.add('active');


        /* SHOW STOCK SETTINGS */

        mtsStockSettings
            .classList.remove('hidden');


        /* ENABLE MTS INPUTS */

        currentStock.disabled = false;

        minimumStock.disabled = false;

        targetStock.disabled = false;

    }


    /* =========================================================
       OPEN MODAL
    ========================================================= */

    function openModal() {

        /*
         * Add Product selalu dimulai
         * dengan form yang fresh.
         */

        resetProductForm();

        productModal
            .classList.add('active');

        document.body.style.overflow = 'hidden';

    }


    /* =========================================================
       CLOSE MODAL
    ========================================================= */

    function closeModal() {

        productModal
            .classList.remove('active');

        document.body.style.overflow = '';

        /*
         * Setelah modal ditutup,
         * buang semua temporary input.
         */

        resetProductForm();

    }


    /* =========================================================
       OPEN BUTTON
    ========================================================= */

    openProductModal.addEventListener(
        'click',
        openModal
    );


    /* =========================================================
       CLOSE BUTTON
    ========================================================= */

    closeProductModal.addEventListener(
        'click',
        closeModal
    );


    /* =========================================================
       CANCEL BUTTON
    ========================================================= */

    cancelProductModal.addEventListener(
        'click',
        closeModal
    );


    /* =========================================================
       CLICK OUTSIDE MODAL
    ========================================================= */

    productModal.addEventListener(
        'click',
        function (event) {

            if (event.target === productModal) {

                closeModal();

            }

        }
    );


    /* =========================================================
       ESC KEY
    ========================================================= */

    document.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key === 'Escape' &&
                productModal.classList.contains('active')
            ) {

                closeModal();

            }

        }
    );


    /* =========================================================
       PRODUCT IMAGE PREVIEW
    ========================================================= */

    productImage.addEventListener(
        'change',
        function () {

            const file =
                productImage.files[0];


            /* NO FILE */

            if (!file) {

                return;

            }


            /* ONLY ALLOW IMAGE */

            if (!file.type.startsWith('image/')) {

                productImage.value = '';

                return;

            }


            /* CREATE LOCAL PREVIEW URL */

            const imageUrl =
                URL.createObjectURL(file);


            /* SHOW IMAGE */

            productPreviewImage.src =
                imageUrl;


            /* SHOW FILE NAME */

            productImageName.textContent =
                file.name;


            /* HIDE DEFAULT UPLOAD */

            productImageDefault
                .classList.add('hidden');


            /* SHOW PREVIEW */

            productImagePreview
                .classList.add('active');

        }
    );


    /* =========================================================
       MTS / MTO
    ========================================================= */

    productionMethods.forEach(function (radio) {

        radio.addEventListener(
            'change',
            function () {


                /* REMOVE ACTIVE FROM ALL CARDS */

                productionCards.forEach(
                    function (card) {

                        card.classList.remove('active');

                    }
                );


                /* ACTIVE SELECTED CARD */

                radio
                    .closest('.production-method-card')
                    .classList.add('active');


                /* =========================
                   MTS
                ========================= */

                if (radio.value === 'mts') {

                    mtsStockSettings
                        .classList.remove('hidden');

                    currentStock.disabled = false;

                    minimumStock.disabled = false;

                    targetStock.disabled = false;

                }


                /* =========================
                   MTO
                ========================= */

                else {

                    mtsStockSettings
                        .classList.add('hidden');

                    currentStock.disabled = true;

                    minimumStock.disabled = true;

                    targetStock.disabled = true;

                }

            }
        );

    });

});
</script>

@endpush