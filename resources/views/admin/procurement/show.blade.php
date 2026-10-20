@extends('layouts.admin')

@section('title', 'Detail Purchase Order')

@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | Dummy Purchase Order Data
    |--------------------------------------------------------------------------
    | Sementara untuk kebutuhan frontend.
    | Nanti data akan berasal dari backend/database.
    */

    if ($po === 'PO-0011') {
        $purchaseOrder = [
            'number' => 'PO-0011',
            'date' => '10 Okt 2026',
            'time' => '11:30',
            'status' => 'received',

            'supplier' => [
                'name' => 'CV MDF Malang',
                'email' => 'order@mdfmalang.com',
                'phone' => '0812-3456-7890',
                'address' => 'Malang, Jawa Timur',
            ],

            'items' => [
                [
                    'name' => 'MDF',
                    'type' => 'SHEET',
                    'unit' => 'lembar',
                    'quantity' => 10,
                    'price' => 125000,
                ],
            ],

            'notes' => 'Mohon dikirim sesuai spesifikasi bahan yang biasa digunakan.',

            'email' => [
                'status' => 'sent',
                'sent_at' => '10 Okt 2026 • 11:35',
            ],

            'received_at' => '13 Okt 2026 • 09:42',
        ];
    } else {
        $purchaseOrder = [
            'number' => $po,
            'date' => '13 Okt 2026',
            'time' => '09:40',
            'status' => 'ordered',

            'supplier' => [
                'name' => 'PT Kayu Jaya',
                'email' => 'supplier@kayujaya.com',
                'phone' => '0812-9876-5432',
                'address' => 'Malang, Jawa Timur',
            ],

            'items' => [
                [
                    'name' => 'MDF',
                    'type' => 'SHEET',
                    'unit' => 'lembar',
                    'quantity' => 10,
                    'price' => 85000,
                ],
            ],

            'notes' => 'Mohon diproses sesuai jumlah purchase order.',

            'email' => [
                'status' => 'sent',
                'sent_at' => '13 Okt 2026 • 09:45',
            ],

            'received_at' => null,
        ];
    }


    $subtotal = 0;

    foreach ($purchaseOrder['items'] as $item) {
        $subtotal += $item['quantity'] * $item['price'];
    }
@endphp


<div
    class="procurement-detail-page"
    id="procurementDetailPage"
    data-status="{{ $purchaseOrder['status'] }}"
