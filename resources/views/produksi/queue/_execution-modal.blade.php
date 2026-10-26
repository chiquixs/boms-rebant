{{-- =========================
    MODAL: EKSEKUSI PRODUKSI & MATERIAL USAGE
    Dibuka dari tombol "Mulai Produksi" / "Lanjutkan Produksi"
    di Production Queue. Semua data di sini masih SIMULASI.
========================= --}}

<div class="pe-overlay" id="peOverlay" hidden>

    <div class="pe-modal pe-modal-wide"
         role="dialog"
         aria-modal="true"
         aria-labelledby="peTitle">


        {{-- HEADER --}}
        <div class="pe-header">

            <div class="pe-header-icon">
                <svg width="22" height="22" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M12 2v3M12 19v3M2 12h3M19 12h3M4.9 4.9l2.1 2.1M17 17l2.1 2.1M4.9 19.1 7 17M17 7l2.1-2.1"/>
                </svg>
            </div>

            <div class="pe-header-text">
                <h2 id="peTitle">Eksekusi Produksi &amp; Material Usage</h2>
                <p>Request No. <strong id="peRequestNo">-</strong></p>
            </div>

            <button type="button" class="pe-close" id="peClose" aria-label="Tutup">
                <svg width="18" height="18" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6 6 18"/>
                    <path d="m6 6 12 12"/>
                </svg>
            </button>

        </div>


        {{-- BODY --}}
        <div class="pe-body" id="peBody">

            {{-- Info produk --}}
            <div class="pe-product">

                <div class="pe-product-thumb" id="peThumb">
                    <svg width="22" height="22" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="1.6">
                        <rect x="3" y="4" width="18" height="16" rx="2"/>
                        <circle cx="9" cy="10" r="1.5"/>
                        <path d="m21 16-5-5-8 9"/>
                    </svg>
                </div>

                <div class="pe-product-info">
                    <strong id="peProductName">-</strong>
                    <span id="peProductSku">-</span>
                </div>

                <div class="pe-product-badges">
                    <span class="pq-badge" id="peSource">-</span>
                    <span class="pq-badge" id="peStatus">-</span>
                </div>

            </div>


            {{-- Loading --}}
            <div id="peLoading">
                <span class="pq-skeleton pe-skel"></span>
                <span class="pq-skeleton pe-skel"></span>
                <span class="pq-skeleton pe-skel"></span>
                <span class="pq-skeleton pe-skel short"></span>
            </div>


            {{-- Error --}}
            <div class="pe-error-box" id="peLoadError" hidden>
                <strong>Data bahan baku gagal dimuat</strong>
                <p>Terjadi kendala saat mengambil resep produk dan stok bahan baku.</p>
                <button type="button" class="pe-btn pe-btn-secondary" id="peRetry">Coba lagi</button>
            </div>


            {{-- Isi --}}
            <div id="peContent" hidden>

                {{-- 1. CEK MATERIAL --}}
                <section class="pe-section">

                    <h3><span>1.</span> Cek Material</h3>
                    <p class="pe-section-desc" id="peCheckDesc">
                        Bandingkan kebutuhan bahan dengan stok yang tersedia.
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
                            <tbody id="peCheckBody"></tbody>
                        </table>
                    </div>

                    <div class="pe-shortage" id="peShortage" hidden>

                        <div class="pe-shortage-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 3 2 20h20L12 3Z"/>
                                <path d="M12 10v4"/>
                                <path d="M12 17h.01"/>
                            </svg>
                        </div>

                        <div class="pe-shortage-text">
                            <strong>Material Shortage Terdeteksi</strong>
                            <span id="peShortageText">-</span>
                        </div>

                        <button type="button" class="pe-btn pe-btn-danger-outline" id="peReportShortage">
                            Laporkan Shortage
                        </button>

                    </div>

                    <p class="pe-note" id="peShortageNote" hidden>
                        Simulasi: laporan shortage belum tersimpan ke database.
                    </p>

                </section>


                {{-- Pemakaian Aktual dan Hasil Produksi: berdampingan di layar lebar --}}
                <div class="pe-columns">

                {{-- 2. PEMAKAIAN AKTUAL --}}
                <section class="pe-section">

                    <h3><span>2.</span> Pemakaian Aktual</h3>
                    <p class="pe-section-desc">
                        Masukkan jumlah bahan yang benar-benar digunakan.
                    </p>

                    <div class="pe-usage-head">
                        <span>Bahan Baku</span>
                        <span>Jumlah Digunakan</span>
                    </div>

                    <div id="peUsageList"></div>

                </section>


                {{-- 3. HASIL PRODUKSI --}}
                <section class="pe-section">

                    <h3><span>3.</span> Hasil Produksi</h3>
                    <p class="pe-section-desc">
                        Masukkan jumlah barang jadi yang selesai diproduksi pada sesi ini. Gunakan Simpan Progres jika produksi belum selesai.
                    </p>

                                        <div class="pe-progress">

                        <div>
                            <span>Jumlah diminta</span>
                            <strong id="peStatRequested">-</strong>
                        </div>

                        <div>
                            <span>Sudah diproduksi</span>
                            <strong id="peStatProduced">-</strong>
                        </div>

                        <div class="accent">
                            <span>Sisa target</span>
                            <strong id="peStatRemaining">-</strong>
                        </div>

                    </div>

                    <label class="pe-label" for="peQtyProduced">Qty Produced (hasil sesi ini)</label>

                    <div class="pe-input-group" id="peQtyGroup">
                        <input type="number"
                               id="peQtyProduced"
                               min="0"
                               step="1"
                               inputmode="numeric"
                               placeholder="0">
                        <span>Pcs</span>
                    </div>

                    <small class="pe-field-error" id="peQtyError" hidden></small>
                    <small class="pe-hint" id="peQtyHint">-</small>

                </section>


                </div>


                <div class="pe-form-error" id="peFormError" hidden></div>

            </div>

        </div>


        {{-- FOOTER --}}
        <div class="pe-footer">

            <button type="button" class="pe-btn pe-btn-secondary" id="peCancel">
                Batal
            </button>

            <button type="button" class="pe-btn pe-btn-outline" id="peSave">
                Simpan Progres
            </button>

            <button type="button" class="pe-btn pe-btn-primary" id="peFinish">
                Selesaikan Produksi
            </button>

        </div>

    </div>

