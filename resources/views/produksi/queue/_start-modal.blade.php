{{-- =========================
    MODAL: KONFIRMASI MULAI PRODUKSI (read-only)
========================= --}}

<div class="pe-overlay" id="psOverlay" hidden>

    <div class="pe-modal" role="dialog" aria-modal="true" aria-labelledby="psTitle">

        <div class="pe-header">

            <div class="pe-header-icon">
                <svg width="22" height="22" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="12" r="9" />
                    <path d="m8 12 3 3 5-6" />
                </svg>
            </div>

            <div class="pe-header-text">
                <h2 id="psTitle">Mulai Produksi</h2>
                <p>Request No. <strong id="psRequestNo">-</strong></p>
            </div>

            <button type="button" class="pe-close" id="psClose" aria-label="Tutup">
                <svg width="18" height="18" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
                </svg>
            </button>

        </div>

        <div class="pe-body">

            <div class="pe-product">
                <div class="pe-product-thumb" id="psThumb">
                    <svg width="22" height="22" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="1.6">
                        <rect x="3" y="4" width="18" height="16" rx="2" />
                        <circle cx="9" cy="10" r="1.5" />
                        <path d="m21 16-5-5-8 9" />
                    </svg>
                </div>
                <div class="pe-product-info">
                    <strong id="psProductName">-</strong>
                    <span id="psProductSku">-</span>
                </div>
                <div class="pe-product-badges">
                    <span class="pq-badge" id="psSource">-</span>
                    <strong id="psQtyRequested" style="margin-left:8px;">-</strong>
                </div>
            </div>

            <div id="psLoading">
                <span class="pq-skeleton pe-skel"></span>
                <span class="pq-skeleton pe-skel"></span>
                <span class="pq-skeleton pe-skel short"></span>
            </div>

            <div class="pe-error-box" id="psLoadError" hidden>
                <strong>Data bahan baku gagal dimuat</strong>
                <p>Terjadi kendala saat mengambil resep produk dan stok bahan baku.</p>
                <button type="button" class="pe-btn pe-btn-secondary" id="psRetry">Coba lagi</button>
            </div>

            <div id="psContent" hidden>

                <section class="pe-section">
                    <h3><span>1.</span> Kebutuhan Bahan Baku</h3>
                    <p class="pe-section-desc">
                        Estimasi kebutuhan untuk seluruh target produksi (<strong id="psQtyNote">-</strong> pcs),
                        dibandingkan dengan stok yang tersedia saat ini.
                    </p>

                    <div class="pe-table-wrap">
                        <table class="pe-table">
                            <thead>
                                <tr>
                                    <th class="pe-col-no">No.</th>
                                    <th>Bahan Baku</th>
                                    <th>Kebutuhan</th>
                                    <th>Stok Tersedia</th>
                                    <th class="pe-col-status">Status</th>
                                </tr>
                            </thead>
                            <tbody id="psMaterialBody"></tbody>
                        </table>
                    </div>

                    <div class="pe-shortage" id="psShortageWarn" hidden>
                        <div class="pe-shortage-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 3 2 20h20L12 3Z" />
                                <path d="M12 10v4" />
                                <path d="M12 17h.01" />
                            </svg>
                        </div>
                        <div class="pe-shortage-text">
                            <strong>Ada bahan yang stoknya belum cukup</strong>
                            <span>Produksi tetap bisa dimulai. Laporkan shortage nanti saat menyelesaikan produksi kalau bahan tidak mencukupi.</span>
                        </div>
                    </div>
                </section>

                <div class="pe-form-error" id="psFormError" hidden></div>

            </div>

        </div>

        <div class="pe-footer">
            <button type="button" class="pe-btn pe-btn-secondary" id="psCancel">Batal</button>
            <button type="button" class="pe-btn pe-btn-primary" id="psConfirm">OK, Mulai Produksi</button>
        </div>

    </div>

</div>


