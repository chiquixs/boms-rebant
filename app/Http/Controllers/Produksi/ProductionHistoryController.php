<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use App\Models\ProductionLog;
use App\Models\ProductionLogMaterial;
use App\Models\ProductionRequest;

class ProductionHistoryController extends Controller
{
    public function page()
    {
        return view('produksi.history.index');
    }

    /**
     * Gabungan dua jenis baris:
     * - IN_PROCESS : satu baris per production request yang sedang dikerjakan
     * - LOG        : satu baris per sesi produksi (production_logs)
     */
    public function data()
    {
        $inProcess = ProductionRequest::with(['product.group'])
            ->where('status', 'IN_PROCESS')
            ->orderByDesc('started_at')
            ->orderByDesc('id')
            ->get()
            ->map(function ($req) {
                return [
                    'id'                    => 'pr-' . $req->id, // beda dari id log, supaya tidak bentrok di JS
                    'entry_type'            => 'IN_PROCESS',
                    'request_status'        => $req->status,
                    'production_request_id' => $req->id,
                    'recorded_by'           => null,
                    'production_date'       => ($req->started_at ?? $req->created_at)->format('Y-m-d'),
                    'qty_produced'          => $req->qty_produced, // akumulasi sampai sekarang
                    'notes'                 => null,
                    'recorder'              => null,
                    'production_request'    => $this->formatRequest($req),
                ];
            });

        $logs = ProductionLog::with(['productionRequest.product.group', 'recorder'])
            ->orderByDesc('production_date')
            ->orderByDesc('id')
            ->get()
            ->map(function ($log) {
                $req = $log->productionRequest;

                return [
                    'id'                    => $log->id,
                    'entry_type'            => 'LOG',
                    'request_status'        => $req?->status,
                    'production_request_id' => $log->production_request_id,
                    'recorded_by'           => $log->recorded_by,
                    'production_date'       => $log->production_date->format('Y-m-d'),
                    'qty_produced'          => $log->qty_produced,
                    'notes'                 => $log->notes,
                    'recorder'              => $log->recorder
                        ? ['id' => $log->recorder->id, 'name' => $log->recorder->name]
                        : null,
                    'production_request'    => $req ? $this->formatRequest($req) : null,
                ];
            });

        // Yang sedang diproses di atas, lalu riwayat sesi
        return response()->json($inProcess->concat($logs)->values());
    }

    public function materials(ProductionLog $log)
    {
        $items = ProductionLogMaterial::with('rawMaterial')
            ->where('production_log_id', $log->id)
            ->get()
            ->map(fn ($m) => [
                'raw_material' => $m->rawMaterial ? [
                    'id'   => $m->rawMaterial->id,
                    'code' => $m->rawMaterial->code,
                    'name' => $m->rawMaterial->name,
                    'unit' => $m->rawMaterial->unit,
                ] : null,
                'qty_used'   => (float) $m->qty_used,
                'usage_date' => $m->usage_date
                    ? \Illuminate\Support\Carbon::parse($m->usage_date)->format('Y-m-d')
                    : $log->production_date->format('Y-m-d'),
            ]);

        return response()->json($items);
    }

    private function formatRequest(ProductionRequest $req): array
    {
        $product = $req->product;
        $group   = $product?->group;

        return [
            'id'            => $req->id,
            'request_no'    => $req->request_no,
            'qty_requested' => $req->qty_requested,
            'qty_produced'  => $req->qty_produced,
            'status'        => $req->status,
            'product'       => $product ? [
                'id'           => $product->id,
                'sku'          => $product->sku,
                'variant_name' => $product->variant_name,
                'image_path'   => $product->image_url,
                'group'        => [
                    'id'         => $group?->id,
                    'name'       => $group?->name ?? '-',
                    'image_path' => $group?->image_url,
                ],
            ] : null,
        ];
    }
}