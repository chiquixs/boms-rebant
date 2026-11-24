<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use App\Models\OrderItemAllocation;
use App\Models\Product;
use App\Models\ProductionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class StockPlanningController extends Controller
{
    // products + product_groups + categories.
    // Hanya varian aktif dari grup MTS yang relevan untuk halaman ini.
    public function products()
    {
        $products = Product::with(['group.category'])
            ->where('is_active', true)
            ->whereHas('group', fn ($q) => $q->where('is_active', true)->where('production_method', 'MTS'))
            ->get();

        $reservedByProduct = $this->reservedStockByProductIds($products->pluck('id'));

        $payload = $products->map(function (Product $p) use ($reservedByProduct): array {
            $currentStock = (float) $p->current_stock;
            $reservedStock = (float) ($reservedByProduct[$p->id] ?? 0);
            $availableStock = max(0, $currentStock - $reservedStock);

            return [
                'id' => $p->id,
                'group_id' => $p->group_id,
                'group' => [
                    'id' => $p->group->id,
                    'name' => $p->group->name,
                    'image_path' => $p->group->image_url,
                    'category' => $p->group->category ? [
                        'id' => $p->group->category->id,
                        'name' => $p->group->category->name,
                    ] : null,
                ],
                'variant_name' => $p->variant_name,
                'sku' => $p->sku,
                'current_stock' => $currentStock,
                'reserved_stock' => $reservedStock,
                'available_stock' => $availableStock,
                'min_stock' => $p->min_stock !== null ? (float) $p->min_stock : 0,
                'target_stock' => $p->target_stock !== null ? (float) $p->target_stock : 0,
                'image_path' => $p->image_url,
                'is_active' => $p->is_active,
            ];
        });

        return response()->json($payload);
    }

    // Request pengisian stok MTS (RESTOCK) yang masih aktif (WAITING / IN_PROCESS).
    public function requests()
    {
        $requests = ProductionRequest::withSum('logs as logged_qty_produced', 'qty_produced')
            ->where('source', ProductionRequest::SOURCE_RESTOCK)
            ->whereIn('status', [
                ProductionRequest::STATUS_WAITING,
                ProductionRequest::STATUS_IN_PROCESS,
            ])
            ->whereHas('product', fn ($q) => $q
                ->where('is_active', true)
                ->whereHas('group', fn ($g) => $g
                    ->where('is_active', true)
                    ->where('production_method', 'MTS')))
            ->get()
            ->map(fn (ProductionRequest $r): array => [
                'id' => $r->id,
                'product_id' => $r->product_id,
                'request_no' => $r->request_no,
                'source' => $r->source,
                'qty_requested' => $r->qty_requested,
                'qty_produced' => (int) ($r->logged_qty_produced ?? $r->qty_produced ?? 0),
                'status' => $r->status,
            ]);

        return response()->json($requests);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'qty_requested' => ['required', 'integer', 'min:1'],
            'source' => ['required', 'in:RESTOCK'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $product = Product::with('group')->findOrFail($data['product_id']);

        if (! $product->is_active || ! $product->group || ! $product->group->is_active || $product->group->production_method !== 'MTS') {
            return response()->json(['message' => 'Produk ini tidak valid untuk permintaan produksi MTS.'], 422);
        }

        // Validasi ulang rekomendasi di server — jangan percaya angka dari client,
        // bisa saja sudah berubah sejak terakhir kali halaman dimuat.
        $reserved = (int) ($this->reservedStockByProductIds(collect([$product->id]))[$product->id] ?? 0);
        $available = max(0, (int) $product->current_stock - $reserved);

        $planned = ProductionRequest::withSum('logs as logged_qty_produced', 'qty_produced')
            ->where('product_id', $product->id)
            ->where('source', ProductionRequest::SOURCE_RESTOCK)
            ->whereIn('status', [ProductionRequest::STATUS_WAITING, ProductionRequest::STATUS_IN_PROCESS])
            ->get()
            ->sum(function (ProductionRequest $r): int {
                $produced = (int) ($r->logged_qty_produced ?? $r->qty_produced ?? 0);

                return max(0, $r->qty_requested - $produced);
            });

        $base = max(0, (int) $product->target_stock - $available);
        $rec = max(0, $base - $planned);

        if ($rec <= 0) {
            return response()->json([
                'message' => 'Rekomendasi produksi untuk produk ini sudah 0 (stok sudah cukup atau sudah ada request aktif). Request tidak dibuat.',
            ], 422);
        }

        // Catatan: production_requests belum punya kolom notes di skema,
        // jadi input catatan divalidasi tapi untuk sekarang tidak disimpan.
        $pr = ProductionRequest::create([
            'product_id' => $product->id,
            'order_item_id' => null,
            'requested_by' => $request->user()->id,
            'request_no' => $this->generateRequestNo(),
            'source' => ProductionRequest::SOURCE_RESTOCK,
            'qty_requested' => $data['qty_requested'],
            'qty_produced' => 0,
            'status' => ProductionRequest::STATUS_WAITING,
        ]);

        return response()->json(['success' => true, 'request_no' => $pr->request_no]);
    }

    /**
     * @param  Collection<int, int>  $productIds
     * @return array<int, int>
     */
    private function reservedStockByProductIds(Collection $productIds): array
    {
        if ($productIds->isEmpty()) {
            return [];
        }

        return OrderItemAllocation::query()
            ->where('status', OrderItemAllocation::STATUS_RESERVED)
            ->whereIn('product_id', $productIds)
            ->selectRaw('product_id, SUM(qty_allocated) as total')
            ->groupBy('product_id')
            ->pluck('total', 'product_id')
            ->map(fn ($total): int => (int) $total)
            ->all();
    }

    private function generateRequestNo(): string
    {
        $last = ProductionRequest::where('request_no', 'like', 'PR-%')
            ->orderByDesc('request_no')
            ->value('request_no');

        $next = $last ? ((int) substr($last, 3)) + 1 : 1;

        return 'PR-'.str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }
}
