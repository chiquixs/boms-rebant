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


{{-- Penanda data demonstrasi (hanya muncul jika apiUrl kosong) --}}
<div class="pd-demo" id="pdDemo" hidden>
    <svg width="16" height="16" viewBox="0 0 24 24"
         fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="9"/>
        <path d="M12 8v5"/>
        <path d="M12 16.5h.01"/>
    </svg>
    <span>DEMO. Angka di halaman ini bukan data produksi aktual dan belum berasal dari database.</span>
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
        <span class="pd-stat-note">Stok di bawah minimum</span>
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
                <p>Varian produk yang stoknya rendah.</p>
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
    // ==========================================

    const PD_CONFIG = {
        // Kosongkan (null) untuk memakai DATA DEMO tanpa backend.
        apiUrl: null,   // backend belum siap, jadi memakai DATA DEMO
        period: 'month',
        listLimit: 5
    };


    // ==========================================
    // DATA DEMO
    // Hanya dipakai saat apiUrl kosong. Selalu diberi label DEMO.
    // Bentuk datanya sama dengan respons endpoint backend.
    // ==========================================

    function hoursAgo(h) {
        return new Date(Date.now() - h * 3600 * 1000).toISOString();
    }

    function buildMock(period) {

        const factor = period === '30d' ? 3 : (period === '7d' ? 1 : 2);

        return {
            summary: {
                active_queue: 8,
                in_process: 3,
                produced: 120 * factor,
                period: period,
                period_label: period === '30d' ? '30 hari terakhir' : (period === '7d' ? '7 hari terakhir' : 'Bulan ini'),
                low_stock_variants: 4
            },
            priority_requests: [
                { request_no: 'PR-20261112-001', product_name: 'Frame Kayu Karakter Jepang (10x30)', variant_name: 'V1', sku: 'FKGU-B-N-V1', source: 'ORDER', status: 'IN_PROCESS', qty_requested: 20, qty_produced: 8, remaining: 12 },
                { request_no: 'PR-20261112-002', product_name: 'Malang Tempo Dulu (30x45)', variant_name: 'Alun alun', sku: 'T29A-1', source: 'ORDER', status: 'WAITING', qty_requested: 15, qty_produced: 0, remaining: 15 },
                { request_no: 'PR-20261111-004', product_name: 'Modern Line Art Black & White set isi 3 (20x30)', variant_name: 'Cat', sku: 'PBS3-O32-A-1', source: 'ORDER', status: 'IN_PROCESS', qty_requested: 25, qty_produced: 10, remaining: 15 },
                { request_no: 'PR-20261111-006', product_name: 'Sign Peringatan Hitam (9x30)', variant_name: 'CCTV', sku: 'D6A-1', source: 'RESTOCK', status: 'WAITING', qty_requested: 10, qty_produced: 0, remaining: 10 },
                { request_no: 'PR-20261110-003', product_name: 'Gantungan Gelang Kayu 2 Sisi', variant_name: 'Natural', sku: 'GG-B-N', source: 'RESTOCK', status: 'WAITING', qty_requested: 12, qty_produced: 0, remaining: 12 }
            ],
            low_stock_variants: [
                { product_name: 'Frame Kayu Karakter Jepang (10x30)', variant_name: 'V2', sku: 'FKGU-B-N-V2', current_stock: 2, min_stock: 10, target_stock: 30 },
                { product_name: 'Sign Free Wifi (20x20)', variant_name: 'Hitam Putih', sku: 'N6A-1', current_stock: 5, min_stock: 12, target_stock: 24 },
                { product_name: 'Quotes Black and White (15x20)', variant_name: 'Leave Home', sku: 'I9J-1', current_stock: 8, min_stock: 15, target_stock: 30 },
                { product_name: 'Basecamp Bola Internasional (9x30)', variant_name: 'Juventus', sku: 'D6E-1', current_stock: 14, min_stock: 20, target_stock: 40 }
            ],
            recent_productions: [
                { id: 41, request_no: 'PR-20261112-001', product_name: 'Frame Kayu Karakter Jepang (10x30)', variant_name: 'V1', sku: 'FKGU-B-N-V1', qty_produced: 8, produced_at: hoursAgo(2), production_date: null, recorded_by: 'Rudi Hartono' },
                { id: 40, request_no: 'PR-20261111-004', product_name: 'Modern Line Art Black & White set isi 3 (20x30)', variant_name: 'Cat', sku: 'PBS3-O32-A-1', qty_produced: 10, produced_at: hoursAgo(20), production_date: null, recorded_by: 'Rudi Hartono' },
                { id: 39, request_no: 'PR-20261109-002', product_name: 'Sign Peringatan Hitam (9x30)', variant_name: 'Dilarang Merokok', sku: 'D6A-2', qty_produced: 30, produced_at: hoursAgo(50), production_date: null, recorded_by: 'Sari Wulandari' },
                { id: 38, request_no: 'PR-20261108-005', product_name: 'Sign Free Wifi (20x20)', variant_name: 'Merah Hitam', sku: 'N6A-3', qty_produced: 35, produced_at: null, production_date: '2026-11-08', recorded_by: null }
            ],
            recent_activities: [
                { type: 'logged', request_no: 'PR-20261112-001', product_name: 'Frame Kayu Karakter Jepang (10x30)', variant_name: 'V1', qty: 8, at: hoursAgo(2), by: 'Rudi Hartono' },
                { type: 'started', request_no: 'PR-20261112-001', product_name: 'Frame Kayu Karakter Jepang (10x30)', variant_name: 'V1', qty: null, at: hoursAgo(5), by: null },
                { type: 'completed', request_no: 'PR-20261109-002', product_name: 'Sign Peringatan Hitam (9x30)', variant_name: 'Dilarang Merokok', qty: null, at: hoursAgo(50), by: null },
                { type: 'created', request_no: 'PR-20261112-002', product_name: 'Malang Tempo Dulu (30x45)', variant_name: 'Alun alun', qty: null, at: hoursAgo(52), by: 'Admin Rebant' }
            ]
        };
    }


    // ==========================================
    // LAYER API
    // Satu-satunya tempat yang bergantung pada backend.
    // ==========================================

    const dashboardApi = {

        isMock: !PD_CONFIG.apiUrl,

        async fetch(period) {

            if (PD_CONFIG.apiUrl) {

                const response = await fetch(
                    PD_CONFIG.apiUrl + '?period=' + encodeURIComponent(period),
                    { headers: { 'Accept': 'application/json' } }
                );

                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }

                return await response.json();
            }

            // --- Mode demo ---
            await new Promise(function (resolve) { setTimeout(resolve, 600); });

            const mock = new URLSearchParams(window.location.search).get('mock');

            if (mock === 'error') {
                throw new Error('Simulasi error');
            }

            if (mock === 'empty') {
                return {
                    summary: { active_queue: 0, in_process: 0, produced: 0, period: period, period_label: 'Bulan ini', low_stock_variants: 0 },
                    priority_requests: [],
                    low_stock_variants: [],
                    recent_productions: [],
                    recent_activities: []
                };
            }

            return buildMock(period);
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
        demo: $('pdDemo'),
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

    // Aturan status dan bar sama dengan Stock & Production Planning:
    // LOW jika current_stock < min_stock, skala bar = target_stock.
    function renderLow(items) {

        if (!items.length) {
            els.lowList.innerHTML = emptyHtml('Tidak ada varian dengan stok rendah.');
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

        els.demo.hidden = !(dashboardApi.isMock && !state.error);

        // Error: satu kartu menggantikan isi dashboard, tanpa angka dummy
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