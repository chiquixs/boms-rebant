/* =========================================
   USER MANAGEMENT - STATE & CONFIG
========================================= */

const config = window.userManagementConfig || {};
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

let users = [];
let summary = { total: 0, owner: 0, admin: 0, production: 0 };
let currentUserId = null;

/* =========================================
   DOM ELEMENTS
========================================= */

const tableBody = document.getElementById('userTableBody');
const searchInput = document.getElementById('userSearch');
const roleFilter = document.getElementById('roleFilter');
const statusFilter = document.getElementById('statusFilter');
const pagination = document.getElementById('userPagination');
const userCount = document.getElementById('userCount');
const lastUpdatedDate = document.getElementById('lastUpdatedDate');

const formModal = document.getElementById('userFormModal');
const detailModal = document.getElementById('userDetailModal');

const userForm = document.getElementById('userForm');
const editingUserId = document.getElementById('editingUserId');
const saveUserButton = document.getElementById('saveUserButton');

const nameInput = document.getElementById('userNameInput');
const emailInput = document.getElementById('userEmailInput');
const passwordInput = document.getElementById('userPasswordInput');
const passwordLabel = document.getElementById('passwordLabel');
const passwordHint = document.getElementById('passwordHint');
const roleInput = document.getElementById('userRoleInput');
const statusInput = document.getElementById('userStatusInput');
const formGeneralError = document.getElementById('formGeneralError');

let filteredUsers = [];
let currentPage = 1;
const rowsPerPage = 10;
let isSubmitting = false;

/* =========================================
   HELPER FUNCTIONS
========================================= */

function escapeHTML(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    })[char]);
}

function formatDate(date) {
    if (!date) return '-';

    return new Date(`${date}T00:00:00`).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    });
}

function getInitials(name) {
    return String(name ?? '')
        .split(' ')
        .filter(Boolean)
        .map(part => part[0])
        .slice(0, 2)
        .join('')
        .toUpperCase() || '?';
}

function getRoleLabel(role) {
    return role === 'Owner' ? 'Business Owner' : role;
}

function buildUrl(template, userId) {
    return String(template || '').replace('__ID__', encodeURIComponent(userId));
}

function updateLastUpdated(label) {
    if (lastUpdatedDate && label) {
        lastUpdatedDate.textContent = label;
    }
}

/* =========================================
   SUMMARY
========================================= */

function renderSummary() {
    document.getElementById('totalUsers').textContent = summary.total ?? users.length;
    document.getElementById('ownerUsers').textContent = summary.owner ?? 0;
    document.getElementById('adminUsers').textContent = summary.admin ?? 0;
    document.getElementById('productionUsers').textContent = summary.production ?? 0;
}

/* =========================================
   TABLE RENDER
========================================= */

function renderTableMessage(message) {
    tableBody.innerHTML = `
        <tr>
            <td colspan="5" class="empty-row">${escapeHTML(message)}</td>
        </tr>
    `;
    pagination.innerHTML = '';
}

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
                    <span class="role-badge role-${escapeHTML(user.role.toLowerCase())}">
                        ${escapeHTML(getRoleLabel(user.role))}
                    </span>
                </td>

                <td>${escapeHTML(formatDate(user.joined))}</td>

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

function applyFilters(resetPage = true) {
    const keyword = searchInput.value.toLowerCase().trim();
    const selectedRole = roleFilter.value;
    const selectedStatus = statusFilter.value;

    filteredUsers = users.filter(user => {
        const matchesSearch =
            user.name.toLowerCase().includes(keyword) ||
            user.email.toLowerCase().includes(keyword) ||
            String(user.code || '').toLowerCase().includes(keyword) ||
            String(user.id).toLowerCase().includes(keyword);

        const matchesRole =
            selectedRole === 'all' || user.role === selectedRole;

        const matchesStatus =
            selectedStatus === 'all' || user.status === selectedStatus;

        return matchesSearch && matchesRole && matchesStatus;
    });

    const totalPages = Math.max(1, Math.ceil(filteredUsers.length / rowsPerPage));
    currentPage = resetPage ? 1 : Math.min(currentPage, totalPages);

    renderSummary();
    renderTable();
}

/* =========================================
   FETCH DATA FROM BACKEND
========================================= */

