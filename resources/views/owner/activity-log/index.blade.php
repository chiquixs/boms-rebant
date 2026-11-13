
@extends('layouts.owner')

@section('title', 'Log Aktivitas - REBANT BOMS')

@push('styles')
    @vite('resources/css/owner-activity-log.css')
@endpush

@section('content')
<div class="activity-page">

    <div class="activity-heading">
        <div>
            <p class="activity-eyebrow">BUSINESS MONITORING</p>
            <h1>Log Aktivitas</h1>
            <p class="activity-description">
                Pantau riwayat aktivitas order, produksi, dan inventory bisnis.
            </p>
        </div>
    </div>

    <!-- SUMMARY -->
    <section class="activity-summary">
        <article class="activity-summary-card">
            <div class="activity-summary-icon total-icon">▤</div>
            <div>
                <p>Total Aktivitas</p>
                <h2 id="totalActivities">0</h2>
            </div>
        </article>

        <article class="activity-summary-card">
            <div class="activity-summary-icon order-icon">▣</div>
            <div>
                <p>Order</p>
                <h2 id="totalOrders">0</h2>
            </div>
        </article>

        <article class="activity-summary-card">
            <div class="activity-summary-icon production-icon">⚙</div>
            <div>
                <p>Produksi</p>
                <h2 id="totalProduction">0</h2>
            </div>
        </article>

        <article class="activity-summary-card">
            <div class="activity-summary-icon inventory-icon">▦</div>
            <div>
                <p>Inventory</p>
                <h2 id="totalInventory">0</h2>
            </div>
        </article>
    </section>

    <!-- FILTER -->
    <section class="activity-filter-panel">
        <div class="activity-filter-heading">
            <div>
                <h3>Riwayat Aktivitas</h3>
                <p>Temukan aktivitas berdasarkan periode dan kategori.</p>
            </div>
            <button type="button" id="resetFilter" class="reset-filter-button">
                Reset Filter
            </button>
        </div>

        <div class="activity-filter-grid">
            <div class="activity-filter-field">
                <label for="activitySearch">Cari aktivitas</label>
                <input
                    type="search"
                    id="activitySearch"
                    placeholder="Cari ID, aktivitas, pengguna..."
                >
            </div>

            <div class="activity-filter-field">
                <label for="activityCategory">Kategori</label>
                <select id="activityCategory">
                    <option value="all">Semua Kategori</option>
                    <option value="Order">Order</option>
                    <option value="Produksi">Produksi</option>
                    <option value="Inventory">Inventory</option>
                </select>
            </div>

            <div class="activity-filter-field">
                <label for="activityStartDate">Dari tanggal</label>
                <input type="date" id="activityStartDate" value="2026-11-01">
            </div>

            <div class="activity-filter-field">
                <label for="activityEndDate">Sampai tanggal</label>
                <input type="date" id="activityEndDate" value="2026-11-13">
            </div>
        </div>
    </section>

    <!-- TABLE -->
    <section class="activity-table-panel">
        <div class="activity-table-header">
            <div>
                <h3>Daftar Aktivitas</h3>
                <p>Catatan perubahan dan aktivitas operasional.</p>
            </div>

            <div class="activity-export-actions">
                <button type="button" id="exportActivityExcel" class="activity-export-button">
                    ↓ Excel
                </button>
                <button type="button" id="exportActivityCsv" class="activity-export-button">
                    ↓ CSV
                </button>
            </div>
        </div>

        <div class="activity-table-responsive">
            <table class="activity-table">
                <thead>
                    <tr>
                        <th>ID Aktivitas</th>
                        <th>Waktu</th>
                        <th>Kategori</th>
                        <th>Aktivitas</th>
                        <th>Pengguna</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody id="activityTableBody">
                    <tr>
                        <td colspan="6" class="activity-empty">
                            Memuat data aktivitas...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="activity-table-footer">
            <span id="activityCount">Menampilkan 0 aktivitas</span>
            <div id="activityPagination" class="activity-pagination"></div>
        </div>
    </section>

    <div class="activity-note">
        <span>ⓘ</span>
        Data aktivitas pada halaman ini masih menggunakan dummy data untuk pengembangan frontend.
    </div>
</div>

<!-- DETAIL MODAL -->
<div class="activity-modal" id="activityModal" aria-hidden="true">
    <div class="activity-modal-backdrop" id="closeActivityBackdrop"></div>

    <div class="activity-modal-content" role="dialog" aria-modal="true" aria-labelledby="activityModalTitle">
        <div class="activity-modal-header">
            <div>
                <p>ACTIVITY DETAIL</p>
                <h3 id="activityModalTitle">Detail Aktivitas</h3>
            </div>
            <button type="button" id="closeActivityModal" class="activity-modal-close" aria-label="Tutup">
                &times;
            </button>
        </div>

        <div id="activityModalBody" class="activity-modal-body"></div>
    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/owner-activity-log.js')
@endpush
