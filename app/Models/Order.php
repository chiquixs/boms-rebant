<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUS_NEW                = 'NEW';
    public const STATUS_WAITING_PRODUCTION = 'WAITING_PRODUCTION';
    public const STATUS_READY_TO_PACK      = 'READY_TO_PACK';
    public const STATUS_COMPLETED          = 'COMPLETED';

    protected $fillable = [
        'platform_id',
        'created_by',
        'platform_order_no',
        'customer_name',
        'order_date',
        'status',
        'total_amount',
        'notes',
        'ready_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'order_date'   => 'date',
            'total_amount' => 'decimal:2',
            'ready_at'     => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Class CSS status: new / waiting / ready / completed
    public function getStatusClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_WAITING_PRODUCTION => 'waiting',
            self::STATUS_READY_TO_PACK      => 'ready',
            self::STATUS_COMPLETED          => 'completed',
            default                         => 'new',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_WAITING_PRODUCTION => 'Waiting Production',
            self::STATUS_READY_TO_PACK      => 'Ready to Pack',
            self::STATUS_COMPLETED          => 'Completed',
            default                         => 'New',
        };
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
