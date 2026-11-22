document.addEventListener('DOMContentLoaded', function () {
    const config = window.masterDataConfig || {};

    const datasets = {
        products: {
            title: 'Data Produk',
            description: 'Informasi seluruh produk induk beserta daftar varian SKU yang terdaftar.',
            columns: ['SKU Produk', 'Nama Produk', 'Varian', 'Kategori', 'Harga Jual', 'Total Stok', 'Status'],
            statusOptions: ['Aktif', 'Stok Rendah', 'Tidak Aktif'],
            data: []
        },

        categories: {
            title: 'Data Kategori',
            description: 'Informasi kategori yang digunakan untuk mengelompokkan produk.',
            columns: ['Kode', 'Nama Kategori', 'Jumlah Produk'],
            statusOptions: [],
            data: []
        },

        materials: {
            title: 'Data Bahan Baku',
            description: 'Informasi persediaan bahan baku yang digunakan dalam produksi.',
            columns: ['Kode', 'Nama Bahan', 'Satuan', 'Stok Tersedia', 'Stok Minimum', 'Status'],
            statusOptions: ['Tersedia', 'Stok Rendah', 'Tidak Aktif'],
            data: []
        },

        bom: {
            title: 'Bill of Materials',
            description: 'Komposisi bahan baku dan kebutuhan material untuk setiap produk.',
            columns: ['Kode BOM', 'Nama Produk', 'Bahan Baku', 'Kebutuhan', 'Satuan'],
            statusOptions: [],
            data: []
        },

        suppliers: {
            title: 'Data Supplier',
            description: 'Informasi pemasok bahan baku dan kontak yang terdaftar.',
            columns: ['Kode', 'Nama Supplier', 'Kontak', 'Alamat', 'Jenis Bahan', 'Status'],
            statusOptions: ['Aktif', 'Tidak Aktif'],
            data: []
        }
    };

    const tabButtons = document.querySelectorAll('.master-tab');
    const tableHead = document.getElementById('masterTableHead');
    const tableBody = document.getElementById('masterTableBody');
    const tableTitle = document.getElementById('tableTitle');
    const tableDescription = document.getElementById('tableDescription');
    const searchInput = document.getElementById('masterSearch');
    const statusFilter = document.getElementById('masterStatus');
    const pagination = document.getElementById('masterPagination');
    const info = document.getElementById('masterInfo');
    const modal = document.getElementById('masterDetailModal');
    const detailTitle = document.getElementById('detailModalTitle');
    const detailContent = document.getElementById('detailModalContent');
    const summaryProductsCaption = document.getElementById('summaryProductsCaption');

    let summary = { products: 0, product_variants: 0, categories: 0, materials: 0, suppliers: 0 };
    let currentTab = 'products';
    let currentPage = 1;
    const perPage = 10;
    const expandedGroups = new Set();
    let isLoading = false;
    let loadError = false;

    function formatRupiah(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        }).format(Number(value) || 0);
    }

    function formatNumber(value, maxDecimals = 3) {
        return new Intl.NumberFormat('id-ID', {
            maximumFractionDigits: maxDecimals
        }).format(Number(value) || 0);
    }

    function escapeHTML(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (char) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[char];
        });
    }

    function getStatusClass(status) {
        if (status === 'Aktif') return 'status-active';
        if (status === 'Tidak Aktif') return 'status-inactive';
        if (status === 'Tersedia') return 'status-available';
        if (status === 'Stok Rendah') return 'status-low';
        return 'status-neutral';
    }

    function updateStatusFilter() {
        const options = datasets[currentTab].statusOptions || [];

        if (options.length === 0) {
            statusFilter.style.display = 'none';
            statusFilter.innerHTML = '<option value="">Semua Status</option>';
            statusFilter.value = '';
            return;
        }

        statusFilter.style.display = '';
        statusFilter.innerHTML = [
            '<option value="">Semua Status</option>',
            ...options.map(option => `<option value="${escapeHTML(option)}">${escapeHTML(option)}</option>`)
        ].join('');
    }

    function variantMatchesKeyword(variant, keyword) {
        if (!keyword) return false;

        return `${variant.sku || ''} ${variant.variant_name || ''} ${variant.status || ''}`
            .toLowerCase()
            .includes(keyword);
    }

    function getFilteredData() {
        const dataset = datasets[currentTab];
        const keyword = searchInput.value.toLowerCase().trim();
        const status = statusFilter.value;

        return dataset.data.filter(item => {
            const baseValues = Object.entries(item)
                .filter(([key]) => key !== 'detail' && key !== 'variants' && key !== 'id')
                .map(([, value]) => String(value ?? ''));

            const detailValues = Array.isArray(item.detail)
                ? item.detail.map(entry => String(entry?.value ?? ''))
                : [];

            const variantValues = Array.isArray(item.variants)
                ? item.variants.map(variant => `${variant.sku || ''} ${variant.variant_name || ''} ${variant.status || ''}`)
                : [];

            const searchable = [...baseValues, ...detailValues, ...variantValues].join(' ').toLowerCase();
            const matchesSearch = keyword === '' || searchable.includes(keyword);

            const itemStatus = item.status || '';
            const matchesStatus = !status || itemStatus === status;

            return matchesSearch && matchesStatus;
        });
    }

    function renderVariantSubRow(item, colspan, keyword) {
        const variants = Array.isArray(item.variants) ? item.variants : [];
        const isMto = item.production_method === 'MTO';

        const variantRowsHtml = variants.length === 0
            ? '<tr><td colspan="6" class="empty-row">Belum ada varian SKU terdaftar.</td></tr>'
            : variants.map(variant => {
                const isMatch = variantMatchesKeyword(variant, keyword);
                const minStockText = !isMto && variant.min_stock !== null
                    ? `${formatNumber(variant.min_stock, 0)} pcs`
                    : '—';
                const targetStockText = !isMto && variant.target_stock !== null
                    ? `${formatNumber(variant.target_stock, 0)} pcs`
                    : '—';

                return `
                    <tr class="${isMatch ? 'is-match' : ''}">
                        <td><strong>${escapeHTML(variant.sku)}</strong></td>
                        <td>${escapeHTML(variant.variant_name)}</td>
                        <td>${escapeHTML(formatNumber(variant.stock, 0))} pcs</td>
                        <td>${escapeHTML(minStockText)}</td>
                        <td>${escapeHTML(targetStockText)}</td>
                        <td>${renderStatus(variant.status)}</td>
                    </tr>
                `;
            }).join('');

        return `
            <tr class="variant-sub-row">
                <td colspan="${colspan}" class="variant-sub-cell">
                    <div class="variant-sub-box">
                        <div class="variant-sub-header">
                            <strong>Daftar Varian — ${escapeHTML(item.name)}</strong>
                            <span>${variants.length} varian (${escapeHTML(item.production_method || '—')})</span>
                        </div>
                        <table class="variant-sub-table">
                            <thead>
                                <tr>
                                    <th>SKU Varian</th>
                                    <th>Nama Varian</th>
                                    <th>Stok Tersedia</th>
                                    <th>Stok Minimum</th>
                                    <th>Target Stok</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${variantRowsHtml}
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
        `;
    }

    function renderTable() {
        const dataset = datasets[currentTab];
        const totalColumns = dataset.columns.length + 1;

        tableTitle.textContent = dataset.title;
        tableDescription.textContent = dataset.description;

        tableHead.innerHTML = `
            <tr>
                ${dataset.columns.map(column => `<th>${escapeHTML(column)}</th>`).join('')}
                <th>Aksi</th>
            </tr>
        `;

        if (isLoading) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="${totalColumns}" class="empty-row">
                        Memuat data dari database...
                    </td>
                </tr>
            `;
            info.textContent = 'Memuat data...';
            pagination.innerHTML = '';
            return;
        }

        if (loadError) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="${totalColumns}" class="empty-row">
                        Gagal memuat data master. Silakan muat ulang halaman.
                    </td>
                </tr>
            `;
            info.textContent = 'Menampilkan 0 data';
            pagination.innerHTML = '';
            return;
        }

        const keyword = searchInput.value.toLowerCase().trim();
        const filtered = getFilteredData();
        const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));

        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        const startIndex = (currentPage - 1) * perPage;
        const pageData = filtered.slice(startIndex, startIndex + perPage);

        if (pageData.length === 0) {
            const emptyMessage = dataset.data.length === 0
                ? 'Belum ada data terdaftar.'
                : 'Tidak ada data yang sesuai dengan pencarian.';

            tableBody.innerHTML = `
                <tr>
                    <td colspan="${totalColumns}" class="empty-row">
                        ${emptyMessage}
                    </td>
                </tr>
            `;
        } else {
            tableBody.innerHTML = pageData.map(item => {
                const hasVariantMatch = currentTab === 'products'
                    && keyword !== ''
                    && Array.isArray(item.variants)
                    && item.variants.some(variant => variantMatchesKeyword(variant, keyword));

                const isExpanded = currentTab === 'products'
                    && (expandedGroups.has(String(item.id)) || hasVariantMatch);

                const mainRow = `
                    <tr>
                        ${renderCells(item, isExpanded)}
                        <td>
                            <button
                                class="detail-button"
                                type="button"
                                data-detail-id="${escapeHTML(item.id)}"
                            >
                                Detail
                            </button>
                        </td>
                    </tr>
                `;

                if (currentTab === 'products' && isExpanded) {
                    return mainRow + renderVariantSubRow(item, totalColumns, keyword);
                }

                return mainRow;
            }).join('');
        }

        const showingStart = filtered.length ? startIndex + 1 : 0;
        const showingEnd = Math.min(startIndex + perPage, filtered.length);

        info.textContent = `Menampilkan ${showingStart}–${showingEnd} dari ${filtered.length} data`;

        renderPagination(totalPages);
    }

    function renderCells(item, isExpanded = false) {
        switch (currentTab) {
            case 'products': {
                const variantCount = Number(item.variant_count || 0);
                const metaParts = [item.production_method, item.dimensions && item.dimensions !== '—' ? item.dimensions : null]
                    .filter(Boolean)
                    .join(' · ');
                const lowNote = Number(item.low_stock_count || 0) > 0
                    ? `<span class="table-low-note">${item.low_stock_count} varian stok rendah</span>`
                    : '';

                return `
                    <td>${escapeHTML(item.code)}</td>
                    <td>
                        <span class="table-primary">${escapeHTML(item.name)}</span>
                        ${metaParts ? `<span class="table-secondary">${escapeHTML(metaParts)}</span>` : ''}
                        ${lowNote}
                    </td>
                    <td>
                        <button
                            type="button"
                            class="variant-toggle-button ${isExpanded ? 'is-open' : ''}"
                            data-toggle-group="${escapeHTML(item.id)}"
                            aria-expanded="${isExpanded ? 'true' : 'false'}"
                        >
                            ${variantCount} Varian ${isExpanded ? '▴' : '▾'}
                        </button>
                    </td>
                    <td>${escapeHTML(item.category)}</td>
                    <td>${escapeHTML(formatRupiah(item.price))}</td>
                    <td>${escapeHTML(formatNumber(item.stock, 0))} pcs</td>
                    <td>${renderStatus(item.status)}</td>
                `;
            }

            case 'categories':
                return `
                    <td>${escapeHTML(item.code)}</td>
                    <td><span class="table-primary">${escapeHTML(item.name)}</span></td>
                    <td>${escapeHTML(formatNumber(item.total, 0))} produk</td>
                `;

            case 'materials':
                return `
                    <td>${escapeHTML(item.code)}</td>
                    <td><span class="table-primary">${escapeHTML(item.name)}</span></td>
                    <td>${escapeHTML(item.unit)}</td>
                    <td>${escapeHTML(formatNumber(item.stock, 3))}</td>
                    <td>${escapeHTML(formatNumber(item.minimum, 3))}</td>
                    <td>${renderStatus(item.status)}</td>
                `;

            case 'bom':
                return `
                    <td>${escapeHTML(item.code)}</td>
                    <td><span class="table-primary">${escapeHTML(item.product)}</span></td>
                    <td>${escapeHTML(item.material)}</td>
                    <td>${escapeHTML(formatNumber(item.quantity, 6))}</td>
                    <td>${escapeHTML(item.unit)}</td>
                `;

            case 'suppliers':
                return `
                    <td>${escapeHTML(item.code)}</td>
                    <td><span class="table-primary">${escapeHTML(item.name)}</span></td>
                    <td>${escapeHTML(item.contact)}</td>
                    <td>${escapeHTML(item.address)}</td>
                    <td>${escapeHTML(item.material)}</td>
                    <td>${renderStatus(item.status)}</td>
                `;

            default:
                return '';
        }
    }

    function renderStatus(status) {
        if (!status) return '-';

        return `<span class="status-pill ${getStatusClass(status)}">${escapeHTML(status)}</span>`;
    }

    function renderPagination(totalPages) {
        let html = `
            <button class="page-button" data-page="${currentPage - 1}" ${currentPage === 1 ? 'disabled' : ''}>
                ‹
            </button>
        `;

        for (let page = 1; page <= totalPages; page++) {
            html += `
                <button
                    class="page-button ${page === currentPage ? 'active' : ''}"
                    data-page="${page}"
                >
                    ${page}
                </button>
            `;
        }

        html += `
            <button class="page-button" data-page="${currentPage + 1}" ${currentPage === totalPages ? 'disabled' : ''}>
                ›
            </button>
        `;

        pagination.innerHTML = html;
    }

    function renderSummary() {
        document.getElementById('summaryProducts').textContent = summary.products ?? datasets.products.data.length;
        document.getElementById('summaryCategories').textContent = summary.categories ?? datasets.categories.data.length;
        document.getElementById('summaryMaterials').textContent = summary.materials ?? datasets.materials.data.length;
        document.getElementById('summarySuppliers').textContent = summary.suppliers ?? datasets.suppliers.data.length;

        if (summaryProductsCaption) {
            const variantTotal = summary.product_variants ?? 0;
            summaryProductsCaption.textContent = `${formatNumber(variantTotal, 0)} varian SKU terdaftar`;
        }
    }

    function openDetail(id) {
        const item = datasets[currentTab].data.find(row => String(row.id) === String(id));

        if (!item) return;

        detailTitle.textContent = 'Detail ' + datasets[currentTab].title;

        const detailRows = Array.isArray(item.detail) ? item.detail : [];

        let html = detailRows.map(entry => {
            let renderedValue;

            if (entry.type === 'currency') {
                renderedValue = escapeHTML(formatRupiah(entry.value));
            } else if (entry.type === 'status') {
                renderedValue = renderStatus(entry.value);
            } else {
                renderedValue = escapeHTML(entry.value);
            }

            return `
                <div class="detail-row">
                    <span class="detail-label">${escapeHTML(entry.label)}</span>
                    <span class="detail-value">${renderedValue}</span>
                </div>
            `;
        }).join('');

        if (currentTab === 'products' && Array.isArray(item.variants) && item.variants.length > 0) {
            const isMto = item.production_method === 'MTO';
            const variantItemsHtml = item.variants.map(variant => {
                const stockInfo = isMto
                    ? `Stok: ${formatNumber(variant.stock, 0)} pcs (MTO)`
                    : `Stok: ${formatNumber(variant.stock, 0)} pcs · Min: ${variant.min_stock !== null ? formatNumber(variant.min_stock, 0) : '—'} · Target: ${variant.target_stock !== null ? formatNumber(variant.target_stock, 0) : '—'}`;

                return `
                    <div class="detail-variant-item">
                        <div class="detail-variant-info">
                            <strong>${escapeHTML(variant.sku)} — ${escapeHTML(variant.variant_name)}</strong>
                            <span>${escapeHTML(stockInfo)}</span>
                        </div>
                        ${renderStatus(variant.status)}
                    </div>
                `;
            }).join('');

            html += `
                <div class="detail-variants-section">
                    <span class="detail-variants-title">Varian SKU (${item.variants.length})</span>
                    <div class="detail-variant-list">
                        ${variantItemsHtml}
                    </div>
                </div>
            `;
        }

        detailContent.innerHTML = html;
        modal.classList.add('show');
    }

    function closeModal() {
        modal.classList.remove('show');
    }

    async function loadMasterData() {
        if (!config.dataUrl) {
            loadError = true;
            renderSummary();
            renderTable();
            return;
        }

        isLoading = true;
        loadError = false;
        renderTable();

        try {
            const response = await fetch(config.dataUrl, {
                headers: {
                    Accept: 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }

            const payload = await response.json();
            const incomingDatasets = payload.datasets || {};

            Object.keys(datasets).forEach(key => {
                datasets[key].data = Array.isArray(incomingDatasets[key]) ? incomingDatasets[key] : [];
            });

            summary = payload.summary || {
                products: datasets.products.data.length,
                product_variants: 0,
                categories: datasets.categories.data.length,
                materials: datasets.materials.data.length,
                suppliers: datasets.suppliers.data.length
            };
        } catch (error) {
            loadError = true;
        } finally {
            isLoading = false;
            renderSummary();
            renderTable();
        }
    }

    tabButtons.forEach(button => {
        button.addEventListener('click', function () {
            tabButtons.forEach(tab => tab.classList.remove('active'));
            this.classList.add('active');

            currentTab = this.dataset.tab;
            currentPage = 1;
            searchInput.value = '';
            statusFilter.value = '';

            updateStatusFilter();
            renderTable();
        });
    });

    searchInput.addEventListener('input', function () {
        currentPage = 1;
        renderTable();
    });

    statusFilter.addEventListener('change', function () {
        currentPage = 1;
        renderTable();
    });

    document.getElementById('resetMasterFilter').addEventListener('click', function () {
        searchInput.value = '';
        statusFilter.value = '';
        currentPage = 1;
        renderTable();
    });

    pagination.addEventListener('click', function (event) {
        const button = event.target.closest('[data-page]');
        if (!button || button.disabled) return;

        currentPage = Number(button.dataset.page);
        renderTable();
    });

    tableBody.addEventListener('click', function (event) {
        const toggleButton = event.target.closest('[data-toggle-group]');
        if (toggleButton) {
            const groupId = String(toggleButton.dataset.toggleGroup);

            if (expandedGroups.has(groupId)) {
                expandedGroups.delete(groupId);
            } else {
                expandedGroups.add(groupId);
            }

            renderTable();
            return;
        }

        const detailButton = event.target.closest('[data-detail-id]');
        if (!detailButton) return;

        openDetail(detailButton.dataset.detailId);
    });

    modal.querySelectorAll('[data-close-modal]').forEach(button => {
        button.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') closeModal();
    });

    updateStatusFilter();
    renderSummary();
    loadMasterData();
});
