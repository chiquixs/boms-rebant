<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RawMaterialMovement extends Model
{
    // Tabel cuma punya created_at, tidak ada updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'raw_material_id',
        'created_by',
        'movement_type',   // IN | OUT | ADJUSTMENT
        'qty',
        'qty_before',
        'qty_after',
        'note',
        'production_log_material_id',
        'purchase_order_item_id',
    ];

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Delta = qty_after - qty_before (positif = masuk, negatif = keluar)
    public function getDeltaAttribute(): float
    {
        return $this->qty_after - $this->qty_before;
    }
}