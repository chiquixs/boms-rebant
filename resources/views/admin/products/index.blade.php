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
                        <th>SKU Product</th>
                        <th>Varian</th>
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
                            <button
                                type="button"
                                class="variant-count-button"
                                id="toggleVariantProduct1"
                            >
                                5 Varian
                            </button>
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
                                    class="product-action edit edit-product-button"
                                    title="Edit produk"
                                    data-sku="WD-HEX-001"
                                    data-name="Hexagon Wall Decor Set of 3"
                                    data-category="wall-decor"
                                    data-price="150000"
                                    data-length="30"
                                    data-width="20"
                                    data-method="mts"
                                    data-image="{{ asset('images/login-illustration.png') }}"
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


                    {{-- PRODUCT 1 VARIANTS --}}
                    <tr
                        class="variant-detail-row"
                        id="variantRowProduct1"
                        style="display: none;"
                    >

                        <td colspan="9">

                            <div class="variant-detail-content">

                                <div class="variant-detail-header">
                                    <div>
                                        <strong>Varian Produk</strong>
                                        <span>5 varian tersedia</span>
                                    </div>
                                </div>

                                <div class="variant-list">

                                    <div class="variant-item">
                                        <span class="sku-code">D6A-1</span>
                                        <strong>CCTV</strong>
                                        <span>Stock: 50</span>
                                        <span>Min: 30</span>
                                        <span>Target: 80</span>
                                    </div>

                                    <div class="variant-item">
                                        <span class="sku-code">D6A-2</span>
                                        <strong>Dilarang Merokok</strong>
                                        <span>Stock: 75</span>
                                        <span>Min: 30</span>
                                        <span>Target: 80</span>
                                    </div>

                                    <div class="variant-item">
                                        <span class="sku-code">D6A-3</span>
                                        <strong>Area Merokok</strong>
                                        <span>Stock: 30</span>
                                        <span>Min: 30</span>
                                        <span>Target: 80</span>
                                    </div>

                                    <div class="variant-item">
                                        <span class="sku-code">D6A-4</span>
                                        <strong>Arah Kiblat</strong>
                                        <span>Stock: 34</span>
                                        <span>Min: 30</span>
                                        <span>Target: 80</span>
                                    </div>

                                    <div class="variant-item">
                                        <span class="sku-code">D6A-5</span>
                                        <strong>Jagalah Kebersihan</strong>
                                        <span>Stock: 20</span>
                                        <span>Min: 30</span>
                                        <span>Target: 80</span>
                                    </div>

                                </div>

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
                            <span class="stock-muted">
                                —
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
                            <span class="stock-muted">
                                —
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
                            <span class="stock-muted">
                                —
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
                            <span class="stock-muted">
                                —
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

                        <div class="product-form-group">

                            <label for="productSku">
                                SKU Product
                            </label>

                            <input
                                type="text"
                                id="productSku"
                                placeholder="Contoh: D6A"
                            >

                        </div>


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


                        <div class="product-form-group">

                            <label for="productPrice">
                                Harga Jual / Unit
                            </label>

                            <div class="price-input-wrapper">

                                <span>Rp</span>

                                <input
                                    type="number"
                                    id="productPrice"
                                    min="0"
                                    placeholder="0"
                                >

                            </div>

                        </div>


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

                                <span>cm</span>

                            </div>

                        </div>


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

                                <span>cm</span>

                            </div>

                        </div>

                    </div>


                    {{-- PRODUCT IMAGE --}}
                    <div class="product-form-group product-image-group">

                        <label for="productImage">
                            Foto Produk
                        </label>

                        <label
                            for="productImage"
                            class="product-image-upload"
                            id="productImageUpload"
                        >

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

                </div>


                {{-- =========================
                    PRODUCT VARIANTS
                ========================= --}}
                <div class="product-form-section">

                    <div class="product-form-section-title">

                        <h3>
                            Varian Produk
                        </h3>

                        <p>
                            Tambahkan SKU dan informasi stok
                            untuk setiap varian produk.
                        </p>

                    </div>


                    <div id="variantFormsContainer">

                        {{-- VARIANT 1 --}}
                        <div class="product-variant-form">

                            <div class="product-variant-form-header">

                                <div>
                                    <strong>
                                        Varian 1
                                    </strong>

                                    <span>
                                        Varian utama produk
                                    </span>
                                </div>

                            </div>


                            <div class="variant-form-grid">

                                <div class="product-form-group">

                                    <label for="variantSku">
                                        SKU Variant
                                    </label>

                                    <input
                                        type="text"
                                        id="variantSku"
                                        name="variantSku[]"
                                        placeholder="Contoh: D6A-1"
                                    >

                                </div>


                                <div class="product-form-group">

                                    <label for="variantName">
                                        Nama Variant
                                    </label>

                                    <input
                                        type="text"
                                        id="variantName"
                                        name="variantName[]"
                                        placeholder="Contoh: CCTV"
                                    >

                                </div>


                                <div class="product-form-group variant-stock-field">

                                    <label for="variantCurrentStock">
                                        Current Stock
                                    </label>

                                    <div class="stock-input-wrapper">

                                        <input
                                            type="number"
                                            id="variantCurrentStock"
                                            name="variantCurrentStock[]"
                                            min="0"
                                            placeholder="0"
                                        >

                                        <span>pcs</span>

                                    </div>

                                </div>


                                <div class="product-form-group variant-stock-field">

                                    <label for="variantMinimumStock">
                                        Minimum Stock
                                    </label>

                                    <div class="stock-input-wrapper">

                                        <input
                                            type="number"
                                            id="variantMinimumStock"
                                            name="variantMinimumStock[]"
                                            min="0"
                                            placeholder="30"
                                        >

                                        <span>pcs</span>

                                    </div>

                                </div>


                                <div class="product-form-group variant-stock-field">

                                    <label for="variantTargetStock">
                                        Target Stock
                                    </label>

                                    <div class="stock-input-wrapper">

                                        <input
                                            type="number"
                                            id="variantTargetStock"
                                            name="variantTargetStock[]"
                                            min="0"
                                            placeholder="80"
                                        >

                                        <span>pcs</span>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    <button
                        type="button"
                        class="add-variant-button"
                        id="addVariantButton"
                    >
                        + Tambah Varian
                    </button>

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
                    id="productSaveButton"
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

    const productModalTitle =
        document.getElementById('productModalTitle');

    const productModalDescription =
        document.getElementById('productModalDescription');

    const productSaveButton =
        document.getElementById('productSaveButton');

    const editProductButtons =
        document.querySelectorAll('.edit-product-button');


    /* FORM INPUTS */

    const productSku =
        document.getElementById('productSku');

    const productName =
        document.getElementById('productName');

    const productCategory =
        document.getElementById('productCategory');

    const productPrice =
        document.getElementById('productPrice');

    const productLength =
        document.getElementById('productLength');

    const productWidth =
        document.getElementById('productWidth');


    /* PRODUCTION METHOD */

    const productionMethods =
        document.querySelectorAll(
            'input[name="productionMethod"]'
        );

    const productionCards =
        document.querySelectorAll(
            '.production-method-card'
        );


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


    /* VARIANTS */

    const variantFormsContainer =
        document.getElementById('variantFormsContainer');

    const addVariantButton =
        document.getElementById('addVariantButton');

    let variantCount = 1;


    /* =========================================================
       RESET PRODUCT FORM
    ========================================================= */

    function resetProductForm() {

        productForm.reset();

        productModalTitle.textContent =
            'Tambah Produk Baru';

        productModalDescription.textContent =
            'Tambahkan informasi produk dan metode produksinya.';

        productSaveButton.textContent =
            'Simpan Produk';

            /* RESET DYNAMIC VARIANTS */

const variantCards =
    variantFormsContainer.querySelectorAll(
        '.product-variant-form'
    );

variantCards.forEach(function (card, index) {

    if (index > 0) {
        card.remove();
    }

});

variantCount = 1;


        /* RESET IMAGE */

        productImage.value = '';

        productPreviewImage.src = '';

        productImageName.textContent = '';

        productImageDefault
            .classList.remove('hidden');

        productImagePreview
            .classList.remove('active');


        /* RESET PRODUCTION METHOD */

        productionCards.forEach(function (card) {

            card.classList.remove('active');

        });

        const mtsRadio =
            document.querySelector(
                'input[name="productionMethod"][value="mts"]'
            );

        mtsRadio.checked = true;

        mtsRadio
            .closest('.production-method-card')
            .classList.add('active');

    }


    /* =========================================================
       OPEN EDIT PRODUCT
    ========================================================= */

    function openEditModal(button) {

        resetProductForm();


        /* MODAL MODE */

        productModalTitle.textContent =
            'Edit Produk';

        productModalDescription.textContent =
            'Perbarui informasi dan pengaturan produk.';

        productSaveButton.textContent =
            'Simpan Perubahan';


        /* BASIC PRODUCT DATA */

        productSku.value =
            button.dataset.sku;

        productName.value =
            button.dataset.name;

        productCategory.value =
            button.dataset.category;

        productPrice.value =
            button.dataset.price;

        productLength.value =
            button.dataset.length;

        productWidth.value =
            button.dataset.width;


        /* PRODUCT IMAGE */

        if (button.dataset.image) {

            productPreviewImage.src =
                button.dataset.image;

            productImageName.textContent =
                'Foto produk saat ini';

            productImageDefault
                .classList.add('hidden');

            productImagePreview
                .classList.add('active');

        }


        /* PRODUCTION METHOD */

        productionCards.forEach(function (card) {

            card.classList.remove('active');

        });

        const selectedMethod =
            document.querySelector(
                'input[name="productionMethod"][value="' +
                button.dataset.method +
                '"]'
            );

        if (selectedMethod) {

    selectedMethod.checked = true;

    selectedMethod
        .closest('.production-method-card')
        .classList.add('active');

    updateVariantStockVisibility(
        selectedMethod.value
    );

}


        /* OPEN MODAL */

        productModal
            .classList.add('active');

        document.body.style.overflow =
            'hidden';

    }


    editProductButtons.forEach(function (button) {

        button.addEventListener(
            'click',
            function () {

                openEditModal(button);

            }
        );

    });


    /* =========================================================
       OPEN ADD PRODUCT MODAL
    ========================================================= */

    function openModal() {

        resetProductForm();

        productModal
            .classList.add('active');

        document.body.style.overflow =
            'hidden';

    }


    /* =========================================================
       CLOSE MODAL
    ========================================================= */

    function closeModal() {

        productModal
            .classList.remove('active');

        document.body.style.overflow =
            '';

        resetProductForm();

    }


    /* =========================================================
       MODAL EVENTS
    ========================================================= */

    openProductModal.addEventListener(
        'click',
        openModal
    );

    closeProductModal.addEventListener(
        'click',
        closeModal
    );

    cancelProductModal.addEventListener(
        'click',
        closeModal
    );


    productModal.addEventListener(
        'click',
        function (event) {

            if (event.target === productModal) {

                closeModal();

            }

        }
    );


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

            if (!file) {

                return;

            }

            if (!file.type.startsWith('image/')) {

                productImage.value = '';

                return;

            }

            const imageUrl =
                URL.createObjectURL(file);

            productPreviewImage.src =
                imageUrl;

            productImageName.textContent =
                file.name;

            productImageDefault
                .classList.add('hidden');

            productImagePreview
                .classList.add('active');

        }
    );

    function updateVariantStockVisibility(method) {

    const variantStockFields =
        document.querySelectorAll(
            '.variant-stock-field'
        );

    variantStockFields.forEach(function (field) {

        const input =
            field.querySelector('input');

        if (method === 'mto') {

            field.style.display = 'none';

            if (input) {
                input.disabled = true;
            }

        } else {

            field.style.display = '';

            if (input) {
                input.disabled = false;
            }

        }

    });

}


    /* =========================================================
       MTS / MTO
    ========================================================= */

    productionMethods.forEach(function (radio) {

        radio.addEventListener(
            'change',
            function () {

                productionCards.forEach(
                    function (card) {

                        card.classList.remove('active');

                    }
                );

                radio
                    .closest('.production-method-card')
                    .classList.add('active');

                    updateVariantStockVisibility(
    radio.value
);

            }
        );

    });


    /* =========================================================
       PRODUCT VARIANT TABLE TOGGLE
    ========================================================= */

    const toggleVariantProduct1 =
        document.getElementById('toggleVariantProduct1');

    const variantRowProduct1 =
        document.getElementById('variantRowProduct1');

    if (
        toggleVariantProduct1 &&
        variantRowProduct1
    ) {

        toggleVariantProduct1.addEventListener(
            'click',
            function () {

                const isHidden =
                    variantRowProduct1.style.display === 'none';

                variantRowProduct1.style.display =
                    isHidden
                        ? 'table-row'
                        : 'none';

            }
        );

    }


    /* =========================================================
       DYNAMIC PRODUCT VARIANTS
    ========================================================= */

    addVariantButton.addEventListener(
        'click',
        function () {

            variantCount++;

            const variantCard =
                document.createElement('div');

            variantCard.className =
                'product-variant-form';

            variantCard.innerHTML = `
                <div class="product-variant-form-header">

                    <div>
                        <strong>
                            Varian ${variantCount}
                        </strong>

                        <span>
                            Varian tambahan produk
                        </span>
                    </div>

                    <button
                        type="button"
                        class="remove-variant-button"
                    >
                        Hapus
                    </button>

                </div>


                <div class="variant-form-grid">

                    <div class="product-form-group">

                        <label>
                            SKU Variant
                        </label>

                        <input
                            type="text"
                            name="variantSku[]"
                            placeholder="Contoh: D6A-${variantCount}"
                        >

                    </div>


                    <div class="product-form-group">

                        <label>
                            Nama Variant
                        </label>

                        <input
                            type="text"
                            name="variantName[]"
                            placeholder="Masukkan nama varian"
                        >

                    </div>


                    <div class="product-form-group variant-stock-field">

                        <label>
                            Current Stock
                        </label>

                        <div class="stock-input-wrapper">

                            <input
                                type="number"
                                name="variantCurrentStock[]"
                                min="0"
                                placeholder="0"
                            >

                            <span>pcs</span>

                        </div>

                    </div>


                    <div class="product-form-group variant-stock-field">

                        <label>
                            Minimum Stock
                        </label>

                        <div class="stock-input-wrapper">

                            <input
                                type="number"
                                name="variantMinimumStock[]"
                                min="0"
                                placeholder="30"
                            >

                            <span>pcs</span>

                        </div>

                    </div>


                    <div class="product-form-group variant-stock-field">

                        <label>
                            Target Stock
                        </label>

                        <div class="stock-input-wrapper">

                            <input
                                type="number"
                                name="variantTargetStock[]"
                                min="0"
                                placeholder="80"
                            >

                            <span>pcs</span>

                        </div>

                    </div>

                </div>
            `;


            variantFormsContainer
                .appendChild(variantCard);

                const selectedProductionMethod =
    document.querySelector(
        'input[name="productionMethod"]:checked'
    );

if (selectedProductionMethod) {

    updateVariantStockVisibility(
        selectedProductionMethod.value
    );

}


            /* REMOVE VARIANT */

            const removeButton =
                variantCard.querySelector(
                    '.remove-variant-button'
                );

            removeButton.addEventListener(
                'click',
                function () {

                    variantCard.remove();

                }
            );

        }
    );

});
</script>

@endpush