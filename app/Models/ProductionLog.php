<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionLog extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'production_request_id',
        'recorded_by',
        'production_date',
        'qty_produced',
        'notes',
    ];

    protected function casts(): array
    {
        return ['production_date' => 'date'];
    }

    public function productionRequest(): BelongsTo
    {
        return $this->belongsTo(ProductionRequest::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(ProductionLogMaterial::class);
    }
}