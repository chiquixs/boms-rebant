@extends('layouts.admin')

@section('title', 'Bill of Materials - REBANT BOMS')

@section('content')

<div class="bom-page">

    {{-- =========================================================
        PAGE HEADER
    ========================================================= --}}
    <div class="bom-page-header">

        <a href="/admin/products" class="back-to-products">
            ← Kembali ke Master Produk
        </a>

        <div class="bom-header-content">

            <div>
                <span class="bom-eyebrow">
                    BILL OF MATERIALS
                </span>

                <h1>Resep Produksi</h1>

                <p>
                    Kelola kebutuhan bahan baku untuk setiap unit produk.
                </p>
            </div>

        </div>

    </div>


    {{-- =========================================================
        PRODUCT INFORMATION
    ========================================================= --}}
    <div class="bom-product-card">

        <div class="bom-product-info">

            <img
                src="{{ asset('images/login-illustration.png') }}"
                alt="Sign Peringatan Hitam"
                class="bom-product-image"
            >

            <div class="bom-product-main">

                <span class="bom-product-label">
                    PRODUCT
                </span>

                <h2>
                    Sign Peringatan Hitam (9x30)
                </h2>

                <div class="bom-product-meta">

                    <span class="bom-sku">
                        {{ $sku }}
                    </span>

                    <span class="production-badge mts">
                        MTS
                    </span>

                </div>

            </div>

        </div>


        <div class="bom-product-summary">

            <div class="bom-summary-item">
                <span>Kategori</span>
                <strong>Sign Peringatan</strong>
            </div>

            <div class="bom-summary-item">
                <span>Ukuran</span>
                <strong>9 × 30 cm</strong>
            </div>

            <div class="bom-summary-item">
                <span>Jumlah Varian</span>
                <strong>5 Varian</strong>
            </div>

        </div>

    </div>


    {{-- =========================================================
        MATERIAL TABLE
    ========================================================= --}}
    <div class="bom-card">

        <div class="bom-card-header">

            <div>
                <h2>Daftar Bahan Baku</h2>

                <p>
                    Kebutuhan bahan untuk memproduksi 1 unit produk.
                </p>
            </div>

            <button
                type="button"
                class="add-material-button"
                id="addMaterialButton"
            >
                + Tambah Bahan
            </button>

        </div>


        <div class="bom-table-wrapper">

            <table class="bom-table">

                <thead>
                    <tr>
                        <th>Bahan Baku</th>
                        <th>Metode</th>
                        <th>Kebutuhan</th>
                        <th>Satuan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody id="bomMaterialTableBody">

                    {{-- =========================
                        DUMMY MATERIAL - FIX
                    ========================= --}}
                    <tr>

                        <td>
                            <div class="material-info">
                                <strong>Hanger</strong>
                                <span>Aksesoris produk</span>
                            </div>
                        </td>

                        <td>
                            <span class="bom-method-badge fix">
                                Fix
                            </span>
                        </td>

                        <td>
                            <div class="fix-requirement">
                                <strong>2</strong>
                            </div>
                        </td>

                        <td>
                            <span class="material-unit">
                                pcs
                            </span>
                        </td>

                        <td>
                            <div class="bom-actions">

                                <button
                                    type="button"
                                    class="bom-action edit"
                                >
                                    Edit
                                </button>

                                <button
                                    type="button"
                                    class="bom-action delete"
                                >
                                    Hapus
                                </button>

                            </div>
                        </td>

                    </tr>


                    {{-- =========================
                        DUMMY MATERIAL - AREA
                    ========================= --}}
                    <tr>

                        <td>
                            <div class="material-info">
                                <strong>MDF</strong>
                                <span>Material utama</span>
                            </div>
                        </td>

                        <td>
                            <span class="bom-method-badge area">
                                Area
                            </span>
                        </td>

                        <td>
                            <div class="area-requirement">

                                <strong>
                                    30 × 9 cm
                                </strong>

                                <span>
                                    2 potongan • Waste 10%
                                </span>

                            </div>
                        </td>

                        <td>
                            <span class="material-unit">
                                lembar
                            </span>
                        </td>

                        <td>
                            <div class="bom-actions">

                                <button
                                    type="button"
                                    class="bom-action edit"
                                >
                                    Edit
                                </button>

                                <button
                                    type="button"
                                    class="bom-action delete"
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


    {{-- =========================================================
        PRODUCTION NOTES
    ========================================================= --}}
    <div class="bom-card">

        <div class="bom-card-header">

            <div>
                <h2>Catatan Produksi</h2>

                <p>
                    Informasi tambahan untuk proses produksi produk.
                </p>
            </div>

        </div>

        <div class="production-note">

            MDF dipotong sesuai ukuran produk,
            kemudian dilakukan proses finishing
            dan pemasangan desain sesuai varian pesanan.

        </div>

    </div>

