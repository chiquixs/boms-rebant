
/* =========================================
   USER MANAGEMENT - DUMMY DATA
========================================= */

let users = [
    { id: 'USR-001', name: 'Alyfa Zahra', email: 'owner@rebant.com', role: 'Owner', joined: '2026-01-10', status: 'Aktif' },
    { id: 'USR-002', name: 'Andi Pratama', email: 'andi@rebant.com', role: 'Admin', joined: '2026-01-12', status: 'Aktif' },
    { id: 'USR-003', name: 'Nadia Putri', email: 'nadia@rebant.com', role: 'Production', joined: '2026-01-15', status: 'Aktif' },
    { id: 'USR-004', name: 'Rizky Ramadhan', email: 'rizky@rebant.com', role: 'Production', joined: '2026-02-01', status: 'Aktif' },
    { id: 'USR-005', name: 'Sinta Maharani', email: 'sinta@rebant.com', role: 'Admin', joined: '2026-02-08', status: 'Aktif' },
    { id: 'USR-006', name: 'Dimas Saputra', email: 'dimas@rebant.com', role: 'Production', joined: '2026-03-11', status: 'Nonaktif' },
    { id: 'USR-007', name: 'Putri Amelia', email: 'putri@rebant.com', role: 'Production', joined: '2026-03-15', status: 'Aktif' },
    { id: 'USR-008', name: 'Fajar Nugraha', email: 'fajar@rebant.com', role: 'Admin', joined: '2026-04-02', status: 'Aktif' },
    { id: 'USR-009', name: 'Rani Oktaviani', email: 'rani@rebant.com', role: 'Production', joined: '2026-04-12', status: 'Aktif' },
    { id: 'USR-010', name: 'Bima Aditya', email: 'bima@rebant.com', role: 'Production', joined: '2026-05-03', status: 'Nonaktif' },
    { id: 'USR-011', name: 'Dewi Lestari', email: 'dewi@rebant.com', role: 'Admin', joined: '2026-05-17', status: 'Aktif' },
    { id: 'USR-012', name: 'Yoga Firmansyah', email: 'yoga@rebant.com', role: 'Production', joined: '2026-06-01', status: 'Aktif' },
    { id: 'USR-013', name: 'Naufal Akbar', email: 'naufal@rebant.com', role: 'Production', joined: '2026-06-19', status: 'Aktif' },
    { id: 'USR-014', name: 'Citra Wulandari', email: 'citra@rebant.com', role: 'Admin', joined: '2026-07-04', status: 'Aktif' },
    { id: 'USR-015', name: 'Galih Prakoso', email: 'galih@rebant.com', role: 'Production', joined: '2026-07-20', status: 'Aktif' }
];

/* =========================================
   DOM ELEMENTS
========================================= */

const tableBody = document.getElementById('userTableBody');
const searchInput = document.getElementById('userSearch');
const roleFilter = document.getElementById('roleFilter');
const statusFilter = document.getElementById('statusFilter');
const pagination = document.getElementById('userPagination');
const userCount = document.getElementById('userCount');

const formModal = document.getElementById('userFormModal');
const detailModal = document.getElementById('userDetailModal');

const userForm = document.getElementById('userForm');
const editingUserId = document.getElementById('editingUserId');

const nameInput = document.getElementById('userNameInput');
const emailInput = document.getElementById('userEmailInput');
const passwordInput = document.getElementById('userPasswordInput');
const roleInput = document.getElementById('userRoleInput');
const statusInput = document.getElementById('userStatusInput');

let filteredUsers = [...users];
let currentPage = 1;
const rowsPerPage = 10;

/* =========================================
   HELPER FUNCTIONS
========================================= */

function escapeHTML(value) {
    return String(value).replace(/[&<>"']/g, char => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    })[char]);
}

function formatDate(date) {
    return new Date(date + 'T00:00:00').toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    });
}

