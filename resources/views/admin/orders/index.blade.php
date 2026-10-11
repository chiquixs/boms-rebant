@extends('layouts.admin')

@section('title', 'Order Management - REBANT BOMS')

@section('content')

{{-- =========================
    PAGE HEADER
========================= --}}

<div class="page-header">

    <div>
        <span class="page-eyebrow">ORDER MANAGEMENT</span>
        <h1>Order Management</h1>
        <p>Kelola dan pantau seluruh pesanan dari berbagai platform.</p>
    </div>

    <button
        type="button"
        class="add-order-button"
        id="openAddOrderModal"
    >
        <svg width="17" height="17"
             viewBox="0 0 24 24"
             fill="none"
             stroke="currentColor"
             stroke-width="2">
            <path d="M12 5v14"/>
            <path d="M5 12h14"/>
        </svg>

        <span>Add Order</span>
    </button>

</div>


{{-- =========================
    ORDER STATUS SUMMARY
========================= --}}

<div class="order-stats">

    {{-- NEW --}}
    <div class="stat-card">

        <div class="stat-icon new">
            <svg width="21" height="21"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.8">
                <path d="M12 5v14"/>
                <path d="M5 12h14"/>
                <circle cx="12" cy="12" r="9"/>
            </svg>
        </div>

        <div class="stat-content">
            <span>New</span>
            <strong>12</strong>
            <small>Pesanan baru masuk</small>
        </div>

    </div>


    {{-- WAITING PRODUCTION --}}
    <div class="stat-card">

        <div class="stat-icon waiting">
            <svg width="21" height="21"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.8">
                <circle cx="12" cy="12" r="9"/>
                <path d="M12 7v5l3 2"/>
            </svg>
        </div>

        <div class="stat-content">
            <span>Waiting Production</span>
            <strong>8</strong>
            <small>Menunggu proses produksi</small>
        </div>

    </div>


    {{-- READY TO PACK --}}
    <div class="stat-card">

        <div class="stat-icon ready">
            <svg width="21" height="21"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.8">
                <path d="M21 8l-9 5-9-5"/>
                <path d="M3 8l9-5 9 5v8l-9 5-9-5V8Z"/>
                <path d="M12 13v8"/>
            </svg>
        </div>

        <div class="stat-content">
            <span>Ready to Pack</span>
            <strong>6</strong>
            <small>Siap untuk dikemas</small>
        </div>

    </div>


    {{-- COMPLETED --}}
    <div class="stat-card">

        <div class="stat-icon completed">
            <svg width="21" height="21"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.8">
                <circle cx="12" cy="12" r="9"/>
                <path d="m8 12 2.5 2.5L16 9"/>
            </svg>
        </div>

        <div class="stat-content">
            <span>Completed</span>
            <strong>102</strong>
            <small>Pesanan selesai</small>
        </div>

    </div>

</div>


{{-- =========================
    ORDER TABLE
========================= --}}

