<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductGroup;
use App\Models\ProductMaterial;
use App\Models\RawMaterial;
use App\Models\Supplier;
use App\Models\SupplierMaterial;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class MasterDataController extends Controller
{
    /**
     * Tampilkan halaman Master Data (read-only) untuk Business Owner.
     */
    public function index(): View
    {
        return view('owner.master-data.index');
    }

    /**
     * Ambil seluruh data master dan ringkasan statistik langsung dari database.
     */
    public function data(): JsonResponse
    {
        $productGroups = ProductGroup::with([
            'category',
            'products' => fn ($query) => $query->orderBy('sku'),
        ])
            ->orderBy('name')
            ->get()
            ->map(fn (ProductGroup $group): array => $this->formatProductGroup($group))
            ->values();

        $totalVariants = (int) $productGroups->sum('variant_count');

        $categories = Category::withCount(['productGroups', 'products'])
            ->orderBy('name')
            ->get()
            ->map(fn (Category $category): array => $this->formatCategory($category))
            ->values();

        $materials = RawMaterial::orderBy('name')
            ->get()
            ->map(fn (RawMaterial $material): array => $this->formatMaterial($material))
            ->values();

        $bom = ProductMaterial::with(['group', 'rawMaterial'])
            ->orderBy('group_id')
            ->orderBy('id')
            ->get()
            ->map(fn (ProductMaterial $item): array => $this->formatBom($item))
            ->values();

        $suppliers = Supplier::with(['materials.rawMaterial'])
            ->orderBy('name')
            ->get()
            ->map(fn (Supplier $supplier): array => $this->formatSupplier($supplier))
            ->values();

        return response()->json([
            'summary' => [
                'products' => $productGroups->count(),
                'product_variants' => $totalVariants,
                'categories' => $categories->count(),
                'materials' => $materials->count(),
                'suppliers' => $suppliers->count(),
            ],
            'datasets' => [
                'products' => $productGroups,
                'categories' => $categories,
                'materials' => $materials,
                'bom' => $bom,
                'suppliers' => $suppliers,
            ],
        ]);
    }

    /**
     * Format satu grup produk beserta daftar varian SKU-nya untuk sub-baris tabel dan modal detail.
     *
     * @return array<string, mixed>
     */
    private function formatProductGroup(ProductGroup $group): array
    {
        $categoryName = $group->category?->name ?? '—';
        $method = strtoupper((string) ($group->production_method ?? '—'));

        $variants = $group->products->map(function (Product $product) use ($group, $method): array {
            $isActive = $group->is_active && $product->is_active;

            if (! $isActive) {
                $status = 'Tidak Aktif';
            } elseif ($method === 'MTS' && $product->min_stock !== null && $product->current_stock < $product->min_stock) {
                $status = 'Stok Rendah';
            } else {
                $status = 'Aktif';
            }

            return [
                'id' => $product->id,
                'sku' => $product->sku,
                'variant_name' => $product->variant_name ?: 'Standar',
                'stock' => (int) $product->current_stock,
                'min_stock' => $product->min_stock !== null ? (int) $product->min_stock : null,
                'target_stock' => $product->target_stock !== null ? (int) $product->target_stock : null,
                'status' => $status,
            ];
        })->values();

        $hasActiveVariant = $group->products->contains(fn (Product $product): bool => $product->is_active);
        $lowStockCount = $variants->where('status', 'Stok Rendah')->count();

        if (! $group->is_active || ($variants->isNotEmpty() && ! $hasActiveVariant)) {
            $groupStatus = 'Tidak Aktif';
        } elseif ($lowStockCount > 0) {
            $groupStatus = 'Stok Rendah';
        } else {
            $groupStatus = 'Aktif';
        }

        $totalStock = (int) $variants->sum('stock');
        $dimensions = ($group->length_cm && $group->width_cm)
            ? $this->formatNumber($group->length_cm, 2).' × '.$this->formatNumber($group->width_cm, 2).' cm'
            : '—';

        return [
            'id' => $group->id,
            'code' => $group->code,
            'name' => $group->name,
            'category' => $categoryName,
            'production_method' => $method,
            'dimensions' => $dimensions,
            'price' => (float) $group->price,
            'stock' => $totalStock,
            'variant_count' => $variants->count(),
            'low_stock_count' => $lowStockCount,
            'status' => $groupStatus,
            'variants' => $variants->all(),
            'detail' => [
                ['label' => 'Kode Produk', 'value' => $group->code],
                ['label' => 'Nama Produk', 'value' => $group->name],
                ['label' => 'Kategori', 'value' => $categoryName],
                ['label' => 'Metode Produksi', 'value' => $method],
                ['label' => 'Harga Jual', 'value' => (float) $group->price, 'type' => 'currency'],
                ['label' => 'Total Stok', 'value' => $totalStock.' pcs'],
                ['label' => 'Jumlah Varian', 'value' => $variants->count().' varian'],
                ['label' => 'Ukuran', 'value' => $dimensions],
                ['label' => 'Deskripsi', 'value' => $group->description ?: '—'],
                ['label' => 'Status', 'value' => $groupStatus, 'type' => 'status'],
            ],
        ];
    }

    /**
     * Format satu baris kategori produk.
     *
     * @return array<string, mixed>
     */
    private function formatCategory(Category $category): array
    {
        $code = 'CAT-'.str_pad((string) $category->id, 3, '0', STR_PAD_LEFT);
        $groupCount = (int) ($category->product_groups_count ?? 0);
        $variantCount = (int) ($category->products_count ?? 0);

        return [
            'id' => $category->id,
            'code' => $code,
            'name' => $category->name,
            'total' => $groupCount,
            'detail' => [
                ['label' => 'Kode Kategori', 'value' => $code],
                ['label' => 'Nama Kategori', 'value' => $category->name],
                ['label' => 'Jumlah Produk Induk', 'value' => $groupCount.' produk'],
                ['label' => 'Total Varian SKU', 'value' => $variantCount.' SKU'],
            ],
        ];
    }

    /**
     * Format satu baris bahan baku.
     *
     * @return array<string, mixed>
     */
    private function formatMaterial(RawMaterial $material): array
    {
        $stock = (float) $material->current_stock;
        $safety = (float) $material->safety_stock;

        if (! $material->is_active) {
            $status = 'Tidak Aktif';
        } elseif ($stock <= $safety) {
            $status = 'Stok Rendah';
        } else {
            $status = 'Tersedia';
        }

        $sheetSize = $material->has_sheet_size
            ? $this->formatNumber($material->sheet_length_cm, 2).' × '.$this->formatNumber($material->sheet_width_cm, 2).' cm'
            : '—';

        return [
            'id' => $material->id,
            'code' => $material->code,
            'name' => $material->name,
            'unit' => $material->unit,
            'stock' => $stock,
            'minimum' => $safety,
            'status' => $status,
            'detail' => [
                ['label' => 'Kode Bahan', 'value' => $material->code],
                ['label' => 'Nama Bahan', 'value' => $material->name],
                ['label' => 'Tipe Material', 'value' => $material->isSheet() ? 'Lembaran (SHEET)' : 'Non-Lembaran (NON_SHEET)'],
                ['label' => 'Ukuran Lembar', 'value' => $sheetSize],
                ['label' => 'Satuan', 'value' => $material->unit],
                ['label' => 'Stok Tersedia', 'value' => $this->formatNumber($material->current_stock).' '.$material->unit],
                ['label' => 'Stok Minimum', 'value' => $this->formatNumber($material->safety_stock).' '.$material->unit],
                ['label' => 'Target Stok', 'value' => $this->formatNumber($material->target_stock).' '.$material->unit],
                ['label' => 'Reorder Point', 'value' => $this->formatNumber($material->reorder_point).' '.$material->unit],
                ['label' => 'Status', 'value' => $status, 'type' => 'status'],
            ],
        ];
    }

    /**
     * Format satu baris Bill of Materials (resep produk).
     *
     * @return array<string, mixed>
     */
    private function formatBom(ProductMaterial $item): array
    {
        $code = 'BOM-'.str_pad((string) $item->id, 3, '0', STR_PAD_LEFT);
        $group = $item->group;
        $material = $item->rawMaterial;
        $unit = $material?->unit ?? '—';
        $usageMethod = strtoupper((string) $item->usage_method);

        $pieceSize = ($usageMethod === 'AREA' && $item->piece_length_cm && $item->piece_width_cm)
            ? $this->formatNumber($item->piece_length_cm, 2).' × '.$this->formatNumber($item->piece_width_cm, 2).' cm ('.$item->piece_count.' pcs)'
            : '—';

        return [
            'id' => $item->id,
            'code' => $code,
            'product' => $group ? ($group->code.' — '.$group->name) : '—',
            'material' => $material?->name ?? '—',
            'quantity' => (float) $item->qty_per_unit,
            'unit' => $unit,
            'detail' => [
                ['label' => 'Kode BOM', 'value' => $code],
                ['label' => 'Kode Produk', 'value' => $group?->code ?? '—'],
                ['label' => 'Nama Produk', 'value' => $group?->name ?? '—'],
                ['label' => 'Bahan Baku', 'value' => $material ? ($material->code.' — '.$material->name) : '—'],
                ['label' => 'Metode Hitung', 'value' => $usageMethod],
                ['label' => 'Ukuran Potong', 'value' => $pieceSize],
                ['label' => 'Toleransi Limbah', 'value' => $usageMethod === 'AREA' ? $this->formatNumber($item->waste_percent, 2).'%' : '—'],
                ['label' => 'Kebutuhan per Unit', 'value' => $this->formatNumber($item->qty_per_unit, 6).' '.$unit],
            ],
        ];
    }

    /**
     * Format satu baris supplier beserta ringkasan bahan yang dipasok.
     *
     * @return array<string, mixed>
     */
    private function formatSupplier(Supplier $supplier): array
    {
        $code = 'SUP-'.str_pad((string) $supplier->id, 3, '0', STR_PAD_LEFT);
        $status = $supplier->is_active ? 'Aktif' : 'Tidak Aktif';

        $materialNames = $supplier->materials
            ->map(fn (SupplierMaterial $sm): ?string => $sm->rawMaterial?->name)
            ->filter()
            ->unique()
            ->values();

        $materialDetails = $supplier->materials
            ->map(function (SupplierMaterial $sm): ?string {
                $name = $sm->rawMaterial?->name;
                if (! $name) {
                    return null;
                }

                $parts = ['Rp'.number_format((float) $sm->unit_price, 0, ',', '.')];

                if ($sm->lead_time_days !== null) {
                    $parts[] = 'lead time '.$sm->lead_time_days.' hari';
                }

                return $name.' ('.implode(' · ', $parts).')';
            })
            ->filter()
            ->values();

        return [
            'id' => $supplier->id,
            'code' => $code,
            'name' => $supplier->name,
            'contact' => $supplier->phone ?: ($supplier->email ?: '—'),
            'address' => $supplier->address ?: '—',
            'material' => $materialNames->isNotEmpty() ? $materialNames->implode(', ') : '—',
            'status' => $status,
            'detail' => [
                ['label' => 'Kode Supplier', 'value' => $code],
                ['label' => 'Nama Supplier', 'value' => $supplier->name],
                ['label' => 'Email', 'value' => $supplier->email ?: '—'],
                ['label' => 'Telepon', 'value' => $supplier->phone ?: '—'],
                ['label' => 'Alamat', 'value' => $supplier->address ?: '—'],
                ['label' => 'Bahan Dipasok', 'value' => $materialDetails->isNotEmpty() ? $materialDetails->implode('; ') : '—'],
                ['label' => 'Status', 'value' => $status, 'type' => 'status'],
            ],
        ];
    }

    /**
     * Format angka desimal dalam format Indonesia tanpa nol berlebih di belakang koma.
     */
    private function formatNumber(float|int|string|null $value, int $decimals = 3): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        $formatted = number_format((float) $value, $decimals, ',', '.');

        return rtrim(rtrim($formatted, '0'), ',');
    }
}