>

    {{-- ========================================
        BACK
    ========================================= --}}
    <a
        href="/admin/procurement"
        class="procurement-detail-back"
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

        Kembali ke Procurement
    </a>


    {{-- ========================================
        HEADER
    ========================================= --}}
    <div class="procurement-detail-header">

        <div>

            <div class="procurement-detail-breadcrumb">
                PROCUREMENT / PURCHASE ORDER
            </div>

            <div class="procurement-detail-title">

                <h1>
                    {{ $purchaseOrder['number'] }}
                </h1>

                <span
                    class="procurement-detail-status {{ $purchaseOrder['status'] }}"
                    id="purchaseOrderStatus"
                >
                    @if ($purchaseOrder['status'] === 'received')
                        Received
                    @else
                        Ordered
                    @endif
                </span>

            </div>

            <p>
                Dibuat {{ $purchaseOrder['date'] }}
                •
                {{ $purchaseOrder['time'] }}
            </p>

        </div>


        @if ($purchaseOrder['status'] === 'ordered')

            <button
                type="button"
                class="procurement-receive-button"
                id="openReceiveModal"
            >
                <svg
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="M21 8l-9 5-9-5"/>
                    <path d="M3 8l9-5 9 5v8l-9 5-9-5V8Z"/>
                    <path d="M12 13v8"/>
                </svg>

                Tandai Barang Diterima
            </button>

        @endif

    </div>



    {{-- ========================================
        STATUS / PROGRESS
    ========================================= --}}
    <div class="procurement-detail-progress">

        <div class="procurement-progress-item completed">

            <div class="procurement-progress-icon">

                <svg
                    width="15"
                    height="15"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="m5 12 4 4L19 6"/>
                </svg>

            </div>

            <div>
                <strong>PO Dibuat</strong>
                <span>{{ $purchaseOrder['date'] }}</span>
            </div>

        </div>


        <div class="procurement-progress-line completed"></div>


        <div class="procurement-progress-item completed">

            <div class="procurement-progress-icon">

                <svg
                    width="15"
                    height="15"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="m5 12 4 4L19 6"/>
                </svg>

            </div>

            <div>
                <strong>Email Terkirim</strong>
                <span>{{ $purchaseOrder['email']['sent_at'] }}</span>
            </div>

        </div>


        <div
            class="procurement-progress-line {{ $purchaseOrder['status'] === 'received' ? 'completed' : '' }}"
            id="receiveProgressLine"
        ></div>


        <div
            class="procurement-progress-item {{ $purchaseOrder['status'] === 'received' ? 'completed' : 'pending' }}"
            id="receiveProgress"
        >

            <div class="procurement-progress-icon">

                @if ($purchaseOrder['status'] === 'received')

                    <svg
                        width="15"
                        height="15"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="m5 12 4 4L19 6"/>
                    </svg>

                @else

                    <span></span>

                @endif

            </div>

            <div>
                <strong>Barang Diterima</strong>

                <span id="receivedProgressText">
                    @if ($purchaseOrder['status'] === 'received')
                        {{ $purchaseOrder['received_at'] }}
                    @else
                        Menunggu penerimaan
                    @endif
                </span>
            </div>

        </div>

    </div>



    {{-- ========================================
        CONTENT GRID
    ========================================= --}}
    <div class="procurement-detail-grid">


        {{-- ========================================
            LEFT COLUMN
        ========================================= --}}
        <div class="procurement-detail-main">


            {{-- PURCHASE ORDER ITEMS --}}
            <div class="procurement-detail-card">

                <div class="procurement-detail-card-header">

                    <div>
                        <h2>Detail Pesanan</h2>

                        <p>
                            Bahan baku yang dipesan dalam purchase order ini.
                        </p>
                    </div>

                </div>


                <div class="procurement-detail-table-wrapper">

                    <table class="procurement-detail-table">

                        <thead>
                            <tr>
                                <th>Bahan Baku</th>
                                <th>Tipe</th>
                                <th>Harga Satuan</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>


                        <tbody>

                            @foreach ($purchaseOrder['items'] as $item)

                                @php
                                    $itemSubtotal =
                                        $item['quantity'] * $item['price'];
                                @endphp

                                <tr>

                                    <td>
                                        <div class="procurement-detail-material">

                                            <strong>
                                                {{ $item['name'] }}
                                            </strong>

                                            <span>
                                                Satuan: {{ $item['unit'] }}
                                            </span>

                                        </div>
                                    </td>


                                    <td>

                                        <span class="procurement-detail-material-type {{ strtolower($item['type']) === 'sheet' ? 'sheet' : 'non-sheet' }}">
                                            {{ $item['type'] }}
                                        </span>

                                    </td>


                                    <td>
                                        Rp {{ number_format($item['price'], 0, ',', '.') }}
                                        / {{ $item['unit'] }}
                                    </td>


                                    <td>
                                        <strong>
                                            {{ $item['quantity'] }}
                                            {{ $item['unit'] }}
                                        </strong>
                                    </td>


                                    <td>
                                        <strong>
                                            Rp {{ number_format($itemSubtotal, 0, ',', '.') }}
                                        </strong>
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>


                {{-- TOTAL --}}
                <div class="procurement-detail-total">

                    <div>
                        <span>Subtotal</span>

                        <strong>
                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                        </strong>
                    </div>


                    <div class="grand-total">
                        <span>Total Purchase Order</span>

                        <strong>
                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                        </strong>
                    </div>

                </div>

            </div>



            {{-- NOTES --}}
            <div class="procurement-detail-card">

                <div class="procurement-detail-card-header">

                    <div>
                        <h2>Catatan Pesanan</h2>

                        <p>
                            Catatan yang disertakan untuk supplier.
                        </p>
                    </div>

                </div>


                <div class="procurement-detail-notes">

                    @if ($purchaseOrder['notes'])

                        {{ $purchaseOrder['notes'] }}

                    @else

                        <span>
                            Tidak ada catatan tambahan.
                        </span>

                    @endif

                </div>

            </div>


            {{-- EMAIL STATUS --}}
            <div class="procurement-detail-card">

                <div class="procurement-detail-card-header">

                    <div>
                        <h2>Status Email</h2>

                        <p>
                            Informasi pengiriman purchase order ke supplier.
                        </p>
                    </div>

                </div>


                <div class="procurement-email-history">

                    <div class="procurement-email-history-icon success">

                        <svg
                            width="19"
                            height="19"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <rect
                                x="3"
                                y="5"
                                width="18"
                                height="14"
                                rx="2"
                            />

                            <path d="m3 7 9 6 9-6"/>
                        </svg>

                    </div>


                    <div class="procurement-email-history-content">

                        <div class="procurement-email-history-title">

                            <strong>
                                Purchase Order berhasil dikirim
                            </strong>

                            <span class="procurement-email-badge">
                                Terkirim
                            </span>

                        </div>


                        <span>
                            Dikirim ke
                            {{ $purchaseOrder['supplier']['email'] }}
                        </span>

                        <small>
                            {{ $purchaseOrder['email']['sent_at'] }}
                        </small>

                    </div>

                </div>

            </div>

        </div>



        {{-- ========================================
            RIGHT COLUMN
        ========================================= --}}
        <div class="procurement-detail-sidebar">


            {{-- SUPPLIER --}}
            <div class="procurement-detail-card">

                <div class="procurement-detail-card-header">

                    <div>
                        <h2>Supplier</h2>

                        <p>
                            Informasi supplier pesanan.
                        </p>
                    </div>

                </div>


                <div class="procurement-supplier-profile">

                    <div class="procurement-supplier-avatar">

                        {{ strtoupper(substr($purchaseOrder['supplier']['name'], 0, 1)) }}

                    </div>


                    <div class="procurement-supplier-name">

                        <strong>
                            {{ $purchaseOrder['supplier']['name'] }}
                        </strong>

                        <span>
                            Supplier Aktif
                        </span>

                    </div>

                </div>


                <div class="procurement-supplier-details">

                    <div class="procurement-supplier-detail-item">

                        <span>Email</span>

                        <strong>
                            {{ $purchaseOrder['supplier']['email'] }}
                        </strong>

                    </div>


                    <div class="procurement-supplier-detail-item">

                        <span>Nomor Telepon</span>

                        <strong>
                            {{ $purchaseOrder['supplier']['phone'] }}
                        </strong>

                    </div>


                    <div class="procurement-supplier-detail-item">

                        <span>Alamat</span>

                        <strong>
                            {{ $purchaseOrder['supplier']['address'] }}
                        </strong>

                    </div>

                </div>

            </div>



            {{-- PO INFORMATION --}}
            <div class="procurement-detail-card">

                <div class="procurement-detail-card-header">

                    <div>
                        <h2>Informasi PO</h2>
                    </div>

                </div>


                <div class="procurement-po-information">

                    <div>
                        <span>Nomor PO</span>

                        <strong>
                            {{ $purchaseOrder['number'] }}
                        </strong>
                    </div>


                    <div>
                        <span>Tanggal Dibuat</span>

                        <strong>
                            {{ $purchaseOrder['date'] }}
                        </strong>
                    </div>


                    <div>
                        <span>Total Item</span>

                        <strong>
                            {{ count($purchaseOrder['items']) }} bahan
                        </strong>
                    </div>


                    <div>
                        <span>Status</span>

                        <strong
                            class="po-info-status {{ $purchaseOrder['status'] }}"
                            id="poInfoStatus"
                        >
                            @if ($purchaseOrder['status'] === 'received')
                                Received
                            @else
                                Ordered
                            @endif
                        </strong>

                    </div>

                </div>

            </div>



            {{-- INVENTORY INFORMATION --}}
            <div
                class="procurement-inventory-info {{ $purchaseOrder['status'] === 'received' ? 'received' : '' }}"
                id="inventoryInfo"
            >

                <div class="procurement-inventory-info-icon">

                    <svg
                        width="18"
                        height="18"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <rect
                            x="3"
                            y="5"
                            width="18"
                            height="16"
                            rx="2"
                        />

                        <path d="M3 10h18"/>
                        <path d="M9 14h6"/>
                    </svg>

                </div>


                <div>

                    <strong id="inventoryInfoTitle">

                        @if ($purchaseOrder['status'] === 'received')
                            Inventory telah diperbarui
                        @else
                            Menunggu penerimaan barang
                        @endif

                    </strong>


                    <span id="inventoryInfoText">

                        @if ($purchaseOrder['status'] === 'received')

                            Stok bahan baku telah ditambahkan
                            dan riwayat inventory tercatat.

                        @else

                            Setelah barang diterima, stok bahan baku
                            akan diperbarui oleh sistem.

                        @endif

                    </span>

                </div>

            </div>

        </div>

    </div>

