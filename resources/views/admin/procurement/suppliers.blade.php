@extends('layouts.admin')

@section('title', 'Supplier')

@section('content')

<div class="suppliers-page">

    {{-- ========================================
        PAGE HEADER
    ========================================= --}}
    <div class="suppliers-page-header">

        <div>
            <h1>Supplier</h1>
            <p>
                Kelola data supplier untuk kebutuhan procurement bahan baku.
            </p>
        </div>

        <button
            type="button"
            class="supplier-add-button"
            id="openSupplierModal"
        >
            <svg
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >
                <path d="M12 5v14"/>
                <path d="M5 12h14"/>
            </svg>

            Tambah Supplier
        </button>

    </div>


    {{-- ========================================
        SUMMARY
    ========================================= --}}
    <div class="supplier-summary">

        <div class="supplier-summary-card">

            <span class="supplier-summary-label">
                Total Supplier
            </span>

            <strong
                class="supplier-summary-value"
                id="totalSupplier"
            >
                3
            </strong>

            <span class="supplier-summary-caption">
                supplier terdaftar
            </span>

        </div>


        <div class="supplier-summary-card">

            <span class="supplier-summary-label">
                Supplier Aktif
            </span>

            <strong
                class="supplier-summary-value"
                id="activeSupplier"
            >
                3
            </strong>

            <span class="supplier-summary-caption">
                dapat digunakan untuk procurement
            </span>

        </div>


        <div class="supplier-summary-card">

            <span class="supplier-summary-label">
                Supplier Nonaktif
            </span>

            <strong
                class="supplier-summary-value"
                id="inactiveSupplier"
            >
                0
            </strong>

            <span class="supplier-summary-caption">
                tidak tersedia untuk pemesanan
            </span>

        </div>

    </div>


    {{-- ========================================
        MAIN CARD
    ========================================= --}}
    <div class="suppliers-card">

        {{-- HEADER --}}
        <div class="suppliers-card-header">

            <div>
                <h2>Daftar Supplier</h2>

                <p>
                    Supplier yang tersedia untuk pemesanan bahan baku.
                </p>
            </div>

        </div>


        {{-- ========================================
            TOOLBAR
        ========================================= --}}
        <div class="suppliers-toolbar">

            <div class="suppliers-search">

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
                    id="supplierSearch"
                    placeholder="Cari supplier..."
                >

            </div>


            <select
                class="suppliers-filter"
                id="supplierStatusFilter"
            >
                <option value="all">
                    Semua Status
                </option>

                <option value="active">
                    Aktif
                </option>

                <option value="inactive">
                    Nonaktif
                </option>
            </select>

        </div>


        {{-- ========================================
            TABLE
        ========================================= --}}
        <div class="suppliers-table-wrapper">

            <table class="suppliers-table">

                <thead>
                    <tr>
                        <th>Supplier</th>
                        <th>Kontak</th>
                        <th>Alamat</th>
                        <th>Bahan Disupply</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>


                <tbody id="supplierTableBody">

                    {{-- PT KAYU JAYA --}}
                    <tr
                        data-id="1"
                        data-name="PT Kayu Jaya"
                        data-email="supplier@kayujaya.com"
                        data-phone="0812-9876-5432"
                        data-address="Malang, Jawa Timur"
                        data-materials="MDF"
                        data-status="active"
                    >

                        <td>
                            <div class="supplier-name-cell">

                                <div class="supplier-avatar">
                                    K
                                </div>

                                <div>
                                    <strong>
                                        PT Kayu Jaya
                                    </strong>

                                    <span>
                                        Supplier bahan baku
                                    </span>
                                </div>

                            </div>
                        </td>


                        <td>
                            <div class="supplier-contact-cell">

                                <span>
                                    supplier@kayujaya.com
                                </span>

                                <span>
                                    0812-9876-5432
                                </span>

                            </div>
                        </td>


                        <td>
                            Malang, Jawa Timur
                        </td>


                        <td>
                            <div class="supplier-material-list">
                                <span>MDF</span>
                            </div>
                        </td>


                        <td>
                            <span class="supplier-status active">
                                <span></span>
                                Aktif
                            </span>
                        </td>


                        <td>
                            <div class="supplier-actions">

                                <button
                                    type="button"
                                    class="supplier-action-button edit supplier-edit-button"
                                    title="Edit Supplier"
                                >
                                    <svg
                                        width="15"
                                        height="15"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M12 20h9"/>
                                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                    </svg>
                                </button>


                                <button
                                    type="button"
                                    class="supplier-action-button delete supplier-delete-button"
                                    title="Hapus Supplier"
                                >
                                    <svg
                                        width="15"
                                        height="15"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
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


                    {{-- CV MDF MALANG --}}
                    <tr
                        data-id="2"
                        data-name="CV MDF Malang"
                        data-email="order@mdfmalang.com"
                        data-phone="0813-4455-6677"
                        data-address="Kabupaten Malang, Jawa Timur"
                        data-materials="MDF"
                        data-status="active"
                    >

                        <td>
                            <div class="supplier-name-cell">

                                <div class="supplier-avatar">
                                    M
                                </div>

                                <div>
                                    <strong>
                                        CV MDF Malang
                                    </strong>

                                    <span>
                                        Supplier bahan baku
                                    </span>
                                </div>

                            </div>
                        </td>


                        <td>
                            <div class="supplier-contact-cell">

                                <span>
                                    order@mdfmalang.com
                                </span>

                                <span>
                                    0813-4455-6677
                                </span>

                            </div>
                        </td>


                        <td>
                            Kabupaten Malang, Jawa Timur
                        </td>


                        <td>
                            <div class="supplier-material-list">
                                <span>MDF</span>
                            </div>
                        </td>


                        <td>
                            <span class="supplier-status active">
                                <span></span>
                                Aktif
                            </span>
                        </td>


                        <td>
                            <div class="supplier-actions">

                                <button
                                    type="button"
                                    class="supplier-action-button edit supplier-edit-button"
                                    title="Edit Supplier"
                                >
                                    <svg
                                        width="15"
                                        height="15"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M12 20h9"/>
                                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                    </svg>
                                </button>


                                <button
                                    type="button"
                                    class="supplier-action-button delete supplier-delete-button"
                                    title="Hapus Supplier"
                                >
                                    <svg
                                        width="15"
                                        height="15"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
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


                    {{-- PT WARNA NUSANTARA --}}
                    <tr
                        data-id="3"
                        data-name="PT Warna Nusantara"
                        data-email="order@warnanusantara.com"
                        data-phone="0812-1122-3344"
                        data-address="Surabaya, Jawa Timur"
                        data-materials="Cat Hitam"
                        data-status="active"
                    >

                        <td>
                            <div class="supplier-name-cell">

                                <div class="supplier-avatar">
                                    W
                                </div>

                                <div>
                                    <strong>
                                        PT Warna Nusantara
                                    </strong>

                                    <span>
                                        Supplier bahan baku
                                    </span>
                                </div>

                            </div>
                        </td>


                        <td>
                            <div class="supplier-contact-cell">

                                <span>
                                    order@warnanusantara.com
                                </span>

                                <span>
                                    0812-1122-3344
                                </span>

                            </div>
                        </td>


                        <td>
                            Surabaya, Jawa Timur
                        </td>


                        <td>
                            <div class="supplier-material-list">
                                <span>Cat Hitam</span>
                            </div>
                        </td>


                        <td>
                            <span class="supplier-status active">
                                <span></span>
                                Aktif
                            </span>
                        </td>


                        <td>
                            <div class="supplier-actions">

                                <button
                                    type="button"
                                    class="supplier-action-button edit supplier-edit-button"
                                    title="Edit Supplier"
                                >
                                    <svg
                                        width="15"
                                        height="15"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M12 20h9"/>
                                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                    </svg>
                                </button>


                                <button
                                    type="button"
                                    class="supplier-action-button delete supplier-delete-button"
                                    title="Hapus Supplier"
                                >
                                    <svg
                                        width="15"
                                        height="15"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
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

                </tbody>

            </table>

        </div>


        {{-- EMPTY STATE --}}
        <div
            class="suppliers-empty"
            id="supplierEmpty"
        >

            <svg
                width="32"
                height="32"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
            >
                <circle cx="11" cy="11" r="7"/>
                <path d="m20 20-3.5-3.5"/>
            </svg>

            <strong>
                Supplier tidak ditemukan
            </strong>

            <span>
                Tidak ada supplier yang sesuai dengan pencarian atau filter.
            </span>

        </div>

    </div>

