@extends('layouts.admin')

@section('title', 'Kategori Produk')

@section('content')

<div class="category-page">

    {{-- PAGE HEADER --}}
    <div class="category-page-header">
        <div>
            <h1>Kategori Produk</h1>
            <p>Kelola kategori yang digunakan pada produk.</p>
        </div>

        <button type="button" class="add-category-button" id="addCategoryButton">
            <span>+</span>
            Tambah Kategori
        </button>
    </div>

    {{-- FLASH MESSAGE --}}
    @if (session('success'))
        <div class="category-alert success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="category-alert error">{{ session('error') }}</div>
    @endif

    {{-- CATEGORY CARD --}}
    <div class="category-card">

        <div class="category-toolbar">
            <div class="category-search">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.8">
                    <circle cx="11" cy="11" r="7"/>
                    <path d="m20 20-3.5-3.5"/>
                </svg>

                <input type="text" id="categorySearch"
                       placeholder="Cari kategori..." autocomplete="off">
            </div>
        </div>

        <div class="category-table-wrapper">
            <table class="category-table">
                <thead>
                    <tr>
                        <th class="category-number-column">No</th>
                        <th>Nama Kategori</th>
                        <th class="category-action-column">Aksi</th>
                    </tr>
                </thead>

                <tbody id="categoryTableBody">
                    @foreach ($categories as $category)
                        <tr data-category-name="{{ $category->name }}"
                            data-update-url="{{ route('admin.categories.update', $category) }}"
                            data-id="{{ $category->id }}">

                            <td class="category-number">{{ $loop->iteration }}</td>

                            <td>
                                <strong class="category-name">{{ $category->name }}</strong>
                            </td>

                            <td>
                                <div class="category-actions">
                                    <button type="button" class="category-action edit">
                                        Edit
                                    </button>

                                    <form action="{{ route('admin.categories.destroy', $category) }}"
                                          method="POST"
                                          class="category-delete-form"
                                          data-name="{{ $category->name }}"
                                          style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="category-action delete">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="category-empty-state" id="categoryEmptyState" style="display: none;">
                Kategori tidak ditemukan.
            </div>
        </div>
    </div>
</div>


{{-- CATEGORY MODAL --}}
<div class="category-modal-overlay" id="categoryModal"
     data-store-url="{{ route('admin.categories.store') }}"
     data-has-error="{{ $errors->has('name') ? '1' : '0' }}"
     data-old-edit-id="{{ old('edit_id') }}"
     data-old-edit-url="{{ old('edit_id') ? route('admin.categories.update', old('edit_id')) : '' }}">

    <div class="category-modal">

        <div class="category-modal-header">
            <div>
                <h2 id="categoryModalTitle">Tambah Kategori</h2>
                <p id="categoryModalDescription">Tambahkan kategori baru untuk produk.</p>
            </div>

            <button type="button" class="category-modal-close"
                    id="closeCategoryModal" aria-label="Tutup modal">×</button>
        </div>

        <form id="categoryForm" method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            <input type="hidden" name="_method" id="categoryMethod" value="PUT" disabled>
            <input type="hidden" name="edit_id" id="categoryEditId" value="{{ old('edit_id') }}">

            <div class="category-modal-body">
                <div class="category-form-group">
                    <label for="categoryName">
                        Nama Kategori <span>*</span>
                    </label>

                    <input type="text" id="categoryName" name="name"
                           value="{{ old('name') }}"
                           placeholder="Contoh: SIGN PERINGATAN"
                           autocomplete="off">

                    @error('name')
                        <small style="color:#C0392B;">{{ $message }}</small>
                    @else
                        <small>Nama kategori akan digunakan pada data produk.</small>
                    @enderror
                </div>
            </div>

            <div class="category-modal-footer">
                <button type="button" class="category-modal-button cancel"
                        id="cancelCategoryModal">Batal</button>

                <button type="submit" class="category-modal-button save"
                        id="saveCategoryButton">Simpan Kategori</button>
            </div>
        </form>
    </div>
</div>

