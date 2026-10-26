@extends('layouts.produksi')

@section('title', 'Stock & Production Planning - REBANT BOMS')

@section('content')

{{-- =========================
    PAGE HEADER
========================= --}}

<div class="pq-page-header">

    <div class="pq-page-header-text">
        <h1>Stock & Production Planning</h1>
        <p>Pantau ketersediaan stok produk dan identifikasi kebutuhan produksi untuk memenuhi target persediaan.</p>
    </div>

    <button type="button" class="sp-primary" id="spCreate">
        <svg width="18" height="18" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 5v14"/>
            <path d="M5 12h14"/>
        </svg>
        <span>Buat Permintaan Produksi (MTS)</span>
    </button>

</div>


{{-- =========================
    SUMMARY
========================= --}}

<div class="pq-stats">

    <div class="pq-stat total">
        <div class="pq-stat-head">
            <div class="pq-stat-icon">
                <svg width="20" height="20" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 8 12 3 3 8v8l9 5 9-5V8Z"/>
                    <path d="m3 8 9 5 9-5"/>
                    <path d="M12 13v8"/>
                </svg>
            </div>
            <span class="pq-stat-label">Total Produk</span>
        </div>
        <strong class="pq-stat-value" id="spStatTotal">-</strong>
        <span class="sp-stat-note">Produk aktif di Master Produk & SKU</span>
    </div>

    <div class="pq-stat low">
        <div class="pq-stat-head">
            <div class="pq-stat-icon">
                <svg width="20" height="20" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 3 2 20h20L12 3Z"/>
                    <path d="M12 10v4"/>
                    <path d="M12 17h.01"/>
                </svg>
            </div>
            <span class="pq-stat-label">Stok di Bawah Minimum</span>
        </div>
        <strong class="pq-stat-value" id="spStatLow">-</strong>
        <span class="sp-stat-note">Stok saat ini &lt; stok minimum</span>
    </div>

    <div class="pq-stat waiting">
        <div class="pq-stat-head">
            <div class="pq-stat-icon">
                <svg width="20" height="20" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 3v18h18"/>
                    <path d="m7 15 4-4 3 3 5-6"/>
                </svg>
            </div>
            <span class="pq-stat-label">Perlu Diproduksi (MTS)</span>
        </div>
        <strong class="pq-stat-value" id="spStatNeed">-</strong>
        <span class="sp-stat-note">Rekomendasi produksi &gt; 0</span>
    </div>

    <div class="pq-stat progress">
        <div class="pq-stat-head">
            <div class="pq-stat-icon">
                <svg width="20" height="20" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 12a9 9 0 1 1-3-6.7"/>
                    <path d="M21 4v5h-5"/>
                </svg>
            </div>
            <span class="pq-stat-label">Permintaan Produksi Aktif</span>
        </div>
        <strong class="pq-stat-value" id="spStatActive">-</strong>
        <span class="sp-stat-note">Status WAITING atau IN_PROCESS</span>
    </div>

</div>


{{-- =========================
    TAB
========================= --}}

<div class="sp-tabs" id="spTabs" role="tablist" aria-label="Kelompok stok">
    <button type="button" class="sp-tab active" data-tab="ALL" role="tab">Semua Produk <span id="spTabAll">0</span></button>
    <button type="button" class="sp-tab" data-tab="SAFE" role="tab">Stok Aman <span id="spTabSafe">0</span></button>
    <button type="button" class="sp-tab" data-tab="LOW" role="tab">Stok Rendah <span id="spTabLow">0</span></button>
    <button type="button" class="sp-tab" data-tab="NEED" role="tab">Perlu Diproduksi <span id="spTabNeed">0</span></button>
</div>

<p class="sp-tab-hint">
    Tab Perlu Diproduksi menampilkan semua produk dengan rekomendasi lebih dari 0,
    termasuk yang berstatus Stok Rendah.
</p>


{{-- =========================
    SEARCH + FILTER
========================= --}}

<div class="pq-toolbar">

    <label class="pq-search">
        <svg width="18" height="18" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="7"/>
            <path d="m20 20-3.5-3.5"/>
        </svg>

        <input
            type="text"
            id="spSearch"
            placeholder="Cari nama produk, SKU, atau variasi..."
            autocomplete="off"
            aria-label="Cari nama produk, SKU, atau variasi"
        >
    </label>

    <select class="pq-select" id="spFilterCategory" aria-label="Filter kategori">
        <option value="">Semua Kategori</option>
    </select>

    <select class="pq-select" id="spFilterStatus" aria-label="Filter status stok">
        <option value="">Semua Status Stok</option>
        <option value="SAFE">Stok Aman</option>
        <option value="LOW">Stok Rendah</option>
        <option value="NEED">Perlu Diproduksi</option>
    </select>

    <button type="button" class="pq-reset" id="spReset" hidden>
        Reset Filters
    </button>

</div>


{{-- =========================
    TABLE
========================= --}}

<div class="pq-card">

    <div class="pq-card-header">
        <h2>Pantau Stok Produk</h2>
        <span class="pq-count" id="spCount">0 produk</span>
    </div>


    <div class="pq-table-wrapper" id="spTableWrapper">

        <table class="pq-table sp-table">

            <caption class="pq-sr-only">Daftar stok produk jadi dan rekomendasi produksi</caption>

            <thead>
                <tr>
                    <th class="center">No.</th>
                    <th>Produk / SKU</th>
                    <th>Kategori</th>
                    <th class="center">Stok Saat Ini</th>
                    <th class="center">Min. Stock</th>
                    <th class="center">Target Stock</th>
                    <th class="center">Rekomendasi Produksi (MTS)</th>
                    <th class="center">Status Stok</th>
                    <th class="center">Aksi</th>
                </tr>
            </thead>

            <tbody id="spTableBody"></tbody>

        </table>

    </div>


    {{-- Empty state --}}
    <div class="pq-state" id="spEmpty" hidden>
        <div class="pq-state-icon">
            <svg width="24" height="24" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="11" cy="11" r="7"/>
                <path d="m20 20-3.5-3.5"/>
            </svg>
        </div>
        <h3>Tidak ada produk ditemukan</h3>
        <p id="spEmptyText">Belum ada produk yang sesuai dengan pencarian, tab, atau filter.</p>
        <button type="button" id="spEmptyReset">Reset Filters</button>
    </div>


    {{-- Error state --}}
    <div class="pq-state error" id="spError" hidden>
        <div class="pq-state-icon">
            <svg width="24" height="24" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="12" cy="12" r="9"/>
                <path d="M12 7v6"/>
                <path d="M12 16.5h.01"/>
            </svg>
        </div>
        <h3>Data gagal dimuat</h3>
        <p>Terjadi kendala saat mengambil data stok produk. Coba muat ulang.</p>
        <button type="button" id="spRetry">Coba lagi</button>
    </div>


    <div class="pq-footer" id="spFooter" hidden>
        <span id="spInfo" aria-live="polite"></span>
        <div class="pq-pagination" id="spPagination"></div>
    </div>