<div class="orders-card">

    <div class="orders-card-header">

        <div>
            <h2>Daftar Order</h2>
            <p>Pantau seluruh pesanan TikTok Shop dan Shopee.</p>
        </div>

        <div class="order-tools">

            {{-- SEARCH --}}
            <div class="search-box">

                <svg width="18" height="18"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.8">
                    <circle cx="11" cy="11" r="7"/>
                    <path d="m20 20-3.5-3.5"/>
                </svg>

                <input
                    type="text"
                    placeholder="Cari order ID..."
                >

            </div>


            {{-- PLATFORM FILTER --}}
            <select class="platform-filter">
                <option value="">Semua Platform</option>
                <option value="tiktok">TikTok Shop</option>
                <option value="shopee">Shopee</option>
            </select>


            {{-- STATUS FILTER --}}
            <select class="status-filter">
                <option value="">Semua Status</option>
                <option value="new">New</option>
                <option value="waiting-production">Waiting Production</option>
                <option value="ready-to-pack">Ready to Pack</option>
                <option value="completed">Completed</option>
            </select>

        </div>

    </div>


    {{-- =========================
        TABLE
    ========================= --}}

    <div class="table-wrapper">

        <table class="orders-table">

            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Tanggal</th>
                    <th>Platform</th>
                    <th>Total Nominal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

                {{-- ORDER 1 --}}
                <tr>

                    <td>
                        <span class="order-id">#RB-1001</span>
                    </td>

                    <td>11 Oct 2026</td>

                    <td>
                        <span class="platform-badge tiktok">
                            TikTok Shop
                        </span>
                    </td>

                    <td>
                        <strong>Rp350.000</strong>
                    </td>

                    <td>
                        <span class="order-status new">
                            <span></span>
                            New
                        </span>
                    </td>

                    <td>
                        <button
                            type="button"
                            class="action-button"
                            title="Lihat detail order"
                        >
                            <svg width="18" height="18"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="1.8">
                                <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/>
                                <circle cx="12" cy="12" r="2.5"/>
                            </svg>
                        </button>
                    </td>

                </tr>


                {{-- ORDER 2 --}}
                <tr>

                    <td>
                        <span class="order-id">#RB-1002</span>
                    </td>

                    <td>11 Oct 2026</td>

                    <td>
                        <span class="platform-badge shopee">
                            Shopee
                        </span>
                    </td>

                    <td>
                        <strong>Rp275.000</strong>
                    </td>

                    <td>
                        <span class="order-status waiting">
                            <span></span>
                            Waiting Production
                        </span>
                    </td>

                    <td>
                        <button
                            type="button"
                            class="action-button"
                            title="Lihat detail order"
                        >
                            <svg width="18" height="18"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="1.8">
                                <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/>
                                <circle cx="12" cy="12" r="2.5"/>
                            </svg>
                        </button>
                    </td>

                </tr>


                {{-- ORDER 3 --}}
                <tr>

                    <td>
                        <span class="order-id">#RB-1003</span>
                    </td>

                    <td>10 Oct 2026</td>

                    <td>
                        <span class="platform-badge tiktok">
                            TikTok Shop
                        </span>
                    </td>

                    <td>
                        <strong>Rp420.000</strong>
                    </td>

                    <td>
                        <span class="order-status ready">
                            <span></span>
                            Ready to Pack
                        </span>
                    </td>

                    <td>
                        <button
                            type="button"
                            class="action-button"
                            title="Lihat detail order"
                        >
                            <svg width="18" height="18"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="1.8">
                                <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/>
                                <circle cx="12" cy="12" r="2.5"/>
                            </svg>
                        </button>
                    </td>

                </tr>


                {{-- ORDER 4 --}}
                <tr>

                    <td>
                        <span class="order-id">#RB-1004</span>
                    </td>

                    <td>09 Oct 2026</td>

                    <td>
                        <span class="platform-badge shopee">
                            Shopee
                        </span>
                    </td>

                    <td>
                        <strong>Rp180.000</strong>
                    </td>

                    <td>
                        <span class="order-status completed">
                            <span></span>
                            Completed
                        </span>
                    </td>

                    <td>
                        <button
                            type="button"
                            class="action-button"
                            title="Lihat detail order"
                        >
                            <svg width="18" height="18"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="1.8">
                                <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/>
                                <circle cx="12" cy="12" r="2.5"/>
                            </svg>
                        </button>
                    </td>

                </tr>


                {{-- ORDER 5 --}}
                <tr>

                    <td>
                        <span class="order-id">#RB-1005</span>
                    </td>

                    <td>09 Oct 2026</td>

                    <td>
                        <span class="platform-badge tiktok">
                            TikTok Shop
                        </span>
                    </td>

                    <td>
                        <strong>Rp515.000</strong>
                    </td>

                    <td>
                        <span class="order-status waiting">
                            <span></span>
                            Waiting Production
                        </span>
                    </td>

                    <td>
                        <button
                            type="button"
                            class="action-button"
                            title="Lihat detail order"
                        >
                            <svg width="18" height="18"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="1.8">
                                <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/>
                                <circle cx="12" cy="12" r="2.5"/>
                            </svg>
                        </button>
                    </td>

                </tr>

            </tbody>

        </table>

    </div>


    {{-- =========================
        TABLE FOOTER
    ========================= --}}

    <div class="table-footer">

        <span>
            Menampilkan 1–5 dari 128 order
        </span>

        <div class="pagination">
            <button type="button" disabled>‹</button>
            <button type="button" class="active">1</button>
            <button type="button">2</button>
            <button type="button">3</button>
            <button type="button">›</button>
        </div>

    </div>

