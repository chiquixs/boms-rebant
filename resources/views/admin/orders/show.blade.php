@extends('layouts.admin')

@section('title', 'Detail Order - REBANT BOMS')

@section('content')

@php
    // Urutan status untuk progress bar
    $steps = ['NEW', 'WAITING_PRODUCTION', 'READY_TO_PACK', 'COMPLETED'];
    $currentStep = array_search($order->status, $steps);
    $currentStep = $currentStep === false ? 0 : $currentStep;
@endphp

{{-- =========================
    PAGE HEADER
========================= --}}

<div class="detail-page-header">

    <div>

        <a href="{{ route('admin.orders.index') }}" class="back-to-orders">

            <svg width="15" height="15"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2">
                <path d="M19 12H5"/>
                <path d="m12 19-7-7 7-7"/>
            </svg>

            Back to Orders

        </a>

        <span class="detail-eyebrow">
            ORDER DETAIL
        </span>

        <div class="detail-title-row">

            <h1>
                {{ $order->platform_order_no }}
            </h1>

            <span class="order-status {{ $order->status_class }}">
                <span></span>
                {{ $order->status_label }}
            </span>

        </div>

        <p>
            Detail informasi dan status pesanan pelanggan.
        </p>

    </div>

</div>


{{-- =========================
    ORDER & CUSTOMER INFORMATION
========================= --}}

<div class="detail-info-grid">

    {{-- ORDER INFORMATION --}}
    <div class="detail-card">

        <div class="detail-card-header">

            <div>
                <h2>Order Information</h2>
                <p>Informasi utama pesanan.</p>
            </div>

            <div class="detail-card-icon">
                <svg width="18" height="18"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.8">
                    <path d="M6 2h9l5 5v15H6z"/>
                    <path d="M14 2v6h6"/>
                    <path d="M9 13h6"/>
                    <path d="M9 17h6"/>
                </svg>
            </div>

        </div>

        <div class="detail-info-list">

            <div class="detail-info-item">
                <span>Order ID</span>
                <strong>{{ $order->platform_order_no }}</strong>
            </div>

            <div class="detail-info-item">
                <span>Tanggal Order</span>
                <strong>{{ $order->order_date->format('d M Y') }}</strong>
            </div>

            <div class="detail-info-item">

                <span>Platform</span>

                <strong>
                    <span class="platform-badge {{ $order->platform->badge_class }}">
                        {{ $order->platform->name }}
                    </span>
                </strong>

            </div>

            <div class="detail-info-item">
                <span>Total Nominal</span>
                <strong>Rp{{ number_format($order->total_amount, 0, ',', '.') }}</strong>
            </div>

        </div>

    </div>


    {{-- CUSTOMER INFORMATION --}}
    <div class="detail-card">

        <div class="detail-card-header">

            <div>
                <h2>Customer Information</h2>
                <p>Informasi pelanggan dan catatan pesanan.</p>
            </div>

            <div class="detail-card-icon">
                <svg width="18" height="18"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.8">
                    <circle cx="12" cy="8" r="4"/>
                    <path d="M4 21c0-4 3.5-7 8-7s8 3 8 7"/>
                </svg>
            </div>

        </div>

        <div class="detail-info-list">

            <div class="detail-info-item">
                <span>Nama Pelanggan</span>
                <strong>{{ $order->customer_name }}</strong>
            </div>

            <div class="detail-info-item detail-info-item-notes">

                <span>Customer Notes</span>

                <div class="customer-note">
                    {{ $order->notes ?: '-' }}
                </div>

            </div>

        </div>

    </div>

</div>


{{-- =========================
    ORDER ITEMS
========================= --}}

