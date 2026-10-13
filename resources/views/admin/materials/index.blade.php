@extends('layouts.admin')

@section('title', 'Bahan Mentah')

@section('content')

<div class="material-page">

    {{-- PAGE HEADER --}}
    <div class="material-page-header">
        <div>
            <h1>Bahan Mentah</h1>
            <p>Kelola master data bahan mentah yang digunakan dalam proses produksi.</p>
        </div>

        <button
            type="button"
            class="add-material-button"
            id="addMaterialButton"
        >
            <span>+</span>
            Tambah Bahan
        </button>
    </div>


    {{-- MATERIAL CARD --}}
    <div class="material-card">

        {{-- TOOLBAR --}}
        <div class="material-toolbar">

            <div class="material-search">
                <svg
                    width="17"
                    height="17"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <circle cx="11" cy="11" r="7"/>
                    <path d="m20 20-3.5-3.5"/>
                </svg>

                <input
                    type="text"
                    id="materialSearch"
                    placeholder="Cari bahan mentah..."
                >
            </div>

            <select
                class="material-filter"
                id="materialTypeFilter"
            >
                <option value="">Semua Tipe</option>
                <option value="SHEET">SHEET</option>
                <option value="NON-SHEET">NON-SHEET</option>
            </select>

        </div>


        {{-- TABLE --}}
        <div class="material-table-wrapper">

            <table class="material-table">

                <thead>
                    <tr>
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

                    {{-- MDF --}}
                    <tr
                        data-name="MDF"
                        data-unit="lembar"
                        data-type="SHEET"
                        data-length="120"
                        data-width="240"
                        data-current="20"
                        data-safety="5"
                        data-target="30"
                        data-active="true"
                    >
                        <td class="material-name">MDF</td>

                        <td>
                            <span class="material-type sheet">
                                SHEET
                            </span>
                        </td>

                        <td>lembar</td>

                        <td>120 × 240 cm</td>

                        <td class="stock-value">20</td>
                        <td class="stock-value">5</td>
                        <td class="stock-value">30</td>

                        <td>
                            <span class="material-status active">
                                Aktif
                            </span>
                        </td>

                        <td>
                            <div class="material-actions">
                                <button
                                    type="button"
                                    class="material-action edit"
                                >
                                    Edit
                                </button>

                                <button
                                    type="button"
                                    class="material-action delete"
                                >
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>


                    {{-- HANGER --}}
                    <tr
                        data-name="Hanger"
                        data-unit="pcs"
                        data-type="NON-SHEET"
                        data-length=""
                        data-width=""
                        data-current="150"
                        data-safety="30"
                        data-target="200"
                        data-active="true"
                    >
                        <td class="material-name">Hanger</td>

                        <td>
                            <span class="material-type non-sheet">
                                NON-SHEET
                            </span>
                        </td>

                        <td>pcs</td>

                        <td class="empty-value">—</td>

                        <td class="stock-value">150</td>
                        <td class="stock-value">30</td>
                        <td class="stock-value">200</td>

                        <td>
                            <span class="material-status active">
                                Aktif
                            </span>
                        </td>

                        <td>
                            <div class="material-actions">
                                <button
                                    type="button"
                                    class="material-action edit"
                                >
                                    Edit
                                </button>

                                <button
                                    type="button"
                                    class="material-action delete"
                                >
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>


                    {{-- CAT HITAM --}}
                    <tr
                        data-name="Cat Hitam"
                        data-unit="lt"
                        data-type="NON-SHEET"
                        data-length=""
                        data-width=""
                        data-current="10"
                        data-safety="3"
                        data-target="15"
                        data-active="true"
                    >
                        <td class="material-name">Cat Hitam</td>

                        <td>
                            <span class="material-type non-sheet">
                                NON-SHEET
                            </span>
                        </td>

                        <td>lt</td>

                        <td class="empty-value">—</td>

                        <td class="stock-value">10</td>
                        <td class="stock-value">3</td>
                        <td class="stock-value">15</td>

                        <td>
                            <span class="material-status active">
                                Aktif
                            </span>
                        </td>

                        <td>
                            <div class="material-actions">
                                <button
                                    type="button"
                                    class="material-action edit"
                                >
                                    Edit
                                </button>

                                <button
                                    type="button"
                                    class="material-action delete"
                                >
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- ========================================
     MATERIAL MODAL
======================================== --}}

