document.addEventListener('DOMContentLoaded', function () {
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');

    if (!startDateInput || !endDateInput) return;

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

    // Transaksi dianggap selesai bila status_code COMPLETED (atau status_class completed)
    const isCompleted = function (item) {
        return item.status_code === 'COMPLETED' || item.status_class === 'completed';
    };

    // allTransactions       : semua status, dipakai kartu, grafik, dan performa produk
    // completedTransactions : hanya COMPLETED, dipakai GMV, riwayat transaksi, dan export
    let allTransactions = [];
    let completedTransactions = [];

    let isLoading = false;

    /*
    |--------------------------------------------------------------------------
    | AMBIL DATA DARI SERVER
    |--------------------------------------------------------------------------
    */

    async function getFilteredTransactions() {
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

        try {
            const response = await fetch(
                window.salesReportUrl + '?start=' + start + '&end=' + end,
                { headers: { 'Accept': 'application/json' } }
            );

            if (!response.ok) throw new Error('HTTP ' + response.status);

            return await response.json();
        } catch (error) {
            alert('Data penjualan gagal dimuat. Coba lagi.');
            return null;
        }
    }

    function updatePeriodLabel() {
        document.getElementById('periodLabel').textContent =
            'Periode ' + formatDate(startDateInput.value) +
            ' – ' + formatDate(endDateInput.value);
    }

    function sumTotal(list) {
        return list.reduce(function (sum, item) {
            return sum + item.total;
        }, 0);
    }

    function renderSummary(data, completed) {
        const revenue = sumTotal(data);
        const gmv = sumTotal(completed);

        const products = data.reduce(function (sum, item) {
            return sum + item.quantity;
        }, 0);

        const transactionCount = data.length;

        const average = transactionCount > 0
            ? revenue / transactionCount
            : 0;

        document.getElementById('totalGmv').textContent = rupiah(gmv);
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

    // Satu order bisa berisi beberapa produk, jadi dihitung per item
    function renderProductTable(data) {
        const tbody = document.getElementById('productTableBody');

        const products = {};

        data.forEach(function (transaction) {
            (transaction.items || []).forEach(function (item) {
                if (!products[item.name]) {
                    products[item.name] = { quantity: 0, revenue: 0 };
                }

                products[item.name].quantity += item.quantity;
                products[item.name].revenue += item.total;
            });
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

    // Riwayat transaksi: hanya yang berstatus completed
    function renderTransactionTable(completed) {
        const tbody = document.getElementById('transactionTableBody');
        const pagination = document.getElementById('paginationControls');

        pagination.innerHTML = '';

        const totalData = completed.length;

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
            return;
        }

        tbody.innerHTML = completed.map(function (item) {
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

        document.getElementById('transactionCount').textContent =
            `Menampilkan ${totalData} transaksi`;
    }

    async function updateReport() {
        if (isLoading) return;

        isLoading = true;

        const applyButton = document.getElementById('applyFilter');
        applyButton.disabled = true;

        try {
            const data = await getFilteredTransactions();

            if (data === null) return;

            allTransactions = data;
            completedTransactions = data.filter(isCompleted);

            updatePeriodLabel();
            renderSummary(allTransactions, completedTransactions);
            renderSalesChart(allTransactions);
            renderPlatformChart(allTransactions);
            renderProductTable(allTransactions);
            renderTransactionTable(completedTransactions);
        } finally {
            isLoading = false;
            applyButton.disabled = false;
        }
    }

    // FILTER BUTTON
    document.getElementById('applyFilter').addEventListener('click', function () {
        updateReport();
    });

    // CSV EXPORT (hanya transaksi completed)
    function exportCSV() {
        if (completedTransactions.length === 0) {
            alert('Tidak ada transaksi completed untuk diekspor.');
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

        const rows = completedTransactions.map(function (item) {
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

    // EXCEL EXPORT (.xls yang dapat dibuka Microsoft Excel)
    function exportExcel() {
        if (completedTransactions.length === 0) {
            alert('Tidak ada transaksi completed untuk diekspor.');
            return;
        }

        const rows = completedTransactions.map(function (item) {
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
                <p>GMV (transaksi completed): ${sumTotal(completedTransactions)}</p>
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

    // PDF EXPORT (tampilan cetak browser, pilih Save as PDF)
    function exportPDF() {
        if (allTransactions.length === 0) {
            alert('Tidak ada transaksi untuk diekspor.');
            return;
        }

        buildPrintReport(allTransactions, completedTransactions);
        window.print();
    }


    function getFileName(extension) {
        return 'laporan-penjualan-' +
            startDateInput.value + '-sampai-' +
            endDateInput.value + '.' + extension;
    }

    function buildPrintReport(data, completed) {
        const revenue = sumTotal(data);
        const gmv = sumTotal(completed);
        const units = data.reduce((s, i) => s + i.quantity, 0);
        const average = data.length ? revenue / data.length : 0;

        const daily = {};
        const platforms = {};
        const products = {};

        data.forEach(function (t) {
            daily[t.date] = (daily[t.date] || 0) + t.total;
            platforms[t.platform] = (platforms[t.platform] || 0) + t.total;

            (t.items || []).forEach(function (it) {
                products[it.name] = products[it.name] || { q: 0, r: 0 };
                products[it.name].q += it.quantity;
                products[it.name].r += it.total;
            });
        });

        const maxDaily = Math.max(...Object.values(daily), 1);
        const platformTotal = Math.max(Object.values(platforms).reduce((a, b) => a + b, 0), 1);

        const dailyRows = Object.keys(daily).sort().map(function (d) {
            return `<tr><td style="width:90px">${formatDate(d)}</td>
                <td><div class="pr-bar"><i style="width:${Math.max(daily[d] / maxDaily * 100, 2)}%"></i></div></td>
                <td class="r" style="width:100px">${rupiah(daily[d])}</td></tr>`;
        }).join('');

        const platformRows = Object.keys(platforms).map(function (p) {
            const pct = platforms[p] / platformTotal * 100;
            return `<tr><td style="width:90px">${escapeHTML(p)}</td>
                <td><div class="pr-bar"><i class="${p === 'TikTok' ? 'tiktok' : ''}" style="width:${pct}%"></i></div></td>
                <td class="r" style="width:100px">${rupiah(platforms[p])}</td>
                <td class="r" style="width:45px">${pct.toFixed(1)}%</td></tr>`;
        }).join('');

        const productRows = Object.entries(products)
            .sort((a, b) => b[1].q - a[1].q)
            .map(function ([name, v], i) {
                return `<tr><td style="width:35px">${i + 1}</td><td>${escapeHTML(name)}</td>
                    <td class="r">${numberFormat(v.q)}</td><td class="r">${rupiah(v.r)}</td></tr>`;
            }).join('');

        const trxRows = completed.length
            ? completed.map(function (t) {
                return `<tr><td>${escapeHTML(t.id)}</td><td>${formatDate(t.date)}</td>
                    <td>${escapeHTML(t.product)}</td><td>${escapeHTML(t.platform)}</td>
                    <td class="r">${numberFormat(t.quantity)}</td><td class="r">${rupiah(t.total)}</td>
                    <td>${escapeHTML(t.status)}</td></tr>`;
            }).join('')
            : '<tr><td colspan="7">Tidak ada transaksi completed pada periode ini.</td></tr>';

        const today = new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });

        document.getElementById('printReport').innerHTML = `
            <div class="pr-head">
                <div class="pr-brand">REBANT BOMS<small>Business Operation Management System</small></div>
                <div class="pr-title">LAPORAN PENJUALAN
                    <small>Periode ${formatDate(startDateInput.value)} – ${formatDate(endDateInput.value)}</small></div>
            </div>

            <h2>1. Ringkasan</h2>
            <div class="pr-summary">
                <div class="pr-card"><span>GMV (Completed)</span><strong>${rupiah(gmv)}</strong></div>
                <div class="pr-card"><span>Total Omzet</span><strong>${rupiah(revenue)}</strong></div>
                <div class="pr-card"><span>Produk Terjual</span><strong>${numberFormat(units)} unit</strong></div>
                <div class="pr-card"><span>Total Transaksi</span><strong>${numberFormat(data.length)}</strong></div>
                <div class="pr-card"><span>Rata-rata Transaksi</span><strong>${rupiah(average)}</strong></div>
            </div>

            <h2>2. Tren Penjualan Harian</h2>
            <table class="pr-table"><thead><tr><th>Tanggal</th><th>Omzet</th><th class="r">Nilai</th></tr></thead>
                <tbody>${dailyRows}</tbody></table>

            <h2>3. Performa Platform</h2>
            <table class="pr-table"><thead><tr><th>Platform</th><th>Kontribusi</th><th class="r">Omzet</th><th class="r">%</th></tr></thead>
                <tbody>${platformRows}</tbody></table>

            <h2>4. Performa Produk</h2>
            <table class="pr-table"><thead><tr><th>No</th><th>Nama Produk</th><th class="r">Unit</th><th class="r">Omzet</th></tr></thead>
                <tbody>${productRows}</tbody></table>

            <h2>5. Riwayat Transaksi (Completed)</h2>
            <table class="pr-table"><thead><tr><th>ID</th><th>Tanggal</th><th>Produk</th><th>Platform</th><th class="r">Qty</th><th class="r">Total</th><th>Status</th></tr></thead>
                <tbody>${trxRows}</tbody></table>

            <div class="pr-sign">Malang, ${today}<br>Dicetak oleh,<br><br><br><br><strong>Business Owner</strong></div>
        `;
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