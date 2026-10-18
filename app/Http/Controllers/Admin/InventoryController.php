<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductStockMovement;
use App\Models\RawMaterial;
use App\Models\RawMaterialMovement;


class InventoryController extends Controller
{
    public function index()
    {
        $rawMaterials = RawMaterial::orderBy('name')->get();

        $lowStockMaterialsCount = RawMaterial::whereColumn('current_stock', '<', 'safety_stock')
            ->where('is_active', true)
            ->count();

        $products = Product::with('group')
            ->where('is_active', true)
            ->orderBy('group_id')
            ->orderBy('variant_name')
            ->get();

        $lowStockSkuCount = Product::whereNotNull('min_stock')
            ->whereColumn('current_stock', '<', 'min_stock')
            ->where('is_active', true)
            ->count();

        $recentHistory = $this->buildCombinedHistory(limit: 50);

        $todayInCount = ProductStockMovement::whereDate('created_at', today())
            ->whereColumn('qty_after', '>', 'qty_before')->count()
            + RawMaterialMovement::whereDate('created_at', today())
            ->whereColumn('qty_after', '>', 'qty_before')->count();

        $todayOutCount = ProductStockMovement::whereDate('created_at', today())
            ->whereColumn('qty_after', '<', 'qty_before')->count()
            + RawMaterialMovement::whereDate('created_at', today())
            ->whereColumn('qty_after', '<', 'qty_before')->count();

        return view('admin.inventory.index', compact(
            'rawMaterials',
            'lowStockMaterialsCount',
            'products',
            'lowStockSkuCount',
            'recentHistory',
            'todayInCount',
            'todayOutCount'
        ));
    }

    private function buildCombinedHistory(int $limit = 50)
    {
        $productMovements = ProductStockMovement::with(['product.group', 'orderItem.order'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($m) {
                $delta = $m->qty_after - $m->qty_before;

                return (object) [
                    'created_at'  => $m->created_at,
                    'item_name'   => $m->product->sku ?? '—',
                    'item_sub'    => $m->product->group->name ?? ($m->product->variant_name ?? '—'),
                    'activity'    => match (true) {
                        $m->movement_type === 'IN' && $m->production_log_id => 'Produksi selesai',
                        $m->movement_type === 'OUT' && $m->order_item_id    => 'Pesanan siap dikemas',
                        default => ucfirst(strtolower($m->movement_type)),
                    },
                    'delta'       => $delta,
                    'unit'        => 'pcs',
                    'source'      => $m->orderItem ? 'Order' : ($m->production_log_id ? 'Produksi' : 'Adjustment'),
                    'direction'   => $delta >= 0 ? 'in' : 'out',
                    'period'      => $m->created_at->isToday() ? 'today' : 'other',
                ];
            });

        $materialMovements = RawMaterialMovement::with('rawMaterial')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($m) {
                $delta = $m->qty_after - $m->qty_before;

                return (object) [
                    'created_at'  => $m->created_at,
                    'item_name'   => $m->rawMaterial->name ?? '—',
                    'item_sub'    => 'Bahan Baku',
                    'activity'    => match (true) {
                        $m->movement_type === 'IN' && $m->purchase_order_item_id => 'Penerimaan bahan',
                        $m->movement_type === 'OUT' && $m->production_log_material_id => 'Pemakaian produksi',
                        default => ucfirst(strtolower($m->movement_type)),
                    },
                    'delta'       => $delta,
                    'unit'        => $m->rawMaterial->unit ?? '',
                    'source'      => $m->purchase_order_item_id ? 'Procurement' : ($m->production_log_material_id ? 'Produksi' : 'Adjustment'),
                    'direction'   => $delta >= 0 ? 'in' : 'out',
                    'period'      => $m->created_at->isToday() ? 'today' : 'other',
                ];
            });

        return $productMovements
            ->concat($materialMovements)
            ->sortByDesc('created_at')
            ->take($limit)
            ->values();
    }

    public function productHistory(Product $product)
    {
        $movements = ProductStockMovement::where('product_id', $product->id)
            ->with(['creator', 'orderItem.order'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        $totalIn  = $movements->sum(fn($m) => max($m->delta, 0));
        $totalOut = $movements->sum(fn($m) => max(-$m->delta, 0));

        return view('admin.inventory.history', compact(
            'product',
            'movements',
            'totalIn',
            'totalOut'
        ));
    }

    public function materialHistory(RawMaterial $material)
    {
        $movements = RawMaterialMovement::where('raw_material_id', $material->id)
            ->with('creator')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        $totalIn  = $movements->sum(fn($m) => max($m->delta, 0));
        $totalOut = $movements->sum(fn($m) => max(-$m->delta, 0));

        return view('admin.inventory.material-history', compact(
            'material',
            'movements',
            'totalIn',
            'totalOut'
        ));
    }
}
