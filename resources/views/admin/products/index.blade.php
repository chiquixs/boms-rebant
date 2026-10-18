@extends('layouts.admin')

@section('title', 'Master Produk & SKU - REBANT BOMS')

@section('content')

<div class="products-page">

    {{-- =========================
        PAGE HEADER
    ========================= --}}
    <div class="page-header">

        <div>
            <span class="page-eyebrow">
                PRODUCT MANAGEMENT
            </span>

            <h1>Master Data Produk & SKU</h1>

            <p>
                Kelola katalog produk, stok, metode produksi,
                dan resep bahan baku.
            </p>
        </div>

        <button
            type="button"
            class="add-product-button"
        >
            <span>+</span>
            Tambah Produk Baru
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

    @if (session('error'))
        <div style="margin-bottom:16px;padding:12px 14px;background:#FDECEA;color:#C0392B;border-radius:12px;font-size:13px;">
            {{ session('error') }}
        </div>
    @endif


    {{-- =========================
        SEARCH & FILTER
    ========================= --}}
    <form method="GET" action="{{ route('admin.products.index') }}" class="product-filter-card">

        <div class="product-search">
            {{-- svg search tetap --}}
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari berdasarkan Nama Produk atau SKU...">
        </div>

        <select name="category" class="product-filter-select" onchange="this.form.submit()">
            <option value="">Semua Kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(request('category') == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        <select name="type" class="product-filter-select" onchange="this.form.submit()">
            <option value="">Semua Tipe</option>
            <option value="mts" @selected(request('type') === 'mts')>MTS — Make to Stock</option>
            <option value="mto" @selected(request('type') === 'mto')>MTO — Make to Order</option>
        </select>

    </form>


    {{-- =========================
        PRODUCT TABLE
    ========================= --}}
    <div class="product-table-card">

        <div class="product-table-header">

            <div>
                <h2>Daftar Produk</h2>

                <p>
                    Kelola seluruh produk dan SKU yang tersedia.
                </p>
            </div>

            <span class="product-count">{{ $groups->count() }} Produk</span>

        </div>


        <div class="product-table-wrapper">

            <table class="product-table">

                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>SKU Product</th>
                        <th>Varian</th>
                        <th>Kategori</th>
                        <th>Tipe</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($groups as $group)

                        @php
                            $method      = strtolower($group->production_method);
                            $isMto       = $method === 'mto';
                            $status      = $group->stock_status;
                            $single = $group->products->count() === 1 ? $group->products->first() : null;
                            $hasBom      = $group->materials_count > 0;
                            $lowVariants = $isMto
                                            ? collect()
                                            : $group->products->filter(fn ($p) => $p->stock_status === 'low');
                            $hasVariants = $group->products->whereNotNull('variant_name')->isNotEmpty();
                            $image = $group->image_url
                                    ?? $group->products->first(fn ($p) => $p->image_url)?->image_url
                                    ?? asset('images/login-illustration.png');
                        @endphp

                        <tr>

                            <td>
                                <div class="product-info">

                                    <img src="{{ $image }}" alt="{{ $group->name }}">

                                    <div>
                                        <strong>{{ $group->name }}</strong>
                                        <span>Rp{{ number_format($group->price, 0, ',', '.') }}</span>
                                    </div>

                                </div>

                                @if ($lowVariants->isNotEmpty())
                                    <div class="low-stock-note">

                                        <div class="low-stock-note-title">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2">
                                                <path d="M12 9v4"/>
                                                <path d="M12 17h.01"/>
                                                <path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z"/>
                                            </svg>
                                            {{ $lowVariants->count() }} varian low stock
                                        </div>

                                        <ul>
                                            @foreach ($lowVariants as $low)
                                                <li>
                                                    {{ $low->variant_name ?: $low->sku }}
                                                    <span>({{ $low->current_stock }} / min {{ $low->min_stock }})</span>
                                                </li>
                                            @endforeach
                                        </ul>

                                    </div>
                                @endif
                            </td>

                            <td>
                                <span class="sku-code">{{ $group->code }}</span>
                            </td>

                            <td>
                                @if ($hasVariants)
                                    <button type="button" class="variant-count-button"
                                            data-target="variantRow{{ $group->id }}">
                                        {{ $group->products->count() }} Varian
                                    </button>
                                @else
                                    <span class="stock-muted">—</span>
                                @endif
                            </td>

                            <td>{{ $group->category?->name ?? '—' }}</td>

                            <td>
                                <span class="production-badge {{ $method }}">{{ strtoupper($method) }}</span>
                            </td>

                            <td>
                                <div class="product-actions">

                                    <a href="{{ route('admin.products.bom', ['sku' => $group->code]) }}"
                                        class="product-action bom {{ $hasBom ? 'bom-filled' : 'bom-empty' }}"
                                        title="{{ $hasBom ? 'BOM sudah diisi' : 'BOM belum diisi' }}">
                                            BOM
                                        </a>

                                    <button
                                        type="button"
                                        class="product-action edit edit-product-button"
                                        title="Edit produk"
                                        data-update-url="{{ route('admin.products.update', $group) }}"
                                        data-sku="{{ $group->code }}"
                                        data-name="{{ $group->name }}"
                                        data-category="{{ $group->category_id }}"
                                        data-price="{{ (int) $group->price }}"
                                        data-length="{{ $group->length_cm }}"
                                        data-width="{{ $group->width_cm }}"
                                        data-method="{{ $method }}"
                                        data-variants="{{ json_encode($group->products->map(fn ($p) => [
                                            'id'            => $p->id,
                                            'sku'           => $p->sku,
                                            'variant_name'  => $p->variant_name,
                                            'current_stock' => $p->current_stock,
                                            'min_stock'     => $p->min_stock,
                                            'target_stock'  => $p->target_stock,
                                            'image'         => $p->image_url,
                                        ])->values()) }}"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        class="product-action delete delete-product-button"
                                        title="Hapus produk"
                                        data-delete-url="{{ route('admin.products.destroy', $group) }}"
                                        data-name="{{ $group->name }}"
                                    >
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="1.8">
                                            <path d="M3 6h18"/>
                                            <path d="M8 6V4h8v2"/>
                                            <path d="M19 6l-1 14H6L5 6"/>
                                            <path d="M10 11v5"/>
                                            <path d="M14 11v5"/>
                                        </svg>
                                    </button>

                                </div>
                            </td>

                        </tr>

                        @if ($hasVariants)
                            <tr class="variant-detail-row" id="variantRow{{ $group->id }}" style="display: none;">

                                <td colspan="9">

                                    <div class="variant-detail-content">

                                        <div class="variant-detail-header">
                                            <div>
                                                <strong>Varian Produk</strong>
                                                <span>{{ $group->products->count() }} varian tersedia</span>
                                            </div>
                                        </div>

                                        <div class="variant-list">

                                            @foreach ($group->products as $variant)
                                                <div class="variant-item">

                                                    @if ($variant->image_url)
                                                        <img src="{{ $variant->image_url }}"
                                                            alt="{{ $variant->variant_name ?: $variant->sku }}"
                                                            class="variant-thumb"
                                                            loading="lazy">
                                                    @else
                                                        <span class="variant-thumb variant-thumb-empty" title="Belum ada foto">
                                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="1.8">
                                                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                                                <circle cx="9" cy="9" r="2"/>
                                                                <path d="m21 15-5-5L5 21"/>
                                                            </svg>
                                                        </span>
                                                    @endif

                                                    <span class="sku-code">{{ $variant->sku }}</span>
                                                    <strong>{{ $variant->variant_name ?: '—' }}</strong>

                                                    @unless ($isMto)
                                                        <span>Stock: {{ $variant->current_stock }}</span>
                                                        <span>Min: {{ $variant->min_stock ?? '—' }}</span>
                                                        <span>Target: {{ $variant->target_stock ?? '—' }}</span>
                                                        <span class="stock-status {{ $variant->stock_status }}">
                                                            <span></span>
                                                            {{ $variant->stock_status === 'low' ? 'Low Stock' : 'Aman' }}
                                                        </span>
                                                    @endunless

                                                </div>
                                            @endforeach

                                        </div>

                                    </div>

                                </td>

                            </tr>
                        @endif

                    @empty
                        <tr>
                            <td colspan="9" style="text-align:center;padding:30px;">Belum ada produk.</td>
                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- =========================================================
    ADD / EDIT PRODUCT MODAL