<div class="detail-card order-items-card">

    <div class="detail-card-header">

        <div>
            <h2>Order Items</h2>
            <p>Daftar produk dan varian yang dipesan pelanggan.</p>
        </div>

        <div class="detail-card-icon">
            <svg width="18" height="18"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.8">
                <path d="M21 8l-9 5-9-5"/>
                <path d="M3 8l9-5 9 5v8l-9 5-9-5V8Z"/>
                <path d="M12 13v8"/>
            </svg>
        </div>

    </div>


    <div class="detail-items-table-wrapper">

        <table class="detail-items-table">

            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Varian</th>
                    <th>SKU Variant</th>
                    <th>Harga Satuan</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>

            <tbody>

                @foreach ($order->items as $item)

                    <tr>

                        <td>

                            <div class="detail-product">

                                <img
                                    src="{{ $item->product->image_url ?? asset('images/login-illustration.png') }}"
                                    alt="{{ $item->product->group->name }}"
                                    class="detail-product-image"
                                >

                                <div>
                                    <strong>
                                        {{ $item->product->group->name }}
                                    </strong>

                                    {{-- sesuaikan: kolom kode/SKU di product_groups jika ada --}}
                                    <span>
                                        SKU Product: {{ $item->product->group->sku ?? '-' }}
                                    </span>
                                </div>

                            </div>

                        </td>

                        <td>
                            <span class="detail-variant-name">
                                {{ $item->product->variant_name ?: '-' }}
                            </span>
                        </td>

                        <td>
                            <span class="detail-variant-sku">
                                {{ $item->product->sku }}
                            </span>
                        </td>

                        <td>
                            Rp{{ number_format($item->unit_price, 0, ',', '.') }}
                        </td>

                        <td>
                            <span class="detail-quantity">
                                {{ $item->qty }}
                            </span>
                        </td>

                        <td>
                            <strong>
                                Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                            </strong>
                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>


    {{-- TOTAL --}}
    <div class="detail-order-total">

        <span>Total Nominal</span>

        <strong>
            Rp{{ number_format($order->total_amount, 0, ',', '.') }}
        </strong>

    </div>

</div>


{{-- =========================
    ORDER STATUS
========================= --}}

<div class="detail-card order-status-card">

    <div class="detail-card-header">

        <div>
            <h2>Order Status</h2>
            <p>Pantau progress pesanan hingga selesai.</p>
        </div>

        <div class="detail-card-icon">
            <svg width="18" height="18"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.8">
                <circle cx="12" cy="12" r="9"/>
                <path d="m8 12 2.5 2.5L16 9"/>
            </svg>
        </div>

    </div>


    <div class="status-progress">

        {{-- NEW --}}
        <div class="status-step {{ $currentStep >= 0 ? 'active' : '' }}">

            <div class="status-step-marker">
                <span>1</span>
            </div>

            <div class="status-step-info">
                <strong>New</strong>
                <span>Order diterima</span>
            </div>

        </div>


        <div class="status-line"></div>


        {{-- WAITING PRODUCTION --}}
        <div class="status-step {{ $currentStep >= 1 ? 'active' : '' }}">

            <div class="status-step-marker">
                <span>2</span>
            </div>

            <div class="status-step-info">
                <strong>Waiting Production</strong>
                <span>Stok belum mencukupi</span>
            </div>

        </div>


        <div class="status-line"></div>


        {{-- READY TO PACK --}}
        <div class="status-step {{ $currentStep >= 2 ? 'active' : '' }}">

            <div class="status-step-marker">
                <span>3</span>
            </div>

            <div class="status-step-info">
                <strong>Ready to Pack</strong>
                <span>Barang siap dikemas</span>
            </div>

        </div>


        <div class="status-line"></div>


        {{-- COMPLETED --}}
        <div class="status-step {{ $currentStep >= 3 ? 'active' : '' }}">

            <div class="status-step-marker">
                <span>4</span>
            </div>

            <div class="status-step-info">
                <strong>Completed</strong>
                <span>Pesanan selesai</span>
            </div>

        </div>

    </div>


    {{-- STATUS INFORMATION --}}
    <div class="status-information">

        <div class="status-information-icon">
            <svg width="16" height="16"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2">
                <circle cx="12" cy="12" r="9"/>
                <path d="M12 11v5"/>
                <path d="M12 8h.01"/>
            </svg>
        </div>

        <div>
            <strong>
                Status sedang diproses oleh sistem
            </strong>

            <p>
                Sistem akan memeriksa ketersediaan stok setiap
                varian untuk menentukan proses order selanjutnya.
            </p>
        </div>

    </div>

</div>

@endsection