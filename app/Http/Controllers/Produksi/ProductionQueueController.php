<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use App\Models\ProductionRequest;
use App\Models\MaterialShortage;
use App\Models\ProductionLog;
use App\Models\ProductionLogMaterial;
use App\Models\ProductStockMovement;
use App\Models\RawMaterial;
use App\Models\RawMaterialMovement;
use App\Services\OrderStockService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductionQueueController extends Controller
{
    public function __construct(
        private readonly OrderStockService $orderStockService
    ) {}

    public function index()
    {
        $requests = ProductionRequest::with(['product.group.category', 'orderItem'])
            ->get()
            ->map(fn($r) => $this->format($r))
            ->sortBy([
                fn($a, $b) => $this->priorityOf($a) <=> $this->priorityOf($b),
                fn($a, $b) => $a['created_at'] <=> $b['created_at'],
                fn($a, $b) => $a['id'] <=> $b['id'],
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

            'product_group' => $group ? [
                'name'               => $group->name,
                'code'               => $group->code,
                'description'        => $group->description,
                'production_method'  => $group->production_method,
                'category_name'      => $group->category->name ?? null,
                'length_cm'          => $group->length_cm,
                'width_cm'           => $group->width_cm,
                'price'              => $group->price,
                'image_url'          => $group->image_url,
            ] : null,
        ];
    }

    public function materials(ProductionRequest $productionRequest)
    {
        $product = $productionRequest->product()->with('group.materials.rawMaterial')->firstOrFail();
        $remaining = max(0, $productionRequest->qty_requested - $productionRequest->total_produced);

        $continuousUnits = ['ml', 'lt', 'kg', 'gr', 'mm', 'cm', 'm'];

        $boms = $product->group->materials->map(function ($pm) use ($continuousUnits) {
            $rm = $pm->rawMaterial;
            $unit = $rm->unit ?? '-';
            $normalizedUnit = strtolower(trim($unit));

            return [
                'raw_material_id' => $pm->raw_material_id,
                'name'            => $rm->name ?? '-',
                'unit'            => $unit,
                'qty_per_unit'    => (float) $pm->qty_per_unit,
                'current_stock'   => (float) ($rm->current_stock ?? 0),
                'is_discrete'     => ! in_array($normalizedUnit, $continuousUnits, true),

                'usage_method'    => $pm->usage_method,
                'piece_length_cm' => $pm->piece_length_cm ? (float) $pm->piece_length_cm : null,
                'piece_width_cm'  => $pm->piece_width_cm ? (float) $pm->piece_width_cm : null,
                'piece_count'     => $pm->piece_count,
                'waste_percent'   => (float) $pm->waste_percent,
                'sheet_length_cm' => $rm->sheet_length_cm ? (float) $rm->sheet_length_cm : null,
                'sheet_width_cm'  => $rm->sheet_width_cm ? (float) $rm->sheet_width_cm : null,
            ];
        });

        return response()->json($boms);
    }

    public function execute(Request $request, ProductionRequest $productionRequest)
    {
        $data = $request->validate([
            'qty_produced_this_session' => ['required', 'integer', 'min:0'],
            'finish'                    => ['required', 'boolean'],
            'notes'                     => ['nullable', 'string'],
            'usages'                    => ['required', 'array'],
            'usages.*.raw_material_id'  => ['required', 'exists:raw_materials,id'],
            'usages.*.qty_used'         => ['required', 'numeric', 'min:0'],
        ]);

        if ($productionRequest->status === ProductionRequest::STATUS_COMPLETED) {
            return response()->json(['message' => 'Permintaan produksi ini sudah selesai.'], 422);
        }

        DB::transaction(function () use ($data, $request, $productionRequest) {

            $pr = ProductionRequest::where('id', $productionRequest->id)->lockForUpdate()->firstOrFail();

            $remaining = max(0, $pr->qty_requested - $pr->total_produced);

            if ($data['qty_produced_this_session'] > $remaining) {
                abort(422, 'Jumlah melebihi sisa target produksi (' . $remaining . ' pcs).');
            }

            // 1. Catat sesi produksi
            $log = ProductionLog::create([
                'production_request_id' => $pr->id,
                'recorded_by'           => $request->user()->id,
                'production_date'       => now()->toDateString(),
                'qty_produced'          => $data['qty_produced_this_session'],
                'notes'                 => $data['notes'] ?? null,
            ]);

            // 2. Catat pemakaian bahan baku + kurangi stok
            foreach ($data['usages'] as $usage) {

                $material = RawMaterial::where('id', $usage['raw_material_id'])->lockForUpdate()->firstOrFail();
                $qtyUsed  = (float) $usage['qty_used'];


                $logMaterial = ProductionLogMaterial::create([
                    'production_log_id'     => $log->id,
                    'production_request_id' => $pr->id,
                    'raw_material_id'       => $material->id,
                    'usage_date'            => now()->toDateString(),
                    'qty_used'              => $qtyUsed,
                ]);

                if ($qtyUsed > 0) {
                    $qtyBefore = $material->current_stock;
                    $qtyAfter  = $qtyBefore - $qtyUsed;

                    $material->update(['current_stock' => $qtyAfter]);

                    RawMaterialMovement::create([
                        'raw_material_id'             => $material->id,
                        'created_by'                  => $request->user()->id,
                        'movement_type'                => 'OUT',
                        'qty'                          => $qtyUsed,
                        'qty_before'                   => $qtyBefore,
                        'qty_after'                    => $qtyAfter,
                        'note'                         => 'Pemakaian produksi ' . $pr->request_no,
                        'production_log_material_id'  => $logMaterial->id,
                    ]);
                }
            }

            // 3. Update status & timestamp production_request
            if (! $pr->started_at) {
                $pr->started_at = now();
            }

            $pr->qty_produced = $pr->total_produced; // sinkron ulang dari logs
            $pr->status = $data['finish'] ? ProductionRequest::STATUS_COMPLETED : ProductionRequest::STATUS_IN_PROCESS;

            if ($data['finish']) {
                $pr->completed_at = now();
            }

            $pr->save();

            // 4. Efek balik stok: HANYA untuk MTS (RESTOCK atau ORDER-MTS), dan HANYA kalau ada output sesi ini
            $isMto = $pr->source === ProductionRequest::SOURCE_ORDER
                && $pr->orderItem
                && $pr->orderItem->production_method === 'MTO';

            if (! $isMto && $data['qty_produced_this_session'] > 0) {

                $product = Product::where('id', $pr->product_id)->lockForUpdate()->firstOrFail();

                $qtyBefore = $product->current_stock;
                $qtyAfter  = $qtyBefore + $data['qty_produced_this_session'];

                $product->update(['current_stock' => $qtyAfter]);

                ProductStockMovement::create([
                    'product_id'         => $product->id,
                    'created_by'         => $request->user()->id,
                    'movement_type'      => 'IN',
                    'qty'                => $data['qty_produced_this_session'],
                    'qty_before'         => $qtyBefore,
                    'qty_after'          => $qtyAfter,
                    'note'               => 'Hasil produksi ' . $pr->request_no,
                    'production_log_id'  => $log->id,
                ]);
            }

            // 5. Sambungkan balik ke Order (kalau source ORDER)
            if ($pr->source === ProductionRequest::SOURCE_ORDER && $pr->order_item_id) {

                if ($isMto) {
                    // MTO: readiness baru dicek kalau request ini sudah COMPLETED
                    if ($data['finish']) {
                        $this->orderStockService->checkOrderReadiness($pr->orderItem->order);
                    }
                } else {
                    // MTS: stok baru saja nambah, coba alokasikan ulang + cek readiness
                    $this->orderStockService->retryAllocationAfterProduction($pr->fresh());
                }
            }
        });

        return response()->json(['success' => true]);
    }

    public function shortage(Request $request, ProductionRequest $productionRequest)
    {
        $data = $request->validate([
            'items'                      => ['required', 'array', 'min:1'],
            'items.*.raw_material_id'    => ['required', 'exists:raw_materials,id'],
            'items.*.qty_needed'         => ['required', 'numeric', 'min:0'],
            'items.*.qty_available'      => ['required', 'numeric', 'min:0'],
        ]);

        foreach ($data['items'] as $item) {

            // Hindari duplikat: kalau sudah ada shortage OPEN untuk bahan ini
            // pada request yang sama, tidak usah bikin baris baru.
            $exists = MaterialShortage::where('production_request_id', $productionRequest->id)
                ->where('raw_material_id', $item['raw_material_id'])
                ->where('status', MaterialShortage::STATUS_OPEN)
                ->exists();

            if ($exists) {
                continue;
            }

            MaterialShortage::create([
                'production_request_id' => $productionRequest->id,
                'raw_material_id'       => $item['raw_material_id'],
                'qty_needed'            => $item['qty_needed'],
                'qty_available'         => $item['qty_available'],
                'status'                => MaterialShortage::STATUS_OPEN,
                'reported_at'           => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function start(ProductionRequest $productionRequest)
    {
        if ($productionRequest->status !== ProductionRequest::STATUS_WAITING) {
            return response()->json(['message' => 'Permintaan ini sudah tidak dalam status WAITING.'], 422);
        }

        $productionRequest->update([
            'status'     => ProductionRequest::STATUS_IN_PROCESS,
            'started_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }
}