</div>


{{-- =========================================================
    ADD MATERIAL MODAL
========================================================= --}}
<div
    class="material-modal-overlay"
    id="materialModal"
>

    <div class="material-modal">

        {{-- =========================
            MODAL HEADER
        ========================= --}}
        <div class="material-modal-header">

            <div>

                <span class="material-modal-eyebrow">
                    BOM MATERIAL
                </span>

                <h2 id="materialModalTitle">
                    Tambah Bahan Baku
                </h2>

                <p>
                    Tambahkan kebutuhan bahan untuk 1 unit produk.
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


        {{-- =========================
            FORM
        ========================= --}}
        <form id="materialForm">

            <div class="material-modal-body">

                {{-- =========================
                    RAW MATERIAL
                ========================= --}}
                <div class="material-form-group">

                    <label for="materialName">
                        Bahan Mentah
                    </label>

                    <select id="materialName">

                        <option value="">
                            Pilih bahan mentah
                        </option>

                        <option value="MDF">
                            MDF
                        </option>

                        <option value="Hanger">
                            Hanger
                        </option>

                        <option value="Cat Hitam">
                            Cat Hitam
                        </option>

                        <option value="Lem Kayu">
                            Lem Kayu
                        </option>

                    </select>

                    <span class="material-field-helper">
                        Data bahan nantinya diambil dari Master Bahan Mentah.
                    </span>

                </div>


                {{-- =========================
                    UNIT PREVIEW
                ========================= --}}
                <div class="material-form-group">

                    <label>
                        Satuan
                    </label>

                    <div
                        class="material-unit-preview"
                        id="materialUnitPreview"
                    >
                        Pilih bahan terlebih dahulu
                    </div>

                    <span class="material-field-helper">
                        Satuan mengikuti data dari Master Bahan Mentah.
                    </span>

                </div>


                {{-- =========================
                    METHOD
                ========================= --}}
                <div class="material-form-group">

                    <label>
                        Metode Kebutuhan
                    </label>

                    <div class="material-method-options">

                        <label class="material-method-option">

                            <input
                                type="radio"
                                name="material_method"
                                value="fix"
                                checked
                            >

                            <span class="material-method-card">

                                <strong>
                                    Fix
                                </strong>

                                <small>
                                    Jumlah bahan tetap
                                </small>

                            </span>

                        </label>


                        <label class="material-method-option">

                            <input
                                type="radio"
                                name="material_method"
                                value="area"
                            >

                            <span class="material-method-card">

                                <strong>
                                    Area
                                </strong>

                                <small>
                                    Berdasarkan ukuran potongan
                                </small>

                            </span>

                        </label>

                    </div>

                </div>


                {{-- =========================
                    FIX
                ========================= --}}
                <div
                    class="material-method-fields"
                    id="fixFields"
                >

                    <div class="material-form-group">

                        <label for="materialQuantity">
                            Quantity
                        </label>

                        <input
                            type="number"
                            id="materialQuantity"
                            min="0"
                            step="0.01"
                            placeholder="Contoh: 2"
                        >

                        <span class="material-field-helper">
                            Jumlah bahan yang dibutuhkan untuk 1 unit produk.
                        </span>

                    </div>

                </div>


                {{-- =========================
                    AREA
                ========================= --}}
                <div
                    class="material-method-fields hidden"
                    id="areaFields"
                >

                    <div class="material-form-grid">

                        <div class="material-form-group">

                            <label for="pieceLength">
                                Panjang Potongan (cm)
                            </label>

                            <input
                                type="number"
                                id="pieceLength"
                                min="0"
                                step="0.01"
                                placeholder="Contoh: 30"
                            >

                        </div>


                        <div class="material-form-group">

                            <label for="pieceWidth">
                                Lebar Potongan (cm)
                            </label>

                            <input
                                type="number"
                                id="pieceWidth"
                                min="0"
                                step="0.01"
                                placeholder="Contoh: 9"
                            >

                        </div>


                        <div class="material-form-group">

                            <label for="pieceCount">
                                Jumlah Potongan
                            </label>

                            <input
                                type="number"
                                id="pieceCount"
                                min="1"
                                step="1"
                                placeholder="Contoh: 2"
                            >

                        </div>


                        <div class="material-form-group">

                            <label for="wastePercent">
                                Waste / Toleransi (%)
                            </label>

                            <input
                                type="number"
                                id="wastePercent"
                                min="0"
                                max="100"
                                step="0.01"
                                placeholder="Contoh: 10"
                            >

                        </div>

                    </div>


                    <div class="area-information">

                        <strong>
                            Metode Area
                        </strong>

                        <span>
                            Kebutuhan bahan dihitung berdasarkan ukuran
                            potongan, jumlah potongan, dan waste produksi.
                        </span>

                    </div>

                </div>


                {{-- =========================
                    DESCRIPTION
                ========================= --}}
                <div class="material-form-group">

                    <label for="materialDescription">
                        Keterangan
                    </label>

                    <textarea
                        id="materialDescription"
                        rows="3"
                        placeholder="Contoh: Material utama produk"
                    ></textarea>

                </div>

            </div>


            {{-- =========================
                FOOTER
            ========================= --}}
            <div class="material-modal-footer">

                <button
                    type="button"
                    class="material-cancel-button"
                    id="cancelMaterialModal"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="material-save-button"
                >
                    Simpan Bahan
                </button>

            </div>

        </form>

    </div>

