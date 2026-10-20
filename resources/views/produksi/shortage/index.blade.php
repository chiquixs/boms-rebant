@extends('layouts.produksi')

@section('title', 'Material Shortage - REBANT BOMS')

@section('content')

{{-- =========================
    PAGE HEADER
========================= --}}

<div class="pq-page-header">

    <div class="pq-page-header-text">
        <h1>Material Shortage List</h1>
        <p>Pantau kekurangan bahan baku yang menghambat produksi</p>
    </div>

    <button type="button" class="ms-primary" id="msCreatePo">
        <svg width="18" height="18" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 5v14"/>
            <path d="M5 12h14"/>
        </svg>
        <span>Buat Purchase Order</span>
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
            <span class="pq-stat-label">Total Shortages</span>
        </div>
        <strong class="pq-stat-value" id="msStatTotal">-</strong>
        <span class="ms-stat-note">Seluruh laporan kekurangan bahan</span>
    </div>

    <div class="pq-stat waiting">
        <div class="pq-stat-head">
            <div class="pq-stat-icon">
                <svg width="20" height="20" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 3 2 20h20L12 3Z"/>
                    <path d="M12 10v4"/>
                    <path d="M12 17h.01"/>
                </svg>
            </div>
            <span class="pq-stat-label">Open</span>
        </div>
        <strong class="pq-stat-value" id="msStatOpen">-</strong>
        <span class="ms-stat-note">Masih perlu ditangani</span>
    </div>

    <div class="pq-stat done">
        <div class="pq-stat-head">
            <div class="pq-stat-icon">
                <svg width="20" height="20" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="m8 12 3 3 5-6"/>
                </svg>
            </div>
            <span class="pq-stat-label">Resolved</span>
        </div>
        <strong class="pq-stat-value" id="msStatResolved">-</strong>
        <span class="ms-stat-note">Sudah ditangani</span>
    </div>

    <div class="pq-stat critical">
        <div class="pq-stat-head">
            <div class="pq-stat-icon">
                <svg width="20" height="20" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 7v6"/>
                    <path d="M12 16.5h.01"/>
                </svg>
            </div>
            <span class="pq-stat-label">Critical Materials</span>
        </div>
        <strong class="pq-stat-value" id="msStatCritical">-</strong>
        <span class="ms-stat-note">Bahan baku dengan risiko tinggi</span>
    </div>

</div>


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
            id="msSearch"
            placeholder="Cari production request no atau bahan baku..."
            autocomplete="off"
            aria-label="Cari production request no, nama bahan baku, atau kode bahan baku"
        >
    </label>

    <select class="pq-select" id="msFilterStatus" aria-label="Filter status">
        <option value="">All Status</option>
        <option value="OPEN">OPEN</option>
        <option value="RESOLVED">RESOLVED</option>
    </select>

    <select class="pq-select" id="msFilterMaterial" aria-label="Filter bahan baku">
        <option value="">All Material</option>
    </select>

    <button type="button" class="pq-reset" id="msReset" hidden>
        Reset Filters
    </button>

</div>


{{-- =========================
    TABLE
========================= --}}

<div class="pq-card">

    <div class="pq-card-header">
        <h2>Daftar Kekurangan Bahan</h2>
        <span class="pq-count" id="msCount">0 laporan</span>
    </div>


    <div class="pq-table-wrapper" id="msTableWrapper">

        <table class="pq-table">

            <caption class="pq-sr-only">Daftar laporan kekurangan bahan baku</caption>

            <thead>
                <tr>
                    <th>Production Request No</th>
                    <th>Bahan Baku</th>
                    <th class="center">Qty Needed</th>
                    <th class="center">Qty Available</th>
                    <th class="center">Shortage</th>
                    <th class="center">Status</th>
                    <th class="center">Aksi</th>
                </tr>
            </thead>

            <tbody id="msTableBody"></tbody>

        </table>

    </div>


    {{-- Empty state --}}
    <div class="pq-state" id="msEmpty" hidden>
        <div class="pq-state-icon">
            <svg width="24" height="24" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="11" cy="11" r="7"/>
                <path d="m20 20-3.5-3.5"/>
            </svg>
        </div>
        <h3>Tidak ada laporan ditemukan</h3>
        <p id="msEmptyText">Belum ada laporan kekurangan bahan yang sesuai dengan pencarian atau filter.</p>
        <button type="button" id="msEmptyReset">Reset Filters</button>
    </div>


    {{-- Error state --}}
    <div class="pq-state error" id="msError" hidden>
        <div class="pq-state-icon">
            <svg width="24" height="24" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="12" cy="12" r="9"/>
                <path d="M12 7v6"/>
                <path d="M12 16.5h.01"/>
            </svg>
        </div>
        <h3>Data gagal dimuat</h3>
        <p>Terjadi kendala saat mengambil daftar kekurangan bahan. Coba muat ulang.</p>
        <button type="button" id="msRetry">Coba lagi</button>
    </div>


    <div class="pq-footer" id="msFooter" hidden>
        <span id="msInfo" aria-live="polite"></span>
        <div class="pq-pagination" id="msPagination"></div>
    </div>