</div>



{{-- ========================================
    RECEIVE ORDER MODAL
======================================== --}}
@if ($purchaseOrder['status'] === 'ordered')

<div
    class="procurement-receive-modal-overlay"
    id="receiveModal"
>

    <div class="procurement-receive-modal">

        {{-- HEADER --}}
        <div class="procurement-receive-modal-header">

            <div>

                <h2>
                    Konfirmasi Barang Diterima
                </h2>

                <p>
                    Pastikan barang dari supplier telah diterima
                    sebelum melakukan konfirmasi.
                </p>

            </div>


            <button
                type="button"
                class="procurement-receive-modal-close"
                id="closeReceiveModal"
                aria-label="Tutup modal"
            >

                <svg
                    width="20"
                    height="20"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="M18 6 6 18"/>
                    <path d="m6 6 12 12"/>
                </svg>

            </button>

        </div>


        {{-- BODY --}}
        <div class="procurement-receive-modal-body">

            <div class="procurement-receive-warning">

                <div class="procurement-receive-warning-icon">

                    <svg
                        width="20"
                        height="20"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M12 9v4"/>
                        <path d="M12 17h.01"/>
                        <path d="M10.3 3.8 2.6 17.2A2 2 0 0 0 4.3 20h15.4a2 2 0 0 0 1.7-2.8L13.7 3.8a2 2 0 0 0-3.4 0Z"/>
                    </svg>

                </div>


                <div>

                    <strong>
                        Konfirmasi penerimaan akan memperbarui inventory
                    </strong>

                    <span>
                        Setelah dikonfirmasi, status PO menjadi Received
                        dan stok bahan baku akan bertambah sesuai jumlah
                        barang yang diterima.
                    </span>

                </div>

            </div>


            <div class="procurement-receive-summary">

                <div>
                    <span>Purchase Order</span>
                    <strong>{{ $purchaseOrder['number'] }}</strong>
                </div>


                <div>
                    <span>Supplier</span>
                    <strong>{{ $purchaseOrder['supplier']['name'] }}</strong>
                </div>

            </div>


            <div class="procurement-receive-items">

                <span class="procurement-receive-items-label">
                    Barang yang diterima
                </span>


                @foreach ($purchaseOrder['items'] as $item)

                    <div class="procurement-receive-item">

                        <div>
                            <strong>
                                {{ $item['name'] }}
                            </strong>

                            <span>
                                {{ $item['type'] }}
                            </span>
                        </div>


                        <strong>
                            {{ $item['quantity'] }}
                            {{ $item['unit'] }}
                        </strong>

                    </div>

                @endforeach

            </div>

        </div>


        {{-- FOOTER --}}
        <div class="procurement-receive-modal-footer">

            <button
                type="button"
                class="procurement-detail-button secondary"
                id="cancelReceiveButton"
            >
                Batal
            </button>


            <button
                type="button"
                class="procurement-detail-button primary"
                id="confirmReceiveButton"
            >
                <svg
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="m5 12 4 4L19 6"/>
                </svg>

                Ya, Barang Diterima
            </button>

        </div>

    </div>

