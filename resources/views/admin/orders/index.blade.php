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

@if (session('success'))
    <div style="margin-bottom:16px;padding:12px 14px;background:#E8F3E3;color:#2F5D24;border-radius:12px;font-size:13px;">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div style="margin-bottom:16px;padding:12px 14px;background:#FDECEA;color:#C0392B;border-radius:12px;font-size:13px;">
        {{ $errors->first() }}
    </div>
@endif

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
            <strong>{{ $counts['NEW'] ?? 0 }}</strong>                  {{-- New --}}
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
            <strong>{{ $counts['WAITING_PRODUCTION'] ?? 0 }}</strong>   {{-- Waiting Production --}}
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
            <strong>{{ $counts['READY_TO_PACK'] ?? 0 }}</strong>        {{-- Ready to Pack --}}
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
            <strong>{{ $counts['COMPLETED'] ?? 0 }}</strong>            {{-- Completed --}}
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

            <form method="GET" action="{{ route('admin.orders.index') }}" style="display: contents;">

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
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari order ID..."
                    >

                </div>

                {{-- PLATFORM FILTER --}}
                <select name="platform" class="platform-filter" onchange="this.form.submit()">
                    <option value="">Semua Platform</option>
                    @foreach ($platforms as $platform)
                        <option value="{{ $platform->id }}" @selected(request('platform') == $platform->id)>
                            {{ $platform->name }}
                        </option>
                    @endforeach
                </select>

                {{-- STATUS FILTER --}}
                <select name="status" class="status-filter" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="NEW" @selected(request('status') === 'NEW')>New</option>
                    <option value="WAITING_PRODUCTION" @selected(request('status') === 'WAITING_PRODUCTION')>Waiting Production</option>
                    <option value="READY_TO_PACK" @selected(request('status') === 'READY_TO_PACK')>Ready to Pack</option>
                    <option value="COMPLETED" @selected(request('status') === 'COMPLETED')>Completed</option>
                </select>

            </form>

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
                @forelse ($orders as $order)
                    <tr>
                        <td><span class="order-id">{{ $order->platform_order_no }}</span></td>
                        <td>{{ $order->order_date->format('d M Y') }}</td>
                        <td>
                            <span class="platform-badge {{ $order->platform->badge_class }}">
                                {{ $order->platform->name }}
                            </span>
                        </td>
                        <td><strong>Rp{{ number_format($order->total_amount, 0, ',', '.') }}</strong></td>
                        <td>
                            <span class="order-status {{ $order->status_class }}">
                                <span></span>
                                {{ $order->status_label }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order) }}" class="action-button" title="Lihat detail order">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/>
                                    <circle cx="12" cy="12" r="2.5"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:30px;">Belum ada order.</td>
                    </tr>
                @endforelse
            </tbody>

        </table>

    </div>


    {{-- =========================
        TABLE FOOTER
    ========================= --}}

    <div class="table-footer">

        <span>
            Menampilkan {{ $orders->firstItem() ?? 0 }}–{{ $orders->lastItem() ?? 0 }} dari {{ $orders->total() }} order
        </span>

        <div class="pagination">

            <button type="button"
                    @disabled($orders->onFirstPage())
                    onclick="location.href='{{ $orders->previousPageUrl() }}'">‹</button>

            @for ($page = max(1, $orders->currentPage() - 2); $page <= min($orders->lastPage(), $orders->currentPage() + 2); $page++)
                <button type="button"
                        class="{{ $page === $orders->currentPage() ? 'active' : '' }}"
                        onclick="location.href='{{ $orders->url($page) }}'">{{ $page }}</button>
            @endfor

            <button type="button"
                    @disabled(! $orders->hasMorePages())
                    onclick="location.href='{{ $orders->nextPageUrl() }}'">›</button>

        </div>

    </div>

</div>


{{-- =========================
    ADD ORDER MODAL
========================= --}}

<div
    class="modal-overlay"
    id="addOrderModal"
    data-has-errors="{{ $errors->any() ? '1' : '0' }}"
    data-catalog="{{ json_encode($catalog) }}"
