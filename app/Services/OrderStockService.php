<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemAllocation;
use App\Models\Product;
use App\Models\ProductionRequest;
use App\Models\ProductStockMovement;

class OrderStockService
{
    public function allocateForNewOrder(Order $order): void
    {
        $order->load('items');

        $allReady = true;

        foreach ($order->items as $item) {
            if (! $this->tryAllocateItem($item, $order)) {
                $allReady = false;
            }
        }

        if ($allReady) {
            $this->markReadyToPack($order);
        } else {
            $order->update(['status' => Order::STATUS_WAITING_PRODUCTION]);
        }
    }

    /**
     * Coba alokasikan 1 item MTS. Return true jika SIAP (sudah teralokasi penuh),
     * false jika masih butuh produksi (production_request dibuat/sudah ada).
     */
    public function tryAllocateItem(OrderItem $item, Order $order): bool
    {
        if ($item->production_method === 'MTO') {
            // MTO tidak pernah lewat stok produk jadi. "Siap" ditentukan oleh
            // production_request yang terkait, bukan di sini.
            if (! $item->productionRequest) {
                $this->createProductionRequest($item, $order, $item->qty);
            }

            return false;
        }

        // Sudah teralokasi sebelumnya? Tidak perlu alokasi ulang.
        if ($item->allocation && $item->allocation->status === OrderItemAllocation::STATUS_RESERVED) {
            return true;
        }

        $product = Product::where('id', $item->product_id)->lockForUpdate()->firstOrFail();

        $reserved = OrderItemAllocation::where('product_id', $product->id)
            ->where('status', OrderItemAllocation::STATUS_RESERVED)
            ->sum('qty_allocated');

        $available = $product->current_stock - $reserved;

        if ($available >= $item->qty) {
            OrderItemAllocation::create([
                'order_item_id' => $item->id,
                'product_id'    => $product->id,
                'qty_allocated' => $item->qty,
                'status'        => OrderItemAllocation::STATUS_RESERVED,
            ]);

            return true;
        }

        // Stok masih kurang. Buat production_request untuk kekurangannya
        // kalau belum ada request aktif untuk item ini.
        if (! $item->productionRequest) {
            $shortage = $item->qty - max($available, 0);
            $this->createProductionRequest($item, $order, $shortage);
        }

        return false;
    }

    private function createProductionRequest(OrderItem $item, Order $order, int $qty): void
    {
        ProductionRequest::create([
            'product_id'    => $item->product_id,
            'order_item_id' => $item->id,
            'requested_by'  => $order->created_by,
            'request_no'    => $this->generateRequestNo(),
            'source'        => ProductionRequest::SOURCE_ORDER,
            'qty_requested' => $qty,
            'qty_produced'  => 0,
            'status'        => ProductionRequest::STATUS_WAITING,
        ]);
    }

    private function generateRequestNo(): string
    {
        $last = ProductionRequest::where('request_no', 'like', 'PR-%')
            ->orderByDesc('request_no')
            ->value('request_no');

        $next = $last ? ((int) substr($last, 3)) + 1 : 1;

        return 'PR-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Dipanggil setelah sesi produksi MTS (RESTOCK atau ORDER-MTS) menambah
     * stok produk. Coba alokasikan ulang item order terkait, lalu cek
     * apakah seluruh order sudah siap READY_TO_PACK.
     */
    public function retryAllocationAfterProduction(ProductionRequest $pr): void
    {
        if (! $pr->order_item_id) {
            return; // RESTOCK murni, tidak ada order terkait
        }

        $item = $pr->orderItem()->with('order')->first();
        if (! $item) {
            return;
        }

        $this->tryAllocateItem($item, $item->order);
        $this->checkOrderReadiness($item->order);
    }

    /**
     * Cek apakah SEMUA item dalam order sudah siap (MTS: punya allocation
     * RESERVED; MTO: production_request terkait sudah COMPLETED).
     * Kalau semua siap, pindahkan order ke READY_TO_PACK.
     */
    public function checkOrderReadiness(Order $order): void
    {
        if ($order->status === Order::STATUS_READY_TO_PACK || $order->status === Order::STATUS_COMPLETED) {
            return;
        }

        $order->load(['items.allocation', 'items.productionRequest']);

        $allReady = $order->items->every(function ($item) {
            if ($item->production_method === 'MTO') {
                return $item->productionRequest
                    && $item->productionRequest->status === ProductionRequest::STATUS_COMPLETED;
            }

            return $item->allocation && $item->allocation->status === OrderItemAllocation::STATUS_RESERVED;
        });

        if ($allReady) {
            $this->markReadyToPack($order);
        }
    }

    public function markReadyToPack(Order $order): void
    {
        $order->load('items.allocation');

        foreach ($order->items as $item) {

            $allocation = $item->allocation;

            // MTO tidak pernah punya allocation / stock movement produk jadi.
            // Cukup "diakui siap" lewat production_request COMPLETED, tidak ada aksi stok di sini.
            if (! $allocation || $allocation->status !== OrderItemAllocation::STATUS_RESERVED) {
                continue;
            }

            $product = Product::where('id', $allocation->product_id)->lockForUpdate()->firstOrFail();

            $qtyBefore = $product->current_stock;
            $qtyAfter  = $qtyBefore - $allocation->qty_allocated;

            $product->update(['current_stock' => $qtyAfter]);

            ProductStockMovement::create([
                'product_id'    => $product->id,
                'created_by'    => $order->created_by,
                'movement_type' => 'OUT',
                'qty'           => $allocation->qty_allocated,
                'qty_before'    => $qtyBefore,
                'qty_after'     => $qtyAfter,
                'note'          => 'Order siap dikemas',
                'order_item_id' => $item->id,
            ]);

            $allocation->update([
                'status'      => OrderItemAllocation::STATUS_CONSUMED,
                'consumed_at' => now(),
            ]);
        }

        $order->update([
            'status'   => Order::STATUS_READY_TO_PACK,
            'ready_at' => now(),
        ]);
    }
}