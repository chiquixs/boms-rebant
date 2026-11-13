
@extends('layouts.owner')

@section('title', 'Laporan Penjualan - REBANT BOMS')

@push('styles')
    @vite('resources/css/owner-sales-report.css')
@endpush

@section('content')
<div class="sales-report-page">
    <div class="sales-report-heading">
        <div>
            <p class="page-eyebrow">BUSINESS ANALYTICS</p>
            <h1>Laporan Penjualan</h1>
            <p class="page-description">
                Pantau performa penjualan dan analisis bisnis berdasarkan periode.
            </p>
        </div>
    </div>

    <!-- FILTER & EXPORT -->
    <section class="report-toolbar">
        <div class="report-filter">
            <div class="filter-field">
                <label for="startDate">Dari tanggal</label>
                <input type="date" id="startDate" value="2026-11-01">
            </div>

            <div class="filter-field">
                <label for="endDate">Sampai tanggal</label>
                <input type="date" id="endDate" value="2026-11-13">
            </div>

            <button type="button" class="filter-button" id="applyFilter">
                Terapkan
            </button>
        </div>

        <div class="export-actions">
            <button type="button" class="export-button export-pdf" id="exportPdf">
                <span>↓</span> PDF
            </button>

            <button type="button" class="export-button export-excel" id="exportExcel">
                <span>↓</span> Excel
            </button>

            <button type="button" class="export-button export-csv" id="exportCsv">
                <span>↓</span> CSV
            </button>
        </div>
    </section>

    <div class="report-period-label" id="periodLabel">
        Periode 1 November 2026 – 13 November 2026
    </div>

    <!-- SUMMARY CARDS -->
    <section class="report-summary-grid">
        <article class="report-summary-card">
            <div class="summary-card-top">
                <span class="summary-label">Total Omzet</span>
                <span class="summary-icon revenue-icon">Rp</span>
            </div>
            <h2 id="totalRevenue">Rp0</h2>
            <p class="summary-note">Dari transaksi completed</p>
        </article>

        <article class="report-summary-card">
            <div class="summary-card-top">
                <span class="summary-label">Produk Terjual</span>
                <span class="summary-icon product-icon">▦</span>
            </div>
            <h2 id="totalProducts">0</h2>
            <p class="summary-note">Total unit produk terjual</p>
        </article>

        <article class="report-summary-card">
            <div class="summary-card-top">
                <span class="summary-label">Total Transaksi</span>
                <span class="summary-icon transaction-icon">▤</span>
            </div>
            <h2 id="totalTransactions">0</h2>
            <p class="summary-note">Transaksi berstatus completed</p>
        </article>

        <article class="report-summary-card">
            <div class="summary-card-top">
                <span class="summary-label">Rata-rata Transaksi</span>
                <span class="summary-icon average-icon">↗</span>
            </div>
            <h2 id="averageOrderValue">Rp0</h2>
            <p class="summary-note">Average order value</p>
        </article>
    </section>

    <!-- CHARTS -->
    <section class="report-chart-grid">
        <article class="report-panel sales-trend-panel">
            <div class="panel-heading">
                <div>
                    <h3>Tren Penjualan</h3>
                    <p>Omzet berdasarkan tanggal transaksi</p>
                </div>
                <span class="panel-tag">Revenue</span>
            </div>

            <div class="sales-chart" id="salesChart">
                <div class="chart-empty">Memuat data penjualan...</div>
            </div>
        </article>

        <article class="report-panel platform-panel">
            <div class="panel-heading">
                <div>
                    <h3>Performa Platform</h3>
                    <p>Kontribusi omzet setiap platform</p>
                </div>
            </div>

            <div class="platform-chart" id="platformChart">
                <div class="chart-empty">Memuat data platform...</div>
            </div>
        </article>
    </section>

    <!-- PRODUCT PERFORMANCE -->
    <section class="report-panel product-performance-panel">
        <div class="panel-heading">
            <div>
                <h3>Performa Produk</h3>
                <p>Produk berdasarkan jumlah unit terjual</p>
            </div>
            <span class="panel-tag">Best Seller</span>
        </div>

        <div class="table-responsive">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Ranking</th>
                        <th>Nama Produk</th>
                        <th>Unit Terjual</th>
                        <th>Total Omzet</th>
                    </tr>
                </thead>
                <tbody id="productTableBody">
                    <tr>
                        <td colspan="4" class="empty-table">Memuat data produk...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- TRANSACTION HISTORY -->
    <section class="report-panel transaction-panel">
        <div class="panel-heading">
            <div>
                <h3>Riwayat Transaksi</h3>
                <p>Daftar transaksi selesai pada periode yang dipilih</p>
            </div>
            <span class="completed-badge">Completed Only</span>
        </div>

        <div class="table-responsive">
            <table class="report-table transaction-table">
                <thead>
                    <tr>
                        <th>ID Transaksi</th>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Platform</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="transactionTableBody">
                    <tr>
                        <td colspan="7" class="empty-table">Memuat transaksi...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <span id="transactionCount">Menampilkan 0 transaksi</span>

            <div class="pagination-controls" id="paginationControls"></div>

            <span class="table-footer-note">Data berdasarkan transaksi completed</span>
        </div>
    </section>

    <div class="report-disclaimer">
        <span>ⓘ</span>
        Data pada halaman ini masih menggunakan dummy data untuk kebutuhan pengembangan frontend.
    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/owner-sales-report.js')
@endpush
