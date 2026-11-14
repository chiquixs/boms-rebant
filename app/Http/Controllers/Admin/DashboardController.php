<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\RawMaterial;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    private const ORDER_STATUS = [
        'NEW'                => ['label' => 'New',                'class' => 'new'],
        'WAITING_PRODUCTION' => ['label' => 'Waiting Production', 'class' => 'waiting'],
        'READY_TO_PACK'      => ['label' => 'Ready to Pack',      'class' => 'ready'],
        'COMPLETED'          => ['label' => 'Completed',          'class' => 'completed'],
    ];

    public function index()
    {
        // ---------- Kartu ringkasan ----------
        $summary = [
            'new_orders'         => Order::where('status', 'NEW')->count(),
            'waiting_production' => Order::where('status', 'WAITING_PRODUCTION')->count(),
            'low_variants'       => $this->lowVariantQuery()->count(),
            'restock'            => $this->restockQuery()->count(),
        ];

        // ---------- Pesanan terbaru (yang belum selesai) ----------
        $recentOrders = Order::with('platform')
            ->where('status', '!=', 'COMPLETED')
            ->orderByDesc('order_date')
            ->orderByDesc('id')
            ->limit(5)
            ->get()
            ->map(function ($order) {
                $meta     = self::ORDER_STATUS[$order->status] ?? ['label' => $order->status, 'class' => 'new'];
                $platform = $order->platform?->name ?? '-';
                $slug     = Str::slug($platform);

                return [
                    'url'            => route('admin.orders.show', $order),
                    'order_no'       => $order->platform_order_no,
                    'platform'       => $platform,
                    'platform_class' => str_contains($slug, 'tiktok') ? 'tiktok'
                        : (str_contains($slug, 'shopee') ? 'shopee' : 'other'),
                    'total'          => $this->rupiah($order->total_amount),
                    'status_label'   => $meta['label'],
                    'status_class'   => $meta['class'],
                ];
            });

        // ---------- Peringatan stok: varian MTS + bahan mentah ----------
        $variantAlerts = $this->lowVariantQuery()
            ->with('group')
            ->orderByRaw('current_stock::numeric / min_stock')
            ->orderBy('sku')
            ->limit(5)
            ->get()
            ->map(fn ($p) => [
                'kind'      => 'product',
                'name'      => ($p->group?->name ?? '-') . ($p->variant_name ? ' (' . $p->variant_name . ')' : ''),
                'sub'       => $p->sku . ' · Produk Jadi',
                'current'   => $this->num($p->current_stock) . ' pcs',
                'threshold' => 'Min. ' . $this->num($p->min_stock) . ' pcs',
                'ratio'     => $p->min_stock > 0 ? $p->current_stock / $p->min_stock : 0,
            ]);

        $materialAlerts = $this->restockQuery()
            ->orderByRaw('current_stock / safety_stock')
            ->limit(5)
            ->get()
            ->map(fn ($m) => [
                'kind'      => 'material',
                'name'      => $m->name,
                'sub'       => 'Bahan Mentah · ' . ($m->material_type === 'SHEET' ? 'SHEET' : 'NON-SHEET'),
                'current'   => $this->num($m->current_stock) . ' ' . $m->unit,
                'threshold' => 'Safety ' . $this->num($m->safety_stock) . ' ' . $m->unit,
                'ratio'     => (float) $m->safety_stock > 0 ? (float) $m->current_stock / (float) $m->safety_stock : 0,
            ]);

        // Yang paling parah (rasio terkecil) di atas
        $stockAlerts = $variantAlerts->concat($materialAlerts)
            ->sortBy('ratio')
            ->take(5)
            ->values();

        // ---------- Procurement: rekomendasi restock + PO terbaru ----------
        $restockItems = $this->restockQuery()
            ->orderByRaw('current_stock / safety_stock')
            ->limit(2)
            ->get()
            ->map(fn ($m) => [
                'name'      => $m->name,
                'detail'    => 'Stok ' . $this->num($m->current_stock) . ' ' . $m->unit
                    . ' · Safety ' . $this->num($m->safety_stock) . ' ' . $m->unit
                    . ' · Target ' . $this->num($m->target_stock) . ' ' . $m->unit,
                'recommend' => '+' . $this->num(max($m->target_stock - $m->current_stock, 0)) . ' ' . $m->unit,
            ]);

        $recentPos = PurchaseOrder::with(['supplier', 'items'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(3)
            ->get()
            ->map(function ($po) {
                $total = $po->items->sum(fn ($i) => (float) $i->qty_ordered * (float) $i->unit_price);

                return [
                    'url'          => route('admin.procurement.show', $po->po_no),
                    'po_no'        => $po->po_no,
                    'detail'       => ($po->supplier?->name ?? '-') . ' · ' . $this->rupiah($total),
                    'status_label' => $po->status_label,
                    'status_class' => $po->status_class,
                ];
            });

        // ---------- Aktivitas terbaru ----------
        $activities = ActivityLog::with('user')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get()
            ->map(fn ($log) => [
                'kind'  => $this->activityKind((string) $log->entity_type),
                'title' => Str::headline(strtolower((string) $log->action)),
                'text'  => $log->description
                    ?: (($log->user?->name ?? 'Sistem') . ' · ' . $log->entity_type),
                'time'  => $log->created_at->isToday()
                    ? $log->created_at->format('H:i')
                    : $log->created_at->translatedFormat('d M, H:i'),
            ]);

        return view('admin.dashboard.index', compact(
            'summary',
            'recentOrders',
            'stockAlerts',
            'restockItems',
            'recentPos',
            'activities'
        ));
    }

    /** Varian aktif dari produk MTS yang stoknya di bawah minimum. */
    private function lowVariantQuery()
    {
        return Product::query()
            ->where('is_active', true)
            ->whereNotNull('min_stock')
            ->whereColumn('current_stock', '<', 'min_stock')
            ->whereHas('group', fn ($q) => $q
                ->where('production_method', 'MTS')
                ->where('is_active', true));
    }

    /** Bahan mentah aktif di bawah safety stock (sama dengan halaman Procurement). */
    private function restockQuery()
    {
        return RawMaterial::query()
            ->where('is_active', true)
            ->whereColumn('current_stock', '<', 'safety_stock');
    }

    private function activityKind(string $entity): string
    {
        $e = strtolower($entity);

        return match (true) {
            Str::contains($e, ['purchase', 'procurement', 'supplier']) => 'procurement',
            Str::contains($e, ['production'])                          => 'production',
            Str::contains($e, ['product'])                             => 'product',
            Str::contains($e, ['material', 'inventory', 'stock'])      => 'inventory',
            default                                                    => 'default',
        };
    }

    private function num($value): string
    {
        return rtrim(rtrim(number_format((float) $value, 3, ',', '.'), '0'), ',');
    }

    private function rupiah($value): string
    {
        return 'Rp' . number_format((float) $value, 0, ',', '.');
    }
}