<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use App\Models\ProductionLog;
use App\Models\ProductionLogMaterial;

class ProductionHistoryController extends Controller
{
    public function page()
    {
        return view('produksi.history.index');
    }

    // Satu elemen = satu sesi produksi (satu baris production_logs)
    public function data()
    {
        $logs = ProductionLog::with(['productionRequest.product.group', 'recorder'])
            ->orderByDesc('production_date')
            ->orderByDesc('id')
            ->get()
            ->map(function ($log) {
                $req     = $log->productionRequest;
                $product = $req?->product;
                $group   = $product?->group;

                return [
                    'id'                    => $log->id,
                    'production_request_id' => $log->production_request_id,
                    'recorded_by'           => $log->recorded_by,
                    'production_date'       => $log->production_date->format('Y-m-d'),
                    'qty_produced'          => $log->qty_produced,
                    'notes'                 => $log->notes,
                    'recorder'              => $log->recorder
                        ? ['id' => $log->recorder->id, 'name' => $log->recorder->name]
                        : null,
                    'production_request'    => $req ? [
                        'id'            => $req->id,
                        'request_no'    => $req->request_no,
                        'qty_requested' => $req->qty_requested,
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
                    ] : null,
                ];
            });

        return response()->json($logs);
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
}