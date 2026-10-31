<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialShortage extends Model
{
    public const STATUS_OPEN     = 'OPEN';
    public const STATUS_RESOLVED = 'RESOLVED';

    protected $fillable = [
        'production_request_id', 'raw_material_id',
        'qty_needed', 'qty_available', 'status',
        'reported_at', 'resolved_at',
    ];

    protected function casts(): array
    {
        return ['reported_at' => 'datetime', 'resolved_at' => 'datetime'];
    }

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }
}