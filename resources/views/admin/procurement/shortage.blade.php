@extends('layouts.admin')

@section('title', 'Material Shortage - REBANT BOMS')

@push('styles')
@vite([
'resources/css/production-queue.css',
'resources/css/production-execution-modal.css',
'resources/css/material-shortage.css'
])
@endpush

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
            <path d="M12 5v14" />
            <path d="M5 12h14" />
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
                    <path d="M21 8 12 3 3 8v8l9 5 9-5V8Z" />
                    <path d="m3 8 9 5 9-5" />
                    <path d="M12 13v8" />
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
                    <path d="M12 3 2 20h20L12 3Z" />
                    <path d="M12 10v4" />
                    <path d="M12 17h.01" />
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
                    <circle cx="12" cy="12" r="9" />
                    <path d="m8 12 3 3 5-6" />
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
                    <circle cx="12" cy="12" r="9" />
                    <path d="M12 7v6" />
                    <path d="M12 16.5h.01" />
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
            <circle cx="11" cy="11" r="7" />
            <path d="m20 20-3.5-3.5" />
        </svg>

        <input
            type="text"
            id="msSearch"
            placeholder="Cari production request no atau bahan baku..."
            autocomplete="off"
            aria-label="Cari production request no, nama bahan baku, atau kode bahan baku">
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
                <circle cx="11" cy="11" r="7" />
                <path d="m20 20-3.5-3.5" />
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
                <circle cx="12" cy="12" r="9" />
                <path d="M12 7v6" />
                <path d="M12 16.5h.01" />
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
                    <path d="M12 3 2 20h20L12 3Z" />
                    <path d="M12 10v4" />
                    <path d="M12 17h.01" />
                </svg>
            </div>

            <div class="pe-header-text">
                <h2 id="msdTitle">Detail Shortage</h2>
                <p>Production Request <strong id="msdRequestNo">-</strong></p>
            </div>

            <button type="button" class="pe-close" id="msdClose" aria-label="Tutup">
                <svg width="18" height="18" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
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
                            <span>Purchase Order</span>
                            <strong id="msdPo">-</strong>
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
    MODAL: BUAT PURCHASE ORDER
========================= --}}