@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ---------- ELEMENTS ---------- */

    const addCategoryButton   = document.getElementById('addCategoryButton');
    const categoryModal       = document.getElementById('categoryModal');
    const closeCategoryModal  = document.getElementById('closeCategoryModal');
    const cancelCategoryModal = document.getElementById('cancelCategoryModal');
    const categoryForm        = document.getElementById('categoryForm');
    const categoryName        = document.getElementById('categoryName');
    const categoryMethod      = document.getElementById('categoryMethod');
    const categoryEditId      = document.getElementById('categoryEditId');
    const modalTitle          = document.getElementById('categoryModalTitle');
    const modalDescription    = document.getElementById('categoryModalDescription');
    const saveButton          = document.getElementById('saveCategoryButton');
    const tableBody           = document.getElementById('categoryTableBody');
    const searchInput         = document.getElementById('categorySearch');
    const emptyState          = document.getElementById('categoryEmptyState');

    /* ---------- DATA DARI BLADE (lewat data attribute) ---------- */

    const storeUrl   = categoryModal.dataset.storeUrl;
    const hasError   = categoryModal.dataset.hasError === '1';
    const oldEditId  = categoryModal.dataset.oldEditId;
    const oldEditUrl = categoryModal.dataset.oldEditUrl;

    /* ---------- MODAL ---------- */

    function showModal() {
        categoryModal.classList.add('active');
        document.body.style.overflow = 'hidden';
        setTimeout(function () {
            categoryName.focus();
            categoryName.select();
        }, 100);
    }

    function setAddMode() {
        categoryForm.action = storeUrl;
        categoryMethod.disabled = true;          // POST biasa
        categoryEditId.value = '';
        modalTitle.textContent = 'Tambah Kategori';
        modalDescription.textContent = 'Tambahkan kategori baru untuk produk.';
        saveButton.textContent = 'Simpan Kategori';
    }

    function setEditMode(url, id) {
        categoryForm.action = url;
        categoryMethod.disabled = false;         // spoof PUT
        categoryEditId.value = id;
        modalTitle.textContent = 'Edit Kategori';
        modalDescription.textContent = 'Ubah nama kategori produk.';
        saveButton.textContent = 'Simpan Perubahan';
    }

    function closeModal() {
        categoryModal.classList.remove('active');
        document.body.style.overflow = '';
    }

    addCategoryButton.addEventListener('click', function () {
        setAddMode();
        categoryName.value = '';
        showModal();
    });

    closeCategoryModal.addEventListener('click', closeModal);
    cancelCategoryModal.addEventListener('click', closeModal);

    categoryModal.addEventListener('click', function (e) {
        if (e.target === categoryModal) closeModal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && categoryModal.classList.contains('active')) {
            closeModal();
        }
    });

    /* ---------- EDIT & DELETE ---------- */

    tableBody.addEventListener('click', function (e) {
        const editButton = e.target.closest('.category-action.edit');
        if (!editButton) return;

        const row = editButton.closest('tr');
        setEditMode(row.dataset.updateUrl, row.dataset.id);
        categoryName.value = row.dataset.categoryName;
        showModal();
    });

    tableBody.addEventListener('submit', function (e) {
        const form = e.target.closest('.category-delete-form');
        if (!form) return;

        if (!confirm('Yakin ingin menghapus kategori ' + form.dataset.name + '?')) {
            e.preventDefault();
        }
    });

    /* ---------- SEARCH ---------- */

    function filterCategories() {
        const keyword = searchInput.value.trim().toLowerCase();
        const rows = tableBody.querySelectorAll('tr');
        let visible = 0;

        rows.forEach(function (row) {
            const match = (row.dataset.categoryName || '').toLowerCase().includes(keyword);
            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        emptyState.textContent = rows.length === 0
            ? 'Belum ada kategori.'
            : 'Kategori tidak ditemukan.';

        emptyState.style.display = visible === 0 ? 'block' : 'none';
    }

    searchInput.addEventListener('input', filterCategories);
    filterCategories();

    /* ---------- BUKA ULANG MODAL JIKA VALIDASI GAGAL ---------- */

    if (hasError) {
        if (oldEditId && oldEditUrl) {
            setEditMode(oldEditUrl, oldEditId);
        } else {
            setAddMode();
        }
        showModal();
    }
});
</script>
@endpush