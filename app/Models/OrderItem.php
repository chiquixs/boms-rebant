<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'qty',
        'unit_price',
        'subtotal',
        'sku_snapshot',
        'product_name_snapshot',
        'production_method',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function allocation(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(OrderItemAllocation::class);
    }

    public function productionRequest(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ProductionRequest::class);
    }
}
