
const users = [
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

const tableBody = document.getElementById('userTableBody');
const searchInput = document.getElementById('userSearch');
const roleFilter = document.getElementById('roleFilter');
const statusFilter = document.getElementById('statusFilter');
const pagination = document.getElementById('userPagination');
const userCount = document.getElementById('userCount');

const modal = document.getElementById('userModal');
const modalBody = document.getElementById('userModalBody');

let filteredUsers = [...users];
let currentPage = 1;
const rowsPerPage = 10;

function formatDate(date) {
    return new Date(date + 'T00:00:00').toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    });
}

function escapeHTML(value) {
    return String(value).replace(/[&<>"']/g, char => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    })[char]);
}

function renderSummary() {
    document.getElementById('totalUsers').textContent = users.length;
    document.getElementById('ownerUsers').textContent =
        users.filter(user => user.role === 'Owner').length;
    document.getElementById('adminUsers').textContent =
        users.filter(user => user.role === 'Admin').length;
    document.getElementById('productionUsers').textContent =
        users.filter(user => user.role === 'Production').length;
}

function getRoleLabel(role) {
    return role === 'Owner' ? 'Business Owner' : role;
}

function renderTable() {
    const totalPages = Math.ceil(filteredUsers.length / rowsPerPage);
    const startIndex = (currentPage - 1) * rowsPerPage;
    const pageUsers = filteredUsers.slice(startIndex, startIndex + rowsPerPage);

    if (pageUsers.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="empty-row">
                    Tidak ada pengguna yang sesuai dengan filter.
                </td>
            </tr>`;
    } else {
        tableBody.innerHTML = pageUsers.map(user => {
            const initials = user.name.split(' ')
                .map(part => part[0])
                .slice(0, 2)
                .join('')
                .toUpperCase();

            return `
                <tr>
                    <td>
                        <div class="user-profile">
                            <div class="user-avatar">${escapeHTML(initials)}</div>
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
                        <button class="detail-button" data-user-id="${escapeHTML(user.id)}">
                            Lihat Detail
                        </button>
                    </td>
                </tr>`;
        }).join('');
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
        <button class="page-button" data-page="${currentPage - 1}"
            ${currentPage === 1 ? 'disabled' : ''}>‹</button>`;

    for (let page = 1; page <= totalPages; page++) {
        buttons += `
            <button class="page-button ${page === currentPage ? 'active' : ''}"
                data-page="${page}">${page}</button>`;
    }

    buttons += `
        <button class="page-button" data-page="${currentPage + 1}"
            ${currentPage === totalPages ? 'disabled' : ''}>›</button>`;

    pagination.innerHTML = buttons;
}

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
    renderTable();
}

function showUserDetail(userId) {
    const user = users.find(item => item.id === userId);
    if (!user) return;

    const initials = user.name.split(' ')
        .map(part => part[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();

    modalBody.innerHTML = `
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
            <strong>${escapeHTML(user.status)}</strong>
        </div>
    `;

    document.getElementById('modalUserName').textContent = 'Detail Pengguna';
    modal.classList.add('show');
}

function closeModal() {
    modal.classList.remove('show');
}

function exportData(format) {
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
    link.click();

    URL.revokeObjectURL(url);
}

// Event listeners
searchInput.addEventListener('input', applyFilters);
roleFilter.addEventListener('change', applyFilters);
statusFilter.addEventListener('change', applyFilters);

document.getElementById('resetUserFilter').addEventListener('click', () => {
    searchInput.value = '';
    roleFilter.value = 'all';
    statusFilter.value = 'all';
    applyFilters();
});

pagination.addEventListener('click', event => {
    const button = event.target.closest('[data-page]');
    if (!button || button.disabled) return;

    currentPage = Number(button.dataset.page);
    renderTable();
});

tableBody.addEventListener('click', event => {
    const button = event.target.closest('[data-user-id]');
    if (!button) return;

    showUserDetail(button.dataset.userId);
});

document.getElementById('closeUserModal').addEventListener('click', closeModal);
document.getElementById('closeUserBackdrop').addEventListener('click', closeModal);

document.addEventListener('keydown', event => {
    if (event.key === 'Escape') closeModal();
});

document.getElementById('exportCsv').addEventListener('click', () => exportData('csv'));
document.getElementById('exportExcel').addEventListener('click', () => exportData('xls'));

// Initial render
renderSummary();
applyFilters();
