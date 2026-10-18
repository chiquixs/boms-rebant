<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionRequest extends Model
{
    public const SOURCE_ORDER   = 'ORDER';
    public const SOURCE_RESTOCK = 'RESTOCK';

    public const STATUS_WAITING    = 'WAITING';
    public const STATUS_IN_PROCESS = 'IN_PROCESS';
    public const STATUS_COMPLETED  = 'COMPLETED';

    protected $fillable = [
        'product_id',
        'order_item_id',
        'requested_by',
        'request_no',
        'source',
        'qty_requested',
        'qty_produced',
        'status',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at'   => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}