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

    // Aman kalau elemen tidak ada di view
    const setText = function (id, value) {
        const el = document.getElementById(id);
        if (el) el.textContent = value;
    };

    const setHTML = function (id, html) {
        const el = document.getElementById(id);
        if (el) el.innerHTML = html;
    };

    // Transaksi dianggap selesai bila status_code COMPLETED (atau status_class completed)
    const isCompleted = function (item) {
        return item.status_code === 'COMPLETED' || item.status_class === 'completed';
    };

    // Kelompok platform dari nama aslinya ("TikTok Shop" tetap dikenali sebagai TikTok)
    const platformKey = function (name) {
        const n = String(name || '').toLowerCase();

        if (n.indexOf('tiktok') !== -1) return 'tiktok';
        if (n.indexOf('shopee') !== -1) return 'shopee';

        return 'other';
    };

    // allTransactions       : semua status, dipakai kartu, tren, dan performa produk
    // completedTransactions : hanya COMPLETED, dipakai GMV, platform, riwayat, dan export
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
            console.error('Gagal mengambil data penjualan:', error);
            return null;
        }
    }

    function showLoadError() {
        const message = '<div class="chart-empty">Data gagal dimuat. Coba lagi.</div>';

        setHTML('salesChart', message);
        setHTML('platformChart', message);
        setHTML('productTableBody', '<tr><td colspan="4" class="empty-table">Data gagal dimuat.</td></tr>');
        setHTML('transactionTableBody', '<tr><td colspan="7" class="empty-table">Data gagal dimuat.</td></tr>');
        setText('transactionCount', 'Menampilkan 0 transaksi');
    }

    function updatePeriodLabel() {
        setText(
            'periodLabel',
            'Periode ' + formatDate(startDateInput.value) + ' – ' + formatDate(endDateInput.value)
        );
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
        const average = transactionCount > 0 ? revenue / transactionCount : 0;

        setText('totalGmv', rupiah(gmv));
        setText('totalRevenue', rupiah(revenue));
        setText('totalProducts', numberFormat(products));
        setText('totalTransactions', numberFormat(transactionCount));
        setText('averageOrderValue', rupiah(average));
    }

    function renderSalesChart(data) {
        if (data.length === 0) {
            setHTML('salesChart', '<div class="chart-empty">Tidak ada data penjualan pada periode ini.</div>');
            return;
        }

        const dailyRevenue = {};

        data.forEach(function (item) {
            dailyRevenue[item.date] = (dailyRevenue[item.date] || 0) + item.total;
        });

        const dates = Object.keys(dailyRevenue).sort();
        const maxRevenue = Math.max(...Object.values(dailyRevenue), 1);

        setHTML('salesChart', dates.map(function (date) {
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
        }).join(''));
    }

    // Performa platform: hanya transaksi completed, semua platform dari tabel platforms
    function renderPlatformChart(completed) {
        const byPlatform = {};

        completed.forEach(function (item) {
            const name = item.platform || '-';
            byPlatform[name] = (byPlatform[name] || 0) + item.total;
        });

        const entries = Object.keys(byPlatform)
            .map(function (name) {
                return { name: name, total: byPlatform[name] };
            })
            .sort(function (a, b) {
                return b.total - a.total;
            });

        const grandTotal = entries.reduce(function (sum, entry) {
            return sum + entry.total;
        }, 0);

        if (entries.length === 0 || grandTotal === 0) {
            setHTML('platformChart', '<div class="chart-empty">Belum ada data platform dari transaksi completed.</div>');
            return;
        }

        setHTML('platformChart', entries.map(function (entry) {
            const key = platformKey(entry.name);
            const pct = (entry.total / grandTotal) * 100;
            const otherDot = key === 'other' ? ' style="background:#6B8FB5"' : '';
            const otherFill = key === 'other' ? ';background:#6B8FB5' : '';

            return `
                <div class="platform-item">
                    <div class="platform-info">
                        <span class="platform-name">
                            <span class="platform-dot ${key}-dot"${otherDot}></span>
                            ${escapeHTML(entry.name)}
                        </span>
                        <span class="platform-value">${rupiah(entry.total)}</span>
                    </div>
                    <div class="platform-progress">
                        <div class="platform-progress-fill ${key}-fill"
                             style="width: ${pct}%${otherFill}"></div>
                    </div>
                    <span class="platform-percentage">${pct.toFixed(1)}% dari GMV</span>
                </div>
            `;
        }).join(''));
    }

    // Satu order bisa berisi beberapa produk, jadi dihitung per item
    function renderProductTable(data) {
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
            setHTML(
                'productTableBody',
                '<tr><td colspan="4" class="empty-table">Tidak ada data produk pada periode ini.</td></tr>'
            );
            return;
        }

        setHTML('productTableBody', sortedProducts.map(function (product, index) {
            return `
                <tr>
                    <td><span class="rank-number">${index + 1}</span></td>
                    <td class="product-name-cell">${escapeHTML(product.name)}</td>
                    <td>${numberFormat(product.quantity)} unit</td>
                    <td>${rupiah(product.revenue)}</td>
                </tr>
            `;
        }).join(''));
    }

    // Riwayat transaksi: hanya yang berstatus completed
    function renderTransactionTable(completed) {
        setHTML('paginationControls', '');

        const totalData = completed.length;

        if (totalData === 0) {
            setHTML(
                'transactionTableBody',
                '<tr><td colspan="7" class="empty-table">Tidak ada transaksi completed pada periode ini.</td></tr>'
            );
            setText('transactionCount', 'Menampilkan 0 transaksi');
            return;
        }

        setHTML('transactionTableBody', completed.map(function (item) {
            const key = platformKey(item.platform);
            const platformClass = key === 'other' ? '' : key + '-pill';

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
        }).join(''));

        setText('transactionCount', 'Menampilkan ' + totalData + ' transaksi');
    }

    async function updateReport() {
        if (isLoading) return;

        isLoading = true;

        const applyButton = document.getElementById('applyFilter');
        if (applyButton) applyButton.disabled = true;

        try {
            const data = await getFilteredTransactions();

            if (data === null) {
                showLoadError();
                return;
            }

            allTransactions = data;
            completedTransactions = data.filter(isCompleted);

            updatePeriodLabel();
            renderSummary(allTransactions, completedTransactions);
            renderSalesChart(allTransactions);
            renderPlatformChart(completedTransactions);
            renderProductTable(allTransactions);
            renderTransactionTable(completedTransactions);
        } catch (error) {
            console.error('Gagal merender laporan penjualan:', error);
            showLoadError();
        } finally {
            isLoading = false;
            if (applyButton) applyButton.disabled = false;
        }
    }

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
        const units = data.reduce(function (s, i) { return s + i.quantity; }, 0);
        const average = data.length ? revenue / data.length : 0;

        const daily = {};
        const platforms = {};
        const products = {};

        data.forEach(function (t) {
            daily[t.date] = (daily[t.date] || 0) + t.total;

            (t.items || []).forEach(function (it) {
                products[it.name] = products[it.name] || { q: 0, r: 0 };
                products[it.name].q += it.quantity;
                products[it.name].r += it.total;
            });
        });

        // Platform dihitung dari transaksi completed saja (sama dengan grafik di layar)
        completed.forEach(function (t) {
            const name = t.platform || '-';
            platforms[name] = (platforms[name] || 0) + t.total;
        });

        const maxDaily = Math.max(...Object.values(daily), 1);
        const platformTotal = Math.max(
            Object.values(platforms).reduce(function (a, b) { return a + b; }, 0),
            1
        );

        const dailyRows = Object.keys(daily).sort().map(function (d) {
            return `<tr><td style="width:90px">${formatDate(d)}</td>
                <td><div class="pr-bar"><i style="width:${Math.max(daily[d] / maxDaily * 100, 2)}%"></i></div></td>
                <td class="r" style="width:100px">${rupiah(daily[d])}</td></tr>`;
        }).join('');

        const platformRows = Object.keys(platforms).length
            ? Object.keys(platforms).map(function (p) {
                const pct = platforms[p] / platformTotal * 100;
                return `<tr><td style="width:90px">${escapeHTML(p)}</td>
                    <td><div class="pr-bar"><i class="${platformKey(p) === 'tiktok' ? 'tiktok' : ''}" style="width:${pct}%"></i></div></td>
                    <td class="r" style="width:100px">${rupiah(platforms[p])}</td>
                    <td class="r" style="width:45px">${pct.toFixed(1)}%</td></tr>`;
            }).join('')
            : '<tr><td colspan="4">Belum ada data platform dari transaksi completed.</td></tr>';

        const productRows = Object.entries(products)
            .sort(function (a, b) { return b[1].q - a[1].q; })
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

        setHTML('printReport', `
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

            <h2>3. Performa Platform (Completed)</h2>
            <table class="pr-table"><thead><tr><th>Platform</th><th>Kontribusi</th><th class="r">GMV</th><th class="r">%</th></tr></thead>
                <tbody>${platformRows}</tbody></table>

            <h2>4. Performa Produk</h2>
            <table class="pr-table"><thead><tr><th>No</th><th>Nama Produk</th><th class="r">Unit</th><th class="r">Omzet</th></tr></thead>
                <tbody>${productRows}</tbody></table>

            <h2>5. Riwayat Transaksi (Completed)</h2>
            <table class="pr-table"><thead><tr><th>ID</th><th>Tanggal</th><th>Produk</th><th>Platform</th><th class="r">Qty</th><th class="r">Total</th><th>Status</th></tr></thead>
                <tbody>${trxRows}</tbody></table>

            <div class="pr-sign">Malang, ${today}<br>Dicetak oleh,<br><br><br><br><strong>Business Owner</strong></div>
        `);
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

    // EVENT
    const applyButtonEl = document.getElementById('applyFilter');
    const csvButtonEl = document.getElementById('exportCsv');
    const excelButtonEl = document.getElementById('exportExcel');
    const pdfButtonEl = document.getElementById('exportPdf');

    if (applyButtonEl) applyButtonEl.addEventListener('click', updateReport);
    if (csvButtonEl) csvButtonEl.addEventListener('click', exportCSV);
    if (excelButtonEl) excelButtonEl.addEventListener('click', exportExcel);
    if (pdfButtonEl) pdfButtonEl.addEventListener('click', exportPDF);

    // INITIAL RENDER
    updateReport();
});