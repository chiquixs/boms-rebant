<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductionRequest;
use Illuminate\Http\Request;

class StockPlanningController extends Controller
{
    // products + product_groups + categories.
    // Hanya varian aktif dari grup MTS yang relevan untuk halaman ini.
    public function products()
    {
        $products = Product::with(['group.category'])
            ->where('is_active', true)
            ->whereHas('group', fn ($q) => $q->where('is_active', true)->where('production_method', 'MTS'))
            ->get()
            ->map(fn ($p) => [
                'id'            => $p->id,
                'group_id'      => $p->group_id,
                'group'         => [
                    'id'         => $p->group->id,
                    'name'       => $p->group->name,
                    'image_path' => $p->group->image_url,
                    'category'   => $p->group->category ? [
                        'id'   => $p->group->category->id,
                        'name' => $p->group->category->name,
                    ] : null,
                ],
                'variant_name'  => $p->variant_name,
                'sku'           => $p->sku,
                'current_stock' => (float) $p->current_stock,
                'min_stock'     => $p->min_stock !== null ? (float) $p->min_stock : 0,
                'target_stock'  => $p->target_stock !== null ? (float) $p->target_stock : 0,
                'image_path'    => $p->image_url,
                'is_active'     => $p->is_active,
            ]);

        return response()->json($products);
    }

    // production_requests yang masih aktif (WAITING/IN_PROCESS).
    // COMPLETED sengaja tidak dikirim karena tidak relevan untuk hitung rekomendasi.
    public function requests()
    {
        $requests = ProductionRequest::whereIn('status', [
                ProductionRequest::STATUS_WAITING,
                ProductionRequest::STATUS_IN_PROCESS,
            ])
            ->get()
            ->map(fn ($r) => [
                'id'            => $r->id,
                'product_id'    => $r->product_id,
                'request_no'    => $r->request_no,
                'source'        => $r->source,
                'qty_requested' => $r->qty_requested,
                'qty_produced'  => $r->qty_produced,
                'status'        => $r->status,
            ]);

        return response()->json($requests);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id'    => ['required', 'exists:products,id'],
            'qty_requested' => ['required', 'integer', 'min:1'],
            'source'        => ['required', 'in:RESTOCK'],
            'notes'         => ['nullable', 'string', 'max:500'],
        ]);

        $product = Product::with('group')->findOrFail($data['product_id']);

        if (! $product->is_active || ! $product->group || $product->group->production_method !== 'MTS') {
            return response()->json(['message' => 'Produk ini tidak valid untuk permintaan produksi MTS.'], 422);
        }

        // Validasi ulang rekomendasi di server — jangan percaya angka dari client,
        // bisa saja sudah berubah sejak terakhir kali halaman dimuat.
        $planned = ProductionRequest::where('product_id', $product->id)
            ->whereIn('status', [ProductionRequest::STATUS_WAITING, ProductionRequest::STATUS_IN_PROCESS])
            ->get()
            ->sum(fn ($r) => max(0, $r->qty_requested - $r->qty_produced));

        $base = max(0, $product->target_stock - $product->current_stock);
        $rec = max(0, $base - $planned);

        if ($rec <= 0) {
            return response()->json([
                'message' => 'Rekomendasi produksi untuk produk ini sudah 0 (stok sudah cukup atau sudah ada request aktif). Request tidak dibuat.',
            ], 422);
        }

        // Catatan: production_requests belum punya kolom notes di skema,
        // jadi input catatan divalidasi tapi untuk sekarang tidak disimpan.
        $pr = ProductionRequest::create([
            'product_id'    => $product->id,
            'order_item_id' => null,
            'requested_by'  => $request->user()->id,
            'request_no'    => $this->generateRequestNo(),
            'source'        => ProductionRequest::SOURCE_RESTOCK,
            'qty_requested' => $data['qty_requested'],
            'qty_produced'  => 0,
            'status'        => ProductionRequest::STATUS_WAITING,
        ]);

        return response()->json(['success' => true, 'request_no' => $pr->request_no]);
    }

    private function generateRequestNo(): string
    {
        $last = ProductionRequest::where('request_no', 'like', 'PR-%')
            ->orderByDesc('request_no')
            ->value('request_no');

        $next = $last ? ((int) substr($last, 3)) + 1 : 1;

        return 'PR-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }
}