<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductMaterial extends Model
{
    protected $fillable = [
        'group_id', 'raw_material_id', 'usage_method', 'waste_percent',
        'qty_per_unit', 'piece_length_cm', 'piece_width_cm', 'piece_count',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(ProductGroup::class, 'group_id');
    }

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class, 'raw_material_id');
    }
}