</div>


{{-- =========================
    ADD ORDER MODAL
========================= --}}

<div class="modal-overlay" id="addOrderModal">

    <div class="order-modal">

        {{-- MODAL HEADER --}}
        <div class="modal-header">

            <div>
                <span class="modal-eyebrow">NEW ORDER</span>
                <h2>Add New Order</h2>
                <p>Tambahkan pesanan baru ke sistem REBANT.</p>
            </div>

            <button
                type="button"
                class="modal-close"
                id="closeAddOrderModal"
            >
                <svg width="20" height="20"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2">
                    <path d="M6 6l12 12"/>
                    <path d="M18 6L6 18"/>
                </svg>
            </button>

        </div>


        {{-- MODAL FORM --}}
        <form id="addOrderForm" onsubmit="return false;">

            <div class="modal-body">


                {{-- ORDER ID --}}
                <div class="modal-form-group">

                    <label for="orderId">
                        Order ID
                    </label>

                    <input
                        type="text"
                        id="orderId"
                        placeholder="Contoh: #RB-1006"
                    >

                </div>


                {{-- DATE + PLATFORM --}}
                <div class="modal-form-row">

                    <div class="modal-form-group">

                        <label for="orderDate">
                            Tanggal
                        </label>

                        <input
                            type="date"
                            id="orderDate"
                        >

                    </div>


                    <div class="modal-form-group">

                        <label for="orderPlatform">
                            Platform
                        </label>

                        <select id="orderPlatform">

                            <option value="">
                                Pilih platform
                            </option>

                            <option value="tiktok">
                                TikTok Shop
                            </option>

                            <option value="shopee">
                                Shopee
                            </option>

                        </select>

                    </div>

                </div>


                {{-- CUSTOMER NAME --}}
                <div class="modal-form-group">

                    <label for="customerName">
                        Nama Pelanggan
                    </label>

                    <input
                        type="text"
                        id="customerName"
                        name="customer_name"
                        placeholder="Masukkan nama pelanggan"
                    >

                </div>


                {{-- =========================
                    ORDER ITEMS
                ========================= --}}

                <div class="order-items-section">

                    <div class="order-items-header">

                        <div>
                            <label>Produk / SKU</label>
                            <span>Tambahkan produk yang dipesan pelanggan.</span>
                        </div>

                        <button
                            type="button"
                            class="add-product-button"
                            id="addProductButton"
                        >
                            <svg width="14" height="14"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="2">
                                <path d="M12 5v14"/>
                                <path d="M5 12h14"/>
                            </svg>

                            Tambah Produk
                        </button>

                    </div>


                    {{-- PRODUCT ITEMS --}}
                    <div class="order-items" id="orderItems">

                        {{-- ITEM PERTAMA --}}
                        <div class="order-item">

                            <div class="item-product">

                                <label>Produk / SKU</label>

                                <div class="product-select-wrapper">

                                    <img
                                        class="product-thumbnail"
                                        src=""
                                        alt="Product"
                                    >

                                    <select class="product-select">

                                        <option value="">
                                            Pilih produk / SKU
                                        </option>

                                        <option
                                            value="100000"
                                            data-image="{{ asset('images/login-illustration.png') }}"
                                        >
                                            RB-WD01 — Wall Decor Minimalist
                                        </option>

                                        <option
                                            value="125000"
                                            data-image="{{ asset('images/login-illustration.png') }}"
                                        >
                                            RB-WD02 — Wall Decor Classic
                                        </option>

                                        <option
                                            value="150000"
                                            data-image="{{ asset('images/login-illustration.png') }}"
                                        >
                                            RB-WD03 — Wall Decor Premium
                                        </option>

                                    </select>

                                </div>

                            </div>


                            <div class="item-quantity">

                                <label>Qty</label>

                                <input
                                    type="number"
                                    class="quantity-input"
                                    min="1"
                                    value="1"
                                >

                            </div>


                            <div class="item-price">

                                <label>Harga</label>

                                <div class="price-display">
                                    Rp 0
                                </div>

                            </div>


                            <button
                                type="button"
                                class="remove-item-button"
                                title="Hapus produk"
                            >
                                <svg width="17" height="17"
                                     viewBox="0 0 24 24"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="1.8">
                                    <path d="M3 6h18"/>
                                    <path d="M8 6V4h8v2"/>
                                    <path d="M19 6l-1 14H6L5 6"/>
                                    <path d="M10 11v5"/>
                                    <path d="M14 11v5"/>
                                </svg>
                            </button>

                        </div>

                    </div>


                    {{-- TOTAL --}}
                    <div class="order-total">

                        <span>Total Nominal</span>

                        <strong id="orderTotal">
                            Rp 0
                        </strong>

                    </div>

                </div>


                {{-- CUSTOMER NOTES --}}
                <div class="modal-form-group">

                    <label for="customerNotes">
                        Notes Pelanggan
                        <span class="optional-label">(Optional)</span>
                    </label>

                    <textarea
                        id="customerNotes"
                        name="customer_notes"
                        rows="3"
                        placeholder="Contoh: Tolong packing lebih aman, warna sesuai pesanan, dll."
                    ></textarea>

                </div>


                {{-- STATUS INFO --}}
                <div class="new-order-info">

                    <span>Status order</span>

                    <strong>
                        New
                    </strong>

                </div>

            </div>


            {{-- MODAL FOOTER --}}
            <div class="modal-footer">

                <button
                    type="button"
                    class="modal-cancel"
                    id="cancelAddOrder"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="modal-save"
                >
                    Save Order
                </button>

            </div>

        </form>

    </div>