@push('scripts')
<script>
    (function() {

        'use strict';

        function sourceMeta(source) {
            if (source === 'ORDER') return {
                label: 'Pesanan Pelanggan (MTO)',
                cls: 'order'
            };
            if (source === 'MTS_MIN_STOCK' || source === 'MTS-MIN-STOCK' || source === 'RESTOCK') {
                return {
                    label: 'Pengisian Stok (MTS)',
                    cls: 'mts'
                };
            }
            return {
                label: source ? String(source) : '-',
                cls: 'normal'
            };
        }

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
                const body = await res.json().catch(() => null);
                throw new Error(body?.message || 'Request gagal (' + res.status + ')');
            }
            return res.json();
        }

        const $ = function(id) {
            return document.getElementById(id);
        };

        const els = {
            overlay: $('psOverlay'),
            close: $('psClose'),
            cancel: $('psCancel'),
            confirm: $('psConfirm'),
            requestNo: $('psRequestNo'),
            thumb: $('psThumb'),
            name: $('psProductName'),
            sku: $('psProductSku'),
            source: $('psSource'),
            qtyReq: $('psQtyRequested'),
            qtyNote: $('psQtyNote'),
            loading: $('psLoading'),
            loadError: $('psLoadError'),
            retry: $('psRetry'),
            content: $('psContent'),
            materialBody: $('psMaterialBody'),
            shortageWarn: $('psShortageWarn'),
            formError: $('psFormError')
        };

        const THUMB_PLACEHOLDER = els.thumb.innerHTML;

        const state = {
            item: null,
            opener: null,
            saving: false
        };

        function esc(value) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            };
            return String(value).replace(/[&<>"']/g, function(c) {
                return map[c];
            });
        }

        function fmt(n) {
            return Number(n).toLocaleString('id-ID', {
                maximumFractionDigits: 2
            });
        }

        function renderHeader() {

            const item = state.item;
            const source = sourceMeta(item.source);

            els.requestNo.textContent = item.request_no;
            els.name.textContent = item.product_name;
            els.sku.textContent = 'SKU: ' + item.sku + (item.variant_name ? ' \u00B7 Varian: ' + item.variant_name : '');
            els.source.textContent = source.label;
            els.source.className = 'pq-badge ' + source.cls;
            els.qtyReq.textContent = item.qty_requested + ' pcs diminta';
            els.qtyNote.textContent = item.qty_requested;

            els.thumb.innerHTML = THUMB_PLACEHOLDER;

            if (item.image_url) {
                const img = document.createElement('img');
                img.alt = item.product_name;
                img.onerror = function() {
                    els.thumb.innerHTML = THUMB_PLACEHOLDER;
                };
                img.src = item.image_url;
                els.thumb.innerHTML = '';
                els.thumb.appendChild(img);
            }
        }

        async function loadMaterials() {

            els.loading.hidden = false;
            els.loadError.hidden = true;
            els.content.hidden = true;
            els.confirm.disabled = true;

            let materials;

            try {
                materials = await fetchJson('/produksi/queue/' + encodeURIComponent(state.item.request_no) + '/materials');
            } catch (error) {
                els.loading.hidden = true;
                els.loadError.hidden = false;
                return;
            }

            const anyShort = materials.some(function(m) {
                return m.qty_per_unit * state.item.qty_requested > m.current_stock;
            });

            els.materialBody.innerHTML = materials.map(function(m, i) {

                const need = m.is_discrete ?
                    Math.ceil(m.qty_per_unit * state.item.qty_requested) :
                    Math.round(m.qty_per_unit * state.item.qty_requested * 100) / 100;

                const short = need > m.current_stock;

                return '<tr>' +
                    '<td class="pe-col-no">' + (i + 1) + '</td>' +
                    '<td>' + esc(m.name) + '</td>' +
                    '<td>' + fmt(need) + ' ' + esc(m.unit) + '</td>' +
                    '<td>' + fmt(m.current_stock) + ' ' + esc(m.unit) + '</td>' +
                    '<td class="pe-col-status"><span class="pe-status ' + (short ? 'low' : 'ok') + '">' +
                    (short ? 'Kurang' : 'Cukup') + '</span></td>' +
                    '</tr>';
            }).join('');

            els.shortageWarn.hidden = !anyShort;

            els.loading.hidden = true;
            els.content.hidden = false;
            els.confirm.disabled = false;
        }

        function open(item) {

            state.item = item;
            state.opener = document.activeElement;
            state.saving = false;

            els.formError.hidden = true;
            renderHeader();

            els.overlay.hidden = false;
            document.body.style.overflow = 'hidden';

            loadMaterials();

            els.confirm.focus();
        }

        function close(force) {
            if (!force && state.saving) return;
            els.overlay.hidden = true;
            document.body.style.overflow = '';
            if (state.opener && state.opener.focus) state.opener.focus();
        }

        async function confirmStart() {

            if (state.saving) return;

            state.saving = true;
            els.confirm.disabled = true;
            els.cancel.disabled = true;
            els.confirm.textContent = 'Memulai...';
            els.formError.hidden = true;

            try {

                await fetchJson('/produksi/queue/' + encodeURIComponent(state.item.request_no) + '/start', {
                    method: 'POST',
                });

                document.dispatchEvent(new CustomEvent('production-queue:started', {
                    detail: {
                        requestNo: state.item.request_no
                    }
                }));

                close(true); 

            } catch (error) {

                els.formError.textContent = error.message || 'Gagal memulai produksi. Coba lagi.';
                els.formError.hidden = false;

            } finally {

                state.saving = false;
                els.confirm.disabled = false;
                els.cancel.disabled = false;
                els.confirm.textContent = 'OK, Mulai Produksi';
            }
        }

        els.close.addEventListener('click', close);
        els.cancel.addEventListener('click', close);
        els.retry.addEventListener('click', loadMaterials);
        els.confirm.addEventListener('click', confirmStart);

        els.overlay.addEventListener('mousedown', function(event) {
            if (event.target === els.overlay) close();
        });

        document.addEventListener('keydown', function(event) {
            if (els.overlay.hidden) return;
            if (event.key === 'Escape') close();
        });

        window.ProductionStartModal = {
            open: open
        };

    })();
</script>
@endpush