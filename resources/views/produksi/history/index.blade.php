@extends('layouts.produksi')

@section('title', 'Riwayat Produksi - REBANT BOMS')

@section('content')

{{-- =========================
    PAGE HEADER
========================= --}}

<div class="pq-page-header">

    <div class="pq-page-header-text">
        <h1>Riwayat Produksi</h1>
        <p>Pantau catatan produksi yang telah dilakukan, produk yang dihasilkan, jumlah produksi, dan petugas yang mencatat.</p>
    </div>

</div>


<div class="ph-demo" id="phDemo" hidden>
    Data demonstrasi. Halaman ini belum terhubung ke database, jadi seluruh catatan di bawah hanya contoh tampilan.
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
            <span class="pq-stat-label">Total Produksi</span>
        </div>
        <strong class="pq-stat-value" id="phStatQty">-</strong>
        <span class="ph-stat-note">Total unit pada data yang ditampilkan</span>
    </div>

    <div class="pq-stat progress">
        <div class="pq-stat-head">
            <div class="pq-stat-icon">
                <svg width="20" height="20" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M8 6h13"/>
                    <path d="M8 12h13"/>
                    <path d="M8 18h13"/>
                    <path d="M3 6h.01"/>
                    <path d="M3 12h.01"/>
                    <path d="M3 18h.01"/>
                </svg>
            </div>
            <span class="pq-stat-label">Sesi Produksi</span>
        </div>
        <strong class="pq-stat-value" id="phStatSessions">-</strong>
        <span class="ph-stat-note">Jumlah catatan production_logs</span>
    </div>

    <div class="pq-stat done">
        <div class="pq-stat-head">
            <div class="pq-stat-icon">
                <svg width="20" height="20" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="16" rx="2"/>
                    <circle cx="9" cy="10" r="1.5"/>
                    <path d="m21 16-5-5-8 9"/>
                </svg>
            </div>
            <span class="pq-stat-label">Produk Diproduksi</span>
        </div>
        <strong class="pq-stat-value" id="phStatProducts">-</strong>
        <span class="ph-stat-note">SKU unik dalam catatan</span>
    </div>

    <div class="pq-stat waiting">
        <div class="pq-stat-head">
            <div class="pq-stat-icon">
                <svg width="20" height="20" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="8" r="4"/>
                    <path d="M4 21a8 8 0 0 1 16 0"/>
                </svg>
            </div>
            <span class="pq-stat-label">Petugas Produksi</span>
        </div>
        <strong class="pq-stat-value" id="phStatUsers">-</strong>
        <span class="ph-stat-note">Petugas unik yang mencatat</span>
    </div>

</div>

<p class="ph-explain">
    Satu sesi produksi berarti satu catatan production_logs, bukan satu production_request.
    Satu request bisa memiliki beberapa sesi, dan setiap sesi ditampilkan terpisah.
    Kartu di atas mengikuti search dan filter yang sedang aktif.
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
            id="phSearch"
            placeholder="Cari request, produk, SKU, petugas..."
            autocomplete="off"
            aria-label="Cari riwayat produksi"
        >
    </label>

    <div class="ph-range">
        <input type="date" class="ph-date" id="phDateFrom" aria-label="Tanggal awal">
        <span>s/d</span>
        <input type="date" class="ph-date" id="phDateTo" aria-label="Tanggal akhir">
    </div>

    <select class="pq-select" id="phFilterProduct" aria-label="Filter produk atau SKU">
        <option value="">Semua Produk / SKU</option>
    </select>

    <select class="pq-select" id="phFilterUser" aria-label="Filter petugas pencatat">
        <option value="">Semua Petugas</option>
    </select>

    <button type="button" class="pq-reset" id="phReset" hidden>
        Reset Filter
    </button>

</div>

<p class="ph-range-error" id="phRangeError" hidden>
    Tanggal akhir tidak boleh lebih awal dari tanggal awal.
</p>


{{-- =========================
    TABLE
========================= --}}

