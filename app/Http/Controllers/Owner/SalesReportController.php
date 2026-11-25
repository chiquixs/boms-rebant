<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class SalesReportController extends Controller
{
    private const STATUS_META = [
        'NEW'                => ['label' => 'New',                'class' => 'new'],
        'WAITING_PRODUCTION' => ['label' => 'Waiting Production', 'class' => 'waiting'],
        'READY_TO_PACK'      => ['label' => 'Ready to Pack',      'class' => 'ready'],
        'COMPLETED'          => ['label' => 'Completed',          'class' => 'completed'],
    ];

    public function page()
    {
        return view('owner.sales-report.index'); // sesuaikan dengan lokasi view Anda
    }

    public function data(Request $request)
    {
        $range = $request->validate([
            'start' => ['required', 'date'],
            'end'   => ['required', 'date', 'after_or_equal:start'],
        ]);

        // Semua status ikut dikirim. Penyaringan "completed" untuk omzet dilakukan di JS.
        $orders = Order::with(['platform', 'items.product.group'])
            ->whereBetween('order_date', [$range['start'], $range['end']])
            ->orderBy('order_date')
            ->orderBy('id')
            ->get();

        $transactions = $orders->map(function ($order) {

            $items = $order->items->map(function ($item) {
                $group   = $item->product?->group?->name ?? 'Produk dihapus';
                $variant = $item->product?->variant_name;

                return [
                    'name'     => $variant ? "$group ($variant)" : $group,
                    'quantity' => (int) $item->qty,
                    'total'    => (float) $item->subtotal,
                ];
            })->values();

            $meta = self::STATUS_META[$order->status]
                ?? ['label' => $order->status, 'class' => 'new'];

            return [
                'key'          => $order->id, // unik, karena nomor order bisa kembar antar platform
                'id'           => $order->platform_order_no,
                'date'         => $order->order_date->format('Y-m-d'),
                'product'      => $items->pluck('name')->implode(', '),
                'platform'     => $order->platform?->name ?? '-',
                'quantity'     => $items->sum('quantity'),
                'total'        => (float) $order->total_amount,
                'status'       => $meta['label'],
                'status_code'  => $order->status,
                'status_class' => $meta['class'],
                'items'        => $items,
            ];
        });

        return response()->json($transactions);
    }
}