function getInitials(name) {
    return name.split(' ')
        .filter(Boolean)
        .map(part => part[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
}

function getRoleLabel(role) {
    return role === 'Owner' ? 'Business Owner' : role;
}

function generateUserId() {
    const maxId = users.reduce((max, user) => {
        const number = parseInt(user.id.replace('USR-', ''), 10);
        return Math.max(max, number);
    }, 0);

    return `USR-${String(maxId + 1).padStart(3, '0')}`;
}

/* =========================================
   SUMMARY
========================================= */

function renderSummary() {
    document.getElementById('totalUsers').textContent = users.length;

    document.getElementById('ownerUsers').textContent =
        users.filter(user => user.role === 'Owner').length;

    document.getElementById('adminUsers').textContent =
        users.filter(user => user.role === 'Admin').length;

    document.getElementById('productionUsers').textContent =
        users.filter(user => user.role === 'Production').length;
}

/* =========================================
   TABLE RENDER
========================================= */

function renderTable() {
    const totalPages = Math.ceil(filteredUsers.length / rowsPerPage);
    const startIndex = (currentPage - 1) * rowsPerPage;

    const pageUsers = filteredUsers.slice(
        startIndex,
        startIndex + rowsPerPage
    );

    if (pageUsers.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="empty-row">
                    Tidak ada pengguna yang sesuai dengan filter.
                </td>
            </tr>
        `;
    } else {
        tableBody.innerHTML = pageUsers.map(user => `
            <tr>
                <td>
                    <div class="user-profile">
                        <div class="user-avatar">
                            ${escapeHTML(getInitials(user.name))}
                        </div>
                        <div class="user-profile-info">
                            <strong>${escapeHTML(user.name)}</strong>
                            <small>${escapeHTML(user.email)}</small>
                        </div>
                    </div>
                </td>

                <td>
                    <span class="role-badge role-${user.role.toLowerCase()}">
                        ${escapeHTML(getRoleLabel(user.role))}
                    </span>
                </td>

                <td>${formatDate(user.joined)}</td>

                <td>
                    <span class="status-badge ${user.status === 'Aktif' ? 'status-active' : 'status-inactive'}">
                        ${escapeHTML(user.status)}
                    </span>
                </td>

                <td>
                    <div class="table-action-buttons">
                        <button type="button"
                            class="detail-button"
                            data-action="detail"
                            data-user-id="${escapeHTML(user.id)}">
                            Detail
                        </button>

                        <button type="button"
                            class="edit-button"
                            data-action="edit"
                            data-user-id="${escapeHTML(user.id)}">
                            Edit
                        </button>

                        <button type="button"
                            class="delete-button"
                            data-action="delete"
                            data-user-id="${escapeHTML(user.id)}">
                            Hapus
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    const from = filteredUsers.length === 0 ? 0 : startIndex + 1;
    const to = Math.min(startIndex + rowsPerPage, filteredUsers.length);

    userCount.textContent =
        `Menampilkan ${from}-${to} dari ${filteredUsers.length} pengguna`;

    renderPagination(totalPages);
}

function renderPagination(totalPages) {
    if (totalPages <= 1) {
        pagination.innerHTML = '';
        return;
    }

    let buttons = `
        <button class="page-button"
            data-page="${currentPage - 1}"
            ${currentPage === 1 ? 'disabled' : ''}>
            ‹
        </button>
    `;

    for (let page = 1; page <= totalPages; page++) {
        buttons += `
            <button class="page-button ${page === currentPage ? 'active' : ''}"
                data-page="${page}">
                ${page}
            </button>
        `;
    }

    buttons += `
        <button class="page-button"
            data-page="${currentPage + 1}"
            ${currentPage === totalPages ? 'disabled' : ''}>
            ›
        </button>
    `;

    pagination.innerHTML = buttons;
}

/* =========================================
   SEARCH & FILTER
========================================= */

function applyFilters() {
    const keyword = searchInput.value.toLowerCase().trim();
    const selectedRole = roleFilter.value;
    const selectedStatus = statusFilter.value;

    filteredUsers = users.filter(user => {
        const matchesSearch =
            user.name.toLowerCase().includes(keyword) ||
            user.email.toLowerCase().includes(keyword) ||
            user.id.toLowerCase().includes(keyword);

        const matchesRole =
            selectedRole === 'all' || user.role === selectedRole;

        const matchesStatus =
            selectedStatus === 'all' || user.status === selectedStatus;

        return matchesSearch && matchesRole && matchesStatus;
    });

    currentPage = 1;
    renderSummary();
    renderTable();
}

/* =========================================
   FORM VALIDATION
========================================= */

function clearErrors() {
    document.querySelectorAll('.form-error').forEach(error => {
        error.textContent = '';
    });

    document.querySelectorAll('#userForm .invalid').forEach(input => {
        input.classList.remove('invalid');
    });
}

function showError(input, errorId, message) {
    input.classList.add('invalid');
    document.getElementById(errorId).textContent = message;
}

function validateForm() {
    clearErrors();

    const name = nameInput.value.trim();
    const email = emailInput.value.trim().toLowerCase();
    const password = passwordInput.value;
    const role = roleInput.value;

    let valid = true;

    if (!name) {
        showError(nameInput, 'nameError', 'Nama lengkap wajib diisi.');
        valid = false;
    } else if (name.length < 3) {
        showError(nameInput, 'nameError', 'Nama minimal 3 karakter.');
        valid = false;
    }

    if (!email) {
        showError(emailInput, 'emailError', 'Email wajib diisi.');
        valid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        showError(emailInput, 'emailError', 'Format email tidak valid.');
        valid = false;
    } else {
        const duplicateEmail = users.some(user =>
            user.email.toLowerCase() === email &&
            user.id !== editingUserId.value
        );

        if (duplicateEmail) {
            showError(emailInput, 'emailError', 'Email sudah digunakan.');
            valid = false;
        }
    }

    const isEditing = Boolean(editingUserId.value);

    if (!isEditing && !password) {
        showError(passwordInput, 'passwordError', 'Password wajib diisi.');
        valid = false;
    } else if (!isEditing && password.length < 8) {
        showError(passwordInput, 'passwordError', 'Password minimal 8 karakter.');
        valid = false;
    }

    if (!role) {
        showError(roleInput, 'roleError', 'Silakan pilih role pengguna.');
        valid = false;
    }

    return valid;
}

/* =========================================
   OPEN & CLOSE FORM MODAL
========================================= */

function openAddModal() {
    userForm.reset();
    editingUserId.value = '';

    clearErrors();

    document.getElementById('userFormTitle').textContent = 'Tambah Pengguna';
    document.getElementById('userFormDescription').textContent =
        'Tambahkan akun pengguna baru ke sistem.';

    document.getElementById('passwordGroup').style.display = 'flex';
    passwordInput.required = true;

    document.getElementById('saveUserButton').textContent = 'Simpan Pengguna';

    formModal.classList.add('show');
}

function openEditModal(userId) {
    const user = users.find(item => item.id === userId);
    if (!user) return;

    userForm.reset();
    clearErrors();

    editingUserId.value = user.id;

    nameInput.value = user.name;
    emailInput.value = user.email;
    roleInput.value = user.role;
    statusInput.value = user.status;

    passwordInput.value = '';
    passwordInput.required = false;

    document.getElementById('passwordGroup').style.display = 'none';

    document.getElementById('userFormTitle').textContent = 'Edit Pengguna';
    document.getElementById('userFormDescription').textContent =
        'Perbarui informasi akun pengguna.';

    document.getElementById('saveUserButton').textContent = 'Simpan Perubahan';

    formModal.classList.add('show');
}

function closeFormModal() {
    formModal.classList.remove('show');
    userForm.reset();
    editingUserId.value = '';
    clearErrors();
}

function closeDetailModal() {
    detailModal.classList.remove('show');
}

/* =========================================
   CREATE & UPDATE USER
========================================= */

userForm.addEventListener('submit', event => {
    event.preventDefault();

    if (!validateForm()) return;

    const name = nameInput.value.trim();
    const email = emailInput.value.trim().toLowerCase();
    const role = roleInput.value;
    const status = statusInput.value;

    if (editingUserId.value) {
        // UPDATE USER
        const user = users.find(item => item.id === editingUserId.value);

        if (!user) return;

        user.name = name;
        user.email = email;
        user.role = role;
        user.status = status;

        alert('Data pengguna berhasil diperbarui.');
    } else {
        // CREATE USER
        const newUser = {
            id: generateUserId(),
            name,
            email,
            role,
            status,
            joined: new Date().toISOString().slice(0, 10)
        };

        users.unshift(newUser);

        alert('Pengguna baru berhasil ditambahkan.');
    }

    closeFormModal();
    applyFilters();
});

/* =========================================
   READ USER DETAIL
========================================= */

function showUserDetail(userId) {
    const user = users.find(item => item.id === userId);
    if (!user) return;

    const initials = getInitials(user.name);

    document.getElementById('userDetailBody').innerHTML = `
        <div class="modal-user-profile">
            <div class="user-avatar">${escapeHTML(initials)}</div>
            <div>
                <strong>${escapeHTML(user.name)}</strong>
                <small>${escapeHTML(user.email)}</small>
            </div>
        </div>

        <div class="modal-detail-row">
            <span>ID Pengguna</span>
            <strong>${escapeHTML(user.id)}</strong>
        </div>

        <div class="modal-detail-row">
            <span>Role</span>
            <strong>${escapeHTML(getRoleLabel(user.role))}</strong>
        </div>

        <div class="modal-detail-row">
            <span>Tanggal Bergabung</span>
            <strong>${formatDate(user.joined)}</strong>
        </div>

        <div class="modal-detail-row">
            <span>Status Akun</span>
            <strong>
                <span class="status-badge ${user.status === 'Aktif' ? 'status-active' : 'status-inactive'}">
                    ${escapeHTML(user.status)}
                </span>
            </strong>
        </div>
    `;

    detailModal.classList.add('show');
}

/* =========================================
   DELETE USER
========================================= */

function deleteUser(userId) {
    const user = users.find(item => item.id === userId);
    if (!user) return;

    // Mencegah penghapusan akun Owner terakhir.
    const activeOwners = users.filter(item =>
        item.role === 'Owner' && item.status === 'Aktif'
    );

    if (
        user.role === 'Owner' &&
        user.status === 'Aktif' &&
        activeOwners.length <= 1
    ) {
        alert('Akun Business Owner aktif terakhir tidak dapat dihapus.');
        return;
    }

    const confirmation = confirm(
        `Apakah kamu yakin ingin menghapus akun ${user.name} (${user.id})?`
    );

    if (!confirmation) return;

    users = users.filter(item => item.id !== userId);

    alert('Pengguna berhasil dihapus.');

    applyFilters();
}

/* =========================================
   EXPORT CSV & EXCEL
========================================= */

function exportData(format) {
    if (filteredUsers.length === 0) {
        alert('Tidak ada data pengguna untuk diekspor.');
        return;
    }

    const headers = [
        'ID Pengguna',
        'Nama',
        'Email',
        'Role',
        'Tanggal Bergabung',
        'Status'
    ];

    const rows = filteredUsers.map(user => [
        user.id,
        user.name,
        user.email,
        getRoleLabel(user.role),
        formatDate(user.joined),
        user.status
    ]);

    const escapeCsv = value =>
        `"${String(value).replace(/"/g, '""')}"`;

    const content = [
        headers.map(escapeCsv).join(','),
        ...rows.map(row => row.map(escapeCsv).join(','))
    ].join('\n');

    const mimeType = format === 'csv'
        ? 'text/csv;charset=utf-8;'
        : 'application/vnd.ms-excel;charset=utf-8;';

    const blob = new Blob(['\uFEFF' + content], { type: mimeType });
    const url = URL.createObjectURL(blob);

    const link = document.createElement('a');
    link.href = url;
    link.download = `data-pengguna-rebant.${format}`;

    document.body.appendChild(link);
    link.click();
    link.remove();

    URL.revokeObjectURL(url);
}

/* =========================================
   EVENT LISTENERS
========================================= */

// Add user
document.getElementById('addUserButton')
    .addEventListener('click', openAddModal);

// Close form modal
document.querySelectorAll('[data-close-form]').forEach(button => {
    button.addEventListener('click', closeFormModal);
});

// Close detail modal
document.querySelectorAll('[data-close-detail]').forEach(button => {
    button.addEventListener('click', closeDetailModal);
});

// Search and filters
searchInput.addEventListener('input', applyFilters);
roleFilter.addEventListener('change', applyFilters);
statusFilter.addEventListener('change', applyFilters);

// Reset filters
document.getElementById('resetUserFilter').addEventListener('click', () => {
    searchInput.value = '';
    roleFilter.value = 'all';
    statusFilter.value = 'all';

    applyFilters();
});

// Table actions
tableBody.addEventListener('click', event => {
    const button = event.target.closest('[data-action]');
    if (!button) return;

    const userId = button.dataset.userId;
    const action = button.dataset.action;

    if (action === 'detail') {
        showUserDetail(userId);
    }

    if (action === 'edit') {
        openEditModal(userId);
    }

    if (action === 'delete') {
        deleteUser(userId);
    }
});

// Pagination
pagination.addEventListener('click', event => {
    const button = event.target.closest('[data-page]');
    if (!button || button.disabled) return;

    currentPage = Number(button.dataset.page);
    renderTable();
});

// Export
document.getElementById('exportCsv')
    .addEventListener('click', () => exportData('csv'));

document.getElementById('exportExcel')
    .addEventListener('click', () => exportData('xls'));

// Escape key closes open modal
document.addEventListener('keydown', event => {
    if (event.key === 'Escape') {
        closeFormModal();
        closeDetailModal();
    }
});

/* =========================================
   INITIAL RENDER
========================================= */

renderSummary();
applyFilters();
