@extends('layouts.admin')

@section('title', 'Detail Purchase Order')

@section('content')

@php
    $fmt = fn ($v) => rtrim(rtrim(number_format((float) $v, 3, '.', ''), '0'), '.');
    $subtotal = $purchaseOrder->items->sum(fn ($i) => $i->subtotal);
@endphp

<div class="procurement-detail-page" id="procurementDetailPage" data-status="{{ strtolower($purchaseOrder->status) }}">

    <a href="{{ route('admin.procurement.index') }}" class="procurement-detail-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="m15 18-6-6 6-6" />
        </svg>
        Kembali ke Procurement
    </a>

    <div class="procurement-detail-header">
        <div>
            <div class="procurement-detail-breadcrumb">PROCUREMENT / PURCHASE ORDER</div>

            <div class="procurement-detail-title">
                <h1>{{ $purchaseOrder->po_no }}</h1>
                <span class="procurement-detail-status {{ $purchaseOrder->status_class }}">
                    {{ $purchaseOrder->status_label }}
                </span>
            </div>

            <p>Dibuat {{ $purchaseOrder->created_at->translatedFormat('d M Y') }} • {{ $purchaseOrder->created_at->format('H:i') }}</p>
        </div>

        @if ($purchaseOrder->status !== \App\Models\PurchaseOrder::STATUS_RECEIVED)
        <button type="button" class="procurement-receive-button" id="openReceiveModal">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M21 8l-9 5-9-5" />
                <path d="M3 8l9-5 9 5v8l-9 5-9-5V8Z" />
                <path d="M12 13v8" />
            </svg>
            Tandai Barang Diterima
        </button>
        @endif
    </div>

    <div class="procurement-detail-progress">
        <div class="procurement-progress-item completed">
            <div class="procurement-progress-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m5 12 4 4L19 6" />
                </svg>
            </div>
            <div><strong>PO Dibuat</strong><span>{{ $purchaseOrder->created_at->translatedFormat('d M Y') }}</span></div>
        </div>

        <div class="procurement-progress-line completed"></div>

        <div class="procurement-progress-item completed">
            <div class="procurement-progress-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m5 12 4 4L19 6" />
                </svg>
            </div>
            <div>
                <strong>Email Terkirim</strong>
                <span>{{ $purchaseOrder->email_sent_at?->translatedFormat('d M Y • H:i') ?? '-' }}</span>
            </div>
        </div>

        <div class="procurement-progress-line {{ $purchaseOrder->status === \App\Models\PurchaseOrder::STATUS_RECEIVED ? 'completed' : '' }}"></div>

        <div class="procurement-progress-item {{ $purchaseOrder->status === \App\Models\PurchaseOrder::STATUS_RECEIVED ? 'completed' : 'pending' }}">
            <div class="procurement-progress-icon">
                @if ($purchaseOrder->status === \App\Models\PurchaseOrder::STATUS_RECEIVED)
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m5 12 4 4L19 6" />
                </svg>
                @else
                <span></span>
                @endif
            </div>
            <div>
                <strong>Barang Diterima</strong>
                <span>
                    @if ($purchaseOrder->status === \App\Models\PurchaseOrder::STATUS_RECEIVED)
                    {{ $purchaseOrder->updated_at->translatedFormat('d M Y • H:i') }}
                    @elseif ($purchaseOrder->status === \App\Models\PurchaseOrder::STATUS_PARTIALLY_RECEIVED)
                    Sebagian diterima
                    @else
                    Menunggu penerimaan
                    @endif
                </span>
            </div>
        </div>
    </div>

    <div class="procurement-detail-grid">

        <div class="procurement-detail-main">

            <div class="procurement-detail-card">
                <div class="procurement-detail-card-header">
                    <div>
                        <h2>Detail Pesanan</h2>
                        <p>Bahan baku yang dipesan dalam purchase order ini.</p>
                    </div>
                </div>

                <div class="procurement-detail-table-wrapper">
                    <table class="procurement-detail-table">
                        <thead>
                            <tr>
                                <th>Bahan Baku</th>
                                <th>Tipe</th>
                                <th>Harga Satuan</th>
                                <th>Qty Dipesan</th>
                                <th>Qty Diterima</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchaseOrder->items as $item)
                            <tr>
                                <td>
                                    <div class="procurement-detail-material">
                                        <strong>{{ $item->rawMaterial->name ?? '—' }}</strong>
                                        <span>Satuan: {{ $item->rawMaterial->unit ?? '—' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="procurement-detail-material-type {{ ($item->rawMaterial->material_type ?? '') === 'SHEET' ? 'sheet' : 'non-sheet' }}">
                                        {{ $item->rawMaterial->material_type ?? '—' }}
                                    </span>
                                </td>
                                <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }} / {{ $item->rawMaterial->unit ?? '' }}</td>
                                <td><strong>{{ $fmt($item->qty_ordered) }} {{ $item->rawMaterial->unit ?? '' }}</strong></td>
                                <td><strong>{{ $fmt($item->qty_received) }} {{ $item->rawMaterial->unit ?? '' }}</strong></td>
                                <td><strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="procurement-detail-total">
                    <div><span>Subtotal</span><strong>Rp {{ number_format($subtotal, 0, ',', '.') }}</strong></div>
                    <div class="grand-total"><span>Total Purchase Order</span><strong>Rp {{ number_format($subtotal, 0, ',', '.') }}</strong></div>
                </div>
            </div>

            <div class="procurement-detail-card">
                <div class="procurement-detail-card-header">
                    <div>
                        <h2>Catatan Pesanan</h2>
                        <p>Catatan yang disertakan untuk supplier.</p>
                    </div>
                </div>
                <div class="procurement-detail-notes">
                    {{ $purchaseOrder->notes ?: 'Tidak ada catatan tambahan.' }}
                </div>
            </div>

            <div class="procurement-detail-card">
                <div class="procurement-detail-card-header">
                    <div>
                        <h2>Status Email</h2>
                        <p>Informasi pengiriman purchase order ke supplier.</p>
                    </div>
                </div>

                <div class="procurement-email-history">
                    <div class="procurement-email-history-icon success">
                        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <rect x="3" y="5" width="18" height="14" rx="2" />
                            <path d="m3 7 9 6 9-6" />
                        </svg>
                    </div>

                    <div class="procurement-email-history-content">
                        <div class="procurement-email-history-title">
                            <strong>Purchase Order berhasil dikirim</strong>
                            <span class="procurement-email-badge">Terkirim</span>
                        </div>

                        <span>Dikirim ke {{ $purchaseOrder->supplier->email ?? '—' }}</span>
                        <small>{{ $purchaseOrder->email_sent_at?->translatedFormat('d M Y • H:i') ?? '-' }}</small>
                    </div>
                </div>
            </div>

        </div>

        <div class="procurement-detail-sidebar">

            <div class="procurement-detail-card">
                <div class="procurement-detail-card-header">
                    <div>
                        <h2>Supplier</h2>
                        <p>Informasi supplier pesanan.</p>
                    </div>
                </div>

                <div class="procurement-supplier-profile">
                    <div class="procurement-supplier-avatar">{{ strtoupper(substr($purchaseOrder->supplier->name ?? '-', 0, 1)) }}</div>
                    <div class="procurement-supplier-name">
                        <strong>{{ $purchaseOrder->supplier->name ?? '—' }}</strong>
                        <span>{{ $purchaseOrder->supplier->is_active ? 'Supplier Aktif' : 'Supplier Nonaktif' }}</span>
                    </div>
                </div>

                <div class="procurement-supplier-details">
                    <div class="procurement-supplier-detail-item"><span>Email</span><strong>{{ $purchaseOrder->supplier->email ?? '—' }}</strong></div>
                    <div class="procurement-supplier-detail-item"><span>Nomor Telepon</span><strong>{{ $purchaseOrder->supplier->phone ?? '—' }}</strong></div>
                    <div class="procurement-supplier-detail-item"><span>Alamat</span><strong>{{ $purchaseOrder->supplier->address ?? '—' }}</strong></div>
                </div>
            </div>

            <div class="procurement-detail-card">
                <div class="procurement-detail-card-header">
                    <div>
                        <h2>Informasi PO</h2>
                    </div>
                </div>
                <div class="procurement-po-information">
                    <div><span>Nomor PO</span><strong>{{ $purchaseOrder->po_no }}</strong></div>
                    <div><span>Tanggal Dibuat</span><strong>{{ $purchaseOrder->created_at->translatedFormat('d M Y') }}</strong></div>
                    <div><span>Total Item</span><strong>{{ $purchaseOrder->items->count() }} bahan</strong></div>
                    <div><span>Status</span><strong class="po-info-status {{ $purchaseOrder->status_class }}">{{ $purchaseOrder->status_label }}</strong></div>
                </div>
            </div>

            <div class="procurement-inventory-info {{ $purchaseOrder->status === \App\Models\PurchaseOrder::STATUS_RECEIVED ? 'received' : '' }}">
                <div class="procurement-inventory-info-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="3" y="5" width="18" height="16" rx="2" />
                        <path d="M3 10h18" />
                        <path d="M9 14h6" />
                    </svg>
                </div>
                <div>
                    <strong>
                        @if ($purchaseOrder->status === \App\Models\PurchaseOrder::STATUS_RECEIVED)
                        Inventory telah diperbarui
                        @elseif ($purchaseOrder->status === \App\Models\PurchaseOrder::STATUS_PARTIALLY_RECEIVED)
                        Sebagian stok telah diperbarui
                        @else
                        Menunggu penerimaan barang
                        @endif
                    </strong>
                    <span>
                        @if ($purchaseOrder->status === \App\Models\PurchaseOrder::STATUS_RECEIVED)
                        Stok bahan baku telah ditambahkan dan riwayat inventory tercatat.
                        @else
                        Setelah barang diterima, stok bahan baku akan diperbarui oleh sistem.
                        @endif
                    </span>
                </div>
            </div>

        </div>
    </div>
