```blade
@extends('layouts.admin')

@section('title', 'Kategori Produk')

@section('content')

<div class="category-page">

    {{-- =========================
        PAGE HEADER
    ========================== --}}
    <div class="category-page-header">

        <div>
            <h1>Kategori Produk</h1>

            <p>
                Kelola kategori yang digunakan pada produk.
            </p>
        </div>

        <button
            type="button"
            class="add-category-button"
            id="addCategoryButton"
        >
            <span>+</span>
            Tambah Kategori
        </button>

    </div>


    {{-- =========================
        CATEGORY CARD
    ========================== --}}
    <div class="category-card">

        {{-- Search --}}
        <div class="category-toolbar">

            <div class="category-search">

                <svg
                    width="18"
                    height="18"
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
                    id="categorySearch"
                    placeholder="Cari kategori..."
                    autocomplete="off"
                >

            </div>

        </div>


        {{-- =========================
            TABLE
        ========================== --}}
        <div class="category-table-wrapper">

            <table class="category-table">

                <thead>
                    <tr>

                        <th class="category-number-column">
                            No
                        </th>

                        <th>
                            Nama Kategori
                        </th>

                        <th class="category-action-column">
                            Aksi
                        </th>

                    </tr>
                </thead>


                <tbody id="categoryTableBody">

                    <tr data-category-name="SIGN PERINGATAN">

                        <td class="category-number">
                            1
                        </td>

                        <td>
                            <strong class="category-name">
                                SIGN PERINGATAN
                            </strong>
                        </td>

                        <td>

                            <div class="category-actions">

                                <button
                                    type="button"
                                    class="category-action edit"
                                >
                                    Edit
                                </button>

                                <button
                                    type="button"
                                    class="category-action delete"
                                >
                                    Hapus
                                </button>

                            </div>

                        </td>

                    </tr>


                    <tr data-category-name="WALL DECOR">

                        <td class="category-number">
                            2
                        </td>

                        <td>
                            <strong class="category-name">
                                WALL DECOR
                            </strong>
                        </td>

                        <td>

                            <div class="category-actions">

                                <button
                                    type="button"
                                    class="category-action edit"
                                >
                                    Edit
                                </button>

                                <button
                                    type="button"
                                    class="category-action delete"
                                >
                                    Hapus
                                </button>

                            </div>

                        </td>

                    </tr>


                    <tr data-category-name="RAK DINDING">

                        <td class="category-number">
                            3
                        </td>

                        <td>
                            <strong class="category-name">
                                RAK DINDING
                            </strong>
                        </td>

                        <td>

                            <div class="category-actions">

                                <button
                                    type="button"
                                    class="category-action edit"
                                >
                                    Edit
                                </button>

                                <button
                                    type="button"
                                    class="category-action delete"
                                >
                                    Hapus
                                </button>

                            </div>

                        </td>

                    </tr>

                </tbody>

            </table>


            {{-- Empty search result --}}
            <div
                class="category-empty-state"
                id="categoryEmptyState"
                style="display: none;"
            >
                Kategori tidak ditemukan.
            </div>

        </div>

    </div>

</div>


{{-- =========================================================
    CATEGORY MODAL
========================================================= --}}
<div
    class="category-modal-overlay"
    id="categoryModal"
>

    <div class="category-modal">

        {{-- Header --}}
        <div class="category-modal-header">

            <div>

                <h2 id="categoryModalTitle">
                    Tambah Kategori
                </h2>

                <p id="categoryModalDescription">
                    Tambahkan kategori baru untuk produk.
                </p>

            </div>


            <button
                type="button"
                class="category-modal-close"
                id="closeCategoryModal"
                aria-label="Tutup modal"
            >
                ×
            </button>

        </div>


        {{-- Form --}}
        <form id="categoryForm">

            <div class="category-modal-body">

                <div class="category-form-group">

                    <label for="categoryName">
                        Nama Kategori
                        <span>*</span>
                    </label>

                    <input
                        type="text"
                        id="categoryName"
                        name="category_name"
                        placeholder="Contoh: SIGN PERINGATAN"
                        autocomplete="off"
                    >

                    <small>
                        Nama kategori akan digunakan pada data produk.
                    </small>

                </div>

            </div>


            {{-- Footer --}}
            <div class="category-modal-footer">

                <button
                    type="button"
                    class="category-modal-button cancel"
                    id="cancelCategoryModal"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="category-modal-button save"
                    id="saveCategoryButton"
                >
                    Simpan Kategori
                </button>

            </div>

        </form>

    </div>

</div>

@endsection


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    /* =========================================================
       ELEMENTS
    ========================================================= */

    const addCategoryButton =
        document.getElementById('addCategoryButton');

    const categoryModal =
        document.getElementById('categoryModal');

    const closeCategoryModal =
        document.getElementById('closeCategoryModal');

    const cancelCategoryModal =
        document.getElementById('cancelCategoryModal');

    const categoryForm =
        document.getElementById('categoryForm');

    const categoryName =
        document.getElementById('categoryName');

    const categoryModalTitle =
        document.getElementById('categoryModalTitle');

    const categoryModalDescription =
        document.getElementById('categoryModalDescription');

    const saveCategoryButton =
        document.getElementById('saveCategoryButton');

    const categoryTableBody =
        document.getElementById('categoryTableBody');

    const categorySearch =
        document.getElementById('categorySearch');

    const categoryEmptyState =
        document.getElementById('categoryEmptyState');


    /* =========================================================
       EDIT STATE
    ========================================================= */

    let editingRow = null;


    /* =========================================================
       OPEN ADD MODAL
    ========================================================= */

    function openAddCategoryModal() {

        editingRow = null;

        categoryForm.reset();

        categoryModalTitle.textContent =
            'Tambah Kategori';

        categoryModalDescription.textContent =
            'Tambahkan kategori baru untuk produk.';

        saveCategoryButton.textContent =
            'Simpan Kategori';

        categoryModal.classList.add('active');

        document.body.style.overflow =
            'hidden';

        setTimeout(function () {
            categoryName.focus();
        }, 100);

    }


    /* =========================================================
       OPEN EDIT MODAL
    ========================================================= */

    function openEditCategoryModal(row) {

        editingRow = row;

        const currentName =
            row.dataset.categoryName ||
            row.querySelector(
                '.category-name'
            ).textContent.trim();


        categoryName.value =
            currentName;


        categoryModalTitle.textContent =
            'Edit Kategori';

        categoryModalDescription.textContent =
            'Ubah nama kategori produk.';

        saveCategoryButton.textContent =
            'Simpan Perubahan';


        categoryModal.classList.add('active');

        document.body.style.overflow =
            'hidden';


        setTimeout(function () {

            categoryName.focus();

            categoryName.select();

        }, 100);

    }


    /* =========================================================
       CLOSE MODAL
    ========================================================= */

    function closeModal() {

        categoryModal.classList.remove(
            'active'
        );

        document.body.style.overflow =
            '';

        editingRow = null;

        categoryForm.reset();

    }


    /* =========================================================
       NORMALIZE CATEGORY NAME
    ========================================================= */

    function normalizeCategoryName(value) {

        return value
            .trim()
            .replace(/\s+/g, ' ')
            .toUpperCase();

    }


    /* =========================================================
       CHECK DUPLICATE CATEGORY
    ========================================================= */

    function categoryAlreadyExists(
        name,
        ignoredRow = null
    ) {

        const rows =
            categoryTableBody.querySelectorAll(
                'tr'
            );


        return Array
            .from(rows)
            .some(function (row) {

                if (row === ignoredRow) {
                    return false;
                }

                const existingName =
                    row.dataset.categoryName ||
                    row.querySelector(
                        '.category-name'
                    ).textContent.trim();

                return (
                    existingName.toLowerCase() ===
                    name.toLowerCase()
                );

            });

    }


    /* =========================================================
       CREATE CATEGORY ROW
    ========================================================= */

    function createCategoryRow(name) {

        const row =
            document.createElement('tr');

        row.dataset.categoryName =
            name;

        row.innerHTML = `

            <td class="category-number">
            </td>

            <td>

                <strong class="category-name">
                    ${escapeHtml(name)}
                </strong>

            </td>

            <td>

                <div class="category-actions">

                    <button
                        type="button"
                        class="category-action edit"
                    >
                        Edit
                    </button>

                    <button
                        type="button"
                        class="category-action delete"
                    >
                        Hapus
                    </button>

                </div>

            </td>

        `;

        return row;

    }


    /* =========================================================
       UPDATE CATEGORY NUMBER
    ========================================================= */

    function updateCategoryNumbers() {

        const rows =
            categoryTableBody.querySelectorAll(
                'tr'
            );

        rows.forEach(function (row, index) {

            const numberCell =
                row.querySelector(
                    '.category-number'
                );

            if (numberCell) {
                numberCell.textContent =
                    index + 1;
            }

        });

    }


    /* =========================================================
       SEARCH
    ========================================================= */

    function filterCategories() {

        const keyword =
            categorySearch.value
                .trim()
                .toLowerCase();


        const rows =
            categoryTableBody.querySelectorAll(
                'tr'
            );


        let visibleCount = 0;


        rows.forEach(function (row) {

            const name =
                (
                    row.dataset.categoryName ||
                    ''
                ).toLowerCase();


            const isMatch =
                name.includes(keyword);


            row.style.display =
                isMatch
                    ? ''
                    : 'none';


            if (isMatch) {
                visibleCount++;
            }

        });


        categoryEmptyState.style.display =
            visibleCount === 0
                ? 'block'
                : 'none';

    }


    /* =========================================================
       ADD BUTTON
    ========================================================= */

    addCategoryButton.addEventListener(
        'click',
        openAddCategoryModal
    );


    /* =========================================================
       TABLE ACTIONS
       EDIT + DELETE
    ========================================================= */

    categoryTableBody.addEventListener(
        'click',
        function (event) {


            /* =========================
               EDIT
            ========================= */

            const editButton =
                event.target.closest(
                    '.category-action.edit'
                );


            if (editButton) {

                const row =
                    editButton.closest('tr');

                if (!row) {
                    return;
                }

                openEditCategoryModal(row);

                return;

            }


            /* =========================
               DELETE
            ========================= */

            const deleteButton =
                event.target.closest(
                    '.category-action.delete'
                );


            if (deleteButton) {

                const row =
                    deleteButton.closest('tr');

                if (!row) {
                    return;
                }


                const name =
                    row.dataset.categoryName ||
                    row.querySelector(
                        '.category-name'
                    ).textContent.trim();


                const confirmed =
                    confirm(
                        `Yakin ingin menghapus kategori ${name}?`
                    );


                if (!confirmed) {
                    return;
                }


                row.remove();

                updateCategoryNumbers();

                filterCategories();

            }

        }
    );


    /* =========================================================
       SAVE CATEGORY
    ========================================================= */

    categoryForm.addEventListener(
        'submit',
        function (event) {

            event.preventDefault();


            const name =
                normalizeCategoryName(
                    categoryName.value
                );


            /* =========================
               VALIDATION
            ========================= */

            if (!name) {

                alert(
                    'Nama kategori wajib diisi.'
                );

                categoryName.focus();

                return;

            }


            if (
                categoryAlreadyExists(
                    name,
                    editingRow
                )
            ) {

                alert(
                    'Kategori dengan nama tersebut sudah tersedia.'
                );

                categoryName.focus();

                categoryName.select();

                return;

            }


            /* =========================
               EDIT
            ========================= */

            if (editingRow) {

                editingRow.dataset.categoryName =
                    name;

                const categoryNameElement =
                    editingRow.querySelector(
                        '.category-name'
                    );

                categoryNameElement.textContent =
                    name;

            }


            /* =========================
               ADD
            ========================= */

            else {

                const newRow =
                    createCategoryRow(name);

                categoryTableBody.appendChild(
                    newRow
                );

            }


            updateCategoryNumbers();

            closeModal();

            filterCategories();

        }
    );


    /* =========================================================
       SEARCH EVENT
    ========================================================= */

    categorySearch.addEventListener(
        'input',
        filterCategories
    );


    /* =========================================================
       CLOSE EVENTS
    ========================================================= */

    closeCategoryModal.addEventListener(
        'click',
        closeModal
    );


    cancelCategoryModal.addEventListener(
        'click',
        closeModal
    );


    categoryModal.addEventListener(
        'click',
        function (event) {

            if (
                event.target === categoryModal
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
                categoryModal.classList.contains(
                    'active'
                )
            ) {

                closeModal();

            }

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

    updateCategoryNumbers();

    filterCategories();

});

</script>

@endpush