</div>


{{-- =========================
    INSIGHT & REKOMENDASI
========================= --}}

<div class="sp-insight" id="spInsight" hidden>

    <div class="sp-panel">
        <h3>Kondisi Stok</h3>
        <p>Sebaran status utama seluruh produk aktif.</p>

        <div class="sp-bar" id="spBar" aria-hidden="true"></div>

        <div class="sp-legend">
            <div><i class="safe"></i> Stok Aman <strong id="spLgSafe">0</strong></div>
            <div><i class="low"></i> Stok Rendah <strong id="spLgLow">0</strong></div>
            <div><i class="need"></i> Perlu Diproduksi (stok di atas minimum) <strong id="spLgNeed">0</strong></div>
        </div>
    </div>

    <div class="sp-panel">
        <h3>Rekomendasi Tertinggi</h3>
        <p>Produk dengan kebutuhan produksi terbesar.</p>

        <div class="sp-top" id="spTop"></div>
    </div>

    <div class="sp-panel">
        <h3>Ringkasan</h3>
        <p>Dihitung dari data yang sama dengan tabel.</p>

        <div class="sp-sum">
            <div>Produk di bawah stok minimum <strong id="spSumLow">0</strong></div>
            <div>Produk dengan request aktif <strong id="spSumActive">0</strong></div>
            <div>Total rekomendasi produksi <strong id="spSumRec">0</strong></div>
        </div>
    </div>

</div>


<div class="pq-toast" id="spToast" role="status" aria-live="polite"></div>


{{-- =========================
    MODAL: DETAIL PRODUK
========================= --}}

<div class="pe-overlay" id="spdOverlay" hidden>

    <div class="pe-modal" role="dialog" aria-modal="true" aria-labelledby="spdTitle">

        <div class="pe-header">

            <div class="pe-header-icon">
                <svg width="22" height="22" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M21 8 12 3 3 8v8l9 5 9-5V8Z"/>
                    <path d="m3 8 9 5 9-5"/>
                    <path d="M12 13v8"/>
                </svg>
            </div>

            <div class="pe-header-text">
                <h2 id="spdTitle">Detail Produk</h2>
                <p>SKU <strong id="spdSku">-</strong></p>
            </div>

            <button type="button" class="pe-close" id="spdClose" aria-label="Tutup">
                <svg width="18" height="18" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6 6 18"/>
                    <path d="m6 6 12 12"/>
                </svg>
            </button>

        </div>


        <div class="pe-body">

            <div class="sp-preview">
                <span class="sp-thumb big" id="spdThumb"></span>

                <div class="sp-preview-text">
                    <strong id="spdName">-</strong>
                    <span id="spdVariant">-</span>
                    <b class="pq-badge" id="spdStatus">-</b>
                </div>
            </div>

            <section class="pe-section">

                <div class="sp-grid">

                    <div class="sp-field">
                        <span>Stok Saat Ini</span>
                        <strong id="spdCurrent">-</strong>
                    </div>

                    <div class="sp-field">
                        <span>Stok Minimum</span>
                        <strong id="spdMin">-</strong>
                    </div>

                    <div class="sp-field">
                        <span>Target Stok</span>
                        <strong id="spdTarget">-</strong>
                    </div>

                    <div class="sp-field">
                        <span>Rekomendasi Produksi (MTS)</span>
                        <strong class="accent" id="spdRec">-</strong>
                    </div>

                </div>

            </section>

            <section class="pe-section">

                <h3>Request Produksi Aktif</h3>
                <p class="pe-section-desc">Request WAITING atau IN_PROCESS untuk produk ini. Jumlahnya sudah dihitung pada rekomendasi.</p>

                <div id="spdRequests"></div>

            </section>

        </div>


        <div class="pe-footer sp-footer">
            <button type="button" class="pe-btn pe-btn-secondary" id="spdDone">Tutup</button>
            <button type="button" class="pe-btn pe-btn-primary" id="spdMts" hidden>Buat MTS</button>
        </div>

    </div>

</div>


{{-- =========================
    MODAL: BUAT PERMINTAAN PRODUKSI (MTS)
========================= --}}

