@extends('layouts.produksi')

@section('title', 'Dashboard - REBANT BOMS')
@section('breadcrumb', 'Dashboard')

@push('styles')
    @vite('resources/css/production-dashboard.css')
@endpush

@section('content')

{{-- =========================
    HEADER + FILTER PERIODE
========================= --}}

<div class="pd-header">

    <div class="pq-page-header">
        <h1>Dashboard</h1>
        <p>Ringkasan aktivitas operasional produksi REBANT BOMS.</p>
    </div>

    <div class="pd-period">
        <label for="pdPeriod">Periode hasil produksi</label>

        <select class="pq-select" id="pdPeriod" aria-label="Periode hasil produksi">
            <option value="month">Bulan ini</option>
            <option value="7d">7 hari terakhir</option>
            <option value="30d">30 hari terakhir</option>
        </select>
    </div>

</div>


{{-- =========================
    KARTU STATISTIK
========================= --}}

<div class="pd-stats" id="pdStats" aria-busy="true">

    <a class="pq-stat pd-stat total" href="{{ route('produksi.queue.index') }}">
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
            <span class="pq-stat-label">Antrian Aktif</span>
        </div>
        <strong class="pq-stat-value" id="pdStatActive"><span class="pq-skeleton pd-sk-value"></span></strong>
        <span class="pd-stat-note">Waiting + In Process</span>
    </a>

    <a class="pq-stat pd-stat progress" href="{{ route('produksi.queue.index') }}">
        <div class="pq-stat-head">
            <div class="pq-stat-icon">
                <svg width="20" height="20" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 12a9 9 0 1 1-3-6.7"/>
                    <path d="M21 4v5h-5"/>
                </svg>
            </div>
            <span class="pq-stat-label">Sedang Diproses</span>
        </div>
        <strong class="pq-stat-value" id="pdStatProcess"><span class="pq-skeleton pd-sk-value"></span></strong>
        <span class="pd-stat-note">Permintaan berstatus In Process</span>
    </a>

    <a class="pq-stat pd-stat done" href="{{ route('produksi.history.index') }}">
        <div class="pq-stat-head">
            <div class="pq-stat-icon">
                <svg width="20" height="20" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 8l-9 5-9-5"/>
                    <path d="M3 8l9-5 9 5v8l-9 5-9-5V8Z"/>
                    <path d="M12 13v8"/>
                </svg>
            </div>
            <span class="pq-stat-label">Hasil Produksi</span>
        </div>
        <strong class="pq-stat-value" id="pdStatProduced"><span class="pq-skeleton pd-sk-value"></span></strong>
        <span class="pd-stat-note" id="pdStatProducedNote">Total unit diproduksi</span>
    </a>

    <a class="pq-stat pd-stat low" href="{{ route('produksi.planning.index') }}">
        <div class="pq-stat-head">
            <div class="pq-stat-icon">
                <svg width="20" height="20" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 3 2 20h20L12 3Z"/>
                    <path d="M12 10v4"/>
                    <path d="M12 17h.01"/>
                </svg>
            </div>
            <span class="pq-stat-label">Varian Stok Rendah</span>
        </div>
        <strong class="pq-stat-value" id="pdStatLow"><span class="pq-skeleton pd-sk-value"></span></strong>
        <span class="pd-stat-note">Varian MTS di bawah stok minimum</span>
    </a>

</div>


{{-- =========================
    PANEL (BARIS 2 DAN 3)
========================= --}}