</div>

@endsection


```blade
@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    /* =========================================================
       DUMMY MASTER BAHAN MENTAH
    ========================================================= */

    const materialMaster = {

        'MDF': {
            unit: 'lembar'
        },

        'Hanger': {
            unit: 'pcs'
        },

        'Cat Hitam': {
            unit: 'ml'
        },

        'Lem Kayu': {
            unit: 'ml'
        }

    };


    /* =========================================================
       ELEMENTS
    ========================================================= */

    const materialModal =
        document.getElementById('materialModal');

    const addMaterialButton =
        document.getElementById('addMaterialButton');

    const closeMaterialModal =
        document.getElementById('closeMaterialModal');

    const cancelMaterialModal =
        document.getElementById('cancelMaterialModal');

    const materialForm =
        document.getElementById('materialForm');

    const materialName =
        document.getElementById('materialName');

    const materialUnitPreview =
        document.getElementById('materialUnitPreview');

    const materialQuantity =
        document.getElementById('materialQuantity');

    const materialDescription =
        document.getElementById('materialDescription');

    const fixFields =
        document.getElementById('fixFields');

    const areaFields =
        document.getElementById('areaFields');

    const pieceLength =
        document.getElementById('pieceLength');

    const pieceWidth =
        document.getElementById('pieceWidth');

    const pieceCount =
        document.getElementById('pieceCount');

    const wastePercent =
        document.getElementById('wastePercent');

    const bomMaterialTableBody =
        document.getElementById('bomMaterialTableBody');

    const materialModalTitle =
        document.getElementById('materialModalTitle');

    const materialSaveButton =
        materialForm.querySelector('.material-save-button');


    /* =========================================================
       EDIT STATE
    ========================================================= */

    let editingRow = null;


    /* =========================================================
       GET SELECTED METHOD
    ========================================================= */

    function getSelectedMethod() {

        const selectedMethod =
            document.querySelector(
                'input[name="material_method"]:checked'
            );

        return selectedMethod
            ? selectedMethod.value
            : 'fix';

    }


    /* =========================================================
       MATERIAL UNIT
    ========================================================= */

    function updateMaterialUnit() {

        const selectedMaterial =
            materialName.value;

        if (
            selectedMaterial &&
            materialMaster[selectedMaterial]
        ) {

            materialUnitPreview.textContent =
                materialMaster[selectedMaterial].unit;

            materialUnitPreview.classList.add(
                'selected'
            );

        } else {

            materialUnitPreview.textContent =
                'Pilih bahan terlebih dahulu';

            materialUnitPreview.classList.remove(
                'selected'
            );

        }

    }


    /* =========================================================
       METHOD VISIBILITY
    ========================================================= */

    function updateMaterialMethodFields() {

        const method =
            getSelectedMethod();

        if (method === 'fix') {

            fixFields.classList.remove('hidden');

            areaFields.classList.add('hidden');

            materialQuantity.disabled = false;

            pieceLength.disabled = true;
            pieceWidth.disabled = true;
            pieceCount.disabled = true;
            wastePercent.disabled = true;

        } else {

            fixFields.classList.add('hidden');

            areaFields.classList.remove('hidden');

            materialQuantity.disabled = true;

            pieceLength.disabled = false;
            pieceWidth.disabled = false;
            pieceCount.disabled = false;
            wastePercent.disabled = false;

        }

    }


    /* =========================================================
       SET METHOD
    ========================================================= */

    function setMaterialMethod(method) {

        const methodRadio =
            document.querySelector(
                `input[name="material_method"][value="${method}"]`
            );

        if (methodRadio) {
            methodRadio.checked = true;
        }

        updateMaterialMethodFields();

    }


    /* =========================================================
       RESET FORM
    ========================================================= */

    function resetMaterialForm() {

        materialForm.reset();

        editingRow = null;

        setMaterialMethod('fix');

        updateMaterialUnit();

        materialModalTitle.textContent =
            'Tambah Bahan Baku';

        materialSaveButton.textContent =
            'Simpan Bahan';

    }


    /* =========================================================
       OPEN ADD MODAL
    ========================================================= */

    function openMaterialModal() {

        resetMaterialForm();

        materialModal.classList.add('active');

        document.body.style.overflow =
            'hidden';

        setTimeout(function () {
            materialName.focus();
        }, 100);

    }


    /* =========================================================
       OPEN EDIT MODAL
    ========================================================= */

    function openEditMaterialModal(row) {

        editingRow = row;

        const name =
            row.dataset.name || '';

        const description =
            row.dataset.description || '';

        const method =
            row.dataset.method || 'fix';


        /* =========================
           COMMON DATA
        ========================= */

        materialName.value = name;

        materialDescription.value =
            description;

        updateMaterialUnit();

        setMaterialMethod(method);


        /* =========================
           FIX DATA
        ========================= */

        if (method === 'fix') {

            materialQuantity.value =
                row.dataset.quantity || '';

            pieceLength.value = '';
            pieceWidth.value = '';
            pieceCount.value = '';
            wastePercent.value = '';

        }


        /* =========================
           AREA DATA
        ========================= */

        if (method === 'area') {

            materialQuantity.value = '';

            pieceLength.value =
                row.dataset.pieceLength || '';

            pieceWidth.value =
                row.dataset.pieceWidth || '';

            pieceCount.value =
                row.dataset.pieceCount || '';

            wastePercent.value =
                row.dataset.wastePercent || '';

        }


        /* =========================
           MODAL STATE
        ========================= */

        materialModalTitle.textContent =
            'Edit Bahan Baku';

        materialSaveButton.textContent =
            'Simpan Perubahan';

        materialModal.classList.add('active');

        document.body.style.overflow =
            'hidden';

    }


    /* =========================================================
       CLOSE MODAL
    ========================================================= */

    function closeModal() {

        materialModal.classList.remove(
            'active'
        );

        document.body.style.overflow =
            '';

    }


    /* =========================================================
       BUILD FIX ROW CONTENT
    ========================================================= */

    function buildFixRowContent(
        name,
        description,
        quantity,
        unit
    ) {

        return `
            <td>

                <div class="material-info">

                    <strong>
                        ${escapeHtml(name)}
                    </strong>

                    <span>
                        ${
                            description
                                ? escapeHtml(description)
                                : 'Tidak ada keterangan'
                        }
                    </span>

                </div>

            </td>


            <td>

                <span class="bom-method-badge fix">
                    Fix
                </span>

            </td>


            <td>

                <div class="fix-requirement">

                    <strong>
                        ${escapeHtml(quantity)}
                    </strong>

                </div>

            </td>


            <td>

                <span class="material-unit">
                    ${escapeHtml(unit)}
                </span>

            </td>


            <td>

                <div class="bom-actions">

                    <button
                        type="button"
                        class="bom-action edit"
                    >
                        Edit
                    </button>

                    <button
                        type="button"
                        class="bom-action delete"
                    >
                        Hapus
                    </button>

                </div>

            </td>
        `;

    }


    /* =========================================================
       BUILD AREA ROW CONTENT
    ========================================================= */

    function buildAreaRowContent(
        name,
        description,
        length,
        width,
        count,
        waste,
        unit
    ) {

        return `
            <td>

                <div class="material-info">

                    <strong>
                        ${escapeHtml(name)}
                    </strong>

                    <span>
                        ${
                            description
                                ? escapeHtml(description)
                                : 'Tidak ada keterangan'
                        }
                    </span>

                </div>

            </td>


            <td>

                <span class="bom-method-badge area">
                    Area
                </span>

            </td>


            <td>

                <div class="area-requirement">

                    <strong>
                        ${escapeHtml(length)}
                        ×
                        ${escapeHtml(width)}
                        cm
                    </strong>

                    <span>
                        ${escapeHtml(count)}
                        potongan
                        •
                        Waste
                        ${escapeHtml(waste)}%
                    </span>

                </div>

            </td>


            <td>

                <span class="material-unit">
                    ${escapeHtml(unit)}
                </span>

            </td>


            <td>

                <div class="bom-actions">

                    <button
                        type="button"
                        class="bom-action edit"
                    >
                        Edit
                    </button>

                    <button
                        type="button"
                        class="bom-action delete"
                    >
                        Hapus
                    </button>

                </div>

            </td>
        `;

    }


    /* =========================================================
       SET FIX ROW DATA
    ========================================================= */

    function setFixRowData(
        row,
        name,
        description,
        quantity,
        unit
    ) {

        row.dataset.name =
            name;

        row.dataset.description =
            description;

        row.dataset.method =
            'fix';

        row.dataset.quantity =
            quantity;

        row.dataset.unit =
            unit;


        delete row.dataset.pieceLength;
        delete row.dataset.pieceWidth;
        delete row.dataset.pieceCount;
        delete row.dataset.wastePercent;


        row.innerHTML =
            buildFixRowContent(
                name,
                description,
                quantity,
                unit
            );

    }


    /* =========================================================
       SET AREA ROW DATA
    ========================================================= */

    function setAreaRowData(
        row,
        name,
        description,
        length,
        width,
        count,
        waste,
        unit
    ) {

        row.dataset.name =
            name;

        row.dataset.description =
            description;

        row.dataset.method =
            'area';

        row.dataset.unit =
            unit;

        row.dataset.pieceLength =
            length;

        row.dataset.pieceWidth =
            width;

        row.dataset.pieceCount =
            count;

        row.dataset.wastePercent =
            waste;


        delete row.dataset.quantity;


        row.innerHTML =
            buildAreaRowContent(
                name,
                description,
                length,
                width,
                count,
                waste,
                unit
            );

    }


    /* =========================================================
       PREPARE EXISTING DUMMY ROWS

       Supaya Hanger dan MDF yang sudah ada dari Blade
       juga bisa langsung diedit.
    ========================================================= */

    function prepareExistingRows() {

        const rows =
            bomMaterialTableBody.querySelectorAll(
                'tr'
            );


        rows.forEach(function (row) {

            const materialNameElement =
                row.querySelector(
                    '.material-info strong'
                );

            const descriptionElement =
                row.querySelector(
                    '.material-info span'
                );

            const methodBadge =
                row.querySelector(
                    '.bom-method-badge'
                );

            const unitElement =
                row.querySelector(
                    '.material-unit'
                );


            if (
                !materialNameElement ||
                !methodBadge
            ) {
                return;
            }


            const name =
                materialNameElement.textContent.trim();

            const description =
                descriptionElement
                    ? descriptionElement.textContent.trim()
                    : '';

            const unit =
                unitElement
                    ? unitElement.textContent.trim()
                    : '';


            /* =========================
               FIX DUMMY ROW
            ========================= */

            if (
                methodBadge.classList.contains(
                    'fix'
                )
            ) {

                const quantityElement =
                    row.querySelector(
                        '.fix-requirement strong'
                    );

                row.dataset.name =
                    name;

                row.dataset.description =
                    description;

                row.dataset.method =
                    'fix';

                row.dataset.quantity =
                    quantityElement
                        ? quantityElement.textContent.trim()
                        : '';

                row.dataset.unit =
                    unit;

            }


            /* =========================
               AREA DUMMY ROW
            ========================= */

            if (
                methodBadge.classList.contains(
                    'area'
                )
            ) {

                row.dataset.name =
                    name;

                row.dataset.description =
                    description;

                row.dataset.method =
                    'area';

                row.dataset.unit =
                    unit;


                /*
                    Untuk dummy MDF awal kita sudah tahu
                    nilai Area-nya.

                    Nantinya backend akan mengirim data ini
                    langsung dari database.
                */

                if (name === 'MDF') {

                    row.dataset.pieceLength =
                        '30';

                    row.dataset.pieceWidth =
                        '9';

                    row.dataset.pieceCount =
                        '2';

                    row.dataset.wastePercent =
                        '10';

                }

            }

        });

    }


    /* =========================================================
       MATERIAL CHANGE
    ========================================================= */

    materialName.addEventListener(
        'change',
        updateMaterialUnit
    );


    /* =========================================================
       METHOD CHANGE
    ========================================================= */

    document
        .querySelectorAll(
            'input[name="material_method"]'
        )
        .forEach(function (radio) {

            radio.addEventListener(
                'change',
                updateMaterialMethodFields
            );

        });


    /* =========================================================
       ADD MATERIAL
    ========================================================= */

    addMaterialButton.addEventListener(
        'click',
        openMaterialModal
    );


    /* =========================================================
   EDIT & DELETE MATERIAL
========================================================= */

bomMaterialTableBody.addEventListener(
    'click',
    function (event) {

        /* =========================
           EDIT
        ========================= */

        const editButton =
            event.target.closest(
                '.bom-action.edit'
            );

        if (editButton) {

            const row =
                editButton.closest('tr');

            if (!row) {
                return;
            }

            openEditMaterialModal(row);

            return;
        }


        /* =========================
           DELETE
        ========================= */

        const deleteButton =
            event.target.closest(
                '.bom-action.delete'
            );

        if (deleteButton) {

            const row =
                deleteButton.closest('tr');

            if (!row) {
                return;
            }


            const name =
                row.dataset.name ||
                row.querySelector(
                    '.material-info strong'
                )?.textContent.trim() ||
                'bahan ini';


            const confirmed =
                confirm(
                    `Yakin ingin menghapus ${name} dari BOM?`
                );


            if (!confirmed) {
                return;
            }


            row.remove();
        }

    }
);


    /* =========================================================
       CLOSE EVENTS
    ========================================================= */

    closeMaterialModal.addEventListener(
        'click',
        closeModal
    );


    cancelMaterialModal.addEventListener(
        'click',
        closeModal
    );


    materialModal.addEventListener(
        'click',
        function (event) {

            if (
                event.target === materialModal
            ) {
                closeModal();
            }

        }
    );


    document.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key === 'Escape' &&
                materialModal.classList.contains(
                    'active'
                )
            ) {
                closeModal();
            }

        }
    );


    /* =========================================================
       SAVE MATERIAL
    ========================================================= */

    materialForm.addEventListener(
        'submit',
        function (event) {

            event.preventDefault();


            /* =========================
               COMMON DATA
            ========================= */

            const name =
                materialName.value;

            const description =
                materialDescription.value.trim();

            const method =
                getSelectedMethod();

            const unit =
                materialMaster[name]
                    ? materialMaster[name].unit
                    : '';


            /* =========================
               MATERIAL VALIDATION
            ========================= */

            if (!name || !unit) {

                alert(
                    'Silakan pilih bahan mentah.'
                );

                materialName.focus();

                return;

            }


            /* =================================================
               FIX
            ================================================= */

            if (method === 'fix') {

                const quantity =
                    materialQuantity.value.trim();


                if (
                    !quantity ||
                    Number(quantity) <= 0
                ) {

                    alert(
                        'Quantity harus lebih dari 0.'
                    );

                    materialQuantity.focus();

                    return;

                }


                /*
                    Kalau sedang edit:
                    gunakan row lama.

                    Kalau sedang add:
                    buat row baru.
                */

                const row =
                    editingRow ||
                    document.createElement('tr');


                setFixRowData(
                    row,
                    name,
                    description,
                    quantity,
                    unit
                );


                if (!editingRow) {

                    bomMaterialTableBody.appendChild(
                        row
                    );

                }

            }


            /* =================================================
               AREA
            ================================================= */

            if (method === 'area') {

                const length =
                    pieceLength.value.trim();

                const width =
                    pieceWidth.value.trim();

                const count =
                    pieceCount.value.trim();

                const waste =
                    wastePercent.value.trim();


                /* =========================
                   AREA VALIDATION
                ========================= */

                if (
                    !length ||
                    Number(length) <= 0
                ) {

                    alert(
                        'Panjang potongan harus lebih dari 0.'
                    );

                    pieceLength.focus();

                    return;

                }


                if (
                    !width ||
                    Number(width) <= 0
                ) {

                    alert(
                        'Lebar potongan harus lebih dari 0.'
                    );

                    pieceWidth.focus();

                    return;

                }


                if (
                    !count ||
                    Number(count) <= 0
                ) {

                    alert(
                        'Jumlah potongan harus lebih dari 0.'
                    );

                    pieceCount.focus();

                    return;

                }


                if (
                    waste === '' ||
                    Number(waste) < 0 ||
                    Number(waste) > 100
                ) {

                    alert(
                        'Waste harus berada di antara 0 sampai 100%.'
                    );

                    wastePercent.focus();

                    return;

                }


                const row =
                    editingRow ||
                    document.createElement('tr');


                setAreaRowData(
                    row,
                    name,
                    description,
                    length,
                    width,
                    count,
                    waste,
                    unit
                );


                if (!editingRow) {

                    bomMaterialTableBody.appendChild(
                        row
                    );

                }

            }


            /* =========================
               FINISH
            ========================= */

            resetMaterialForm();

            closeModal();

        }
    );


    /* =========================================================
       ESCAPE HTML
    ========================================================= */

    function escapeHtml(value) {

        const element =
            document.createElement('div');

        element.textContent =
            value;

        return element.innerHTML;

    }


    /* =========================================================
       INITIAL STATE
    ========================================================= */

    prepareExistingRows();

    updateMaterialUnit();

    updateMaterialMethodFields();

});

</script>

@endpush