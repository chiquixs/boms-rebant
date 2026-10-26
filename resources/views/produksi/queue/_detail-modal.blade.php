{{-- =========================
    MODAL: DETAIL PRODUKSI (hanya baca)
    Dibuka dari tombol "Lihat Detail" pada request berstatus COMPLETED.
    Semua data di sini masih SIMULASI.
========================= --}}

<div class="pe-overlay" id="pdOverlay" hidden>

    <div class="pe-modal"
         role="dialog"
         aria-modal="true"
         aria-labelledby="pdTitle">


        {{-- HEADER --}}
        <div class="pe-header">

            <div class="pe-header-icon">
                <svg width="22" height="22" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="m8 12 3 3 5-6"/>
                </svg>
            </div>

            <div class="pe-header-text">
                <h2 id="pdTitle">Detail Produksi</h2>
                <p>Request No. <strong id="pdRequestNo">-</strong></p>
            </div>

            <button type="button" class="pe-close" id="pdClose" aria-label="Tutup">
                <svg width="18" height="18" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6 6 18"/>
                    <path d="m6 6 12 12"/>
                </svg>
            </button>

        </div>


        {{-- BODY --}}
        <div class="pe-body">

            {{-- Info produk --}}
            <div class="pe-product">

                <div class="pe-product-thumb" id="pdThumb">
                    <svg width="22" height="22" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="1.6">
                        <rect x="3" y="4" width="18" height="16" rx="2"/>
                        <circle cx="9" cy="10" r="1.5"/>
                        <path d="m21 16-5-5-8 9"/>
                    </svg>
                </div>

                <div class="pe-product-info">
                    <strong id="pdProductName">-</strong>
                    <span id="pdProductSku">-</span>
                </div>

                <div class="pe-product-badges">
                    <span class="pq-badge" id="pdSource">-</span>
                    <span class="pq-badge" id="pdStatus">-</span>
                </div>

            </div>


            {{-- Loading --}}
            <div id="pdLoading">
                <span class="pq-skeleton pe-skel"></span>
                <span class="pq-skeleton pe-skel"></span>
                <span class="pq-skeleton pe-skel"></span>
                <span class="pq-skeleton pe-skel short"></span>
            </div>


            {{-- Error --}}
            <div class="pe-error-box" id="pdLoadError" hidden>
                <strong>Detail produksi gagal dimuat</strong>
                <p>Terjadi kendala saat mengambil data hasil produksi dan pemakaian bahan.</p>
                <button type="button" class="pe-btn pe-btn-secondary" id="pdRetry">Coba lagi</button>
            </div>


            {{-- Isi --}}
            <div id="pdContent" hidden>

                {{-- 1. RINGKASAN PRODUKSI --}}
                <section class="pe-section">

                    <h3><span>1.</span> Ringkasan Produksi</h3>
                    <p class="pe-section-desc">Hasil akhir produksi untuk request ini.</p>

                    <div class="pe-progress">

                        <div>
                            <span>Jumlah diminta</span>
                            <strong id="pdRequested">-</strong>
                        </div>

                        <div>
                            <span>Total diproduksi</span>
                            <strong id="pdProduced">-</strong>
                        </div>

                        <div class="accent">
                            <span>Pencapaian</span>
                            <strong id="pdPercent">-</strong>
                        </div>

                    </div>

                    <div class="pd-bar" aria-hidden="true">
                        <span id="pdBar"></span>
                    </div>

                </section>


                {{-- LOG AKTIVITAS --}}
                <section class="pe-section">

                    <h3 class="pd-log-title">
                        <svg width="18" height="18" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="9"/>
                            <path d="M12 7v5l3 2"/>
                        </svg>
                        Log Aktivitas
                    </h3>

                    <ul class="pd-log" id="pdLog"></ul>

                </section>


                {{-- 2. PEMAKAIAN MATERIAL --}}
                <section class="pe-section">

                    <h3><span>2.</span> Pemakaian Material</h3>
                    <p class="pe-section-desc">
                        Perbandingan kebutuhan bahan dengan pemakaian aktual.
                    </p>

                    <div class="pe-table-wrap">
                        <table class="pe-table">
                            <thead>
                                <tr>
                                    <th class="pe-col-no">No.</th>
                                    <th>Bahan Baku</th>
                                    <th>Kebutuhan</th>
                                    <th>Aktual</th>
                                    <th class="pe-col-status">Selisih</th>
                                </tr>
                            </thead>
                            <tbody id="pdMaterialBody"></tbody>
                        </table>
                    </div>

                    <p class="pe-note">Simulasi: data pemakaian belum berasal dari database.</p>

                </section>


                {{-- 3. SHORTAGE (hanya jika pernah dilaporkan) --}}
                <section class="pe-section" id="pdShortageSection" hidden>

                    <h3><span>3.</span> Catatan Shortage</h3>
                    <p class="pe-section-desc" id="pdShortageText">-</p>

                </section>

            </div>

        </div>


        {{-- FOOTER --}}
        <div class="pe-footer single">

            <button type="button" class="pe-btn pe-btn-primary" id="pdDone">
                Tutup
            </button>

        </div>

    </div>

