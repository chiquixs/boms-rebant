@extends('layouts.admin')

@section('title', 'Inventory')

@section('content')

<div class="inventory-page">

    {{-- =========================
        PAGE HEADER
    ========================== --}}
    <div class="inventory-page-header">
        <div>
            <h1>Inventory</h1>
            <p>Pantau stok produk jadi, bahan baku, dan pergerakan persediaan.</p>
        </div>

        <div class="inventory-header-icon">
            <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8">
                <path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5v-9Z" />
                <path d="m4 7.5 8 4.5 8-4.5" />
                <path d="M12 12v9" />
            </svg>
        </div>
    </div>


    {{-- =========================
        SUMMARY
    ========================== --}}
    <div class="inventory-summary-grid">

        <div class="inventory-summary-card">
            <div class="summary-label">SKU Stok Rendah</div>
            <div class="summary-value danger">{{ $lowStockSkuCount }}</div>
            <div class="summary-caption">Variant di bawah safety stock</div>
        </div>

        <div class="inventory-summary-card">
            <div class="summary-label">Bahan Baku Menipis</div>
            <div class="summary-value warning">{{ $lowStockMaterialsCount }}</div>
            <div class="summary-caption">Bahan di bawah safety stock</div>
        </div>

        <div class="inventory-summary-card">
            <div class="summary-label">Stok Masuk Hari Ini</div>
            <div class="summary-value">{{ $todayInCount }}</div>
            <div class="summary-caption">Transaksi stok masuk</div>
        </div>

        <div class="inventory-summary-card">
            <div class="summary-label">Stok Keluar Hari Ini</div>
            <div class="summary-value">{{ $todayOutCount }}</div>
            <div class="summary-caption">Transaksi stok keluar</div>
        </div>

    </div>


    {{-- =========================
        INVENTORY CARD
    ========================== --}}
    <div class="inventory-card">

        {{-- TABS --}}
        <div class="inventory-tabs">

            <button
                type="button"
                class="inventory-tab active"
                data-tab="finishedGoods">
                Produk Jadi
            </button>

            <button
                type="button"
                class="inventory-tab"
                data-tab="rawMaterials">
                Bahan Baku
            </button>

            <button
                type="button"
                class="inventory-tab"
                data-tab="stockHistory">
                Riwayat
            </button>

        </div>


        {{-- ========================================
             PRODUK JADI
        ========================================= --}}
        <div
            class="inventory-tab-content active"
            id="finishedGoods">

            <div class="inventory-section-header">
                <div>
                    <h2>Stok Produk Jadi</h2>
                    <p>Stok produk MTS berdasarkan SKU Variant.</p>
                </div>
            </div>

            <div class="inventory-table-wrapper">

                <table class="inventory-table finished-goods-table">

                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Varian</th>
                            <th>SKU Variant</th>
                            <th>Stok Saat Ini</th>
                            <th>Safety Stock</th>
                            <th>Target Stock</th>
                            <th>Status</th>
                            <th class="inventory-action-column">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($products as $product)
                        @php
                        $current = $product->current_stock;
                        $safety = $product->min_stock;

                        if ($safety === null) {
                        $statusClass = 'safe'; $statusLabel = 'Aman';
                        } elseif ($current < $safety) {
                            $statusClass='low' ; $statusLabel='Rendah' ;
                            } elseif ($current==$safety) {
                            $statusClass='warning' ; $statusLabel='Perhatian' ;
                            } else {
                            $statusClass='safe' ; $statusLabel='Aman' ;
                            }
                            @endphp

                            <tr>
                            <td>
                                <div class="inventory-product-info">
                                    <strong>{{ $product->group->name ?? '—' }}</strong>
                                    <span>SKU Product: {{ $product->group->code ?? '—' }}</span>
                                </div>
                            </td>

                            <td>{{ $product->variant_name ?? '—' }}</td>

                            <td><span class="inventory-sku">{{ $product->sku }}</span></td>

                            <td class="inventory-stock-number {{ $statusClass === 'low' ? 'low-stock' : '' }}">
                                {{ $current }}
                            </td>
                            <td class="inventory-stock-number">{{ $safety ?? '—' }}</td>
                            <td class="inventory-stock-number">{{ $product->target_stock ?? '—' }}</td>

                            <td><span class="stock-status {{ $statusClass }}">{{ $statusLabel }}</span></td>

                            <td>
                                <a href="{{ route('admin.inventory.history', $product) }}" class="history-button">
                                    Riwayat
                                </a>
                            </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" style="text-align:center; padding:24px;">
                                    Belum ada produk jadi.
                                </td>
                            </tr>
                            @endforelse
                    </tbody>

                </table>

            </div>

        </div>


        {{-- ========================================
             BAHAN BAKU
        ========================================= --}}
        <div
            class="inventory-tab-content"
            id="rawMaterials">

            <div class="inventory-section-header">
                <div>
                    <h2>Stok Bahan Baku</h2>
                    <p>Stok dan riwayat bahan mengikuti master data bahan mentah.</p>
                </div>
            </div>

            <div class="inventory-table-wrapper">

                <table class="inventory-table raw-material-table">

                    <thead>
                        <tr>
                            <th>Bahan</th>
                            <th>Tipe</th>
                            <th>Satuan</th>
                            <th>Stok Saat Ini</th>
                            <th>Safety Stock</th>
                            <th>Target Stock</th>
                            <th>Status</th>
                            <th class="inventory-action-column">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($rawMaterials as $material)
                        @php
                        $isLow = $material->current_stock < $material->safety_stock;
                            @endphp

                            <tr>
                                <td>
                                    <div class="inventory-product-info">
                                        <strong>{{ $material->name }}</strong>
                                        @if ($material->isSheet())
                                        <span>Ukuran: {{ rtrim(rtrim(number_format($material->sheet_length_cm, 2), '0'), '.') }} × {{ rtrim(rtrim(number_format($material->sheet_width_cm, 2), '0'), '.') }} cm</span>
                                        @endif
                                    </div>
                                </td>

                                <td>
                                    <span class="inventory-material-type {{ $material->isSheet() ? 'sheet' : 'non-sheet' }}">
                                        {{ $material->isSheet() ? 'SHEET' : 'NON-SHEET' }}
                                    </span>
                                </td>

                                <td>{{ $material->unit }}</td>

                                <td class="inventory-stock-number {{ $isLow ? 'low-stock' : '' }}">
                                    {{ rtrim(rtrim(number_format($material->current_stock, 3), '0'), '.') }}
                                </td>
                                <td class="inventory-stock-number">
                                    {{ rtrim(rtrim(number_format($material->safety_stock, 3), '0'), '.') }}
                                </td>
                                <td class="inventory-stock-number">
                                    {{ rtrim(rtrim(number_format($material->target_stock, 3), '0'), '.') }}
                                </td>

                                <td>
                                    <span class="stock-status {{ $isLow ? 'low' : 'safe' }}">
                                        {{ $isLow ? 'Rendah' : 'Aman' }}
                                    </span>
                                </td>

                                <td>
                                    <a href="{{ route('admin.inventory.material-history', $material) }}" class="history-button">
                                        Riwayat
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" style="text-align:center; padding:24px;">
                                    Belum ada data bahan baku.
                                </td>
                            </tr>
                            @endforelse
                    </tbody>

                </table>

            </div>

        </div>


        {{-- ========================================
             RIWAYAT GLOBAL
        ========================================= --}}
        <div
            class="inventory-tab-content"
            id="stockHistory">

            <div class="inventory-section-header">
                <div>
                    <h2>Riwayat Pergerakan Stok</h2>
                    <p>Catatan perubahan stok produk jadi dan bahan baku.</p>
                </div>
            </div>

            <div class="inventory-table-wrapper">

                <table class="inventory-table history-table">

                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Item</th>
                            <th>Aktivitas</th>
                            <th>Perubahan</th>
                            <th>Sumber</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($recentHistory as $h)
                        <tr>
                            <td>{{ $h->created_at->translatedFormat('d M Y') }}</td>

                            <td>
                                <div class="history-item">
                                    <strong>{{ $h->item_name }}</strong>
                                    <span>{{ $h->item_sub }}</span>
                                </div>
                            </td>

                            <td>{{ $h->activity }}</td>

                            <td>
                                <span class="stock-change {{ $h->direction === 'in' ? 'in' : 'out' }}">
                                    {{ $h->direction === 'in' ? '+' : '−' }}{{ abs($h->delta) }} {{ $h->unit }}
                                </span>
                            </td>

                            <td>{{ $h->source }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:24px;">
                                Belum ada riwayat pergerakan stok.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const tabs =
            document.querySelectorAll('.inventory-tab');

        const tabContents =
            document.querySelectorAll('.inventory-tab-content');


        tabs.forEach(function(tab) {

            tab.addEventListener('click', function() {

                const targetId =
                    tab.dataset.tab;


                tabs.forEach(function(item) {
                    item.classList.remove('active');
                });


                tabContents.forEach(function(content) {
                    content.classList.remove('active');
                });


                tab.classList.add('active');


                const targetContent =
                    document.getElementById(targetId);


                if (targetContent) {
                    targetContent.classList.add('active');
                }

            });

        });

    });
</script>

@endpush