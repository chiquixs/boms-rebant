@extends('layouts.admin')

@section('title', 'Supplier')

@section('content')

<div class="suppliers-page">

    <div class="suppliers-page-header">
        <div>
            <h1>Supplier</h1>
            <p>Kelola data supplier untuk kebutuhan procurement bahan baku.</p>
        </div>

        <button type="button" class="supplier-add-button" id="openSupplierModal">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 5v14"/>
                <path d="M5 12h14"/>
            </svg>
            Tambah Supplier
        </button>
    </div>

    <div class="supplier-summary">
        <div class="supplier-summary-card">
            <span class="supplier-summary-label">Total Supplier</span>
            <strong class="supplier-summary-value">{{ $suppliers->count() }}</strong>
            <span class="supplier-summary-caption">supplier terdaftar</span>
        </div>

        <div class="supplier-summary-card">
            <span class="supplier-summary-label">Supplier Aktif</span>
            <strong class="supplier-summary-value">{{ $activeCount }}</strong>
            <span class="supplier-summary-caption">dapat digunakan untuk procurement</span>
        </div>

        <div class="supplier-summary-card">
            <span class="supplier-summary-label">Supplier Nonaktif</span>
            <strong class="supplier-summary-value">{{ $inactiveCount }}</strong>
            <span class="supplier-summary-caption">tidak tersedia untuk pemesanan</span>
        </div>
    </div>

    <div class="suppliers-card">

        <div class="suppliers-card-header">
            <div>
                <h2>Daftar Supplier</h2>
                <p>Supplier yang tersedia untuk pemesanan bahan baku.</p>
            </div>
        </div>

        <div class="suppliers-toolbar">
            <div class="suppliers-search">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="11" cy="11" r="7"/>
                    <path d="m20 20-3.5-3.5"/>
                </svg>
                <input type="text" id="supplierSearch" placeholder="Cari supplier...">
            </div>

            <select class="suppliers-filter" id="supplierStatusFilter">
                <option value="all">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="inactive">Nonaktif</option>
            </select>
        </div>

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
                    @forelse ($suppliers as $supplier)
                        @php
                            $materialsPayload = $supplier->materials->map(fn ($m) => [
                                'raw_material_id' => $m->raw_material_id,
                                'name'            => $m->rawMaterial->name ?? '—',
                                'unit'            => $m->rawMaterial->unit ?? '',
                                'unit_price'      => (float) $m->unit_price,
                                'lead_time_days'  => (int) $m->lead_time_days,
                                'min_order_qty'   => (float) $m->min_order_qty,
                            ])->values();
                        @endphp

                        <tr
                            data-id="{{ $supplier->id }}"
                            data-name="{{ $supplier->name }}"
                            data-email="{{ $supplier->email }}"
                            data-phone="{{ $supplier->phone }}"
                            data-address="{{ $supplier->address }}"
                            data-status="{{ $supplier->is_active ? 'active' : 'inactive' }}"
                            data-update-url="{{ route('admin.procurement.suppliers.update', $supplier) }}"
                            data-materials='@json($materialsPayload)'
                        >
                            <td>
                                <div class="supplier-name-cell">
                                    <div class="supplier-avatar">{{ strtoupper(substr(preg_replace('/^(PT|CV)\s+/i', '', $supplier->name), 0, 1)) }}</div>
                                    <div>
                                        <strong>{{ $supplier->name }}</strong>
                                        <span>Supplier bahan baku</span>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="supplier-contact-cell">
                                    <span>{{ $supplier->email ?: '—' }}</span>
                                    <span>{{ $supplier->phone ?: '—' }}</span>
                                </div>
                            </td>

                            <td>{{ $supplier->address ?: '—' }}</td>

                            <td>
                                <div class="supplier-material-list">
                                    @forelse ($supplier->materials as $m)
                                        <span>{{ $m->rawMaterial->name ?? '—' }}</span>
                                    @empty
                                        <span class="empty-value">—</span>
                                    @endforelse
                                </div>
                            </td>

                            <td>
                                <span class="supplier-status {{ $supplier->is_active ? 'active' : 'inactive' }}">
                                    <span></span>
                                    {{ $supplier->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>

                            <td>
                                <div class="supplier-actions">
                                    <button type="button" class="supplier-action-button edit supplier-edit-button" title="Edit Supplier">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M12 20h9"/>
                                            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                        </svg>
                                    </button>

                                    <form action="{{ route('admin.procurement.suppliers.destroy', $supplier) }}" method="POST"
                                          class="supplier-delete-form" data-name="{{ $supplier->name }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="supplier-action-button delete" title="Hapus Supplier">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path d="M3 6h18"/>
                                                <path d="M8 6V4h8v2"/>
                                                <path d="M19 6l-1 14H6L5 6"/>
                                                <path d="M10 11v5"/>
                                                <path d="M14 11v5"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:24px;">Belum ada supplier.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="suppliers-empty" id="supplierEmpty" style="display:none;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <circle cx="11" cy="11" r="7"/>
                    <path d="m20 20-3.5-3.5"/>
                </svg>
                <strong>Supplier tidak ditemukan</strong>
                <span>Tidak ada supplier yang sesuai dengan pencarian atau filter.</span>
            </div>
        </div>
    </div>
</div>


{{-- TOAST --}}
<div class="toast-container" id="toastContainer"
     data-success="{{ session('success') }}"
     data-error="{{ session('error') }}"></div>


{{-- ADD / EDIT SUPPLIER MODAL --}}
<div
    class="supplier-modal-overlay"
    id="supplierModal"
    data-store-url="{{ route('admin.procurement.suppliers.store') }}"
    data-has-error="{{ $errors->any() ? '1' : '0' }}"
    data-old-edit-id="{{ old('edit_id') }}"
    data-old-edit-url="{{ old('edit_id') ? route('admin.procurement.suppliers.update', old('edit_id')) : '' }}"
    data-raw-materials='@json($rawMaterials->map(fn ($m) => ["id" => $m->id, "name" => $m->name, "unit" => $m->unit]))'
>

    <div class="supplier-modal">

        <div class="supplier-modal-header">
            <div>
                <h2 id="supplierModalTitle">Tambah Supplier</h2>
                <p id="supplierModalDescription">Tambahkan supplier baru untuk kebutuhan procurement.</p>
            </div>

            <button type="button" class="supplier-modal-close" id="closeSupplierModal" aria-label="Tutup modal">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M18 6 6 18"/>
                    <path d="m6 6 12 12"/>
                </svg>
            </button>
        </div>

        <div class="supplier-modal-body">

            <form id="supplierForm" method="POST" action="{{ route('admin.procurement.suppliers.store') }}">
                @csrf
                <input type="hidden" name="_method" id="supplierMethod" value="PUT" disabled>
                <input type="hidden" name="edit_id" id="supplierEditId" value="{{ old('edit_id') }}">

                @if ($errors->any())
                    <div style="margin-bottom:16px;padding:10px 12px;background:#FDECEA;color:#C0392B;border-radius:10px;font-size:13px;">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="supplier-form-group">
                    <label for="supplierName">Nama Supplier <span>*</span></label>
                    <input type="text" id="supplierName" name="name" value="{{ old('name') }}"
                           placeholder="Contoh: PT Kayu Jaya" required>
                </div>

                <div class="supplier-form-group">
                    <label for="supplierEmail">Email Supplier</label>
                    <input type="email" id="supplierEmail" name="email" value="{{ old('email') }}"
                           placeholder="supplier@email.com">
                    <small>Email ini akan digunakan untuk pengiriman Purchase Order.</small>
                </div>

                <div class="supplier-form-group">
                    <label for="supplierPhone">Nomor Telepon</label>
                    <input type="text" id="supplierPhone" name="phone" value="{{ old('phone') }}"
                           placeholder="Contoh: 0812-3456-7890">
                </div>

                <div class="supplier-form-group">
                    <label for="supplierAddress">Alamat</label>
                    <textarea id="supplierAddress" name="address" rows="3"
                              placeholder="Masukkan alamat supplier...">{{ old('address') }}</textarea>
                </div>

                {{-- ================= BAHAN YANG DISUPPLY (MULTI) ================= --}}
                <div class="supplier-form-group">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <label style="margin:0;">Bahan yang Disupply <span>*</span></label>
                        <button type="button" class="add-product-button" id="addSupplierMaterialButton">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 5v14"/><path d="M5 12h14"/>
                            </svg>
                            Tambah Bahan
                        </button>
                    </div>

                    <div id="supplierMaterials" style="margin-top:10px; display:flex; flex-direction:column; gap:10px;"></div>

                    <small>Minimal satu bahan harus dipilih.</small>
                </div>

                <div class="supplier-form-group">
                    <label for="supplierStatus">Status <span>*</span></label>
                    <select id="supplierStatus" name="is_active" required>
                        <option value="1" @selected(old('is_active', '1') === '1')>Aktif</option>
                        <option value="0" @selected(old('is_active') === '0')>Nonaktif</option>
                    </select>
                </div>

            </form>

        </div>

        <div class="supplier-modal-footer">
            <button type="button" class="supplier-button secondary" id="cancelSupplierButton">Batal</button>
            <button type="submit" form="supplierForm" class="supplier-button primary" id="saveSupplierButton">Simpan Supplier</button>
        </div>
    </div>
</div>

@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const modal        = document.getElementById('supplierModal');
    const openButton    = document.getElementById('openSupplierModal');
    const closeButton   = document.getElementById('closeSupplierModal');
    const cancelButton  = document.getElementById('cancelSupplierButton');
    const form          = document.getElementById('supplierForm');
    const methodInput   = document.getElementById('supplierMethod');
    const editIdInput   = document.getElementById('supplierEditId');
    const modalTitle    = document.getElementById('supplierModalTitle');
    const modalDesc     = document.getElementById('supplierModalDescription');

    const nameInput    = document.getElementById('supplierName');
    const emailInput   = document.getElementById('supplierEmail');
    const phoneInput   = document.getElementById('supplierPhone');
    const addressInput = document.getElementById('supplierAddress');
    const statusInput  = document.getElementById('supplierStatus');

    const materialsContainer = document.getElementById('supplierMaterials');
    const addMaterialButton  = document.getElementById('addSupplierMaterialButton');

    const storeUrl   = modal.dataset.storeUrl;
    const hasError   = modal.dataset.hasError === '1';
    const oldEditId  = modal.dataset.oldEditId;
    const oldEditUrl = modal.dataset.oldEditUrl;
    const rawMaterials = JSON.parse(modal.dataset.rawMaterials || '[]');

    const materialOptionsHtml =
        '<option value="">Pilih bahan</option>' +
        rawMaterials.map(m => `<option value="${m.id}" data-unit="${m.unit}">${m.name}</option>`).join('');

    function createMaterialRow(values = {}) {
        const row = document.createElement('div');
        row.className = 'supplier-material-row';
        row.style.cssText = 'display:grid; grid-template-columns: 2fr 1fr 1fr 1fr auto; gap:8px; align-items:center;';

        row.innerHTML = `
            <select class="material-select" required>${materialOptionsHtml}</select>
            <input type="number" class="material-price" min="0" step="1" placeholder="Harga/unit" required>
            <input type="number" class="material-lead" min="0" step="1" placeholder="Lead time (hari)">
            <input type="number" class="material-min" min="0" step="0.01" placeholder="Min order qty">
            <button type="button" class="remove-item-button" title="Hapus bahan">&times;</button>
        `;

        materialsContainer.appendChild(row);

        const select = row.querySelector('.material-select');
        if (values.raw_material_id) select.value = values.raw_material_id;
        if (values.unit_price !== undefined) row.querySelector('.material-price').value = values.unit_price;
        if (values.lead_time_days !== undefined) row.querySelector('.material-lead').value = values.lead_time_days;
        if (values.min_order_qty !== undefined) row.querySelector('.material-min').value = values.min_order_qty;
    }

    addMaterialButton.addEventListener('click', () => createMaterialRow());

    materialsContainer.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-item-button');
        if (!btn) return;
        if (materialsContainer.querySelectorAll('.supplier-material-row').length <= 1) return;
        btn.closest('.supplier-material-row').remove();
    });

    function clearMaterialRows() {
        materialsContainer.innerHTML = '';
    }

    function showModal() {
        modal.classList.add('show');
        document.body.classList.add('modal-open');
        setTimeout(() => nameInput.focus(), 100);
    }

    function closeModal() {
        modal.classList.remove('show');
        document.body.classList.remove('modal-open');
    }

    function setAddMode() {
        form.action = storeUrl;
        methodInput.disabled = true;
        editIdInput.value = '';
        modalTitle.textContent = 'Tambah Supplier';
        modalDesc.textContent = 'Tambahkan supplier baru untuk kebutuhan procurement.';
    }

    function setEditMode(url, id) {
        form.action = url;
        methodInput.disabled = false;
        editIdInput.value = id;
        modalTitle.textContent = 'Edit Supplier';
        modalDesc.textContent = 'Perbarui informasi supplier yang dipilih.';
    }

    openButton.addEventListener('click', function () {
        setAddMode();
        nameInput.value = ''; emailInput.value = ''; phoneInput.value = '';
        addressInput.value = ''; statusInput.value = '1';
        clearMaterialRows();
        createMaterialRow();
        showModal();
    });

    closeButton.addEventListener('click', closeModal);
    cancelButton.addEventListener('click', closeModal);
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape' && modal.classList.contains('show')) closeModal(); });

    document.getElementById('supplierTableBody').addEventListener('click', function (e) {
        const editBtn = e.target.closest('.supplier-edit-button');
        if (!editBtn) return;

        const row = editBtn.closest('tr');
        setEditMode(row.dataset.updateUrl, row.dataset.id);

        nameInput.value    = row.dataset.name || '';
        emailInput.value   = row.dataset.email || '';
        phoneInput.value   = row.dataset.phone || '';
        addressInput.value = row.dataset.address || '';
        statusInput.value  = row.dataset.status === 'active' ? '1' : '0';

        clearMaterialRows();
        const materials = JSON.parse(row.dataset.materials || '[]');
        if (materials.length === 0) {
            createMaterialRow();
        } else {
            materials.forEach(m => createMaterialRow(m));
        }

        showModal();
    });

    document.getElementById('supplierTableBody').addEventListener('submit', function (e) {
        const deleteForm = e.target.closest('.supplier-delete-form');
        if (!deleteForm) return;
        if (!confirm('Yakin ingin menghapus ' + deleteForm.dataset.name + '?')) {
            e.preventDefault();
        }
    });

    // Sebelum submit: validasi minimal 1 bahan lengkap + beri nama input sesuai index array
    form.addEventListener('submit', function (e) {
        const rows = Array.from(materialsContainer.querySelectorAll('.supplier-material-row'));

        const incomplete = rows.some(r => !r.querySelector('.material-select').value || !r.querySelector('.material-price').value);
        if (incomplete) {
            e.preventDefault();
            alert('Lengkapi bahan dan harga di setiap baris, atau hapus baris yang belum lengkap.');
            return;
        }

        // Bersihkan name lama (kalau submit gagal lalu disubmit lagi tanpa reload)
        materialsContainer.querySelectorAll('[name]').forEach(el => el.removeAttribute('name'));

        rows.forEach((row, index) => {
            row.querySelector('.material-select').name = `materials[${index}][raw_material_id]`;
            row.querySelector('.material-price').name  = `materials[${index}][unit_price]`;
            row.querySelector('.material-lead').name   = `materials[${index}][lead_time_days]`;
            row.querySelector('.material-min').name     = `materials[${index}][min_order_qty]`;
        });
    });

    // ---------- SEARCH + FILTER ----------
    const searchInput = document.getElementById('supplierSearch');
    const statusFilter = document.getElementById('supplierStatusFilter');
    const tableBody = document.getElementById('supplierTableBody');
    const emptyState = document.getElementById('supplierEmpty');

    function filterSuppliers() {
        const keyword = searchInput.value.trim().toLowerCase();
        const status = statusFilter.value;
        const rows = tableBody.querySelectorAll('tr[data-id]');
        let visible = 0;

        rows.forEach(row => {
            const haystack = (
                (row.dataset.name || '') + ' ' +
                (row.dataset.email || '') + ' ' +
                (row.dataset.address || '')
            ).toLowerCase();

            const matchSearch = haystack.includes(keyword);
            const matchStatus = status === 'all' || row.dataset.status === status;
            const show = matchSearch && matchStatus;

            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        emptyState.style.display = visible === 0 ? 'flex' : 'none';
    }

    searchInput.addEventListener('input', filterSuppliers);
    statusFilter.addEventListener('change', filterSuppliers);
    filterSuppliers();

    // ---------- TOAST (sama seperti halaman Material) ----------
    const toastContainer = document.getElementById('toastContainer');
    const TOAST_DURATION = 4000;

    function showToast(type, message) {
        if (!message) return;
        const toast = document.createElement('div');
        toast.className = 'toast ' + type;
        toast.innerHTML =
            '<div class="toast-body"><div class="toast-title">' + (type === 'success' ? 'Berhasil' : 'Gagal') +
            '</div><div class="toast-message"></div></div><button type="button" class="toast-close">&times;</button>';
        toast.querySelector('.toast-message').textContent = message;
        toastContainer.appendChild(toast);
        const timer = setTimeout(() => toast.remove(), TOAST_DURATION);
        toast.querySelector('.toast-close').addEventListener('click', () => { clearTimeout(timer); toast.remove(); });
    }

    showToast('success', toastContainer.dataset.success);
    showToast('error', toastContainer.dataset.error);

    // ---------- REOPEN MODAL ON VALIDATION ERROR ----------
    if (hasError) {
        if (oldEditId && oldEditUrl) {
            setEditMode(oldEditUrl, oldEditId);
        } else {
            setAddMode();
        }
        clearMaterialRows();
        createMaterialRow(); // catatan: input bahan lama TIDAK di-refill otomatis, lihat catatan di bawah
        showModal();
    }
});
</script>
@endpush