</div>


<div class="pq-toast" id="msToast" role="status" aria-live="polite"></div>


{{-- =========================
    MODAL: DETAIL SHORTAGE
========================= --}}

<div class="pe-overlay" id="msdOverlay" hidden>

    <div class="pe-modal" role="dialog" aria-modal="true" aria-labelledby="msdTitle">

        <div class="pe-header">

            <div class="pe-header-icon">
                <svg width="22" height="22" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M12 3 2 20h20L12 3Z"/>
                    <path d="M12 10v4"/>
                    <path d="M12 17h.01"/>
                </svg>
            </div>

            <div class="pe-header-text">
                <h2 id="msdTitle">Detail Shortage</h2>
                <p>Production Request <strong id="msdRequestNo">-</strong></p>
            </div>

            <button type="button" class="pe-close" id="msdClose" aria-label="Tutup">
                <svg width="18" height="18" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6 6 18"/>
                    <path d="m6 6 12 12"/>
                </svg>
            </button>

        </div>


        <div class="pe-body">

            {{-- Loading --}}
            <div id="msdLoading">
                <span class="pq-skeleton pe-skel"></span>
                <span class="pq-skeleton pe-skel"></span>
                <span class="pq-skeleton pe-skel"></span>
                <span class="pq-skeleton pe-skel short"></span>
            </div>

            {{-- Error --}}
            <div class="pe-error-box" id="msdError" hidden>
                <strong>Detail shortage gagal dimuat</strong>
                <p>Terjadi kendala saat mengambil data laporan kekurangan bahan.</p>
                <button type="button" class="pe-btn pe-btn-secondary" id="msdRetry">Coba lagi</button>
            </div>

            {{-- Isi --}}
            <div id="msdContent" hidden>

                <section class="pe-section">

                    <div class="ms-grid">

                        <div class="ms-field">
                            <span>Bahan Baku</span>
                            <strong id="msdMaterial">-</strong>
                            <small id="msdCode">-</small>
                        </div>

                        <div class="ms-field">
                            <span>Status</span>
                            <b class="pq-badge" id="msdStatus">-</b>
                        </div>

                        <div class="ms-field">
                            <span>Satuan</span>
                            <strong id="msdUnit">-</strong>
                        </div>

                        <div class="ms-field">
                            <span>Qty Needed</span>
                            <strong id="msdNeeded">-</strong>
                        </div>

                        <div class="ms-field">
                            <span>Qty Available (saat laporan dibuat)</span>
                            <strong id="msdAvailable">-</strong>
                        </div>

                        <div class="ms-field">
                            <span>Shortage</span>
                            <strong class="ms-short" id="msdShortage">-</strong>
                        </div>

                        <div class="ms-field">
                            <span>Dilaporkan</span>
                            <strong id="msdReported">-</strong>
                        </div>

                        <div class="ms-field">
                            <span>Diselesaikan</span>
                            <strong id="msdResolved">-</strong>
                        </div>

                    </div>

                </section>

                <p class="ms-note">
                    Qty Available adalah jumlah yang tersedia saat laporan dibuat,
                    bukan stok terkini bahan baku.
                </p>

            </div>

        </div>


        <div class="pe-footer ms-footer" id="msdFooter">
            <button type="button" class="pe-btn pe-btn-secondary" id="msdDone">Tutup</button>
            <button type="button" class="pe-btn pe-btn-primary" id="msdCreatePo" hidden>Buat PO</button>
        </div>

    </div>

</div>


{{-- =========================
    MODAL: BUAT PURCHASE ORDER (rancangan awal)
========================= --}}

