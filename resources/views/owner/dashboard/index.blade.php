@extends('layouts.owner')

@section('title', 'Dashboard Owner - REBANT BOMS')

@section('content')

<div class="owner-dashboard">

    {{-- ========================================
        PAGE HEADER
    ========================================= --}}
    <div class="owner-dashboard-header">

        <div>
            <h1>Dashboard Owner</h1>
            <p>Ringkasan performa bisnis REBANT dalam satu tampilan.</p>
        </div>

        <div class="owner-date-filter" id="ownerDateFilterWrap">

            <button type="button" id="ownerDateFilterBtn" class="odf-trigger">
                <svg width="15" height="15" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="5" width="18" height="16" rx="2" />
                    <path d="M16 3v4M8 3v4M3 10h18" />
                </svg>
                <span>{{ \Carbon\Carbon::parse($filterStart)->translatedFormat('d M Y') }} – {{ \Carbon\Carbon::parse($filterEnd)->translatedFormat('d M Y') }}</span>
            </button>

            <div id="ownerDateFilterPopover" class="odf-popover" @if($dateError) data-open-on-load="1" @endif hidden>

                <form method="GET" action="{{ route('owner.dashboard') }}">

                    @if ($dateError)
                    <div class="odf-error">{{ $dateError }}</div>
                    @endif

                    <label class="odf-field">
                        Dari tanggal
                        <input type="date" name="start" value="{{ $inputStart }}" class="odf-input">
                    </label>

                    <label class="odf-field">
                        Sampai tanggal
                        <input type="date" name="end" value="{{ $inputEnd }}" class="odf-input">
                    </label>

                    <button type="submit" class="odf-apply">Terapkan</button>

                    <a href="{{ route('owner.dashboard') }}" class="odf-reset">Reset ke 30 hari terakhir</a>

                </form>

            </div>

        </div>

        <style>

        </style>

    </div>


    {{-- ========================================
        SUMMARY CARDS
    ========================================= --}}
    <div class="owner-summary-grid">

        {{-- TOTAL PESANAN --}}
        <div class="owner-summary-card">

            <div class="owner-summary-icon green">
                <svg width="23" height="23" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1" />
                    <circle cx="20" cy="21" r="1" />
                    <path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6" />
                </svg>
            </div>

            <div class="owner-summary-content">
                <span class="owner-summary-label">Total Pesanan</span>
                <strong class="owner-summary-value">{{ number_format($totalOrders) }}</strong>
                <span class="owner-summary-caption">{{ $orderGrowth >= 0 ? '↑' : '↓' }} {{ abs($orderGrowth) }}% vs periode sebelumnya</span>
            </div>

        </div>


        {{-- TOTAL PENJUALAN --}}
        <div class="owner-summary-card">

            <div class="owner-summary-icon yellow">
                <svg width="23" height="23" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2v20" />
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                </svg>
            </div>

            <div class="owner-summary-content">
                <span class="owner-summary-label">Total Penjualan</span>
                <strong class="owner-summary-value currency">Rp {{ number_format($totalSales, 0, ',', '.') }}</strong>
                <span class="owner-summary-caption">{{ $salesGrowth >= 0 ? '↑' : '↓' }} {{ abs($salesGrowth) }}% vs periode sebelumnya</span>
            </div>

        </div>


        {{-- PRODUK TERJUAL --}}
        <div class="owner-summary-card">

            <div class="owner-summary-icon gray">
                <svg width="23" height="23" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="m12 3 9 5-9 5-9-5 9-5Z" />
                    <path d="m3 8 9 5 9-5" />
                    <path d="M3 8v9l9 5 9-5V8" />
                </svg>
            </div>

            <div class="owner-summary-content">
                <span class="owner-summary-label">Produk Terjual</span>
                <strong class="owner-summary-value">1.245</strong>
                <span class="owner-summary-caption">↑ 15% vs periode sebelumnya</span>
            </div>

        </div>


        {{-- STOK MINIMUM --}}
        <div class="owner-summary-card">

            <div class="owner-summary-icon orange">
                <svg width="23" height="23" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="m10.3 3.9-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.7-3.1l-8-14a2 2 0 0 0-3.4 0Z" />
                    <path d="M12 9v4M12 17h.01" />
                </svg>
            </div>

            <div class="owner-summary-content">
                <span class="owner-summary-label">Stok Minimum</span>
                <strong class="owner-summary-value">{{ $lowStockCount }}</strong>
                <span class="owner-summary-caption">SKU perlu diperhatikan</span>
            </div>

        </div>

    </div>



    {{-- ========================================
    SALES & TOP PRODUCTS
========================================= --}}

    <div class="owner-dashboard-grid">

        {{-- SALES CHART --}}
        <div class="owner-card">
            <div class="owner-card-header">
                <div>
                    <h2>Tren Penjualan</h2>
                    <p>Performa penjualan dari {{ \Carbon\Carbon::parse($filterStart)->translatedFormat('d M Y') }} sampai {{ \Carbon\Carbon::parse($filterEnd)->translatedFormat('d M Y') }}.</p>
                </div>
            </div>

            <div class="owner-chart-content">
                <div class="owner-chart-legend" id="ownerChartLegend"></div>
            </div>

            <div class="owner-chart">
                <svg id="ownerSalesChart" viewBox="0 0 650 245"
                    role="img" aria-label="Grafik tren penjualan"
                    data-start="{{ $filterStart }}" data-end="{{ $filterEnd }}">
                    role="img" aria-label="Grafik tren penjualan">
                    <defs id="ownerChartGradients"></defs>
                    <g id="ownerChartGrid"></g>
                    <g id="ownerChartLines"></g>
                    <g id="ownerChartLabels"></g>
                </svg>
            </div>
        </div>

        {{-- TOP PRODUCTS --}}
        <div class="owner-card">
            <div class="owner-card-header">
                <div>
                    <h2>Produk Terlaris</h2>
                    <p>Produk dengan penjualan tertinggi.</p>
                </div>

                <a href="#" class="owner-view-all">
                    Lihat Semua
                    <span>→</span>
                </a>
            </div>

            <div class="owner-product-list">
                @forelse ($topProducts as $index => $p)
                <div class="owner-product-item">
                    <span class="owner-product-rank">
                        {{ $index + 1 }}
                    </span>

                    <div class="owner-product-image">
                        <svg width="22" height="22" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="m12 3 9 5-9 5-9-5 9-5Z" />
                            <path d="m3 8 9 5 9-5M3 8v9l9 5 9-5V8" />
                        </svg>
                    </div>

                    <div class="owner-product-info">
                        <strong>{{ $p['name'] }}</strong>
                        <span>{{ $p['sku'] }}</span>
                    </div>

                    <div class="owner-product-sales">
                        <strong>{{ number_format($p['qty']) }} pcs</strong>
                        <div class="owner-product-progress">
                            <span @style(['width: ' . min(100, $p[' percentage'] ?? 0) . '%' ])></span>
                        </div>
                        <span>{{ $p['percentage'] }}%</span>
                    </div>
                </div>
                @empty
                <p style="color:#8A8D86; font-size:13px;">
                    Belum ada data penjualan 30 hari terakhir.
                </p>
                @endforelse
            </div>
        </div>

    </div>



    {{-- ========================================
        PRODUCTION & INVENTORY
    ========================================= --}}
    <div class="owner-dashboard-grid owner-dashboard-grid-bottom">

        {{-- PRODUCTION MONITORING --}}
        <div class="owner-card">

            <div class="owner-card-header">
                <div>
                    <h2>Monitoring Produksi</h2>
                    <p>Status produksi dan antrean terbaru.</p>
                </div>

                <a href="#" class="owner-view-all">
                    Lihat Semua
                    <span>→</span>
                </a>
            </div>

            <div class="owner-production-content">

                <div class="owner-production-overview">

                    @php
                    $dashOffset = 251.2 - (251.2 * $productionProgress / 100);
                    @endphp

                    <div class="owner-production-chart">
                        <svg viewBox="0 0 100 100">
                            <circle class="track" cx="50" cy="50" r="40" />
                            <circle class="progress" cx="50" cy="50" r="40"
                                stroke-dasharray="251.2"
                                stroke-dashoffset="{{ $dashOffset }}" />
                        </svg>

                        <div class="owner-production-chart-label">
                            <strong>{{ $productionProgress }}%</strong>
                            <span>Progress Produksi</span>
                        </div>
                    </div>

                    <div class="owner-production-divider"></div>

                    <div class="owner-production-status">
                        <div class="owner-production-status-item">
                            <div class="owner-production-status-label">
                                <i class="process"></i>
                                Dalam Proses
                            </div>
                            <strong>{{ $inProcess }} order</strong>
                        </div>

                        <div class="owner-production-status-item">
                            <div class="owner-production-status-label">
                                <i class="waiting"></i>
                                Menunggu Produksi
                            </div>
                            <strong>{{ $waiting }} order</strong>
                        </div>

                        <div class="owner-production-status-item">
                            <div class="owner-production-status-label">
                                <i class="completed"></i>
                                Selesai Hari Ini
                            </div>
                            <strong>{{ $completedToday }} order</strong>
                        </div>
                    </div>

                </div>

                @if ($urgentCount > 0)
                <div class="owner-production-alert">
                    <svg width="19" height="19" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M12 8v4M12 16h.01" />
                    </svg>

                    <span>{{ $urgentCount }} produk perlu segera diproduksi</span>

                    <svg width="15" height="15" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m9 18 6-6-6-6" />
                    </svg>
                </div>
                @endif

            </div>

        </div>


        {{-- INVENTORY MONITORING --}}
        <div class="owner-card">

            <div class="owner-card-header">
                <div>
                    <h2>Persediaan</h2>
                    <p>Ringkasan stok produk dan bahan baku.</p>
                </div>

                <a href="#" class="owner-view-all">
                    Lihat Detail
                    <span>→</span>
                </a>
            </div>

            <div class="owner-inventory-content">

                <div class="owner-inventory-overview">

                    {{-- FINISHED PRODUCT --}}
                    <div class="owner-inventory-item">

                        <div class="owner-inventory-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.7"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="m12 3 9 5-9 5-9-5 9-5Z" />
                                <path d="m3 8 9 5 9-5M3 8v9l9 5 9-5V8" />
                            </svg>
                        </div>

                        <div class="owner-inventory-info">
                            <span>Stok Produk Jadi</span>
                            <strong>{{ number_format($finishedStockTotal) }} pcs</strong>
                        </div>

                    </div>


                    {{-- RAW MATERIAL --}}
                    <div class="owner-inventory-item">

                        <div class="owner-inventory-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.7"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 3h6M10 3v7l-5 9a2 2 0 0 0 2 3h10a2 2 0 0 0 2-3l-5-9V3" />
                                <path d="M8 15h8" />
                            </svg>
                        </div>

                        <div class="owner-inventory-info">
                            <span>Stok Bahan Baku</span>
                            <strong>{{ number_format($rawMaterialCount) }} item</strong>
                        </div>

                    </div>

                </div>


                @if ($lowMaterialCount > 0)
                <div class="owner-inventory-alert">
                    <svg width="19" height="19" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="m10.3 3.9-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.7-3.1l-8-14a2 2 0 0 0-3.4 0Z" />
                        <path d="M12 9v4M12 17h.01" />
                    </svg>

                    <span>{{ $lowMaterialCount }} bahan baku mencapai stok minimum</span>

                    <svg width="15" height="15" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m9 18 6-6-6-6" />
                    </svg>
                </div>
                @endif

            </div>

        </div>

    </div>

