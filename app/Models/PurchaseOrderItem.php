<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'raw_material_id',
        'material_shortage_id',
        'qty_suggested',
        'qty_ordered',
        'qty_received',
        'unit_price',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function getSubtotalAttribute(): float
    {
        return $this->qty_ordered * $this->unit_price;
    }
}