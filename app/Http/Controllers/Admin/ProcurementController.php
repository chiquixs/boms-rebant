<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\RawMaterial;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProcurementController extends Controller
{
    public function index()
    {
        $restockMaterials = RawMaterial::where('is_active', true)
            ->whereColumn('current_stock', '<', 'safety_stock')
            ->orderBy('name')
            ->get()
            ->map(function ($m) {
                $m->recommendation = max($m->target_stock - $m->current_stock, 0);
                return $m;
            });

        $purchaseOrders = PurchaseOrder::with(['supplier', 'items'])
            ->orderByDesc('created_at')
            ->get();

        $activePoCount      = $purchaseOrders->whereIn('status', [
            PurchaseOrder::STATUS_ORDERED,
            PurchaseOrder::STATUS_PARTIALLY_RECEIVED,
        ])->count();

        $waitingGoodsCount  = $purchaseOrders->where('status', PurchaseOrder::STATUS_ORDERED)->count();

        $receivedThisMonth  = PurchaseOrder::where('status', PurchaseOrder::STATUS_RECEIVED)
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

        // Supplier + harga per bahan, untuk diisi otomatis di modal Review
        $suppliersByMaterial = Supplier::where('is_active', true)
            ->with('materials')
            ->get()
            ->flatMap(function ($supplier) {
                return $supplier->materials->map(fn($sm) => [
                    'raw_material_id' => $sm->raw_material_id,
                    'supplier_id'     => $supplier->id,
                    'supplier_name'   => $supplier->name,
                    'supplier_email'  => $supplier->email,
                    'unit_price'      => (float) $sm->unit_price,
                    'lead_time_days'  => $sm->lead_time_days,
                    'min_order_qty'   => (float) $sm->min_order_qty,
                ]);
            })
            ->groupBy('raw_material_id');

        return view('admin.procurement.index', compact(
            'restockMaterials',
            'purchaseOrders',
            'activePoCount',
            'waitingGoodsCount',
            'receivedThisMonth',
            'suppliersByMaterial'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'raw_material_id' => ['required', 'exists:raw_materials,id'],
            'supplier_id'     => ['required', 'exists:suppliers,id'],
            'qty_ordered'     => ['required', 'numeric', 'gt:0'],
            'unit_price'      => ['required', 'numeric', 'min:0'],
            'notes'           => ['nullable', 'string'],
        ], [
            'required' => ':attribute wajib diisi.',
            'gt'       => ':attribute harus lebih dari 0.',
            'exists'   => ':attribute tidak valid.',
        ], [
            'raw_material_id' => 'Bahan baku',
            'supplier_id'     => 'Supplier',
            'qty_ordered'     => 'Jumlah pesan',
            'unit_price'      => 'Harga satuan',
        ]);

        $material = RawMaterial::findOrFail($data['raw_material_id']);
        $recommendation = max($material->target_stock - $material->current_stock, 0);

        DB::transaction(function () use ($data, $request, $recommendation) {

            $po = PurchaseOrder::create([
                'supplier_id'   => $data['supplier_id'],
                'created_by'    => $request->user()->id,
                'approved_by'   => $request->user()->id, // tidak ada approval terpisah, lihat catatan bisnis
                'po_no'         => $this->generatePoNo(),
                'trigger_type'  => PurchaseOrder::TRIGGER_MANUAL,
                'status'        => PurchaseOrder::STATUS_ORDERED,
                'approved_at'   => now(),
                'email_sent_at' => now(), // simulasi: dianggap langsung terkirim
                'notes'         => $data['notes'] ?? null,
            ]);

            $po->items()->create([
                'raw_material_id' => $data['raw_material_id'],
                'qty_suggested'   => $recommendation,
                'qty_ordered'     => $data['qty_ordered'],
                'qty_received'    => 0,
                'unit_price'      => $data['unit_price'],
            ]);
        });

        return redirect()
            ->route('admin.procurement.index')
            ->with('success', 'Purchase Order berhasil dibuat dan dikirim ke supplier.');
    }

    private function generatePoNo(): string
    {
        $last = PurchaseOrder::where('po_no', 'like', 'PO-%')
            ->orderByDesc('po_no')
            ->value('po_no');

        $next = $last ? ((int) substr($last, 3)) + 1 : 1;

        return 'PO-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