<div class="pq-card">

    <div class="pq-card-header">
        <h2>Catatan Produksi</h2>
        <span class="pq-count" id="phCount">0 sesi</span>
    </div>


    <div class="pq-table-wrapper" id="phTableWrapper">

        <table class="pq-table ph-table">

            <caption class="pq-sr-only">Daftar riwayat sesi produksi</caption>

            <thead>
                <tr>
                    <th class="center">No.</th>
                    <th>Tanggal Produksi</th>
                    <th>Produk / SKU</th>
                    {{-- KOREKSI: kolom "Gambar Produk" dihapus --}}
                    <th>Request Produksi</th>
                    <th class="center">Jumlah Diproduksi</th>
                    <th>Dicatat Oleh</th>
                    <th>Catatan</th>
                    <th class="center">Aksi</th>
                </tr>
            </thead>

            <tbody id="phTableBody"></tbody>

        </table>

    </div>


    {{-- Empty state --}}
    <div class="pq-state" id="phEmpty" hidden>
        <div class="pq-state-icon">
            <svg width="24" height="24" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="11" cy="11" r="7"/>
                <path d="m20 20-3.5-3.5"/>
            </svg>
        </div>
        <h3>Tidak ada catatan ditemukan</h3>
        <p id="phEmptyText">Belum ada catatan produksi yang sesuai dengan pencarian atau filter.</p>
        <button type="button" id="phEmptyReset">Reset Filter</button>
    </div>


    {{-- Error state --}}
    <div class="pq-state error" id="phError" hidden>
        <div class="pq-state-icon">
            <svg width="24" height="24" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="12" cy="12" r="9"/>
                <path d="M12 7v6"/>
                <path d="M12 16.5h.01"/>
            </svg>
        </div>
        <h3>Data gagal dimuat</h3>
        <p>Terjadi kendala saat mengambil riwayat produksi. Coba muat ulang.</p>
        <button type="button" id="phRetry">Coba lagi</button>
    </div>


    <div class="pq-footer" id="phFooter" hidden>
        <span id="phInfo" aria-live="polite"></span>
        <div class="pq-pagination" id="phPagination"></div>
    </div>

</div>


{{-- =========================
    MODAL: DETAIL RIWAYAT PRODUKSI (hanya baca)
========================= --}}