</div>


@push('styles')

<style>
/* =========================
   LOG AKTIVITAS (timeline)
========================= */

.pd-log-title {
    display: flex;
    align-items: center;

    gap: 10px;

    margin: 0 0 20px !important;

    font-size: 14px;
    font-weight: 700;
}

.pd-log-title svg {
    flex-shrink: 0;

    color: #4B4E48;
}

.pd-log {
    margin: 0;
    padding: 0 0 0 6px;

    list-style: none;
}

.pd-log li {
    position: relative;

    margin: 0;
    padding: 0 0 22px 30px;

    list-style: none;
}

.pd-log li:last-child {
    padding-bottom: 0;
}

.pd-log li::marker {
    content: "";
}

/* garis penghubung antar titik */
.pd-log li::before {
    content: "";

    position: absolute;

    left: 5px;
    top: 14px;
    bottom: -4px;

    width: 2px;

    background: #CFE3C9;
}

.pd-log li:last-child::before {
    display: none;
}

/* titik hijau */
.pd-log-dot {
    position: absolute;

    left: 0;
    top: 2px;

    width: 12px;
    height: 12px;

    border-radius: 50%;

    background: #3E9B5F;

    box-shadow: 0 0 0 3px #DCEEDD;
}

.pd-log time {
    display: block;

    margin-bottom: 4px;

    color: #8A8D86;

    font-size: 11px;
}

.pd-log p {
    margin: 0;

    color: var(--text);

    font-size: 13px;
    font-weight: 500;
}
</style>

@endpush


@push('scripts')