async function loadUsers(resetPage = true) {
    if (!config.dataUrl) {
        renderTableMessage('Konfigurasi endpoint belum tersedia.');
        return;
    }

    renderTableMessage('Memuat data pengguna...');

    try {
        const response = await fetch(config.dataUrl, {
            headers: {
                Accept: 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const data = await response.json();

        users = Array.isArray(data.users) ? data.users : [];
        summary = data.summary || { total: users.length, owner: 0, admin: 0, production: 0 };
        currentUserId = data.current_user_id ?? null;

        updateLastUpdated(data.last_updated);
        applyFilters(resetPage);
    } catch (error) {
        renderTableMessage('Gagal memuat data pengguna. Silakan muat ulang halaman.');
        userCount.textContent = 'Menampilkan 0 pengguna';
    }
}

/* =========================================
   FORM VALIDATION
========================================= */

function clearErrors() {
    document.querySelectorAll('.form-error').forEach(error => {
        error.textContent = '';
    });

    if (formGeneralError) {
        formGeneralError.hidden = true;
    }

    document.querySelectorAll('#userForm .invalid').forEach(input => {
        input.classList.remove('invalid');
    });
}

function showError(input, errorId, message) {
    if (input) {
        input.classList.add('invalid');
    }

    const errorElement = document.getElementById(errorId);
    if (errorElement) {
        errorElement.textContent = message;
    }
}

function validateForm() {
    clearErrors();

    const name = nameInput.value.trim();
    const email = emailInput.value.trim().toLowerCase();
    const password = passwordInput.value;
    const role = roleInput.value;
    const isEditing = Boolean(editingUserId.value);

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
            String(user.id) !== String(editingUserId.value)
        );

        if (duplicateEmail) {
            showError(emailInput, 'emailError', 'Email sudah digunakan.');
            valid = false;
        }
    }

    if (!isEditing && !password) {
        showError(passwordInput, 'passwordError', 'Password wajib diisi.');
        valid = false;
    } else if (password && password.length < 8) {
        showError(passwordInput, 'passwordError', 'Password minimal 8 karakter.');
        valid = false;
    }

    if (!role) {
        showError(roleInput, 'roleError', 'Silakan pilih role pengguna.');
        valid = false;
    }

    return valid;
}

function applyServerValidationErrors(data) {
    const errors = data?.errors || {};
    const fieldMap = {
        name: [nameInput, 'nameError'],
        email: [emailInput, 'emailError'],
        password: [passwordInput, 'passwordError'],
        role: [roleInput, 'roleError'],
        status: [statusInput, 'statusError']
    };

    let hasMappedFieldError = false;

    Object.entries(fieldMap).forEach(([field, [input, errorId]]) => {
        if (Array.isArray(errors[field]) && errors[field].length > 0) {
            showError(input, errorId, errors[field][0]);
            hasMappedFieldError = true;
        }
    });

    if (!hasMappedFieldError && data?.message && formGeneralError) {
        formGeneralError.textContent = data.message;
        formGeneralError.hidden = false;
    }
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
    passwordLabel.innerHTML = 'Password <span>*</span>';
    passwordInput.placeholder = 'Masukkan password';
    passwordHint.textContent = 'Minimal 8 karakter.';
    passwordInput.required = true;

    saveUserButton.textContent = 'Simpan Pengguna';
    formModal.classList.add('show');
}

function openEditModal(userId) {
    const user = users.find(item => Number(item.id) === Number(userId));
    if (!user) return;

    userForm.reset();
    clearErrors();

    editingUserId.value = String(user.id);

    nameInput.value = user.name;
    emailInput.value = user.email;
    roleInput.value = user.role;
    statusInput.value = user.status;

    passwordInput.value = '';
    passwordInput.required = false;

    document.getElementById('passwordGroup').style.display = 'flex';
    passwordLabel.textContent = 'Password Baru';
    passwordInput.placeholder = 'Kosongkan jika tidak ingin mengubah password';
    passwordHint.textContent = 'Opsional. Minimal 8 karakter jika diisi.';

    document.getElementById('userFormTitle').textContent = 'Edit Pengguna';
    document.getElementById('userFormDescription').textContent =
        'Perbarui informasi akun pengguna.';

    saveUserButton.textContent = 'Simpan Perubahan';
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

userForm.addEventListener('submit', async event => {
    event.preventDefault();

    if (isSubmitting || !validateForm()) return;

    const isEditing = Boolean(editingUserId.value);
    const targetUrl = isEditing
        ? buildUrl(config.updateUrlTemplate, editingUserId.value)
        : config.storeUrl;

    const payload = {
        name: nameInput.value.trim(),
        email: emailInput.value.trim().toLowerCase(),
        role: roleInput.value,
        status: statusInput.value
    };

    if (!isEditing || passwordInput.value !== '') {
        payload.password = passwordInput.value;
    }

    isSubmitting = true;
    const defaultButtonLabel = saveUserButton.textContent;
    saveUserButton.disabled = true;
    saveUserButton.textContent = 'Menyimpan...';

    try {
        const response = await fetch(targetUrl, {
            method: isEditing ? 'PUT' : 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(payload)
        });

        const data = await response.json().catch(() => ({}));

        if (response.status === 419 || response.status === 401) {
            alert('Sesi Anda telah berakhir. Silakan muat ulang halaman.');
            return;
        }

        if (response.status === 422) {
            applyServerValidationErrors(data);
            return;
        }

        if (!response.ok) {
            alert(data.message || 'Gagal menyimpan data pengguna.');
            return;
        }

        if (data.user) {
            if (isEditing) {
                users = users.map(item =>
                    Number(item.id) === Number(data.user.id) ? data.user : item
                );
            } else {
                users.unshift(data.user);
            }
        }

        if (data.summary) {
            summary = data.summary;
        }

        updateLastUpdated(data.last_updated);
        closeFormModal();
        applyFilters(!isEditing);
        alert(data.message || 'Data pengguna berhasil disimpan.');
    } catch (error) {
        alert('Terjadi kesalahan jaringan saat menyimpan data.');
    } finally {
        isSubmitting = false;
        saveUserButton.disabled = false;
        saveUserButton.textContent = defaultButtonLabel;
    }
});

/* =========================================
   READ USER DETAIL
========================================= */

function showUserDetail(userId) {
    const user = users.find(item => Number(item.id) === Number(userId));
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
            <strong>${escapeHTML(user.code || user.id)}</strong>
        </div>

        <div class="modal-detail-row">
            <span>Role</span>
            <strong>${escapeHTML(getRoleLabel(user.role))}</strong>
        </div>

        <div class="modal-detail-row">
            <span>Tanggal Bergabung</span>
            <strong>${escapeHTML(formatDate(user.joined))}</strong>
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

async function deleteUser(userId) {
    const user = users.find(item => Number(item.id) === Number(userId));
    if (!user) return;

    if (currentUserId !== null && Number(user.id) === Number(currentUserId)) {
        alert('Akun yang sedang Anda gunakan tidak dapat dihapus.');
        return;
    }

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
        `Apakah kamu yakin ingin menghapus akun ${user.name} (${user.code || user.id})?`
    );

    if (!confirmation) return;

    try {
        const response = await fetch(buildUrl(config.deleteUrlTemplate, user.id), {
            method: 'DELETE',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        const data = await response.json().catch(() => ({}));

        if (response.status === 419 || response.status === 401) {
            alert('Sesi Anda telah berakhir. Silakan muat ulang halaman.');
            return;
        }

        if (!response.ok) {
            alert(data.message || 'Gagal menghapus pengguna.');
            return;
        }

        users = users.filter(item => Number(item.id) !== Number(user.id));

        if (data.summary) {
            summary = data.summary;
        }

        updateLastUpdated(data.last_updated);
        applyFilters(false);
        alert(data.message || 'Pengguna berhasil dihapus.');
    } catch (error) {
        alert('Terjadi kesalahan jaringan saat menghapus pengguna.');
    }
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
        user.code || user.id,
        user.name,
        user.email,
        getRoleLabel(user.role),
        formatDate(user.joined),
        user.status
    ]);

    const escapeCsv = value =>
        `"${String(value ?? '').replace(/"/g, '""')}"`;

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

document.getElementById('addUserButton')
    .addEventListener('click', openAddModal);

document.querySelectorAll('[data-close-form]').forEach(button => {
    button.addEventListener('click', closeFormModal);
});

document.querySelectorAll('[data-close-detail]').forEach(button => {
    button.addEventListener('click', closeDetailModal);
});

searchInput.addEventListener('input', () => applyFilters(true));
roleFilter.addEventListener('change', () => applyFilters(true));
statusFilter.addEventListener('change', () => applyFilters(true));

document.getElementById('resetUserFilter').addEventListener('click', () => {
    searchInput.value = '';
    roleFilter.value = 'all';
    statusFilter.value = 'all';

    applyFilters(true);
});

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

pagination.addEventListener('click', event => {
    const button = event.target.closest('[data-page]');
    if (!button || button.disabled) return;

    currentPage = Number(button.dataset.page);
    renderTable();
});

document.getElementById('exportCsv')
    .addEventListener('click', () => exportData('csv'));

document.getElementById('exportExcel')
    .addEventListener('click', () => exportData('xls'));

document.addEventListener('keydown', event => {
    if (event.key === 'Escape') {
        closeFormModal();
        closeDetailModal();
    }
});

/* =========================================
   INITIAL LOAD
========================================= */

loadUsers(true);