</div>


@push('scripts')

<script>
(function () {

    'use strict';


    // ==========================================
    // LABEL BADGE (sama dengan Production Queue)
    // ==========================================

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

    const STATUS_META = {
        WAITING:    { label: 'WAITING',    cls: 'waiting' },
        IN_PROCESS: { label: 'IN_PROCESS', cls: 'progress' },
        COMPLETED:  { label: 'COMPLETED',  cls: 'done' }
    };


    // ==========================================
    // DATA SIMULASI (MOCK)
    // Resep (BOM) + stok bahan baku. Hanya untuk tampilan.
    // Nama field mengikuti raw_materials dan product_materials.
    // Hapus saat backend siap.
    // ==========================================

    const MOCK_BOM = [
        { raw_material_id: 1, name: 'Kayu MDF',    unit: 'lembar', qty_per_unit: 0.5,  current_stock: 6 },
        { raw_material_id: 2, name: 'Paku 2 inch', unit: 'pcs',    qty_per_unit: 5,    current_stock: 100 },
        { raw_material_id: 3, name: 'Lem Kayu',    unit: 'botol',  qty_per_unit: 0.1,  current_stock: 2 },
        { raw_material_id: 4, name: 'Cat Kayu',    unit: 'kaleng', qty_per_unit: 0.05, current_stock: 1 }
    ];


    // ==========================================
    // LAYER API (SIMULASI)
    // Ganti isi fungsi ini dengan fetch ke backend saat siap.
    // Fungsi tampilan di bawah tidak perlu diubah.
    //
    // Untuk menguji tampilan, tambahkan di URL:
    //   ?mock=modalerror  -> data bahan baku gagal dimuat
    //   ?mock=savefail    -> simpan gagal
    // ==========================================

    function mockMode() {
        return new URLSearchParams(window.location.search).get('mock');
    }

    function wait(ms) {
        return new Promise(function (resolve) { setTimeout(resolve, ms); });
    }

    const executionApi = {

        async fetchMaterials(item) {

            await wait(450);

            if (mockMode() === 'modalerror') {
                throw new Error('Simulasi error');
            }

            return MOCK_BOM.map(function (m) { return Object.assign({}, m); });
        },

        async save(payload) {

            await wait(700);

            if (mockMode() === 'savefail') {
                throw new Error('Simulasi gagal simpan');
            }

            return { ok: true };
        },

        async reportShortage(payload) {

            await wait(500);

            return { ok: true };
        }
    };


    // ==========================================
    // STATE + ELEMEN
    // ==========================================

    const state = {
        item: null,
        materials: [],
        remaining: 0,
        dirty: false,
        saving: false,
        reported: false,
        opener: null
    };

    const $ = function (id) { return document.getElementById(id); };

    const els = {
        overlay:      $('peOverlay'),
        close:        $('peClose'),
        requestNo:    $('peRequestNo'),
        productName:  $('peProductName'),
        productSku:   $('peProductSku'),
        source:       $('peSource'),
        status:       $('peStatus'),
        thumb:        $('peThumb'),

        loading:      $('peLoading'),
        loadError:    $('peLoadError'),
        retry:        $('peRetry'),
        content:      $('peContent'),

        checkDesc:    $('peCheckDesc'),
        checkBody:    $('peCheckBody'),
        shortage:     $('peShortage'),
        shortageText: $('peShortageText'),
        reportBtn:    $('peReportShortage'),
        shortageNote: $('peShortageNote'),

        usageList:    $('peUsageList'),

        qty:          $('peQtyProduced'),
        qtyGroup:     $('peQtyGroup'),
        qtyError:     $('peQtyError'),
        qtyHint:      $('peQtyHint'),

        statRequested: $('peStatRequested'),
        statProduced:  $('peStatProduced'),
        statRemaining: $('peStatRemaining'),

        formError:    $('peFormError'),
        cancel:       $('peCancel'),
        save:         $('peSave'),
        finish:       $('peFinish')
    };


    // ==========================================
    // HELPER
    // ==========================================

    function esc(value) {

        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' };

        return String(value).replace(/[&<>"']/g, function (c) { return map[c]; });
    }

    function round2(n) {
        return Math.round(n * 100) / 100;
    }

    function fmt(n) {
        return Number(n).toLocaleString('id-ID', { maximumFractionDigits: 2 });
    }

    // Estimasi kebutuhan = qty_per_unit x sisa target produksi
    function needOf(material) {
        return round2(material.qty_per_unit * state.remaining);
    }

    function isShort(material) {
        return needOf(material) > material.current_stock;
    }


    // ==========================================
    // RENDER
    // ==========================================

    // Placeholder bawaan di markup, dipakai saat gambar tidak ada atau gagal dimuat
    const THUMB_PLACEHOLDER = els.thumb.innerHTML;

    function renderHeader() {

        const item = state.item;

        const source = sourceMeta(item.source);
        const status = STATUS_META[item.status] || { label: item.status || '-', cls: 'normal' };

        els.requestNo.textContent = item.request_no;
        els.productName.textContent = item.product_name;
        els.productSku.textContent =
            'SKU: ' + item.sku + (item.variant_name ? ' \u00B7 Varian: ' + item.variant_name : '');

        els.source.textContent = source.label;
        els.source.className = 'pq-badge ' + source.cls;

        els.status.textContent = status.label;
        els.status.className = 'pq-badge ' + status.cls;

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

    function renderMaterials() {

        const rows = state.materials;

        // 1. Cek material
        els.checkDesc.textContent =
            'Estimasi kebutuhan bahan untuk ' + state.remaining +
            ' pcs yang belum diproduksi, dibandingkan dengan stok yang tersedia.';

        els.checkBody.innerHTML = rows.map(function (m, i) {

            const short = isShort(m);

            return '<tr>' +
                '<td class="pe-col-no">' + (i + 1) + '</td>' +
                '<td>' + esc(m.name) + '</td>' +
                '<td>' + fmt(needOf(m)) + ' ' + esc(m.unit) + '</td>' +
                '<td>' + fmt(m.current_stock) + ' ' + esc(m.unit) + '</td>' +
                '<td class="pe-col-status"><span class="pe-status ' + (short ? 'low' : 'ok') + '">' +
                    (short ? 'Kurang' : 'Cukup') + '</span></td>' +
            '</tr>';

        }).join('');

        // Shortage
        const shortList = rows.filter(isShort);

        els.shortage.hidden = shortList.length === 0;

        els.shortageText.textContent = 'Kekurangan ' + shortList.map(function (m) {
            return m.name + ' sebanyak ' + fmt(round2(needOf(m) - m.current_stock)) + ' ' + m.unit;
        }).join(', ') + '.';

        // 2. Pemakaian aktual (prefill: kebutuhan, maksimal sebesar stok)
        els.usageList.innerHTML = rows.map(function (m) {

            const prefill = Math.min(needOf(m), m.current_stock);

            return '<div class="pe-usage-row">' +
                '<label for="peUsage' + m.raw_material_id + '">' + esc(m.name) + '</label>' +
                '<div>' +
                    '<div class="pe-input-group">' +
                        '<input type="number" min="0" step="any" inputmode="decimal"' +
                            ' id="peUsage' + m.raw_material_id + '"' +
                            ' data-material-id="' + m.raw_material_id + '"' +
                            ' value="' + prefill + '">' +
                        '<span>' + esc(m.unit) + '</span>' +
                    '</div>' +
                    '<small class="pe-hint">Estimasi: ' + fmt(needOf(m)) + ' ' + esc(m.unit) + '</small>' +
                    '<small class="pe-field-error" data-error-for="' + m.raw_material_id + '" hidden></small>' +
                '</div>' +
            '</div>';

        }).join('');

        // 3. Hasil produksi
        els.qty.value = '';

        els.statRequested.textContent = state.item.qty_requested + ' pcs';
        els.statProduced.textContent  = state.item.qty_produced + ' pcs';
        els.statRemaining.textContent = state.remaining + ' pcs';

        updateQtyHint();

        resetShortageButton();
    }

    // Petunjuk di bawah input Qty Produced, ikut berubah saat user mengetik
    function updateQtyHint() {

        const raw = els.qty.value.trim();
        const qty = Number(raw);

        if (raw !== '' && Number.isInteger(qty) && qty >= 0 && qty <= state.remaining) {

            els.qtyHint.textContent =
                'Setelah disimpan, total produksi menjadi ' + (state.item.qty_produced + qty) +
                ' dari ' + state.item.qty_requested + ' pcs.';

            return;
        }

        els.qtyHint.textContent =
            'Isi jumlah barang jadi yang selesai pada sesi ini (maksimal ' + state.remaining + ' pcs).';
    }

    function resetShortageButton() {

        els.reportBtn.disabled = state.reported;
        els.reportBtn.textContent = state.reported ? 'Sudah dilaporkan' : 'Laporkan Shortage';

        els.shortageNote.hidden = !state.reported;
    }

    function setActionsDisabled(disabled) {

        els.save.disabled = disabled;
        els.finish.disabled = disabled;
    }

    function showLoading() {

        els.loading.hidden = false;
        els.loadError.hidden = true;
        els.content.hidden = true;

        setActionsDisabled(true);
    }

    function showError() {

        els.loading.hidden = true;
        els.loadError.hidden = false;
        els.content.hidden = true;

        setActionsDisabled(true);
    }

    function showContent() {

        els.loading.hidden = true;
        els.loadError.hidden = true;
        els.content.hidden = false;

        setActionsDisabled(false);
    }


    // ==========================================
    // BUKA / TUTUP
    // ==========================================

    async function loadMaterials() {

        showLoading();

        try {

            state.materials = await executionApi.fetchMaterials(state.item);

        } catch (error) {

            showError();

            return;
        }

        renderMaterials();
        showContent();

        const first = els.usageList.querySelector('input');

        if (first) first.focus();
    }

    function open(item) {

        state.item = item;
        state.remaining = Math.max(0, item.qty_requested - item.qty_produced);
        state.dirty = false;
        state.saving = false;
        state.reported = false;
        state.opener = document.activeElement;

        clearErrors();

        renderHeader();

        els.overlay.hidden = false;

        document.body.style.overflow = 'hidden';

        els.save.textContent = 'Simpan Progres';
        els.finish.textContent = 'Selesaikan Produksi';

        loadMaterials();
    }

    function close(force) {

        if (state.saving) return;

        if (!force && state.dirty &&
            !window.confirm('Perubahan belum disimpan. Tutup modal?')) {
            return;
        }

        els.overlay.hidden = true;

        document.body.style.overflow = '';

        if (state.opener && state.opener.focus) state.opener.focus();
    }


    // ==========================================
    // VALIDASI
    // ==========================================

    function clearErrors() {

        els.formError.hidden = true;
        els.qtyError.hidden = true;
        els.qtyGroup.classList.remove('invalid');

        els.usageList.querySelectorAll('.pe-field-error').forEach(function (el) {
            el.hidden = true;
        });

        els.usageList.querySelectorAll('.pe-input-group').forEach(function (el) {
            el.classList.remove('invalid');
        });
    }

    function setUsageError(input, message) {

        const group = input.closest('.pe-input-group');
        const error = els.usageList.querySelector('[data-error-for="' + input.dataset.materialId + '"]');

        group.classList.add('invalid');
        error.textContent = message;
        error.hidden = false;
    }

    function validate() {

        clearErrors();

        let firstInvalid = null;

        const usages = [];

        // Pemakaian aktual
        els.usageList.querySelectorAll('input').forEach(function (input) {

            const material = state.materials.find(function (m) {
                return String(m.raw_material_id) === input.dataset.materialId;
            });

            const raw = input.value.trim();
            const value = Number(raw);

            let message = '';

            if (raw === '') {
                message = 'Wajib diisi.';
            } else if (!isFinite(value) || value < 0) {
                message = 'Isi angka 0 atau lebih.';
            } else if (value > material.current_stock) {
                message = 'Melebihi stok tersedia (' + fmt(material.current_stock) + ' ' + material.unit + ').';
            }

            if (message) {

                setUsageError(input, message);

                if (!firstInvalid) firstInvalid = input;

                return;
            }

            usages.push({
                raw_material_id: material.raw_material_id,
                qty_planned: needOf(material),
                qty_used: value
            });
        });

        // Hasil produksi
        const rawQty = els.qty.value.trim();
        const qty = Number(rawQty);

        let qtyMessage = '';

        if (rawQty === '') {
            qtyMessage = 'Wajib diisi.';
        } else if (!Number.isInteger(qty) || qty < 0) {
            qtyMessage = 'Isi bilangan bulat 0 atau lebih.';
        } else if (qty > state.remaining) {
            qtyMessage = 'Melebihi sisa target (' + state.remaining + ' pcs).';
        }

        if (qtyMessage) {

            els.qtyGroup.classList.add('invalid');
            els.qtyError.textContent = qtyMessage;
            els.qtyError.hidden = false;

            if (!firstInvalid) firstInvalid = els.qty;
        }

        if (firstInvalid) {

            firstInvalid.focus();

            return null;
        }

        return { qty: qty, usages: usages };
    }


    // ==========================================
    // SIMPAN
    // ==========================================

    // mode: 'progress' -> status IN_PROCESS (tombol di tabel jadi "Lanjutkan Produksi")
    //       'finish'   -> status COMPLETED   (tombol di tabel jadi "Lihat Detail")
    async function handleSubmit(mode) {

        if (state.saving) return;

        const result = validate();

        if (!result) return;

        const totalProduced = state.item.qty_produced + result.qty;
        const isFinish = mode === 'finish';

        if (isFinish) {

            if (totalProduced <= 0) {

                els.qtyGroup.classList.add('invalid');
                els.qtyError.textContent = 'Isi hasil produksi minimal 1 pcs untuk menyelesaikan produksi.';
                els.qtyError.hidden = false;
                els.qty.focus();

                return;
            }

            if (totalProduced < state.item.qty_requested &&
                !window.confirm('Total hasil ' + totalProduced + ' pcs masih di bawah target ' +
                    state.item.qty_requested + ' pcs. Tetap selesaikan produksi?')) {
                return;
            }
        }

        const payload = {
            request_no: state.item.request_no,
            qty_produced_this_session: result.qty,
            finish: isFinish,
            usages: result.usages
        };

        const button = isFinish ? els.finish : els.save;
        const idleLabel = button.textContent;

        state.saving = true;

        setActionsDisabled(true);
        button.textContent = 'Menyimpan...';

        try {

            await executionApi.save(payload);

        } catch (error) {

            state.saving = false;

            setActionsDisabled(false);
            button.textContent = idleLabel;

            els.formError.textContent = 'Data gagal disimpan. Periksa koneksi lalu coba lagi.';
            els.formError.hidden = false;

            return;
        }

        state.saving = false;

        document.dispatchEvent(new CustomEvent('production-execution:saved', {
            detail: {
                requestNo: state.item.request_no,
                qtyProduced: totalProduced,
                status: isFinish ? 'COMPLETED' : 'IN_PROCESS',
                mode: mode,
                usages: result.usages.map(function (u) {
                    const m = state.materials.find(function (x) { return x.raw_material_id === u.raw_material_id; });
                    return { name: m.name, unit: m.unit, qty_planned: u.qty_planned, qty_used: u.qty_used };
                }),
                simulated: true
            }
        }));

        close(true);
    }


    // ==========================================
    // LAPORKAN SHORTAGE
    // ==========================================

    async function handleReportShortage() {

        if (state.reported) return;

        const items = state.materials.filter(isShort).map(function (m) {
            return {
                raw_material_id: m.raw_material_id,
                qty_needed: needOf(m),
                qty_available: m.current_stock
            };
        });

        els.reportBtn.disabled = true;
        els.reportBtn.textContent = 'Melaporkan...';

        try {

            await executionApi.reportShortage({
                request_no: state.item.request_no,
                items: items
            });

        } catch (error) {

            els.reportBtn.disabled = false;
            els.reportBtn.textContent = 'Laporkan Shortage';

            return;
        }

        state.reported = true;

        resetShortageButton();

        document.dispatchEvent(new CustomEvent('production-execution:shortage', {
            detail: {
                requestNo: state.item.request_no,
                items: items.map(function (it) {
                    const m = state.materials.find(function (x) { return x.raw_material_id === it.raw_material_id; });
                    return Object.assign({ name: m.name }, it);
                }),
                simulated: true
            }
        }));
    }


    // ==========================================
    // EVENT
    // ==========================================

    els.close.addEventListener('click', function () { close(false); });
    els.cancel.addEventListener('click', function () { close(false); });
    els.retry.addEventListener('click', loadMaterials);
    els.save.addEventListener('click', function () { handleSubmit('progress'); });
    els.finish.addEventListener('click', function () { handleSubmit('finish'); });
    els.reportBtn.addEventListener('click', handleReportShortage);

    // Klik area gelap di luar modal
    els.overlay.addEventListener('mousedown', function (event) {
        if (event.target === els.overlay) close(false);
    });

    // Tandai ada perubahan yang belum disimpan
    els.content.addEventListener('input', function () {
        state.dirty = true;
    });

    els.qty.addEventListener('input', updateQtyHint);

    document.addEventListener('keydown', function (event) {

        if (els.overlay.hidden) return;

        if (event.key === 'Escape') {
            close(false);
            return;
        }

        // Fokus tetap berputar di dalam modal
        if (event.key === 'Tab') {

            const focusable = els.overlay.querySelectorAll(
                'button:not([disabled]), input:not([disabled])'
            );

            const visible = Array.from(focusable).filter(function (el) {
                return el.offsetParent !== null;
            });

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


    // ==========================================
    // DIPAKAI OLEH PRODUCTION QUEUE
    // ==========================================

    window.ProductionExecutionModal = { open: open };

})();
</script>

@endpush