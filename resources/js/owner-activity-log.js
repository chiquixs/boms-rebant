
document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.getElementById('activityTableBody');

    if (!tableBody) return;

    /*
    |--------------------------------------------------------------------------
    | DUMMY ACTIVITY DATA
    |--------------------------------------------------------------------------
    | Data sementara untuk kebutuhan frontend.
    */

    const activities = [
        { id: 'ACT-001', date: '2026-11-13', time: '09:15', category: 'Order', action: 'Pesanan baru ditambahkan', user: 'Admin', detail: 'Pesanan ORD-013 berhasil dicatat ke dalam sistem.', reference: 'ORD-013' },
        { id: 'ACT-002', date: '2026-11-13', time: '08:50', category: 'Produksi', action: 'Status produksi diperbarui', user: 'Production', detail: 'Wall Decor Minimalist diperbarui menjadi proses produksi.', reference: 'PRD-008' },
        { id: 'ACT-003', date: '2026-11-12', time: '16:40', category: 'Inventory', action: 'Stok bahan baku diperbarui', user: 'Admin', detail: 'Stok kayu MDF diperbarui setelah pencatatan barang masuk.', reference: 'INV-021' },
        { id: 'ACT-004', date: '2026-11-12', time: '14:25', category: 'Order', action: 'Status pesanan diperbarui', user: 'Admin', detail: 'Pesanan ORD-012 diperbarui menjadi Completed.', reference: 'ORD-012' },
        { id: 'ACT-005', date: '2026-11-12', time: '11:10', category: 'Produksi', action: 'Produksi diselesaikan', user: 'Production', detail: 'Produksi Wall Decor Floral sebanyak 3 unit telah selesai.', reference: 'PRD-007' },
        { id: 'ACT-006', date: '2026-11-11', time: '15:35', category: 'Inventory', action: 'Stok produk diperbarui', user: 'Admin', detail: 'Stok produk jadi Wall Decor Abstract bertambah 5 unit.', reference: 'INV-020' },
        { id: 'ACT-007', date: '2026-11-11', time: '13:20', category: 'Order', action: 'Pesanan diperiksa', user: 'Admin', detail: 'Pesanan ORD-011 telah diperiksa sebelum diproses.', reference: 'ORD-011' },
        { id: 'ACT-008', date: '2026-11-10', time: '16:00', category: 'Produksi', action: 'Permintaan produksi dibuat', user: 'Admin', detail: 'Permintaan produksi Wall Decor Minimalist sebanyak 4 unit dibuat.', reference: 'PRD-006' },
        { id: 'ACT-009', date: '2026-11-10', time: '10:45', category: 'Inventory', action: 'Bahan baku digunakan', user: 'Production', detail: 'Bahan baku cat kayu digunakan untuk proses produksi.', reference: 'INV-019' },
        { id: 'ACT-010', date: '2026-11-09', time: '14:30', category: 'Order', action: 'Pesanan diselesaikan', user: 'Admin', detail: 'Pesanan ORD-009 telah selesai dan dikonfirmasi.', reference: 'ORD-009' },
        { id: 'ACT-011', date: '2026-11-09', time: '09:20', category: 'Produksi', action: 'Status produksi diperbarui', user: 'Production', detail: 'Wall Decor Abstract memasuki tahap finishing.', reference: 'PRD-005' },
        { id: 'ACT-012', date: '2026-11-08', time: '15:15', category: 'Inventory', action: 'Stok bahan baku berkurang', user: 'Production', detail: 'Stok resin berkurang setelah digunakan dalam produksi.', reference: 'INV-018' },
        { id: 'ACT-013', date: '2026-11-08', time: '11:30', category: 'Order', action: 'Pesanan baru ditambahkan', user: 'Admin', detail: 'Pesanan ORD-008 berhasil dicatat dari TikTok Shop.', reference: 'ORD-008' },
        { id: 'ACT-014', date: '2026-11-07', time: '16:25', category: 'Produksi', action: 'Produksi diselesaikan', user: 'Production', detail: 'Produksi Wall Decor Minimalist sebanyak 2 unit telah selesai.', reference: 'PRD-004' },
        { id: 'ACT-015', date: '2026-11-07', time: '10:10', category: 'Inventory', action: 'Barang masuk dicatat', user: 'Admin', detail: 'Barang masuk berupa kayu MDF sebanyak 20 lembar.', reference: 'INV-017' },
        { id: 'ACT-016', date: '2026-11-06', time: '14:05', category: 'Order', action: 'Status pesanan diperbarui', user: 'Admin', detail: 'Pesanan ORD-006 berubah menjadi Completed.', reference: 'ORD-006' },
        { id: 'ACT-017', date: '2026-11-05', time: '13:45', category: 'Produksi', action: 'Permintaan produksi dibuat', user: 'Admin', detail: 'Permintaan produksi Wall Decor Floral sebanyak 5 unit dibuat.', reference: 'PRD-003' },
        { id: 'ACT-018', date: '2026-11-04', time: '09:50', category: 'Inventory', action: 'Stok produk diperbarui', user: 'Admin', detail: 'Stok produk jadi Wall Decor Floral diperbarui.', reference: 'INV-016' },
        { id: 'ACT-019', date: '2026-11-03', time: '15:20', category: 'Order', action: 'Pesanan baru ditambahkan', user: 'Admin', detail: 'Pesanan ORD-003 berhasil dicatat dari Shopee.', reference: 'ORD-003' },
        { id: 'ACT-020', date: '2026-11-02', time: '10:30', category: 'Produksi', action: 'Produksi dimulai', user: 'Production', detail: 'Proses produksi Wall Decor Abstract mulai dikerjakan.', reference: 'PRD-002' },
        { id: 'ACT-021', date: '2026-11-01', time: '09:00', category: 'Inventory', action: 'Stok awal diperiksa', user: 'Admin', detail: 'Pemeriksaan stok awal bahan baku dan produk jadi dilakukan.', reference: 'INV-015' },
        { id: 'ACT-022', date: '2026-10-30', time: '14:10', category: 'Order', action: 'Pesanan diselesaikan', user: 'Admin', detail: 'Pesanan ORD-001 telah diselesaikan.', reference: 'ORD-001' },
        { id: 'ACT-023', date: '2026-10-29', time: '11:20', category: 'Produksi', action: 'Produksi diselesaikan', user: 'Production', detail: 'Produksi Wall Decor Floral telah selesai.', reference: 'PRD-001' },
        { id: 'ACT-024', date: '2026-10-28', time: '08:45', category: 'Inventory', action: 'Barang masuk dicatat', user: 'Admin', detail: 'Barang masuk berupa cat kayu sebanyak 10 kaleng.', reference: 'INV-014' }
    ];

    const rowsPerPage = 10;
    let currentPage = 1;
    let filteredActivities = [...activities];

    const searchInput = document.getElementById('activitySearch');
    const categoryInput = document.getElementById('activityCategory');
    const startDateInput = document.getElementById('activityStartDate');
    const endDateInput = document.getElementById('activityEndDate');
    const pagination = document.getElementById('activityPagination');

    function escapeHTML(value) {
        return String(value).replace(/[&<>"']/g, function (character) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[character];
        });
    }

    function formatDate(dateString) {
        return new Date(dateString + 'T00:00:00').toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
    }

    function formatDateTime(item) {
        return formatDate(item.date) + ', ' + item.time;
    }

    function getCategoryClass(category) {
        if (category === 'Order') return 'category-order';
        if (category === 'Produksi') return 'category-production';
        return 'category-inventory';
    }

    function getInitials(name) {
        return name.split(' ').map(function (part) {
            return part.charAt(0);
        }).join('').substring(0, 2).toUpperCase();
    }

    // SUMMARY COUNTS: mengikuti data yang sedang difilter
    function renderSummary(data) {
        document.getElementById('totalActivities').textContent = data.length;
        document.getElementById('totalOrders').textContent =
            data.filter(item => item.category === 'Order').length;
        document.getElementById('totalProduction').textContent =
            data.filter(item => item.category === 'Produksi').length;
        document.getElementById('totalInventory').textContent =
            data.filter(item => item.category === 'Inventory').length;
    }

    // FILTER DATA
    function applyFilters() {
        const keyword = searchInput.value.trim().toLowerCase();
        const category = categoryInput.value;
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;

        if (startDate && endDate && startDate > endDate) {
            alert('Tanggal awal tidak boleh melebihi tanggal akhir.');
            return;
        }

        filteredActivities = activities.filter(function (item) {
            const matchesSearch =
                item.id.toLowerCase().includes(keyword) ||
                item.action.toLowerCase().includes(keyword) ||
                item.user.toLowerCase().includes(keyword) ||
                item.detail.toLowerCase().includes(keyword) ||
                item.reference.toLowerCase().includes(keyword);

            const matchesCategory =
                category === 'all' || item.category === category;

            const matchesStart =
                !startDate || item.date >= startDate;

            const matchesEnd =
                !endDate || item.date <= endDate;

            return matchesSearch &&
                matchesCategory &&
                matchesStart &&
                matchesEnd;
        });

        currentPage = 1;
        renderActivities();
    }

    // TABLE + PAGINATION
    function renderActivities() {
        const totalData = filteredActivities.length;
        const totalPages = Math.ceil(totalData / rowsPerPage);

        if (totalPages > 0 && currentPage > totalPages) {
            currentPage = totalPages;
        }

        const startIndex = (currentPage - 1) * rowsPerPage;
        const endIndex = Math.min(startIndex + rowsPerPage, totalData);
        const currentData = filteredActivities.slice(startIndex, endIndex);

        renderSummary(filteredActivities);

        if (totalData === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="activity-empty">
                        Tidak ada aktivitas yang sesuai dengan filter.
                    </td>
                </tr>
            `;

            document.getElementById('activityCount').textContent =
                'Menampilkan 0 aktivitas';

            pagination.innerHTML = '';
            return;
        }

        tableBody.innerHTML = currentData.map(function (item) {
            return `
                <tr>
                    <td class="activity-id">${escapeHTML(item.id)}</td>
                    <td>${formatDateTime(item)}</td>
                    <td>
                        <span class="activity-category ${getCategoryClass(item.category)}">
                            ${escapeHTML(item.category)}
                        </span>
                    </td>
                    <td class="activity-description-cell">${escapeHTML(item.action)}</td>
                    <td>
                        <div class="activity-user">
                            <span class="activity-user-avatar">
                                ${getInitials(item.user)}
                            </span>
                            ${escapeHTML(item.user)}
                        </div>
                    </td>
                    <td>
                        <button
                            type="button"
                            class="activity-detail-button"
                            data-detail-id="${escapeHTML(item.id)}"
                        >
                            Lihat Detail
                        </button>
                    </td>
                </tr>
            `;
        }).join('');

        document.getElementById('activityCount').textContent =
            `Menampilkan ${startIndex + 1}–${endIndex} dari ${totalData} aktivitas`;

        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        if (totalPages <= 1) {
            pagination.innerHTML = '';
            return;
        }

        let html = `
            <button
                type="button"
                class="activity-page-button"
                data-page="${currentPage - 1}"
                ${currentPage === 1 ? 'disabled' : ''}
            >‹</button>
        `;

        for (let page = 1; page <= totalPages; page++) {
            html += `
                <button
                    type="button"
                    class="activity-page-button ${page === currentPage ? 'active' : ''}"
                    data-page="${page}"
                >${page}</button>
            `;
        }

        html += `
            <button
                type="button"
                class="activity-page-button"
                data-page="${currentPage + 1}"
                ${currentPage === totalPages ? 'disabled' : ''}
            >›</button>
        `;

        pagination.innerHTML = html;
    }

    // DETAIL MODAL
    function openDetail(activityId) {
        const item = activities.find(activity => activity.id === activityId);

        if (!item) return;

        document.getElementById('activityModalBody').innerHTML = `
            <div class="activity-detail-row">
                <span class="activity-detail-label">ID Aktivitas</span>
                <span class="activity-detail-value">${escapeHTML(item.id)}</span>
            </div>

            <div class="activity-detail-row">
                <span class="activity-detail-label">Tanggal dan Waktu</span>
                <span class="activity-detail-value">${formatDateTime(item)}</span>
            </div>

            <div class="activity-detail-row">
                <span class="activity-detail-label">Kategori</span>
                <span class="activity-detail-value">${escapeHTML(item.category)}</span>
            </div>

            <div class="activity-detail-row">
                <span class="activity-detail-label">Aktivitas</span>
                <span class="activity-detail-value">${escapeHTML(item.action)}</span>
            </div>

            <div class="activity-detail-row">
                <span class="activity-detail-label">Pengguna</span>
                <span class="activity-detail-value">${escapeHTML(item.user)}</span>
            </div>

            <div class="activity-detail-row">
                <span class="activity-detail-label">Referensi</span>
                <span class="activity-detail-value">${escapeHTML(item.reference)}</span>
            </div>

            <div class="activity-detail-row">
                <span class="activity-detail-label">Keterangan</span>
                <span class="activity-detail-value">${escapeHTML(item.detail)}</span>
            </div>
        `;

        const modal = document.getElementById('activityModal');
        modal.classList.add('show');
        modal.setAttribute('aria-hidden', 'false');
    }

    function closeDetail() {
        const modal = document.getElementById('activityModal');
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
    }

    // EXPORT HELPERS
    function getExportFileName(extension) {
        const start = startDateInput.value || 'semua';
        const end = endDateInput.value || 'semua';

        return `log-aktivitas-${start}-sampai-${end}.${extension}`;
    }

    function downloadBlob(blob, fileName) {
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');

        link.href = url;
        link.download = fileName;

        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        setTimeout(function () {
            URL.revokeObjectURL(url);
        }, 1000);
    }

    // CSV EXPORT
    function exportCSV() {
        if (filteredActivities.length === 0) {
            alert('Tidak ada aktivitas untuk diekspor.');
            return;
        }

        const headers = [
            'ID Aktivitas',
            'Tanggal',
            'Waktu',
            'Kategori',
            'Aktivitas',
            'Pengguna',
            'Referensi',
            'Keterangan'
        ];

        const rows = filteredActivities.map(function (item) {
            return [
                item.id,
                item.date,
                item.time,
                item.category,
                item.action,
                item.user,
                item.reference,
                item.detail
            ];
        });

        const csv = [headers, ...rows].map(function (row) {
            return row.map(function (value) {
                return '"' + String(value).replace(/"/g, '""') + '"';
            }).join(',');
        }).join('\r\n');

        const blob = new Blob(['\uFEFF' + csv], {
            type: 'text/csv;charset=utf-8;'
        });

        downloadBlob(blob, getExportFileName('csv'));
    }

    // EXCEL EXPORT
    // File .xls kompatibel untuk dibuka menggunakan Microsoft Excel.
    function exportExcel() {
        if (filteredActivities.length === 0) {
            alert('Tidak ada aktivitas untuk diekspor.');
            return;
        }

        const rows = filteredActivities.map(function (item) {
            return `
                <tr>
                    <td>${escapeHTML(item.id)}</td>
                    <td>${escapeHTML(item.date)}</td>
                    <td>${escapeHTML(item.time)}</td>
                    <td>${escapeHTML(item.category)}</td>
                    <td>${escapeHTML(item.action)}</td>
                    <td>${escapeHTML(item.user)}</td>
                    <td>${escapeHTML(item.reference)}</td>
                    <td>${escapeHTML(item.detail)}</td>
                </tr>
            `;
        }).join('');

        const html = `
            <html>
            <head><meta charset="UTF-8"></head>
            <body>
                <h2>Log Aktivitas REBANT BOMS</h2>
                <table border="1">
                    <thead>
                        <tr>
                            <th>ID Aktivitas</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Kategori</th>
                            <th>Aktivitas</th>
                            <th>Pengguna</th>
                            <th>Referensi</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
            </body>
            </html>
        `;

        const blob = new Blob([html], {
            type: 'application/vnd.ms-excel;charset=utf-8;'
        });

        downloadBlob(blob, getExportFileName('xls'));
    }

    // EVENT LISTENERS
    searchInput.addEventListener('input', applyFilters);
    categoryInput.addEventListener('change', applyFilters);
    startDateInput.addEventListener('change', applyFilters);
    endDateInput.addEventListener('change', applyFilters);

    document.getElementById('resetFilter').addEventListener('click', function () {
        searchInput.value = '';
        categoryInput.value = 'all';
        startDateInput.value = '';
        endDateInput.value = '';

        currentPage = 1;
        applyFilters();
    });

    pagination.addEventListener('click', function (event) {
        const button = event.target.closest('[data-page]');

        if (!button || button.disabled) return;

        const selectedPage = Number(button.dataset.page);
        const totalPages = Math.ceil(filteredActivities.length / rowsPerPage);

        if (selectedPage >= 1 && selectedPage <= totalPages) {
            currentPage = selectedPage;
            renderActivities();
        }
    });

    tableBody.addEventListener('click', function (event) {
        const button = event.target.closest('[data-detail-id]');

        if (!button) return;

        openDetail(button.dataset.detailId);
    });

    document.getElementById('closeActivityModal')
        .addEventListener('click', closeDetail);

    document.getElementById('closeActivityBackdrop')
        .addEventListener('click', closeDetail);

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') closeDetail();
    });

    document.getElementById('exportActivityCsv')
        .addEventListener('click', exportCSV);

    document.getElementById('exportActivityExcel')
        .addEventListener('click', exportExcel);

    // INITIAL RENDER
    // INITIAL RENDER
    applyFilters();
});