</div>



{{-- ========================================
    ADD / EDIT SUPPLIER MODAL
======================================== --}}
<div
    class="supplier-modal-overlay"
    id="supplierModal"
>

    <div class="supplier-modal">

        {{-- HEADER --}}
        <div class="supplier-modal-header">

            <div>
                <h2 id="supplierModalTitle">
                    Tambah Supplier
                </h2>

                <p id="supplierModalDescription">
                    Tambahkan supplier baru untuk kebutuhan procurement.
                </p>
            </div>


            <button
                type="button"
                class="supplier-modal-close"
                id="closeSupplierModal"
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
        <div class="supplier-modal-body">

            <form id="supplierForm">

                <input
                    type="hidden"
                    id="supplierId"
                >


                {{-- NAMA --}}
                <div class="supplier-form-group">

                    <label for="supplierName">
                        Nama Supplier
                        <span>*</span>
                    </label>

                    <input
                        type="text"
                        id="supplierName"
                        placeholder="Contoh: PT Kayu Jaya"
                        required
                    >

                </div>


                {{-- EMAIL --}}
                <div class="supplier-form-group">

                    <label for="supplierEmail">
                        Email Supplier
                        <span>*</span>
                    </label>

                    <input
                        type="email"
                        id="supplierEmail"
                        placeholder="supplier@email.com"
                        required
                    >

                    <small>
                        Email ini akan digunakan untuk pengiriman Purchase Order.
                    </small>

                </div>


                {{-- PHONE --}}
                <div class="supplier-form-group">

                    <label for="supplierPhone">
                        Nomor Telepon
                    </label>

                    <input
                        type="text"
                        id="supplierPhone"
                        placeholder="Contoh: 0812-3456-7890"
                    >

                </div>


                {{-- ADDRESS --}}
                <div class="supplier-form-group">

                    <label for="supplierAddress">
                        Alamat
                    </label>

                    <textarea
                        id="supplierAddress"
                        rows="3"
                        placeholder="Masukkan alamat supplier..."
                    ></textarea>

                </div>


                {{-- MATERIAL --}}
                <div class="supplier-form-group">

                    <label for="supplierMaterial">
                        Bahan yang Disupply
                        <span>*</span>
                    </label>

                    <select
                        id="supplierMaterial"
                        required
                    >
                        <option value="">
                            Pilih bahan baku
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
                    </select>

                    <small>
                        Daftar bahan nantinya berasal dari Master Bahan Mentah.
                    </small>

                </div>


                {{-- STATUS --}}
                <div class="supplier-form-group">

                    <label for="supplierStatus">
                        Status
                        <span>*</span>
                    </label>

                    <select
                        id="supplierStatus"
                        required
                    >
                        <option value="active">
                            Aktif
                        </option>

                        <option value="inactive">
                            Nonaktif
                        </option>
                    </select>

                </div>

            </form>

        </div>


        {{-- FOOTER --}}
        <div class="supplier-modal-footer">

            <button
                type="button"
                class="supplier-button secondary"
                id="cancelSupplierButton"
            >
                Batal
            </button>


            <button
                type="submit"
                form="supplierForm"
                class="supplier-button primary"
            >
                Simpan Supplier
            </button>

        </div>

    </div>

