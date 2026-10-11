@extends('layouts.admin')

@section('title', 'Master Produk & SKU - REBANT BOMS')

@section('content')

<div class="products-page">

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

    {{-- SEARCH --}}
    <div class="product-search">

        <svg width="15" height="15"
             viewBox="0 0 24 24"
             fill="none"
             stroke="currentColor"
             stroke-width="2">
            <circle cx="11" cy="11" r="7"/>
            <path d="m20 20-3.5-3.5"/>
        </svg>

        <input
            type="text"
            placeholder="Cari berdasarkan Nama Produk atau SKU..."
        >

    </div>


    {{-- CATEGORY --}}
    <select class="product-filter-select">
        <option value="">Semua Kategori</option>
        <option value="wall-decor">Wall Decor</option>
        <option value="rak-dinding">Rak Dinding</option>
        <option value="vas-bunga">Vas Bunga</option>
    </select>


    {{-- PRODUCTION METHOD --}}
    <select class="product-filter-select">
        <option value="">Semua Tipe</option>
        <option value="mts">MTS — Make to Stock</option>
        <option value="mto">MTO — Make to Order</option>
    </select>


    {{-- STOCK STATUS --}}
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
            <p>Kelola seluruh produk dan SKU yang tersedia.</p>
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

                {{-- PRODUCT 1 --}}
                <tr>

                    <td>
                        <div class="product-info">

                            <img
                                src="{{ asset('images/login-illustration.png') }}"
                                alt="Hexagon Wall Decor"
                            >

                            <div>
                                <strong>Hexagon Wall Decor Set of 3</strong>
                                <span>Rp150.000</span>
                            </div>

                        </div>
                    </td>

                    <td>
                        <span class="sku-code">
                            WD-HEX-001
                        </span>
                    </td>

                    <td>Wall Decor</td>

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
                                <svg width="13" height="13"
                                     viewBox="0 0 24 24"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="1.8">
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


                {{-- PRODUCT 2 --}}
                <tr>

                    <td>
                        <div class="product-info">

                            <img
                                src="{{ asset('images/login-illustration.png') }}"
                                alt="Minimalist Wall Shelf"
                            >

                            <div>
                                <strong>Minimalist Wall Shelf</strong>
                                <span>Rp125.000</span>
                            </div>

                        </div>
                    </td>

                    <td>
                        <span class="sku-code">
                            RAK-MIN-002
                        </span>
                    </td>

                    <td>Rak Dinding</td>

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
                                <svg width="13" height="13"
                                     viewBox="0 0 24 24"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="1.8">
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


                {{-- PRODUCT 3 — MTO --}}
                <tr>

                    <td>
                        <div class="product-info">

                            <img
                                src="{{ asset('images/login-illustration.png') }}"
                                alt="Custom Wooden Name Decor"
                            >

                            <div>
                                <strong>Custom Wooden Name Decor</strong>
                                <span>Rp175.000</span>
                            </div>

                        </div>
                    </td>

                    <td>
                        <span class="sku-code">
                            WD-CUS-003
                        </span>
                    </td>

                    <td>Wall Decor</td>

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
                                <svg width="13" height="13"
                                     viewBox="0 0 24 24"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="1.8">
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


                {{-- PRODUCT 4 --}}
                <tr>

                    <td>
                        <div class="product-info">

                            <img
                                src="{{ asset('images/login-illustration.png') }}"
                                alt="Nordic Flower Vase"
                            >

                            <div>
                                <strong>Nordic Flower Vase</strong>
                                <span>Rp95.000</span>
                            </div>

                        </div>
                    </td>

                    <td>
                        <span class="sku-code">
                            VAS-NOR-004
                        </span>
                    </td>

                    <td>Vas Bunga</td>

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

                            <button type="button"
                                    class="product-action bom">
                                BOM
                            </button>

                            <button type="button"
                                    class="product-action edit">
                                Edit
                            </button>

                            <button type="button"
                                    class="product-action delete"
                                    title="Hapus produk">
                                <svg width="13" height="13"
                                     viewBox="0 0 24 24"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="1.8">
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


                {{-- PRODUCT 5 --}}
                <tr>

                    <td>
                        <div class="product-info">

                            <img
                                src="{{ asset('images/login-illustration.png') }}"
                                alt="Floating Wooden Shelf"
                            >

                            <div>
                                <strong>Floating Wooden Shelf</strong>
                                <span>Rp135.000</span>
                            </div>

                        </div>
                    </td>

                    <td>
                        <span class="sku-code">
                            RAK-FLT-005
                        </span>
                    </td>

                    <td>Rak Dinding</td>

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

                            <button type="button"
                                    class="product-action bom">
                                BOM
                            </button>

                            <button type="button"
                                    class="product-action edit">
                                Edit
                            </button>

                            <button type="button"
                                    class="product-action delete"
                                    title="Hapus produk">
                                <svg width="13" height="13"
                                     viewBox="0 0 24 24"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="1.8">
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

@endsection