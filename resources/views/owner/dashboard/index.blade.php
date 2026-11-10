
@extends('layouts.owner')

@section('title', 'Owner Dashboard | REBANT BOMS')

@php
    // Dummy data untuk tampilan Owner Dashboard
    $summaryCards = [
        [
            'label' => 'Total Penjualan',
            'value' => 'Rp 12.850.000',
            'change' => '+12.8%',
            'description' => 'dibanding bulan lalu',
            'icon' => 'sales',
            'trend' => 'up',
        ],
        [
            'label' => 'Pesanan Masuk',
            'value' => '128',
            'change' => '+8.2%',
            'description' => 'dibanding bulan lalu',
            'icon' => 'orders',
            'trend' => 'up',
        ],
        [
            'label' => 'Antrean Produksi',
            'value' => '24',
            'change' => '8 pesanan',
            'description' => 'sedang dikerjakan',
            'icon' => 'production',
            'trend' => 'neutral',
        ],
        [
            'label' => 'Stok Kritis',
            'value' => '5',
            'change' => 'Perlu perhatian',
            'description' => 'bahan hampir habis',
            'icon' => 'stock',
            'trend' => 'warning',
        ],
    ];

    $orderStatuses = [
        ['label' => 'Waiting Production', 'count' => 12, 'percentage' => 50, 'class' => 'waiting'],
        ['label' => 'Ready to Pack', 'count' => 7, 'percentage' => 29, 'class' => 'packing'],
        ['label' => 'Completed', 'count' => 5, 'percentage' => 21, 'class' => 'completed'],
    ];

    $productionQueue = [
        ['code' => 'ORD-1028', 'product' => 'Wooden Wall Decor', 'quantity' => 3, 'status' => 'In Progress'],
        ['code' => 'ORD-1026', 'product' => 'Minimalist Frame', 'quantity' => 2, 'status' => 'In Progress'],
        ['code' => 'ORD-1024', 'product' => 'Custom Wall Art', 'quantity' => 1, 'status' => 'Waiting'],
    ];

    $lowStocks = [
        ['name' => 'Papan MDF', 'remaining' => '3 lembar', 'minimum' => '10 lembar', 'level' => 20],
        ['name' => 'Cat Putih', 'remaining' => '1 liter', 'minimum' => '5 liter', 'level' => 15],
        ['name' => 'Lem Kayu', 'remaining' => '2 botol', 'minimum' => '5 botol', 'level' => 35],
        ['name' => 'Kertas Amplas', 'remaining' => '4 pcs', 'minimum' => '10 pcs', 'level' => 40],
    ];

    $topProducts = [
        ['name' => 'Minimalist Wooden Frame', 'category' => 'Wall Decor', 'sold' => 86, 'percentage' => 92],
        ['name' => 'Custom Quote Wall Art', 'category' => 'Custom Decor', 'sold' => 72, 'percentage' => 78],
        ['name' => 'Geometric Wall Decor', 'category' => 'Wall Decor', 'sold' => 58, 'percentage' => 63],
    ];
@endphp

