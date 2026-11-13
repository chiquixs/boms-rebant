
@extends('layouts.owner')

@section('title', 'Master Data')

@push('styles')
    @vite('resources/css/owner-master-data.css')
@endpush

@section('content')
<div class="master-page">
    <div class="master-header">
        <div>
            <p class="master-eyebrow">BUSINESS MANAGEMENT</p>
            <h1>Master Data</h1>
            <p class="master-subtitle">
                Lihat dan pantau seluruh data utama bisnis REBANT.
            </p>
        </div>
        <div class="master-access">
            <span class="access-dot"></span>
            Read Only Access
        </div>
    </div>

    <div class="master-summary">
        <div class="summary-card">
            <div class="summary-icon green-icon">▦</div>
            <div>
                <p>Total Produk</p>
                <h3 id="summaryProducts">0</h3>
                <span>Data produk terdaftar</span>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-icon yellow-icon">◈</div>
            <div>
                <p>Kategori</p>
                <h3 id="summaryCategories">0</h3>
                <span>Kategori produk</span>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-icon blue-icon">▤</div>
            <div>
                <p>Bahan Baku</p>
                <h3 id="summaryMaterials">0</h3>
                <span>Jenis bahan terdaftar</span>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-icon purple-icon">♧</div>
            <div>
                <p>Supplier</p>
                <h3 id="summarySuppliers">0</h3>
                <span>Supplier terdaftar</span>
            </div>
        </div>
    </div>

    <div class="master-content">
        <div class="master-tabs" role="tablist">
            <button class="master-tab active" data-tab="products" type="button">
                <span>▦</span> Produk
            </button>
            <button class="master-tab" data-tab="categories" type="button">
                <span>◈</span> Kategori
            </button>
            <button class="master-tab" data-tab="materials" type="button">
                <span>▤</span> Bahan Baku
            </button>
            <button class="master-tab" data-tab="bom" type="button">
                <span>⌘</span> Bill of Materials
            </button>
            <button class="master-tab" data-tab="suppliers" type="button">
                <span>♧</span> Supplier
            </button>
        </div>

        <div class="master-panel">
            <div class="panel-heading">
                <div>
                    <h2 id="tableTitle">Data Produk</h2>
                    <p id="tableDescription">
                        Informasi seluruh produk yang terdaftar dalam sistem.
                    </p>
                </div>
                <div class="read-only-label">
                    <span>●</span> View Only
                </div>
            </div>

            <div class="master-toolbar">
                <div class="master-search">
                    <span>⌕</span>
                    <input
                        type="text"
                        id="masterSearch"
                        placeholder="Cari data..."
                    >
                </div>

                <select id="masterStatus" class="master-filter">
                    <option value="">Semua Status</option>
                    <option value="Aktif">Aktif</option>
                    <option value="Tidak Aktif">Tidak Aktif</option>
                    <option value="Tersedia">Tersedia</option>
                    <option value="Stok Rendah">Stok Rendah</option>
                </select>

                <button type="button" id="resetMasterFilter" class="reset-filter">
                    Reset Filter
                </button>
            </div>

            <div class="master-table-wrapper">
                <table class="master-table">
                    <thead id="masterTableHead"></thead>
                    <tbody id="masterTableBody"></tbody>
                </table>
            </div>

            <div class="master-table-footer">
                <p id="masterInfo">Menampilkan 0 data</p>
                <div class="master-pagination" id="masterPagination"></div>
            </div>
        </div>
    </div>

    <div class="master-note">
        <div class="note-icon">ⓘ</div>
        <div>
            <strong>Informasi Akses</strong>
            <p>
                Halaman ini hanya digunakan untuk melihat informasi master data.
                Perubahan data hanya dapat dilakukan melalui akun dengan hak akses
                yang sesuai.
            </p>
        </div>
    </div>
</div>

<div class="master-modal" id="masterDetailModal">
    <div class="master-modal-backdrop" data-close-modal></div>
    <div class="master-modal-card">
        <div class="modal-heading">
            <div>
                <span class="modal-eyebrow">MASTER DATA DETAIL</span>
                <h3 id="detailModalTitle">Detail Data</h3>
            </div>
            <button type="button" class="modal-close" data-close-modal>&times;</button>
        </div>

        <div id="detailModalContent" class="detail-content"></div>

        <div class="modal-footer">
            <button type="button" class="modal-done" data-close-modal>
                Tutup
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/owner-master-data.js')
@endpush