</div>

@endif

@endsection



@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | Receive Modal
    |--------------------------------------------------------------------------
    */

    const openReceiveModal =
        document.getElementById('openReceiveModal');

    const receiveModal =
        document.getElementById('receiveModal');

    const closeReceiveModal =
        document.getElementById('closeReceiveModal');

    const cancelReceiveButton =
        document.getElementById('cancelReceiveButton');

    const confirmReceiveButton =
        document.getElementById('confirmReceiveButton');


    if (!receiveModal || !openReceiveModal) {
        return;
    }


    function openModal() {

        receiveModal.classList.add('show');

        document.body.classList.add('modal-open');

    }


    function closeModal() {

        receiveModal.classList.remove('show');

        document.body.classList.remove('modal-open');

    }


    openReceiveModal.addEventListener(
        'click',
        openModal
    );


    closeReceiveModal.addEventListener(
        'click',
        closeModal
    );


    cancelReceiveButton.addEventListener(
        'click',
        closeModal
    );


    receiveModal.addEventListener(
        'click',
        function (event) {

            if (event.target === receiveModal) {
                closeModal();
            }

        }
    );



    /*
    |--------------------------------------------------------------------------
    | Dummy Receive Confirmation
    |--------------------------------------------------------------------------
    |
    | Frontend simulation only.
    |
    | Nanti backend akan:
    | 1. Update status PO -> Received
    | 2. Tambah current_stock bahan baku
    | 3. Buat stock movement / inventory history
    | 4. Simpan tanggal penerimaan
    |
    */

    confirmReceiveButton.addEventListener(
        'click',
        function () {

            const purchaseOrderStatus =
                document.getElementById(
                    'purchaseOrderStatus'
                );

            const poInfoStatus =
                document.getElementById(
                    'poInfoStatus'
                );

            const receiveProgress =
                document.getElementById(
                    'receiveProgress'
                );

            const receiveProgressLine =
                document.getElementById(
                    'receiveProgressLine'
                );

            const receivedProgressText =
                document.getElementById(
                    'receivedProgressText'
                );

            const inventoryInfo =
                document.getElementById(
                    'inventoryInfo'
                );

            const inventoryInfoTitle =
                document.getElementById(
                    'inventoryInfoTitle'
                );

            const inventoryInfoText =
                document.getElementById(
                    'inventoryInfoText'
                );


            /*
            | Update PO status
            */

            purchaseOrderStatus.textContent =
                'Received';

            purchaseOrderStatus.classList.remove(
                'ordered'
            );

            purchaseOrderStatus.classList.add(
                'received'
            );


            poInfoStatus.textContent =
                'Received';

            poInfoStatus.classList.remove(
                'ordered'
            );

            poInfoStatus.classList.add(
                'received'
            );


            /*
            | Update progress
            */

            receiveProgress.classList.remove(
                'pending'
            );

            receiveProgress.classList.add(
                'completed'
            );

            receiveProgressLine.classList.add(
                'completed'
            );


            receiveProgress.querySelector(
                '.procurement-progress-icon'
            ).innerHTML = `
                <svg
                    width="15"
                    height="15"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="m5 12 4 4L19 6"/>
                </svg>
            `;


            receivedProgressText.textContent =
                'Baru saja';


            /*
            | Update inventory information
            */

            inventoryInfo.classList.add(
                'received'
            );

            inventoryInfoTitle.textContent =
                'Inventory telah diperbarui';

            inventoryInfoText.textContent =
                'Stok bahan baku telah ditambahkan dan riwayat inventory tercatat.';


            /*
            | Close modal
            */

            closeModal();


            /*
            | Remove confirmation button
            */

            openReceiveModal.style.display =
                'none';


            alert(
                'Barang berhasil ditandai sebagai diterima.\n\n' +
                'Simulasi frontend: stok bahan baku akan diperbarui oleh backend.'
            );

        }
    );

});
</script>

@endpush