
document.addEventListener('DOMContentLoaded', function () {
    const datasets = {
        products: {
            title: 'Data Produk',
            description: 'Informasi seluruh produk yang terdaftar dalam sistem.',
            columns: ['SKU', 'Nama Produk', 'Kategori', 'Harga Jual', 'Stok', 'Status'],
            data: [
                { id: 'PRD-001', sku: 'REB-001', name: 'Wall Decor Minimalist', category: 'Wall Decor', price: 125000, stock: 35, status: 'Aktif' },
                { id: 'PRD-002', sku: 'REB-002', name: 'Wooden Flower Frame', category: 'Wall Frame', price: 150000, stock: 18, status: 'Aktif' },
                { id: 'PRD-003', sku: 'REB-003', name: 'Macrame Wall Hanging', category: 'Macrame', price: 95000, stock: 8, status: 'Aktif' },
                { id: 'PRD-004', sku: 'REB-004', name: 'Boho Mirror Decor', category: 'Wall Decor', price: 175000, stock: 4, status: 'Aktif' },
                { id: 'PRD-005', sku: 'REB-005', name: 'Rustic Photo Frame', category: 'Wall Frame', price: 85000, stock: 22, status: 'Aktif' },
                { id: 'PRD-006', sku: 'REB-006', name: 'Minimalist Wooden Shelf', category: 'Wall Shelf', price: 210000, stock: 0, status: 'Tidak Aktif' },
                { id: 'PRD-007', sku: 'REB-007', name: 'Round Rattan Decor', category: 'Wall Decor', price: 135000, stock: 16, status: 'Aktif' },
                { id: 'PRD-008', sku: 'REB-008', name: 'Abstract Canvas Art', category: 'Canvas Art', price: 185000, stock: 12, status: 'Aktif' },
                { id: 'PRD-009', sku: 'REB-009', name: 'Wooden Quote Board', category: 'Wall Decor', price: 110000, stock: 25, status: 'Aktif' },
                { id: 'PRD-010', sku: 'REB-010', name: 'Natural Macrame Set', category: 'Macrame', price: 160000, stock: 9, status: 'Aktif' },
                { id: 'PRD-011', sku: 'REB-011', name: 'Geometric Wall Art', category: 'Canvas Art', price: 145000, stock: 14, status: 'Aktif' },
                { id: 'PRD-012', sku: 'REB-012', name: 'Wooden Hanging Planter', category: 'Wall Decor', price: 120000, stock: 6, status: 'Aktif' }
            ]
        },

        categories: {
            title: 'Data Kategori',
            description: 'Informasi kategori yang digunakan untuk mengelompokkan produk.',
            columns: ['Kode', 'Nama Kategori', 'Deskripsi', 'Jumlah Produk', 'Status'],
            data: [
                { id: 'CAT-001', code: 'CAT-001', name: 'Wall Decor', description: 'Dekorasi dinding berbagai desain.', total: 4, status: 'Aktif' },
                { id: 'CAT-002', code: 'CAT-002', name: 'Wall Frame', description: 'Produk dekorasi berbentuk bingkai.', total: 2, status: 'Aktif' },
                { id: 'CAT-003', code: 'CAT-003', name: 'Macrame', description: 'Dekorasi berbahan tali dan simpul.', total: 2, status: 'Aktif' },
                { id: 'CAT-004', code: 'CAT-004', name: 'Wall Shelf', description: 'Rak dekoratif untuk dinding.', total: 1, status: 'Aktif' },
                { id: 'CAT-005', code: 'CAT-005', name: 'Canvas Art', description: 'Dekorasi dinding berbahan kanvas.', total: 2, status: 'Aktif' }
            ]
        },

        materials: {
            title: 'Data Bahan Baku',
            description: 'Informasi persediaan bahan baku yang digunakan dalam produksi.',
            columns: ['Kode', 'Nama Bahan', 'Satuan', 'Stok Tersedia', 'Stok Minimum', 'Status'],
            data: [
                { id: 'MAT-001', code: 'MAT-001', name: 'Kayu Pinus', unit: 'Pcs', stock: 45, minimum: 15, status: 'Tersedia' },
                { id: 'MAT-002', code: 'MAT-002', name: 'Tali Katun', unit: 'Meter', stock: 8, minimum: 20, status: 'Stok Rendah' },
                { id: 'MAT-003', code: 'MAT-003', name: 'Lem Kayu', unit: 'Botol', stock: 25, minimum: 10, status: 'Tersedia' },
                { id: 'MAT-004', code: 'MAT-004', name: 'Cat Kayu', unit: 'Kaleng', stock: 12, minimum: 5, status: 'Tersedia' },
                { id: 'MAT-005', code: 'MAT-005', name: 'Rotan Sintetis', unit: 'Meter', stock: 6, minimum: 15, status: 'Stok Rendah' },
                { id: 'MAT-006', code: 'MAT-006', name: 'Kanvas', unit: 'Lembar', stock: 30, minimum: 10, status: 'Tersedia' },
                { id: 'MAT-007', code: 'MAT-007', name: 'Paku Kecil', unit: 'Box', stock: 18, minimum: 5, status: 'Tersedia' },
                { id: 'MAT-008', code: 'MAT-008', name: 'Kawat Gantungan', unit: 'Roll', stock: 3, minimum: 8, status: 'Stok Rendah' },
                { id: 'MAT-009', code: 'MAT-009', name: 'Vernis Kayu', unit: 'Botol', stock: 14, minimum: 5, status: 'Tersedia' },
                { id: 'MAT-010', code: 'MAT-010', name: 'Ring Besi', unit: 'Pcs', stock: 50, minimum: 15, status: 'Tersedia' }
            ]
        },

        bom: {
            title: 'Bill of Materials',
            description: 'Komposisi bahan baku dan kebutuhan material untuk setiap produk.',
            columns: ['Kode BOM', 'Nama Produk', 'Bahan Baku', 'Kebutuhan', 'Satuan'],
            data: [
                { id: 'BOM-001', code: 'BOM-001', product: 'Wall Decor Minimalist', material: 'Kayu Pinus', quantity: 2, unit: 'Pcs' },
                { id: 'BOM-002', code: 'BOM-002', product: 'Wall Decor Minimalist', material: 'Lem Kayu', quantity: 0.1, unit: 'Botol' },
                { id: 'BOM-003', code: 'BOM-003', product: 'Wooden Flower Frame', material: 'Kayu Pinus', quantity: 3, unit: 'Pcs' },
                { id: 'BOM-004', code: 'BOM-004', product: 'Wooden Flower Frame', material: 'Cat Kayu', quantity: 0.2, unit: 'Kaleng' },
                { id: 'BOM-005', code: 'BOM-005', product: 'Macrame Wall Hanging', material: 'Tali Katun', quantity: 5, unit: 'Meter' },
                { id: 'BOM-006', code: 'BOM-006', product: 'Boho Mirror Decor', material: 'Rotan Sintetis', quantity: 2, unit: 'Meter' },
                { id: 'BOM-007', code: 'BOM-007', product: 'Rustic Photo Frame', material: 'Kayu Pinus', quantity: 2, unit: 'Pcs' },
                { id: 'BOM-008', code: 'BOM-008', product: 'Abstract Canvas Art', material: 'Kanvas', quantity: 1, unit: 'Lembar' },
                { id: 'BOM-009', code: 'BOM-009', product: 'Wooden Quote Board', material: 'Vernis Kayu', quantity: 0.1, unit: 'Botol' },
                { id: 'BOM-010', code: 'BOM-010', product: 'Natural Macrame Set', material: 'Tali Katun', quantity: 8, unit: 'Meter' }
            ]
        },

        suppliers: {
            title: 'Data Supplier',
            description: 'Informasi pemasok bahan baku dan kontak yang terdaftar.',
            columns: ['Kode', 'Nama Supplier', 'Kontak', 'Alamat', 'Jenis Bahan', 'Status'],
            data: [
                { id: 'SUP-001', code: 'SUP-001', name: 'CV Kayu Sejahtera', contact: '081234567801', address: 'Malang', material: 'Kayu Pinus', status: 'Aktif' },
                { id: 'SUP-002', code: 'SUP-002', name: 'Katun Nusantara', contact: '081234567802', address: 'Surabaya', material: 'Tali Katun', status: 'Aktif' },
                { id: 'SUP-003', code: 'SUP-003', name: 'Mitra Lemindo', contact: '081234567803', address: 'Malang', material: 'Lem Kayu', status: 'Aktif' },
                { id: 'SUP-004', code: 'SUP-004', name: 'Rotan Jaya', contact: '081234567804', address: 'Pasuruan', material: 'Rotan Sintetis', status: 'Aktif' },
                { id: 'SUP-005', code: 'SUP-005', name: 'Kanvas Kreatif', contact: '081234567805', address: 'Sidoarjo', material: 'Kanvas', status: 'Tidak Aktif' },
                { id: 'SUP-006', code: 'SUP-006', name: 'Warna Abadi', contact: '081234567806', address: 'Malang', material: 'Cat dan Vernis', status: 'Aktif' }
            ]
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

    let currentTab = 'products';
    let currentPage = 1;
    const perPage = 10;

    function formatRupiah(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        }).format(value);
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

    function getFilteredData() {
        const dataset = datasets[currentTab];
        const keyword = searchInput.value.toLowerCase().trim();
        const status = statusFilter.value;

        return dataset.data.filter(item => {
            const searchable = Object.values(item).join(' ').toLowerCase();
            const matchesSearch = searchable.includes(keyword);

            const itemStatus = item.status || '';
            const matchesStatus = !status || itemStatus === status;

            return matchesSearch && matchesStatus;
        });
    }

    function renderTable() {
        const dataset = datasets[currentTab];
        const filtered = getFilteredData();

        tableTitle.textContent = dataset.title;
        tableDescription.textContent = dataset.description;

        tableHead.innerHTML = `
            <tr>
                ${dataset.columns.map(column => `<th>${escapeHTML(column)}</th>`).join('')}
                <th>Aksi</th>
            </tr>
        `;

        const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));

        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        const startIndex = (currentPage - 1) * perPage;
        const pageData = filtered.slice(startIndex, startIndex + perPage);

        if (pageData.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="${dataset.columns.length + 1}" class="empty-row">
                        Tidak ada data yang sesuai dengan pencarian.
                    </td>
                </tr>
            `;
        } else {
            tableBody.innerHTML = pageData.map(item => {
                return `
                    <tr>
                        ${renderCells(item)}
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
            }).join('');
        }

        const showingStart = filtered.length ? startIndex + 1 : 0;
        const showingEnd = Math.min(startIndex + perPage, filtered.length);

        info.textContent = `Menampilkan ${showingStart}–${showingEnd} dari ${filtered.length} data`;

        renderPagination(totalPages);
    }

    function renderCells(item) {
        switch (currentTab) {
            case 'products':
                return `
                    <td>${escapeHTML(item.sku)}</td>
                    <td><span class="table-primary">${escapeHTML(item.name)}</span></td>
                    <td>${escapeHTML(item.category)}</td>
                    <td>${formatRupiah(item.price)}</td>
                    <td>${item.stock} pcs</td>
                    <td>${renderStatus(item.status)}</td>
                `;

            case 'categories':
                return `
                    <td>${escapeHTML(item.code)}</td>
                    <td><span class="table-primary">${escapeHTML(item.name)}</span></td>
                    <td>${escapeHTML(item.description)}</td>
                    <td>${item.total} produk</td>
                    <td>${renderStatus(item.status)}</td>
                `;

            case 'materials':
                return `
                    <td>${escapeHTML(item.code)}</td>
                    <td><span class="table-primary">${escapeHTML(item.name)}</span></td>
                    <td>${escapeHTML(item.unit)}</td>
                    <td>${item.stock}</td>
                    <td>${item.minimum}</td>
                    <td>${renderStatus(item.status)}</td>
                `;

            case 'bom':
                return `
                    <td>${escapeHTML(item.code)}</td>
                    <td><span class="table-primary">${escapeHTML(item.product)}</span></td>
                    <td>${escapeHTML(item.material)}</td>
                    <td>${item.quantity}</td>
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
        document.getElementById('summaryProducts').textContent = datasets.products.data.length;
        document.getElementById('summaryCategories').textContent = datasets.categories.data.length;
        document.getElementById('summaryMaterials').textContent = datasets.materials.data.length;
        document.getElementById('summarySuppliers').textContent = datasets.suppliers.data.length;
    }

    function openDetail(id) {
        const item = datasets[currentTab].data.find(row => row.id === id);

        if (!item) return;

        detailTitle.textContent = 'Detail ' + datasets[currentTab].title;

        detailContent.innerHTML = Object.entries(item).map(([key, value]) => {
            let label = key
                .replace(/([A-Z])/g, ' $1')
                .replace(/^./, char => char.toUpperCase());

            if (key === 'price') {
                value = formatRupiah(value);
            }

            if (key === 'status') {
                value = renderStatus(value);
            } else {
                value = escapeHTML(value);
            }

            return `
                <div class="detail-row">
                    <span class="detail-label">${escapeHTML(label)}</span>
                    <span class="detail-value">${value}</span>
                </div>
            `;
        }).join('');

        modal.classList.add('show');
    }

    function closeModal() {
        modal.classList.remove('show');
    }

    tabButtons.forEach(button => {
        button.addEventListener('click', function () {
            tabButtons.forEach(tab => tab.classList.remove('active'));
            this.classList.add('active');

            currentTab = this.dataset.tab;
            currentPage = 1;
            searchInput.value = '';
            statusFilter.value = '';

            statusFilter.style.display = ['products', 'categories', 'materials', 'suppliers'].includes(currentTab)
                ? ''
                : 'none';

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
        const button = event.target.closest('[data-detail-id]');
        if (!button) return;

        openDetail(button.dataset.detailId);
    });

    modal.querySelectorAll('[data-close-modal]').forEach(button => {
        button.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') closeModal();
    });

    renderSummary();
    renderTable();
});
