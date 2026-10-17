<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'group_id', 'sku', 'variant_name', 'image_path',
        'current_stock', 'min_stock', 'target_stock', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(ProductGroup::class, 'group_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        $baseUrl = config('filesystems.disks.supabase.url');

        if (! $this->image_path || ! $baseUrl) {
            return null;
        }

        return rtrim($baseUrl, '/') . '/' . ltrim($this->image_path, '/');
    }
}