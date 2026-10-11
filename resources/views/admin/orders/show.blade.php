@extends('layouts.admin')

@section('title', 'Detail Order - REBANT BOMS')

@section('content')

{{-- =========================
    PAGE HEADER
========================= --}}

<div class="detail-page-header">

    <div>

        <a href="/admin/orders" class="back-to-orders">

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
                #RB-{{ $orderId }}
            </h1>

            <span class="order-status new">
                <span></span>
                New
            </span>

        </div>

        <p>
            Detail informasi dan status pesanan pelanggan.
        </p>

    </div>

</div>

{{-- =========================
    ORDER INFORMATION
========================= --}}

<div class="detail-info-grid">

    {{-- ORDER INFORMATION --}}
    <div class="detail-card">

        <div class="detail-card-header status-card-header">

    <div>
        <h2>Order Status</h2>
        <p>Pantau progress pesanan hingga selesai.</p>
    </div>

    <button
        type="button"
        class="next-status-button"
        id="nextStatusButton"
    >
        <span>Move to Waiting Production</span>

        <svg width="14" height="14"
             viewBox="0 0 24 24"
             fill="none"
             stroke="currentColor"
             stroke-width="2">
            <path d="M5 12h14"/>
            <path d="m13 6 6 6-6 6"/>
        </svg>
    </button>

</div>

        <div class="detail-info-list">

            <div class="detail-info-item">
                <span>Order ID</span>
                <strong>#RB-{{ $orderId }}</strong>
            </div>

            <div class="detail-info-item">
                <span>Tanggal Order</span>
                <strong>11 Oct 2026</strong>
            </div>

            <div class="detail-info-item">
                <span>Platform</span>

                <strong>
                    <span class="platform-badge tiktok">
                        TikTok Shop
                    </span>
                </strong>
            </div>

            <div class="detail-info-item">
                <span>Total Nominal</span>
                <strong>Rp350.000</strong>
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
                <strong>Alya Putri</strong>
            </div>

            <div class="detail-info-item detail-info-item-notes">

                <span>Customer Notes</span>

                <div class="customer-note">
                    Tolong packing lebih aman dan pastikan produk
                    tidak tergores.
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
            <p>Daftar produk yang dipesan pelanggan.</p>
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
                    <th>Harga Satuan</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>

            <tbody>

                {{-- PRODUCT 1 --}}
                <tr>

                    <td>

                        <div class="detail-product">

                            <img
                                src="{{ asset('images/login-illustration.png') }}"
                                alt="Wall Decor Minimalist"
                                class="detail-product-image"
                            >

                            <div>
                                <strong>Wall Decor Minimalist</strong>
                                <span>SKU: RB-WD01</span>
                            </div>

                        </div>

                    </td>

                    <td>Rp100.000</td>

                    <td>
                        <span class="detail-quantity">
                            2
                        </span>
                    </td>

                    <td>
                        <strong>Rp200.000</strong>
                    </td>

                </tr>


                {{-- PRODUCT 2 --}}
                <tr>

                    <td>

                        <div class="detail-product">

                            <img
                                src="{{ asset('images/login-illustration.png') }}"
                                alt="Wall Decor Premium"
                                class="detail-product-image"
                            >

                            <div>
                                <strong>Wall Decor Premium</strong>
                                <span>SKU: RB-WD03</span>
                            </div>

                        </div>

                    </td>

                    <td>Rp150.000</td>

                    <td>
                        <span class="detail-quantity">
                            1
                        </span>
                    </td>

                    <td>
                        <strong>Rp150.000</strong>
                    </td>

                </tr>

            </tbody>

        </table>

    </div>


    {{-- TOTAL --}}
    <div class="detail-order-total">

        <span>Total Nominal</span>

        <strong>
            Rp350.000
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
        <div class="status-step active" data-status-step="0">

            <div class="status-step-marker">
                <span>1</span>
            </div>

            <div class="status-step-info">
                <strong>New</strong>
                <span>Order diterima</span>
            </div>

        </div>


        <div class="status-line" data-status-line="0"></div>


        {{-- WAITING PRODUCTION --}}
        <div class="status-step" data-status-step="1">

            <div class="status-step-marker">
                <span>2</span>
            </div>

            <div class="status-step-info">
                <strong>Waiting Production</strong>
                <span>Menunggu produksi</span>
            </div>

        </div>


        <div class="status-line" data-status-line="1"></div>


        {{-- READY TO PACK --}}
        <div class="status-step" data-status-step="2">

            <div class="status-step-marker">
                <span>3</span>
            </div>

            <div class="status-step-info">
                <strong>Ready to Pack</strong>
                <span>Siap dikemas</span>
            </div>

        </div>


        <div class="status-line" data-status-line="2"></div>


        {{-- COMPLETED --}}
        <div class="status-step" data-status-step="3">

            <div class="status-step-marker">
                <span>4</span>
            </div>

            <div class="status-step-info">
                <strong>Completed</strong>
                <span>Order selesai</span>
            </div>

        </div>

    </div>

</div>

@endsection

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const statusSteps =
        document.querySelectorAll('[data-status-step]');

    const statusLines =
        document.querySelectorAll('[data-status-line]');

    const nextStatusButton =
        document.getElementById('nextStatusButton');

    const statusBadge =
        document.querySelector('.detail-title-row .order-status');

    const statuses = [
        {
            name: 'New',
            className: 'new'
        },
        {
            name: 'Waiting Production',
            className: 'waiting'
        },
        {
            name: 'Ready to Pack',
            className: 'ready'
        },
        {
            name: 'Completed',
            className: 'completed'
        }
    ];

    let currentStatus = 0;


    function updateStatusUI() {

        statusSteps.forEach((step, index) => {

            step.classList.remove(
                'active',
                'completed'
            );

            if (index < currentStatus) {
                step.classList.add('completed');
            }

            if (index === currentStatus) {
                step.classList.add('active');
            }

        });


        statusLines.forEach((line, index) => {

            line.classList.toggle(
                'completed',
                index < currentStatus
            );

        });


        // Update badge di header
        if (statusBadge) {

            statusBadge.className =
                'order-status ' +
                statuses[currentStatus].className;

            statusBadge.innerHTML =
                '<span></span>' +
                statuses[currentStatus].name;

        }


        // Kalau sudah Completed
        if (currentStatus === statuses.length - 1) {

            nextStatusButton.disabled = true;

            nextStatusButton.querySelector('span')
                .textContent = 'Order Completed';

            return;
        }


        // Tampilkan status berikutnya di tombol
        nextStatusButton.disabled = false;

        nextStatusButton.querySelector('span')
            .textContent =
                'Move to ' +
                statuses[currentStatus + 1].name;

    }


    nextStatusButton.addEventListener('click', function () {

        if (currentStatus < statuses.length - 1) {

            currentStatus++;

            updateStatusUI();

        }

    });


    updateStatusUI();

});
</script>

@endpush