>

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
        <form id="addOrderForm" method="POST" action="{{ route('admin.orders.store') }}">
        @csrf

            <div class="modal-body">


                {{-- ORDER ID --}}
                <div class="modal-form-group">

                    <label for="orderId">
                        Order ID
                    </label>

                    <input
                        type="text"
                        id="orderId"
                        name="platform_order_no"
                        value="{{ old('platform_order_no') }}"
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
                            name="order_date"
                            value="{{ old('order_date', now()->toDateString()) }}"
                        >

                    </div>


                    <div class="modal-form-group">

                        <label for="orderPlatform">
                            Platform
                        </label>

                        <select id="orderPlatform" name="platform_id">
                            <option value="">Pilih platform</option>
                            @foreach ($platforms as $platform)
                                <option value="{{ $platform->id }}" @selected(old('platform_id') == $platform->id)>
                                    {{ $platform->name }}
                                </option>
                            @endforeach
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
                        value="{{ old('customer_name') }}"
                        placeholder="Masukkan nama pelanggan"
                    >

                </div>


                {{-- =========================
                    ORDER ITEMS
                ========================= --}}

                <div class="order-items-section">

                    <div class="order-items-header">

                        <div>
                            <label>Produk Pesanan</label>
                            <span>Pilih produk dan varian yang dipesan pelanggan.</span>
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
                <div class="order-items" id="orderItems"></div>


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
                        name="notes"
                        rows="3"
                        placeholder="Contoh: Tolong packing lebih aman, warna sesuai pesanan, dll."
                    >{{ old('notes') }}</textarea>

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

{{-- =========================
    CONFIRM SAVE ORDER MODAL
========================= --}}