</div>


@push('scripts')
<script>
    (function() {
        'use strict';

        const NS = 'http://www.w3.org/2000/svg';
        const COLORS = ['#779968', '#F1C34D', '#6B8FB5', '#C97B63', '#9B7FB5'];

        const svg = document.getElementById('ownerSalesChart');
        const gridG = document.getElementById('ownerChartGrid');
        const linesG = document.getElementById('ownerChartLines');
        const labelsG = document.getElementById('ownerChartLabels');
        const legendEl = document.getElementById('ownerChartLegend');
        const defsEl = document.getElementById('ownerChartGradients');
        const container = svg.parentElement;

        const PAD_LEFT = 55,
            PAD_RIGHT = 620,
            PAD_TOP = 25,
            PAD_BOTTOM = 215;

        // Layer hover (garis, titik) + tooltip dibuat sekali
        const hoverG = document.createElementNS(NS, 'g');
        hoverG.setAttribute('pointer-events', 'none');
        svg.appendChild(hoverG);

        const tooltip = document.createElement('div');
        tooltip.className = 'owner-chart-tooltip';
        container.appendChild(tooltip);

        let view = null;

        const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        function fmtShort(n) {
            if (n >= 1000000) return (n / 1000000).toFixed(1).replace('.0', '') + ' jt';
            if (n >= 1000) return (n / 1000).toFixed(0) + ' rb';
            return String(Math.round(n));
        }

        function fmtRp(n) {
            return 'Rp ' + Math.round(n).toLocaleString('id-ID');
        }

        function fmtDate(iso) {
            const d = new Date(iso + 'T00:00:00');
            return d.getDate() + ' ' + MONTHS[d.getMonth()];
        }

        function fmtDateLong(iso) {
            const d = new Date(iso + 'T00:00:00');
            return d.getDate() + ' ' + MONTHS[d.getMonth()] + ' ' + d.getFullYear();
        }

        // Skala Y rapi: 4 interval dengan step 1 / 2 / 2.5 / 5 x 10^k
        function niceScale(maxValue) {
            const rough = Math.max(maxValue, 1) / 4;
            const mag = Math.pow(10, Math.floor(Math.log10(rough)));
            const norm = rough / mag;
            const nice = norm <= 1 ? 1 : norm <= 2 ? 2 : norm <= 2.5 ? 2.5 : norm <= 5 ? 5 : 10;
            const step = nice * mag;
            return {
                step: step,
                max: step * 4
            };
        }

        // Kurva monotone cubic: melewati semua titik, tanpa overshoot
        function smoothPath(pts) {
            const n = pts.length;
            if (n < 2) return '';
            if (n === 2) {
                return 'M ' + pts[0].x + ' ' + pts[0].y + ' L ' + pts[1].x + ' ' + pts[1].y;
            }

            const dx = [],
                m = [],
                t = new Array(n);
            for (let i = 0; i < n - 1; i++) {
                dx[i] = pts[i + 1].x - pts[i].x;
                m[i] = (pts[i + 1].y - pts[i].y) / dx[i];
            }

            t[0] = m[0];
            t[n - 1] = m[n - 2];
            for (let i = 1; i < n - 1; i++) {
                t[i] = (m[i - 1] * m[i] <= 0) ? 0 : (m[i - 1] + m[i]) / 2;
            }

            for (let i = 0; i < n - 1; i++) {
                if (m[i] === 0) {
                    t[i] = 0;
                    t[i + 1] = 0;
                } else {
                    const a = t[i] / m[i],
                        b = t[i + 1] / m[i];
                    const s = a * a + b * b;
                    if (s > 9) {
                        const tau = 3 / Math.sqrt(s);
                        t[i] = tau * a * m[i];
                        t[i + 1] = tau * b * m[i];
                    }
                }
            }

            let d = 'M ' + pts[0].x + ' ' + pts[0].y;
            for (let i = 0; i < n - 1; i++) {
                const h = dx[i] / 3;
                d += ' C ' + (pts[i].x + h) + ' ' + (pts[i].y + t[i] * h) +
                    ', ' + (pts[i + 1].x - h) + ' ' + (pts[i + 1].y - t[i + 1] * h) +
                    ', ' + pts[i + 1].x + ' ' + pts[i + 1].y;
            }
            return d;
        }

        function render(data) {

            const series = data.series || [];
            const allValues = series.flatMap(function(s) {
                return s.values;
            });
            const scale = niceScale(Math.max(10000, ...allValues));
            const maxVal = scale.max;

            const dates = series.length ? series[0].dates : [];
            const n = dates.length;

            function xOf(i) {
                return n <= 1 ? PAD_LEFT : PAD_LEFT + (i / (n - 1)) * (PAD_RIGHT - PAD_LEFT);
            }

            function yOf(v) {
                return PAD_BOTTOM - (v / maxVal) * (PAD_BOTTOM - PAD_TOP);
            }

            view = {
                series: series,
                dates: dates,
                n: n,
                xOf: xOf,
                yOf: yOf
            };
            hideHover();

            // Grid + label Y
            let gridHtml = '';
            let labelsHtml = '';
            for (let i = 0; i <= 4; i++) {
                const y = PAD_TOP + (i * (PAD_BOTTOM - PAD_TOP) / 4);
                const val = maxVal - (i * scale.step);
                gridHtml += '<line class="owner-chart-gridline" x1="' + PAD_LEFT + '" y1="' + y + '" x2="' + PAD_RIGHT + '" y2="' + y + '"/>';
                labelsHtml += '<text class="owner-chart-label" x="5" y="' + (y + 4) + '">' + fmtShort(val) + '</text>';
            }
            gridG.innerHTML = gridHtml;

            // Garis + area (tanpa titik permanen)
            let linesHtml = '';
            let gradsHtml = '';

            series.forEach(function(s, idx) {
                const color = COLORS[idx % COLORS.length];
                const gradId = 'ownerGrad' + idx;

                const points = s.values.map(function(v, i) {
                    return {
                        x: xOf(i),
                        y: yOf(v)
                    };
                });

                const linePath = smoothPath(points);
                if (!linePath) return;
                const areaPath = linePath + ' L ' + xOf(n - 1) + ' ' + PAD_BOTTOM + ' L ' + xOf(0) + ' ' + PAD_BOTTOM + ' Z';

                gradsHtml += '<linearGradient id="' + gradId + '" x1="0" y1="0" x2="0" y2="1">' +
                    '<stop offset="0%" stop-color="' + color + '" stop-opacity=".18"/>' +
                    '<stop offset="100%" stop-color="' + color + '" stop-opacity="0"/></linearGradient>';

                linesHtml += '<path d="' + areaPath + '" fill="url(#' + gradId + ')"/>';
                linesHtml += '<path d="' + linePath + '" fill="none" stroke="' + color + '" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>';
            });

            defsEl.innerHTML = gradsHtml;
            linesG.innerHTML = linesHtml;

            // Label X: 4 titik merata, tanpa duplikat
            const tickIdx = Array.from(new Set([0, Math.floor((n - 1) / 3), Math.floor(2 * (n - 1) / 3), n - 1]));
            tickIdx.forEach(function(i, k) {
                if (i < 0 || i >= n) return;
                const anchor = k === 0 ? 'start' : (k === tickIdx.length - 1 ? 'end' : 'middle');
                labelsHtml += '<text class="owner-chart-label" x="' + xOf(i) + '" y="237" text-anchor="' + anchor + '">' + fmtDate(dates[i]) + '</text>';
            });
            labelsG.innerHTML = labelsHtml;

            // Legend
            legendEl.innerHTML = series.map(function(s, idx) {
                return '<div class="owner-legend-item"><span class="owner-legend-dot" style="background:' + COLORS[idx % COLORS.length] + '"></span>' + s.platform + '</div>';
            }).join('');
        }

        // ---------- HOVER ----------
        function hideHover() {
            hoverG.innerHTML = '';
            tooltip.classList.remove('show');
        }

        function showHover(i) {
            const x = view.xOf(i);
            let html = '<line x1="' + x + '" y1="' + PAD_TOP + '" x2="' + x + '" y2="' + PAD_BOTTOM +
                '" stroke="#C9CCC3" stroke-width="1" stroke-dasharray="3 3"/>';

            let minY = PAD_BOTTOM;
            let rows = '';

            view.series.forEach(function(s, idx) {
                const color = COLORS[idx % COLORS.length];
                const y = view.yOf(s.values[i]);
                minY = Math.min(minY, y);
                html += '<circle cx="' + x + '" cy="' + y + '" r="5" fill="' + color + '" stroke="#fff" stroke-width="2"/>';
                rows += '<div class="tt-row"><i class="tt-dot" style="background:' + color + '"></i>' +
                    s.platform + '<strong>' + fmtRp(s.values[i]) + '</strong></div>';
            });
            hoverG.innerHTML = html;

            tooltip.innerHTML = '<div class="tt-date">' + fmtDateLong(view.dates[i]) + '</div>' + rows;
            tooltip.classList.add('show');

            // Posisi tooltip (konversi koordinat SVG ke piksel)
            const ctm = svg.getScreenCTM();
            const pt = svg.createSVGPoint();
            pt.x = x;
            pt.y = minY;
            const s = pt.matrixTransform(ctm);
            const cRect = container.getBoundingClientRect();

            const w = tooltip.offsetWidth;
            const h = tooltip.offsetHeight;
            let left = s.x - cRect.left;
            left = Math.max(w / 2 + 4, Math.min(cRect.width - w / 2 - 4, left));
            const top = Math.max(4, (s.y - cRect.top) - h - 14);

            tooltip.style.left = left + 'px';
            tooltip.style.top = top + 'px';
        }

        svg.addEventListener('pointermove', function(e) {
            if (!view || !view.n) return;
            const ctm = svg.getScreenCTM();
            if (!ctm) return;

            const pt = svg.createSVGPoint();
            pt.x = e.clientX;
            pt.y = e.clientY;
            const p = pt.matrixTransform(ctm.inverse());

            if (p.x < PAD_LEFT - 15 || p.x > PAD_RIGHT + 15 || p.y < PAD_TOP - 15 || p.y > PAD_BOTTOM + 15) {
                hideHover();
                return;
            }

            const ratio = (p.x - PAD_LEFT) / (PAD_RIGHT - PAD_LEFT);
            const i = view.n <= 1 ? 0 : Math.max(0, Math.min(view.n - 1, Math.round(ratio * (view.n - 1))));
            showHover(i);
        });

        svg.addEventListener('pointerleave', hideHover);

        // ---------- LOAD DATA ----------
        async function load(start, end) {
            try {
                const res = await fetch('{{ route("owner.dashboard.data") }}?start=' + start + '&end=' + end);
                if (!res.ok) return;
                const data = await res.json();
                render(data);
            } catch (e) {
                // diam saja, chart tetap kosong kalau gagal
            }
        }


        load(svg.dataset.start, svg.dataset.end);
        // ---------- DATE FILTER POPOVER ----------
        const dateBtn = document.getElementById('ownerDateFilterBtn');
        const datePopover = document.getElementById('ownerDateFilterPopover');

        if (dateBtn && datePopover) {

            dateBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                datePopover.hidden = !datePopover.hidden;
            });

            document.addEventListener('click', function(e) {
                if (!datePopover.hidden && !datePopover.contains(e.target) && e.target !== dateBtn) {
                    datePopover.hidden = true;
                }
            });

            datePopover.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    })();
</script>
@endpush
@endsection