<script>
(function () {

    'use strict';


    // Sumber permintaan (production_requests.source).
    //   ORDER                           -> Pesanan Pelanggan (MTO)
    //   MTS_MIN_STOCK / MTS-MIN-STOCK   -> Pengisian Stok (MTS)
    //   RESTOCK                         -> Pengisian Stok (MTS)
    // Nilai ini konvensi aplikasi. Constraint database saat ini hanya
    // mengizinkan ORDER dan RESTOCK, jadi ketiganya dikenali.
    // Nilai lain atau kosong ditampilkan dengan aman sebagai apa adanya.
    function sourceMeta(source) {

        if (source === 'ORDER') {
            return { label: 'Pesanan Pelanggan (MTO)', cls: 'order', group: 'ORDER' };
        }

        if (source === 'MTS_MIN_STOCK' || source === 'MTS-MIN-STOCK' || source === 'RESTOCK') {
            return { label: 'Pengisian Stok (MTS)', cls: 'mts', group: 'MTS' };
        }

        return { label: source ? String(source) : 'Tidak diketahui', cls: 'normal', group: '' };
    }


    // ==========================================
    // DATA SIMULASI (MOCK)
    // Resep + faktor pemakaian aktual (1 = sesuai rencana).
    // Hanya untuk tampilan. Hapus saat backend siap.
    // ==========================================

    const MOCK_MATERIALS = [
        { name: 'Kayu MDF',    unit: 'lembar', qty_per_unit: 0.5,  factor: 1 },
        { name: 'Paku 2 inch', unit: 'pcs',    qty_per_unit: 5,    factor: 1.04 },
        { name: 'Lem Kayu',    unit: 'botol',  qty_per_unit: 0.1,  factor: 0.95 },
        { name: 'Cat Kayu',    unit: 'kaleng', qty_per_unit: 0.05, factor: 1 }
    ];

    // Data yang baru diisi lewat modal eksekusi pada sesi halaman ini
    // (requestNo -> { usages, completedAt, shortages }).
    const recorded = {};


    // ==========================================
    // LAYER API (SIMULASI)
    // Ganti dengan fetch ke backend saat siap.
    // Uji tampilan error: tambahkan ?mock=detailerror di URL.
    // ==========================================

    function wait(ms) {
        return new Promise(function (resolve) { setTimeout(resolve, ms); });
    }

    function round2(n) {
        return Math.round(n * 100) / 100;
    }

    // PR-20261010-001 -> 10 Okt 2026 (hanya untuk data simulasi)
    function mockDay(requestNo) {

        const m = /PR-(\d{4})(\d{2})(\d{2})-/.exec(requestNo);

        return m ? new Date(Number(m[1]), Number(m[2]) - 1, Number(m[3])) : new Date();
    }

    const detailApi = {

        async fetchDetail(item) {

            await wait(400);

            if (new URLSearchParams(window.location.search).get('mock') === 'detailerror') {
                throw new Error('Simulasi error');
            }

            const rec = recorded[item.request_no] || {};

            const day = mockDay(item.request_no);

            const startedAt = new Date(day.getTime());
            startedAt.setHours(8, 30);

            const completedAt = rec.completedAt || new Date(day.getTime());

            if (!rec.completedAt) completedAt.setHours(15, 45);

            const materials = rec.usages
                ? Object.keys(rec.usages).map(function (key) { return rec.usages[key]; })
                : MOCK_MATERIALS.map(function (m) {

                    const planned = round2(m.qty_per_unit * item.qty_produced);

                    return {
                        name: m.name,
                        unit: m.unit,
                        qty_planned: planned,
                        qty_used: round2(planned * m.factor)
                    };
                });

            return {
                started_at: startedAt,
                completed_at: completedAt,
                materials: materials,
                shortages: rec.shortages || []
            };
        }
    };


    // ==========================================
    // STATE + ELEMEN
    // ==========================================

    const state = { item: null, opener: null };

    const $ = function (id) { return document.getElementById(id); };

    const els = {
        overlay:     $('pdOverlay'),
        close:       $('pdClose'),
        done:        $('pdDone'),
        requestNo:   $('pdRequestNo'),
        productName: $('pdProductName'),
        productSku:  $('pdProductSku'),
        source:      $('pdSource'),
        thumb:       $('pdThumb'),
        status:      $('pdStatus'),

        loading:     $('pdLoading'),
        loadError:   $('pdLoadError'),
        retry:       $('pdRetry'),
        content:     $('pdContent'),

        requested:   $('pdRequested'),
        produced:    $('pdProduced'),
        percent:     $('pdPercent'),
        bar:         $('pdBar'),
        log:         $('pdLog'),

        materialBody:    $('pdMaterialBody'),
        shortageSection: $('pdShortageSection'),
        shortageText:    $('pdShortageText')
    };


    // ==========================================
    // HELPER
    // ==========================================

    function esc(value) {

        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' };

        return String(value).replace(/[&<>"']/g, function (c) { return map[c]; });
    }

    function fmt(n) {
        return Number(n).toLocaleString('id-ID', { maximumFractionDigits: 2 });
    }

    function fmtDate(date) {

        return date.toLocaleString('id-ID', {
            day: '2-digit', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        }).replace('.', ':');
    }


    // ==========================================
    // RENDER
    // ==========================================

    // Placeholder bawaan di markup, dipakai saat gambar tidak ada atau gagal dimuat
    const THUMB_PLACEHOLDER = els.thumb.innerHTML;

    function renderHeader() {

        const item = state.item;

        const source = sourceMeta(item.source);

        els.requestNo.textContent = item.request_no;
        els.productName.textContent = item.product_name;
        els.productSku.textContent =
            'SKU: ' + item.sku + (item.variant_name ? ' \u00B7 Varian: ' + item.variant_name : '');

        els.source.textContent = source.label;
        els.source.className = 'pq-badge ' + source.cls;

        els.status.textContent = 'COMPLETED';
        els.status.className = 'pq-badge done';

        // Gambar produk (image_url dari backend) atau placeholder
        els.thumb.innerHTML = THUMB_PLACEHOLDER;

        if (item.image_url) {

            const img = document.createElement('img');

            img.alt = item.product_name;

            img.onerror = function () {
                els.thumb.innerHTML = THUMB_PLACEHOLDER;
            };

            img.src = item.image_url;

            els.thumb.innerHTML = '';
            els.thumb.appendChild(img);
        }
    }

    function renderDetail(data) {

        const item = state.item;

        const percent = item.qty_requested > 0
            ? Math.min(100, Math.round(item.qty_produced / item.qty_requested * 100))
            : 0;

        els.requested.textContent = item.qty_requested + ' pcs';
        els.produced.textContent = item.qty_produced + ' pcs';
        els.percent.textContent = percent + '%';
        els.bar.style.width = percent + '%';

        // Log aktivitas
        const logs = [
            { at: data.started_at,   text: 'Produksi dimulai oleh Production' },
            { at: data.completed_at, text: 'Produksi selesai. Hasil: ' + item.qty_produced + ' pcs' }
        ];

        els.log.innerHTML = logs.map(function (entry) {

            return '<li>' +
                '<span class="pd-log-dot"></span>' +
                '<time>' + esc(fmtDate(entry.at)) + '</time>' +
                '<p>' + esc(entry.text) + '</p>' +
            '</li>';

        }).join('');

        // Pemakaian material
        els.materialBody.innerHTML = data.materials.map(function (m, i) {

            const diff = round2(m.qty_used - m.qty_planned);

            let label = 'Sesuai';
            let cls = 'same';

            if (diff > 0) {
                label = '+' + fmt(diff) + ' ' + m.unit;
                cls = 'over';
            } else if (diff < 0) {
                label = '-' + fmt(Math.abs(diff)) + ' ' + m.unit;
                cls = 'under';
            }

            return '<tr>' +
                '<td class="pe-col-no">' + (i + 1) + '</td>' +
                '<td>' + esc(m.name) + '</td>' +
                '<td>' + fmt(m.qty_planned) + ' ' + esc(m.unit) + '</td>' +
                '<td>' + fmt(m.qty_used) + ' ' + esc(m.unit) + '</td>' +
                '<td class="pe-col-status"><span class="pd-diff ' + cls + '">' + esc(label) + '</span></td>' +
            '</tr>';

        }).join('');

        els.shortageSection.hidden = data.shortages.length === 0;

        if (data.shortages.length) {

            els.shortageText.textContent = 'Shortage dilaporkan untuk: ' +
                data.shortages.join(', ') + '.';
        }
    }

    function showLoading() {

        els.loading.hidden = false;
        els.loadError.hidden = true;
        els.content.hidden = true;
    }

    function showError() {

        els.loading.hidden = true;
        els.loadError.hidden = false;
        els.content.hidden = true;
    }

    function showContent() {

        els.loading.hidden = true;
        els.loadError.hidden = true;
        els.content.hidden = false;
    }


    // ==========================================
    // BUKA / TUTUP
    // ==========================================

    async function load() {

        showLoading();

        let data;

        try {

            data = await detailApi.fetchDetail(state.item);

        } catch (error) {

            showError();

            return;
        }

        renderDetail(data);
        showContent();
    }

    function open(item) {

        state.item = item;
        state.opener = document.activeElement;

        renderHeader();

        els.overlay.hidden = false;

        document.body.style.overflow = 'hidden';

        load();

        els.done.focus();
    }

    function close() {

        els.overlay.hidden = true;

        document.body.style.overflow = '';

        if (state.opener && state.opener.focus) state.opener.focus();
    }


    // ==========================================
    // EVENT
    // ==========================================

    els.close.addEventListener('click', close);
    els.done.addEventListener('click', close);
    els.retry.addEventListener('click', load);

    els.overlay.addEventListener('mousedown', function (event) {
        if (event.target === els.overlay) close();
    });

    document.addEventListener('keydown', function (event) {

        if (els.overlay.hidden) return;

        if (event.key === 'Escape') {
            close();
            return;
        }

        // Fokus tetap berputar di dalam modal
        if (event.key === 'Tab') {

            const visible = Array.from(
                els.overlay.querySelectorAll('button:not([disabled])')
            ).filter(function (el) { return el.offsetParent !== null; });

            if (visible.length === 0) return;

            const first = visible[0];
            const last = visible[visible.length - 1];

            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        }
    });

    // Rekam data dari modal eksekusi agar detail sesuai yang baru diisi
    document.addEventListener('production-execution:saved', function (event) {

        const d = event.detail;

        const rec = recorded[d.requestNo] || (recorded[d.requestNo] = { usages: {}, shortages: [] });

        (d.usages || []).forEach(function (u) {

            const prev = rec.usages[u.name] || { name: u.name, unit: u.unit, qty_planned: 0, qty_used: 0 };

            prev.qty_planned = round2(prev.qty_planned + u.qty_planned);
            prev.qty_used = round2(prev.qty_used + u.qty_used);

            rec.usages[u.name] = prev;
        });

        if (d.status === 'COMPLETED') rec.completedAt = new Date();
    });

    document.addEventListener('production-execution:shortage', function (event) {

        const d = event.detail;

        const rec = recorded[d.requestNo] || (recorded[d.requestNo] = { usages: {}, shortages: [] });

        d.items.forEach(function (it) {
            if (rec.shortages.indexOf(it.name) === -1) rec.shortages.push(it.name);
        });
    });


    // ==========================================
    // DIPAKAI OLEH PRODUCTION QUEUE
    // ==========================================

    window.ProductionDetailModal = { open: open };

})();
</script>

@endpush