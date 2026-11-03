<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionLogMaterial extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'production_log_id',
        'production_request_id',
        'raw_material_id',
        'usage_date',
        'qty_used',
    ];

    protected function casts(): array
    {
        return ['usage_date' => 'date'];
    }

    public function productionLog(): BelongsTo
    {
        return $this->belongsTo(ProductionLog::class);
    }

    public function productionRequest(): BelongsTo
    {
        return $this->belongsTo(ProductionRequest::class);
    }

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }
}