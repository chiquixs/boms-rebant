<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaterialShortage;
use App\Models\RawMaterial;
use App\Models\Supplier;
use App\Models\SupplierMaterial;
use App\Models\PurchaseOrderItem;

class MaterialShortageController extends Controller
{
    // Render halaman kosong, data diisi lewat JS (fetch ke data())
    public function page()
    {
        return view('admin.procurement.shortage');
    }

    public function data()
    {
        $shortages = MaterialShortage::with(['productionRequest', 'rawMaterial'])
            ->orderByRaw("status = 'OPEN' DESC")
            ->orderByDesc('reported_at')
            ->get()
            ->map(fn($s) => $this->format($s));

        return response()->json($shortages);
    }

    public function show(MaterialShortage $materialShortage)
    {
        $materialShortage->load(['productionRequest', 'rawMaterial']);

        return response()->json($this->format($materialShortage));
    }

    public function materials()
    {
        return response()->json(
            RawMaterial::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'code', 'name', 'unit'])
        );
    }

    public function suppliers()
    {
        return response()->json(
            Supplier::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name'])
        );
    }

    private function format(MaterialShortage $s): array
    {
        // Cari PO yang masih berjalan (belum fully received) untuk shortage ini
        $existingPo = PurchaseOrderItem::with('purchaseOrder')
            ->where('material_shortage_id', $s->id)
            ->whereHas(
                'purchaseOrder',
                fn($q) =>
                $q->whereIn('status', [
                    \App\Models\PurchaseOrder::STATUS_ORDERED,
                    \App\Models\PurchaseOrder::STATUS_PARTIALLY_RECEIVED,
                ])
            )
            ->get()
            ->map(fn($item) => [
                'po_no'  => $item->purchaseOrder->po_no,
                'status' => $item->purchaseOrder->status,
            ])
            ->unique('po_no')
            ->values();

        return [
            'id'                     => $s->id,
            'production_request_id'  => $s->production_request_id,
            'raw_material_id'        => $s->raw_material_id,
            'qty_needed'             => (float) $s->qty_needed,
            'qty_available'          => (float) $s->qty_available,
            'status'                 => $s->status,
            'reported_at'            => $s->reported_at?->toIso8601String(),
            'resolved_at'            => $s->resolved_at?->toIso8601String(),
            'existing_po'            => $existingPo,   
            'production_request'     => $s->productionRequest ? [
                'id'         => $s->productionRequest->id,
                'request_no' => $s->productionRequest->request_no,
            ] : null,
            'raw_material' => $s->rawMaterial ? [
                'id'             => $s->rawMaterial->id,
                'code'           => $s->rawMaterial->code,
                'name'           => $s->rawMaterial->name,
                'unit'           => $s->rawMaterial->unit,
                'current_stock'  => (float) $s->rawMaterial->current_stock,
                'safety_stock'   => (float) $s->rawMaterial->safety_stock,
                'reorder_point'  => (float) $s->rawMaterial->reorder_point,
                'is_active'      => $s->rawMaterial->is_active,
            ] : null,
        ];
    }

    public function supplierMaterials()
    {
        return response()->json(
            SupplierMaterial::with('supplier')
                ->whereHas('supplier', fn($q) => $q->where('is_active', true))
                ->get()
                ->map(fn($sm) => [
                    'raw_material_id' => $sm->raw_material_id,
                    'supplier_id'     => $sm->supplier_id,
                    'supplier_name'   => $sm->supplier->name,
                ])
        );
    }
}
