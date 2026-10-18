<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductStockMovement extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'product_id',
        'created_by',
        'movement_type',   // IN | OUT | ADJUSTMENT
        'qty',
        'qty_before',
        'qty_after',
        'note',
        'order_item_id',
        'production_log_id',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function getDeltaAttribute(): float
    {
        return $this->qty_after - $this->qty_before;
    }
}