<div class="modal-overlay" id="confirmOrderModal" style="z-index: 1100;">

    <div class="order-modal" style="max-width: 460px;">

        <div class="modal-header">

            <div>
                <span class="modal-eyebrow">KONFIRMASI</span>
                <h2>Pastikan isian order benar</h2>
                <p>Harap cek kembali data order sebelum disimpan.</p>
            </div>

        </div>

        <div class="modal-body">

            <div class="confirm-summary">

                <div class="confirm-row">
                    <span>Order ID</span>
                    <strong id="confirmOrderNo">-</strong>
                </div>

                <div class="confirm-row">
                    <span>Tanggal</span>
                    <strong id="confirmDate">-</strong>
                </div>

                <div class="confirm-row">
                    <span>Platform</span>
                    <strong id="confirmPlatform">-</strong>
                </div>

                <div class="confirm-row">
                    <span>Pelanggan</span>
                    <strong id="confirmCustomer">-</strong>
                </div>

                <div class="confirm-items" id="confirmItems"></div>

                <div class="confirm-row confirm-total">
                    <span>Total Nominal</span>
                    <strong id="confirmTotal">Rp 0</strong>
                </div>

            </div>

        </div>

        <div class="modal-footer">

            <button type="button" class="modal-cancel" id="cancelConfirm">
                Batal
            </button>

            <button type="button" class="modal-save" id="confirmSave">
                Save
            </button>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script>

    // ==========================
    // MODAL
    // ==========================

    const addOrderModal = document.getElementById('addOrderModal');
    const openAddOrderModal = document.getElementById('openAddOrderModal');
    const closeAddOrderModal = document.getElementById('closeAddOrderModal');
    const cancelAddOrder = document.getElementById('cancelAddOrder');

    openAddOrderModal.addEventListener('click', function () {
        addOrderModal.classList.add('active');
    });

    closeAddOrderModal.addEventListener('click', function () {
        addOrderModal.classList.remove('active');
    });

    cancelAddOrder.addEventListener('click', function () {
        addOrderModal.classList.remove('active');
    });

    addOrderModal.addEventListener('click', function (event) {
        if (event.target === addOrderModal) {
            addOrderModal.classList.remove('active');
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            if (confirmOrderModal.classList.contains('active')) {
                confirmOrderModal.classList.remove('active');
            } else {
                addOrderModal.classList.remove('active');
            }
        }
    });

    // Buka modal lagi jika validasi gagal
    if (addOrderModal.dataset.hasErrors === '1') {
        addOrderModal.classList.add('active');
    }


    // ==========================
    // DATA KATALOG (dari database)
    // ==========================

    const catalog = JSON.parse(addOrderModal.dataset.catalog || '[]');

    const orderItems = document.getElementById('orderItems');
    const addProductButton = document.getElementById('addProductButton');
    const orderTotal = document.getElementById('orderTotal');


    // ==========================
    // HELPER
    // ==========================

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(number);
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function findGroup(id) {
        return catalog.find(function (group) {
            return String(group.id) === String(id);
        });
    }

    const groupOptionsHtml =
        '<option value="">Pilih produk</option>' +
        catalog.map(function (group) {
            return `<option value="${group.id}">${escapeHtml(group.name)}</option>`;
        }).join('');


    // ==========================
    // BUAT BARIS PRODUK
    // ==========================

    function createItemRow() {

        const item = document.createElement('div');
        item.classList.add('order-item');

        item.innerHTML = `
            <div class="item-product">
                <label>Produk</label>
                <div class="product-select-wrapper">
                    <img class="product-thumbnail" src="" alt="Product">
                    <select class="product-select">${groupOptionsHtml}</select>
                </div>
            </div>

            <div class="item-variant">
                <label>Varian</label>
                <select class="variant-select" disabled>
                    <option value="">Pilih produk terlebih dahulu</option>
                </select>
            </div>

            <div class="item-quantity">
                <label>Qty</label>
                <input type="number" class="quantity-input" min="1" value="1">
            </div>

            <div class="item-price">
                <label>Harga</label>
                <div class="price-display">Rp 0</div>
            </div>

            <button type="button" class="remove-item-button" title="Hapus produk">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.8">
                    <path d="M3 6h18"/>
                    <path d="M8 6V4h8v2"/>
                    <path d="M19 6l-1 14H6L5 6"/>
                    <path d="M10 11v5"/>
                    <path d="M14 11v5"/>
                </svg>
            </button>
        `;

        orderItems.appendChild(item);
    }


    // ==========================
    // PRODUK DIPILIH -> ISI VARIAN
    // ==========================

    function updateGroup(item) {

        const groupSelect = item.querySelector('.product-select');
        const variantSelect = item.querySelector('.variant-select');
        const priceDisplay = item.querySelector('.price-display');
        const thumbnail = item.querySelector('.product-thumbnail');

        const group = findGroup(groupSelect.value);

        // reset thumbnail
        thumbnail.src = '';
        thumbnail.classList.remove('active');

        if (!group) {
            variantSelect.disabled = true;
            variantSelect.innerHTML =
                '<option value="">Pilih produk terlebih dahulu</option>';
            priceDisplay.textContent = formatRupiah(0);
            return;
        }

        variantSelect.disabled = false;
        variantSelect.innerHTML =
            '<option value="">Pilih varian</option>' +
            group.variants.map(function (variant) {
                return `<option value="${variant.id}">${escapeHtml(variant.name)} — ${escapeHtml(variant.sku)}</option>`;
            }).join('');

        priceDisplay.textContent = formatRupiah(group.price);
    }


    // ==========================
    // VARIAN DIPILIH -> THUMBNAIL
    // ==========================

    function updateVariant(item) {

        const group = findGroup(item.querySelector('.product-select').value);
        const variantId = item.querySelector('.variant-select').value;
        const thumbnail = item.querySelector('.product-thumbnail');

        const variant = group
            ? group.variants.find(function (v) { return String(v.id) === String(variantId); })
            : null;

        if (variant && variant.image) {
            thumbnail.src = variant.image;
            thumbnail.classList.add('active');
        } else {
            thumbnail.src = '';
            thumbnail.classList.remove('active');
        }
    }


    // ==========================
    // HITUNG TOTAL
    // ==========================

    function calculateOrderTotal() {

        let total = 0;

        orderItems.querySelectorAll('.order-item').forEach(function (item) {

            const group = findGroup(item.querySelector('.product-select').value);
            const qty = Number(item.querySelector('.quantity-input').value) || 0;

            if (group) {
                total += group.price * qty;
            }
        });

        orderTotal.textContent = formatRupiah(total);
    }


    // ==========================
    // EVENT: PRODUK / VARIAN / QTY
    // ==========================

    orderItems.addEventListener('change', function (event) {

        const item = event.target.closest('.order-item');

        if (event.target.classList.contains('product-select')) {
            updateGroup(item);
            calculateOrderTotal();
        }

        if (event.target.classList.contains('variant-select')) {
            updateVariant(item);
        }

        if (event.target.classList.contains('quantity-input')) {
            calculateOrderTotal();
        }
    });

    orderItems.addEventListener('input', function (event) {
        if (event.target.classList.contains('quantity-input')) {
            calculateOrderTotal();
        }
    });


    // ==========================
    // TAMBAH & HAPUS PRODUK
    // ==========================

    addProductButton.addEventListener('click', function () {
        createItemRow();
    });

    orderItems.addEventListener('click', function (event) {

        const removeButton = event.target.closest('.remove-item-button');

        if (!removeButton) {
            return;
        }

        // Minimal harus ada satu produk
        if (orderItems.querySelectorAll('.order-item').length <= 1) {
            return;
        }

        removeButton.closest('.order-item').remove();
        calculateOrderTotal();
    });


    // ==========================
    // KONFIRMASI SEBELUM SIMPAN
    // ==========================

    const addOrderForm = document.getElementById('addOrderForm');
    const confirmOrderModal = document.getElementById('confirmOrderModal');

    function assignItemNames() {
        orderItems.querySelectorAll('.order-item').forEach(function (item, index) {
            item.querySelector('.variant-select').name = `items[${index}][product_id]`;
            item.querySelector('.quantity-input').name = `items[${index}][qty]`;
        });
    }

    function selectedText(select) {
        return select.options[select.selectedIndex]?.textContent.trim() || '';
    }

    // Klik Save Order -> tampilkan konfirmasi (form belum dikirim)
    addOrderForm.addEventListener('submit', function (event) {

        event.preventDefault();

        const orderNo  = document.getElementById('orderId').value.trim();
        const date     = document.getElementById('orderDate').value;
        const platform = document.getElementById('orderPlatform');
        const customer = document.getElementById('customerName').value.trim();

        // Cek isian wajib
        if (!orderNo || !date || !platform.value || !customer) {
            alert('Lengkapi Order ID, tanggal, platform, dan nama pelanggan.');
            return;
        }

        const rows = Array.from(orderItems.querySelectorAll('.order-item'));

        const incomplete = rows.some(function (row) {
            return !row.querySelector('.variant-select').value;
        });

        if (incomplete) {
            alert('Pilih produk dan varian di semua baris.');
            return;
        }

        // Isi ringkasan
        document.getElementById('confirmOrderNo').textContent  = orderNo;
        document.getElementById('confirmDate').textContent     = date;
        document.getElementById('confirmPlatform').textContent = selectedText(platform);
        document.getElementById('confirmCustomer').textContent = customer;
        document.getElementById('confirmTotal').textContent    = orderTotal.textContent;

        document.getElementById('confirmItems').innerHTML = rows.map(function (row) {
            const product = selectedText(row.querySelector('.product-select'));
            const variant = selectedText(row.querySelector('.variant-select'));
            const qty     = row.querySelector('.quantity-input').value;

            return `<div>
                        <span>${escapeHtml(product)} — ${escapeHtml(variant)}</span>
                        <strong>x${escapeHtml(qty)}</strong>
                    </div>`;
        }).join('');

        confirmOrderModal.classList.add('active');
    });

    // Batal -> tutup konfirmasi, kembali ke form
    document.getElementById('cancelConfirm').addEventListener('click', function () {
        confirmOrderModal.classList.remove('active');
    });

    // Save -> kirim form sungguhan
    document.getElementById('confirmSave').addEventListener('click', function () {
        assignItemNames();
        addOrderForm.submit();
    });

    // ==========================
    // BARIS PERTAMA
    // ==========================

    createItemRow();

</script>

@endpush