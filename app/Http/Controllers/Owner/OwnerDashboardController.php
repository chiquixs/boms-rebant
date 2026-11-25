<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Platform;
use App\Models\Product;
use App\Models\ProductionRequest;
use App\Models\RawMaterial;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OwnerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $filterStart = $request->query('start');
        $filterEnd   = $request->query('end');
        $dateError   = null;

        if ($filterStart && $filterEnd) {
            if ($filterStart > $filterEnd) {
                $dateError = 'Tanggal akhir harus setelah tanggal awal.';
                $start = now()->subDays(29)->toDateString();
                $end   = now()->toDateString();
            } else {
                $start = $filterStart;
                $end   = $filterEnd;
            }
        } else {
            $start = now()->subDays(29)->toDateString();
            $end   = now()->toDateString();
        }

        // Nilai yang ditampilkan kembali di input, supaya user tidak perlu
        // mengetik ulang walau inputnya invalid (start > end).
        $inputStart = $filterStart ?: $start;
        $inputEnd   = $filterEnd ?: $end;

        $days = (int) (Carbon::parse($start)->diffInDays(Carbon::parse($end)) + 1);

        $prevEnd   = Carbon::parse($start)->subDay()->toDateString();
        $prevStart = Carbon::parse($prevEnd)->subDays($days - 1)->toDateString();

        $totalOrders = Order::whereBetween('order_date', [$start, $end])->count();
        $prevOrders  = Order::whereBetween('order_date', [$prevStart, $prevEnd])->count();

        $totalSales = (float) Order::whereBetween('order_date', [$start, $end])->sum('total_amount');
        $prevSales  = (float) Order::whereBetween('order_date', [$prevStart, $prevEnd])->sum('total_amount');

        $totalQty = (int) OrderItem::whereHas('order', fn ($q) => $q->whereBetween('order_date', [$start, $end]))->sum('qty');
        $prevQty  = (int) OrderItem::whereHas('order', fn ($q) => $q->whereBetween('order_date', [$prevStart, $prevEnd]))->sum('qty');

        $lowStockCount = Product::where('is_active', true)
            ->whereNotNull('min_stock')
            ->whereColumn('current_stock', '<', 'min_stock')
            ->count();

        $lowMaterialCount = RawMaterial::where('is_active', true)
            ->whereColumn('current_stock', '<=', 'safety_stock')
            ->count();

        $finishedStockTotal = (int) Product::where('is_active', true)->sum('current_stock');
        $rawMaterialCount   = RawMaterial::where('is_active', true)->count();

        $waiting        = ProductionRequest::where('status', ProductionRequest::STATUS_WAITING)->count();
        $inProcess      = ProductionRequest::where('status', ProductionRequest::STATUS_IN_PROCESS)->count();
        $completedToday = ProductionRequest::where('status', ProductionRequest::STATUS_COMPLETED)
            ->whereDate('completed_at', now()->toDateString())
            ->count();

        $active          = ProductionRequest::whereIn('status', [ProductionRequest::STATUS_WAITING, ProductionRequest::STATUS_IN_PROCESS])->get();
        $activeRequested = $active->sum('qty_requested');
        $activeProduced  = $active->sum('qty_produced');
        $productionProgress = $activeRequested > 0 ? round($activeProduced / $activeRequested * 100) : 0;

        $urgentCount = ProductionRequest::where('source', ProductionRequest::SOURCE_ORDER)
            ->where('status', ProductionRequest::STATUS_WAITING)
            ->count();

        $topQtyTotal = $totalQty ?: 1;

        // Semua item order pada periode, apa pun status ordernya
        $soldItems = OrderItem::with('product.group')
            ->whereHas('order', fn ($q) => $q->whereBetween('order_date', [$start, $end]))
            ->get();

        // Per varian (SKU)
        $topVariants = $soldItems
            ->groupBy('product_id')
            ->map(function ($rows) use ($topQtyTotal) {
                $product = $rows->first()->product;
                $qty     = (int) $rows->sum('qty');

                return [
                    'name'       => $product?->group?->name ?? 'Produk dihapus',
                    'variant'    => $product?->variant_name,
                    'sku'        => $product?->sku ?? '-',
                    'qty'        => $qty,
                    'revenue'    => (float) $rows->sum('subtotal'),
                    'percentage' => round($qty / $topQtyTotal * 100, 1),
                ];
            })
            ->sortByDesc('qty')
            ->values();

        // Per produk induk, dengan rincian varian
        $topProducts = $soldItems
            ->groupBy(fn ($item) => $item->product?->group_id ?? 0)
            ->map(function ($rows) use ($topQtyTotal) {
                $group = $rows->first()->product?->group;
                $qty   = (int) $rows->sum('qty');

                return [
                    'name'       => $group?->name ?? 'Produk dihapus',
                    'code'       => $group?->code ?? '-',
                    'qty'        => $qty,
                    'revenue'    => (float) $rows->sum('subtotal'),
                    'percentage' => round($qty / $topQtyTotal * 100, 1),
                    'variants'   => $rows->groupBy('product_id')->map(fn ($v) => [
                        'sku'     => $v->first()->product?->sku ?? '-',
                        'variant' => $v->first()->product?->variant_name,
                        'qty'     => (int) $v->sum('qty'),
                    ])->sortByDesc('qty')->values(),
                ];
            })
            ->sortByDesc('qty')
            ->values();

        return view('owner.dashboard.index', [
            'filterStart' => $start,
            'filterEnd'   => $end,
            'inputStart'  => $inputStart,
            'inputEnd'    => $inputEnd,
            'dateError'   => $dateError,

            'totalOrders'   => $totalOrders,
            'orderGrowth'   => $this->growth($totalOrders, $prevOrders),
            'totalSales'    => $totalSales,
            'salesGrowth'   => $this->growth($totalSales, $prevSales),
            'totalQty'      => $totalQty,
            'qtyGrowth'     => $this->growth($totalQty, $prevQty),
            'lowStockCount' => $lowStockCount,

            'finishedStockTotal' => $finishedStockTotal,
            'rawMaterialCount'   => $rawMaterialCount,
            'lowMaterialCount'   => $lowMaterialCount,

            'waiting'            => $waiting,
            'inProcess'          => $inProcess,
            'completedToday'     => $completedToday,
            'productionProgress' => $productionProgress,
            'urgentCount'        => $urgentCount,

            'topProducts' => $topProducts,
            'topVariants' => $topVariants,
        ]);
    }

    public function data(Request $request)
    {
        $start = $request->query('start') ?: now()->subDays(29)->toDateString();
        $end   = $request->query('end') ?: now()->toDateString();

        if ($start > $end) {
            [$start, $end] = [$end, $start];
        }

        $platforms = Platform::orderBy('name')->get();

        $rows = Order::selectRaw('platform_id, order_date, SUM(total_amount) as total')
            ->whereBetween('order_date', [$start, $end])
            ->groupBy('platform_id', 'order_date')
            ->get();

        $series = [];

        foreach ($platforms as $platform) {

            $byDate = [];
            for ($d = Carbon::parse($start); $d->lte(Carbon::parse($end)); $d->addDay()) {
                $byDate[$d->toDateString()] = 0;
            }

            foreach ($rows->where('platform_id', $platform->id) as $row) {
                $byDate[$row->order_date->toDateString()] = (float) $row->total;
            }

            $series[] = [
                'platform' => $platform->name,
                'dates'    => array_keys($byDate),
                'values'   => array_values($byDate),
            ];
        }

        return response()->json(['series' => $series]);
    }

    private function growth($current, $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}