<div class="pe-overlay" id="mspOverlay" hidden>

    <div class="pe-modal" role="dialog" aria-modal="true" aria-labelledby="mspTitle">

        <div class="pe-header">

            <div class="pe-header-icon">
                <svg width="22" height="22" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M6 3h9l4 4v14H6V3Z" />
                    <path d="M14 3v5h5" />
                    <path d="M9 13h7" />
                    <path d="M9 17h7" />
                </svg>
            </div>

            <div class="pe-header-text">
                <h2 id="mspTitle">Buat Purchase Order</h2>
                <p>Pilih shortage yang akan dipesan ke supplier</p>
            </div>

            <button type="button" class="pe-close" id="mspClose" aria-label="Tutup">
                <svg width="18" height="18" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
                </svg>
            </button>

        </div>


        <div class="pe-body">

            <p class="ms-note">
                PO akan disimpan lalu dikirim lewat email ke supplier. Shortage menjadi
                RESOLVED setelah barang diterima dan stok mencukupi.
            </p>

            <div class="pe-form-error" id="mspError" hidden></div>

            <section class="pe-section">
                <h3><span>1.</span> Item Shortage & Supplier</h3>
                <p class="pe-section-desc">
                    Centang shortage OPEN yang ingin dipesan, lalu pilih supplier untuk tiap bahan.
                    Kalau beda bahan punya supplier berbeda, sistem otomatis membuat PO terpisah per supplier.
                    <span id="mspSelected">0 item dipilih</span>
                </p>

                <div class="ms-items" id="mspItems"></div>
            </section>

            <section class="pe-section">
                <h3><span>2.</span> Catatan</h3>
                <p class="pe-section-desc">Opsional. Disimpan ke kolom notes pada setiap PO yang dibuat.</p>

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
    (function() {

        'use strict';


        // ==========================================
        // KONFIGURASI
        // ==========================================

        const MS_CONFIG = {
            perPage: 8,
            searchDelay: 250,
            toastDuration: 6500
        };

        // Status mengikuti ERD: default OPEN. SQL belum membatasi nilai,
        // jadi status tak dikenal tetap ditampilkan apa adanya.
        const STATUS_META = {
            OPEN: {
                label: 'OPEN',
                cls: 'waiting'
            },
            RESOLVED: {
                label: 'RESOLVED',
                cls: 'done'
            }
        };


        // ==========================================
        // LAPISAN DATA (API)
        // ==========================================

        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content;

        async function fetchJson(url, options = {}) {
            const res = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    ...(options.headers || {}),
                },
                ...options,
            });

            if (!res.ok) {
                let message = '';

                try {
                    const body = await res.json();
                    message = body.errors ?
                        Object.values(body.errors)[0][0] :
                        (body.message || '');
                } catch (e) {
                    // respons bukan JSON
                }

                const error = new Error(message || 'Request failed: ' + res.status);
                error.status = res.status;
                throw error;
            }

            return res.json();
        }

        const api = {

            getMaterialShortages: function() {
                return fetchJson('{{ route("admin.procurement.shortage.data") }}');
            },

            getMaterialShortageDetail: function(id) {
                return fetchJson('{{ url("admin/procurement/shortage") }}/' + id + '/data');
            },

            getRawMaterials: function() {
                return fetchJson('{{ route("admin.procurement.shortage.materials") }}');
            },

            getSuppliers: function() {
                return fetchJson('{{ route("admin.procurement.shortage.suppliers") }}');
            },

            createPurchaseOrderFromShortage: function(payload) {
                return fetchJson('{{ route("admin.procurement.store-from-shortage") }}', {
                    method: 'POST',
                    body: JSON.stringify(payload),
                });
            },

            getSupplierMaterials: function() {
                return fetchJson('{{ route("admin.procurement.shortage.supplier-materials") }}');
            },
        };


        // ==========================================
        // STATE + ELEMEN
        // ==========================================

        const state = {
            rows: [],
            materials: [],
            supplierMaterials: [],
            search: '',
            status: '',
            material: '',
            page: 1,
            loading: true,
            failed: false
        };

        function $(id) {
            return document.getElementById(id);
        }

        const els = {
            statTotal: $('msStatTotal'),
            statOpen: $('msStatOpen'),
            statResolved: $('msStatResolved'),
            statCritical: $('msStatCritical'),

            search: $('msSearch'),
            status: $('msFilterStatus'),
            material: $('msFilterMaterial'),
            reset: $('msReset'),

            count: $('msCount'),
            wrapper: $('msTableWrapper'),
            body: $('msTableBody'),
            empty: $('msEmpty'),
            emptyTxt: $('msEmptyText'),
            emptyRst: $('msEmptyReset'),
            error: $('msError'),
            retry: $('msRetry'),

            footer: $('msFooter'),
            info: $('msInfo'),
            pagination: $('msPagination'),

            toast: $('msToast'),
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
            return new Intl.NumberFormat('id-ID', {
                maximumFractionDigits: 2
            }).format(n);
        }

        function fmtQty(n, unit) {
            return fmtNum(n) + ' ' + (unit || '');
        }

        function pad(n) {
            return n < 10 ? '0' + n : String(n);
        }

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
            if (isNaN(diff)) return {
                value: 0,
                invalid: true
            };
            return {
                value: Math.max(0, diff),
                invalid: diff < 0
            };
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
            return STATUS_META[status] || {
                label: status || '-',
                cls: 'normal'
            };
        }

        function poStatusLabel(status) {
            if (status === 'PARTIALLY_RECEIVED') return 'Sebagian diterima';
            if (status === 'RECEIVED') return 'Diterima';
            return 'Menunggu barang';
        }

        function hasRunningPo(row) {
            return !!(row.existing_po && row.existing_po.length > 0);
        }

        let toastTimer = null;

        function toast(message) {
            els.toast.textContent = message;
            els.toast.classList.add('show');
            clearTimeout(toastTimer);
            toastTimer = setTimeout(function() {
                els.toast.classList.remove('show');
            }, MS_CONFIG.toastDuration);
        }

        function findRow(id) {
            return state.rows.find(function(row) {
                return row.id === id;
            });
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
                )).filter(function(el) {
                    return el.offsetParent !== null;
                });
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

            overlay.addEventListener('mousedown', function(event) {
                if (event.target === overlay && settings.backdrop !== false) close();
            });

            overlay.addEventListener('keydown', function(event) {
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

            return {
                open: open,
                close: close
            };
        }


        // ==========================================
        // STATISTIK
        // Dihitung dari SELURUH laporan (tidak ikut filter).
        // ==========================================

        function renderStats() {

            const rows = state.rows;
            const criticalMaterials = {};

            rows.forEach(function(row) {
                if (isCritical(row)) criticalMaterials[row.raw_material_id] = true;
            });

            els.statTotal.textContent = state.failed ? '-' : rows.length;
            els.statOpen.textContent = state.failed ? '-' :
                rows.filter(function(r) {
                    return r.status === 'OPEN';
                }).length;
            els.statResolved.textContent = state.failed ? '-' :
                rows.filter(function(r) {
                    return r.status === 'RESOLVED';
                }).length;
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

            return state.rows.filter(function(row) {

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
            const poList = row.existing_po || [];
            const hasPo = poList.length > 0;

            const warn = short.invalid ?
                '<span class="ms-warn" title="Qty Available melebihi Qty Needed. Data perlu diperiksa.">Periksa data</span>' :
                '';

            // Info PO yang sedang berjalan
            const poInfo = hasPo ?
                '<div class="ms-po-info">' +
                '<strong>PO sudah dijalankan</strong>' +
                poList.map(function(p) {
                    return '<span>' + esc(p.po_no) + ' &middot; ' + esc(poStatusLabel(p.status)) + '</span>';
                }).join('') +
                '</div>' :
                '';

            // Tombol hanya muncul kalau shortage OPEN dan belum ada PO berjalan
            const poButton = (isOpen && !hasPo) ?
                '<button type="button" class="ms-action primary" data-act="po" data-id="' + row.id + '">Buat PO</button>' :
                '';

            return '' +
                '<tr>' +
                '<td><span class="pq-request-no">' + esc(row.production_request ? row.production_request.request_no : '-') + '</span></td>' +
                '<td><span class="pq-product">' + esc(material.name || '-') +
                '<small>' + esc(material.code || '') + '</small></span></td>' +
                '<td class="center pq-qty">' + esc(fmtQty(row.qty_needed, unit)) + '</td>' +
                '<td class="center pq-qty">' + esc(fmtQty(row.qty_available, unit)) + '</td>' +
                '<td class="center"><strong class="ms-short' + (isOpen ? ' open' : '') + '">' +
                esc(fmtQty(short.value, unit)) + '</strong>' + warn + '</td>' +
                '<td class="center"><span class="pq-badge ' + meta.cls + '">' + esc(meta.label) + '</span>' + poInfo + '</td>' +
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
                els.emptyTxt.textContent = state.rows.length ?
                    'Tidak ada laporan yang sesuai dengan pencarian atau filter.' :
                    'Belum ada laporan kekurangan bahan baku.';
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
                state.materials.map(function(m) {
                    return '<option value="' + m.id + '">' + esc(m.name) + '</option>';
                }).join('');
        }


        // ==========================================
        // MODAL DETAIL
        // ==========================================

        const D = {
            overlay: $('msdOverlay'),
            requestNo: $('msdRequestNo'),
            loading: $('msdLoading'),
            error: $('msdError'),
            content: $('msdContent'),
            material: $('msdMaterial'),
            code: $('msdCode'),
            status: $('msdStatus'),
            unit: $('msdUnit'),
            needed: $('msdNeeded'),
            available: $('msdAvailable'),
            shortage: $('msdShortage'),
            po: $('msdPo'),
            reported: $('msdReported'),
            resolved: $('msdResolved'),
            createPo: $('msdCreatePo'),
            retry: $('msdRetry'),
            close: $('msdClose'),
            done: $('msdDone')
        };

        const detailModal = createModal(D.overlay, {
            onClose: function() {
                detailToken++;
            }
        });

        let detailToken = 0;
        let detailId = null;

        async function openDetail(id) {

            detailId = id;
            const token = ++detailToken;
            const known = findRow(id);

            D.requestNo.textContent = known && known.production_request ?
                known.production_request.request_no : '-';
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
            const poList = item.existing_po || [];
            const hasPo = poList.length > 0;

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

            D.po.textContent = hasPo ?
                poList.map(function(p) {
                    return p.po_no + ' (' + poStatusLabel(p.status) + ')';
                }).join(', ') :
                'Belum ada PO';

            if (item.resolved_at) {
                D.resolved.textContent = fmtDate(item.resolved_at);
            } else {
                D.resolved.textContent = item.status === 'RESOLVED' ? 'Waktu tidak tercatat' : 'Belum selesai';
            }

            D.createPo.hidden = item.status !== 'OPEN' || hasPo;

            D.loading.hidden = true;
            D.error.hidden = true;
            D.content.hidden = false;
        }

        D.close.addEventListener('click', detailModal.close);
        D.done.addEventListener('click', detailModal.close);
        D.retry.addEventListener('click', function() {
            openDetail(detailId);
        });

        D.createPo.addEventListener('click', function() {
            const id = detailId;
            detailModal.close();
            openPo([id]);
        });


        // ==========================================
        // MODAL BUAT PURCHASE ORDER
        // ==========================================

        const P = {
            overlay: $('mspOverlay'),
            error: $('mspError'),
            items: $('mspItems'),
            selected: $('mspSelected'),
            notes: $('mspNotes'),
            confirm: $('mspConfirm'),
            save: $('mspSave'),
            cancel: $('mspCancel'),
            close: $('mspClose')
        };

        const po = {
            saving: false,
            confirming: false
        };

        const poModal = createModal(P.overlay, {
            backdrop: false,
            canClose: function() {
                return !po.saving;
            }
        });

        function suppliersForMaterial(materialId) {
            const map = new Map();

            state.supplierMaterials
                .filter(function(sm) {
                    return sm.raw_material_id === materialId;
                })
                .forEach(function(sm) {
                    map.set(sm.supplier_id, sm.supplier_name);
                });

            return Array.from(map, function(entry) {
                    return {
                        id: entry[0],
                        name: entry[1]
                    };
                })
                .sort(function(a, b) {
                    return a.name.localeCompare(b.name);
                });
        }

        function openPo(preselectedIds) {

            // Hanya shortage OPEN yang belum punya PO berjalan
            const open = state.rows.filter(function(row) {
                return row.status === 'OPEN' && !hasRunningPo(row);
            });

            if (!open.length) {
                toast('Semua shortage OPEN sudah punya PO berjalan.');
                return;
            }

            const chosen = preselectedIds || [];

            P.items.innerHTML = open.map(function(row) {

                const short = shortageOf(row);
                const checked = chosen.indexOf(row.id) !== -1;
                const material = row.raw_material || {};
                const suppliers = suppliersForMaterial(row.raw_material_id);

                const supplierOptionsHtml = suppliers.length ?
                    '<option value="">Pilih supplier</option>' +
                    suppliers.map(function(s) {
                        return '<option value="' + s.id + '">' + esc(s.name) + '</option>';
                    }).join('') :
                    '<option value="">Tidak ada supplier terdaftar</option>';

                return '' +
                    '<div class="ms-item' + (checked ? ' on' : '') + '" data-id="' + row.id + '" data-material="' + row.raw_material_id + '">' +
                    '<label class="ms-item-main">' +
                    '<input type="checkbox" class="ms-check"' + (checked ? ' checked' : '') + (suppliers.length ? '' : ' disabled') + '>' +
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
                    '<select class="ms-supplier"' + (checked ? '' : ' disabled') + ' aria-label="Supplier untuk ' + esc(material.name || '') + '">' +
                    supplierOptionsHtml +
                    '</select>' +
                    '</div>';
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
        // po_no, status, created_by, dan trigger_type ditetapkan backend.
        function buildPayload() {

            const items = [];
            let valid = true;

            P.items.querySelectorAll('.ms-item').forEach(function(node) {

                const check = node.querySelector('.ms-check');
                const qtyInput = node.querySelector('.ms-qty');
                const supplierSelect = node.querySelector('.ms-supplier');
                const qtyGroup = qtyInput.parentNode;

                qtyGroup.classList.remove('invalid');
                supplierSelect.classList.remove('invalid');

                if (!check.checked) return;

                const row = findRow(Number(node.getAttribute('data-id')));
                const qty = Number(qtyInput.value);
                let rowValid = true;

                if (!supplierSelect.value) {
                    supplierSelect.classList.add('invalid');
                    rowValid = false;
                }

                if (!qtyInput.value || isNaN(qty) || qty <= 0) {
                    qtyGroup.classList.add('invalid');
                    rowValid = false;
                }

                if (!rowValid) {
                    valid = false;
                    return;
                }

                items.push({
                    raw_material_id: row.raw_material_id,
                    material_shortage_id: row.id,
                    qty_suggested: shortageOf(row).value,
                    qty_ordered: qty,
                    supplier_id: Number(supplierSelect.value)
                });
            });

            return {
                valid: valid,
                payload: {
                    notes: P.notes.value.trim() || null,
                    items: items
                }
            };
        }

        async function savePo() {

            hidePoError();

            const built = buildPayload();
            const data = built.payload;

            if (!data.items.length) {
                resetConfirm();
                showPoError('Pilih minimal satu item shortage.');
                return;
            }

            if (!built.valid) {
                resetConfirm();
                showPoError('Lengkapi jumlah dan supplier pada setiap item yang dicentang.');
                return;
            }

            const supplierCount = new Set(data.items.map(function(i) {
                return i.supplier_id;
            })).size;

            if (!po.confirming) {
                po.confirming = true;

                const msg = supplierCount > 1 ?
                    'Akan dibuat ' + supplierCount + ' PO terpisah (per supplier) untuk total ' + data.items.length + ' item.' :
                    'Buat PO untuk ' + data.items.length + ' item dan kirim email ke supplier?';

                P.confirm.textContent = msg + ' Email PO akan dikirim ke setiap supplier.';
                P.confirm.hidden = false;
                P.save.textContent = 'Ya, buat & kirim PO';
                P.confirm.scrollIntoView({
                    block: 'nearest'
                });
                return;
            }

            po.saving = true;
            P.save.disabled = true;
            P.cancel.disabled = true;
            P.save.textContent = 'Mengirim...';

            try {
                const result = await api.createPurchaseOrderFromShortage(data);

                po.saving = false;
                poModal.close();

                document.dispatchEvent(new CustomEvent('material-shortage:po-created', {
                    detail: result
                }));

                const created = result.purchase_orders;
                const list = created.map(function(p) {
                    return p.po_no;
                }).join(', ');
                const failed = created.filter(function(p) {
                    return !p.email_sent;
                }).length;

                toast(
                    created.length + ' PO dibuat: ' + list + '. ' +
                    (failed ?
                        failed + ' email gagal dikirim, kirim ulang dari halaman detail PO.' :
                        'Email terkirim ke supplier.')
                );

                // Muat ulang supaya info "PO sudah dijalankan" langsung tampil
                loadData();

            } catch (error) {
                po.saving = false;
                resetConfirm();
                showPoError(
                    error.status === 422 && error.message ?
                    error.message :
                    'PO gagal disimpan. Coba lagi.'
                );
            } finally {
                po.saving = false;
                P.save.disabled = false;
                P.cancel.disabled = false;
                if (!po.confirming) P.save.textContent = 'Simpan PO';
            }
        }

        P.items.addEventListener('change', function(event) {

            const node = event.target.closest('.ms-item');
            if (!node) return;

            if (event.target.classList.contains('ms-check')) {
                const on = event.target.checked;
                node.classList.toggle('on', on);
                node.querySelector('.ms-qty').disabled = !on;
                node.querySelector('.ms-supplier').disabled = !on;
                updateSelected();
            }

            resetConfirm();
        });

        P.items.addEventListener('input', resetConfirm);
        P.notes.addEventListener('input', resetConfirm);

        P.save.addEventListener('click', savePo);
        P.cancel.addEventListener('click', poModal.close);
        P.close.addEventListener('click', poModal.close);

        els.createPo.addEventListener('click', function() {
            openPo([]);
        });


        // ==========================================
        // EVENT HALAMAN
        // ==========================================

        let searchTimer = null;

        els.search.addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                state.search = els.search.value;
                state.page = 1;
                render();
            }, MS_CONFIG.searchDelay);
        });

        els.status.addEventListener('change', function() {
            state.status = els.status.value;
            state.page = 1;
            render();
        });

        els.material.addEventListener('change', function() {
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

        els.pagination.addEventListener('click', function(event) {
            const button = event.target.closest('button[data-page]');
            if (!button || button.disabled) return;
            state.page = Number(button.getAttribute('data-page'));
            render();
        });

        els.body.addEventListener('click', function(event) {

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
                    api.getSupplierMaterials()
                ]);

                // OPEN di atas, lalu laporan terbaru lebih dulu
                state.rows = results[0].slice().sort(function(a, b) {
                    if (a.status !== b.status) return a.status === 'OPEN' ? -1 : (b.status === 'OPEN' ? 1 : 0);
                    return new Date(b.reported_at) - new Date(a.reported_at);
                });
                state.materials = results[1];
                state.supplierMaterials = results[2];

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