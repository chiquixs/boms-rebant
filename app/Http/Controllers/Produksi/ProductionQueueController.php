<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use App\Models\MaterialShortage;
use App\Models\Product;
use App\Models\ProductionLog;
use App\Models\ProductionLogMaterial;
use App\Models\ProductionRequest;
use App\Models\ProductStockMovement;
use App\Models\RawMaterial;
use App\Models\RawMaterialMovement;
use App\Services\OrderStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductionQueueController extends Controller
{
    private const CONTINUOUS_UNITS = ['ml', 'lt', 'kg', 'gr', 'mm', 'cm', 'm'];

    public function __construct(
        private readonly OrderStockService $orderStockService
    ) {}

    public function index()
    {
        $requests = ProductionRequest::with(['product.group.category', 'orderItem'])
            ->get()
            ->map(fn ($r) => $this->format($r))
            ->sortBy([
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
            'id' => $r->id,
            'request_no' => $r->request_no,
            'source' => $r->source,
            'product_name' => $group->name ?? '-',
            'variant_name' => $product->variant_name ?? null,
            'sku' => $product->sku ?? '-',
            'image_url' => $product->image_url ?? $group->image_url ?? null,
            'qty_requested' => $r->qty_requested,
            'qty_produced' => $r->total_produced,
            'status' => $r->status,
            'created_at' => $r->created_at->toIso8601String(),

            'product_group' => $group ? [
                'name' => $group->name,
                'code' => $group->code,
                'description' => $group->description,
                'production_method' => $group->production_method,
                'category_name' => $group->category->name ?? null,
                'length_cm' => $group->length_cm,
                'width_cm' => $group->width_cm,
                'price' => $group->price,
                'image_url' => $group->image_url,
            ] : null,
        ];
    }

    private function isDiscreteMaterial(?RawMaterial $material): bool
    {
        $normalizedUnit = strtolower(trim($material?->unit ?? '-'));

        return ! in_array($normalizedUnit, self::CONTINUOUS_UNITS, true);
    }

    public function materials(ProductionRequest $productionRequest)
    {
        $product = $productionRequest->product()->with('group.materials.rawMaterial')->firstOrFail();

        $boms = $product->group->materials->map(function ($pm) {
            $rm = $pm->rawMaterial;
            $unit = $rm->unit ?? '-';

            return [
                'raw_material_id' => $pm->raw_material_id,
                'name' => $rm->name ?? '-',
                'unit' => $unit,
                'qty_per_unit' => (float) $pm->qty_per_unit,
                'current_stock' => (float) ($rm->current_stock ?? 0),
                'is_discrete' => $this->isDiscreteMaterial($rm),

                'usage_method' => $pm->usage_method,
                'piece_length_cm' => $pm->piece_length_cm ? (float) $pm->piece_length_cm : null,
                'piece_width_cm' => $pm->piece_width_cm ? (float) $pm->piece_width_cm : null,
                'piece_count' => $pm->piece_count,
                'waste_percent' => (float) $pm->waste_percent,
                'sheet_length_cm' => $rm->sheet_length_cm ? (float) $rm->sheet_length_cm : null,
                'sheet_width_cm' => $rm->sheet_width_cm ? (float) $rm->sheet_width_cm : null,
            ];
        });

        return response()->json($boms);
    }

    public function execute(Request $request, ProductionRequest $productionRequest)
    {
        $data = $request->validate([
            'qty_produced_this_session' => ['required', 'integer', 'min:0'],
            'finish' => ['required', 'boolean'],
            'notes' => ['nullable', 'string'],
            'usages' => ['present', 'array'],
            'usages.*.raw_material_id' => ['required', 'distinct', 'exists:raw_materials,id'],
            'usages.*.qty_used' => ['required', 'numeric', 'min:0'],
        ]);

        if ($productionRequest->status === ProductionRequest::STATUS_COMPLETED) {
            return response()->json(['message' => 'Permintaan produksi ini sudah selesai.'], 422);
        }

        DB::transaction(function () use ($data, $request, $productionRequest): void {

            $pr = ProductionRequest::where('id', $productionRequest->id)->lockForUpdate()->firstOrFail();

            if ($pr->status === ProductionRequest::STATUS_COMPLETED) {
                throw ValidationException::withMessages([
                    'request_no' => 'Permintaan produksi ini sudah selesai.',
                ]);
            }

            $alreadyProduced = $pr->total_produced;
            $qtyThisSession = (int) $data['qty_produced_this_session'];
            $remaining = max(0, $pr->qty_requested - $alreadyProduced);

            if ($qtyThisSession > $remaining) {
                throw ValidationException::withMessages([
                    'qty_produced_this_session' => 'Jumlah melebihi sisa target produksi ('.$remaining.' pcs).',
                ]);
            }

            if (! $data['finish'] && $qtyThisSession <= 0) {
                throw ValidationException::withMessages([
                    'qty_produced_this_session' => 'Isi hasil produksi minimal 1 pcs untuk menyimpan progres.',
                ]);
            }

            $newTotalProduced = $alreadyProduced + $qtyThisSession;

            if ($data['finish'] && $newTotalProduced < $pr->qty_requested) {
                throw ValidationException::withMessages([
                    'qty_produced_this_session' => 'Total hasil produksi ('.$newTotalProduced.' pcs) kurang dari jumlah yang diminta ('.$pr->qty_requested.' pcs). Gunakan Simpan Progres atau lengkapi sisa target ('.$remaining.' pcs).',
                ]);
            }

            $usages = collect($data['usages'])
                ->sortBy(fn (array $usage): int => (int) $usage['raw_material_id'])
                ->values();

            foreach ($usages as $usage) {
                $rawQty = (float) $usage['qty_used'];
                $roundedQty = round($rawQty, 3);

                if ($rawQty > 0 && $roundedQty <= 0) {
                    throw ValidationException::withMessages([
                        'usages' => 'Jumlah pemakaian bahan baku minimal 0.001.',
                    ]);
                }
            }

            $hasPositiveUsage = $usages->contains(
                fn (array $usage): bool => round((float) $usage['qty_used'], 3) > 0
            );

            if ($qtyThisSession === 0 && $hasPositiveUsage) {
                throw ValidationException::withMessages([
                    'qty_produced_this_session' => 'Pemakaian bahan baku harus disertai minimal 1 pcs hasil produksi pada sesi ini.',
                ]);
            }

            $bomMaterialIds = $pr->product()
                ->with('group.materials')
                ->firstOrFail()
                ->group
                ?->materials
                ?->pluck('raw_material_id')
                ?->map(fn ($id): int => (int) $id)
                ?? collect();

            if ($bomMaterialIds->isNotEmpty()) {
                $hasInvalidMaterial = $usages->contains(
                    fn (array $usage): bool => ! $bomMaterialIds->contains((int) $usage['raw_material_id'])
                );

                if ($hasInvalidMaterial) {
                    throw ValidationException::withMessages([
                        'usages' => 'Ada bahan baku yang tidak terdaftar pada BOM produk ini. Muat ulang halaman lalu coba lagi.',
                    ]);
                }

                if ($qtyThisSession > 0 && ! $hasPositiveUsage) {
                    $hasPriorUsage = ProductionLogMaterial::where('production_request_id', $pr->id)->exists();

                    if (! $hasPriorUsage) {
                        throw ValidationException::withMessages([
                            'usages' => 'Minimal satu bahan baku harus memiliki jumlah pemakaian lebih dari 0.',
                        ]);
                    }
                }
            }

            $log = null;

            // 1. Catat sesi produksi + pemakaian bahan baku hanya jika sesi ini menghasilkan >= 1 pcs
            if ($qtyThisSession > 0) {
                $log = ProductionLog::create([
                    'production_request_id' => $pr->id,
                    'recorded_by' => $request->user()->id,
                    'production_date' => now()->toDateString(),
                    'qty_produced' => $qtyThisSession,
                    'notes' => $data['notes'] ?? null,
                ]);

                // 2. Catat pemakaian bahan baku + kurangi stok (terurut per raw_material_id untuk mencegah deadlock)
                foreach ($usages as $usage) {
                    $rawQty = (float) $usage['qty_used'];
                    $qtyUsed = round($rawQty, 3);

                    if ($qtyUsed <= 0) {
                        continue;
                    }

                    $material = RawMaterial::where('id', $usage['raw_material_id'])->lockForUpdate()->firstOrFail();

                    if ($this->isDiscreteMaterial($material) && abs($rawQty - round($rawQty)) > 0.000001) {
                        throw ValidationException::withMessages([
                            'usages' => 'Pemakaian '.$material->name.' ('.$material->unit.') harus berupa bilangan bulat.',
                        ]);
                    }

                    $qtyBefore = round((float) $material->current_stock, 3);

                    if ($qtyUsed > $qtyBefore) {
                        throw ValidationException::withMessages([
                            'usages' => 'Pemakaian '.$material->name.' melebihi stok tersedia ('.$qtyBefore.' '.$material->unit.').',
                        ]);
                    }

                    $logMaterial = ProductionLogMaterial::create([
                        'production_log_id' => $log->id,
                        'production_request_id' => $pr->id,
                        'raw_material_id' => $material->id,
                        'usage_date' => now()->toDateString(),
                        'qty_used' => $qtyUsed,
                    ]);

                    $qtyAfter = max(0.0, round($qtyBefore - $qtyUsed, 3));

                    $material->update(['current_stock' => $qtyAfter]);

                    RawMaterialMovement::create([
                        'raw_material_id' => $material->id,
                        'created_by' => $request->user()->id,
                        'movement_type' => 'OUT',
                        'qty' => $qtyUsed,
                        'qty_before' => $qtyBefore,
                        'qty_after' => $qtyAfter,
                        'note' => 'Pemakaian produksi '.$pr->request_no,
                        'production_log_material_id' => $logMaterial->id,
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

            if (! $isMto && $log && $qtyThisSession > 0) {

                $product = Product::where('id', $pr->product_id)->lockForUpdate()->firstOrFail();

                $qtyBefore = $product->current_stock;
                $qtyAfter = $qtyBefore + $qtyThisSession;

                $product->update(['current_stock' => $qtyAfter]);

                ProductStockMovement::create([
                    'product_id' => $product->id,
                    'created_by' => $request->user()->id,
                    'movement_type' => 'IN',
                    'qty' => $qtyThisSession,
                    'qty_before' => $qtyBefore,
                    'qty_after' => $qtyAfter,
                    'note' => 'Hasil produksi '.$pr->request_no,
                    'production_log_id' => $log->id,
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
            'items' => ['required', 'array', 'min:1'],
            'items.*.raw_material_id' => ['required', 'exists:raw_materials,id'],
            'items.*.qty_needed' => ['required', 'numeric', 'min:0'],
            'items.*.qty_available' => ['required', 'numeric', 'min:0'],
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
                'raw_material_id' => $item['raw_material_id'],
                'qty_needed' => $item['qty_needed'],
                'qty_available' => $item['qty_available'],
                'status' => MaterialShortage::STATUS_OPEN,
                'reported_at' => now(),
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
            'status' => ProductionRequest::STATUS_IN_PROCESS,
            'started_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }
}
