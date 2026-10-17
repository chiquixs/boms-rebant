@extends('layouts.admin')

@section('title', 'Bahan Mentah')

@section('content')

@php
    // 20.000 -> 20, 2.500 -> 2.5
    $fmt = fn ($v) => rtrim(rtrim(number_format((float) $v, 3, '.', ''), '0'), '.');
    $fmtSize = fn ($v) => rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.');
@endphp

<div class="material-page">

    {{-- PAGE HEADER --}}
    <div class="material-page-header">
        <div>
            <h1>Bahan Mentah</h1>
            <p>Kelola master data bahan mentah yang digunakan dalam proses produksi.</p>
        </div>

        <button type="button" class="add-material-button" id="addMaterialButton">
            <span>+</span>
            Tambah Bahan
        </button>
    </div>

    {{-- MATERIAL CARD --}}
    <div class="material-card">

        <div class="material-toolbar">
            <div class="material-search">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.8">
                    <circle cx="11" cy="11" r="7"/>
                    <path d="m20 20-3.5-3.5"/>
                </svg>

                <input type="text" id="materialSearch" placeholder="Cari kode atau nama bahan...">
            </div>

            <select class="material-filter" id="materialTypeFilter">
                <option value="">Semua Tipe</option>
                <option value="SHEET">SHEET</option>
                <option value="NON_SHEET">NON-SHEET</option>
            </select>
        </div>

        <div class="material-table-wrapper">
            <table class="material-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Bahan</th>
                        <th>Tipe</th>
                        <th>Satuan</th>
                        <th>Ukuran Sheet</th>
                        <th class="stock-column">Current Stock</th>
                        <th class="stock-column">Safety Stock</th>
                        <th class="stock-column">Target Stock</th>
                        <th>Status</th>
                        <th class="action-column">Aksi</th>
                    </tr>
                </thead>

                <tbody id="materialTableBody">
                    @foreach ($materials as $material)
                        <tr data-id="{{ $material->id }}"
                            data-update-url="{{ route('admin.materials.update', $material) }}"
                            data-code="{{ $material->code }}"
                            data-name="{{ $material->name }}"
                            data-unit="{{ $material->unit }}"
                            data-type="{{ $material->material_type }}"
                            data-length="{{ $material->sheet_length_cm !== null ? $fmtSize($material->sheet_length_cm) : '' }}"
                            data-width="{{ $material->sheet_width_cm !== null ? $fmtSize($material->sheet_width_cm) : '' }}"
                            data-current="{{ $fmt($material->current_stock) }}"
                            data-safety="{{ $fmt($material->safety_stock) }}"
                            data-target="{{ $fmt($material->target_stock) }}"
                            data-active="{{ $material->is_active ? '1' : '0' }}">

                            <td class="material-code">{{ $material->code }}</td>

                            <td class="material-name">{{ $material->name }}</td>

                            <td>
                                <span class="material-type {{ $material->isSheet() ? 'sheet' : 'non-sheet' }}">
                                    {{ $material->isSheet() ? 'SHEET' : 'NON-SHEET' }}
                                </span>
                            </td>

                            <td>{{ $material->unit }}</td>

                            @if ($material->isSheet())
                                <td>{{ $fmtSize($material->sheet_length_cm) }} × {{ $fmtSize($material->sheet_width_cm) }} cm</td>
                            @else
                                <td class="empty-value">—</td>
                            @endif

                            <td class="stock-value">{{ $fmt($material->current_stock) }}</td>
                            <td class="stock-value">{{ $fmt($material->safety_stock) }}</td>
                            <td class="stock-value">{{ $fmt($material->target_stock) }}</td>

                            <td>
                                <span class="material-status {{ $material->is_active ? 'active' : 'inactive' }}">
                                    {{ $material->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>

                            <td>
                                <div class="material-actions">
                                    <button type="button" class="material-action edit">Edit</button>

                                    <form action="{{ route('admin.materials.destroy', $material) }}"
                                          method="POST"
                                          class="material-delete-form"
                                          data-name="{{ $material->name }}"
                                          style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="material-action delete">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="material-empty-state" id="materialEmptyState" style="display:none;">
                Bahan mentah tidak ditemukan.
            </div>
        </div>
    </div>
</div>


{{-- TOAST --}}
<div class="toast-container" id="toastContainer"
     data-success="{{ session('success') }}"
     data-error="{{ session('error') }}"></div>


{{-- ========================================
     MATERIAL MODAL
======================================== --}}
<div class="material-modal-overlay" id="materialModal"
     data-store-url="{{ route('admin.materials.store') }}"
     data-has-error="{{ $errors->any() ? '1' : '0' }}"
     data-old-edit-id="{{ old('edit_id') }}"
     data-old-edit-url="{{ old('edit_id') ? route('admin.materials.update', old('edit_id')) : '' }}">

    <div class="material-modal">

        <div class="material-modal-header">
            <div>
                <h2 id="materialModalTitle">Tambah Bahan Mentah</h2>
                <p>Lengkapi data bahan mentah yang digunakan dalam proses produksi.</p>
            </div>

            <button type="button" class="material-modal-close"
                    id="closeMaterialModal" aria-label="Tutup modal">×</button>
        </div>

        <form id="materialForm" method="POST" action="{{ route('admin.materials.store') }}">
            @csrf
            <input type="hidden" name="_method" id="materialMethod" value="PUT" disabled>
            <input type="hidden" name="edit_id" id="materialEditId" value="{{ old('edit_id') }}">

            <div class="material-modal-body">

                <div class="material-form-section-title">Informasi Bahan</div>

                <div class="material-form-grid">

                    <div class="material-form-group">
                        <label for="materialName">Nama Bahan <span>*</span></label>
                        <input type="text" id="materialName" name="name"
                               value="{{ old('name') }}"
                               placeholder="Contoh: MDF" autocomplete="off" required>
                        @error('name')<small class="material-field-error">{{ $message }}</small>@enderror
                    </div>

                    <div class="material-form-group">
                        <label for="materialUnit">Satuan <span>*</span></label>
                        <select id="materialUnit" name="unit" required>
                            <option value="">Pilih satuan</option>
                            @foreach (['lembar', 'mm', 'lt', 'pcs'] as $unit)
                                <option value="{{ $unit }}" @selected(old('unit') === $unit)>{{ $unit }}</option>
                            @endforeach
                        </select>
                        @error('unit')<small class="material-field-error">{{ $message }}</small>@enderror
                    </div>

                    <div class="material-form-group">
                        <label for="materialType">Material Type <span>*</span></label>
                        <select id="materialType" name="material_type" required>
                            <option value="">Pilih tipe</option>
                            <option value="SHEET" @selected(old('material_type') === 'SHEET')>SHEET</option>
                            <option value="NON_SHEET" @selected(old('material_type') === 'NON_SHEET')>NON-SHEET</option>
                        </select>
                        @error('material_type')<small class="material-field-error">{{ $message }}</small>@enderror
                    </div>

                    <div class="material-form-group">
                        <label for="materialActive">Status <span>*</span></label>
                        <select id="materialActive" name="is_active" required>
                            <option value="1" @selected(old('is_active', '1') === '1')>Aktif</option>
                            <option value="0" @selected(old('is_active') === '0')>Nonaktif</option>
                        </select>
                        @error('is_active')<small class="material-field-error">{{ $message }}</small>@enderror
                    </div>

                </div>

                {{-- SHEET DIMENSION --}}
                <div class="material-sheet-fields" id="materialSheetFields">

                    <div class="material-form-section-title">Ukuran Sheet</div>

                    <div class="material-form-grid">

                        <div class="material-form-group">
                            <label for="sheetLength">Panjang Sheet (cm) <span>*</span></label>
                            <input type="number" id="sheetLength" name="sheet_length_cm"
                                   value="{{ old('sheet_length_cm') }}"
                                   min="0" step="0.01" placeholder="Contoh: 120">
                            @error('sheet_length_cm')<small class="material-field-error">{{ $message }}</small>@enderror
                        </div>

                        <div class="material-form-group">
                            <label for="sheetWidth">Lebar Sheet (cm) <span>*</span></label>
                            <input type="number" id="sheetWidth" name="sheet_width_cm"
                                   value="{{ old('sheet_width_cm') }}"
                                   min="0" step="0.01" placeholder="Contoh: 240">
                            @error('sheet_width_cm')<small class="material-field-error">{{ $message }}</small>@enderror
                        </div>

                    </div>
                </div>

                {{-- STOCK --}}
                <div class="material-form-section-title">Informasi Stok</div>

                <div class="material-form-grid stock-grid">

                    <div class="material-form-group">
                        <label for="currentStock">Current Stock <span>*</span></label>
                        <input type="number" id="currentStock" name="current_stock"
                               value="{{ old('current_stock', 0) }}"
                               min="0" step="0.001" required>
                        @error('current_stock')<small class="material-field-error">{{ $message }}</small>@enderror
                    </div>

                    <div class="material-form-group">
                        <label for="safetyStock">Safety Stock <span>*</span></label>
                        <input type="number" id="safetyStock" name="safety_stock"
                               value="{{ old('safety_stock', 0) }}"
                               min="0" step="0.001" required>
                        @error('safety_stock')<small class="material-field-error">{{ $message }}</small>@enderror
                    </div>

                    <div class="material-form-group">
                        <label for="targetStock">Target Stock <span>*</span></label>
                        <input type="number" id="targetStock" name="target_stock"
                               value="{{ old('target_stock', 0) }}"
                               min="0" step="0.001" required>
                        @error('target_stock')<small class="material-field-error">{{ $message }}</small>@enderror
                    </div>

                </div>

            </div>

            <div class="material-modal-footer">
                <button type="button" class="material-modal-button cancel"
                        id="cancelMaterialModal">Batal</button>

                <button type="submit" class="material-modal-button save"
                        id="saveMaterialButton">Simpan Bahan</button>
            </div>
        </form>
    </div>
</div>

@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ---------- ELEMENTS ---------- */

    const modal        = document.getElementById('materialModal');
    const addButton    = document.getElementById('addMaterialButton');
    const closeButton  = document.getElementById('closeMaterialModal');
    const cancelButton = document.getElementById('cancelMaterialModal');
    const form         = document.getElementById('materialForm');
    const methodInput  = document.getElementById('materialMethod');
    const editIdInput  = document.getElementById('materialEditId');
    const modalTitle   = document.getElementById('materialModalTitle');
    const saveButton   = document.getElementById('saveMaterialButton');

    const nameInput    = document.getElementById('materialName');
    const unitInput    = document.getElementById('materialUnit');
    const typeInput    = document.getElementById('materialType');
    const activeInput  = document.getElementById('materialActive');

    const sheetFields      = document.getElementById('materialSheetFields');
    const sheetLengthInput = document.getElementById('sheetLength');
    const sheetWidthInput  = document.getElementById('sheetWidth');

    const currentStockInput = document.getElementById('currentStock');
    const safetyStockInput  = document.getElementById('safetyStock');
    const targetStockInput  = document.getElementById('targetStock');

    const tableBody  = document.getElementById('materialTableBody');
    const searchInput = document.getElementById('materialSearch');
    const typeFilter  = document.getElementById('materialTypeFilter');
    const emptyState  = document.getElementById('materialEmptyState');

    /* ---------- DATA DARI BLADE ---------- */

    const storeUrl   = modal.dataset.storeUrl;
    const hasError   = modal.dataset.hasError === '1';
    const oldEditId  = modal.dataset.oldEditId;
    const oldEditUrl = modal.dataset.oldEditUrl;

    /* ---------- SHEET / NON-SHEET ---------- */

    function updateSheetFields() {
        const isSheet = typeInput.value === 'SHEET';

        sheetFields.style.display = isSheet ? 'block' : 'none';
        sheetLengthInput.required = isSheet;
        sheetWidthInput.required  = isSheet;

        if (!isSheet) {
            sheetLengthInput.value = '';
            sheetWidthInput.value  = '';
        }
    }

    typeInput.addEventListener('change', updateSheetFields);

    /* ---------- MODAL ---------- */

    function clearErrors() {
        modal.querySelectorAll('.material-field-error').forEach(function (el) {
            el.remove();
        });
    }

    function showModal() {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        setTimeout(function () { nameInput.focus(); }, 100);
    }

    function closeModal() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    function setAddMode() {
        form.action = storeUrl;
        methodInput.disabled = true;     // POST biasa
        editIdInput.value = '';
        modalTitle.textContent = 'Tambah Bahan Mentah';
        saveButton.textContent = 'Simpan Bahan';
    }

    function setEditMode(url, id) {
        form.action = url;
        methodInput.disabled = false;    // spoof PUT
        editIdInput.value = id;
        modalTitle.textContent = 'Edit Bahan Mentah';
        saveButton.textContent = 'Simpan Perubahan';
    }

    function fillForm(values) {
        nameInput.value         = values.name;
        unitInput.value         = values.unit;
        typeInput.value         = values.type;
        activeInput.value       = values.active;
        currentStockInput.value = values.current;
        safetyStockInput.value  = values.safety;
        targetStockInput.value  = values.target;

        updateSheetFields();

        if (values.type === 'SHEET') {
            sheetLengthInput.value = values.length;
            sheetWidthInput.value  = values.width;
        }
    }

    addButton.addEventListener('click', function () {
        clearErrors();
        setAddMode();
        fillForm({
            name: '', unit: '', type: '', active: '1',
            current: 0, safety: 0, target: 0, length: '', width: ''
        });
        showModal();
    });

    closeButton.addEventListener('click', closeModal);
    cancelButton.addEventListener('click', closeModal);

    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('active')) closeModal();
    });

    /* ---------- EDIT ---------- */

    tableBody.addEventListener('click', function (e) {
        const editButton = e.target.closest('.material-action.edit');
        if (!editButton) return;

        const row = editButton.closest('tr');
        if (!row) return;

        clearErrors();
        setEditMode(row.dataset.updateUrl, row.dataset.id);
        fillForm({
            name:    row.dataset.name    || '',
            unit:    row.dataset.unit    || '',
            type:    row.dataset.type    || '',
            active:  row.dataset.active  || '1',
            current: row.dataset.current || 0,
            safety:  row.dataset.safety  || 0,
            target:  row.dataset.target  || 0,
            length:  row.dataset.length  || '',
            width:   row.dataset.width   || ''
        });
        showModal();
    });

    /* ---------- DELETE (konfirmasi) ---------- */

    tableBody.addEventListener('submit', function (e) {
        const deleteForm = e.target.closest('.material-delete-form');
        if (!deleteForm) return;

        if (!confirm('Yakin ingin menghapus ' + deleteForm.dataset.name + '?')) {
            e.preventDefault();
        }
    });

    /* ---------- SEARCH + FILTER ---------- */

    function filterMaterials() {
        const keyword      = searchInput.value.trim().toLowerCase();
        const selectedType = typeFilter.value;
        const rows         = tableBody.querySelectorAll('tr');
        let visible = 0;

        rows.forEach(function (row) {
            const haystack = ((row.dataset.name || '') + ' ' + (row.dataset.code || '')).toLowerCase();

            const matchesSearch = haystack.includes(keyword);
            const matchesType   = !selectedType || row.dataset.type === selectedType;
            const show          = matchesSearch && matchesType;

            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        emptyState.textContent = rows.length === 0
            ? 'Belum ada bahan mentah.'
            : 'Bahan mentah tidak ditemukan.';

        emptyState.style.display = visible === 0 ? 'block' : 'none';
    }

    searchInput.addEventListener('input', filterMaterials);
    typeFilter.addEventListener('change', filterMaterials);
    filterMaterials();

    /* ---------- TOAST ---------- */

    const toastContainer = document.getElementById('toastContainer');
    const TOAST_DURATION = 4000;

    function showToast(type, message) {
        if (!message) return;

        const toast = document.createElement('div');
        toast.className = 'toast ' + type;

        const icon = type === 'success'
            ? '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg>'
            : '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M18 6 6 18M6 6l12 12"/></svg>';

        toast.innerHTML =
            '<div class="toast-icon">' + icon + '</div>' +
            '<div class="toast-body">' +
                '<div class="toast-title">' + (type === 'success' ? 'Berhasil' : 'Gagal') + '</div>' +
                '<div class="toast-message"></div>' +
            '</div>' +
            '<button type="button" class="toast-close" aria-label="Tutup">×</button>' +
            '<div class="toast-progress"></div>';

        toast.querySelector('.toast-message').textContent = message;
        toast.querySelector('.toast-progress').style.animationDuration = TOAST_DURATION + 'ms';

        toastContainer.appendChild(toast);

        let timer;

        function dismiss() {
            clearTimeout(timer);
            toast.classList.add('hide');
            toast.addEventListener('animationend', function () {
                toast.remove();
            }, { once: true });
        }

        function startTimer(ms) {
            timer = setTimeout(dismiss, ms);
        }

        startTimer(TOAST_DURATION);

        toast.addEventListener('mouseenter', function () { clearTimeout(timer); });
        toast.addEventListener('mouseleave', function () { startTimer(1500); });
        toast.querySelector('.toast-close').addEventListener('click', dismiss);
    }

    showToast('success', toastContainer.dataset.success);
    showToast('error', toastContainer.dataset.error);

    /* ---------- BUKA ULANG MODAL JIKA VALIDASI GAGAL ---------- */

    if (hasError) {
        // Nilai input sudah diisi ulang oleh old() dari server, jadi jangan di-reset
        if (oldEditId && oldEditUrl) {
            setEditMode(oldEditUrl, oldEditId);
        } else {
            setAddMode();
        }
        updateSheetFields();
        showModal();
    }
});
</script>
@endpush