<div
    class="material-modal-overlay"
    id="materialModal"
>

    <div class="material-modal">

        {{-- MODAL HEADER --}}
        <div class="material-modal-header">

            <div>
                <h2 id="materialModalTitle">
                    Tambah Bahan Mentah
                </h2>

                <p>
                    Lengkapi data bahan mentah yang digunakan dalam proses produksi.
                </p>
            </div>

            <button
                type="button"
                class="material-modal-close"
                id="closeMaterialModal"
                aria-label="Tutup modal"
            >
                ×
            </button>

        </div>


        <form id="materialForm">

            {{-- MODAL BODY --}}
            <div class="material-modal-body">

                {{-- GENERAL DATA --}}
                <div class="material-form-section-title">
                    Informasi Bahan
                </div>

                <div class="material-form-grid">

                    <div class="material-form-group">
                        <label for="materialName">
                            Nama Bahan
                            <span>*</span>
                        </label>

                        <input
                            type="text"
                            id="materialName"
                            placeholder="Contoh: MDF"
                            autocomplete="off"
                            required
                        >
                    </div>


                    <div class="material-form-group">
                        <label for="materialUnit">
                            Satuan
                            <span>*</span>
                        </label>

                        <select
                            id="materialUnit"
                            required
                        >
                            <option value="">Pilih satuan</option>
                            <option value="lembar">lembar</option>
                            <option value="mm">mm</option>
                            <option value="lt">lt</option>
                            <option value="pcs">pcs</option>
                        </select>
                    </div>


                    <div class="material-form-group">
                        <label for="materialType">
                            Material Type
                            <span>*</span>
                        </label>

                        <select
                            id="materialType"
                            required
                        >
                            <option value="">Pilih tipe</option>
                            <option value="SHEET">SHEET</option>
                            <option value="NON-SHEET">NON-SHEET</option>
                        </select>
                    </div>


                    <div class="material-form-group">
                        <label for="materialActive">
                            Status
                            <span>*</span>
                        </label>

                        <select
                            id="materialActive"
                            required
                        >
                            <option value="true">Aktif</option>
                            <option value="false">Nonaktif</option>
                        </select>
                    </div>

                </div>


                {{-- SHEET DIMENSION --}}
                <div
                    class="material-sheet-fields"
                    id="materialSheetFields"
                >

                    <div class="material-form-section-title">
                        Ukuran Sheet
                    </div>

                    <div class="material-form-grid">

                        <div class="material-form-group">
                            <label for="sheetLength">
                                Panjang Sheet (cm)
                                <span>*</span>
                            </label>

                            <input
                                type="number"
                                id="sheetLength"
                                min="0"
                                step="0.01"
                                placeholder="Contoh: 120"
                            >
                        </div>


                        <div class="material-form-group">
                            <label for="sheetWidth">
                                Lebar Sheet (cm)
                                <span>*</span>
                            </label>

                            <input
                                type="number"
                                id="sheetWidth"
                                min="0"
                                step="0.01"
                                placeholder="Contoh: 240"
                            >
                        </div>

                    </div>

                </div>


                {{-- STOCK --}}
                <div class="material-form-section-title">
                    Informasi Stok
                </div>

                <div class="material-form-grid stock-grid">

                    <div class="material-form-group">
                        <label for="currentStock">
                            Current Stock
                            <span>*</span>
                        </label>

                        <input
                            type="number"
                            id="currentStock"
                            min="0"
                            step="0.01"
                            value="0"
                            required
                        >
                    </div>


                    <div class="material-form-group">
                        <label for="safetyStock">
                            Safety Stock
                            <span>*</span>
                        </label>

                        <input
                            type="number"
                            id="safetyStock"
                            min="0"
                            step="0.01"
                            value="0"
                            required
                        >
                    </div>


                    <div class="material-form-group">
                        <label for="targetStock">
                            Target Stock
                            <span>*</span>
                        </label>

                        <input
                            type="number"
                            id="targetStock"
                            min="0"
                            step="0.01"
                            value="0"
                            required
                        >
                    </div>

                </div>

            </div>


            {{-- MODAL FOOTER --}}
            <div class="material-modal-footer">

                <button
                    type="button"
                    class="material-modal-button cancel"
                    id="cancelMaterialModal"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="material-modal-button save"
                    id="saveMaterialButton"
                >
                    Simpan Bahan
                </button>

            </div>

        </form>

    </div>

