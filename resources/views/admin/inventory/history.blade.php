@extends('layouts.admin')

@section('title', 'Riwayat Stok')

@section('content')

<div class="stock-history-page">

    {{-- =========================
        BACK
    ========================== --}}
    <a
        href="/admin/inventory"
        class="stock-history-back"
    >
        <svg
            width="16"
            height="16"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
        >
            <path d="m15 18-6-6 6-6"/>
        </svg>

        Kembali ke Inventory
    </a>


    {{-- =========================
        HEADER
    ========================== --}}
    <div class="stock-history-header">

        <div>
            <div class="stock-history-breadcrumb">
                INVENTORY / RIWAYAT STOK
            </div>

            <h1>Sign Peringatan Hitam (9x30)</h1>

            <div class="stock-history-variant">
                <span>CCTV</span>
                <span class="separator">•</span>
                <span>SKU Variant: {{ $sku }}</span>
                <span class="separator">•</span>
                <span>Satuan: pcs</span>
            </div>
        </div>

        <span class="stock-history-type">
            Produk Jadi
        </span>

    </div>


    {{-- =========================
        SUMMARY
    ========================== --}}
    <div class="stock-history-summary">

        <div class="stock-history-summary-card primary">

            <span class="history-summary-label">
                Stok Saat Ini
            </span>

            <strong class="history-summary-value">
                50
            </strong>

            <span class="history-summary-unit">
                pcs
            </span>

        </div>


        <div class="stock-history-summary-card">

            <span class="history-summary-label">
                Total Masuk
            </span>

            <strong class="history-summary-value incoming">
                +30
            </strong>

            <span class="history-summary-caption">
                Periode filter
            </span>

        </div>


        <div class="stock-history-summary-card">

            <span class="history-summary-label">
                Total Keluar
            </span>

            <strong class="history-summary-value outgoing">
                −10
            </strong>

            <span class="history-summary-caption">
                Periode filter
            </span>

        </div>

    </div>


    {{-- =========================
        HISTORY CARD
    ========================== --}}
    <div class="stock-history-card">

        {{-- FILTER --}}
        <div class="stock-history-filter-section">

            <div class="stock-history-section-heading">
                <h2>Filter Transaksi</h2>

                <p>
                    Filter riwayat berdasarkan jenis pergerakan dan periode.
                </p>
            </div>


            <div class="stock-history-filters">

                <div class="stock-history-filter-group">

                    <label for="movementFilter">
                        Jenis Pergerakan
                    </label>

                    <select id="movementFilter">
                        <option value="all">
                            Semua Transaksi
                        </option>

                        <option value="in">
                            Stok Masuk
                        </option>

                        <option value="out">
                            Stok Keluar
                        </option>
                    </select>

                </div>


                <div class="stock-history-filter-group">

                    <label for="periodFilter">
                        Periode
                    </label>

                    <select id="periodFilter">
                        <option value="all">
                            Semua Periode
                        </option>

                        <option value="today">
                            Hari Ini
                        </option>

                        <option value="week">
                            7 Hari Terakhir
                        </option>

                        <option value="month" selected>
                            Bulan Ini
                        </option>
                    </select>

                </div>

            </div>

        </div>


        {{-- TABLE SECTION --}}
        <div class="stock-history-table-section">

            <div class="stock-history-section-heading">
                <h2>Riwayat Pergerakan</h2>

                <p>
                    Setiap perubahan stok memiliki catatan transaksi.
                </p>
            </div>


            <div class="stock-history-table-wrapper">

                <table class="stock-history-table">

                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Aktivitas</th>
                            <th>Sumber</th>
                            <th>Perubahan</th>
                            <th>Saldo</th>
                        </tr>
                    </thead>

                    <tbody id="stockHistoryTableBody">

                        <tr data-movement="out" data-period="today">
                            <td>
                                <div class="history-date">
                                    <strong>13 Okt 2026</strong>
                                    <span>10:24</span>
                                </div>
                            </td>

                            <td>
                                <div class="history-activity">
                                    <strong>Pesanan siap dikemas</strong>
                                    <span>Order #RB-1001</span>
                                </div>
                            </td>

                            <td>
                                <span class="history-source">
                                    Order
                                </span>
                            </td>

                            <td>
                                <span class="history-change outgoing">
                                    −3 pcs
                                </span>
                            </td>

                            <td class="history-balance">
                                50 pcs
                            </td>
                        </tr>


                        <tr data-movement="in" data-period="week">
                            <td>
                                <div class="history-date">
                                    <strong>12 Okt 2026</strong>
                                    <span>15:10</span>
                                </div>
                            </td>

                            <td>
                                <div class="history-activity">
                                    <strong>Produksi selesai</strong>
                                    <span>Produksi D6A-1</span>
                                </div>
                            </td>

                            <td>
                                <span class="history-source production">
                                    Produksi
                                </span>
                            </td>

                            <td>
                                <span class="history-change incoming">
                                    +10 pcs
                                </span>
                            </td>

                            <td class="history-balance">
                                53 pcs
                            </td>
                        </tr>


                        <tr data-movement="out" data-period="week">
                            <td>
                                <div class="history-date">
                                    <strong>11 Okt 2026</strong>
                                    <span>13:42</span>
                                </div>
                            </td>

                            <td>
                                <div class="history-activity">
                                    <strong>Pesanan siap dikemas</strong>
                                    <span>Order #RB-0997</span>
                                </div>
                            </td>

                            <td>
                                <span class="history-source">
                                    Order
                                </span>
                            </td>

                            <td>
                                <span class="history-change outgoing">
                                    −5 pcs
                                </span>
                            </td>

                            <td class="history-balance">
                                43 pcs
                            </td>
                        </tr>


                        <tr data-movement="in" data-period="week">
                            <td>
                                <div class="history-date">
                                    <strong>10 Okt 2026</strong>
                                    <span>09:15</span>
                                </div>
                            </td>

                            <td>
                                <div class="history-activity">
                                    <strong>Produksi selesai</strong>
                                    <span>Produksi D6A-1</span>
                                </div>
                            </td>

                            <td>
                                <span class="history-source production">
                                    Produksi
                                </span>
                            </td>

                            <td>
                                <span class="history-change incoming">
                                    +20 pcs
                                </span>
                            </td>

                            <td class="history-balance">
                                48 pcs
                            </td>
                        </tr>


                        <tr data-movement="in" data-period="month">
                            <td>
                                <div class="history-date">
                                    <strong>1 Okt 2026</strong>
                                    <span>08:00</span>
                                </div>
                            </td>

                            <td>
                                <div class="history-activity">
                                    <strong>Stok awal</strong>
                                    <span>Saldo awal inventory</span>
                                </div>
                            </td>

                            <td>
                                <span class="history-source adjustment">
                                    Adjustment
                                </span>
                            </td>

                            <td>
                                <span class="history-change initial">
                                    +28 pcs
                                </span>
                            </td>

                            <td class="history-balance">
                                28 pcs
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>


            {{-- EMPTY STATE --}}
            <div
                class="stock-history-empty"
                id="stockHistoryEmpty"
            >
                <svg
                    width="30"
                    height="30"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                >
                    <path d="M3 12a9 9 0 1 0 3-6.7"/>
                    <path d="M3 4v5h5"/>
                    <path d="M12 7v5l3 2"/>
                </svg>

                <strong>Tidak ada riwayat</strong>

                <span>
                    Tidak ada transaksi yang sesuai dengan filter.
                </span>
            </div>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const movementFilter =
        document.getElementById('movementFilter');

    const periodFilter =
        document.getElementById('periodFilter');

    const tableBody =
        document.getElementById('stockHistoryTableBody');

    const emptyState =
        document.getElementById('stockHistoryEmpty');


    function filterHistory() {

        const movement =
            movementFilter.value;

        const period =
            periodFilter.value;

        const rows =
            tableBody.querySelectorAll('tr');

        let visibleRows = 0;


        rows.forEach(function (row) {

            const rowMovement =
                row.dataset.movement;

            const rowPeriod =
                row.dataset.period;


            const movementMatch =
                movement === 'all' ||
                rowMovement === movement;


            let periodMatch = false;


            if (period === 'all') {

                periodMatch = true;

            } else if (period === 'month') {

                periodMatch = true;

            } else if (period === 'week') {

                periodMatch =
                    rowPeriod === 'today' ||
                    rowPeriod === 'week';

            } else if (period === 'today') {

                periodMatch =
                    rowPeriod === 'today';

            }


            const show =
                movementMatch &&
                periodMatch;


            row.style.display =
                show ? '' : 'none';


            if (show) {
                visibleRows++;
            }

        });


        emptyState.style.display =
            visibleRows === 0
                ? 'flex'
                : 'none';

    }


    movementFilter.addEventListener(
        'change',
        filterHistory
    );


    periodFilter.addEventListener(
        'change',
        filterHistory
    );


    filterHistory();

});
</script>

@endpush