========================================================= --}}
<div class="product-modal-overlay" id="productModal"
     data-has-errors="{{ $errors->any() ? '1' : '0' }}"
     data-old-variants="{{ json_encode(old('variants', [])) }}">

    <div class="product-modal">

        {{-- HEADER --}}
        <div class="product-modal-header">

            <div>
                <span class="product-modal-eyebrow">PRODUCT MASTER</span>
                <h2 id="productModalTitle">Tambah Produk Baru</h2>
                <p id="productModalDescription">
                    Tambahkan informasi produk dan metode produksinya.
                </p>
            </div>

            <button type="button" class="product-modal-close" id="closeProductModal"
                    aria-label="Tutup modal">×</button>

        </div>

        {{-- FORM (tampilan saja, belum menyimpan ke database) --}}
        <form id="productForm" method="POST" action="{{ route('admin.products.store') }}"
            data-store-url="{{ route('admin.products.store') }}"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" value="PUT" id="productMethodOverride" disabled>

            <div class="product-modal-body">

                {{-- INFORMASI DASAR --}}
                <div class="product-form-section">

                    <div class="product-form-section-title">
                        <h3>Informasi Dasar</h3>
                        <p>Informasi utama produk yang akan dijual.</p>
                    </div>

                    <div class="product-form-grid">

                        <div class="product-form-group">
                            <label for="productSku">SKU Product</label>
                            <input type="text" id="productSku" name="code" value="{{ old('code') }}" placeholder="Contoh: D6A">
                        </div>

                        <div class="product-form-group">
                            <label for="productName">Nama Produk</label>
                            <input type="text" id="productName" name="name" value="{{ old('name') }}" placeholder="Masukkan nama produk">
                        </div>

                        <div class="product-form-group">
                            <label for="productCategory">Kategori Produk</label>
                            <select id="productCategory" name="category_id">
                                <option value="">Pilih kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="product-form-group">
                            <label for="productPrice">Harga Jual / Unit</label>
                            <div class="price-input-wrapper">
                                <span>Rp</span>
                                <input type="number" id="productPrice" name="price" min="0" value="{{ old('price') }}" placeholder="0">
                            </div>
                        </div>

                        <div class="product-form-group">
                            <label for="productLength">Panjang Produk</label>
                            <div class="stock-input-wrapper">
                                <input type="number" id="productLength" name="length_cm" min="0" step="0.01" value="{{ old('length_cm') }}" placeholder="Contoh: 30">
                                <span>cm</span>
                            </div>
                        </div>

                        <div class="product-form-group">
                            <label for="productWidth">Lebar Produk</label>
                            <div class="stock-input-wrapper">
                                <input type="number" id="productWidth" name="width_cm" min="0" step="0.01" value="{{ old('width_cm') }}" placeholder="Contoh: 20">
                                <span>cm</span>
                            </div>
                        </div>

                    </div>

                </div>


                {{-- METODE PRODUKSI --}}
                <div class="product-form-section">

                    <div class="product-form-section-title">
                        <h3>Metode Produksi</h3>
                        <p>Tentukan bagaimana produk diproduksi.</p>
                    </div>

                    <div class="production-method-options">

                        <label class="production-method-card {{ old('production_method', 'mts') === 'mts' ? 'active' : '' }}">
                            <input type="radio" name="production_method" value="mts" @checked(old('production_method', 'mts') === 'mts')>
                            <div>
                                <strong>Make-to-Stock (MTS)</strong>
                                <span>Produk disimpan sebagai stok barang jadi.</span>
                            </div>
                        </label>

                        <label class="production-method-card {{ old('production_method') === 'mto' ? 'active' : '' }}">
                            <input type="radio" name="production_method" value="mto" @checked(old('production_method') === 'mto')>
                            <div>
                                <strong>Make-to-Order (MTO)</strong>
                                <span>Produk dibuat saat ada pesanan masuk.</span>
                            </div>
                        </label>

                    </div>

                </div>


                {{-- VARIAN (kartu dibuat oleh JavaScript) --}}
                <div class="product-form-section">

                    <div class="product-form-section-title">
                        <h3>Varian Produk</h3>
                        <p>Tambahkan foto, SKU, dan informasi stok untuk setiap varian produk.</p>
                    </div>

                    <div id="variantFormsContainer"></div>

                    <button type="button" class="add-variant-button" id="addVariantButton">
                        + Tambah Varian
                    </button>

                </div>

            </div>

            {{-- FOOTER --}}
            <div class="product-modal-footer">
                <button type="button" class="product-cancel-button" id="cancelProductModal">Batal</button>
                <button type="submit" class="product-save-button" id="productSaveButton">Simpan Produk</button>
            </div>

        </form>

    </div>

