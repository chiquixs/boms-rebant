
document.addEventListener('DOMContentLoaded', function () {
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');

    if (!startDateInput || !endDateInput) return;

    /*
    |--------------------------------------------------------------------------
    | DUMMY TRANSACTION DATA
    |--------------------------------------------------------------------------
    | Hanya transaksi Completed yang digunakan dalam laporan.
    | Data ini sementara dan nantinya bisa diganti dengan data backend.
    */

    const transactions = [
        {
            id: 'TRX-001',
            date: '2026-11-01',
            product: 'Wall Decor Minimalist',
            platform: 'Shopee',
            quantity: 2,
            total: 150000,
            status: 'Completed'
        },
        {
            id: 'TRX-002',
            date: '2026-11-02',
            product: 'Wall Decor Floral',
            platform: 'TikTok',
            quantity: 1,
            total: 85000,
            status: 'Completed'
        },
        {
            id: 'TRX-003',
            date: '2026-11-03',
            product: 'Wall Decor Minimalist',
            platform: 'Shopee',
            quantity: 3,
            total: 225000,
            status: 'Completed'
        },
        {
            id: 'TRX-004',
            date: '2026-11-04',
            product: 'Wall Decor Abstract',
            platform: 'TikTok',
            quantity: 2,
            total: 180000,
            status: 'Completed'
        },
        {
            id: 'TRX-005',
            date: '2026-11-05',
            product: 'Wall Decor Floral',
            platform: 'Shopee',
            quantity: 2,
            total: 170000,
            status: 'Completed'
        },
        {
            id: 'TRX-006',
            date: '2026-11-06',
            product: 'Wall Decor Minimalist',
            platform: 'TikTok',
            quantity: 1,
            total: 75000,
            status: 'Completed'
        },
        {
            id: 'TRX-007',
            date: '2026-11-07',
            product: 'Wall Decor Abstract',
            platform: 'Shopee',
            quantity: 1,
            total: 90000,
            status: 'Completed'
        },
        {
            id: 'TRX-008',
            date: '2026-11-08',
            product: 'Wall Decor Floral',
            platform: 'TikTok',
            quantity: 3,
            total: 255000,
            status: 'Completed'
        },
        {
            id: 'TRX-009',
            date: '2026-11-09',
            product: 'Wall Decor Minimalist',
            platform: 'Shopee',
            quantity: 2,
            total: 150000,
            status: 'Completed'
        },
        {
            id: 'TRX-010',
            date: '2026-11-10',
            product: 'Wall Decor Abstract',
            platform: 'TikTok',
            quantity: 2,
            total: 180000,
            status: 'Completed'
        },
        {
            id: 'TRX-011',
            date: '2026-11-11',
            product: 'Wall Decor Floral',
            platform: 'Shopee',
            quantity: 1,
            total: 85000,
            status: 'Completed'
        },
        {
            id: 'TRX-012',
            date: '2026-11-12',
            product: 'Wall Decor Minimalist',
            platform: 'TikTok',
            quantity: 2,
            total: 150000,
            status: 'Completed'
        },
        {
            id: 'TRX-013',
            date: '2026-11-13',
            product: 'Wall Decor Abstract',
            platform: 'Shopee',
            quantity: 1,
            total: 90000,
            status: 'Completed'
        },

        // Data tambahan untuk menguji filter periode
        {
            id: 'TRX-014',
            date: '2026-10-28',
            product: 'Wall Decor Floral',
            platform: 'Shopee',
            quantity: 2,
            total: 170000,
            status: 'Completed'
        },
        {
            id: 'TRX-015',
            date: '2026-10-29',
            product: 'Wall Decor Minimalist',
            platform: 'TikTok',
            quantity: 1,
            total: 75000,
            status: 'Completed'
        }
    ];

    const rupiah = function (number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        }).format(number);
    };

    const numberFormat = function (number) {
        return new Intl.NumberFormat('id-ID').format(number);
    };

    const formatDate = function (dateString) {
        const date = new Date(dateString + 'T00:00:00');

        return date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        });
    };

    const escapeHTML = function (value) {
        return String(value).replace(/[&<>"']/g, function (character) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[character];
        });
    };

    let filteredTransactions = [];

    const rowsPerPage = 10;
    let currentPage = 1;

    function getFilteredTransactions() {
        const start = startDateInput.value;
        const end = endDateInput.value;

        if (!start || !end) {
            alert('Silakan pilih tanggal awal dan tanggal akhir.');
            return null;
        }

        if (start > end) {
            alert('Tanggal awal tidak boleh melebihi tanggal akhir.');
            return null;
        }

        return transactions.filter(function (transaction) {
            return transaction.status === 'Completed' &&
                transaction.date >= start &&
                transaction.date <= end;
        });
    }

    function updatePeriodLabel() {
        document.getElementById('periodLabel').textContent =
            'Periode ' + formatDate(startDateInput.value) +
            ' – ' + formatDate(endDateInput.value);
    }

    function renderSummary(data) {
        const revenue = data.reduce(function (sum, item) {
            return sum + item.total;
        }, 0);

        const products = data.reduce(function (sum, item) {
            return sum + item.quantity;
        }, 0);

        const transactionCount = data.length;

        const average = transactionCount > 0
            ? revenue / transactionCount
            : 0;

        document.getElementById('totalRevenue').textContent = rupiah(revenue);
        document.getElementById('totalProducts').textContent = numberFormat(products);
        document.getElementById('totalTransactions').textContent = numberFormat(transactionCount);
        document.getElementById('averageOrderValue').textContent = rupiah(average);
    }

    function renderSalesChart(data) {
        const chart = document.getElementById('salesChart');

        if (data.length === 0) {
            chart.innerHTML = '<div class="chart-empty">Tidak ada data penjualan pada periode ini.</div>';
            return;
        }

        const dailyRevenue = {};

        data.forEach(function (item) {
            dailyRevenue[item.date] = (dailyRevenue[item.date] || 0) + item.total;
        });

        const dates = Object.keys(dailyRevenue).sort();
        const maxRevenue = Math.max(...Object.values(dailyRevenue), 1);

        chart.innerHTML = dates.map(function (date) {
            const revenue = dailyRevenue[date];
            const height = Math.max((revenue / maxRevenue) * 100, 3);
            const label = new Date(date + 'T00:00:00').toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short'
            });

            return `
                <div class="chart-column">
                    <div class="chart-bar-area">
                        <div class="chart-bar" style="height: ${height}%;">
                            <span class="chart-bar-value">${numberFormat(revenue / 1000)}k</span>
                        </div>
                    </div>
                    <span class="chart-label">${label}</span>
                </div>
            `;
        }).join('');
    }

    function renderPlatformChart(data) {
        const chart = document.getElementById('platformChart');

        const platformRevenue = {
            Shopee: 0,
            TikTok: 0
        };

        data.forEach(function (item) {
            if (Object.prototype.hasOwnProperty.call(platformRevenue, item.platform)) {
                platformRevenue[item.platform] += item.total;
            }
        });

        const totalRevenue = platformRevenue.Shopee + platformRevenue.TikTok;

        if (totalRevenue === 0) {
            chart.innerHTML = '<div class="chart-empty">Belum ada data platform.</div>';
            return;
        }

        const shopeePercentage = (platformRevenue.Shopee / totalRevenue) * 100;
        const tiktokPercentage = (platformRevenue.TikTok / totalRevenue) * 100;

        chart.innerHTML = `
            <div class="platform-item">
                <div class="platform-info">
                    <span class="platform-name">
                        <span class="platform-dot shopee-dot"></span>
                        Shopee
                    </span>
                    <span class="platform-value">${rupiah(platformRevenue.Shopee)}</span>
                </div>
                <div class="platform-progress">
                    <div class="platform-progress-fill shopee-fill"
                         style="width: ${shopeePercentage}%"></div>
                </div>
                <span class="platform-percentage">${shopeePercentage.toFixed(1)}% dari total omzet</span>
            </div>

            <div class="platform-item">
                <div class="platform-info">
                    <span class="platform-name">
                        <span class="platform-dot tiktok-dot"></span>
                        TikTok
                    </span>
                    <span class="platform-value">${rupiah(platformRevenue.TikTok)}</span>
                </div>
                <div class="platform-progress">
                    <div class="platform-progress-fill tiktok-fill"
                         style="width: ${tiktokPercentage}%"></div>
                </div>
                <span class="platform-percentage">${tiktokPercentage.toFixed(1)}% dari total omzet</span>
            </div>
        `;
    }

    function renderProductTable(data) {
        const tbody = document.getElementById('productTableBody');

        const products = {};

        data.forEach(function (item) {
            if (!products[item.product]) {
                products[item.product] = {
                    quantity: 0,
                    revenue: 0
                };
            }

            products[item.product].quantity += item.quantity;
            products[item.product].revenue += item.total;
        });

        const sortedProducts = Object.entries(products)
            .map(function ([name, value]) {
                return {
                    name: name,
                    quantity: value.quantity,
                    revenue: value.revenue
                };
            })
            .sort(function (a, b) {
                return b.quantity - a.quantity;
            });

        if (sortedProducts.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="empty-table">Tidak ada data produk pada periode ini.</td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = sortedProducts.map(function (product, index) {
            return `
                <tr>
                    <td><span class="rank-number">${index + 1}</span></td>
                    <td class="product-name-cell">${escapeHTML(product.name)}</td>
                    <td>${numberFormat(product.quantity)} unit</td>
                    <td>${rupiah(product.revenue)}</td>
                </tr>
            `;
        }).join('');
    }

    
    function renderTransactionTable(data) {
        const tbody = document.getElementById('transactionTableBody');
        const pagination = document.getElementById('paginationControls');

        const totalData = data.length;
        const totalPages = Math.ceil(totalData / rowsPerPage);

        // Pastikan halaman tetap valid setelah filter berubah
        if (totalPages > 0 && currentPage > totalPages) {
            currentPage = totalPages;
        }

        const startIndex = (currentPage - 1) * rowsPerPage;
        const endIndex = Math.min(startIndex + rowsPerPage, totalData);

        const currentData = data.slice(startIndex, endIndex);

        if (totalData === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="empty-table">
                        Tidak ada transaksi completed pada periode ini.
                    </td>
                </tr>
            `;

            document.getElementById('transactionCount').textContent =
                'Menampilkan 0 transaksi';

            pagination.innerHTML = '';
            return;
        }

        tbody.innerHTML = currentData.map(function (item) {
            const platformClass = item.platform === 'Shopee'
                ? 'shopee-pill'
                : 'tiktok-pill';

            return `
                <tr>
                    <td class="product-name-cell">${escapeHTML(item.id)}</td>
                    <td>${formatDate(item.date)}</td>
                    <td>${escapeHTML(item.product)}</td>
                    <td>
                        <span class="platform-pill ${platformClass}">
                            ${escapeHTML(item.platform)}
                        </span>
                    </td>
                    <td>${numberFormat(item.quantity)}</td>
                    <td>${rupiah(item.total)}</td>
                    <td><span class="status-completed">Completed</span></td>
                </tr>
            `;
        }).join('');

        // Informasi jumlah data
        document.getElementById('transactionCount').textContent =
            `Menampilkan ${startIndex + 1}–${endIndex} dari ${totalData} transaksi`;

        // Pagination buttons
        let paginationHTML = `
            <button
                type="button"
                class="pagination-button"
                data-page="${currentPage - 1}"
                ${currentPage === 1 ? 'disabled' : ''}
            >
                ‹
            </button>
        `;

        for (let page = 1; page <= totalPages; page++) {
            paginationHTML += `
                <button
                    type="button"
                    class="pagination-button ${page === currentPage ? 'active' : ''}"
                    data-page="${page}"
                >
                    ${page}
                </button>
            `;
        }

        paginationHTML += `
            <button
                type="button"
                class="pagination-button"
                data-page="${currentPage + 1}"
                ${currentPage === totalPages ? 'disabled' : ''}
            >
                ›
            </button>
        `;

        pagination.innerHTML = paginationHTML;

        // Event pagination
        pagination.querySelectorAll('[data-page]').forEach(function (button) {
            button.addEventListener('click', function () {
                const selectedPage = Number(this.dataset.page);

                if (selectedPage >= 1 && selectedPage <= totalPages) {
                    currentPage = selectedPage;
                    renderTransactionTable(filteredTransactions);
                }
            });
        });
    }


    function updateReport() {
        const data = getFilteredTransactions();

        if (data === null) return;

        filteredTransactions = data;
        currentPage = 1;

        updatePeriodLabel();
        renderSummary(data);
        renderSalesChart(data);
        renderPlatformChart(data);
        renderProductTable(data);
        renderTransactionTable(data);
    }

    // FILTER BUTTON
    document.getElementById('applyFilter').addEventListener('click', function () {
        updateReport();
    });

    // CSV EXPORT
    function exportCSV() {
        if (filteredTransactions.length === 0) {
            alert('Tidak ada transaksi untuk diekspor.');
            return;
        }

        const headers = [
            'ID Transaksi',
            'Tanggal',
            'Produk',
            'Platform',
            'Jumlah',
            'Total',
            'Status'
        ];

        const rows = filteredTransactions.map(function (item) {
            return [
                item.id,
                item.date,
                item.product,
                item.platform,
                item.quantity,
                item.total,
                item.status
            ];
        });

        const csvContent = [headers, ...rows]
            .map(function (row) {
                return row.map(function (value) {
                    return '"' + String(value).replace(/"/g, '""') + '"';
                }).join(',');
            })
            .join('\r\n');

        const blob = new Blob(['\uFEFF' + csvContent], {
            type: 'text/csv;charset=utf-8;'
        });

        downloadBlob(blob, getFileName('csv'));
    }

    // EXCEL EXPORT
    // Menghasilkan file .xls yang dapat dibuka menggunakan Microsoft Excel.
    function exportExcel() {
        if (filteredTransactions.length === 0) {
            alert('Tidak ada transaksi untuk diekspor.');
            return;
        }

        const rows = filteredTransactions.map(function (item) {
            return `
                <tr>
                    <td>${escapeHTML(item.id)}</td>
                    <td>${escapeHTML(item.date)}</td>
                    <td>${escapeHTML(item.product)}</td>
                    <td>${escapeHTML(item.platform)}</td>
                    <td>${item.quantity}</td>
                    <td>${item.total}</td>
                    <td>${escapeHTML(item.status)}</td>
                </tr>
            `;
        }).join('');

        const html = `
            <html>
            <head>
                <meta charset="UTF-8">
            </head>
            <body>
                <h2>Laporan Penjualan REBANT BOMS</h2>
                <p>Periode: ${escapeHTML(startDateInput.value)} sampai ${escapeHTML(endDateInput.value)}</p>
                <table border="1">
                    <thead>
                        <tr>
                            <th>ID Transaksi</th>
                            <th>Tanggal</th>
                            <th>Produk</th>
                            <th>Platform</th>
                            <th>Jumlah</th>
                            <th>Total</th>
                            <th>Status</th>
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

        downloadBlob(blob, getFileName('xls'));
    }

    // PDF EXPORT
    // Membuka tampilan cetak browser, lalu pengguna bisa memilih Save as PDF.
    function exportPDF() {
        if (filteredTransactions.length === 0) {
            alert('Tidak ada transaksi untuk diekspor.');
            return;
        }

        window.print();
    }

    function getFileName(extension) {
        return 'laporan-penjualan-' +
            startDateInput.value + '-sampai-' +
            endDateInput.value + '.' + extension;
    }

    function downloadBlob(blob, fileName) {
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');

        link.href = url;
        link.download = fileName;

        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        URL.revokeObjectURL(url);
    }

    document.getElementById('exportCsv').addEventListener('click', exportCSV);
    document.getElementById('exportExcel').addEventListener('click', exportExcel);
    document.getElementById('exportPdf').addEventListener('click', exportPDF);

    // INITIAL RENDER
    updateReport();
});
