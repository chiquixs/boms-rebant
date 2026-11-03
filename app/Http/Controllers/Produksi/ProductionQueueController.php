<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use App\Models\ProductionRequest;

class ProductionQueueController extends Controller
{
    public function index()
    {
        $requests = ProductionRequest::with(['product.group', 'orderItem'])
            ->get()
            ->map(fn ($r) => $this->format($r))
            ->sortBy([
                // ORDER duluan (0), RESTOCK belakangan (1)
                fn ($a, $b) => $this->priorityOf($a) <=> $this->priorityOf($b),
                fn ($a, $b) => $a['created_at'] <=> $b['created_at'],
                fn ($a, $b) => $a['id'] <=> $b['id'],
            ])
            ->values();

        return response()->json($requests);
    }

    private function priorityOf(array $row): int
    {
        return $row['source'] === 'ORDER' ? 0 : 1;
    }

    private function format(ProductionRequest $r): array
    {
        $product = $r->product;
        $group = $product?->group;

        return [
            'id'            => $r->id,
            'request_no'    => $r->request_no,
            'source'        => $r->source,
            'product_name'  => $group->name ?? '-',
            'variant_name'  => $product->variant_name ?? null,
            'sku'           => $product->sku ?? '-',
            'image_url'     => $product->image_url ?? $group->image_url ?? null,
            'qty_requested' => $r->qty_requested,
            'qty_produced'  => $r->total_produced,
            'status'        => $r->status,
            'created_at'    => $r->created_at->toIso8601String(),
        ];
    }
}