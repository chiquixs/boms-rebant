@extends('layouts.produksi')

@section('title', 'Production Queue - REBANT BOMS')

@section('content')

{{-- =========================
    PAGE HEADER
========================= --}}

<div class="pq-page-header">

    <div class="pq-page-header-text">
        <h1>Production Queue</h1>
        <p>Kelola antrean dan status pekerjaan produksi</p>
    </div>
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
                    <path d="M8 6h13"/>
                    <path d="M8 12h13"/>
                    <path d="M8 18h13"/>
                    <path d="M3 6h.01"/>
                    <path d="M3 12h.01"/>
                    <path d="M3 18h.01"/>
                </svg>
            </div>
            <span class="pq-stat-label">Total Requests</span>
        </div>
        <strong class="pq-stat-value" id="pqStatTotal">-</strong>
    </div>

    <div class="pq-stat waiting">
        <div class="pq-stat-head">
            <div class="pq-stat-icon">
                <svg width="20" height="20" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 7v5l3 2"/>
                </svg>
            </div>
            <span class="pq-stat-label">Waiting</span>
        </div>
        <strong class="pq-stat-value" id="pqStatWaiting">-</strong>
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
            <span class="pq-stat-label">In Process</span>
        </div>
        <strong class="pq-stat-value" id="pqStatProgress">-</strong>
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
            <span class="pq-stat-label">Completed Today</span>
        </div>
        <strong class="pq-stat-value" id="pqStatDone">-</strong>
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
            id="pqSearch"
            placeholder="Cari request no atau nama produk..."
            autocomplete="off"
            aria-label="Cari request no, nama produk, varian, atau SKU"
        >
    </label>

    <select class="pq-select" id="pqFilterSource" aria-label="Filter source">
        <option value="">Semua Source</option>
        <option value="ORDER">ORDER</option>
        <option value="MTS_MIN_STOCK">MTS-MIN-STOCK</option>
    </select>

    <select class="pq-select" id="pqFilterStatus" aria-label="Filter status">
        <option value="">Semua Status</option>
        <option value="WAITING">WAITING</option>
        <option value="IN_PROCESS">IN_PROCESS</option>
        <option value="COMPLETED">COMPLETED</option>
    </select>

    <button type="button" class="pq-reset" id="pqReset" hidden>
        Reset Filters
    </button>

</div>


{{-- =========================
    TABLE
========================= --}}

<div class="pq-card">

    <div class="pq-card-header">
        <h2>Daftar Pekerjaan Produksi</h2>
        <span class="pq-count" id="pqCount">0 request</span>
    </div>


    <div class="pq-table-wrapper" id="pqTableWrapper">

        <table class="pq-table">

            <caption class="pq-sr-only">Daftar request produksi</caption>

            <thead>
                <tr>
                    <th>Request No</th>
                    <th>Product</th>
                    <th class="center">Source</th>
                    <th class="center">Qty Requested</th>
                    <th class="center">Qty Produced</th>
                    <th class="center">Status</th>
                    <th class="center">Aksi</th>
                </tr>
            </thead>

            <tbody id="pqTableBody"></tbody>

        </table>

    </div>


    {{-- Empty state --}}
    <div class="pq-state" id="pqEmpty" hidden>
        <div class="pq-state-icon">
            <svg width="24" height="24" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="11" cy="11" r="7"/>
                <path d="m20 20-3.5-3.5"/>
            </svg>
        </div>
        <h3>Tidak ada request ditemukan</h3>
        <p id="pqEmptyText">Belum ada request produksi yang sesuai dengan pencarian atau filter.</p>
        <button type="button" id="pqEmptyReset">Reset Filters</button>
    </div>


    {{-- Error state --}}
    <div class="pq-state error" id="pqError" hidden>
        <div class="pq-state-icon">
            <svg width="24" height="24" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="12" cy="12" r="9"/>
                <path d="M12 7v6"/>
                <path d="M12 16.5h.01"/>
            </svg>
        </div>
        <h3>Data gagal dimuat</h3>
        <p>Terjadi kendala saat mengambil daftar request produksi. Coba muat ulang.</p>
        <button type="button" id="pqRetry">Coba lagi</button>
    </div>


    <div class="pq-footer" id="pqFooter" hidden>
        <span id="pqInfo" aria-live="polite"></span>
        <div class="pq-pagination" id="pqPagination"></div>
    </div>

