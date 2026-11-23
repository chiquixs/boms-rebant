@extends('layouts.admin')

@section('title', 'Bill of Materials - REBANT BOMS')

@section('content')

<div class="bom-page">

    {{-- =========================================================
        PAGE HEADER
    ========================================================= --}}
        @if (session('success'))
            <div class="bom-alert bom-alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="bom-alert bom-alert-error">{{ $errors->first() }}</div>
        @endif

        <div class="bom-page-header">

            <a href="{{ route('admin.products.index') }}" class="back-to-products">
                ← Kembali ke Master Produk
            </a>

            <div class="bom-header-content">
                <div>
                    <span class="bom-eyebrow">BILL OF MATERIALS</span>
                    <h1>Resep Produksi</h1>
                    <p>Kelola kebutuhan bahan baku untuk setiap unit produk.</p>
                </div>
            </div>

        </div>


    {{-- =========================================================
        PRODUCT INFORMATION
    ========================================================= --}}
    <div class="bom-product-card">

        <div class="bom-product-info">

            <img
                src="{{ $group->image_url ?? $group->products->first(fn ($p) => $p->image_url)?->image_url ?? asset('images/login-illustration.png') }}"
                alt="{{ $group->name }}"
                class="bom-product-image"
            >

            <div class="bom-product-main">

                <span class="bom-product-label">PRODUCT</span>

                <h2>{{ $group->name }}</h2>

                <div class="bom-product-meta">
                    <span class="bom-sku">{{ $group->code }}</span>
                    <span class="production-badge {{ strtolower($group->production_method) }}">
                        {{ strtoupper($group->production_method) }}
                    </span>
                </div>

            </div>

        </div>

        <div class="bom-product-summary">

            <div class="bom-summary-item">
                <span>Kategori</span>
                <strong>{{ $group->category?->name ?? '—' }}</strong>
            </div>

            <div class="bom-summary-item">
                <span>Ukuran</span>
                <strong>
                    @if ($group->length_cm && $group->width_cm)
                        {{ (float) $group->length_cm }} × {{ (float) $group->width_cm }} cm
                    @else
                        —
                    @endif
                </strong>
            </div>

            <div class="bom-summary-item">
                <span>Jumlah Varian</span>
                <strong>{{ $group->products->count() }} Varian</strong>
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

                    @php
                        $fmt = fn ($n) => rtrim(rtrim(number_format((float) $n, 6, '.', ''), '0'), '.');
                    @endphp

                    @forelse ($group->materials as $item)

                       @php
                            $isArea = $item->usage_method === 'AREA';
                            $isInactive = ! $item->rawMaterial->is_active;
                        @endphp

                        <tr
                            data-name="{{ $item->rawMaterial->name }}"
                            data-material-id="{{ $item->raw_material_id }}"
                            data-method="{{ $isArea ? 'area' : 'fix' }}"
                            data-quantity="{{ $isArea ? '' : $fmt($item->qty_per_unit) }}"
                            data-piece-length="{{ $isArea ? (float) $item->piece_length_cm : '' }}"
                            data-piece-width="{{ $isArea ? (float) $item->piece_width_cm : '' }}"
                            data-piece-count="{{ $isArea ? $item->piece_count : '' }}"
                            data-waste-percent="{{ $isArea ? (float) $item->waste_percent : '' }}"
                            data-update-url="{{ route('admin.products.bom.update', [$group->code, $item->id]) }}"
                            data-delete-url="{{ route('admin.products.bom.destroy', [$group->code, $item->id]) }}"
                        >

                            <td>
                                <div class="material-info">
                                    <div class="material-name-line">
                                        <strong>{{ $item->rawMaterial->name }}</strong>

                                        @if ($isInactive)
                                            <span class="material-inactive-badge" title="Bahan ini sudah dinonaktifkan di Master Bahan Mentah">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </div>

                                    <span>{{ $item->rawMaterial->code }}</span>
                                </div>
                            </td>

                            <td>
                                <span class="bom-method-badge {{ $isArea ? 'area' : 'fix' }}">
                                    {{ $isArea ? 'Area' : 'Fix' }}
                                </span>
                            </td>

                            <td>
                                @if ($isArea)
                                    <div class="area-requirement">
                                        <strong>{{ (float) $item->piece_length_cm }} × {{ (float) $item->piece_width_cm }} cm</strong>
                                        <span>{{ $item->piece_count }} potongan • Waste {{ (float) $item->waste_percent }}%</span>
                                    </div>
                                @else
                                    <div class="fix-requirement">
                                        <strong>{{ $fmt($item->qty_per_unit) }}</strong>
                                    </div>
                                @endif
                            </td>

                            <td>
                                <span class="material-unit">{{ $item->rawMaterial->unit }}</span>
                            </td>

                            <td>
                                <div class="bom-actions">
                                    <button type="button" class="bom-action edit">Edit</button>
                                    <button type="button" class="bom-action delete">Hapus</button>
                                </div>
                            </td>

                        </tr>

                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center;padding:30px;">
                                BOM belum diisi. Klik "Tambah Bahan" untuk mulai.
                            </td>
                        </tr>
                    @endforelse

                    </tbody>
            </table>

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
        <form id="materialForm" method="POST" action="{{ route('admin.products.bom.store', $group->code) }}"
        data-store-url="{{ route('admin.products.bom.store', $group->code) }}">
        @csrf
        <input type="hidden" name="_method" value="PUT" id="materialMethodOverride" disabled>

            <div class="material-modal-body">

                {{-- =========================
                    RAW MATERIAL
                ========================= --}}
                <div class="material-form-group">

                    <label for="materialName">
                        Bahan Mentah
                    </label>

                    <select id="materialName" name="raw_material_id">
                        <option value="">Pilih bahan mentah</option>
                        @foreach ($rawMaterials as $raw)
                            <option
                                value="{{ $raw->id }}"
                                data-unit="{{ $raw->unit }}"
                                data-type="{{ $raw->material_type }}"
                                data-method="{{ $raw->isSheet() ? 'area' : 'fix' }}"
                                data-sheet="{{ $raw->has_sheet_size ? '1' : '0' }}"
                                data-sheet-size="{{ $raw->has_sheet_size ? ((float) $raw->sheet_length_cm) . ' × ' . ((float) $raw->sheet_width_cm) . ' cm' : '' }}"
                            >
                                {{ $raw->name }} ({{ $raw->code }})
                            </option>
                        @endforeach
                    </select>

                    <span class="material-field-helper">
                        Data bahan diambil dari Master Bahan Mentah.
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
                    METHOD (AUTO FROM RAW MATERIAL)
                ========================= --}}
                <div class="material-form-group">

                    <label>
                        Metode Kebutuhan
                    </label>

                    <div class="material-unit-preview" id="materialMethodPreview">
                        Pilih bahan terlebih dahulu
                    </div>

                    <span class="material-field-helper">
                        Metode ditentukan otomatis dari tipe bahan di Master Bahan Mentah (Non-Sheet = Fix, Sheet = Area).
                    </span>

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

                        <input type="number" id="materialQuantity" name="quantity" min="0" step="0.01" placeholder="Contoh: 2">

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

                            <input type="number" id="pieceLength"      name="piece_length_cm" min="0" step="0.01" placeholder="Contoh: 30">

                        </div>


                        <div class="material-form-group">

                            <label for="pieceWidth">
                                Lebar Potongan (cm)
                            </label>

                            <input type="number" id="pieceWidth"       name="piece_width_cm"  min="0" step="0.01" placeholder="Contoh: 9">

                        </div>


                        <div class="material-form-group">

                            <label for="pieceCount">
                                Jumlah Potongan
                            </label>

                            <input type="number" id="pieceCount"       name="piece_count"     min="1" step="1"    placeholder="Contoh: 2">

                        </div>


                        <div class="material-form-group">

                            <label for="wastePercent">
                                Waste / Toleransi (%)
                            </label>

                            <input type="number" id="wastePercent"     name="waste_percent"   min="0" max="100" step="0.01" placeholder="Contoh: 10">

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
<form id="deleteMaterialForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const materialModal = document.getElementById('materialModal');
    const materialForm = document.getElementById('materialForm');
    const methodOverride = document.getElementById('materialMethodOverride');
    const materialName = document.getElementById('materialName');
    const materialUnitPreview = document.getElementById('materialUnitPreview');
    const materialQuantity = document.getElementById('materialQuantity');
    const fixFields = document.getElementById('fixFields');
    const areaFields = document.getElementById('areaFields');
    const pieceLength = document.getElementById('pieceLength');
    const pieceWidth = document.getElementById('pieceWidth');
    const pieceCount = document.getElementById('pieceCount');
    const wastePercent = document.getElementById('wastePercent');
    const materialModalTitle = document.getElementById('materialModalTitle');
    const materialSaveButton = materialForm.querySelector('.material-save-button');
    const tableBody = document.getElementById('bomMaterialTableBody');
    const deleteForm = document.getElementById('deleteMaterialForm');

    const materialMethodPreview = document.getElementById('materialMethodPreview');

    function getSelectedOption() {
        return materialName.options[materialName.selectedIndex] || null;
    }

    function getSelectedMethod() {
        const option = getSelectedOption();

        if (!materialName.value || !option) {
            return '';
        }

        return option.dataset.method || '';
    }

    function updateMaterialUnit() {
        const option = getSelectedOption();

        if (materialName.value && option) {
            materialUnitPreview.textContent = option.dataset.unit;
            materialUnitPreview.classList.add('selected');
        } else {
            materialUnitPreview.textContent = 'Pilih bahan terlebih dahulu';
            materialUnitPreview.classList.remove('selected');
        }
    }

    function updateMaterialMethodPreview() {
        const option = getSelectedOption();

        if (!materialName.value || !option) {
            materialMethodPreview.textContent = 'Pilih bahan terlebih dahulu';
            materialMethodPreview.classList.remove('selected');
            return;
        }

        materialMethodPreview.innerHTML = '';

        const isArea = option.dataset.method === 'area';
        const badge = document.createElement('span');
        badge.className = isArea ? 'bom-method-badge area' : 'bom-method-badge fix';
        badge.textContent = isArea ? 'Area' : 'Fix';

        const desc = document.createElement('span');
        desc.style.marginLeft = '8px';

        if (isArea) {
            const sheetInfo = option.dataset.sheet === '1' && option.dataset.sheetSize
                ? ` — Ukuran lembar ${option.dataset.sheetSize}`
                : ' — Ukuran lembar belum lengkap di Master Bahan Mentah';
            desc.textContent = `Otomatis (Sheet)${sheetInfo}`;
        } else {
            desc.textContent = 'Otomatis (Non-Sheet) — Jumlah bahan tetap per unit';
        }

        materialMethodPreview.appendChild(badge);
        materialMethodPreview.appendChild(desc);
        materialMethodPreview.classList.add('selected');
    }

    // Input yang disabled tidak ikut terkirim ke server
    function updateMaterialMethodFields() {
        const method = getSelectedMethod();
        const isFix = method === 'fix';
        const isArea = method === 'area';

        fixFields.classList.toggle('hidden', !isFix);
        areaFields.classList.toggle('hidden', !isArea);

        materialQuantity.disabled = !isFix;
        pieceLength.disabled = !isArea;
        pieceWidth.disabled = !isArea;
        pieceCount.disabled = !isArea;
        wastePercent.disabled = !isArea;
    }

    function syncMaterialSelection() {
        updateMaterialUnit();
        updateMaterialMethodPreview();
        updateMaterialMethodFields();
    }

    function openModal() {
        materialModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        materialModal.classList.remove('active');
        document.body.style.overflow = '';
    }

    // MODE TAMBAH
    function openAddModal() {
        materialForm.reset();
        materialForm.action = materialForm.dataset.storeUrl;
        methodOverride.disabled = true;

        syncMaterialSelection();

        materialModalTitle.textContent = 'Tambah Bahan Baku';
        materialSaveButton.textContent = 'Simpan Bahan';

        openModal();
    }

    // MODE EDIT
    function openEditModal(row) {
        materialForm.reset();
        materialForm.action = row.dataset.updateUrl;
        methodOverride.disabled = false;

        materialName.value = row.dataset.materialId;
        syncMaterialSelection();

        const method = getSelectedMethod();

        if (method === 'fix') {
            materialQuantity.value = row.dataset.method === 'fix' ? (row.dataset.quantity || '') : '';
        } else if (method === 'area') {
            if (row.dataset.method === 'area') {
                pieceLength.value = row.dataset.pieceLength || '';
                pieceWidth.value = row.dataset.pieceWidth || '';
                pieceCount.value = row.dataset.pieceCount || '1';
                wastePercent.value = row.dataset.wastePercent || '0';
            } else {
                pieceLength.value = '';
                pieceWidth.value = '';
                pieceCount.value = '1';
                wastePercent.value = '0';
            }
        }

        materialModalTitle.textContent = 'Edit Bahan Baku';
        materialSaveButton.textContent = 'Simpan Perubahan';

        openModal();
    }

    document.getElementById('addMaterialButton').addEventListener('click', openAddModal);
    document.getElementById('closeMaterialModal').addEventListener('click', closeModal);
    document.getElementById('cancelMaterialModal').addEventListener('click', closeModal);

    materialModal.addEventListener('click', function (event) {
        if (event.target === materialModal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && materialModal.classList.contains('active')) {
            closeModal();
        }
    });

    materialName.addEventListener('change', syncMaterialSelection);

    // EDIT & HAPUS
    tableBody.addEventListener('click', function (event) {

        const row = event.target.closest('tr');

        if (!row || !row.dataset.updateUrl) {
            return;
        }

        if (event.target.closest('.bom-action.edit')) {
            openEditModal(row);
            return;
        }

        if (event.target.closest('.bom-action.delete')) {
            if (confirm(`Yakin ingin menghapus ${row.dataset.name} dari BOM?`)) {
                deleteForm.action = row.dataset.deleteUrl;
                deleteForm.submit();
            }
        }
    });

    // Validasi ringan sebelum kirim (validasi utama ada di server)
    materialForm.addEventListener('submit', function (event) {

        if (!materialName.value) {
            event.preventDefault();
            alert('Silakan pilih bahan mentah.');
            return;
        }

        const option = getSelectedOption();

        if (getSelectedMethod() === 'area' && (!option || option.dataset.sheet !== '1')) {
            event.preventDefault();
            alert('Bahan tipe lembar (Area) belum memiliki ukuran lembar. Lengkapi ukuran lembar di Master Bahan Mentah.');
        }
    });

    syncMaterialSelection();

});
</script>

@endpush