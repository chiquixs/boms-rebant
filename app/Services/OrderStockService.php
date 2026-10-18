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
    /**
     * Dipanggil sekali, langsung setelah order + order_items dibuat.
     * HARUS dipanggil di dalam DB::transaction() yang sama dengan pembuatan order.
     */
    public function allocateForNewOrder(Order $order): void
    {
        $order->load('items');

        $allReady = true;

        foreach ($order->items as $item) {
            $needsProduction = $this->allocateItem($item, $order);

            if ($needsProduction) {
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
     * Return true jika item ini masih butuh produksi (belum bisa dialokasikan penuh).
     */
    private function allocateItem(OrderItem $item, Order $order): bool
    {
        if ($item->production_method === 'MTO') {
            $this->createProductionRequest($item, $order, $item->qty);
            return true;
        }

        // MTS — lock baris produk, supaya aman dari order lain yang diproses bersamaan
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

            return false;
        }

        // Stok tidak cukup -> minta produksi untuk kekurangannya saja
        $shortage = $item->qty - max($available, 0);

        $this->createProductionRequest($item, $order, $shortage);

        return true;

        // TODO (modul Produksi): setelah production_request source=ORDER ini
        // COMPLETED dan menambah stok produk, panggil ulang allocateItem()
        // untuk item ini, lalu cek apakah seluruh order sudah bisa markReadyToPack().
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
     * Konsumsi semua alokasi RESERVED milik order ini: kurangi stok produk jadi
     * tepat satu kali, catat movement OUT, lalu set order ke READY_TO_PACK.
     */
    public function markReadyToPack(Order $order): void
    {
        $order->load('items.allocation');

        foreach ($order->items as $item) {
            $allocation = $item->allocation;

            if (! $allocation || $allocation->status !== OrderItemAllocation::STATUS_RESERVED) {
                continue; // item MTO, atau belum teralokasi -> tidak kurangi stok produk jadi
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