<div class="pd-grid" id="pdGrid">

    {{-- Antrian Prioritas --}}
    <section class="pd-panel" aria-labelledby="pdQueueTitle">
        <div class="pd-panel-head">
            <div>
                <h2 id="pdQueueTitle">Antrian Prioritas</h2>
                <p>Permintaan produksi yang perlu dikerjakan.</p>
            </div>
            <a class="pd-link" href="{{ route('produksi.queue.index') }}">Lihat Semua</a>
        </div>
        <ul class="pd-list" id="pdQueue"></ul>
    </section>

    {{-- Stok Perlu Perhatian --}}
    <section class="pd-panel side" aria-labelledby="pdLowTitle">
        <div class="pd-panel-head">
            <div>
                <h2 id="pdLowTitle">Stok Perlu Perhatian</h2>
                <p>Varian produk MTS yang stoknya di bawah minimum.</p>
            </div>
            <a class="pd-link" href="{{ route('produksi.planning.index') }}">Buka Planning</a>
        </div>
        <ul class="pd-list" id="pdLow"></ul>
    </section>

    {{-- Produksi Terbaru --}}
    <section class="pd-panel" aria-labelledby="pdRecentTitle">
        <div class="pd-panel-head">
            <div>
                <h2 id="pdRecentTitle">Produksi Terbaru</h2>
                <p>Hasil produksi yang baru dicatat.</p>
            </div>
            <a class="pd-link" href="{{ route('produksi.history.index') }}">Lihat Semua</a>
        </div>
        <ul class="pd-list" id="pdRecent"></ul>
    </section>

    {{-- Aktivitas Produksi (tanpa tautan: belum ada halaman tujuan khusus) --}}
    <section class="pd-panel side" aria-labelledby="pdActTitle">
        <div class="pd-panel-head">
            <div>
                <h2 id="pdActTitle">Aktivitas Produksi</h2>
                <p>Aktivitas terakhir yang tercatat di sistem.</p>
            </div>
        </div>
        <ul class="pd-list" id="pdAct"></ul>
    </section>

</div>


{{-- Error state: satu kartu menggantikan seluruh isi dashboard --}}
<div class="pq-card" id="pdErrorCard" hidden>
    <div class="pq-state error" id="pdError">
        <div class="pq-state-icon">
            <svg width="24" height="24" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="12" cy="12" r="9"/>
                <path d="M12 7v6"/>
                <path d="M12 16.5h.01"/>
            </svg>
        </div>
        <h3>Data dashboard gagal dimuat</h3>
        <p>Terjadi kendala saat mengambil ringkasan produksi. Coba muat ulang.</p>
        <button type="button" id="pdRetry">Coba lagi</button>
    </div>
</div>

@endsection


@push('scripts')

