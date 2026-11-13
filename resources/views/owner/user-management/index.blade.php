
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
            <p>Monitor pengguna dan hak akses pada sistem REBANT BOMS.</p>
        </div>
        <div class="page-date">
            <span>Last updated</span>
            <strong>13 November 2026</strong>
        </div>
    </div>

    <div class="user-stats">
        <div class="user-stat-card">
            <div class="stat-icon total-icon">♙</div>
            <div><span>Total Pengguna</span><strong id="totalUsers">0</strong><small>Seluruh akun terdaftar</small></div>
        </div>
        <div class="user-stat-card">
            <div class="stat-icon owner-icon">♛</div>
            <div><span>Business Owner</span><strong id="ownerUsers">0</strong><small>Akses monitoring</small></div>
        </div>
        <div class="user-stat-card">
            <div class="stat-icon admin-icon">▣</div>
            <div><span>Admin</span><strong id="adminUsers">0</strong><small>Akses administrasi</small></div>
        </div>
        <div class="user-stat-card">
            <div class="stat-icon production-icon">⚙</div>
            <div><span>Production</span><strong id="productionUsers">0</strong><small>Akses produksi</small></div>
        </div>
    </div>

    <div class="access-info">
        <div class="access-info-icon">ⓘ</div>
        <div>
            <strong>Informasi Hak Akses</strong>
            <p>Halaman ini hanya digunakan untuk memantau akun dan hak akses pengguna. Pengelolaan akun dilakukan oleh pihak yang memiliki kewenangan administrasi.</p>
        </div>
    </div>

    <div class="user-table-card">
        <div class="table-heading">
            <div>
                <h2>Daftar Pengguna</h2>
                <p>Informasi seluruh pengguna yang terdaftar dalam sistem.</p>
            </div>
            <div class="export-actions">
                <button type="button" id="exportExcel" class="export-button">↓ Excel</button>
                <button type="button" id="exportCsv" class="export-button">↓ CSV</button>
            </div>
        </div>

        <div class="user-filters">
            <div class="user-search">
                <span>⌕</span>
                <input type="text" id="userSearch" placeholder="Cari nama atau email pengguna...">
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
            <button type="button" id="resetUserFilter" class="reset-filter">Reset</button>
        </div>

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

    <div class="role-access-section">
        <div class="section-heading">
            <h2>Ringkasan Hak Akses</h2>
            <p>Gambaran kewenangan setiap role dalam sistem.</p>
        </div>

        <div class="role-access-grid">
            <div class="role-access-card">
                <div class="role-card-heading">
                    <span class="role-symbol owner-symbol">♛</span>
                    <div><strong>Business Owner</strong><small>Monitoring & Evaluasi</small></div>
                </div>
                <ul>
                    <li>Melihat dashboard bisnis</li>
                    <li>Melihat laporan penjualan</li>
                    <li>Memantau log aktivitas</li>
                    <li>Memantau pengguna dan hak akses</li>
                </ul>
            </div>

            <div class="role-access-card">
                <div class="role-card-heading">
                    <span class="role-symbol admin-symbol">▣</span>
                    <div><strong>Admin</strong><small>Administrasi Operasional</small></div>
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
                    <div><strong>Production</strong><small>Operasional Produksi</small></div>
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

<div class="user-modal" id="userModal">
    <div class="user-modal-backdrop" id="closeUserBackdrop"></div>
    <div class="user-modal-content">
        <div class="modal-heading">
            <div>
                <span>USER INFORMATION</span>
                <h2 id="modalUserName">Detail Pengguna</h2>
            </div>
            <button type="button" id="closeUserModal" class="modal-close">×</button>
        </div>
        <div id="userModalBody"></div>
    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/owner-user-management.js')
@endpush
