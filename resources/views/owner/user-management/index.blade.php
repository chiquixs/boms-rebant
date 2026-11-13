
@extends('layouts.owner')

@section('title', 'User & Access Management - REBANT BOMS')

@push('styles')
    @vite('resources/css/owner-user-management.css')
@endpush

@section('content')
<div class="user-management-page">

    <div class="page-heading">
        <div>
            <span class="page-eyebrow">MANAGEMENT</span>
            <h1>User & Access Management</h1>
            <p>Kelola akun pengguna dan hak akses sistem REBANT BOMS.</p>
        </div>
        <div class="page-date">
            <span>Last updated</span>
            <strong>13 November 2026</strong>
        </div>
    </div>

    {{-- SUMMARY --}}
    <div class="user-stats">
        <div class="user-stat-card">
            <div class="stat-icon total-icon">♙</div>
            <div>
                <span>Total Pengguna</span>
                <strong id="totalUsers">0</strong>
                <small>Seluruh akun terdaftar</small>
            </div>
        </div>

        <div class="user-stat-card">
            <div class="stat-icon owner-icon">♛</div>
            <div>
                <span>Business Owner</span>
                <strong id="ownerUsers">0</strong>
                <small>Akses pemilik bisnis</small>
            </div>
        </div>

        <div class="user-stat-card">
            <div class="stat-icon admin-icon">▣</div>
            <div>
                <span>Admin</span>
                <strong id="adminUsers">0</strong>
                <small>Akses administrasi</small>
            </div>
        </div>

        <div class="user-stat-card">
            <div class="stat-icon production-icon">⚙</div>
            <div>
                <span>Production</span>
                <strong id="productionUsers">0</strong>
                <small>Akses produksi</small>
            </div>
        </div>
    </div>

    {{-- ACCESS INFO --}}
    <div class="access-info">
        <div class="access-info-icon">ⓘ</div>
        <div>
            <strong>Manajemen Pengguna</strong>
            <p>Business Owner dapat menambahkan, mengubah, dan menghapus akun pengguna serta mengatur role dan status akun.</p>
        </div>
    </div>

    {{-- USER TABLE --}}
    <div class="user-table-card">

        <div class="table-heading">
            <div>
                <h2>Daftar Pengguna</h2>
                <p>Kelola seluruh akun yang terdaftar dalam sistem.</p>
            </div>

            <div class="table-heading-actions">
                <div class="export-actions">
                    <button type="button" id="exportExcel" class="export-button">
                        ↓ Excel
                    </button>
                    <button type="button" id="exportCsv" class="export-button">
                        ↓ CSV
                    </button>
                </div>

                <button type="button" id="addUserButton" class="add-user-button">
                    <span>＋</span> Tambah Pengguna
                </button>
            </div>
        </div>

        {{-- FILTERS --}}
        <div class="user-filters">
            <div class="user-search">
                <span>⌕</span>
                <input type="text" id="userSearch"
                    placeholder="Cari nama, email, atau ID pengguna...">
            </div>

            <select id="roleFilter">
                <option value="all">Semua Role</option>
                <option value="Owner">Business Owner</option>
                <option value="Admin">Admin</option>
                <option value="Production">Production</option>
            </select>

            <select id="statusFilter">
                <option value="all">Semua Status</option>
                <option value="Aktif">Aktif</option>
                <option value="Nonaktif">Nonaktif</option>
            </select>

            <button type="button" id="resetUserFilter" class="reset-filter">
                Reset
            </button>
        </div>

        {{-- TABLE --}}
        <div class="table-responsive">
            <table class="user-table">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Role</th>
                        <th>Tanggal Bergabung</th>
                        <th>Status Akun</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="userTableBody"></tbody>
            </table>
        </div>

        <div class="table-footer">
            <span id="userCount">Menampilkan 0 pengguna</span>
            <div id="userPagination" class="user-pagination"></div>
        </div>
    </div>

    {{-- ROLE ACCESS --}}
    <div class="role-access-section">
        <div class="section-heading">
            <h2>Ringkasan Hak Akses</h2>
            <p>Gambaran kewenangan setiap role dalam sistem.</p>
        </div>

        <div class="role-access-grid">
            <div class="role-access-card">
                <div class="role-card-heading">
                    <span class="role-symbol owner-symbol">♛</span>
                    <div>
                        <strong>Business Owner</strong>
                        <small>Monitoring & Manajemen</small>
                    </div>
                </div>
                <ul>
                    <li>Melihat dashboard bisnis</li>
                    <li>Melihat laporan penjualan</li>
                    <li>Memantau log aktivitas</li>
                    <li>Mengelola akun pengguna</li>
                </ul>
            </div>

            <div class="role-access-card">
                <div class="role-card-heading">
                    <span class="role-symbol admin-symbol">▣</span>
                    <div>
                        <strong>Admin</strong>
                        <small>Administrasi Operasional</small>
                    </div>
                </div>
                <ul>
                    <li>Mengelola data pesanan</li>
                    <li>Mengelola data produk</li>
                    <li>Mengelola data bahan baku</li>
                    <li>Mencatat transaksi operasional</li>
                </ul>
            </div>

            <div class="role-access-card">
                <div class="role-card-heading">
                    <span class="role-symbol production-symbol">⚙</span>
                    <div>
                        <strong>Production</strong>
                        <small>Operasional Produksi</small>
                    </div>
                </div>
                <ul>
                    <li>Melihat daftar pekerjaan produksi</li>
                    <li>Memperbarui status produksi</li>
                    <li>Memantau kebutuhan bahan baku</li>
                    <li>Mencatat hasil produksi</li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- ADD / EDIT USER MODAL --}}