</div>

@endsection



@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | Elements
    |--------------------------------------------------------------------------
    */

    const supplierTableBody =
        document.getElementById('supplierTableBody');

    const supplierSearch =
        document.getElementById('supplierSearch');

    const supplierStatusFilter =
        document.getElementById('supplierStatusFilter');

    const supplierEmpty =
        document.getElementById('supplierEmpty');


    const supplierModal =
        document.getElementById('supplierModal');

    const openSupplierModal =
        document.getElementById('openSupplierModal');

    const closeSupplierModal =
        document.getElementById('closeSupplierModal');

    const cancelSupplierButton =
        document.getElementById('cancelSupplierButton');


    const supplierModalTitle =
        document.getElementById('supplierModalTitle');

    const supplierModalDescription =
        document.getElementById('supplierModalDescription');


    const supplierForm =
        document.getElementById('supplierForm');

    const supplierId =
        document.getElementById('supplierId');

    const supplierName =
        document.getElementById('supplierName');

    const supplierEmail =
        document.getElementById('supplierEmail');

    const supplierPhone =
        document.getElementById('supplierPhone');

    const supplierAddress =
        document.getElementById('supplierAddress');

    const supplierMaterial =
        document.getElementById('supplierMaterial');

    const supplierStatus =
        document.getElementById('supplierStatus');


    const totalSupplier =
        document.getElementById('totalSupplier');

    const activeSupplier =
        document.getElementById('activeSupplier');

    const inactiveSupplier =
        document.getElementById('inactiveSupplier');


    let editingRow = null;



    /*
    |--------------------------------------------------------------------------
    | Modal
    |--------------------------------------------------------------------------
    */

    function openModal() {

        supplierModal.classList.add('show');

        document.body.classList.add('modal-open');

    }


    function closeModal() {

        supplierModal.classList.remove('show');

        document.body.classList.remove('modal-open');

        supplierForm.reset();

        supplierId.value = '';

        editingRow = null;

    }


    function openAddModal() {

        editingRow = null;

        supplierForm.reset();

        supplierId.value = '';

        supplierStatus.value = 'active';


        supplierModalTitle.textContent =
            'Tambah Supplier';

        supplierModalDescription.textContent =
            'Tambahkan supplier baru untuk kebutuhan procurement.';


        openModal();

    }


    openSupplierModal.addEventListener(
        'click',
        openAddModal
    );


    closeSupplierModal.addEventListener(
        'click',
        closeModal
    );


    cancelSupplierButton.addEventListener(
        'click',
        closeModal
    );


    supplierModal.addEventListener(
        'click',
        function (event) {

            if (event.target === supplierModal) {
                closeModal();
            }

        }
    );



    /*
    |--------------------------------------------------------------------------
    | Edit Supplier
    |--------------------------------------------------------------------------
    */

    function bindEditButtons() {

        const editButtons =
            document.querySelectorAll(
                '.supplier-edit-button'
            );


        editButtons.forEach(function (button) {

            button.onclick = function () {

                editingRow =
                    button.closest('tr');


                supplierId.value =
                    editingRow.dataset.id;

                supplierName.value =
                    editingRow.dataset.name;

                supplierEmail.value =
                    editingRow.dataset.email;

                supplierPhone.value =
                    editingRow.dataset.phone;

                supplierAddress.value =
                    editingRow.dataset.address;

                supplierMaterial.value =
                    editingRow.dataset.materials;

                supplierStatus.value =
                    editingRow.dataset.status;


                supplierModalTitle.textContent =
                    'Edit Supplier';

                supplierModalDescription.textContent =
                    'Perbarui informasi supplier yang dipilih.';


                openModal();

            };

        });

    }



    /*
    |--------------------------------------------------------------------------
    | Delete Supplier
    |--------------------------------------------------------------------------
    */

    function bindDeleteButtons() {

        const deleteButtons =
            document.querySelectorAll(
                '.supplier-delete-button'
            );


        deleteButtons.forEach(function (button) {

            button.onclick = function () {

                const row =
                    button.closest('tr');

                const name =
                    row.dataset.name;


                const confirmed =
                    confirm(
                        'Hapus supplier "' +
                        name +
                        '"?'
                    );


                if (!confirmed) {
                    return;
                }


                row.remove();

                updateSummary();

                filterSuppliers();

            };

        });

    }



    /*
    |--------------------------------------------------------------------------
    | Save Supplier
    |--------------------------------------------------------------------------
    */

    supplierForm.addEventListener(
        'submit',
        function (event) {

            event.preventDefault();


            const name =
                supplierName.value.trim();

            const email =
                supplierEmail.value.trim();

            const phone =
                supplierPhone.value.trim();

            const address =
                supplierAddress.value.trim();

            const material =
                supplierMaterial.value;

            const status =
                supplierStatus.value;


            if (
                !name ||
                !email ||
                !material
            ) {
                return;
            }


            /*
            | Initial avatar
            */

            const words =
                name
                    .replace(/^PT\s+/i, '')
                    .replace(/^CV\s+/i, '')
                    .trim();

            const initial =
                words.charAt(0).toUpperCase();


            /*
            | Status HTML
            */

            const statusText =
                status === 'active'
                    ? 'Aktif'
                    : 'Nonaktif';


            const rowHTML = `
                <td>
                    <div class="supplier-name-cell">

                        <div class="supplier-avatar">
                            ${initial}
                        </div>

                        <div>
                            <strong>
                                ${escapeHtml(name)}
                            </strong>

                            <span>
                                Supplier bahan baku
                            </span>
                        </div>

                    </div>
                </td>


                <td>
                    <div class="supplier-contact-cell">

                        <span>
                            ${escapeHtml(email)}
                        </span>

                        <span>
                            ${phone ? escapeHtml(phone) : '—'}
                        </span>

                    </div>
                </td>


                <td>
                    ${address ? escapeHtml(address) : '—'}
                </td>


                <td>
                    <div class="supplier-material-list">
                        <span>
                            ${escapeHtml(material)}
                        </span>
                    </div>
                </td>


                <td>
                    <span class="supplier-status ${status}">
                        <span></span>
                        ${statusText}
                    </span>
                </td>


                <td>
                    <div class="supplier-actions">

                        <button
                            type="button"
                            class="supplier-action-button edit supplier-edit-button"
                            title="Edit Supplier"
                        >
                            <svg
                                width="15"
                                height="15"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M12 20h9"/>
                                <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                            </svg>
                        </button>


                        <button
                            type="button"
                            class="supplier-action-button delete supplier-delete-button"
                            title="Hapus Supplier"
                        >
                            <svg
                                width="15"
                                height="15"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M3 6h18"/>
                                <path d="M8 6V4h8v2"/>
                                <path d="M19 6l-1 14H6L5 6"/>
                                <path d="M10 11v5"/>
                                <path d="M14 11v5"/>
                            </svg>
                        </button>

                    </div>
                </td>
            `;


            /*
            | EDIT
            */

            if (editingRow) {

                editingRow.dataset.name =
                    name;

                editingRow.dataset.email =
                    email;

                editingRow.dataset.phone =
                    phone;

                editingRow.dataset.address =
                    address;

                editingRow.dataset.materials =
                    material;

                editingRow.dataset.status =
                    status;


                editingRow.innerHTML =
                    rowHTML;

            }


            /*
            | ADD
            */

            else {

                const newRow =
                    document.createElement('tr');


                newRow.dataset.id =
                    Date.now();

                newRow.dataset.name =
                    name;

                newRow.dataset.email =
                    email;

                newRow.dataset.phone =
                    phone;

                newRow.dataset.address =
                    address;

                newRow.dataset.materials =
                    material;

                newRow.dataset.status =
                    status;


                newRow.innerHTML =
                    rowHTML;


                supplierTableBody.appendChild(
                    newRow
                );

            }


            bindEditButtons();

            bindDeleteButtons();

            updateSummary();

            filterSuppliers();

            closeModal();

        }
    );



    /*
    |--------------------------------------------------------------------------
    | Search & Filter
    |--------------------------------------------------------------------------
    */

    function filterSuppliers() {

        const keyword =
            supplierSearch.value
                .toLowerCase()
                .trim();

        const status =
            supplierStatusFilter.value;


        const rows =
            supplierTableBody.querySelectorAll(
                'tr'
            );


        let visibleRows = 0;


        rows.forEach(function (row) {

            const searchableText = (
                row.dataset.name +
                ' ' +
                row.dataset.email +
                ' ' +
                row.dataset.phone +
                ' ' +
                row.dataset.address +
                ' ' +
                row.dataset.materials
            ).toLowerCase();


            const searchMatch =
                searchableText.includes(keyword);


            const statusMatch =
                status === 'all' ||
                row.dataset.status === status;


            const show =
                searchMatch &&
                statusMatch;


            row.style.display =
                show ? '' : 'none';


            if (show) {
                visibleRows++;
            }

        });


        supplierEmpty.style.display =
            visibleRows === 0
                ? 'flex'
                : 'none';

    }


    supplierSearch.addEventListener(
        'input',
        filterSuppliers
    );


    supplierStatusFilter.addEventListener(
        'change',
        filterSuppliers
    );



    /*
    |--------------------------------------------------------------------------
    | Summary
    |--------------------------------------------------------------------------
    */

    function updateSummary() {

        const rows =
            supplierTableBody.querySelectorAll(
                'tr'
            );


        let active = 0;

        let inactive = 0;


        rows.forEach(function (row) {

            if (
                row.dataset.status === 'active'
            ) {

                active++;

            } else {

                inactive++;

            }

        });


        totalSupplier.textContent =
            rows.length;

        activeSupplier.textContent =
            active;

        inactiveSupplier.textContent =
            inactive;

    }



    /*
    |--------------------------------------------------------------------------
    | Escape HTML
    |--------------------------------------------------------------------------
    */

    function escapeHtml(value) {

        const div =
            document.createElement('div');

        div.textContent =
            value;

        return div.innerHTML;

    }



    /*
    |--------------------------------------------------------------------------
    | Initial State
    |--------------------------------------------------------------------------
    */

    bindEditButtons();

    bindDeleteButtons();

    updateSummary();

    filterSuppliers();

});
</script>

@endpush