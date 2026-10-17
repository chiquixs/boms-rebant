<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    protected $fillable = [
        'code', 'name', 'unit', 'material_type',
        'sheet_length_cm', 'sheet_width_cm',
        'current_stock', 'safety_stock', 'target_stock',
        'avg_daily_usage', 'reorder_point', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function getHasSheetSizeAttribute(): bool
    {
        return $this->sheet_length_cm > 0 && $this->sheet_width_cm > 0;
    }
}