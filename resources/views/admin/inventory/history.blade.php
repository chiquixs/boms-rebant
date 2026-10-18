@extends('layouts.admin')

@section('title', 'Riwayat Stok')

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
            <div class="stock-history-breadcrumb">INVENTORY / RIWAYAT STOK</div>

            <h1>{{ $product->group->name ?? '—' }}</h1>

            <div class="stock-history-variant">
                <span>{{ $product->variant_name ?? '—' }}</span>
                <span class="separator">•</span>
                <span>SKU Variant: {{ $product->sku }}</span>
                <span class="separator">•</span>
                <span>Satuan: pcs</span>
            </div>
        </div>

        <span class="stock-history-type">Produk Jadi</span>
    </div>

    <div class="stock-history-summary">
        <div class="stock-history-summary-card primary">
            <span class="history-summary-label">Stok Saat Ini</span>
            <strong class="history-summary-value">{{ $product->current_stock }}</strong>
            <span class="history-summary-unit">pcs</span>
        </div>

        <div class="stock-history-summary-card">
            <span class="history-summary-label">Total Masuk</span>
            <strong class="history-summary-value incoming">+{{ $totalIn }}</strong>
            <span class="history-summary-caption">semua riwayat</span>
        </div>

        <div class="stock-history-summary-card">
            <span class="history-summary-label">Total Keluar</span>
            <strong class="history-summary-value outgoing">−{{ $totalOut }}</strong>
            <span class="history-summary-caption">semua riwayat</span>
        </div>
    </div>

    <div class="stock-history-card">

        <div class="stock-history-filter-section">
            <div class="stock-history-section-heading">
                <h2>Filter Transaksi</h2>
                <p>Filter riwayat berdasarkan jenis pergerakan dan periode.</p>
            </div>

            <div class="stock-history-filters">
                <div class="stock-history-filter-group">
                    <label for="movementFilter">Jenis Pergerakan</label>
                    <select id="movementFilter">
                        <option value="all">Semua Transaksi</option>
                        <option value="in">Stok Masuk</option>
                        <option value="out">Stok Keluar</option>
                    </select>
                </div>

                <div class="stock-history-filter-group">
                    <label for="periodFilter">Periode</label>
                    <select id="periodFilter">
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
                <h2>Riwayat Pergerakan</h2>
                <p>Setiap perubahan stok memiliki catatan transaksi.</p>
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
                        @forelse ($movements as $m)
                        @php
                        $delta = $m->delta;
                        $period = $m->created_at->isToday()
                        ? 'today'
                        : ($m->created_at->greaterThanOrEqualTo(now()->subDays(7)) ? 'week' : 'month');

                        $activityLabel = match (true) {
                        $m->movement_type === 'IN' && $m->production_log_id => 'Produksi selesai',
                        $m->movement_type === 'IN' => 'Stok Masuk',
                        $m->movement_type === 'OUT' && $m->order_item_id => 'Pesanan siap dikemas',
                        $m->movement_type === 'OUT' => 'Stok Keluar',
                        default => 'Penyesuaian Stok',
                        };

                        $sourceLabel = match (true) {
                        (bool) $m->orderItem => 'Order',
                        (bool) $m->production_log_id => 'Produksi',
                        default => 'Adjustment',
                        };

                        $sourceClass = match (true) {
                        (bool) $m->orderItem => '',
                        (bool) $m->production_log_id => 'production',
                        default => 'adjustment',
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
                                    <span>
                                        @if ($m->orderItem)
                                        Order #{{ $m->orderItem->order->platform_order_no ?? $m->orderItem->order_id }}
                                        @elseif ($m->production_log_id)
                                        Produksi #{{ $m->production_log_id }}
                                        @else
                                        {{ $m->note ?? '—' }}
                                        @endif
                                    </span>
                                </div>
                            </td>

                            <td><span class="history-source {{ $sourceClass }}">{{ $sourceLabel }}</span></td>

                            <td>
                                <span class="history-change {{ $delta >= 0 ? 'incoming' : 'outgoing' }}">
                                    {{ $delta >= 0 ? '+' : '−' }}{{ abs($delta) }} pcs
                                </span>
                            </td>

                            <td class="history-balance">{{ $m->qty_after }} pcs</td>
                        </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div
                class="stock-history-empty {{ $movements->isEmpty() ? '' : 'is-hidden' }}"
                id="stockHistoryEmpty">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path d="M3 12a9 9 0 1 0 3-6.7" />
                    <path d="M3 4v5h5" />
                    <path d="M12 7v5l3 2" />
                </svg>
                <strong>Tidak ada riwayat</strong>
                <span>Belum ada transaksi untuk produk ini.</span>
            </div>
        </div>
    </div>
</div>

@endsection


@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function() {

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


            rows.forEach(function(row) {

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