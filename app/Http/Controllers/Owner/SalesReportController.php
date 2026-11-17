<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class SalesReportController extends Controller
{
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

        $orders = Order::with(['platform', 'items.product.group'])
            ->where('status', Order::STATUS_COMPLETED)
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

            return [
                'id'       => $order->platform_order_no,
                'date'     => $order->order_date->format('Y-m-d'),
                'product'  => $items->pluck('name')->implode(', '),
                'platform' => $order->platform?->badge_class === 'tiktok' ? 'TikTok' : 'Shopee',
                'quantity' => $items->sum('quantity'),
                'total'    => (float) $order->total_amount,
                'status'   => 'Completed',
                'items'    => $items,
            ];
        });

        return response()->json($transactions);
    }
}