<div class="pe-overlay" id="mspOverlay" hidden>

    <div class="pe-modal" role="dialog" aria-modal="true" aria-labelledby="mspTitle">

        <div class="pe-header">

            <div class="pe-header-icon">
                <svg width="22" height="22" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M6 3h9l4 4v14H6V3Z"/>
                    <path d="M14 3v5h5"/>
                    <path d="M9 13h7"/>
                    <path d="M9 17h7"/>
                </svg>
            </div>

            <div class="pe-header-text">
                <h2 id="mspTitle">Buat Purchase Order</h2>
                <p>Pilih shortage yang akan dipesan ke supplier</p>
            </div>

            <button type="button" class="pe-close" id="mspClose" aria-label="Tutup">
                <svg width="18" height="18" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6 6 18"/>
                    <path d="m6 6 12 12"/>
                </svg>
            </button>

        </div>


        <div class="pe-body">

            <p class="ms-note">
                Ini draf awal. Membuat PO tidak mengubah status shortage menjadi RESOLVED
                dan tidak mengubah stok. Supplier, approval, dan pengiriman email
                masih menunggu backend.
            </p>

            <div class="pe-form-error" id="mspError" hidden></div>

            <section class="pe-section">
                <h3><span>1.</span> Supplier</h3>
                <p class="pe-section-desc">Satu PO hanya untuk satu supplier.</p>

                <select class="pq-select ms-full" id="mspSupplier" aria-label="Supplier">
                    <option value="">Pilih supplier</option>
                </select>
            </section>

            <section class="pe-section">
                <h3><span>2.</span> Item Shortage</h3>
                <p class="pe-section-desc">
                    Centang shortage OPEN yang ingin dipesan dan atur jumlah pesanan.
                    <span id="mspSelected">0 item dipilih</span>
                </p>

                <div class="ms-items" id="mspItems"></div>
            </section>

            <section class="pe-section">
                <h3><span>3.</span> Catatan</h3>
                <p class="pe-section-desc">Opsional. Disimpan ke kolom notes pada PO.</p>

                <textarea class="ms-textarea" id="mspNotes" rows="3"
                          placeholder="Catatan untuk supplier atau tim pengadaan..."></textarea>
            </section>

            <div class="ms-confirm" id="mspConfirm" hidden></div>

        </div>


        <div class="pe-footer ms-footer">
            <button type="button" class="pe-btn pe-btn-secondary" id="mspCancel">Batal</button>
            <button type="button" class="pe-btn pe-btn-primary" id="mspSave">Simpan PO</button>
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

    const MS_CONFIG = {
        perPage: 8,
        searchDelay: 250
    };

    // Status mengikuti ERD: default OPEN. SQL belum membatasi nilai,
    // jadi status tak dikenal tetap ditampilkan apa adanya.
    const STATUS_META = {
        OPEN:     { label: 'OPEN',     cls: 'waiting' },
        RESOLVED: { label: 'RESOLVED', cls: 'done' }
    };


    // ==========================================
    // DATA SIMULASI (MOCK)
    // Hanya untuk tampilan. Bukan data database.
    // Bentuk field mengikuti material_shortages + relasinya.
    // ==========================================

    const MOCK_MATERIALS = [
        { id: 1, code: 'MDF-12',  name: 'MDF Board 12 mm', unit: 'pcs',   current_stock: 6,   safety_stock: 10,  reorder_point: 15,  is_active: true },
        { id: 2, code: 'CAT-WH',  name: 'Cat Kayu (White)', unit: 'liter', current_stock: 14,  safety_stock: 5,   reorder_point: 8,   is_active: true },
        { id: 3, code: 'PLY-09',  name: 'Plywood 9 mm',    unit: 'pcs',   current_stock: 3,   safety_stock: 8,   reorder_point: 12,  is_active: true },
        { id: 4, code: 'SKR-30',  name: 'Sekrup 3 cm',    unit: 'box',   current_stock: 900, safety_stock: 300, reorder_point: 400, is_active: true },
        { id: 5, code: 'LEM-PV',  name: 'Lem Kayu PVAC',  unit: 'kg',    current_stock: 2,   safety_stock: 6,   reorder_point: 9,   is_active: true },
        { id: 6, code: 'AMP-120', name: 'Amplas 120',     unit: 'lembar', current_stock: 120, safety_stock: 60,  reorder_point: 80,  is_active: true },
        { id: 7, code: 'ENG-01',  name: 'Engsel Pintu',   unit: 'pcs',   current_stock: 60,  safety_stock: 30,  reorder_point: 40,  is_active: true },
        { id: 8, code: 'VRN-CL',  name: 'Vernis Clear',   unit: 'liter', current_stock: 12,  safety_stock: 5,   reorder_point: 8,   is_active: true }
    ];

    const MOCK_SUPPLIERS = [
        { id: 1, name: 'CV Sumber Kayu Jaya' },
        { id: 2, name: 'PT Mitra Material Nusantara' },
        { id: 3, name: 'UD Cat & Perekat Makmur' }
    ];

    function mockShortage(id, requestNo, materialId, needed, available, status, reportedAt, resolvedAt) {
        return {
            id: id,
            production_request_id: 100 + id,
            raw_material_id: materialId,
            qty_needed: needed,
            qty_available: available,
            status: status,
            reported_at: reportedAt,
            resolved_at: resolvedAt || null,
            production_request: { id: 100 + id, request_no: requestNo },
            raw_material: MOCK_MATERIALS.find(function (m) { return m.id === materialId; })
        };
    }

    const MOCK_SHORTAGES = [
        mockShortage(1,  'PR-20261010-001', 1, 10,  6,   'OPEN',     '2026-10-10T08:15:00'),
        mockShortage(2,  'PR-20261010-002', 2, 5,   2,   'RESOLVED', '2026-10-10T09:05:00', '2026-10-11T13:30:00'),
        mockShortage(3,  'PR-20261010-003', 3, 12,  4,   'OPEN',     '2026-10-10T10:40:00'),
        mockShortage(4,  'PR-20261011-001', 4, 200, 150, 'OPEN',     '2026-10-11T08:50:00'),
        mockShortage(5,  'PR-20261011-002', 5, 8,   5,   'OPEN',     '2026-10-11T09:20:00'),
        mockShortage(6,  'PR-20261011-003', 1, 6,   2,   'OPEN',     '2026-10-11T11:10:00'),
        mockShortage(7,  'PR-20261012-001', 6, 40,  30,  'RESOLVED', '2026-10-12T08:00:00', '2026-10-12T15:45:00'),
        mockShortage(8,  'PR-20261012-002', 7, 24,  10,  'OPEN',     '2026-10-12T10:25:00'),
        mockShortage(9,  'PR-20261012-003', 8, 4,   1,   'OPEN',     '2026-10-12T13:05:00'),
        mockShortage(10, 'PR-20261013-001', 2, 10,  6,   'RESOLVED', '2026-10-13T08:30:00', '2026-10-14T09:10:00'),
        mockShortage(11, 'PR-20261013-002', 4, 100, 80,  'RESOLVED', '2026-10-13T09:45:00', '2026-10-14T10:00:00'),
        mockShortage(12, 'PR-20261013-003', 3, 8,   3,   'OPEN',     '2026-10-13T11:35:00')
    ];


    // ==========================================
    // LAPISAN DATA (API)
    // Semua akses data lewat objek ini. Saat backend siap, ganti isi
    // fungsi (mis. fetch ke endpoint proyek) tanpa menyentuh UI.
    // Simulasi: ?mock=error | empty | detailerror | savefail
    // ==========================================

    const flag = new URLSearchParams(window.location.search).get('mock');

    function wait(ms) {
        return new Promise(function (resolve) { setTimeout(resolve, ms); });
    }

    const api = {

        // Daftar laporan (material_shortages + production_requests + raw_materials)
        getMaterialShortages: async function () {
            await wait(450);
            if (flag === 'error') throw new Error('mock error');
            if (flag === 'empty') return [];
            return MOCK_SHORTAGES.map(function (row) { return Object.assign({}, row); });
        },

        getMaterialShortageDetail: async function (id) {
            await wait(300);
            if (flag === 'detailerror') throw new Error('mock detail error');
            const found = MOCK_SHORTAGES.find(function (row) { return row.id === id; });
            if (!found) throw new Error('not found');
            return Object.assign({}, found);
        },

        // raw_materials yang aktif (untuk filter All Material)
        getRawMaterials: async function () {
            await wait(200);
            return MOCK_MATERIALS.filter(function (m) { return m.is_active; });
        },

        // Modul supplier belum ada di frontend. Data ini hanya placeholder.
        getSuppliers: async function () {
            await wait(200);
            return MOCK_SUPPLIERS.slice();
        },

        // Menyimpan purchase_orders + purchase_order_items. Simulasi saja.
        createPurchaseOrderFromShortage: async function (payload) {
            await wait(600);
            if (flag === 'savefail') throw new Error('mock save error');
            return { simulated: true, payload: payload };
        }
    };


    // ==========================================
    // STATE + ELEMEN
    // ==========================================

    const state = {
        rows: [],
        materials: [],
        suppliers: [],
        search: '',
        status: '',
        material: '',
        page: 1,
        loading: true,
        failed: false
    };

    function $(id) { return document.getElementById(id); }

    const els = {
        statTotal:    $('msStatTotal'),
        statOpen:     $('msStatOpen'),
        statResolved: $('msStatResolved'),
        statCritical: $('msStatCritical'),

        search:   $('msSearch'),
        status:   $('msFilterStatus'),
        material: $('msFilterMaterial'),
        reset:    $('msReset'),

        count:    $('msCount'),
        wrapper:  $('msTableWrapper'),
        body:     $('msTableBody'),
        empty:    $('msEmpty'),
        emptyTxt: $('msEmptyText'),
        emptyRst: $('msEmptyReset'),
        error:    $('msError'),
        retry:    $('msRetry'),

        footer:     $('msFooter'),
        info:       $('msInfo'),
        pagination: $('msPagination'),

        toast:    $('msToast'),
        createPo: $('msCreatePo')
    };


    // ==========================================
    // HELPER
    // ==========================================

    const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    function esc(value) {
        return String(value == null ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function fmtNum(n) {
        return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(n);
    }

    function fmtQty(n, unit) {
        return fmtNum(n) + ' ' + (unit || '');
    }

    function pad(n) { return n < 10 ? '0' + n : String(n); }

    function fmtDate(value) {
        if (!value) return '';
        const d = new Date(value);
        if (isNaN(d.getTime())) return '';
        return d.getDate() + ' ' + MONTHS[d.getMonth()] + ' ' + d.getFullYear() +
            ', ' + pad(d.getHours()) + ':' + pad(d.getMinutes());
    }

    // Shortage selalu dihitung: qty_needed - qty_available.
    // Hasil negatif ditampilkan 0 dan ditandai "perlu diperiksa".
    function shortageOf(row) {
        const diff = Number(row.qty_needed) - Number(row.qty_available);
        if (isNaN(diff)) return { value: 0, invalid: true };
        return { value: Math.max(0, diff), invalid: diff < 0 };
    }

    function unitOf(row) {
        return row.raw_material ? row.raw_material.unit : '';
    }

    // ATURAN USULAN (belum resmi, perlu dikonfirmasi tim):
    // bahan baku dianggap kritis bila punya shortage OPEN dan
    // current_stock <= safety_stock. ERD belum punya field khusus.
    function isCritical(row) {
        const m = row.raw_material;
        return row.status === 'OPEN' && !!m &&
            Number(m.current_stock) <= Number(m.safety_stock);
    }

    function statusMeta(status) {
        return STATUS_META[status] || { label: status || '-', cls: 'normal' };
    }

    let toastTimer = null;

    function toast(message) {
        els.toast.textContent = message;
        els.toast.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(function () {
            els.toast.classList.remove('show');
        }, 4200);
    }

    function findRow(id) {
        return state.rows.find(function (row) { return row.id === id; });
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
            if (settings.onClose) settings.onClose();
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
    // STATISTIK
    // Dihitung dari SELURUH laporan (tidak ikut filter).
    // ==========================================

    function renderStats() {

        const rows = state.rows;
        const criticalMaterials = {};

        rows.forEach(function (row) {
            if (isCritical(row)) criticalMaterials[row.raw_material_id] = true;
        });

        els.statTotal.textContent = state.failed ? '-' : rows.length;
        els.statOpen.textContent = state.failed ? '-' :
            rows.filter(function (r) { return r.status === 'OPEN'; }).length;
        els.statResolved.textContent = state.failed ? '-' :
            rows.filter(function (r) { return r.status === 'RESOLVED'; }).length;
        els.statCritical.textContent = state.failed ? '-' : Object.keys(criticalMaterials).length;
    }


    // ==========================================
    // FILTER
    // ==========================================

    function hasFilter() {
        return !!(state.search.trim() || state.status || state.material);
    }

    function getFiltered() {

        const q = state.search.trim().toLowerCase();

        return state.rows.filter(function (row) {

            if (state.status && row.status !== state.status) return false;
            if (state.material && String(row.raw_material_id) !== state.material) return false;

            if (q) {
                const haystack = [
                    row.production_request ? row.production_request.request_no : '',
                    row.raw_material ? row.raw_material.name : '',
                    row.raw_material ? row.raw_material.code : ''
                ].join(' ').toLowerCase();

                if (haystack.indexOf(q) === -1) return false;
            }

            return true;
        });
    }


    // ==========================================
    // RENDER TABEL
    // ==========================================

    function skeletonRows() {
        let html = '';
        for (let i = 0; i < 5; i++) {
            html += '<tr>';
            for (let c = 0; c < 7; c++) {
                html += '<td><span class="pq-skeleton"></span></td>';
            }
            html += '</tr>';
        }
        return html;
    }

    function rowHtml(row) {

        const unit = unitOf(row);
        const short = shortageOf(row);
        const meta = statusMeta(row.status);
        const material = row.raw_material || {};
        const isOpen = row.status === 'OPEN';

        const warn = short.invalid
            ? '<span class="ms-warn" title="Qty Available melebihi Qty Needed. Data perlu diperiksa.">Periksa data</span>'
            : '';

        const poButton = isOpen
            ? '<button type="button" class="ms-action primary" data-act="po" data-id="' + row.id + '">Buat PO</button>'
            : '';

        return '' +
            '<tr>' +
                '<td><span class="pq-request-no">' + esc(row.production_request ? row.production_request.request_no : '-') + '</span></td>' +
                '<td><span class="pq-product">' + esc(material.name || '-') +
                    '<small>' + esc(material.code || '') + '</small></span></td>' +
                '<td class="center pq-qty">' + esc(fmtQty(row.qty_needed, unit)) + '</td>' +
                '<td class="center pq-qty">' + esc(fmtQty(row.qty_available, unit)) + '</td>' +
                '<td class="center"><strong class="ms-short' + (isOpen ? ' open' : '') + '">' +
                    esc(fmtQty(short.value, unit)) + '</strong>' + warn + '</td>' +
                '<td class="center"><span class="pq-badge ' + meta.cls + '">' + esc(meta.label) + '</span></td>' +
                '<td class="center"><div class="ms-actions">' +
                    '<button type="button" class="ms-action outline" data-act="detail" data-id="' + row.id + '">Detail</button>' +
                    poButton +
                '</div></td>' +
            '</tr>';
    }

    function render() {

        els.error.hidden = true;
        els.empty.hidden = true;
        els.footer.hidden = true;
        els.wrapper.hidden = false;
        els.reset.hidden = !hasFilter();

        if (state.loading) {
            els.body.innerHTML = skeletonRows();
            els.count.textContent = 'Memuat...';
            return;
        }

        if (state.failed) {
            els.wrapper.hidden = true;
            els.error.hidden = false;
            els.count.textContent = '0 laporan';
            return;
        }

        const filtered = getFiltered();
        els.count.textContent = filtered.length + ' laporan';

        if (!filtered.length) {
            els.wrapper.hidden = true;
            els.empty.hidden = false;
            els.emptyTxt.textContent = state.rows.length
                ? 'Tidak ada laporan yang sesuai dengan pencarian atau filter.'
                : 'Belum ada laporan kekurangan bahan baku.';
            els.emptyRst.hidden = !hasFilter();
            return;
        }

        const pages = Math.ceil(filtered.length / MS_CONFIG.perPage);
        if (state.page > pages) state.page = pages;

        const start = (state.page - 1) * MS_CONFIG.perPage;
        const slice = filtered.slice(start, start + MS_CONFIG.perPage);

        els.body.innerHTML = slice.map(rowHtml).join('');

        els.footer.hidden = false;
        els.info.textContent = 'Menampilkan ' + (start + 1) + '-' + (start + slice.length) +
            ' dari ' + filtered.length + ' laporan';

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

    function fillMaterialFilter() {
        els.material.innerHTML = '<option value="">All Material</option>' +
            state.materials.map(function (m) {
                return '<option value="' + m.id + '">' + esc(m.name) + '</option>';
            }).join('');
    }


    // ==========================================
    // MODAL DETAIL
    // ==========================================

    const D = {
        overlay:   $('msdOverlay'),
        requestNo: $('msdRequestNo'),
        loading:   $('msdLoading'),
        error:     $('msdError'),
        content:   $('msdContent'),
        material:  $('msdMaterial'),
        code:      $('msdCode'),
        status:    $('msdStatus'),
        unit:      $('msdUnit'),
        needed:    $('msdNeeded'),
        available: $('msdAvailable'),
        shortage:  $('msdShortage'),
        reported:  $('msdReported'),
        resolved:  $('msdResolved'),
        createPo:  $('msdCreatePo'),
        retry:     $('msdRetry'),
        close:     $('msdClose'),
        done:      $('msdDone')
    };

    const detailModal = createModal(D.overlay, {
        onClose: function () { detailToken++; }
    });

    let detailToken = 0;
    let detailId = null;

    async function openDetail(id) {

        detailId = id;
        const token = ++detailToken;
        const known = findRow(id);

        D.requestNo.textContent = known && known.production_request
            ? known.production_request.request_no : '-';
        D.loading.hidden = false;
        D.error.hidden = true;
        D.content.hidden = true;
        D.createPo.hidden = true;

        if (D.overlay.hidden) detailModal.open();

        try {
            const item = await api.getMaterialShortageDetail(id);
            if (token !== detailToken) return;
            fillDetail(item);
        } catch (error) {
            if (token !== detailToken) return;
            D.loading.hidden = true;
            D.error.hidden = false;
        }
    }

    function fillDetail(item) {

        const unit = unitOf(item);
        const short = shortageOf(item);
        const meta = statusMeta(item.status);
        const material = item.raw_material || {};

        D.requestNo.textContent = item.production_request ? item.production_request.request_no : '-';
        D.material.textContent = material.name || '-';
        D.code.textContent = material.code ? 'Kode ' + material.code : '';
        D.status.className = 'pq-badge ' + meta.cls;
        D.status.textContent = meta.label;
        D.unit.textContent = unit || '-';
        D.needed.textContent = fmtQty(item.qty_needed, unit);
        D.available.textContent = fmtQty(item.qty_available, unit);
        D.shortage.textContent = fmtQty(short.value, unit) + (short.invalid ? ' (data perlu diperiksa)' : '');
        D.shortage.className = 'ms-short' + (item.status === 'OPEN' ? ' open' : '');
        D.reported.textContent = fmtDate(item.reported_at) || '-';

        if (item.resolved_at) {
            D.resolved.textContent = fmtDate(item.resolved_at);
        } else {
            D.resolved.textContent = item.status === 'RESOLVED' ? 'Waktu tidak tercatat' : 'Belum selesai';
        }

        D.createPo.hidden = item.status !== 'OPEN';

        D.loading.hidden = true;
        D.error.hidden = true;
        D.content.hidden = false;
    }

    D.close.addEventListener('click', detailModal.close);
    D.done.addEventListener('click', detailModal.close);
    D.retry.addEventListener('click', function () { openDetail(detailId); });

    D.createPo.addEventListener('click', function () {
        const id = detailId;
        detailModal.close();
        openPo([id]);
    });


    // ==========================================
    // MODAL BUAT PURCHASE ORDER (rancangan awal)
    // ==========================================

    const P = {
        overlay:  $('mspOverlay'),
        error:    $('mspError'),
        supplier: $('mspSupplier'),
        items:    $('mspItems'),
        selected: $('mspSelected'),
        notes:    $('mspNotes'),
        confirm:  $('mspConfirm'),
        save:     $('mspSave'),
        cancel:   $('mspCancel'),
        close:    $('mspClose')
    };

    const po = { saving: false, confirming: false };

    const poModal = createModal(P.overlay, {
        backdrop: false,
        canClose: function () { return !po.saving; }
    });

    function openPo(preselectedIds) {

        const open = state.rows.filter(function (row) { return row.status === 'OPEN'; });

        if (!open.length) {
            toast('Tidak ada shortage berstatus OPEN yang bisa dipesan.');
            return;
        }

        const chosen = preselectedIds || [];

        P.items.innerHTML = open.map(function (row) {

            const short = shortageOf(row);
            const checked = chosen.indexOf(row.id) !== -1;
            const material = row.raw_material || {};

            return '' +
                '<div class="ms-item' + (checked ? ' on' : '') + '" data-id="' + row.id + '">' +
                    '<label class="ms-item-main">' +
                        '<input type="checkbox" class="ms-check"' + (checked ? ' checked' : '') + '>' +
                        '<span class="ms-item-text">' +
                            '<strong>' + esc(material.name || '-') + '</strong>' +
                            '<small>' + esc(row.production_request ? row.production_request.request_no : '-') +
                                ' &middot; shortage ' + esc(fmtQty(short.value, unitOf(row))) + '</small>' +
                        '</span>' +
                    '</label>' +
                    '<div class="pe-input-group">' +
                        '<input type="number" class="ms-qty" min="0" step="any" value="' + short.value + '"' +
                            (checked ? '' : ' disabled') + ' aria-label="Jumlah dipesan">' +
                        '<span>' + esc(unitOf(row)) + '</span>' +
                    '</div>' +
                '</div>';
        }).join('');

        P.supplier.innerHTML = '<option value="">Pilih supplier</option>' +
            state.suppliers.map(function (s) {
                return '<option value="' + s.id + '">' + esc(s.name) + '</option>';
            }).join('');

        P.notes.value = '';
        hidePoError();
        resetConfirm();
        updateSelected();
        poModal.open();
    }

    function updateSelected() {
        const n = P.items.querySelectorAll('.ms-check:checked').length;
        P.selected.textContent = n + ' item dipilih';
    }

    function resetConfirm() {
        po.confirming = false;
        P.confirm.hidden = true;
        P.save.textContent = 'Simpan PO';
    }

    function showPoError(message) {
        P.error.textContent = message;
        P.error.hidden = false;
        P.overlay.querySelector('.pe-body').scrollTop = 0;
    }

    function hidePoError() {
        P.error.hidden = true;
    }

    // Mengumpulkan data sesuai tabel purchase_orders + purchase_order_items.
    // po_no, status, created_by, dan trigger_type diisi/ditetapkan backend.
    function buildPayload() {

        const items = [];
        let valid = true;

        P.items.querySelectorAll('.ms-item').forEach(function (node) {

            const check = node.querySelector('.ms-check');
            const qtyInput = node.querySelector('.ms-qty');
            const group = qtyInput.parentNode;

            group.classList.remove('invalid');
            if (!check.checked) return;

            const row = findRow(Number(node.getAttribute('data-id')));
            const qty = Number(qtyInput.value);

            if (!qtyInput.value || isNaN(qty) || qty <= 0) {
                group.classList.add('invalid');
                valid = false;
                return;
            }

            items.push({
                raw_material_id: row.raw_material_id,
                material_shortage_id: row.id,
                qty_suggested: shortageOf(row).value,
                qty_ordered: qty
            });
        });

        return {
            valid: valid,
            payload: {
                purchase_order: {
                    supplier_id: P.supplier.value ? Number(P.supplier.value) : null,
                    trigger_type: null,
                    notes: P.notes.value.trim() || null
                },
                items: items
            }
        };
    }

    async function savePo() {

        hidePoError();

        const built = buildPayload();
        const data = built.payload;

        if (!data.purchase_order.supplier_id) {
            resetConfirm();
            showPoError('Pilih supplier terlebih dahulu.');
            return;
        }

        if (!data.items.length) {
            resetConfirm();
            showPoError('Pilih minimal satu item shortage.');
            return;
        }

        if (!built.valid) {
            resetConfirm();
            showPoError('Jumlah dipesan harus lebih dari 0 pada setiap item yang dipilih.');
            return;
        }

        // Langkah 1: minta konfirmasi sebelum menyimpan
        if (!po.confirming) {
            po.confirming = true;
            P.confirm.textContent = 'Simpan draf PO untuk ' + data.items.length +
                ' item? Status shortage tidak akan berubah.';
            P.confirm.hidden = false;
            P.save.textContent = 'Ya, simpan PO';
            P.confirm.scrollIntoView({ block: 'nearest' });
            return;
        }

        // Langkah 2: simpan
        po.saving = true;
        P.save.disabled = true;
        P.cancel.disabled = true;
        P.save.textContent = 'Menyimpan...';

        try {
            const result = await api.createPurchaseOrderFromShortage(data);

            po.saving = false;
            poModal.close();

            document.dispatchEvent(new CustomEvent('material-shortage:po-created', {
                detail: result.payload
            }));

            toast('Simulasi: draf PO untuk ' + data.items.length +
                ' item sudah disiapkan. Belum tersimpan ke database dan status shortage tidak berubah.');

        } catch (error) {
            po.saving = false;
            resetConfirm();
            showPoError('PO gagal disimpan. Coba lagi.');
        } finally {
            po.saving = false;
            P.save.disabled = false;
            P.cancel.disabled = false;
            if (!po.confirming) P.save.textContent = 'Simpan PO';
        }
    }

    P.items.addEventListener('change', function (event) {

        const node = event.target.closest('.ms-item');
        if (!node) return;

        if (event.target.classList.contains('ms-check')) {
            const on = event.target.checked;
            node.classList.toggle('on', on);
            node.querySelector('.ms-qty').disabled = !on;
            updateSelected();
        }

        resetConfirm();
    });

    P.items.addEventListener('input', resetConfirm);
    P.supplier.addEventListener('change', resetConfirm);
    P.notes.addEventListener('input', resetConfirm);

    P.save.addEventListener('click', savePo);
    P.cancel.addEventListener('click', poModal.close);
    P.close.addEventListener('click', poModal.close);

    els.createPo.addEventListener('click', function () { openPo([]); });


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
        }, MS_CONFIG.searchDelay);
    });

    els.status.addEventListener('change', function () {
        state.status = els.status.value;
        state.page = 1;
        render();
    });

    els.material.addEventListener('change', function () {
        state.material = els.material.value;
        state.page = 1;
        render();
    });

    function resetFilters() {
        state.search = '';
        state.status = '';
        state.material = '';
        state.page = 1;
        els.search.value = '';
        els.status.value = '';
        els.material.value = '';
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
        if (!button) return;

        const id = Number(button.getAttribute('data-id'));

        if (button.getAttribute('data-act') === 'detail') openDetail(id);
        if (button.getAttribute('data-act') === 'po') openPo([id]);
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
            const results = await Promise.all([
                api.getMaterialShortages(),
                api.getRawMaterials(),
                api.getSuppliers()
            ]);

            // OPEN di atas, lalu laporan terbaru lebih dulu
            state.rows = results[0].slice().sort(function (a, b) {
                if (a.status !== b.status) return a.status === 'OPEN' ? -1 : (b.status === 'OPEN' ? 1 : 0);
                return new Date(b.reported_at) - new Date(a.reported_at);
            });
            state.materials = results[1];
            state.suppliers = results[2];

            fillMaterialFilter();

        } catch (error) {
            state.rows = [];
            state.failed = true;
        }

        state.loading = false;
        renderStats();
        render();
    }

    loadData();

})();
</script>

@endpush