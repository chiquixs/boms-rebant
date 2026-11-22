document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.getElementById('activityTableBody');

    if (!tableBody) return;

    const config = window.activityLogConfig || {};

    let activities = [];
    let summary = { total: 0, order: 0, production: 0, inventory: 0 };
    const rowsPerPage = 10;
    let currentPage = 1;
    let searchDebounceTimer = null;
    let activeController = null;

    const searchInput = document.getElementById('activitySearch');
    const categoryInput = document.getElementById('activityCategory');
    const startDateInput = document.getElementById('activityStartDate');
    const endDateInput = document.getElementById('activityEndDate');
    const pagination = document.getElementById('activityPagination');

    function escapeHTML(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (character) {
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
        if (!dateString) return '-';

        return new Date(dateString + 'T00:00:00').toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
    }

    function formatDateTime(item) {
        return `${formatDate(item.date)}, ${item.time || '00:00'}`;
    }

    function getCategoryClass(entityType) {
        const normalized = String(entityType || '').toLowerCase();

        if (normalized === 'order') return 'category-order';
        if (normalized === 'production') return 'category-production';
        if (normalized === 'inventory' || normalized === 'material') return 'category-inventory';
        if (normalized === 'product' || normalized === 'category') return 'category-product';
        if (normalized === 'procurement' || normalized === 'supplier') return 'category-procurement';
        if (normalized === 'user') return 'category-user';

        return 'category-default';
    }

    function getInitials(name) {
        return String(name ?? '')
            .split(' ')
            .filter(Boolean)
            .map(function (part) {
                return part.charAt(0);
            })
            .join('')
            .substring(0, 2)
            .toUpperCase() || 'S';
    }

    function renderSummary() {
        document.getElementById('totalActivities').textContent = summary.total ?? activities.length;
        document.getElementById('totalOrders').textContent = summary.order ?? 0;
        document.getElementById('totalProduction').textContent = summary.production ?? 0;
        document.getElementById('totalInventory').textContent = summary.inventory ?? 0;
    }

    function renderMessageRow(message) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="activity-empty">
                    ${escapeHTML(message)}
                </td>
            </tr>
        `;
        document.getElementById('activityCount').textContent = 'Menampilkan 0 aktivitas';
        pagination.innerHTML = '';
    }

    async function fetchActivities() {
        if (!config.dataUrl) {
            renderMessageRow('Konfigurasi endpoint log aktivitas belum tersedia.');
            return;
        }

        const keyword = searchInput.value.trim();
        const category = categoryInput.value;
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;

        if (startDate && endDate && startDate > endDate) {
            alert('Tanggal awal tidak boleh melebihi tanggal akhir.');
            return;
        }

        if (activeController) {
            activeController.abort();
        }
        activeController = new AbortController();

        const params = new URLSearchParams();
        if (keyword) params.set('search', keyword);
        if (category && category !== 'all') params.set('category', category);
        if (startDate) params.set('start', startDate);
        if (endDate) params.set('end', endDate);

        const requestUrl = params.toString()
            ? `${config.dataUrl}?${params.toString()}`
            : config.dataUrl;

        renderMessageRow('Memuat data aktivitas...');

        try {
            const response = await fetch(requestUrl, {
                headers: { Accept: 'application/json' },
                signal: activeController.signal
            });

            const payload = await response.json().catch(() => ({}));

            if (!response.ok) {
                if (response.status === 422 && payload.message) {
                    alert(payload.message);
                }
                throw new Error(`HTTP ${response.status}`);
            }

            activities = Array.isArray(payload.activities) ? payload.activities : [];
            summary = payload.summary || {
                total: activities.length,
                order: 0,
                production: 0,
                inventory: 0
            };

            currentPage = 1;
            renderSummary();
            renderActivities();
        } catch (error) {
            if (error.name === 'AbortError') return;

            activities = [];
            summary = { total: 0, order: 0, production: 0, inventory: 0 };
            renderSummary();
            renderMessageRow('Gagal memuat data aktivitas. Silakan coba lagi.');
        }
    }

    function renderActivities() {
        const totalData = activities.length;
        const totalPages = Math.ceil(totalData / rowsPerPage);

        if (totalPages > 0 && currentPage > totalPages) {
            currentPage = totalPages;
        }

        const startIndex = (currentPage - 1) * rowsPerPage;
        const endIndex = Math.min(startIndex + rowsPerPage, totalData);
        const currentData = activities.slice(startIndex, endIndex);

        if (totalData === 0) {
            const hasFilter = searchInput.value.trim() !== ''
                || categoryInput.value !== 'all'
                || startDateInput.value !== ''
                || endDateInput.value !== '';

            renderMessageRow(
                hasFilter
                    ? 'Tidak ada aktivitas yang sesuai dengan filter.'
                    : 'Belum ada aktivitas yang tercatat di database.'
            );
            return;
        }

        tableBody.innerHTML = currentData.map(function (item) {
            return `
                <tr>
                    <td class="activity-id">${escapeHTML(item.id)}</td>
                    <td>${escapeHTML(formatDateTime(item))}</td>
                    <td>
                        <span class="activity-category ${getCategoryClass(item.entity_type)}">
                            ${escapeHTML(item.category)}
                        </span>
                    </td>
                    <td class="activity-description-cell">${escapeHTML(item.action)}</td>
                    <td>
                        <div class="activity-user">
                            <span class="activity-user-avatar">
                                ${escapeHTML(getInitials(item.user))}
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

    function openDetail(activityId) {
        const item = activities.find(activity => String(activity.id) === String(activityId));

        if (!item) return;

        const userLabel = item.user_role && item.user_role !== 'Sistem'
            ? `${item.user} (${item.user_role})`
            : item.user;

        document.getElementById('activityModalBody').innerHTML = `
            <div class="activity-detail-row">
                <span class="activity-detail-label">ID Aktivitas</span>
                <span class="activity-detail-value">${escapeHTML(item.id)}</span>
            </div>

            <div class="activity-detail-row">
                <span class="activity-detail-label">Tanggal dan Waktu</span>
                <span class="activity-detail-value">${escapeHTML(formatDateTime(item))}</span>
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
                <span class="activity-detail-value">${escapeHTML(userLabel)}</span>
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
        link.remove();

        setTimeout(function () {
            URL.revokeObjectURL(url);
        }, 1000);
    }

    function exportCSV() {
        if (activities.length === 0) {
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

        const rows = activities.map(function (item) {
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
                return '"' + String(value ?? '').replace(/"/g, '""') + '"';
            }).join(',');
        }).join('\r\n');

        const blob = new Blob(['\uFEFF' + csv], {
            type: 'text/csv;charset=utf-8;'
        });

        downloadBlob(blob, getExportFileName('csv'));
    }

    function exportExcel() {
        if (activities.length === 0) {
            alert('Tidak ada aktivitas untuk diekspor.');
            return;
        }

        const rows = activities.map(function (item) {
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

    searchInput.addEventListener('input', function () {
        clearTimeout(searchDebounceTimer);
        searchDebounceTimer = setTimeout(fetchActivities, 250);
    });

    categoryInput.addEventListener('change', fetchActivities);
    startDateInput.addEventListener('change', fetchActivities);
    endDateInput.addEventListener('change', fetchActivities);

    document.getElementById('resetFilter').addEventListener('click', function () {
        clearTimeout(searchDebounceTimer);
        searchInput.value = '';
        categoryInput.value = 'all';
        startDateInput.value = '';
        endDateInput.value = '';

        currentPage = 1;
        fetchActivities();
    });

    pagination.addEventListener('click', function (event) {
        const button = event.target.closest('[data-page]');

        if (!button || button.disabled) return;

        const selectedPage = Number(button.dataset.page);
        const totalPages = Math.ceil(activities.length / rowsPerPage);

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

    fetchActivities();
});
