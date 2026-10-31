<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\RawMaterial;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MaterialShortage;
use App\Models\PurchaseOrderItem;
use App\Models\RawMaterialMovement;

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

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'creator', 'items.rawMaterial']);

        return view('admin.procurement.show', compact('purchaseOrder'));
    }

    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === PurchaseOrder::STATUS_RECEIVED) {
            return redirect()
                ->route('admin.procurement.show', $purchaseOrder->po_no)
                ->with('error', 'Purchase order ini sudah selesai diterima.');
        }

        $data = $request->validate([
            'items'                => ['required', 'array'],
            'items.*.item_id'      => ['required', 'exists:purchase_order_items,id'],
            'items.*.qty_received' => ['required', 'numeric', 'min:0.001'],
        ]);

        DB::transaction(function () use ($data, $purchaseOrder, $request) {

            foreach ($data['items'] as $row) {

                $item = PurchaseOrderItem::where('id', $row['item_id'])
                    ->where('purchase_order_id', $purchaseOrder->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $remaining = $item->qty_ordered - $item->qty_received;
                $qtyIn     = min((float) $row['qty_received'], $remaining);

                if ($qtyIn <= 0) {
                    continue;
                }

                $material = RawMaterial::where('id', $item->raw_material_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $qtyBefore = $material->current_stock;
                $qtyAfter  = $qtyBefore + $qtyIn;

                $material->update(['current_stock' => $qtyAfter]);

                RawMaterialMovement::create([
                    'raw_material_id'           => $material->id,
                    'created_by'                => $request->user()->id,
                    'movement_type'             => 'IN',
                    'qty'                       => $qtyIn,
                    'qty_before'                => $qtyBefore,
                    'qty_after'                 => $qtyAfter,
                    'note'                      => 'Penerimaan PO ' . $purchaseOrder->po_no,
                    'purchase_order_item_id'    => $item->id,
                ]);

                $item->update(['qty_received' => $item->qty_received + $qtyIn]);

                // Resolve shortage terkait kalau stok sudah cukup lagi
                if ($item->material_shortage_id) {
                    $shortage = MaterialShortage::find($item->material_shortage_id);

                    if (
                        $shortage && $shortage->status === MaterialShortage::STATUS_OPEN
                        && $qtyAfter >= $shortage->qty_needed
                    ) {
                        $shortage->update([
                            'status'      => MaterialShortage::STATUS_RESOLVED,
                            'resolved_at' => now(),
                        ]);
                    }
                }
            }

            $purchaseOrder->refresh()->load('items');

            $allReceived = $purchaseOrder->items->every(
                fn($i) => $i->qty_received >= $i->qty_ordered
            );

            $anyReceived = $purchaseOrder->items->contains(fn ($i) => $i->qty_received > 0);

            $purchaseOrder->update([
                'status' => $allReceived
                    ? PurchaseOrder::STATUS_RECEIVED
                    : ($anyReceived ? PurchaseOrder::STATUS_PARTIALLY_RECEIVED : PurchaseOrder::STATUS_ORDERED),
            ]);
        });

        return redirect()
            ->route('admin.procurement.show', $purchaseOrder->po_no)
            ->with('success', 'Penerimaan barang berhasil dicatat dan stok telah diperbarui.');
    }
}