<div class="pe-overlay" id="phdOverlay" hidden>

    <div class="pe-modal" role="dialog" aria-modal="true" aria-labelledby="phdTitle">

        <div class="pe-header">

            <div class="pe-header-icon">
                <svg width="22" height="22" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 7v5l3 2"/>
                </svg>
            </div>

            <div class="pe-header-text">
                <h2 id="phdTitle">Detail Riwayat Produksi</h2>
                <p>Request <strong id="phdRequestNo">-</strong></p>
            </div>

            <button type="button" class="pe-close" id="phdClose" aria-label="Tutup">
                <svg width="18" height="18" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6 6 18"/>
                    <path d="m6 6 12 12"/>
                </svg>
            </button>

        </div>


        <div class="pe-body">

            {{-- Produk --}}
            <div class="ph-preview">
                <span class="ph-thumb big" id="phdThumb"></span>

                <div class="ph-preview-text">
                    <strong id="phdName">-</strong>
                    <span id="phdVariant">-</span>
                </div>
            </div>


            {{-- 1. INFORMASI PRODUKSI --}}
            <section class="pe-section">

                <h3><span>1.</span> Informasi Produksi</h3>
                <p class="pe-section-desc">Catatan satu sesi produksi.</p>

                <div class="ph-grid">

                    <div class="ph-field">
                        <span>Nomor Request</span>
                        <strong id="phdRequest">-</strong>
                    </div>

                    <div class="ph-field">
                        <span>SKU</span>
                        <strong id="phdSku">-</strong>
                    </div>

                    <div class="ph-field">
                        <span>Tanggal Produksi</span>
                        <strong id="phdDate">-</strong>
                    </div>

                    <div class="ph-field">
                        <span>Jumlah Diproduksi (sesi ini)</span>
                        <strong class="accent" id="phdQty">-</strong>
                    </div>

                    <div class="ph-field">
                        <span>Dicatat Oleh</span>
                        <strong id="phdUser">-</strong>
                    </div>

                    <div class="ph-field full">
                        <span>Catatan Produksi</span>
                        <p id="phdNotes">-</p>
                    </div>

                </div>

            </section>


            {{-- 2. PENGGUNAAN BAHAN BAKU --}}
            <section class="pe-section">

                <h3><span>2.</span> Penggunaan Bahan Baku</h3>
                <p class="pe-section-desc">Bahan baku yang tercatat terpakai pada sesi ini.</p>

                <div id="phdLoading">
                    <span class="pq-skeleton pe-skel"></span>
                    <span class="pq-skeleton pe-skel"></span>
                    <span class="pq-skeleton pe-skel short"></span>
                </div>

                <div class="pe-error-box" id="phdError" hidden>
                    <strong>Penggunaan bahan gagal dimuat</strong>
                    <p>Terjadi kendala saat mengambil data penggunaan bahan baku.</p>
                    <button type="button" class="pe-btn pe-btn-secondary" id="phdRetry">Coba lagi</button>
                </div>

                <div id="phdMaterials" hidden></div>

            </section>

        </div>


        <div class="pe-footer single ph-footer">
            <button type="button" class="pe-btn pe-btn-secondary" id="phdDone">Tutup</button>
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

    const PH_CONFIG = {

        perPage: 8,

        searchDelay: 250,

        // true selama masih memakai DATA SIMULASI di bawah.
        // Ubah ke false saat sudah terhubung ke backend.
        isDemo: true
    };

    const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    const PLACEHOLDER_ICON =
        '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">' +
        '<rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="1.5"/><path d="m21 16-5-5-8 9"/></svg>';


    // ==========================================
    // DATA SIMULASI (MOCK)
    // Hanya untuk demonstrasi. Bukan catatan produksi sungguhan.
    // Bentuk field mengikuti production_logs, production_requests,
    // products, product_groups, users, production_log_materials,
    // dan raw_materials.
    // ==========================================

    const MOCK_IMG = "{{ asset('images/produk.jpg') }}";

    const USERS = {
        akmal: { id: 2, name: 'Akmal' },
        rina:  { id: 5, name: 'Rina Setiawati' },
        budi:  { id: 6, name: 'Budi Santoso' }
    };

    const GROUP = {
        kerja:  { id: 1, name: 'Meja Kerja Minimalis',   image_path: MOCK_IMG },
        kursi:  { id: 2, name: 'Kursi Kantor Ergonomis', image_path: MOCK_IMG },
        lemari: { id: 3, name: 'Lemari Pakaian 2 Pintu', image_path: null },
        rak:    { id: 4, name: 'Rak Buku 5 Susun',       image_path: MOCK_IMG }
    };

    const PRODUCT = {
        wln: { id: 1,  sku: 'MJK-WLN', variant_name: 'Walnut',  image_path: MOCK_IMG, group: GROUP.kerja },
        wht: { id: 2,  sku: 'MJK-WHT', variant_name: 'Putih',   image_path: null,     group: GROUP.kerja },
        blk: { id: 4,  sku: 'KRS-BLK', variant_name: 'Hitam',   image_path: MOCK_IMG, group: GROUP.kursi },
        gry: { id: 5,  sku: 'KRS-GRY', variant_name: 'Abu-abu', image_path: null,     group: GROUP.kursi },
        jti: { id: 7,  sku: 'LMR-JTI', variant_name: 'Jati',    image_path: null,     group: GROUP.lemari },
        oak: { id: 9,  sku: 'RAK-OAK', variant_name: 'Oak',     image_path: null,     group: GROUP.rak },
        rbk: { id: 10, sku: 'RAK-BLK', variant_name: 'Hitam',   image_path: MOCK_IMG, group: GROUP.rak }
    };

    const REQUEST = {
        r1: { id: 1, request_no: 'PR-20261019-004', qty_requested: 34, product: PRODUCT.blk },
        r2: { id: 2, request_no: 'PR-20261015-009', qty_requested: 20, product: PRODUCT.oak },
        r3: { id: 3, request_no: 'PR-20261012-003', qty_requested: 25, product: PRODUCT.wln },
        r4: { id: 4, request_no: 'PR-20261008-001', qty_requested: 12, product: PRODUCT.jti },
        r5: { id: 5, request_no: 'PR-20261005-002', qty_requested: 18, product: PRODUCT.wht },
        r6: { id: 6, request_no: 'PR-20261003-006', qty_requested: 15, product: PRODUCT.gry },
        r7: { id: 7, request_no: 'PR-20261001-003', qty_requested: 20, product: PRODUCT.rbk }
    };

    function mockLog(id, request, user, date, qty, notes) {
        return {
            id: id,
            production_request_id: request.id,
            recorded_by: user.id,
            production_date: date,
            qty_produced: qty,
            notes: notes || null,
            production_request: request,
            recorder: user
        };
    }

    // Beberapa request memiliki lebih dari satu sesi produksi.
    const MOCK_LOGS = [
        mockLog(1,  REQUEST.r1, USERS.akmal, '2026-10-24', 10, 'Sesi pertama. Rangka dan dudukan selesai, pelapis menunggu pengiriman.'),
        mockLog(2,  REQUEST.r1, USERS.rina,  '2026-10-22', 12, 'Sesi kedua. Perakitan roda dan sandaran.'),
        mockLog(3,  REQUEST.r1, USERS.akmal, '2026-10-20', 12, null),
        mockLog(4,  REQUEST.r2, USERS.budi,  '2026-10-17', 20, 'Selesai satu sesi. Finishing oak berjalan lancar.'),
        mockLog(5,  REQUEST.r3, USERS.rina,  '2026-10-14', 15, 'Sesi pertama. Cat walnut dua lapis.'),
        mockLog(6,  REQUEST.r3, USERS.rina,  '2026-10-13', 10, null),
        mockLog(7,  REQUEST.r4, USERS.akmal, '2026-10-10', 12, 'Papan jati sedikit melengkung pada 1 unit, sudah diganti.'),
        mockLog(8,  REQUEST.r5, USERS.budi,  '2026-10-08', 8,  null),
        mockLog(9,  REQUEST.r5, USERS.budi,  '2026-10-07', 10, 'Sesi lanjutan setelah bahan cat datang.'),
        mockLog(10, REQUEST.r6, USERS.akmal, '2026-10-06', 15, null),
        mockLog(11, REQUEST.r7, USERS.rina,  '2026-10-04', 12, 'Sesi pertama.'),
        mockLog(12, REQUEST.r7, USERS.rina,  '2026-10-02', 8,  'Sesi kedua. Target request tercapai.')
    ];

    const RAW = {
        mdf: { id: 1, code: 'MDF-12',  name: 'MDF Board 12 mm',  unit: 'pcs' },
        ply: { id: 3, code: 'PLY-09',  name: 'Plywood 9 mm',     unit: 'pcs' },
        cat: { id: 2, code: 'CAT-WH',  name: 'Cat Kayu (White)', unit: 'liter' },
        lem: { id: 5, code: 'LEM-PV',  name: 'Lem Kayu PVAC',    unit: 'kg' },
        skr: { id: 4, code: 'SKR-30',  name: 'Sekrup 3 cm',      unit: 'box' },
        amp: { id: 6, code: 'AMP-120', name: 'Amplas 120',       unit: 'lembar' },
        vrn: { id: 8, code: 'VRN-CL',  name: 'Vernis Clear',     unit: 'liter' }
    };

    // production_log_materials per sesi. Sesi yang tidak ada di sini
    // dianggap belum punya catatan pemakaian bahan (id 3, 6, 10).
    const MOCK_MATERIALS = {
        1:  [{ raw_material: RAW.ply, qty_used: 10, usage_date: '2026-10-24' }, { raw_material: RAW.skr, qty_used: 1, usage_date: '2026-10-24' }, { raw_material: RAW.lem, qty_used: 2, usage_date: '2026-10-24' }],
        2:  [{ raw_material: RAW.ply, qty_used: 12, usage_date: '2026-10-22' }, { raw_material: RAW.skr, qty_used: 2, usage_date: '2026-10-22' }],
        4:  [{ raw_material: RAW.mdf, qty_used: 20, usage_date: '2026-10-17' }, { raw_material: RAW.vrn, qty_used: 3.5, usage_date: '2026-10-17' }, { raw_material: RAW.amp, qty_used: 24, usage_date: '2026-10-17' }],
        5:  [{ raw_material: RAW.mdf, qty_used: 15, usage_date: '2026-10-14' }, { raw_material: RAW.cat, qty_used: 4, usage_date: '2026-10-14' }],
        7:  [{ raw_material: RAW.ply, qty_used: 12, usage_date: '2026-10-10' }, { raw_material: RAW.vrn, qty_used: 2, usage_date: '2026-10-10' }],
        8:  [{ raw_material: RAW.mdf, qty_used: 8, usage_date: '2026-10-08' }],
        9:  [{ raw_material: RAW.mdf, qty_used: 10, usage_date: '2026-10-07' }, { raw_material: RAW.cat, qty_used: 3, usage_date: '2026-10-07' }],
        11: [{ raw_material: RAW.ply, qty_used: 12, usage_date: '2026-10-04' }],
        12: [{ raw_material: RAW.ply, qty_used: 8, usage_date: '2026-10-02' }, { raw_material: RAW.lem, qty_used: 1.5, usage_date: '2026-10-02' }]
    };


    // ==========================================
    // LAPISAN DATA (API)
    // Semua akses data lewat objek ini. Saat backend siap, ganti isi
    // fungsi (mis. fetch ke endpoint proyek) tanpa menyentuh UI.
    // Simulasi: ?mock=error | empty | detailerror
    // ==========================================

    const flag = new URLSearchParams(window.location.search).get('mock');

    function wait(ms) {
        return new Promise(function (resolve) { setTimeout(resolve, ms); });
    }

    const api = {

        // production_logs + production_requests + products + product_groups + users.
        // Satu elemen = satu sesi produksi (satu record production_logs).
        getProductionLogs: async function () {
            await wait(450);
            if (flag === 'error') throw new Error('mock error');
            if (flag === 'empty') return [];
            return MOCK_LOGS.map(function (log) { return Object.assign({}, log); });
        },

        // production_log_materials + raw_materials untuk satu sesi.
        getProductionLogMaterials: async function (logId) {
            await wait(350);
            if (flag === 'detailerror') throw new Error('mock detail error');
            return (MOCK_MATERIALS[logId] || []).map(function (m) { return Object.assign({}, m); });
        }
    };


    // ==========================================
    // STATE + ELEMEN
    // ==========================================

    const state = {
        logs: [],
        search: '',
        dateFrom: '',
        dateTo: '',
        product: '',
        user: '',
        page: 1,
        loading: true,
        failed: false
    };

    function $(id) { return document.getElementById(id); }

    const els = {
        demo:     $('phDemo'),

        statQty:      $('phStatQty'),
        statSessions: $('phStatSessions'),
        statProducts: $('phStatProducts'),
        statUsers:    $('phStatUsers'),

        search:    $('phSearch'),
        dateFrom:  $('phDateFrom'),
        dateTo:    $('phDateTo'),
        product:   $('phFilterProduct'),
        user:      $('phFilterUser'),
        reset:     $('phReset'),
        rangeErr:  $('phRangeError'),

        count:    $('phCount'),
        wrapper:  $('phTableWrapper'),
        body:     $('phTableBody'),
        empty:    $('phEmpty'),
        emptyTxt: $('phEmptyText'),
        emptyRst: $('phEmptyReset'),
        error:    $('phError'),
        retry:    $('phRetry'),

        footer:     $('phFooter'),
        info:       $('phInfo'),
        pagination: $('phPagination')
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
        return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(n);
    }

    // production_date diperlakukan sebagai tanggal (YYYY-MM-DD),
    // supaya tidak bergeser karena zona waktu.
    function dateKey(log) {
        return String(log.production_date || '').slice(0, 10);
    }

    function fmtDate(key) {
        const p = String(key || '').slice(0, 10).split('-');
        if (p.length !== 3) return '-';
        return parseInt(p[2], 10) + ' ' + MONTHS[parseInt(p[1], 10) - 1] + ' ' + p[0];
    }

    function productOf(log) {
        return log.production_request && log.production_request.product
            ? log.production_request.product : null;
    }

    function productName(log) {
        const p = productOf(log);
        return p && p.group ? p.group.name : '-';
    }

    // Gambar produk (products.image_path) lebih dulu,
    // lalu gambar grup (product_groups.image_path) sebagai cadangan.
    function imageOf(log) {
        const p = productOf(log);
        if (!p) return '';
        return p.image_path || (p.group && p.group.image_path) || '';
    }

    function thumbInner(log) {
        const src = imageOf(log);
        return src
            ? '<img src="' + esc(src) + '" alt="' + esc(productName(log)) + '" loading="lazy">'
            : PLACEHOLDER_ICON;
    }

    // Jika gambar gagal dimuat, ganti dengan ikon.
    document.addEventListener('error', function (event) {
        const img = event.target;
        if (img && img.tagName === 'IMG' && img.parentNode && img.parentNode.classList.contains('ph-thumb')) {
            img.parentNode.innerHTML = PLACEHOLDER_ICON;
        }
    }, true);

    function initials(name) {
        return String(name || '?').trim().charAt(0).toUpperCase();
    }

    function userName(log) {
        return log.recorder ? log.recorder.name : '-';
    }


    // ==========================================
    // FILTER
    // ==========================================

    function rangeInvalid() {
        return !!(state.dateFrom && state.dateTo && state.dateTo < state.dateFrom);
    }

    function hasFilter() {
        return !!(state.search.trim() || state.dateFrom || state.dateTo || state.product || state.user);
    }

    function getFiltered() {

        if (rangeInvalid()) return [];

        const q = state.search.trim().toLowerCase();

        return state.logs.filter(function (log) {

            const key = dateKey(log);
            const p = productOf(log);

            if (state.dateFrom && key < state.dateFrom) return false;
            if (state.dateTo && key > state.dateTo) return false;
            if (state.product && (!p || String(p.id) !== state.product)) return false;
            if (state.user && String(log.recorded_by) !== state.user) return false;

            if (q) {
                const haystack = [
                    log.production_request ? log.production_request.request_no : '',
                    productName(log),
                    p ? p.variant_name : '',
                    p ? p.sku : '',
                    userName(log),
                    log.notes || ''
                ].join(' ').toLowerCase();

                if (haystack.indexOf(q) === -1) return false;
            }

            return true;
        });
    }

    // Terbaru lebih dulu. Jika tanggal sama, record terbaru lebih dulu.
    function sortLogs(list) {
        return list.slice().sort(function (a, b) {
            const da = dateKey(a);
            const db = dateKey(b);
            if (da !== db) return da < db ? 1 : -1;
            return b.id - a.id;
        });
    }

    function fillFilters() {

        const products = {};
        const users = {};

        state.logs.forEach(function (log) {
            const p = productOf(log);
            if (p) products[p.id] = p;
            if (log.recorder) users[log.recorder.id] = log.recorder;
        });

        els.product.innerHTML = '<option value="">Semua Produk / SKU</option>' +
            Object.keys(products)
                .map(function (id) { return products[id]; })
                .sort(function (a, b) { return a.sku.localeCompare(b.sku); })
                .map(function (p) {
                    return '<option value="' + p.id + '">' +
                        esc(p.sku + ' \u2014 ' + p.group.name + ' ' + p.variant_name) + '</option>';
                }).join('');

        els.user.innerHTML = '<option value="">Semua Petugas</option>' +
            Object.keys(users)
                .map(function (id) { return users[id]; })
                .sort(function (a, b) { return a.name.localeCompare(b.name); })
                .map(function (u) {
                    return '<option value="' + u.id + '">' + esc(u.name) + '</option>';
                }).join('');
    }


    // ==========================================
    // STATISTIK
    // Dihitung dari data yang sedang tampil (sesuai filter).
    // Satu sesi = satu record production_logs.
    // ==========================================

    function renderStats(filtered) {

        if (state.failed || state.loading) {
            els.statQty.textContent = '-';
            els.statSessions.textContent = '-';
            els.statProducts.textContent = '-';
            els.statUsers.textContent = '-';
            return;
        }

        const products = {};
        const users = {};
        let total = 0;

        filtered.forEach(function (log) {
            total += Number(log.qty_produced) || 0;
            const p = productOf(log);
            if (p) products[p.id] = true;
            users[log.recorded_by] = true;
        });

        els.statQty.textContent = fmt(total);
        els.statSessions.textContent = fmt(filtered.length);
        els.statProducts.textContent = Object.keys(products).length;
        els.statUsers.textContent = Object.keys(users).length;
    }


    // ==========================================
    // RENDER TABEL
    // ==========================================

    function skeletonRows() {
        let html = '';
        for (let i = 0; i < 5; i++) {
            html += '<tr>';
            // KOREKSI: kolom tinggal 8 (kolom Gambar Produk sudah dihapus)
            for (let c = 0; c < 8; c++) {
                html += '<td><span class="pq-skeleton"></span></td>';
            }
            html += '</tr>';
        }
        return html;
    }

    function rowHtml(log, number) {

        const p = productOf(log);
        const req = log.production_request || {};

        const notes = log.notes
            ? '<span class="ph-notes" title="' + esc(log.notes) + '">' + esc(log.notes) + '</span>'
            : '<span class="ph-none">&mdash;</span>';

        return '' +
            '<tr>' +
                '<td class="center">' + number + '</td>' +
                '<td class="ph-date-cell">' + esc(fmtDate(dateKey(log))) + '</td>' +
                // KOREKSI: gambar digabung ke kolom Produk / SKU
                '<td><div class="ph-product-cell">' +
                    '<span class="ph-thumb">' + thumbInner(log) + '</span>' +
                    '<div class="ph-product"><strong title="' + esc(productName(log)) + '">' + esc(productName(log)) + '</strong>' +
                    '<small>SKU: ' + esc(p ? p.sku : '-') + '</small>' +
                    (p && p.variant_name ? '<small>Varian: ' + esc(p.variant_name) + '</small>' : '') +
                    '</div></div></td>' +
                '<td><span class="pq-request-no">' + esc(req.request_no || '-') + '</span></td>' +
                '<td class="center"><span class="ph-qty">' + esc(fmt(log.qty_produced)) + ' pcs</span></td>' +
                '<td><div class="ph-user"><span class="ph-avatar">' + esc(initials(userName(log))) + '</span>' +
                    '<span>' + esc(userName(log)) + '</span></div></td>' +
                '<td>' + notes + '</td>' +
                '<td class="center"><button type="button" class="ph-action" data-id="' + log.id + '">Detail</button></td>' +
            '</tr>';
    }

    function render() {

        els.error.hidden = true;
        els.empty.hidden = true;
        els.footer.hidden = true;
        els.wrapper.hidden = false;
        els.reset.hidden = !hasFilter();

        const invalid = rangeInvalid();
        els.rangeErr.hidden = !invalid;
        els.dateFrom.classList.toggle('invalid', invalid);
        els.dateTo.classList.toggle('invalid', invalid);

        if (state.loading) {
            els.body.innerHTML = skeletonRows();
            els.count.textContent = 'Memuat...';
            renderStats([]);
            return;
        }

        if (state.failed) {
            els.wrapper.hidden = true;
            els.error.hidden = false;
            els.count.textContent = '0 sesi';
            renderStats([]);
            return;
        }

        const filtered = sortLogs(getFiltered());

        renderStats(filtered);
        els.count.textContent = filtered.length + ' sesi';

        if (!filtered.length) {
            els.wrapper.hidden = true;
            els.empty.hidden = false;
            els.emptyTxt.textContent = invalid
                ? 'Rentang tanggal tidak valid. Perbaiki tanggal awal dan akhir.'
                : (state.logs.length
                    ? 'Tidak ada catatan yang sesuai dengan pencarian atau filter.'
                    : 'Belum ada catatan produksi.');
            els.emptyRst.hidden = !hasFilter();
            return;
        }

        const pages = Math.ceil(filtered.length / PH_CONFIG.perPage);
        if (state.page > pages) state.page = pages;

        const start = (state.page - 1) * PH_CONFIG.perPage;
        const slice = filtered.slice(start, start + PH_CONFIG.perPage);

        els.body.innerHTML = slice.map(function (log, i) {
            return rowHtml(log, start + i + 1);
        }).join('');

        els.footer.hidden = false;
        els.info.textContent = 'Menampilkan ' + (start + 1) + '-' + (start + slice.length) +
            ' dari ' + filtered.length + ' sesi';

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


    // ==========================================
    // MODAL DETAIL (hanya baca)
    // ==========================================

    const D = {
        overlay:  $('phdOverlay'),
        requestNo: $('phdRequestNo'),
        thumb:    $('phdThumb'),
        name:     $('phdName'),
        variant:  $('phdVariant'),
        request:  $('phdRequest'),
        sku:      $('phdSku'),
        date:     $('phdDate'),
        qty:      $('phdQty'),
        user:     $('phdUser'),
        notes:    $('phdNotes'),
        loading:  $('phdLoading'),
        error:    $('phdError'),
        materials: $('phdMaterials'),
        retry:    $('phdRetry'),
        close:    $('phdClose'),
        done:     $('phdDone')
    };

    let opener = null;
    let detailToken = 0;
    let detailLog = null;

    function findLog(id) {
        return state.logs.find(function (log) { return log.id === id; });
    }

    function openDetail(id, trigger) {

        const log = findLog(id);
        if (!log) return;

        detailLog = log;
        opener = trigger || document.activeElement;

        const p = productOf(log);
        const req = log.production_request || {};

        D.requestNo.textContent = req.request_no || '-';
        D.thumb.innerHTML = thumbInner(log);
        D.name.textContent = productName(log);
        D.variant.textContent = 'Variasi: ' + (p ? p.variant_name : '-');
        D.request.textContent = req.request_no || '-';
        D.sku.textContent = p ? p.sku : '-';
        D.date.textContent = fmtDate(dateKey(log));
        D.qty.textContent = fmt(log.qty_produced) + ' pcs';
        D.user.textContent = userName(log);
        D.notes.textContent = log.notes || '\u2014';

        D.overlay.hidden = false;
        document.body.style.overflow = 'hidden';
        D.done.focus();

        loadMaterials();
    }

    async function loadMaterials() {

        const token = ++detailToken;

        D.loading.hidden = false;
        D.error.hidden = true;
        D.materials.hidden = true;

        try {
            const list = await api.getProductionLogMaterials(detailLog.id);
            if (token !== detailToken) return;

            D.loading.hidden = true;
            D.materials.hidden = false;

            if (!list.length) {
                D.materials.innerHTML =
                    '<p class="ph-empty-line">Belum ada data penggunaan bahan baku untuk sesi ini.</p>';
                return;
            }

            D.materials.innerHTML =
                '<div class="ph-mat-wrap"><table class="ph-mat">' +
                '<thead><tr><th>Bahan Baku</th><th>Satuan</th><th class="num">Jumlah Digunakan</th><th>Tanggal Pakai</th></tr></thead><tbody>' +
                list.map(function (m) {
                    const raw = m.raw_material || {};
                    return '<tr>' +
                        '<td><strong>' + esc(raw.name || '-') + '</strong>' +
                            (raw.code ? '<small>' + esc(raw.code) + '</small>' : '') + '</td>' +
                        '<td>' + esc(raw.unit || '-') + '</td>' +
                        '<td class="num">' + esc(fmt(m.qty_used)) + '</td>' +
                        '<td>' + esc(fmtDate(m.usage_date)) + '</td>' +
                        '</tr>';
                }).join('') +
                '</tbody></table></div>';

        } catch (error) {
            if (token !== detailToken) return;
            D.loading.hidden = true;
            D.error.hidden = false;
        }
    }

    function closeDetail() {
        detailToken++;
        D.overlay.hidden = true;
        document.body.style.overflow = '';
        if (opener && opener.focus && document.contains(opener)) opener.focus();
        opener = null;
    }

    D.close.addEventListener('click', closeDetail);
    D.done.addEventListener('click', closeDetail);
    D.retry.addEventListener('click', loadMaterials);

    D.overlay.addEventListener('mousedown', function (event) {
        if (event.target === D.overlay) closeDetail();
    });

    D.overlay.addEventListener('keydown', function (event) {

        if (event.key === 'Escape') {
            closeDetail();
            return;
        }

        if (event.key !== 'Tab') return;

        const list = Array.prototype.slice.call(
            D.overlay.querySelectorAll('button:not([disabled])')
        ).filter(function (el) { return el.offsetParent !== null; });

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
        }, PH_CONFIG.searchDelay);
    });

    els.dateFrom.addEventListener('change', function () {
        state.dateFrom = els.dateFrom.value;
        state.page = 1;
        render();
    });

    els.dateTo.addEventListener('change', function () {
        state.dateTo = els.dateTo.value;
        state.page = 1;
        render();
    });

    els.product.addEventListener('change', function () {
        state.product = els.product.value;
        state.page = 1;
        render();
    });

    els.user.addEventListener('change', function () {
        state.user = els.user.value;
        state.page = 1;
        render();
    });

    function resetFilters() {
        state.search = '';
        state.dateFrom = '';
        state.dateTo = '';
        state.product = '';
        state.user = '';
        state.page = 1;
        els.search.value = '';
        els.dateFrom.value = '';
        els.dateTo.value = '';
        els.product.value = '';
        els.user.value = '';
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
        const button = event.target.closest('button[data-id]');
        if (button) openDetail(Number(button.getAttribute('data-id')), button);
    });

    els.retry.addEventListener('click', loadData);


    // ==========================================
    // MUAT DATA
    // ==========================================

    async function loadData() {

        state.loading = true;
        state.failed = false;
        render();

        try {
            state.logs = await api.getProductionLogs();
            fillFilters();
        } catch (error) {
            state.logs = [];
            state.failed = true;
        }

        state.loading = false;
        render();
    }

    els.demo.hidden = !PH_CONFIG.isDemo;

    loadData();

})();
</script>

@endpush