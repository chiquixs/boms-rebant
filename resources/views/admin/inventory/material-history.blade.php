@extends('layouts.admin')

@section('title', 'Riwayat Stok Bahan Baku')

@section('content')

<div class="stock-history-page">

    <a href="{{ route('admin.inventory.index') }}" class="stock-history-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="m15 18-6-6 6-6" />
        </svg>
        Kembali ke Inventory
    </a>

    <div class="stock-history-header">
        <div>
            <div class="stock-history-breadcrumb">INVENTORY / BAHAN BAKU / RIWAYAT STOK</div>

            <h1>{{ $material->name }}</h1>

            <div class="stock-history-variant">
                <span>{{ $material->isSheet() ? 'SHEET' : 'NON-SHEET' }}</span>
                <span class="separator">•</span>
                <span>Satuan: {{ $material->unit }}</span>

                @if ($material->isSheet())
                <span class="separator">•</span>
                <span>
                    Ukuran: {{ rtrim(rtrim(number_format($material->sheet_length_cm, 2), '0'), '.') }} × {{ rtrim(rtrim(number_format($material->sheet_width_cm, 2), '0'), '.') }} cm
                </span>
                @endif
            </div>
        </div>

        <span class="stock-history-type raw-material">Bahan Baku</span>
    </div>

    <div class="stock-history-summary">
        <div class="stock-history-summary-card primary">
            <span class="history-summary-label">Stok Saat Ini</span>
            <strong class="history-summary-value">{{ rtrim(rtrim(number_format($material->current_stock, 3), '0'), '.') }}</strong>
            <span class="history-summary-unit">{{ $material->unit }}</span>
        </div>

        <div class="stock-history-summary-card">
            <span class="history-summary-label">Total Masuk</span>
            <strong class="history-summary-value incoming">+{{ rtrim(rtrim(number_format($totalIn, 3), '0'), '.') }}</strong>
            <span class="history-summary-caption">{{ $material->unit }} • semua riwayat</span>
        </div>

        <div class="stock-history-summary-card">
            <span class="history-summary-label">Total Keluar</span>
            <strong class="history-summary-value outgoing">−{{ rtrim(rtrim(number_format($totalOut, 3), '0'), '.') }}</strong>
            <span class="history-summary-caption">{{ $material->unit }} • semua riwayat</span>
        </div>
    </div>

    <div class="stock-history-card">

        <div class="stock-history-filter-section">
            <div class="stock-history-section-heading">
                <h2>Filter Transaksi</h2>
                <p>Filter pergerakan bahan berdasarkan jenis transaksi dan periode.</p>
            </div>

            <div class="stock-history-filters">
                <div class="stock-history-filter-group">
                    <label for="materialMovementFilter">Jenis Pergerakan</label>
                    <select id="materialMovementFilter">
                        <option value="all">Semua Transaksi</option>
                        <option value="in">Stok Masuk</option>
                        <option value="out">Stok Keluar</option>
                    </select>
                </div>

                <div class="stock-history-filter-group">
                    <label for="materialPeriodFilter">Periode</label>
                    <select id="materialPeriodFilter">
                        <option value="all" selected>Semua Periode</option>
                        <option value="today">Hari Ini</option>
                        <option value="week">7 Hari Terakhir</option>
                        <option value="month">Bulan Ini</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="stock-history-table-section">
            <div class="stock-history-section-heading">
                <h2>Riwayat Pergerakan Bahan</h2>
                <p>Penerimaan dari supplier dan pemakaian bahan dalam produksi.</p>
            </div>

            <div class="stock-history-table-wrapper">
                <table class="stock-history-table material-history-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Aktivitas</th>
                            <th>Dicatat Oleh</th>
                            <th>Catatan</th>
                            <th>Perubahan</th>
                            <th>Saldo</th>
                        </tr>
                    </thead>

                    <tbody id="materialHistoryTableBody">
                        @forelse ($movements as $m)
                        @php
                        $delta = $m->delta;
                        $period = $m->created_at->isToday()
                        ? 'today'
                        : ($m->created_at->greaterThanOrEqualTo(now()->subDays(7)) ? 'week' : 'month');

                        $activityLabel = match ($m->movement_type) {
                        'IN' => 'Stok Masuk',
                        'OUT' => 'Stok Keluar',
                        default => 'Penyesuaian Stok',
                        };
                        @endphp

                        <tr data-movement="{{ $delta >= 0 ? 'in' : 'out' }}" data-period="{{ $period }}">
                            <td>
                                <div class="history-date">
                                    <strong>{{ $m->created_at->translatedFormat('d M Y') }}</strong>
                                    <span>{{ $m->created_at->format('H:i') }}</span>
                                </div>
                            </td>

                            <td>
                                <div class="history-activity">
                                    <strong>{{ $activityLabel }}</strong>
                                    <span>{{ ucfirst(strtolower($m->movement_type)) }}</span>
                                </div>
                            </td>

                            <td>{{ $m->creator->name ?? '—' }}</td>

                            <td>{{ $m->note ?? '—' }}</td>

                            <td>
                                <span class="history-change {{ $delta >= 0 ? 'incoming' : 'outgoing' }}">
                                    {{ $delta >= 0 ? '+' : '−' }}{{ rtrim(rtrim(number_format(abs($delta), 3), '0'), '.') }} {{ $material->unit }}
                                </span>
                            </td>

                            <td class="history-balance">
                                {{ rtrim(rtrim(number_format($m->qty_after, 3), '0'), '.') }} {{ $material->unit }}
                            </td>
                        </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div
                class="stock-history-empty {{ $movements->isEmpty() ? '' : 'is-hidden' }}"
                id="materialHistoryEmpty">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path d="M3 12a9 9 0 1 0 3-6.7" />
                    <path d="M3 4v5h5" />
                    <path d="M12 7v5l3 2" />
                </svg>
                <strong>Tidak ada riwayat</strong>
                <span>Belum ada transaksi untuk bahan ini.</span>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function() {

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


            rows.forEach(function(row) {

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
                visibleRows === 0 ?
                'flex' :
                'none';

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