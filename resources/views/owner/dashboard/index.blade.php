@extends('layouts.owner')

@section('title', 'Dashboard Owner - REBANT BOMS')

@section('content')

<div class="owner-dashboard">

    {{-- ========================================
        PAGE HEADER
    ========================================= --}}
    <div class="owner-dashboard-header">

        <div>
            <h1>Dashboard Owner</h1>
            <p>Ringkasan performa bisnis REBANT dalam satu tampilan.</p>
        </div>

        <div class="owner-date-filter">
            <svg width="15" height="15" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="1.8">
                <rect x="3" y="5" width="18" height="16" rx="2"/>
                <path d="M16 3v4M8 3v4M3 10h18"/>
            </svg>
            <span>18 Oktober 2026</span>
        </div>

    </div>


    {{-- ========================================
        SUMMARY CARDS
    ========================================= --}}
    <div class="owner-summary-grid">

        {{-- TOTAL PESANAN --}}
        <div class="owner-summary-card">

            <div class="owner-summary-icon green">
                <svg width="23" height="23" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="1.8"
                     stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"/>
                    <circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/>
                </svg>
            </div>

            <div class="owner-summary-content">
                <span class="owner-summary-label">Total Pesanan</span>
                <strong class="owner-summary-value">147</strong>
                <span class="owner-summary-caption">↑ 12% vs periode sebelumnya</span>
            </div>

        </div>


        {{-- TOTAL PENJUALAN --}}
        <div class="owner-summary-card">

            <div class="owner-summary-icon yellow">
                <svg width="23" height="23" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="1.8"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2v20"/>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
            </div>

            <div class="owner-summary-content">
                <span class="owner-summary-label">Total Penjualan</span>
                <strong class="owner-summary-value currency">Rp 24.750.000</strong>
                <span class="owner-summary-caption">↑ 12% vs periode sebelumnya</span>
            </div>

        </div>


        {{-- PRODUK TERJUAL --}}
        <div class="owner-summary-card">

            <div class="owner-summary-icon gray">
                <svg width="23" height="23" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="1.8"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="m12 3 9 5-9 5-9-5 9-5Z"/>
                    <path d="m3 8 9 5 9-5"/>
                    <path d="M3 8v9l9 5 9-5V8"/>
                </svg>
            </div>

            <div class="owner-summary-content">
                <span class="owner-summary-label">Produk Terjual</span>
                <strong class="owner-summary-value">1.245</strong>
                <span class="owner-summary-caption">↑ 15% vs periode sebelumnya</span>
            </div>

        </div>


        {{-- STOK MINIMUM --}}
        <div class="owner-summary-card">

            <div class="owner-summary-icon orange">
                <svg width="23" height="23" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="1.8"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="m10.3 3.9-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.7-3.1l-8-14a2 2 0 0 0-3.4 0Z"/>
                    <path d="M12 9v4M12 17h.01"/>
                </svg>
            </div>

            <div class="owner-summary-content">
                <span class="owner-summary-label">Stok Minimum</span>
                <strong class="owner-summary-value">3</strong>
                <span class="owner-summary-caption">SKU perlu diperhatikan</span>
            </div>

        </div>

    </div>


    {{-- ========================================
        SALES & TOP PRODUCTS
    ========================================= --}}
    <div class="owner-dashboard-grid">

        {{-- SALES CHART --}}
        <div class="owner-card">

            <div class="owner-card-header">
                <div>
                    <h2>Tren Penjualan</h2>
                    <p>Performa penjualan dalam 30 hari terakhir.</p>
                </div>
            </div>

            <div class="owner-chart-content">

                <div class="owner-chart-filter">
                    <button type="button">7D</button>
                    <button type="button" class="active">30D</button>
                    <button type="button">90D</button>
                </div>

                <div class="owner-chart-legend">
                    <div class="owner-legend-item">
                        <span class="owner-legend-dot shopee"></span>
                        Shopee
                    </div>

                    <div class="owner-legend-item">
                        <span class="owner-legend-dot tiktok"></span>
                        TikTok Shop
                    </div>
                </div>

                <div class="owner-chart">

                    <svg viewBox="0 0 650 245"
                         role="img"
                         aria-label="Grafik tren penjualan Shopee dan TikTok Shop">

                        <defs>
                            <linearGradient id="shopeeArea" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#779968" stop-opacity=".18"/>
                                <stop offset="100%" stop-color="#779968" stop-opacity="0"/>
                            </linearGradient>

                            <linearGradient id="tiktokArea" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#F1C34D" stop-opacity=".18"/>
                                <stop offset="100%" stop-color="#F1C34D" stop-opacity="0"/>
                            </linearGradient>
                        </defs>

                        {{-- GRID --}}
                        <line class="owner-chart-gridline" x1="55" y1="25" x2="620" y2="25"/>
                        <line class="owner-chart-gridline" x1="55" y1="75" x2="620" y2="75"/>
                        <line class="owner-chart-gridline" x1="55" y1="125" x2="620" y2="125"/>
                        <line class="owner-chart-gridline" x1="55" y1="175" x2="620" y2="175"/>
                        <line class="owner-chart-gridline" x1="55" y1="215" x2="620" y2="215"/>

                        <line class="owner-chart-gridline" x1="55" y1="25" x2="55" y2="215"/>
                        <line class="owner-chart-gridline" x1="245" y1="25" x2="245" y2="215"/>
                        <line class="owner-chart-gridline" x1="435" y1="25" x2="435" y2="215"/>
                        <line class="owner-chart-gridline" x1="620" y1="25" x2="620" y2="215"/>

                        {{-- Y LABELS --}}
                        <text class="owner-chart-label" x="5" y="29">15 jt</text>
                        <text class="owner-chart-label" x="5" y="79">10 jt</text>
                        <text class="owner-chart-label" x="10" y="129">5 jt</text>
                        <text class="owner-chart-label" x="10" y="219">0 jt</text>

                        {{-- SHOPEE AREA --}}
                        <path d="M55 115
                                 C110 102 150 65 175 68
                                 S275 92 310 77
                                 S385 45 435 55
                                 S535 40 620 25
                                 L620 215 L55 215 Z"
                              fill="url(#shopeeArea)"/>

                        {{-- TIKTOK AREA --}}
                        <path d="M55 155
                                 C110 140 150 120 175 120
                                 S275 143 310 135
                                 S385 102 435 108
                                 S535 90 620 82
                                 L620 215 L55 215 Z"
                              fill="url(#tiktokArea)"/>

                        {{-- SHOPEE LINE --}}
                        <path class="owner-chart-line-shopee"
                              d="M55 115
                                 C110 102 150 65 175 68
                                 S275 92 310 77
                                 S385 45 435 55
                                 S535 40 620 25"/>

                        {{-- TIKTOK LINE --}}
                        <path class="owner-chart-line-tiktok"
                              d="M55 155
                                 C110 140 150 120 175 120
                                 S275 143 310 135
                                 S385 102 435 108
                                 S535 90 620 82"/>

                        {{-- POINTS --}}
                        <circle class="owner-chart-point-shopee" cx="55" cy="115" r="4"/>
                        <circle class="owner-chart-point-shopee" cx="175" cy="68" r="4"/>
                        <circle class="owner-chart-point-shopee" cx="310" cy="77" r="4"/>
                        <circle class="owner-chart-point-shopee" cx="435" cy="55" r="4"/>
                        <circle class="owner-chart-point-shopee" cx="550" cy="38" r="4"/>
                        <circle class="owner-chart-point-shopee" cx="620" cy="25" r="4"/>

                        <circle class="owner-chart-point-tiktok" cx="55" cy="155" r="4"/>
                        <circle class="owner-chart-point-tiktok" cx="175" cy="120" r="4"/>
                        <circle class="owner-chart-point-tiktok" cx="310" cy="135" r="4"/>
                        <circle class="owner-chart-point-tiktok" cx="435" cy="108" r="4"/>
                        <circle class="owner-chart-point-tiktok" cx="550" cy="90" r="4"/>
                        <circle class="owner-chart-point-tiktok" cx="620" cy="82" r="4"/>

                        {{-- X LABELS --}}
                        <text class="owner-chart-label" x="55" y="237" text-anchor="middle">1 Okt</text>
                        <text class="owner-chart-label" x="245" y="237" text-anchor="middle">8 Okt</text>
                        <text class="owner-chart-label" x="435" y="237" text-anchor="middle">15 Okt</text>
                        <text class="owner-chart-label" x="620" y="237" text-anchor="end">18 Okt</text>

                    </svg>

                </div>

            </div>

        </div>


        {{-- TOP PRODUCTS --}}
        <div class="owner-card">

            <div class="owner-card-header">
                <div>
                    <h2>Produk Terlaris</h2>
                    <p>Produk dengan penjualan tertinggi.</p>
                </div>

                <a href="#" class="owner-view-all">
                    Lihat Semua
                    <span>→</span>
                </a>
            </div>

            <div class="owner-product-list">

                {{-- PRODUCT 1 --}}
                <div class="owner-product-item">

                    <span class="owner-product-rank">1</span>

                    <div class="owner-product-image">
                        <svg width="22" height="22" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="m12 3 9 5-9 5-9-5 9-5Z"/>
                            <path d="m3 8 9 5 9-5M3 8v9l9 5 9-5V8"/>
                        </svg>
                    </div>

                    <div class="owner-product-info">
                        <strong>Jagalah Kebersihan</strong>
                        <span>D6A-5</span>
                    </div>

                    <div class="owner-product-sales">
                        <strong>420 pcs</strong>
                        <div class="owner-product-progress">
                            <span style="width: 85%"></span>
                        </div>
                        <span>28.6%</span>
                    </div>

                </div>


                {{-- PRODUCT 2 --}}
                <div class="owner-product-item">

                    <span class="owner-product-rank">2</span>

                    <div class="owner-product-image">
                        <svg width="22" height="22" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="m12 3 9 5-9 5-9-5 9-5Z"/>
                            <path d="m3 8 9 5 9-5M3 8v9l9 5 9-5V8"/>
                        </svg>
                    </div>

                    <div class="owner-product-info">
                        <strong>Area Merokok</strong>
                        <span>D6A-3</span>
                    </div>

                    <div class="owner-product-sales">
                        <strong>360 pcs</strong>
                        <div class="owner-product-progress">
                            <span style="width: 72%"></span>
                        </div>
                        <span>24.5%</span>
                    </div>

                </div>


                {{-- PRODUCT 3 --}}
                <div class="owner-product-item">

                    <span class="owner-product-rank">3</span>

                    <div class="owner-product-image">
                        <svg width="22" height="22" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="m12 3 9 5-9 5-9-5 9-5Z"/>
                            <path d="m3 8 9 5 9-5M3 8v9l9 5 9-5V8"/>
                        </svg>
                    </div>

                    <div class="owner-product-info">
                        <strong>Ruang Tunggu</strong>
                        <span>D6A-1</span>
                    </div>

                    <div class="owner-product-sales">
                        <strong>310 pcs</strong>
                        <div class="owner-product-progress">
                            <span style="width: 62%"></span>
                        </div>
                        <span>21.1%</span>
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================
        PRODUCTION & INVENTORY
    ========================================= --}}
    <div class="owner-dashboard-grid owner-dashboard-grid-bottom">

        {{-- PRODUCTION MONITORING --}}
        <div class="owner-card">

            <div class="owner-card-header">
                <div>
                    <h2>Monitoring Produksi</h2>
                    <p>Status produksi dan antrean terbaru.</p>
                </div>

                <a href="#" class="owner-view-all">
                    Lihat Semua
                    <span>→</span>
                </a>
            </div>

            <div class="owner-production-content">

                <div class="owner-production-overview">

                    <div class="owner-production-chart">

                        <svg viewBox="0 0 100 100">
                            <circle class="track" cx="50" cy="50" r="40"/>
                            <circle class="progress" cx="50" cy="50" r="40"/>
                        </svg>

                        <div class="owner-production-chart-label">
                            <strong>67%</strong>
                            <span>Progress Produksi</span>
                        </div>

                    </div>

                    <div class="owner-production-divider"></div>

                    <div class="owner-production-status">

                        <div class="owner-production-status-item">
                            <div class="owner-production-status-label">
                                <i class="process"></i>
                                Dalam Proses
                            </div>
                            <strong>2 order</strong>
                        </div>

                        <div class="owner-production-status-item">
                            <div class="owner-production-status-label">
                                <i class="waiting"></i>
                                Menunggu Produksi
                            </div>
                            <strong>2 order</strong>
                        </div>

                        <div class="owner-production-status-item">
                            <div class="owner-production-status-label">
                                <i class="completed"></i>
                                Selesai Hari Ini
                            </div>
                            <strong>8 order</strong>
                        </div>

                    </div>

                </div>

                <div class="owner-production-alert">

                    <svg width="19" height="19" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="1.8"
                         stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 8v4M12 16h.01"/>
                    </svg>

                    <span>1 produk perlu segera diproduksi</span>

                    <svg width="15" height="15" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m9 18 6-6-6-6"/>
                    </svg>

                </div>

            </div>

        </div>


        {{-- INVENTORY MONITORING --}}
        <div class="owner-card">

            <div class="owner-card-header">
                <div>
                    <h2>Persediaan</h2>
                    <p>Ringkasan stok produk dan bahan baku.</p>
                </div>

                <a href="#" class="owner-view-all">
                    Lihat Detail
                    <span>→</span>
                </a>
            </div>

            <div class="owner-inventory-content">

                <div class="owner-inventory-overview">

                    {{-- FINISHED PRODUCT --}}
                    <div class="owner-inventory-item">

                        <div class="owner-inventory-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="1.7"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="m12 3 9 5-9 5-9-5 9-5Z"/>
                                <path d="m3 8 9 5 9-5M3 8v9l9 5 9-5V8"/>
                            </svg>
                        </div>

                        <div class="owner-inventory-info">
                            <span>Stok Produk Jadi</span>
                            <strong>4.250 pcs</strong>
                            <div class="owner-inventory-growth">↑ 8% vs bulan lalu</div>
                        </div>

                    </div>


                    {{-- RAW MATERIAL --}}
                    <div class="owner-inventory-item">

                        <div class="owner-inventory-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="1.7"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 3h6M10 3v7l-5 9a2 2 0 0 0 2 3h10a2 2 0 0 0 2-3l-5-9V3"/>
                                <path d="M8 15h8"/>
                            </svg>
                        </div>

                        <div class="owner-inventory-info">
                            <span>Stok Bahan Baku</span>
                            <strong>86 item</strong>
                            <div class="owner-inventory-growth">↑ 5% vs bulan lalu</div>
                        </div>

                    </div>

                </div>


                <div class="owner-inventory-alert">

                    <svg width="19" height="19" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="1.8"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="m10.3 3.9-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.7-3.1l-8-14a2 2 0 0 0-3.4 0Z"/>
                        <path d="M12 9v4M12 17h.01"/>
                    </svg>

                    <span>2 bahan baku mencapai stok minimum</span>

                    <svg width="15" height="15" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m9 18 6-6-6-6"/>
                    </svg>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection