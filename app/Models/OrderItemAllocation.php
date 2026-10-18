<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemAllocation extends Model
{
    public const STATUS_RESERVED = 'RESERVED';
    public const STATUS_CONSUMED = 'CONSUMED';

    protected $fillable = [
        'order_item_id',
        'product_id',
        'qty_allocated',
        'status',
        'consumed_at',
    ];

    protected function casts(): array
    {
        return ['consumed_at' => 'datetime'];
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}