</div>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('materialModal');

    const addButton = document.getElementById('addMaterialButton');
    const closeButton = document.getElementById('closeMaterialModal');
    const cancelButton = document.getElementById('cancelMaterialModal');

    const form = document.getElementById('materialForm');

    const modalTitle = document.getElementById('materialModalTitle');
    const saveButton = document.getElementById('saveMaterialButton');

    const nameInput = document.getElementById('materialName');
    const unitInput = document.getElementById('materialUnit');
    const typeInput = document.getElementById('materialType');
    const activeInput = document.getElementById('materialActive');

    const sheetFields = document.getElementById('materialSheetFields');
    const sheetLengthInput = document.getElementById('sheetLength');
    const sheetWidthInput = document.getElementById('sheetWidth');

    const currentStockInput = document.getElementById('currentStock');
    const safetyStockInput = document.getElementById('safetyStock');
    const targetStockInput = document.getElementById('targetStock');

    const tableBody = document.getElementById('materialTableBody');

    const searchInput = document.getElementById('materialSearch');
    const typeFilter = document.getElementById('materialTypeFilter');

    let editingRow = null;


    /* ========================================
       SHEET / NON-SHEET
    ======================================== */

    function updateSheetFields() {

        const isSheet = typeInput.value === 'SHEET';

        sheetFields.style.display = isSheet
            ? 'block'
            : 'none';

        sheetLengthInput.required = isSheet;
        sheetWidthInput.required = isSheet;

        if (!isSheet) {
            sheetLengthInput.value = '';
            sheetWidthInput.value = '';
        }

    }


    typeInput.addEventListener('change', updateSheetFields);


    /* ========================================
       MODAL
    ======================================== */

    function openModal() {

        modal.classList.add('active');

        document.body.style.overflow = 'hidden';

        setTimeout(function () {
            nameInput.focus();
        }, 100);

    }


    function closeModal() {

        modal.classList.remove('active');

        document.body.style.overflow = '';

        editingRow = null;

    }


    function resetForm() {

        form.reset();

        currentStockInput.value = 0;
        safetyStockInput.value = 0;
        targetStockInput.value = 0;

        activeInput.value = 'true';

        sheetFields.style.display = 'none';

        sheetLengthInput.required = false;
        sheetWidthInput.required = false;

        editingRow = null;

    }


    /* ========================================
       ADD
    ======================================== */

    addButton.addEventListener('click', function () {

        resetForm();

        modalTitle.textContent = 'Tambah Bahan Mentah';
        saveButton.textContent = 'Simpan Bahan';

        openModal();

    });


    closeButton.addEventListener('click', closeModal);
    cancelButton.addEventListener('click', closeModal);


    modal.addEventListener('click', function (event) {

        if (event.target === modal) {
            closeModal();
        }

    });


    document.addEventListener('keydown', function (event) {

        if (
            event.key === 'Escape' &&
            modal.classList.contains('active')
        ) {
            closeModal();
        }

    });


    /* ========================================
       CREATE TABLE ROW
    ======================================== */

    function createMaterialRow(data) {

        const row = document.createElement('tr');

        applyMaterialData(row, data);

        return row;

    }


    /* ========================================
       APPLY DATA TO ROW
    ======================================== */

    function applyMaterialData(row, data) {

        row.dataset.name = data.name;
        row.dataset.unit = data.unit;
        row.dataset.type = data.type;
        row.dataset.length = data.length;
        row.dataset.width = data.width;
        row.dataset.current = data.current;
        row.dataset.safety = data.safety;
        row.dataset.target = data.target;
        row.dataset.active = data.active;


        const typeClass =
            data.type === 'SHEET'
                ? 'sheet'
                : 'non-sheet';


        const sheetSize =
            data.type === 'SHEET'
                ? `${data.length} × ${data.width} cm`
                : '—';


        const statusClass =
            data.active === 'true'
                ? 'active'
                : 'inactive';


        const statusText =
            data.active === 'true'
                ? 'Aktif'
                : 'Nonaktif';


        row.innerHTML = `
            <td class="material-name">
                ${escapeHtml(data.name)}
            </td>

            <td>
                <span class="material-type ${typeClass}">
                    ${escapeHtml(data.type)}
                </span>
            </td>

            <td>
                ${escapeHtml(data.unit)}
            </td>

            <td class="${data.type === 'SHEET' ? '' : 'empty-value'}">
                ${escapeHtml(sheetSize)}
            </td>

            <td class="stock-value">
                ${escapeHtml(data.current)}
            </td>

            <td class="stock-value">
                ${escapeHtml(data.safety)}
            </td>

            <td class="stock-value">
                ${escapeHtml(data.target)}
            </td>

            <td>
                <span class="material-status ${statusClass}">
                    ${statusText}
                </span>
            </td>

            <td>
                <div class="material-actions">

                    <button
                        type="button"
                        class="material-action edit"
                    >
                        Edit
                    </button>

                    <button
                        type="button"
                        class="material-action delete"
                    >
                        Hapus
                    </button>

                </div>
            </td>
        `;

    }


    /* ========================================
       SUBMIT ADD / EDIT
    ======================================== */

    form.addEventListener('submit', function (event) {

        event.preventDefault();


        if (
            typeInput.value === 'SHEET' &&
            (
                !sheetLengthInput.value ||
                !sheetWidthInput.value
            )
        ) {

            alert(
                'Panjang dan lebar sheet wajib diisi untuk material SHEET.'
            );

            return;

        }


        const data = {

            name: nameInput.value.trim(),

            unit: unitInput.value,

            type: typeInput.value,

            length:
                typeInput.value === 'SHEET'
                    ? sheetLengthInput.value
                    : '',

            width:
                typeInput.value === 'SHEET'
                    ? sheetWidthInput.value
                    : '',

            current: currentStockInput.value,

            safety: safetyStockInput.value,

            target: targetStockInput.value,

            active: activeInput.value

        };


        if (!data.name || !data.unit || !data.type) {
            return;
        }


        if (editingRow) {

            applyMaterialData(
                editingRow,
                data
            );

        } else {

            const newRow =
                createMaterialRow(data);

            tableBody.appendChild(newRow);

        }


        closeModal();

        filterMaterials();

    });


    /* ========================================
       EDIT + DELETE
    ======================================== */

    tableBody.addEventListener('click', function (event) {

        const editButton =
            event.target.closest('.material-action.edit');

        const deleteButton =
            event.target.closest('.material-action.delete');


        /* EDIT */

        if (editButton) {

            const row =
                editButton.closest('tr');

            if (!row) return;


            editingRow = row;


            modalTitle.textContent =
                'Edit Bahan Mentah';

            saveButton.textContent =
                'Simpan Perubahan';


            nameInput.value =
                row.dataset.name || '';

            unitInput.value =
                row.dataset.unit || '';

            typeInput.value =
                row.dataset.type || '';

            activeInput.value =
                row.dataset.active || 'true';

            currentStockInput.value =
                row.dataset.current || 0;

            safetyStockInput.value =
                row.dataset.safety || 0;

            targetStockInput.value =
                row.dataset.target || 0;


            updateSheetFields();


            if (row.dataset.type === 'SHEET') {

                sheetLengthInput.value =
                    row.dataset.length || '';

                sheetWidthInput.value =
                    row.dataset.width || '';

            }


            openModal();

            return;

        }


        /* DELETE */

        if (deleteButton) {

            const row =
                deleteButton.closest('tr');

            if (!row) return;


            const materialName =
                row.dataset.name || 'bahan ini';


            const confirmed =
                confirm(
                    `Yakin ingin menghapus ${materialName}?`
                );


            if (!confirmed) return;


            row.remove();

        }

    });


    /* ========================================
       SEARCH + FILTER
    ======================================== */

    function filterMaterials() {

        const keyword =
            searchInput.value
                .trim()
                .toLowerCase();


        const selectedType =
            typeFilter.value;


        const rows =
            tableBody.querySelectorAll('tr');


        rows.forEach(function (row) {

            const materialName =
                (row.dataset.name || '')
                    .toLowerCase();


            const materialType =
                row.dataset.type || '';


            const matchesSearch =
                materialName.includes(keyword);


            const matchesType =
                !selectedType ||
                materialType === selectedType;


            row.style.display =
                matchesSearch && matchesType
                    ? ''
                    : 'none';

        });

    }


    searchInput.addEventListener(
        'input',
        filterMaterials
    );


    typeFilter.addEventListener(
        'change',
        filterMaterials
    );


    /* ========================================
       ESCAPE HTML
    ======================================== */

    function escapeHtml(value) {

        const div =
            document.createElement('div');

        div.textContent =
            value ?? '';

        return div.innerHTML;

    }

});
</script>

@endpush