</div>


@if ($purchaseOrder->status !== \App\Models\PurchaseOrder::STATUS_RECEIVED)

<div class="procurement-receive-modal-overlay" id="receiveModal">

    <div class="procurement-receive-modal">

        <div class="procurement-receive-modal-header">
            <div>
                <h2>Konfirmasi Barang Diterima</h2>
                <p>Masukkan jumlah barang yang benar-benar diterima (boleh sebagian).</p>
            </div>
            <button type="button" class="procurement-receive-modal-close" id="closeReceiveModal">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.procurement.receive', $purchaseOrder->po_no) }}">
            @csrf

            <div class="procurement-receive-modal-body">

                <div class="procurement-receive-warning">
                    <div class="procurement-receive-warning-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M12 9v4" />
                            <path d="M12 17h.01" />
                            <path d="M10.3 3.8 2.6 17.2A2 2 0 0 0 4.3 20h15.4a2 2 0 0 0 1.7-2.8L13.7 3.8a2 2 0 0 0-3.4 0Z" />
                        </svg>
                    </div>
                    <div>
                        <strong>Konfirmasi penerimaan akan memperbarui inventory</strong>
                        <span>Stok bahan baku akan bertambah sesuai jumlah yang benar-benar diterima.</span>
                    </div>
                </div>

                <div class="procurement-receive-summary">
                    <div><span>Purchase Order</span><strong>{{ $purchaseOrder->po_no }}</strong></div>
                    <div><span>Supplier</span><strong>{{ $purchaseOrder->supplier->name ?? '—' }}</strong></div>
                </div>

                <div class="procurement-receive-items">
                    <span class="procurement-receive-items-label">Barang yang diterima</span>

                    @foreach ($purchaseOrder->items as $item)
                    @php $remaining = $item->qty_ordered - $item->qty_received; @endphp

                    <div class="procurement-receive-item">
                        <div>
                            <strong>{{ $item->rawMaterial->name ?? '—' }}</strong>
                            <span>Sisa: {{ $fmt($remaining) }} {{ $item->rawMaterial->unit ?? '' }} dari {{ $fmt($item->qty_ordered) }}</span>
                        </div>

                        <input type="hidden" name="items[{{ $loop->index }}][item_id]" value="{{ $item->id }}">
                        <input
                            type="number"
                            name="items[{{ $loop->index }}][qty_received]"
                            value="{{ $fmt($remaining) }}"
                            min="0"
                            max="{{ $remaining }}"
                            step="0.001"
                            style="width:90px; text-align:right;"
                            required>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="procurement-receive-modal-footer">
                <button type="button" class="procurement-detail-button secondary" id="cancelReceiveButton">Batal</button>
                <button type="submit" class="procurement-detail-button primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m5 12 4 4L19 6" />
                    </svg>
                    Ya, Barang Diterima
                </button>
            </div>
        </form>
    </div>
</div>

@endif

@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const openReceiveModal = document.getElementById('openReceiveModal');
        const receiveModal = document.getElementById('receiveModal');
        const closeReceiveModal = document.getElementById('closeReceiveModal');
        const cancelReceiveButton = document.getElementById('cancelReceiveButton');

        if (!receiveModal || !openReceiveModal) return;

        function openModal() {
            receiveModal.classList.add('show');
            document.body.classList.add('modal-open');
        }

        function closeModal() {
            receiveModal.classList.remove('show');
            document.body.classList.remove('modal-open');
        }

        openReceiveModal.addEventListener('click', openModal);
        closeReceiveModal.addEventListener('click', closeModal);
        cancelReceiveButton.addEventListener('click', closeModal);
        receiveModal.addEventListener('click', e => {
            if (e.target === receiveModal) closeModal();
        });
    });
</script>
@endpush