<script>
(function () {

    'use strict';


    // ==========================================
    // KONFIGURASI
    // Data diambil dari database lewat ProductionDashboardController@data
    // ==========================================

    const PD_CONFIG = {
        apiUrl: '{{ route("produksi.dashboard.data") }}',
        period: 'month',
        listLimit: 5
    };


    // ==========================================
    // LAYER API
    // ==========================================

    const dashboardApi = {

        async fetch(period) {

            const response = await fetch(
                PD_CONFIG.apiUrl + '?period=' + encodeURIComponent(period),
                {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin'
                }
            );

            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }

            return await response.json();
        }
    };


    // ==========================================
    // STATE + ELEMEN
    // ==========================================

    const state = {
        loading: true,
        error: false,
        data: null,
        period: PD_CONFIG.period
    };

    const $ = function (id) { return document.getElementById(id); };

    const els = {
        period: $('pdPeriod'),
        stats: $('pdStats'),
        grid: $('pdGrid'),
        errorCard: $('pdErrorCard'),
        retry: $('pdRetry'),

        active: $('pdStatActive'),
        process: $('pdStatProcess'),
        produced: $('pdStatProduced'),
        producedNote: $('pdStatProducedNote'),
        low: $('pdStatLow'),

        queue: $('pdQueue'),
        lowList: $('pdLow'),
        recent: $('pdRecent'),
        act: $('pdAct')
    };


    // ==========================================
    // HELPER
    // ==========================================

    function esc(value) {

        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' };

        return String(value === null || value === undefined ? '' : value).replace(/[&<>"']/g, function (char) {
            return map[char];
        });
    }

    function num(value) {
        return (Number(value) || 0).toLocaleString('id-ID');
    }

    const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    function pad(n) {
        return n < 10 ? '0' + n : String(n);
    }

    function fmtDate(value) {

        if (!value) return '-';

        const d = new Date(String(value).length === 10 ? value + 'T00:00:00' : value);

        if (isNaN(d.getTime())) return '-';

        return d.getDate() + ' ' + MONTHS[d.getMonth()] + ' ' + d.getFullYear();
    }

    function fmtDateTime(value) {

        if (!value) return '-';

        const d = new Date(value);

        if (isNaN(d.getTime())) return '-';

        return fmtDate(value) + ', ' + pad(d.getHours()) + '.' + pad(d.getMinutes());
    }

    // Status request: warna sama dengan Production Queue (pq-badge)
    const STATUS_META = {
        WAITING:    { label: 'WAITING',    cls: 'waiting' },
        IN_PROCESS: { label: 'IN_PROCESS', cls: 'progress' },
        COMPLETED:  { label: 'COMPLETED',  cls: 'done' }
    };

    function productSub(item) {
        return 'SKU: ' + esc(item.sku || '-') + (item.variant_name ? ' &middot; Varian: ' + esc(item.variant_name) : '');
    }

    function productBlock(item) {
        return '<div class="pd-prod" title="' + esc(item.product_name) + '">' +
            '<strong>' + esc(item.product_name) + '</strong>' +
            '<small>' + productSub(item) + '</small>' +
            '</div>';
    }

    function emptyHtml(text) {
        return '<li class="pd-empty">' +
            '<span class="pd-empty-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">' +
            '<circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/></svg></span>' +
            esc(text) + '</li>';
    }

    function skeletonRows(count) {

        let html = '';

        for (let i = 0; i < count; i++) {
            html += '<li class="pd-sk-row"><span class="pq-skeleton"></span><span class="pq-skeleton"></span></li>';
        }

        return html;
    }


    // ==========================================
    // RENDER
    // ==========================================

    function renderStats() {

        const ready = !state.loading && !state.error && state.data;
        const s = ready ? state.data.summary : null;

        const skeleton = '<span class="pq-skeleton pd-sk-value"></span>';

        els.stats.setAttribute('aria-busy', ready ? 'false' : 'true');

        els.active.innerHTML = ready ? num(s.active_queue) : skeleton;
        els.process.innerHTML = ready ? num(s.in_process) : skeleton;
        els.produced.innerHTML = ready ? num(s.produced) + '<small>pcs</small>' : skeleton;
        els.low.innerHTML = ready ? num(s.low_stock_variants) : skeleton;

        els.producedNote.textContent = ready
            ? 'Total unit \u00b7 ' + (s.period_label || 'Bulan ini')
            : 'Total unit diproduksi';
    }

    function renderQueue(items) {

        if (!items.length) {
            els.queue.innerHTML = emptyHtml('Tidak ada permintaan produksi aktif.');
            return;
        }

        const head = '<li class="pd-row pd-row-head" aria-hidden="true">' +
            '<span>No. Permintaan</span><span>Produk</span><span class="r">Sisa</span><span>Status</span></li>';

        els.queue.innerHTML = head + items.map(function (item) {

            const status = STATUS_META[item.status] || { label: item.status || '-', cls: 'normal' };

            return '<li class="pd-row pd-q">' +
                '<span class="pd-q-no">' + esc(item.request_no) + '</span>' +
                productBlock(item) +
                '<span class="pd-q-rem"><em>Sisa </em>' + num(item.remaining) + ' pcs</span>' +
                '<span class="pd-q-status pq-badge ' + status.cls + '">' + esc(status.label) + '</span>' +
                '</li>';

        }).join('');
    }

    // Satu baris = satu varian produk MTS dengan current_stock < min_stock.
    // Skala bar = target_stock (atau nilai terbesar jika target kosong).
    function renderLow(items) {

        if (!items.length) {
            els.lowList.innerHTML = emptyHtml('Tidak ada varian MTS dengan stok rendah.');
            return;
        }

        els.lowList.innerHTML = items.map(function (item) {

            const current = Number(item.current_stock) || 0;
            const min = Number(item.min_stock) || 0;
            const target = Number(item.target_stock) || 0;

            const isLow = current < min;
            const scale = Math.max(target, min, current, 1);

            const fill = Math.min(100, (current / scale) * 100);
            const marker = Math.min(100, (min / scale) * 100);

            return '<li class="pd-row pd-stock">' +
                '<div class="pd-stock-top">' + productBlock(item) +
                '<span class="pq-badge ' + (isLow ? 'pd-low' : 'pd-safe') + '">' + (isLow ? 'LOW STOCK' : 'AMAN') + '</span></div>' +
                '<div class="pd-bar" role="img" aria-label="Stok ' + current + ' dari minimum ' + min + '">' +
                '<span class="' + (isLow ? 'low' : 'safe') + '" style="width:' + fill.toFixed(1) + '%"></span>' +
                (min > 0 ? '<i style="left:' + marker.toFixed(1) + '%" title="Stok minimum"></i>' : '') +
                '</div>' +
                '<div class="pd-stock-meta">' +
                '<span>Stok <b class="' + (isLow ? 'low' : '') + '">' + num(current) + '</b></span>' +
                '<span>Minimum <b>' + num(min) + '</b></span>' +
                '<span>Target <b>' + (item.target_stock !== null && item.target_stock !== undefined ? num(target) : '-') + '</b></span>' +
                '</div></li>';

        }).join('');
    }

    function renderRecent(items) {

        if (!items.length) {
            els.recent.innerHTML = emptyHtml('Belum ada hasil produksi.');
            return;
        }

        els.recent.innerHTML = items.map(function (item) {

            const when = item.produced_at ? fmtDateTime(item.produced_at) : fmtDate(item.production_date);

            return '<li class="pd-row pd-recent">' +
                '<span class="pd-recent-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">' +
                '<path d="M21 8l-9 5-9-5"/><path d="M3 8l9-5 9 5v8l-9 5-9-5V8Z"/><path d="M12 13v8"/></svg></span>' +
                productBlock(item) +
                '<span class="pd-recent-qty"><strong>+' + num(item.qty_produced) + ' pcs</strong><small>' + esc(when) + '</small></span>' +
                '</li>';

        }).join('');
    }

    const ACT_META = {
        created: {
            title: 'Permintaan produksi baru',
            icon: '<path d="M12 5v14"/><path d="M5 12h14"/>'
        },
        started: {
            title: 'Produksi dimulai',
            icon: '<path d="M7 5v14l11-7L7 5Z"/>'
        },
        logged: {
            title: 'Hasil produksi dicatat',
            icon: '<path d="M4 20h4L19 9l-4-4L4 16v4Z"/><path d="m13.5 6.5 4 4"/>'
        },
        completed: {
            title: 'Produksi selesai',
            icon: '<path d="m5 12 5 5 9-10"/>'
        }
    };

    function renderActivities(items) {

        if (!items.length) {
            els.act.innerHTML = emptyHtml('Belum ada aktivitas produksi.');
            return;
        }

        els.act.innerHTML = items.map(function (item) {

            const meta = ACT_META[item.type];

            if (!meta) return '';

            const product = esc(item.product_name) + (item.variant_name ? ' (' + esc(item.variant_name) + ')' : '');
            const qty = item.type === 'logged' && item.qty !== null && item.qty !== undefined
                ? ' &middot; +' + num(item.qty) + ' pcs'
                : '';

            return '<li class="pd-row pd-act">' +
                '<span class="pd-act-icon ' + esc(item.type) + '"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">' + meta.icon + '</svg></span>' +
                '<div class="pd-act-body">' +
                '<strong>' + esc(meta.title) + '</strong>' +
                '<p class="pd-act-desc"><b>' + esc(item.request_no) + '</b> &middot; ' + product + qty + '</p>' +
                '<p class="pd-act-meta">' + esc(fmtDateTime(item.at)) + (item.by ? ' &middot; ' + esc(item.by) : '') + '</p>' +
                '</div></li>';

        }).join('');
    }

    function render() {

        // Error: satu kartu menggantikan isi dashboard
        els.errorCard.hidden = !state.error;
        els.stats.hidden = state.error;
        els.grid.hidden = state.error;

        if (state.error) return;

        renderStats();

        if (state.loading) {

            els.queue.innerHTML = skeletonRows(PD_CONFIG.listLimit);
            els.lowList.innerHTML = skeletonRows(PD_CONFIG.listLimit);
            els.recent.innerHTML = skeletonRows(PD_CONFIG.listLimit);
            els.act.innerHTML = skeletonRows(PD_CONFIG.listLimit);

            return;
        }

        const d = state.data;

        renderQueue((d.priority_requests || []).slice(0, PD_CONFIG.listLimit));
        renderLow((d.low_stock_variants || []).slice(0, PD_CONFIG.listLimit));
        renderRecent((d.recent_productions || []).slice(0, PD_CONFIG.listLimit));
        renderActivities((d.recent_activities || []).slice(0, PD_CONFIG.listLimit));
    }


    // ==========================================
    // LOAD DATA
    // ==========================================

    let requestId = 0;

    async function loadData() {

        const current = ++requestId;

        state.loading = true;
        state.error = false;

        render();

        try {

            const data = await dashboardApi.fetch(state.period);

            if (current !== requestId) return;

            state.data = data;

        } catch (error) {

            if (current !== requestId) return;

            console.error('Gagal memuat Dashboard Produksi:', error);

            state.error = true;
        }

        state.loading = false;

        render();
    }

    els.period.addEventListener('change', function () {

        state.period = els.period.value;

        loadData();
    });

    els.retry.addEventListener('click', loadData);


    // ==========================================
    // INIT
    // ==========================================

    loadData();

})();
</script>

@endpush