</div>

@endsection


@push('scripts')

<script>

    // ==========================
    // MODAL
    // ==========================

    const addOrderModal =
        document.getElementById('addOrderModal');

    const openAddOrderModal =
        document.getElementById('openAddOrderModal');

    const closeAddOrderModal =
        document.getElementById('closeAddOrderModal');

    const cancelAddOrder =
        document.getElementById('cancelAddOrder');


    // Open modal
    openAddOrderModal.addEventListener('click', function () {
        addOrderModal.classList.add('active');
    });


    // Close from X
    closeAddOrderModal.addEventListener('click', function () {
        addOrderModal.classList.remove('active');
    });


    // Close from Cancel
    cancelAddOrder.addEventListener('click', function () {
        addOrderModal.classList.remove('active');
    });


    // Close when clicking outside modal
    addOrderModal.addEventListener('click', function (event) {

        if (event.target === addOrderModal) {
            addOrderModal.classList.remove('active');
        }

    });


    // Close with Escape
    document.addEventListener('keydown', function (event) {

        if (event.key === 'Escape') {
            addOrderModal.classList.remove('active');
        }

    });



    // ==========================
    // ORDER ITEMS
    // ==========================

    const orderItems =
        document.getElementById('orderItems');

    const addProductButton =
        document.getElementById('addProductButton');

    const orderTotal =
        document.getElementById('orderTotal');



    // ==========================
    // FORMAT RUPIAH
    // ==========================

    function formatRupiah(number) {

        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(number);

    }



    // ==========================
    // UPDATE PRODUCT ITEM
    // ==========================

    function updateProductItem(item) {

        const product =
            item.querySelector('.product-select');

        const priceDisplay =
            item.querySelector('.price-display');

        const thumbnail =
            item.querySelector('.product-thumbnail');

        const selectedOption =
            product.options[product.selectedIndex];

        const price =
            Number(product.value) || 0;


        // Update harga
        priceDisplay.textContent =
            formatRupiah(price);


        // Update thumbnail
        if (
            product.value &&
            selectedOption.dataset.image
        ) {

            thumbnail.src =
                selectedOption.dataset.image;

            thumbnail.classList.add('active');

        } else {

            thumbnail.src = '';

            thumbnail.classList.remove('active');

        }

    }



    // ==========================
    // CALCULATE TOTAL
    // ==========================

    function calculateOrderTotal() {

        let total = 0;

        const items =
            orderItems.querySelectorAll('.order-item');


        items.forEach(function (item) {

            const product =
                item.querySelector('.product-select');

            const quantity =
                item.querySelector('.quantity-input');

            const price =
                Number(product.value) || 0;

            const qty =
                Number(quantity.value) || 0;


            total += price * qty;

        });


        orderTotal.textContent =
            formatRupiah(total);

    }



    // ==========================
    // PRODUCT / QUANTITY CHANGE
    // ==========================

    orderItems.addEventListener('change', function (event) {

        if (
            event.target.classList.contains('product-select')
        ) {

            const item =
                event.target.closest('.order-item');

            updateProductItem(item);

            calculateOrderTotal();

        }


        if (
            event.target.classList.contains('quantity-input')
        ) {

            calculateOrderTotal();

        }

    });


    orderItems.addEventListener('input', function (event) {

        if (
            event.target.classList.contains('quantity-input')
        ) {

            calculateOrderTotal();

        }

    });



    // ==========================
    // ADD PRODUCT
    // ==========================

    addProductButton.addEventListener('click', function () {

        const newItem =
            document.createElement('div');


        newItem.classList.add('order-item');


        newItem.innerHTML = `

            <div class="item-product">

                <label>Produk / SKU</label>

                <div class="product-select-wrapper">

                    <img
                        class="product-thumbnail"
                        src=""
                        alt="Product"
                    >

                    <select class="product-select">

                        <option value="">
                            Pilih produk / SKU
                        </option>

                        <option
                            value="100000"
                            data-image="{{ asset('images/login-illustration.png') }}"
                        >
                            RB-WD01 — Wall Decor Minimalist
                        </option>

                        <option
                            value="125000"
                            data-image="{{ asset('images/login-illustration.png') }}"
                        >
                            RB-WD02 — Wall Decor Classic
                        </option>

                        <option
                            value="150000"
                            data-image="{{ asset('images/login-illustration.png') }}"
                        >
                            RB-WD03 — Wall Decor Premium
                        </option>

                    </select>

                </div>

            </div>


            <div class="item-quantity">

                <label>Qty</label>

                <input
                    type="number"
                    class="quantity-input"
                    min="1"
                    value="1"
                >

            </div>


            <div class="item-price">

                <label>Harga</label>

                <div class="price-display">
                    Rp 0
                </div>

            </div>


            <button
                type="button"
                class="remove-item-button"
                title="Hapus produk"
            >

                <svg width="17" height="17"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.8">

                    <path d="M3 6h18"/>
                    <path d="M8 6V4h8v2"/>
                    <path d="M19 6l-1 14H6L5 6"/>
                    <path d="M10 11v5"/>
                    <path d="M14 11v5"/>

                </svg>

            </button>

        `;


        orderItems.appendChild(newItem);

    });



    // ==========================
    // REMOVE PRODUCT
    // ==========================

    orderItems.addEventListener('click', function (event) {

        const removeButton =
            event.target.closest('.remove-item-button');


        if (!removeButton) {
            return;
        }


        const items =
            orderItems.querySelectorAll('.order-item');


        // Minimal harus ada satu produk
        if (items.length <= 1) {
            return;
        }


        const item =
            removeButton.closest('.order-item');


        item.remove();


        calculateOrderTotal();

    });

</script>

@endpush