@section('content')
<div class="owner-dashboard">

    {{-- PAGE HEADING --}}
    <div class="owner-page-heading">
        <div>
            <h1>Welcome back, Business Owner <span>👋</span></h1>
            <p>Here's what's happening with your business today.</p>
        </div>

        <div class="owner-date">
            <span class="date-icon">▦</span>
            <span>November 2026</span>
        </div>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="owner-summary-grid">
        @foreach ($summaryCards as $card)
            <div class="owner-summary-card">
                <div class="summary-card-top">
                    <span class="summary-label">{{ $card['label'] }}</span>

                    <div class="summary-icon {{ $card['icon'] }}">
                        @if ($card['icon'] === 'sales')
                            <span>↗</span>
                        @elseif ($card['icon'] === 'orders')
                            <span>▤</span>
                        @elseif ($card['icon'] === 'production')
                            <span>⚙</span>
                        @else
                            <span>!</span>
                        @endif
                    </div>
                </div>

                <h2>{{ $card['value'] }}</h2>

                <div class="summary-card-bottom">
                    <span class="summary-change {{ $card['trend'] }}">
                        {{ $card['change'] }}
                    </span>
                    <span class="summary-description">{{ $card['description'] }}</span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- SALES OVERVIEW --}}
    <div class="owner-main-grid">
        <section class="owner-panel sales-panel">
            <div class="panel-heading">
                <div>
                    <h3>Sales Overview</h3>
                    <p>Sales performance across platforms</p>
                </div>

                <select class="owner-period-select">
                    <option>Last 7 days</option>
                    <option>Last 30 days</option>
                    <option>This year</option>
                </select>
            </div>

            <div class="sales-legend">
                <span><i class="legend-dot shopee-dot"></i> Shopee</span>
                <span><i class="legend-dot tiktok-dot"></i> TikTok Shop</span>
            </div>

            <div class="sales-chart">
                <div class="chart-y-labels">
                    <span>4 jt</span>
                    <span>3 jt</span>
                    <span>2 jt</span>
                    <span>1 jt</span>
                    <span>0</span>
                </div>

                <div class="chart-content">
                    <div class="chart-grid-lines">
                        <span></span><span></span><span></span><span></span><span></span>
                    </div>

                    <svg viewBox="0 0 700 220" preserveAspectRatio="none"
                         class="sales-svg" role="img" aria-label="Dummy sales chart for Shopee and TikTok Shop">
                        <defs>
                            <linearGradient id="shopeeFill" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#9DBA8C" stop-opacity=".25"/>
                                <stop offset="100%" stop-color="#9DBA8C" stop-opacity="0"/>
                            </linearGradient>
                        </defs>

                        <path d="M0 155 L100 125 L200 142 L300 82 L400 105 L500 55 L600 75 L700 25 L700 220 L0 220 Z"
                              fill="url(#shopeeFill)"/>

                        <polyline points="0,155 100,125 200,142 300,82 400,105 500,55 600,75 700,25"
                                  fill="none" stroke="#789B68" stroke-width="3"
                                  stroke-linecap="round" stroke-linejoin="round"/>

                        <polyline points="0,185 100,165 200,175 300,135 400,150 500,115 600,130 700,90"
                                  fill="none" stroke="#E3BD58" stroke-width="3"
                                  stroke-linecap="round" stroke-linejoin="round"
                                  stroke-dasharray="0"/>

                        <circle cx="700" cy="25" r="5" fill="#789B68"/>
                        <circle cx="700" cy="90" r="5" fill="#E3BD58"/>
                    </svg>

                    <div class="chart-x-labels">
                        <span>05 Nov</span>
                        <span>06 Nov</span>
                        <span>07 Nov</span>
                        <span>08 Nov</span>
                        <span>09 Nov</span>
                        <span>10 Nov</span>
                        <span>11 Nov</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- PLATFORM CONTRIBUTION --}}
        <section class="owner-panel platform-panel">
            <div class="panel-heading">
                <div>
                    <h3>Platform Contribution</h3>
                    <p>Sales distribution by platform</p>
                </div>
            </div>

            <div class="platform-chart-wrapper">
                <div class="platform-donut">
                    <div class="donut-center">
                        <strong>100%</strong>
                        <span>Total Sales</span>
                    </div>
                </div>
            </div>

            <div class="platform-details">
                <div class="platform-item">
                    <div class="platform-name">
                        <i class="legend-dot shopee-dot"></i>
                        <span>Shopee</span>
                    </div>
                    <strong>65%</strong>
                </div>

                <div class="platform-progress">
                    <span class="shopee-progress" style="width: 65%"></span>
                </div>

                <div class="platform-item">
                    <div class="platform-name">
                        <i class="legend-dot tiktok-dot"></i>
                        <span>TikTok Shop</span>
                    </div>
                    <strong>35%</strong>
                </div>

                <div class="platform-progress">
                    <span class="tiktok-progress" style="width: 35%"></span>
                </div>
            </div>
        </section>
    </div>

    {{-- ORDER & PRODUCTION --}}
    <div class="owner-main-grid operations-grid">
        <section class="owner-panel order-status-panel">
            <div class="panel-heading">
                <div>
                    <h3>Order Status</h3>
                    <p>Current order distribution</p>
                </div>
                <a href="#" class="panel-link">View details →</a>
            </div>

            <div class="order-total">
                <strong>24</strong>
                <span>Total active orders</span>
            </div>

            <div class="order-status-list">
                @foreach ($orderStatuses as $status)
                    <div class="order-status-item">
                        <div class="status-info">
                            <span class="status-dot {{ $status['class'] }}"></span>
                            <span>{{ $status['label'] }}</span>
                            <strong>{{ $status['count'] }}</strong>
                        </div>

                        <div class="status-progress">
                            <span class="{{ $status['class'] }}"
                                  style="width: {{ $status['percentage'] }}%"></span>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="owner-panel production-panel">
            <div class="panel-heading">
                <div>
                    <h3>Production Queue</h3>
                    <p>Latest production activities</p>
                </div>
                <a href="#" class="panel-link">View all →</a>
            </div>

            <div class="production-list">
                @foreach ($productionQueue as $item)
                    <div class="production-item">
                        <div class="production-info">
                            <strong>{{ $item['code'] }}</strong>
                            <span>{{ $item['product'] }}</span>
                            <small>{{ $item['quantity'] }} item(s)</small>
                        </div>

                        <span class="production-badge {{ $item['status'] === 'Waiting' ? 'waiting' : 'progress' }}">
                            {{ $item['status'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    {{-- BUSINESS INSIGHTS --}}
    <div class="owner-main-grid insights-grid">
        <section class="owner-panel stock-panel">
            <div class="panel-heading">
                <div>
                    <h3>Low Stock Alert</h3>
                    <p>Materials that need restocking</p>
                </div>
                <a href="#" class="panel-link">View inventory →</a>
            </div>

            <div class="owner-table-wrapper">
                <table class="owner-table">
                    <thead>
                        <tr>
                            <th>Material</th>
                            <th>Remaining</th>
                            <th>Minimum</th>
                            <th>Stock Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lowStocks as $stock)
                            <tr>
                                <td><strong>{{ $stock['name'] }}</strong></td>
                                <td>{{ $stock['remaining'] }}</td>
                                <td>{{ $stock['minimum'] }}</td>
                                <td>
                                    <div class="stock-level">
                                        <div class="stock-progress">
                                            <span style="width: {{ $stock['level'] }}%"></span>
                                        </div>
                                        <small>{{ $stock['level'] }}%</small>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="owner-panel top-products-panel">
            <div class="panel-heading">
                <div>
                    <h3>Top Selling Products</h3>
                    <p>Best performing products</p>
                </div>
                <span class="top-products-period">This month</span>
            </div>

            <div class="top-products-list">
                @foreach ($topProducts as $index => $product)
                    <div class="top-product-item">
                        <div class="product-rank">{{ $index + 1 }}</div>

                        <div class="top-product-info">
                            <strong>{{ $product['name'] }}</strong>
                            <span>{{ $product['category'] }}</span>
                            <div class="product-sales-progress">
                                <span style="width: {{ $product['percentage'] }}%"></span>
                            </div>
                        </div>

                        <div class="product-sold">
                            <strong>{{ $product['sold'] }}</strong>
                            <span>sold</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    <div class="owner-dashboard-footer">
        <span>REBANT BOMS</span>
        <span>Business Overview · Dummy Data</span>
    </div>

</div>
@endsection
