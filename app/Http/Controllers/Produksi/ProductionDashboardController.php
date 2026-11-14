<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductionLog;
use App\Models\ProductionRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class ProductionDashboardController extends Controller
{
    private const LIST_LIMIT = 5;

    public function data(Request $request)
    {
        $period = in_array($request->query('period'), ['month', '7d', '30d'], true)
            ? $request->query('period')
            : 'month';

        [$start, $periodLabel] = match ($period) {
            '7d'    => [now()->subDays(6)->startOfDay(), '7 hari terakhir'],
            '30d'   => [now()->subDays(29)->startOfDay(), '30 hari terakhir'],
            default => [now()->startOfMonth(), 'Bulan ini'],
        };

        $limit = self::LIST_LIMIT;

        // ---------- Ringkasan ----------
        $activeQueue = ProductionRequest::whereIn('status', ['WAITING', 'IN_PROCESS'])->count();
        $inProcess   = ProductionRequest::where('status', 'IN_PROCESS')->count();

        $produced = (int) ProductionLog::where('production_date', '>=', $start->toDateString())
            ->sum('qty_produced');

        $lowStockCount = $this->lowStockQuery()->count();

        // ---------- Antrian prioritas ----------
        // IN_PROCESS dulu, lalu ORDER sebelum RESTOCK, lalu yang paling lama menunggu
        $priority = ProductionRequest::with('product.group')
            ->whereIn('status', ['WAITING', 'IN_PROCESS'])
            ->orderByRaw("CASE status WHEN 'IN_PROCESS' THEN 0 ELSE 1 END")
            ->orderByRaw("CASE source WHEN 'ORDER' THEN 0 ELSE 1 END")
            ->orderBy('created_at')
            ->orderBy('id')
            ->limit($limit)
            ->get()
            ->map(fn ($req) => array_merge($this->productBits($req->product), [
                'request_no'    => $req->request_no,
                'source'        => $req->source,
                'status'        => $req->status,
                'qty_requested' => (int) $req->qty_requested,
                'qty_produced'  => (int) $req->qty_produced,
                'remaining'     => max((int) $req->qty_requested - (int) $req->qty_produced, 0),
            ]))
            ->values();

        // ---------- Varian stok rendah (hanya MTS, per varian) ----------
        // Yang paling parah (rasio stok/minimum terkecil) di atas.
        // min_stock pasti > 0 di sini karena current_stock >= 0 dan current_stock < min_stock.
        $lowVariants = $this->lowStockQuery()
            ->with('group')
            ->orderByRaw('current_stock::numeric / min_stock')
            ->orderBy('sku')
            ->limit($limit)
            ->get()
            ->map(fn ($p) => [
                'product_name'  => $p->group?->name ?? '-',
                'variant_name'  => $p->variant_name,
                'sku'           => $p->sku,
                'current_stock' => (int) $p->current_stock,
                'min_stock'     => (int) $p->min_stock,
                'target_stock'  => $p->target_stock !== null ? (int) $p->target_stock : null,
            ])
            ->values();

        // ---------- Produksi terbaru ----------
        $recentLogs = ProductionLog::with(['productionRequest.product.group', 'recorder'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        $recent = $recentLogs->map(fn ($log) => array_merge(
            $this->productBits($log->productionRequest?->product),
            [
                'id'              => $log->id,
                'request_no'      => $log->productionRequest?->request_no,
                'qty_produced'    => (int) $log->qty_produced,
                'produced_at'     => $this->iso($log->created_at),
                'production_date' => $log->production_date?->format('Y-m-d'),
                'recorded_by'     => $log->recorder?->name,
            ]
        ))->values();

        // ---------- Aktivitas produksi ----------
        // Digabung dari 4 kejadian: request dibuat, dimulai, hasil dicatat, selesai
        $createdReqs = ProductionRequest::with('product.group')
            ->orderByDesc('created_at')->orderByDesc('id')->limit($limit)->get();

        $startedReqs = ProductionRequest::with('product.group')
            ->whereNotNull('started_at')->orderByDesc('started_at')->limit($limit)->get();

        $completedReqs = ProductionRequest::with('product.group')
            ->whereNotNull('completed_at')->orderByDesc('completed_at')->limit($limit)->get();

        $requesterNames = User::whereIn('id', $createdReqs->pluck('requested_by')->filter()->unique())
            ->pluck('name', 'id');

        $events = collect();

        foreach ($createdReqs as $req) {
            $events->push($this->activity('created', $req, $req->created_at, $requesterNames[$req->requested_by] ?? null, null));
        }

        foreach ($startedReqs as $req) {
            $events->push($this->activity('started', $req, $req->started_at, null, null));
        }

        foreach ($completedReqs as $req) {
            $events->push($this->activity('completed', $req, $req->completed_at, null, null));
        }

        foreach ($recentLogs as $log) {
            if ($log->productionRequest) {
                $events->push($this->activity(
                    'logged',
                    $log->productionRequest,
                    $log->created_at,
                    $log->recorder?->name,
                    (int) $log->qty_produced
                ));
            }
        }

        $activities = $events
            ->filter(fn ($e) => $e['ts'] !== null)
            ->sortByDesc('ts')
            ->take($limit)
            ->map(fn ($e) => Arr::except($e, 'ts'))
            ->values();

        return response()->json([
            'summary' => [
                'active_queue'       => $activeQueue,
                'in_process'         => $inProcess,
                'produced'           => $produced,
                'period'             => $period,
                'period_label'       => $periodLabel,
                'low_stock_variants' => $lowStockCount,
            ],
            'priority_requests'  => $priority,
            'low_stock_variants' => $lowVariants,
            'recent_productions' => $recent,
            'recent_activities'  => $activities,
        ]);
    }

    /**
     * Varian aktif dari produk MTS dengan stok di bawah minimum.
     * Aturan sama dengan Product::getStockStatusAttribute() ("low").
     */
    private function lowStockQuery()
    {
        return Product::query()
            ->where('is_active', true)
            ->whereNotNull('min_stock')
            ->whereColumn('current_stock', '<', 'min_stock')
            ->whereHas('group', fn ($q) => $q
                ->where('production_method', 'MTS')
                ->where('is_active', true));
    }

    private function productBits(?Product $product): array
    {
        return [
            'product_name' => $product?->group?->name ?? '-',
            'variant_name' => $product?->variant_name,
            'sku'          => $product?->sku,
        ];
    }

    private function activity(string $type, ProductionRequest $req, $at, ?string $by, ?int $qty): array
    {
        $time = $at ? Carbon::parse($at) : null;

        return array_merge($this->productBits($req->product), [
            'type'       => $type,
            'request_no' => $req->request_no,
            'qty'        => $qty,
            'at'         => $time?->toIso8601String(),
            'by'         => $by,
            'ts'         => $time?->timestamp, // hanya untuk pengurutan, dibuang sebelum dikirim
        ]);
    }

    private function iso($value): ?string
    {
        return $value ? Carbon::parse($value)->toIso8601String() : null;
    }
}