<div class="pe-overlay" id="spmOverlay" hidden>

    <div class="pe-modal" role="dialog" aria-modal="true" aria-labelledby="spmTitle">

        <div class="pe-header">

            <div class="pe-header-icon">
                <svg width="22" height="22" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M3 3v18h18"/>
                    <path d="m7 15 4-4 3 3 5-6"/>
                </svg>
            </div>

            <div class="pe-header-text">
                <h2 id="spmTitle">Buat Permintaan Produksi (MTS)</h2>
                <p>Tambah stok produk melalui production request</p>
            </div>

            <button type="button" class="pe-close" id="spmClose" aria-label="Tutup">
                <svg width="18" height="18" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6 6 18"/>
                    <path d="m6 6 12 12"/>
                </svg>
            </button>

        </div>


        <div class="pe-body">

            <p class="sp-note">
                Request akan masuk ke Production Queue dengan status WAITING.
                Stok produk tidak berubah saat request dibuat. Stok baru bertambah
                melalui proses produksi.
            </p>

            <div class="pe-form-error" id="spmError" hidden></div>

            <section class="pe-section">

                <h3><span>1.</span> Produk</h3>
                <p class="pe-section-desc">Pilih produk aktif yang perlu ditambah stoknya.</p>

                <select class="pq-select sp-full" id="spmProduct" aria-label="Produk / SKU">
                    <option value="">Pilih produk / SKU</option>
                </select>
                <span class="pe-field-error" id="spmProductError" hidden></span>

                <div class="sp-preview" id="spmPreview" hidden style="margin-top: 16px; margin-bottom: 0;">
                    <span class="sp-thumb big" id="spmThumb"></span>

                    <div class="sp-preview-text">
                        <strong id="spmName">-</strong>
                        <span id="spmVariant">-</span>
                        <b class="pq-badge" id="spmStatus">-</b>
                    </div>
                </div>

            </section>

            <section class="pe-section">

                <h3><span>2.</span> Kondisi Stok</h3>
                <p class="pe-section-desc">Terisi otomatis dari produk yang dipilih.</p>

                <div class="sp-grid">

                    <div class="sp-field">
                        <span>Stok Saat Ini</span>
                        <strong id="spmCurrent">-</strong>
                    </div>

                    <div class="sp-field">
                        <span>Target Stok</span>
                        <strong id="spmTarget">-</strong>
                    </div>

                    <div class="sp-field">
                        <span>Sudah Direncanakan (request aktif)</span>
                        <strong id="spmPlanned">-</strong>
                    </div>

                    <div class="sp-field">
                        <span>Rekomendasi Produksi</span>
                        <strong class="accent" id="spmRec">-</strong>
                    </div>

                </div>

                <div class="sp-warn" id="spmNotice" hidden></div>

            </section>

            <section class="pe-section">

                <h3><span>3.</span> Jumlah Diminta</h3>
                <p class="pe-section-desc">Boleh diubah. Harus berupa bilangan bulat lebih dari 0.</p>

                <div class="pe-input-group" id="spmQtyGroup">
                    <input type="number" id="spmQty" min="1" step="1" inputmode="numeric"
                           placeholder="0" aria-label="Jumlah diminta">
                    <span>pcs</span>
                </div>
                <span class="pe-field-error" id="spmQtyError" hidden></span>

                <div class="sp-warn" id="spmOver" hidden></div>

            </section>

        </div>


        <div class="pe-footer sp-footer">
            <button type="button" class="pe-btn pe-btn-secondary" id="spmCancel">Batal</button>
            <button type="button" class="pe-btn pe-btn-primary" id="spmSave">Buat Permintaan</button>
        </div>

    </div>

</div>

@endsection


@push('scripts')

