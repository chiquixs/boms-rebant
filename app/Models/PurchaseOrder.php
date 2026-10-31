<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    public const STATUS_ORDERED            = 'ORDERED';
    public const STATUS_PARTIALLY_RECEIVED = 'PARTIALLY_RECEIVED';
    public const STATUS_RECEIVED           = 'RECEIVED';

    public const TRIGGER_MANUAL = 'MANUAL';

    protected $fillable = [
        'supplier_id',
        'created_by',
        'approved_by',
        'po_no',
        'trigger_type',
        'status',
        'approved_at',
        'email_sent_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'approved_at'   => 'datetime',
            'email_sent_at' => 'datetime',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_RECEIVED           => 'Received',
            self::STATUS_PARTIALLY_RECEIVED => 'Sebagian Diterima',
            default                         => 'Ordered',
        };
    }

    public function getStatusClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_RECEIVED           => 'received',
            self::STATUS_PARTIALLY_RECEIVED => 'partial',
            default                         => 'ordered',
        };
    }
}