</div>
<form id="deleteProductForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const productModal = document.getElementById('productModal');
    const productForm = document.getElementById('productForm');
    const methodOverride = document.getElementById('productMethodOverride');
    const deleteForm = document.getElementById('deleteProductForm');
    const openProductModal = document.querySelector('.add-product-button');
    const closeProductModal = document.getElementById('closeProductModal');
    const cancelProductModal = document.getElementById('cancelProductModal');
    const productModalTitle = document.getElementById('productModalTitle');
    const productModalDescription = document.getElementById('productModalDescription');
    const productSaveButton = document.getElementById('productSaveButton');

    const productSku = document.getElementById('productSku');
    const productName = document.getElementById('productName');
    const productCategory = document.getElementById('productCategory');
    const productPrice = document.getElementById('productPrice');
    const productLength = document.getElementById('productLength');
    const productWidth = document.getElementById('productWidth');

    const productionMethods = document.querySelectorAll('input[name="production_method"]');
    const productionCards = document.querySelectorAll('.production-method-card');

    const variantFormsContainer = document.getElementById('variantFormsContainer');
    const addVariantButton = document.getElementById('addVariantButton');

    const PHOTO_HINT = 'JPG, PNG, atau WEBP. Maksimal 2 MB.';

    let variantIndex = 0;


    function esc(value) {
        const div = document.createElement('div');
        div.textContent = value ?? '';
        return div.innerHTML.replace(/"/g, '&quot;');
    }


    /* =========================================================
       METODE PRODUKSI
    ========================================================= */

    function currentMethod() {
        const checked = document.querySelector('input[name="production_method"]:checked');
        return checked ? checked.value : 'mts';
    }

    // MTO tidak punya stok: field disembunyikan dan tidak terkirim
    function updateVariantStockVisibility() {

        const isMto = currentMethod() === 'mto';

        document.querySelectorAll('.variant-stock-field').forEach(function (field) {

            field.style.display = isMto ? 'none' : '';

            const input = field.querySelector('input');

            if (input) {
                input.disabled = isMto;
            }
        });
    }

    function setActiveMethodCard() {

        productionCards.forEach(function (card) {
            card.classList.remove('active');
        });

        const checked = document.querySelector('input[name="production_method"]:checked');

        if (checked) {
            checked.closest('.production-method-card').classList.add('active');
        }

        updateVariantStockVisibility();
    }

    productionMethods.forEach(function (radio) {
        radio.addEventListener('change', setActiveMethodCard);
    });


    /* =========================================================
       KARTU VARIAN (data = varian yang sudah ada, saat edit)
    ========================================================= */

    function addVariant(data) {

        const v = data || {};
        const isExisting = !!v.id;

        const i = variantIndex++;
        const number = variantFormsContainer.children.length + 1;
        const canRemove = isExisting || number > 1;

        const card = document.createElement('div');
        card.className = 'product-variant-form';

        card.innerHTML = `
            <div class="product-variant-form-header">

                <div>
                    <strong>Varian ${number}</strong>
                    <span>${number === 1 ? 'Varian utama produk' : 'Varian tambahan produk'}</span>
                </div>

                ${canRemove ? '<button type="button" class="remove-variant-button">Hapus</button>' : ''}

            </div>

            ${isExisting ? `<input type="hidden" name="variants[${i}][id]" value="${v.id}">` : ''}

            <div class="variant-form-grid">

                <div class="variant-photo">

                    <label class="variant-photo-box ${v.image ? 'has-image' : ''}" for="variantImage${i}">

                        <img src="${v.image ? esc(v.image) : ''}" alt="Foto varian">

                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.8">
                            <path d="M12 16V4"/>
                            <path d="m7 9 5-5 5 5"/>
                            <path d="M5 20h14"/>
                        </svg>

                    </label>

                    <div class="variant-photo-text">
                        <strong>Foto Varian</strong>
                        <span class="variant-photo-name">${v.image ? 'Foto saat ini. Klik kotak untuk mengganti.' : PHOTO_HINT}</span>
                        <button type="button" class="variant-photo-remove" hidden>Batalkan foto baru</button>
                    </div>

                    <input type="file" id="variantImage${i}" name="variants[${i}][image]"
                           class="variant-photo-input" accept="image/jpeg,image/png,image/webp" hidden>

                </div>

                <div class="product-form-group">
                    <label>SKU Variant</label>
                    <input type="text" name="variants[${i}][sku]" value="${esc(v.sku)}" placeholder="Contoh: D6A-${number}">
                </div>

                <div class="product-form-group">
                    <label>Nama Variant</label>
                    <input type="text" name="variants[${i}][variant_name]" value="${esc(v.variant_name)}" placeholder="Contoh: CCTV">
                </div>

                <div class="product-form-group variant-stock-field">
                    <label>Current Stock</label>
                    <div class="stock-input-wrapper">
                        <input type="number" name="variants[${i}][current_stock]" min="0" value="${esc(v.current_stock)}" placeholder="0">
                        <span>pcs</span>
                    </div>
                </div>

                <div class="product-form-group variant-stock-field">
                    <label>Minimum Stock</label>
                    <div class="stock-input-wrapper">
                        <input type="number" name="variants[${i}][min_stock]" min="0" value="${esc(v.min_stock)}" placeholder="30">
                        <span>pcs</span>
                    </div>
                </div>

                <div class="product-form-group variant-stock-field">
                    <label>Target Stock</label>
                    <div class="stock-input-wrapper">
                        <input type="number" name="variants[${i}][target_stock]" min="0" value="${esc(v.target_stock)}" placeholder="80">
                        <span>pcs</span>
                    </div>
                </div>

            </div>
        `;

        // Simpan foto asli untuk dipulihkan jika foto baru dibatalkan
        card.dataset.originalImage = v.image || '';

        variantFormsContainer.appendChild(card);

        updateVariantStockVisibility();

        return card;
    }

    function renumberVariants() {

        variantFormsContainer.querySelectorAll('.product-variant-form').forEach(function (card, index) {
            card.querySelector('.product-variant-form-header strong').textContent = 'Varian ' + (index + 1);
        });
    }

    addVariantButton.addEventListener('click', function () {
        addVariant();
    });

    variantFormsContainer.addEventListener('click', function (event) {

        const removeButton = event.target.closest('.remove-variant-button');

        if (removeButton) {

            if (variantFormsContainer.children.length <= 1) {
                alert('Minimal harus ada satu varian.');
                return;
            }

            removeButton.closest('.product-variant-form').remove();
            renumberVariants();
            return;
        }

        const cancelPhoto = event.target.closest('.variant-photo-remove');

        if (cancelPhoto) {
            resetVariantPhoto(cancelPhoto.closest('.product-variant-form'));
        }
    });


    /* =========================================================
       FOTO VARIAN
    ========================================================= */

    // Kembali ke foto asli (atau kosong jika varian baru)
    function resetVariantPhoto(card) {

        const original = card.dataset.originalImage || '';
        const box = card.querySelector('.variant-photo-box');

        card.querySelector('.variant-photo-input').value = '';
        card.querySelector('.variant-photo img').src = original;
        box.classList.toggle('has-image', !!original);
        card.querySelector('.variant-photo-name').textContent =
            original ? 'Foto saat ini. Klik kotak untuk mengganti.' : PHOTO_HINT;
        card.querySelector('.variant-photo-remove').hidden = true;
    }

    variantFormsContainer.addEventListener('change', function (event) {

        if (!event.target.classList.contains('variant-photo-input')) {
            return;
        }

        const input = event.target;
        const card = input.closest('.product-variant-form');
        const file = input.files[0];

        if (!file) {
            return;
        }

        if (!file.type.startsWith('image/')) {
            alert('File harus berupa gambar.');
            resetVariantPhoto(card);
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran foto maksimal 2 MB.');
            resetVariantPhoto(card);
            return;
        }

        card.querySelector('.variant-photo img').src = URL.createObjectURL(file);
        card.querySelector('.variant-photo-box').classList.add('has-image');
        card.querySelector('.variant-photo-name').textContent = file.name;
        card.querySelector('.variant-photo-remove').hidden = false;
    });


    /* =========================================================
       MODE FORM (TAMBAH / EDIT)
    ========================================================= */

    function setAddMode() {
        productForm.action = productForm.dataset.storeUrl;
        methodOverride.disabled = true;

        productModalTitle.textContent = 'Tambah Produk Baru';
        productModalDescription.textContent = 'Tambahkan informasi produk dan metode produksinya.';
        productSaveButton.textContent = 'Simpan Produk';
    }

    function setEditMode(updateUrl) {
        productForm.action = updateUrl;
        methodOverride.disabled = false;

        productModalTitle.textContent = 'Edit Produk';
        productModalDescription.textContent = 'Perbarui informasi, varian, dan foto produk.';
        productSaveButton.textContent = 'Simpan Perubahan';
    }

    function resetProductForm() {

        productForm.reset();
        setAddMode();

        variantFormsContainer.innerHTML = '';
        variantIndex = 0;

        setActiveMethodCard();
        addVariant();
    }


    /* =========================================================
       BUKA / TUTUP MODAL
    ========================================================= */

    function showModal() {
        productModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function openModal() {
        resetProductForm();
        showModal();
    }

    function closeModal() {
        productModal.classList.remove('active');
        document.body.style.overflow = '';
        resetProductForm();
    }

    openProductModal.addEventListener('click', openModal);
    closeProductModal.addEventListener('click', closeModal);
    cancelProductModal.addEventListener('click', closeModal);

    productModal.addEventListener('click', function (event) {
        if (event.target === productModal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && productModal.classList.contains('active')) {
            closeModal();
        }
    });


    /* =========================================================
       EDIT
    ========================================================= */

    document.querySelectorAll('.edit-product-button').forEach(function (button) {

        button.addEventListener('click', function () {

            resetProductForm();
            setEditMode(button.dataset.updateUrl);

            productSku.value = button.dataset.sku || '';
            productName.value = button.dataset.name || '';
            productCategory.value = button.dataset.category || '';
            productPrice.value = button.dataset.price || '';
            productLength.value = button.dataset.length || '';
            productWidth.value = button.dataset.width || '';

            const radio = document.querySelector(
                'input[name="production_method"][value="' + button.dataset.method + '"]'
            );

            if (radio) {
                radio.checked = true;
            }

            // Ganti kartu varian kosong dengan varian yang sudah ada
            variantFormsContainer.innerHTML = '';
            variantIndex = 0;

            const variants = JSON.parse(button.dataset.variants || '[]');

            if (variants.length) {
                variants.forEach(function (v) {
                    addVariant(v);
                });
            } else {
                addVariant();
            }

            setActiveMethodCard();
            showModal();
        });

    });


    /* =========================================================
       HAPUS
    ========================================================= */

    document.querySelectorAll('.delete-product-button').forEach(function (button) {

        button.addEventListener('click', function () {

            const ok = confirm(
                'Hapus produk "' + button.dataset.name + '"?\n\n' +
                'Semua varian, foto, dan BOM produk ini ikut terhapus. ' +
                'Tindakan ini tidak bisa dibatalkan.'
            );

            if (!ok) {
                return;
            }

            deleteForm.action = button.dataset.deleteUrl;
            deleteForm.submit();
        });

    });


    /* =========================================================
       SUBMIT
    ========================================================= */

    productForm.addEventListener('submit', function (event) {

        if (!productSku.value.trim() || !productName.value.trim()) {
            event.preventDefault();
            alert('SKU Product dan Nama Produk wajib diisi.');
            return;
        }

        if (productPrice.value === '') {
            event.preventDefault();
            alert('Harga jual wajib diisi.');
            return;
        }

        const skuInputs = variantFormsContainer.querySelectorAll('input[name$="[sku]"]');

        for (const input of skuInputs) {
            if (!input.value.trim()) {
                event.preventDefault();
                alert('SKU Variant wajib diisi di semua varian.');
                input.focus();
                return;
            }
        }
    });


    /* =========================================================
       TOGGLE DAFTAR VARIAN DI TABEL
    ========================================================= */

    document.querySelectorAll('.variant-count-button').forEach(function (button) {

        button.addEventListener('click', function () {

            const row = document.getElementById(button.dataset.target);

            if (!row) {
                return;
            }

            row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
        });

    });


    /* =========================================================
       AWAL
    ========================================================= */

    resetProductForm();

    // Jika validasi tambah produk gagal di server: buka lagi dan isi ulang varian
    if (productModal.dataset.hasErrors === '1') {

        const oldVariants = Object.values(JSON.parse(productModal.dataset.oldVariants || '{}'));

        if (oldVariants.length) {

            variantFormsContainer.innerHTML = '';
            variantIndex = 0;

            oldVariants.forEach(function (v) {
                addVariant({
                    sku: v.sku,
                    variant_name: v.variant_name,
                    current_stock: v.current_stock,
                    min_stock: v.min_stock,
                    target_stock: v.target_stock,
                });
            });
        }

        setActiveMethodCard();
        showModal();
    }

});
</script>

@endpush