</div>


<div class="pq-toast" id="pqToast" role="status" aria-live="polite"></div>


{{-- Modal Eksekusi Produksi & Material Usage --}}
@include('produksi.queue._execution-modal')

{{-- Modal Detail Produksi (hanya baca) --}}
@include('produksi.queue._detail-modal')

@endsection


@push('scripts')

<script>
(function () {

    'use strict';


    // ==========================================
    // KONFIGURASI
    // ==========================================

    const PQ_CONFIG = {

        perPage: 5,

        searchDelay: 250,

        // Isi dengan URL endpoint saat backend sudah siap
        // (contoh: '/api/production-requests').
        // Selama null, halaman memakai DATA SIMULASI di bawah.
        apiUrl: null
    };


    // ==========================================
    // LABEL + KELAS BADGE
    // ==========================================

    const SOURCE_META = {
        ORDER:         { label: 'ORDER',         cls: 'order' },
        MTS_MIN_STOCK: { label: 'MTS-MIN-STOCK', cls: 'mts' }
    };

    // Status mengikuti ERD: WAITING, IN_PROCESS, COMPLETED
    const STATUS_META = {
        WAITING:    { label: 'WAITING',    cls: 'waiting' },
        IN_PROCESS: { label: 'IN_PROCESS', cls: 'progress' },
        COMPLETED:  { label: 'COMPLETED',  cls: 'done' }
    };

    // Tombol aksi menyesuaikan status request
    const ACTION_META = {
    WAITING:    { key: 'start',    label: 'Mulai Produksi' },
    IN_PROCESS: { key: 'continue', label: 'Lanjutkan Produksi' },
    COMPLETED:  { key: 'detail',   label: 'Lihat Detail' }
    };


    // ==========================================
    // DATA SIMULASI (MOCK)
    // Hanya untuk tampilan. Hapus saat backend siap.
    // Nama field mengikuti kolom production_requests, products, dan product_groups.
    // ==========================================

    const MOCK_ROWS = [
        ['PR-20261010-001', 'Frame Kayu Karakter Jepang (10x30)', 'V1', 'FKGU-B-N-V1', 'ORDER', 20, 0, 'WAITING'],
        ['PR-20261010-002', 'Sign Peringatan Hitam (9x30)', 'CCTV', 'D6A-1', 'MTS_MIN_STOCK', 10, 6, 'IN_PROCESS'],
        ['PR-20261010-003', 'Quotes Black and White (15x20)', 'Leave Home', 'I9J-1', 'ORDER', 20, 20, 'COMPLETED'],
        ['PR-20261010-004', 'Sign Free Wifi (20x20)', 'Hitam Putih', 'N6A-1', 'MTS_MIN_STOCK', 30, 6, 'IN_PROCESS'],
        ['PR-20261010-005', 'Malang Tempo Dulu (30x45)', 'Alun alun', 'T29A-1', 'ORDER', 15, 0, 'WAITING'],
        ['PR-20261010-006', 'Buka - Istirahat Pastel (20x30)', 'Pink', 'O13C-1', 'MTS_MIN_STOCK', 8, 3, 'IN_PROCESS'],
        ['PR-20261010-007', 'Gantungan Gelang Kayu 2 Sisi', 'Natural', 'GG-B-N', 'ORDER', 12, 0, 'WAITING'],
        ['PR-20261010-008', 'Modern Line Art Black & White set isi 3 (20x30)', 'Cat', 'PBS3-O32-A-1', 'ORDER', 25, 25, 'COMPLETED'],
        ['PR-20261009-001', 'Basecamp Bola Internasional (9x30)', 'Juventus', 'D6E-1', 'MTS_MIN_STOCK', 40, 18, 'IN_PROCESS'],
        ['PR-20261009-002', 'Modern Art Woman set isi 3 (20x30)', 'Lady Green', 'PBS3-O32-B-1', 'ORDER', 6, 0, 'WAITING'],
        ['PR-20261009-003', 'Sign Peringatan Hitam (9x30)', 'Dilarang Merokok', 'D6A-2', 'MTS_MIN_STOCK', 30, 30, 'COMPLETED'],
        ['PR-20261009-004', 'Frame Kayu Karakter Jepang (10x30)', 'V2', 'FKGU-B-N-V2', 'ORDER', 5, 2, 'IN_PROCESS'],
        ['PR-20261009-005', 'Malang Tempo Dulu (30x45)', 'Balai Kota', 'T29A-2', 'ORDER', 10, 10, 'COMPLETED'],
        ['PR-20261009-006', 'Buka - Istirahat Pastel (20x30)', 'Hijau', 'O13C-2', 'MTS_MIN_STOCK', 16, 0, 'WAITING'],
        ['PR-20261009-007', 'Quotes Black and White (15x20)', 'Die', 'I9J-2', 'ORDER', 9, 0, 'WAITING'],
        ['PR-20261008-001', 'Sign Free Wifi (20x20)', 'Putih Hitam', 'N6A-2', 'MTS_MIN_STOCK', 24, 24, 'COMPLETED'],
        ['PR-20261008-002', 'Gantungan Gelang Kayu 2 Sisi', 'Pink', 'GG-B-P', 'ORDER', 14, 14, 'COMPLETED'],
        ['PR-20261008-003', 'Basecamp Bola Internasional (9x30)', 'Chelsea', 'D6E-2', 'ORDER', 8, 0, 'WAITING'],
        ['PR-20261008-004', 'Sign Peringatan Hitam (9x30)', 'Area Merokok', 'D6A-3', 'MTS_MIN_STOCK', 20, 11, 'IN_PROCESS'],
        ['PR-20261008-005', 'Modern Line Art Black & White set isi 3 (20x30)', 'Home', 'PBS3-O32-A-2', 'ORDER', 12, 0, 'WAITING'],
        ['PR-20261008-006', 'Sign Free Wifi (20x20)', 'Merah Hitam', 'N6A-3', 'MTS_MIN_STOCK', 35, 35, 'COMPLETED'],
        ['PR-20261008-007', 'Quotes Black and White (15x20)', 'Creativity', 'I9J-3', 'ORDER', 7, 7, 'COMPLETED'],
        ['PR-20261008-008', 'Malang Tempo Dulu (30x45)', 'Benstur Kantor', 'T29A-3', 'ORDER', 18, 0, 'WAITING'],
        ['PR-20261008-009', 'Gantungan Gelang Kayu 2 Sisi', 'White', 'GG-B-W', 'ORDER', 22, 9, 'IN_PROCESS']
    ];

    // Dua request COMPLETED dibuat selesai "hari ini" supaya kartu Completed Today terisi
    const MOCK_DONE_TODAY = ['PR-20261010-003', 'PR-20261010-008'];

    // PR-20261010-003 -> 2026-10-10 pada jam tertentu (hanya untuk data simulasi)
    function mockTime(requestNo, hour, minute) {

        const m = /PR-(\d{4})(\d{2})(\d{2})-/.exec(requestNo);

        const d = m
            ? new Date(Number(m[1]), Number(m[2]) - 1, Number(m[3]), hour, minute)
            : new Date();

        return d.toISOString();
    }

    const MOCK_REQUESTS = MOCK_ROWS.map(function (row) {

        const status = row[7];

        let completedAt = null;

        if (status === 'COMPLETED') {

            if (MOCK_DONE_TODAY.indexOf(row[0]) !== -1) {

                const now = new Date();
                now.setHours(14, 20, 0, 0);

                completedAt = now.toISOString();

            } else {
                completedAt = mockTime(row[0], 15, 45);
            }
        }

        return {
            request_no:    row[0],
            product_name:  row[1],   // product_groups.name
            variant_name:  row[2],   // products.variant_name
            sku:           row[3],   // products.sku
            source:        row[4],
            qty_requested: row[5],
            qty_produced:  row[6],
            status:        status,
            started_at:    status === 'WAITING' ? null : mockTime(row[0], 8, 30),
            completed_at:  completedAt
        };
    });

    // Perubahan yang dibuat user pada sesi halaman ini (tetap ada saat Refresh).
    // Hanya untuk simulasi. Hapus saat backend siap.
    const overrides = {};


    // ==========================================
    // LAYER API
    // Satu-satunya tempat yang perlu diubah saat
    // backend siap. Fungsi UI di bawah tidak perlu diubah.
    // ==========================================

    const queueApi = {

        async fetchRequests() {

            // --- Mode backend (aktif jika PQ_CONFIG.apiUrl diisi) ---
            if (PQ_CONFIG.apiUrl) {

                const response = await fetch(PQ_CONFIG.apiUrl, {
                    headers: { 'Accept': 'application/json' }
                });

                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }

                const json = await response.json();

                return Array.isArray(json) ? json : json.data;
            }

            // --- Mode simulasi ---
            await new Promise(function (resolve) {
                setTimeout(resolve, 600);
            });

            // Untuk menguji tampilan: tambahkan ?mock=error atau ?mock=empty di URL
            const mockMode = new URLSearchParams(window.location.search).get('mock');

            if (mockMode === 'error') {
                throw new Error('Simulasi error');
            }

            if (mockMode === 'empty') {
                return [];
            }

            return MOCK_REQUESTS.map(function (item) {
                return Object.assign({}, item, overrides[item.request_no] || {});
            });
        },

        // Mulai produksi: server mencatat started_at dan mengubah status menjadi IN_PROCESS.
        // Uji tampilan gagal: tambahkan ?mock=startfail di URL.
        async startProduction(item) {

            if (PQ_CONFIG.apiUrl) {
                // Hubungkan ke endpoint backend yang disepakati tim.
                throw new Error('Endpoint start production belum dikonfigurasi');
            }

            await new Promise(function (resolve) {
                setTimeout(resolve, 350);
            });

            if (new URLSearchParams(window.location.search).get('mock') === 'startfail') {
                throw new Error('Simulasi gagal');
            }

            return { started_at: new Date().toISOString() };
        }
    };


    // ==========================================
    // STATE
    // ==========================================

    const state = {
        all: [],
        loading: true,
        error: false,
        page: 1,
        filters: {
            q: '',
            source: '',
            status: ''
        }
    };


    // ==========================================
    // ELEMEN
    // ==========================================

    const els = {
        statTotal:    document.getElementById('pqStatTotal'),
        statWaiting:  document.getElementById('pqStatWaiting'),
        statProgress: document.getElementById('pqStatProgress'),
        statDone:     document.getElementById('pqStatDone'),

        search:       document.getElementById('pqSearch'),
        source:       document.getElementById('pqFilterSource'),
        status:       document.getElementById('pqFilterStatus'),
        reset:        document.getElementById('pqReset'),

        count:        document.getElementById('pqCount'),
        wrapper:      document.getElementById('pqTableWrapper'),
        body:         document.getElementById('pqTableBody'),
        empty:        document.getElementById('pqEmpty'),
        emptyText:    document.getElementById('pqEmptyText'),
        emptyReset:   document.getElementById('pqEmptyReset'),
        error:        document.getElementById('pqError'),
        retry:        document.getElementById('pqRetry'),

        footer:       document.getElementById('pqFooter'),
        info:         document.getElementById('pqInfo'),
        pagination:   document.getElementById('pqPagination'),

        toast:        document.getElementById('pqToast')
    };


    // ==========================================
    // HELPER
    // ==========================================

    function esc(value) {

        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        };

        return String(value).replace(/[&<>"']/g, function (char) {
            return map[char];
        });
    }

    function hasActiveFilter() {

        const f = state.filters;

        return f.q !== '' || f.source !== '' || f.status !== '';
    }

    function getFiltered() {

        const f = state.filters;

        const keyword = f.q.trim().toLowerCase();

        return state.all.filter(function (item) {

            if (f.source && item.source !== f.source) return false;
            if (f.status && item.status !== f.status) return false;

            if (keyword) {

                const haystack =
                    (item.request_no + ' ' + item.product_name + ' ' + item.variant_name + ' ' + item.sku).toLowerCase();

                if (haystack.indexOf(keyword) === -1) return false;
            }

            return true;
        });
    }

    let toastTimer = null;

    function showToast(message) {

        els.toast.textContent = message;
        els.toast.classList.add('show');

        clearTimeout(toastTimer);

        toastTimer = setTimeout(function () {
            els.toast.classList.remove('show');
        }, 3200);
    }


    // ==========================================
    // RENDER
    // ==========================================

    function renderSummary() {

        const count = function (status) {
            return state.all.filter(function (item) {
                return item.status === status;
            }).length;
        };

        const completedToday = state.all.filter(function (item) {

            return item.status === 'COMPLETED' && item.completed_at &&
                new Date(item.completed_at).toDateString() === new Date().toDateString();

        }).length;

        const ready = !state.loading && !state.error;

        els.statTotal.textContent    = ready ? state.all.length : '-';
        els.statWaiting.textContent  = ready ? count('WAITING') : '-';
        els.statProgress.textContent = ready ? count('IN_PROCESS') : '-';
        els.statDone.textContent     = ready ? completedToday : '-';
    }

    function renderSkeleton() {

        let html = '';

        for (let i = 0; i < PQ_CONFIG.perPage; i++) {

            html += '<tr>';

            for (let c = 0; c < 7; c++) {
                html += '<td><span class="pq-skeleton"></span></td>';
            }

            html += '</tr>';
        }

        els.body.innerHTML = html;
    }

    function renderRow(item) {

        const source   = SOURCE_META[item.source]     || { label: item.source,   cls: 'normal' };
        const status   = STATUS_META[item.status]     || { label: item.status,   cls: 'normal' };
        const action   = ACTION_META[item.status]     || ACTION_META.COMPLETED;

        return '' +
            '<tr>' +
                '<td><span class="pq-request-no">' + esc(item.request_no) + '</span></td>' +
                '<td><span class="pq-product">' + esc(item.product_name) +
                    '<small>' + esc(item.sku) + (item.variant_name ? ' &middot; ' + esc(item.variant_name) : '') + '</small></span></td>' +
                '<td class="center"><span class="pq-badge ' + source.cls + '">' + esc(source.label) + '</span></td>' +
                '<td class="center"><span class="pq-qty">' + esc(item.qty_requested) + ' pcs</span></td>' +
                '<td class="center"><span class="pq-qty">' + esc(item.qty_produced) + ' / ' + esc(item.qty_requested) + '</span></td>' +
                '<td class="center"><span class="pq-badge ' + status.cls + '">' + esc(status.label) + '</span></td>' +
                '<td class="center">' +
                    '<button type="button" class="pq-action"' +
                        ' data-action="' + action.key + '"' +
                        ' data-request-no="' + esc(item.request_no) + '">' +
                        esc(action.label) +
                    '</button>' +
                '</td>' +
            '</tr>';
    }

    function buildPageList(totalPages, current) {

        if (totalPages <= 7) {
            return Array.from({ length: totalPages }, function (_, i) { return i + 1; });
        }

        const pages = [1];
        const start = Math.max(2, current - 1);
        const end = Math.min(totalPages - 1, current + 1);

        if (start > 2) pages.push('gap');

        for (let p = start; p <= end; p++) pages.push(p);

        if (end < totalPages - 1) pages.push('gap');

        pages.push(totalPages);

        return pages;
    }

    function renderPagination(totalPages) {

        let html = '';

        html += '<button type="button" data-page="' + (state.page - 1) + '"' +
                (state.page === 1 ? ' disabled' : '') + ' aria-label="Halaman sebelumnya">&lsaquo;</button>';

        buildPageList(totalPages, state.page).forEach(function (p) {

            if (p === 'gap') {
                html += '<span class="gap">...</span>';
                return;
            }

            html += '<button type="button" data-page="' + p + '"' +
                    (p === state.page ? ' class="active" aria-current="page"' : '') + '>' + p + '</button>';
        });

        html += '<button type="button" data-page="' + (state.page + 1) + '"' +
                (state.page === totalPages ? ' disabled' : '') + ' aria-label="Halaman berikutnya">&rsaquo;</button>';

        els.pagination.innerHTML = html;
    }

    function render() {

        renderSummary();

        els.reset.hidden = !hasActiveFilter();

        // Loading
        if (state.loading) {

            els.count.textContent = '... request';

            els.wrapper.hidden = false;
            els.empty.hidden = true;
            els.error.hidden = true;
            els.footer.hidden = true;

            renderSkeleton();

            return;
        }

        // Error
        if (state.error) {

            els.count.textContent = '0 request';

            els.wrapper.hidden = true;
            els.empty.hidden = true;
            els.error.hidden = false;
            els.footer.hidden = true;

            return;
        }

        const filtered = getFiltered();

        els.count.textContent = filtered.length + ' request';

        // Empty
        if (filtered.length === 0) {

            els.wrapper.hidden = true;
            els.error.hidden = true;
            els.empty.hidden = false;
            els.footer.hidden = true;

            els.emptyText.textContent = hasActiveFilter()
                ? 'Tidak ada request yang cocok dengan pencarian atau filter yang dipilih.'
                : 'Belum ada request produksi saat ini.';

            els.emptyReset.hidden = !hasActiveFilter();

            return;
        }

        // Data
        const totalPages = Math.ceil(filtered.length / PQ_CONFIG.perPage);

        if (state.page > totalPages) state.page = totalPages;

        const from = (state.page - 1) * PQ_CONFIG.perPage;
        const pageItems = filtered.slice(from, from + PQ_CONFIG.perPage);

        els.wrapper.hidden = false;
        els.empty.hidden = true;
        els.error.hidden = true;
        els.footer.hidden = false;

        els.body.innerHTML = pageItems.map(renderRow).join('');

        els.info.textContent =
            'Menampilkan ' + (from + 1) + '-' + (from + pageItems.length) +
            ' dari ' + filtered.length + ' request';

        renderPagination(totalPages);
    }


    // ==========================================
    // LOAD DATA
    // ==========================================

    async function loadData() {

        state.loading = true;
        state.error = false;

        render();

        try {

            state.all = await queueApi.fetchRequests();

        } catch (error) {

            state.error = true;

        }

        state.loading = false;

        render();
    }


    // ==========================================
    // FILTER
    // ==========================================

    function applyFilters() {

        state.filters.q        = els.search.value;
        state.filters.source   = els.source.value;
        state.filters.status   = els.status.value;

        state.page = 1;

        render();
    }

    function resetFilters() {

        els.search.value   = '';
        els.source.value   = '';
        els.status.value   = '';

        applyFilters();
    }

    let searchTimer = null;

    els.search.addEventListener('input', function () {

        clearTimeout(searchTimer);

        searchTimer = setTimeout(applyFilters, PQ_CONFIG.searchDelay);
    });

    [els.source, els.status].forEach(function (select) {
        select.addEventListener('change', applyFilters);
    });

    els.reset.addEventListener('click', resetFilters);
    els.emptyReset.addEventListener('click', resetFilters);
    els.retry.addEventListener('click', loadData);


    // ==========================================
    // PAGINATION
    // ==========================================

    els.pagination.addEventListener('click', function (event) {

        const button = event.target.closest('button[data-page]');

        if (!button || button.disabled) return;

        state.page = Number(button.dataset.page);

        render();
    });


    // ==========================================
    // AKSI TOMBOL
    //   WAITING    -> Start Production : catat started_at, status IN_PROCESS, buka modal
    //   IN_PROCESS -> Continue         : buka modal eksekusi
    //   COMPLETED  -> View Detail      : buka modal detail (hanya baca)
    // ==========================================

    function findItem(requestNo) {

        return state.all.find(function (row) {
            return row.request_no === requestNo;
        });
    }

    function saveOverride(item) {

        overrides[item.request_no] = {
            qty_produced: item.qty_produced,
            status:       item.status,
            started_at:   item.started_at,
            completed_at: item.completed_at
        };
    }

    async function startProduction(item, button) {

        // Validasi: hanya request WAITING yang boleh dimulai
        if (item.status !== 'WAITING') {

            showToast('Request ' + item.request_no + ' sudah tidak berstatus WAITING.');
            render();

            return;
        }

        button.disabled = true;
        button.textContent = 'Starting...';

        let result;

        try {

            result = await queueApi.startProduction(item);

        } catch (error) {

            showToast('Gagal memulai produksi ' + item.request_no + '. Silakan coba lagi.');
            render();

            return;
        }

        item.status = 'IN_PROCESS';
        item.started_at = result.started_at;

        saveOverride(item);

        render();

        if (window.ProductionExecutionModal) {
            window.ProductionExecutionModal.open(item);
        }
    }

    els.body.addEventListener('click', function (event) {

        const button = event.target.closest('button[data-action]');

        if (!button) return;

        const action = button.dataset.action;
        const requestNo = button.dataset.requestNo;

        document.dispatchEvent(new CustomEvent('production-queue:action', {
            detail: { action: action, requestNo: requestNo }
        }));

        const item = findItem(requestNo);

        if (!item) return;

        if (action === 'start') {

            startProduction(item, button);

            return;
        }

        if (action === 'continue') {

            if (item.status !== 'IN_PROCESS') {
                showToast('Request ' + requestNo + ' tidak berstatus IN_PROCESS.');
                return;
            }

            if (window.ProductionExecutionModal) {
                window.ProductionExecutionModal.open(item);
                return;
            }

            showToast('Modal eksekusi belum termuat. Pastikan partial modal eksekusi sudah terpasang di halaman ini.');
            return;
        }

        if (action === 'detail') {

            if (window.ProductionDetailModal) {
                window.ProductionDetailModal.open(item);
                return;
            }

            showToast('Modal detail belum termuat. Pastikan partial modal detail sudah terpasang di halaman ini.');
        }
    });

    // Setelah modal "disimpan" (SIMULASI), perbarui baris di tabel
    //   Simpan Progres        -> qty bertambah, status IN_PROCESS
    //   Total mencapai target -> status COMPLETED + completed_at
    document.addEventListener('production-execution:saved', function (event) {

        const detail = event.detail;

        const item = findItem(detail.requestNo);

        if (!item) return;

        item.qty_produced = detail.qtyProduced;
        item.status = detail.status;

        if (detail.status === 'COMPLETED') {
            item.completed_at = new Date().toISOString();
        }

        saveOverride(item);

        render();

        showToast(
            'Simulasi: ' + detail.requestNo +
            (detail.status === 'COMPLETED' ? ' selesai (COMPLETED).' : ' progres disimpan.') +
            ' Belum tersimpan ke database.'
        );
    });



    // ==========================================
    // INIT
    // ==========================================

    loadData();

})();
</script>

@endpush