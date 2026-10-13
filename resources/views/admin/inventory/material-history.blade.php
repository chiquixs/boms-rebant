@extends('layouts.admin')

@section('title', 'Riwayat Stok Bahan Baku')

@section('content')

@php
    $materialName = urldecode($material);

    if ($materialName === 'Hanger') {
        $materialData = [
            'name' => 'Hanger',
            'type' => 'NON-SHEET',
            'unit' => 'pcs',
            'size' => null,
            'current' => 150,
            'incoming' => 100,
            'outgoing' => 45,
        ];
    } elseif ($materialName === 'Cat Hitam') {
        $materialData = [
            'name' => 'Cat Hitam',
            'type' => 'NON-SHEET',
            'unit' => 'lt',
            'size' => null,
            'current' => 2,
            'incoming' => 10,
            'outgoing' => 8,
        ];
    } else {
        $materialData = [
            'name' => 'MDF',
            'type' => 'SHEET',
            'unit' => 'lembar',
            'size' => '120 × 240 cm',
            'current' => 20,
            'incoming' => 15,
            'outgoing' => 8,
        ];
    }
@endphp


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
                INVENTORY / BAHAN BAKU / RIWAYAT STOK
            </div>

            <h1>
                {{ $materialData['name'] }}
            </h1>

            <div class="stock-history-variant">

                <span>
                    {{ $materialData['type'] }}
                </span>

                <span class="separator">•</span>

                <span>
                    Satuan: {{ $materialData['unit'] }}
                </span>

                @if ($materialData['size'])
                    <span class="separator">•</span>

                    <span>
                        Ukuran: {{ $materialData['size'] }}
                    </span>
                @endif

            </div>
        </div>


        <span class="stock-history-type raw-material">
            Bahan Baku
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
                {{ $materialData['current'] }}
            </strong>

            <span class="history-summary-unit">
                {{ $materialData['unit'] }}
            </span>

        </div>


        <div class="stock-history-summary-card">

            <span class="history-summary-label">
                Total Masuk
            </span>

            <strong class="history-summary-value incoming">
                +{{ $materialData['incoming'] }}
            </strong>

            <span class="history-summary-caption">
                {{ $materialData['unit'] }} • periode filter
            </span>

        </div>


        <div class="stock-history-summary-card">

            <span class="history-summary-label">
                Total Keluar
            </span>

            <strong class="history-summary-value outgoing">
                −{{ $materialData['outgoing'] }}
            </strong>

            <span class="history-summary-caption">
                {{ $materialData['unit'] }} • periode filter
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
                    Filter pergerakan bahan berdasarkan jenis transaksi dan periode.
                </p>

            </div>


            <div class="stock-history-filters">

                <div class="stock-history-filter-group">

                    <label for="materialMovementFilter">
                        Jenis Pergerakan
                    </label>

                    <select id="materialMovementFilter">

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

                    <label for="materialPeriodFilter">
                        Periode
                    </label>

                    <select id="materialPeriodFilter">

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


        {{-- =========================
            HISTORY TABLE
        ========================== --}}
        <div class="stock-history-table-section">

            <div class="stock-history-section-heading">

                <h2>Riwayat Pergerakan Bahan</h2>

                <p>
                    Penerimaan dari supplier dan pemakaian bahan dalam produksi.
                </p>

            </div>


            <div class="stock-history-table-wrapper">

                <table class="stock-history-table material-history-table">

                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Aktivitas</th>
                            <th>Supplier / Tujuan</th>
                            <th>Referensi</th>
                            <th>Perubahan</th>
                            <th>Saldo</th>
                        </tr>
                    </thead>


                    <tbody id="materialHistoryTableBody">

                        {{-- =====================================
                            PROCUREMENT - STOCK IN
                        ====================================== --}}
                        <tr
                            data-movement="in"
                            data-period="today"
                        >

                            <td>
                                <div class="history-date">
                                    <strong>13 Okt 2026</strong>
                                    <span>09:42</span>
                                </div>
                            </td>


                            <td>
                                <div class="history-activity">
                                    <strong>Penerimaan bahan</strong>
                                    <span>Procurement</span>
                                </div>
                            </td>


                            <td>
                                <div class="material-history-party">
                                    <strong>PT Kayu Jaya</strong>
                                    <span>Supplier</span>
                                </div>
                            </td>


                            <td>
                                <span class="material-reference procurement">
                                    PO-0012
                                </span>
                            </td>


                            <td>
                                <span class="history-change incoming">
                                    +10 {{ $materialData['unit'] }}
                                </span>
                            </td>


                            <td class="history-balance">
                                {{ $materialData['current'] }}
                                {{ $materialData['unit'] }}
                            </td>

                        </tr>


                        {{-- =====================================
                            PRODUCTION - STOCK OUT
                        ====================================== --}}
                        <tr
                            data-movement="out"
                            data-period="week"
                        >

                            <td>
                                <div class="history-date">
                                    <strong>12 Okt 2026</strong>
                                    <span>14:18</span>
                                </div>
                            </td>


                            <td>
                                <div class="history-activity">
                                    <strong>Pemakaian produksi</strong>
                                    <span>Material digunakan untuk produksi</span>
                                </div>
                            </td>


                            <td>
                                <div class="material-history-party">
                                    <strong>D6A</strong>
                                    <span>Sign Peringatan Hitam</span>
                                </div>
                            </td>


                            <td>
                                <span class="material-reference production">
                                    PROD-018
                                </span>
                            </td>


                            <td>
                                <span class="history-change outgoing">
                                    −3 {{ $materialData['unit'] }}
                                </span>
                            </td>


                            <td class="history-balance">
                                10 {{ $materialData['unit'] }}
                            </td>

                        </tr>


                        {{-- =====================================
                            PROCUREMENT - STOCK IN
                        ====================================== --}}
                        <tr
                            data-movement="in"
                            data-period="week"
                        >

                            <td>
                                <div class="history-date">
                                    <strong>10 Okt 2026</strong>
                                    <span>11:30</span>
                                </div>
                            </td>


                            <td>
                                <div class="history-activity">
                                    <strong>Penerimaan bahan</strong>
                                    <span>Procurement</span>
                                </div>
                            </td>


                            <td>
                                <div class="material-history-party">
                                    <strong>CV MDF Malang</strong>
                                    <span>Supplier</span>
                                </div>
                            </td>


                            <td>
                                <span class="material-reference procurement">
                                    PO-0009
                                </span>
                            </td>


                            <td>
                                <span class="history-change incoming">
                                    +5 {{ $materialData['unit'] }}
                                </span>
                            </td>


                            <td class="history-balance">
                                13 {{ $materialData['unit'] }}
                            </td>

                        </tr>


                        {{-- =====================================
                            PRODUCTION - STOCK OUT
                        ====================================== --}}
                        <tr
                            data-movement="out"
                            data-period="week"
                        >

                            <td>
                                <div class="history-date">
                                    <strong>9 Okt 2026</strong>
                                    <span>16:05</span>
                                </div>
                            </td>


                            <td>
                                <div class="history-activity">
                                    <strong>Pemakaian produksi</strong>
                                    <span>Material digunakan untuk produksi</span>
                                </div>
                            </td>


                            <td>
                                <div class="material-history-party">
                                    <strong>D6A</strong>
                                    <span>Sign Peringatan Hitam</span>
                                </div>
                            </td>


                            <td>
                                <span class="material-reference production">
                                    PROD-015
                                </span>
                            </td>


                            <td>
                                <span class="history-change outgoing">
                                    −5 {{ $materialData['unit'] }}
                                </span>
                            </td>


                            <td class="history-balance">
                                8 {{ $materialData['unit'] }}
                            </td>

                        </tr>


                        {{-- =====================================
                            ADJUSTMENT
                        ====================================== --}}
                        <tr
                            data-movement="in"
                            data-period="month"
                        >

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
                                <div class="material-history-party">
                                    <strong>Inventory</strong>
                                    <span>Penyesuaian sistem</span>
                                </div>
                            </td>


                            <td>
                                <span class="material-reference adjustment">
                                    ADJ-001
                                </span>
                            </td>


                            <td>
                                <span class="history-change initial">
                                    +8 {{ $materialData['unit'] }}
                                </span>
                            </td>


                            <td class="history-balance">
                                8 {{ $materialData['unit'] }}
                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>


            {{-- =========================
                EMPTY STATE
            ========================== --}}
            <div
                class="stock-history-empty"
                id="materialHistoryEmpty"
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
                    Tidak ada transaksi bahan yang sesuai dengan filter.
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
        document.getElementById('materialMovementFilter');

    const periodFilter =
        document.getElementById('materialPeriodFilter');

    const tableBody =
        document.getElementById('materialHistoryTableBody');

    const emptyState =
        document.getElementById('materialHistoryEmpty');


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
                movement === rowMovement;


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