<div class="user-modal" id="userFormModal">
    <div class="user-modal-backdrop" data-close-form></div>

    <div class="user-modal-content">
        <div class="modal-heading">
            <div>
                <span>USER MANAGEMENT</span>
                <h2 id="userFormTitle">Tambah Pengguna</h2>
                <p id="userFormDescription">Tambahkan akun pengguna baru ke sistem.</p>
            </div>
            <button type="button" class="modal-close" data-close-form>×</button>
        </div>

        <form id="userForm" novalidate>
            <input type="hidden" id="editingUserId">

            <div class="form-group">
                <label for="userNameInput">Nama Lengkap <span>*</span></label>
                <input type="text" id="userNameInput"
                    placeholder="Masukkan nama lengkap">
                <small class="form-error" id="nameError"></small>
            </div>

            <div class="form-group">
                <label for="userEmailInput">Email <span>*</span></label>
                <input type="email" id="userEmailInput"
                    placeholder="contoh@email.com">
                <small class="form-error" id="emailError"></small>
            </div>

            <div class="form-group" id="passwordGroup">
                <label for="userPasswordInput">Password <span>*</span></label>
                <input type="password" id="userPasswordInput"
                    placeholder="Masukkan password" autocomplete="new-password">
                <small class="form-hint">Minimal 8 karakter. Hanya untuk simulasi frontend.</small>
                <small class="form-error" id="passwordError"></small>
            </div>

            <div class="form-group">
                <label for="userRoleInput">Role <span>*</span></label>
                <select id="userRoleInput">
                    <option value="">Pilih role pengguna</option>
                    <option value="Owner">Business Owner</option>
                    <option value="Admin">Admin</option>
                    <option value="Production">Production</option>
                </select>
                <small class="form-error" id="roleError"></small>
            </div>

            <div class="form-group">
                <label for="userStatusInput">Status Akun <span>*</span></label>
                <select id="userStatusInput">
                    <option value="Aktif">Aktif</option>
                    <option value="Nonaktif">Nonaktif</option>
                </select>
            </div>

            <div class="modal-form-actions">
                <button type="button" class="cancel-button" data-close-form>
                    Batal
                </button>
                <button type="submit" class="save-button" id="saveUserButton">
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

{{-- USER DETAIL MODAL --}}
<div class="user-modal" id="userDetailModal">
    <div class="user-modal-backdrop" data-close-detail></div>

    <div class="user-modal-content">
        <div class="modal-heading">
            <div>
                <span>USER INFORMATION</span>
                <h2>Detail Pengguna</h2>
            </div>
            <button type="button" class="modal-close" data-close-detail>×</button>
        </div>

        <div id="userDetailBody"></div>
    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/owner-user-management.js')
@endpush