<script>
(function () {

    'use strict';


    // ==========================================
    // KONFIGURASI
    // ==========================================

    const SP_CONFIG = {

        perPage: 8,

        searchDelay: 250,

        // Nilai source untuk request MTS. Nilai ini sama dengan yang dipakai
        // halaman Production Queue. Konfirmasikan ke backend sebelum dipakai.
        mtsSource: 'MTS_MIN_STOCK'
    };

    // Satu status utama per produk (urutan prioritas):
    //   LOW  : current_stock < min_stock
    //   NEED : stok >= minimum, tapi rekomendasi produksi > 0
    //   SAFE : stok >= minimum dan rekomendasi produksi = 0
    const STATUS_META = {
        SAFE: { label: 'Stok Aman',        cls: 'done' },
        LOW:  { label: 'Stok Rendah',      cls: 'urgent' },
        NEED: { label: 'Perlu Diproduksi', cls: 'waiting' }
    };

    const REQUEST_STATUS_META = {
        WAITING:    { label: 'WAITING',    cls: 'waiting' },
        IN_PROCESS: { label: 'IN_PROCESS', cls: 'progress' }
    };

    const ACTIVE_STATUSES = ['WAITING', 'IN_PROCESS'];

    const PLACEHOLDER_ICON =
        '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">' +
        '<rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="1.5"/><path d="m21 16-5-5-8 9"/></svg>';


    // ==========================================
    // DATA SIMULASI (MOCK)
    // Hanya untuk demonstrasi. Bukan data database.
    // Bentuk field mengikuti products, product_groups, categories,
    // dan production_requests.
    // ==========================================

    const MOCK_IMG = "{{ asset('images/produk.jpg') }}";

    const CAT = {
        meja:   { id: 1, name: 'Meja' },
        kursi:  { id: 2, name: 'Kursi' },
        lemari: { id: 3, name: 'Lemari' },
        rak:    { id: 4, name: 'Rak' }
    };

    const GROUP = {
        kerja:  { id: 1, name: 'Meja Kerja Minimalis',   category: CAT.meja,   image_path: MOCK_IMG },
        kursi:  { id: 2, name: 'Kursi Kantor Ergonomis', category: CAT.kursi,  image_path: MOCK_IMG },
        lemari: { id: 3, name: 'Lemari Pakaian 2 Pintu', category: CAT.lemari, image_path: null },
        rak:    { id: 4, name: 'Rak Buku 5 Susun',       category: CAT.rak,    image_path: MOCK_IMG },
        makan:  { id: 5, name: 'Meja Makan Lipat',       category: CAT.meja,   image_path: null }
    };

    function mockProduct(id, group, variant, sku, current, min, target, image, active) {
        return {
            id: id,
            group_id: group.id,
            group: group,
            variant_name: variant,
            sku: sku,
            current_stock: current,
            min_stock: min,
            target_stock: target,
            image_path: image,
            is_active: active !== false
        };
    }

    const MOCK_PRODUCTS = [
        mockProduct(1,  GROUP.kerja,  'Walnut',  'MJK-WLN', 4,  10, 30, MOCK_IMG),
        mockProduct(2,  GROUP.kerja,  'Putih',   'MJK-WHT', 18, 10, 30, null),
        mockProduct(3,  GROUP.kerja,  'Natural', 'MJK-NAT', 32, 10, 30, MOCK_IMG),
        mockProduct(4,  GROUP.kursi,  'Hitam',   'KRS-BLK', 6,  12, 40, MOCK_IMG),
        mockProduct(5,  GROUP.kursi,  'Abu-abu', 'KRS-GRY', 25, 12, 40, null),
        mockProduct(6,  GROUP.kursi,  'Biru',    'KRS-BLU', 41, 12, 40, MOCK_IMG),
        mockProduct(7,  GROUP.lemari, 'Jati',    'LMR-JTI', 2,  5,  15, null),
        mockProduct(8,  GROUP.lemari, 'Putih',   'LMR-WHT', 9,  5,  15, null),
        mockProduct(9,  GROUP.rak,    'Oak',     'RAK-OAK', 12, 8,  20, null),
        mockProduct(10, GROUP.rak,    'Hitam',   'RAK-BLK', 20, 8,  20, MOCK_IMG),
        mockProduct(11, GROUP.rak,    'Putih',   'RAK-WHT', 3,  8,  20, null),
        mockProduct(12, GROUP.makan,  'Natural', 'MMK-NAT', 14, 6,  16, null),
        mockProduct(13, GROUP.makan,  'Lama',    'MMK-OLD', 0,  4,  10, null, false)
    ];

    // Request milik produk nonaktif atau berstatus COMPLETED tidak dihitung.
    const MOCK_REQUESTS = [
        { id: 1, product_id: 1,  request_no: 'PR-20261020-001', source: 'MTS_MIN_STOCK', qty_requested: 10, qty_produced: 0,  status: 'WAITING' },
        { id: 2, product_id: 4,  request_no: 'PR-20261019-004', source: 'MTS_MIN_STOCK', qty_requested: 34, qty_produced: 10, status: 'IN_PROCESS' },
        { id: 3, product_id: 11, request_no: 'PR-20261020-002', source: 'MTS_MIN_STOCK', qty_requested: 10, qty_produced: 0,  status: 'WAITING' },
        { id: 4, product_id: 9,  request_no: 'PR-20261015-009', source: 'MTS_MIN_STOCK', qty_requested: 20, qty_produced: 20, status: 'COMPLETED' }
    ];


    // ==========================================
    // LAPISAN DATA (API)
    // Semua akses data lewat objek ini. Saat backend siap, ganti isi
    // fungsi (mis. fetch ke endpoint proyek) tanpa menyentuh UI.
    // Simulasi: ?mock=error | empty | savefail
    // ==========================================

    const flag = new URLSearchParams(window.location.search).get('mock');

    function wait(ms) {
        return new Promise(function (resolve) { setTimeout(resolve, ms); });
    }

    const api = {

        // products + product_groups + categories. Field gambar sudah
        // berupa URL yang bisa dipakai langsung oleh <img>.
        getProducts: async function () {
            await wait(450);
            if (flag === 'error') throw new Error('mock error');
            if (flag === 'empty') return [];
            return MOCK_PRODUCTS.map(function (p) { return Object.assign({}, p); });
        },

        // production_requests. Diambil terpisah dari produk supaya jumlah
        // tidak terhitung ganda akibat JOIN. Di backend, agregasi per
        // product_id sebaiknya memakai subquery GROUP BY.
        getProductionRequests: async function () {
            await wait(300);
            if (flag === 'error') throw new Error('mock error');
            if (flag === 'empty') return [];
            return MOCK_REQUESTS.map(function (r) { return Object.assign({}, r); });
        },

        // Membuat record production_requests. Simulasi saja.
        // request_no, requested_by, dan status awal WAITING diisi backend.
        createProductionRequest: async function (payload) {
            await wait(600);
            if (flag === 'savefail') throw new Error('mock save error');
            return { simulated: true, payload: payload };
        }
    };


    // ==========================================
    // STATE + ELEMEN
    // ==========================================

    const state = {
        products: [],
        requests: [],
        view: [],
        tab: 'ALL',
        search: '',
        category: '',
        status: '',
        page: 1,
        loading: true,
        failed: false,
        simCounter: 0
    };

    function $(id) { return document.getElementById(id); }

    const els = {
        statTotal:  $('spStatTotal'),
        statLow:    $('spStatLow'),
        statNeed:   $('spStatNeed'),
        statActive: $('spStatActive'),

        tabs:    $('spTabs'),
        tabAll:  $('spTabAll'),
        tabSafe: $('spTabSafe'),
        tabLow:  $('spTabLow'),
        tabNeed: $('spTabNeed'),

        search:   $('spSearch'),
        category: $('spFilterCategory'),
        status:   $('spFilterStatus'),
        reset:    $('spReset'),

        count:    $('spCount'),
        wrapper:  $('spTableWrapper'),
        body:     $('spTableBody'),
        empty:    $('spEmpty'),
        emptyTxt: $('spEmptyText'),
        emptyRst: $('spEmptyReset'),
        error:    $('spError'),
        retry:    $('spRetry'),

        footer:     $('spFooter'),
        info:       $('spInfo'),
        pagination: $('spPagination'),

        insight:  $('spInsight'),
        bar:      $('spBar'),
        lgSafe:   $('spLgSafe'),
        lgLow:    $('spLgLow'),
        lgNeed:   $('spLgNeed'),
        top:      $('spTop'),
        sumLow:   $('spSumLow'),
        sumActive: $('spSumActive'),
        sumRec:   $('spSumRec'),

        toast:  $('spToast'),
        create: $('spCreate')
    };


    // ==========================================
    // HELPER
    // ==========================================

    function esc(value) {
        return String(value == null ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function fmt(n) {
        return new Intl.NumberFormat('id-ID').format(n);
    }

    function fullName(p) {
        return p.group.name + (p.variant_name ? ' - ' + p.variant_name : '');
    }

    // Gambar variasi lebih dulu, lalu gambar grup sebagai cadangan.
    function imageOf(p) {
        return p.image_path || (p.group && p.group.image_path) || '';
    }

    function thumbInner(p) {
        const src = imageOf(p);
        return src
            ? '<img src="' + esc(src) + '" alt="' + esc(fullName(p)) + '" loading="lazy">'
            : PLACEHOLDER_ICON;
    }

    // Jika gambar gagal dimuat, ganti dengan ikon.
    document.addEventListener('error', function (event) {
        const img = event.target;
        if (img && img.tagName === 'IMG' && img.parentNode && img.parentNode.classList.contains('sp-thumb')) {
            img.parentNode.innerHTML = PLACEHOLDER_ICON;
        }
    }, true);

    let toastTimer = null;

    function toast(message) {
        els.toast.textContent = message;
        els.toast.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(function () {
            els.toast.classList.remove('show');
        }, 4200);
    }

    function findProduct(id) {
        return state.view.find(function (p) { return p.id === id; });
    }


    // ==========================================
    // PERHITUNGAN (satu sumber untuk semua bagian halaman)
    // ==========================================

    function activeRequestsOf(productId) {
        return state.requests.filter(function (r) {
            return r.product_id === productId && ACTIVE_STATUSES.indexOf(r.status) !== -1;
        });
    }

    // Rekomendasi MTS = max(0, target_stock - current_stock - qty request aktif)
    // qty request aktif = jumlah qty_requested pada WAITING dan IN_PROCESS.
    // Catatan: jika backend sudah menambah stok untuk qty_produced pada
    // request IN_PROCESS, perhitungan ini bisa menghitung ganda. Konfirmasi.
    function compute() {

        state.view = state.products
            .filter(function (p) { return p.is_active; })
            .map(function (p) {

                const actives = activeRequestsOf(p.id);
                const planned = actives.reduce(function (sum, r) { return sum + Number(r.qty_requested); }, 0);
                const base = Math.max(0, p.target_stock - p.current_stock);
                const rec = Math.max(0, p.target_stock - p.current_stock - planned);

                let status = 'SAFE';
                if (p.current_stock < p.min_stock) status = 'LOW';
                else if (rec > 0) status = 'NEED';

                return Object.assign({}, p, {
                    actives: actives,
                    planned: planned,
                    base: base,
                    rec: rec,
                    status: status
                });
            });
    }

    function inGroup(p, key) {
        if (key === 'SAFE') return p.status === 'SAFE';
        if (key === 'LOW')  return p.status === 'LOW';
        if (key === 'NEED') return p.rec > 0;
        return true;
    }


    // ==========================================
    // STATISTIK + TAB + INSIGHT
    // Semua dihitung dari state.view (dataset yang sama dengan tabel).
    // ==========================================

    function renderSummary() {

        const v = state.view;
        const dash = state.failed;

        const low = v.filter(function (p) { return p.status === 'LOW'; }).length;
        const safe = v.filter(function (p) { return p.status === 'SAFE'; }).length;
        const needStatus = v.filter(function (p) { return p.status === 'NEED'; }).length;
        const needRec = v.filter(function (p) { return p.rec > 0; }).length;

        const activeRequests = state.requests.filter(function (r) {
            return ACTIVE_STATUSES.indexOf(r.status) !== -1;
        }).length;

        els.statTotal.textContent  = dash ? '-' : v.length;
        els.statLow.textContent    = dash ? '-' : low;
        els.statNeed.textContent   = dash ? '-' : needRec;
        els.statActive.textContent = dash ? '-' : activeRequests;

        els.tabAll.textContent  = v.length;
        els.tabSafe.textContent = safe;
        els.tabLow.textContent  = low;
        els.tabNeed.textContent = needRec;

        // Insight
        els.insight.hidden = dash || state.loading || !v.length;

        const total = v.length || 1;
        els.bar.innerHTML =
            '<span class="safe" style="width:' + (safe / total * 100) + '%"></span>' +
            '<span class="low"  style="width:' + (low / total * 100) + '%"></span>' +
            '<span class="need" style="width:' + (needStatus / total * 100) + '%"></span>';

        els.lgSafe.textContent = safe;
        els.lgLow.textContent = low;
        els.lgNeed.textContent = needStatus;

        const top = v.filter(function (p) { return p.rec > 0; })
            .sort(function (a, b) { return b.rec - a.rec; })
            .slice(0, 3);

        els.top.innerHTML = top.length
            ? top.map(function (p) {
                return '<button type="button" data-top="' + p.id + '">' +
                    '<span class="sp-thumb">' + thumbInner(p) + '</span>' +
                    '<span class="sp-top-text"><strong>' + esc(p.group.name) + '</strong>' +
                    '<small>' + esc(p.variant_name) + ' &middot; ' + esc(p.sku) + '</small></span>' +
                    '<b>+' + fmt(p.rec) + '</b></button>';
            }).join('')
            : '<p class="sp-top-empty">Tidak ada produk yang perlu diproduksi.</p>';

        els.sumLow.textContent = low;
        els.sumActive.textContent = v.filter(function (p) { return p.planned > 0; }).length;
        els.sumRec.textContent = fmt(v.reduce(function (sum, p) { return sum + p.rec; }, 0));
    }


    // ==========================================
    // FILTER
    // ==========================================

    function hasFilter() {
        return !!(state.search.trim() || state.category || state.status || state.tab !== 'ALL');
    }

    function getFiltered() {

        const q = state.search.trim().toLowerCase();

        return state.view.filter(function (p) {

            if (!inGroup(p, state.tab)) return false;
            if (state.status && !inGroup(p, state.status)) return false;
            if (state.category && String(p.group.category.id) !== state.category) return false;

            if (q) {
                const haystack = [p.group.name, p.variant_name, p.sku].join(' ').toLowerCase();
                if (haystack.indexOf(q) === -1) return false;
            }

            return true;
        });
    }

    function fillCategoryFilter() {

        const seen = {};
        const list = [];

        state.view.forEach(function (p) {
            const c = p.group.category;
            if (c && !seen[c.id]) {
                seen[c.id] = true;
                list.push(c);
            }
        });

        list.sort(function (a, b) { return a.name.localeCompare(b.name); });

        els.category.innerHTML = '<option value="">Semua Kategori</option>' +
            list.map(function (c) {
                return '<option value="' + c.id + '">' + esc(c.name) + '</option>';
            }).join('');
    }


    // ==========================================
    // RENDER TABEL
    // ==========================================

    function skeletonRows() {
        let html = '';
        for (let i = 0; i < 5; i++) {
            html += '<tr>';
            for (let c = 0; c < 9; c++) {
                html += '<td><span class="pq-skeleton"></span></td>';
            }
            html += '</tr>';
        }
        return html;
    }

    function rowHtml(p, number) {

        const meta = STATUS_META[p.status];
        const percent = p.target_stock > 0
            ? Math.min(100, Math.round(p.current_stock / p.target_stock * 100)) : 0;

        const meter = '<div class="sp-meter ' + p.status.toLowerCase() + '"><span style="width:' + percent + '%"></span></div>';

        const rec = p.rec > 0
            ? '<span class="sp-rec">+' + fmt(p.rec) + '</span>'
            : '<span class="sp-rec-none">&mdash;</span>';

        const recNote = p.planned > 0
            ? '<span class="sp-rec-note">Sudah direncanakan: ' + fmt(p.planned) + '</span>'
            : '';

        return '' +
            '<tr>' +
                '<td class="center">' + number + '</td>' +
                '<td><div class="sp-product">' +
                    '<span class="sp-thumb">' + thumbInner(p) + '</span>' +
                    '<div class="sp-product-text"><strong>' + esc(p.group.name) + '</strong>' +
                    '<small>' + esc(p.variant_name) + ' &middot; ' + esc(p.sku) + '</small></div>' +
                '</div></td>' +
                '<td>' + esc(p.group.category ? p.group.category.name : '-') + '</td>' +
                '<td class="center"><div class="sp-stock"><strong>' + fmt(p.current_stock) + '</strong>' + meter + '</div></td>' +
                '<td class="center pq-qty">' + fmt(p.min_stock) + '</td>' +
                '<td class="center pq-qty">' + fmt(p.target_stock) + '</td>' +
                '<td class="center">' + rec + recNote + '</td>' +
                '<td class="center"><span class="pq-badge ' + meta.cls + '">' + meta.label + '</span></td>' +
                '<td class="center"><div class="sp-actions">' +
                    '<button type="button" class="sp-action outline" data-act="detail" data-id="' + p.id + '">Detail</button>' +
                    '<button type="button" class="sp-action primary" data-act="mts" data-id="' + p.id + '"' +
                        (p.rec > 0 ? '' : ' disabled title="Tidak ada kebutuhan produksi"') + '>Buat MTS</button>' +
                '</div></td>' +
            '</tr>';
    }

    function render() {

        els.error.hidden = true;
        els.empty.hidden = true;
        els.footer.hidden = true;
        els.wrapper.hidden = false;
        els.reset.hidden = !hasFilter();

        els.tabs.querySelectorAll('.sp-tab').forEach(function (tab) {
            const on = tab.getAttribute('data-tab') === state.tab;
            tab.classList.toggle('active', on);
            tab.setAttribute('aria-selected', on ? 'true' : 'false');
        });

        if (state.loading) {
            els.body.innerHTML = skeletonRows();
            els.count.textContent = 'Memuat...';
            return;
        }

        if (state.failed) {
            els.wrapper.hidden = true;
            els.error.hidden = false;
            els.count.textContent = '0 produk';
            return;
        }

        const filtered = getFiltered();
        els.count.textContent = filtered.length + ' produk';

        if (!filtered.length) {
            els.wrapper.hidden = true;
            els.empty.hidden = false;
            els.emptyTxt.textContent = state.view.length
                ? 'Tidak ada produk yang sesuai dengan pencarian, tab, atau filter.'
                : 'Belum ada produk aktif di Master Produk & SKU.';
            els.emptyRst.hidden = !hasFilter();
            return;
        }

        const pages = Math.ceil(filtered.length / SP_CONFIG.perPage);
        if (state.page > pages) state.page = pages;

        const start = (state.page - 1) * SP_CONFIG.perPage;
        const slice = filtered.slice(start, start + SP_CONFIG.perPage);

        els.body.innerHTML = slice.map(function (p, i) {
            return rowHtml(p, start + i + 1);
        }).join('');

        els.footer.hidden = false;
        els.info.textContent = 'Menampilkan ' + (start + 1) + '-' + (start + slice.length) +
            ' dari ' + filtered.length + ' produk';

        renderPagination(pages);
    }

    function renderPagination(pages) {

        if (pages <= 1) {
            els.pagination.innerHTML = '';
            return;
        }

        let html = '<button type="button" data-page="' + (state.page - 1) + '"' +
            (state.page === 1 ? ' disabled' : '') + ' aria-label="Sebelumnya">&lsaquo;</button>';

        for (let p = 1; p <= pages; p++) {
            html += '<button type="button" data-page="' + p + '" class="' +
                (p === state.page ? 'active' : '') + '">' + p + '</button>';
        }

        html += '<button type="button" data-page="' + (state.page + 1) + '"' +
            (state.page === pages ? ' disabled' : '') + ' aria-label="Berikutnya">&rsaquo;</button>';

        els.pagination.innerHTML = html;
    }

    function refreshAll() {
        compute();
        renderSummary();
        render();
    }


    // ==========================================
    // MODAL HELPER (buka/tutup, Esc, fokus)
    // ==========================================

    function createModal(overlay, options) {

        const settings = options || {};
        let opener = null;

        function focusables() {
            return Array.prototype.slice.call(overlay.querySelectorAll(
                'button:not([disabled]):not([hidden]), select:not([disabled]), input:not([disabled]), textarea:not([disabled])'
            )).filter(function (el) { return el.offsetParent !== null; });
        }

        function open() {
            opener = document.activeElement;
            overlay.hidden = false;
            document.body.style.overflow = 'hidden';
            const first = focusables()[0];
            if (first) first.focus();
        }

        function close() {
            if (settings.canClose && !settings.canClose()) return;
            overlay.hidden = true;
            document.body.style.overflow = '';
            if (opener && opener.focus && document.contains(opener)) opener.focus();
            opener = null;
        }

        overlay.addEventListener('mousedown', function (event) {
            if (event.target === overlay && settings.backdrop !== false) close();
        });

        overlay.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                close();
                return;
            }
            if (event.key !== 'Tab') return;
            const list = focusables();
            if (!list.length) return;
            const first = list[0];
            const last = list[list.length - 1];
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        });

        return { open: open, close: close };
    }


    // ==========================================
    // MODAL DETAIL PRODUK
    // ==========================================

    const D = {
        overlay:  $('spdOverlay'),
        sku:      $('spdSku'),
        thumb:    $('spdThumb'),
        name:     $('spdName'),
        variant:  $('spdVariant'),
        status:   $('spdStatus'),
        current:  $('spdCurrent'),
        min:      $('spdMin'),
        target:   $('spdTarget'),
        rec:      $('spdRec'),
        requests: $('spdRequests'),
        mts:      $('spdMts'),
        close:    $('spdClose'),
        done:     $('spdDone')
    };

    const detailModal = createModal(D.overlay);
    let detailId = null;

    function openDetail(id) {

        const p = findProduct(id);
        if (!p) return;

        detailId = id;

        const meta = STATUS_META[p.status];

        D.sku.textContent = p.sku;
        D.thumb.innerHTML = thumbInner(p);
        D.name.textContent = p.group.name;
        D.variant.textContent = 'Variasi: ' + (p.variant_name || '-') +
            ' \u00b7 Kategori: ' + (p.group.category ? p.group.category.name : '-');
        D.status.className = 'pq-badge ' + meta.cls;
        D.status.textContent = meta.label;
        D.current.textContent = fmt(p.current_stock);
        D.min.textContent = fmt(p.min_stock);
        D.target.textContent = fmt(p.target_stock);
        D.rec.textContent = p.rec > 0 ? '+' + fmt(p.rec) : 'Tidak perlu';

        D.requests.innerHTML = p.actives.length
            ? p.actives.map(function (r) {
                const rm = REQUEST_STATUS_META[r.status] || { label: r.status, cls: 'normal' };
                return '<div class="sp-req"><div><strong>' + esc(r.request_no || 'Draf simulasi') + '</strong>' +
                    '<small>Diminta ' + fmt(r.qty_requested) + ' &middot; diproduksi ' + fmt(r.qty_produced) + '</small></div>' +
                    '<span class="pq-badge ' + rm.cls + '">' + esc(rm.label) + '</span></div>';
            }).join('')
            : '<p class="sp-empty-line">Tidak ada request produksi aktif untuk produk ini.</p>';

        D.mts.hidden = p.rec <= 0;

        detailModal.open();
    }

    D.close.addEventListener('click', detailModal.close);
    D.done.addEventListener('click', detailModal.close);

    D.mts.addEventListener('click', function () {
        const id = detailId;
        detailModal.close();
        openMts(id);
    });


    // ==========================================
    // MODAL BUAT PERMINTAAN PRODUKSI (MTS)
    // ==========================================

    const M = {
        overlay:  $('spmOverlay'),
        error:    $('spmError'),
        product:  $('spmProduct'),
        prodErr:  $('spmProductError'),
        preview:  $('spmPreview'),
        thumb:    $('spmThumb'),
        name:     $('spmName'),
        variant:  $('spmVariant'),
        status:   $('spmStatus'),
        current:  $('spmCurrent'),
        target:   $('spmTarget'),
        planned:  $('spmPlanned'),
        rec:      $('spmRec'),
        notice:   $('spmNotice'),
        qty:      $('spmQty'),
        qtyGroup: $('spmQtyGroup'),
        qtyErr:   $('spmQtyError'),
        over:     $('spmOver'),
        save:     $('spmSave'),
        cancel:   $('spmCancel'),
        close:    $('spmClose')
    };

    const mts = { saving: false };

    const mtsModal = createModal(M.overlay, {
        backdrop: false,
        canClose: function () { return !mts.saving; }
    });

    function openMts(productId) {

        M.product.innerHTML = '<option value="">Pilih produk / SKU</option>' +
            state.view.map(function (p) {
                return '<option value="' + p.id + '"' + (p.rec > 0 ? '' : ' disabled') + '>' +
                    esc(p.sku + ' \u2014 ' + fullName(p)) +
                    (p.rec > 0 ? '' : ' (tidak perlu produksi)') + '</option>';
            }).join('');

        M.product.value = productId ? String(productId) : '';
        M.error.hidden = true;

        fillMts();
        mtsModal.open();
    }

    function clearMtsErrors() {
        M.error.hidden = true;
        M.prodErr.hidden = true;
        M.qtyErr.hidden = true;
        M.qtyGroup.classList.remove('invalid');
    }

    function fillMts() {

        clearMtsErrors();
        M.over.hidden = true;
        M.notice.hidden = true;

        const p = M.product.value ? findProduct(Number(M.product.value)) : null;

        if (!p) {
            M.preview.hidden = true;
            M.current.textContent = '-';
            M.target.textContent = '-';
            M.planned.textContent = '-';
            M.rec.textContent = '-';
            M.qty.value = '';
            return;
        }

        const meta = STATUS_META[p.status];

        M.preview.hidden = false;
        M.thumb.innerHTML = thumbInner(p);
        M.name.textContent = p.group.name;
        M.variant.textContent = 'Variasi: ' + (p.variant_name || '-') + ' \u00b7 SKU ' + p.sku;
        M.status.className = 'pq-badge ' + meta.cls;
        M.status.textContent = meta.label;

        M.current.textContent = fmt(p.current_stock);
        M.target.textContent = fmt(p.target_stock);
        M.planned.textContent = fmt(p.planned);
        M.rec.textContent = p.rec > 0 ? '+' + fmt(p.rec) : 'Tidak perlu';

        if (p.actives.length) {
            M.notice.hidden = false;
            M.notice.textContent = 'Produk ini sudah punya ' + p.actives.length +
                ' request aktif dengan total ' + fmt(p.planned) +
                '. Rekomendasi sudah dihitung ulang dengan jumlah tersebut.';
        }

        M.qty.value = p.rec > 0 ? p.rec : '';
    }

    function checkOver() {

        const p = M.product.value ? findProduct(Number(M.product.value)) : null;
        const qty = Number(M.qty.value);

        if (p && qty > p.rec && p.rec > 0) {
            M.over.hidden = false;
            M.over.textContent = 'Jumlah melebihi rekomendasi (' + fmt(p.rec) +
                '). Pastikan tidak terjadi produksi berlebih.';
        } else {
            M.over.hidden = true;
        }
    }

    function validateMts() {

        clearMtsErrors();

        let ok = true;
        const p = M.product.value ? findProduct(Number(M.product.value)) : null;
        const raw = M.qty.value;
        const qty = Number(raw);

        if (!p) {
            M.prodErr.textContent = 'Pilih produk terlebih dahulu.';
            M.prodErr.hidden = false;
            ok = false;
        } else if (p.rec <= 0) {
            M.prodErr.textContent = 'Produk ini tidak membutuhkan produksi tambahan.';
            M.prodErr.hidden = false;
            ok = false;
        }

        if (raw === '' || isNaN(qty) || qty <= 0 || Math.floor(qty) !== qty) {
            M.qtyErr.textContent = 'Jumlah harus berupa bilangan bulat lebih dari 0.';
            M.qtyErr.hidden = false;
            M.qtyGroup.classList.add('invalid');
            ok = false;
        }

        return ok ? { product: p, qty: qty } : null;
    }

    async function saveMts() {

        const valid = validateMts();
        if (!valid) return;

        // request_no, requested_by, dan status awal WAITING diisi backend.
        const payload = {
            product_id: valid.product.id,
            qty_requested: valid.qty,
            source: SP_CONFIG.mtsSource
        };

        mts.saving = true;
        M.save.disabled = true;
        M.cancel.disabled = true;
        M.save.textContent = 'Menyimpan...';

        try {
            const result = await api.createProductionRequest(payload);

            // SIMULASI: tambahkan request sementara agar ringkasan dan tabel
            // ikut berubah. Dengan backend asli, muat ulang data dari server.
            state.requests.push({
                id: 'sim-' + (++state.simCounter),
                product_id: payload.product_id,
                request_no: null,
                source: payload.source,
                qty_requested: payload.qty_requested,
                qty_produced: 0,
                status: 'WAITING',
                simulated: true
            });

            mts.saving = false;
            mtsModal.close();
            refreshAll();

            document.dispatchEvent(new CustomEvent('stock-planning:request-created', {
                detail: result.payload
            }));

            toast('Simulasi: permintaan ' + fmt(payload.qty_requested) + ' pcs ' +
                fullName(valid.product) + ' sudah disiapkan. Belum tersimpan ke database dan belum tampil di Production Queue.');

        } catch (error) {
            M.error.textContent = 'Permintaan gagal disimpan. Coba lagi.';
            M.error.hidden = false;
            M.overlay.querySelector('.pe-body').scrollTop = 0;
        } finally {
            mts.saving = false;
            M.save.disabled = false;
            M.cancel.disabled = false;
            M.save.textContent = 'Buat Permintaan';
        }
    }

    M.product.addEventListener('change', fillMts);
    M.qty.addEventListener('input', function () {
        M.qtyErr.hidden = true;
        M.qtyGroup.classList.remove('invalid');
        checkOver();
    });
    M.save.addEventListener('click', saveMts);
    M.cancel.addEventListener('click', mtsModal.close);
    M.close.addEventListener('click', mtsModal.close);

    els.create.addEventListener('click', function () { openMts(null); });


    // ==========================================
    // EVENT HALAMAN
    // ==========================================

    let searchTimer = null;

    els.search.addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () {
            state.search = els.search.value;
            state.page = 1;
            render();
        }, SP_CONFIG.searchDelay);
    });

    els.category.addEventListener('change', function () {
        state.category = els.category.value;
        state.page = 1;
        render();
    });

    els.status.addEventListener('change', function () {
        state.status = els.status.value;
        state.page = 1;
        render();
    });

    els.tabs.addEventListener('click', function (event) {
        const tab = event.target.closest('.sp-tab');
        if (!tab) return;
        state.tab = tab.getAttribute('data-tab');
        state.page = 1;
        render();
    });

    function resetFilters() {
        state.search = '';
        state.category = '';
        state.status = '';
        state.tab = 'ALL';
        state.page = 1;
        els.search.value = '';
        els.category.value = '';
        els.status.value = '';
        render();
    }

    els.reset.addEventListener('click', resetFilters);
    els.emptyRst.addEventListener('click', resetFilters);

    els.pagination.addEventListener('click', function (event) {
        const button = event.target.closest('button[data-page]');
        if (!button || button.disabled) return;
        state.page = Number(button.getAttribute('data-page'));
        render();
    });

    els.body.addEventListener('click', function (event) {

        const button = event.target.closest('button[data-act]');
        if (!button || button.disabled) return;

        const id = Number(button.getAttribute('data-id'));

        if (button.getAttribute('data-act') === 'detail') openDetail(id);
        if (button.getAttribute('data-act') === 'mts') openMts(id);
    });

    els.top.addEventListener('click', function (event) {
        const button = event.target.closest('button[data-top]');
        if (button) openMts(Number(button.getAttribute('data-top')));
    });

    els.retry.addEventListener('click', loadData);


    // ==========================================
    // MUAT DATA
    // ==========================================

    async function loadData() {

        state.loading = true;
        state.failed = false;
        els.insight.hidden = true;
        render();

        try {
            const results = await Promise.all([
                api.getProducts(),
                api.getProductionRequests()
            ]);

            state.products = results[0];
            state.requests = results[1];

            compute();
            fillCategoryFilter();

        } catch (error) {
            state.products = [];
            state.requests = [];
            state.view = [];
            state.failed = true;
        }

        state.loading = false;
        renderSummary();
        render();
    }

    loadData();

})();
</script>

@endpush