<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductGroup extends Model
{
    protected $fillable = [
        'code', 'name', 'category_id', 'production_method',
        'length_cm', 'width_cm', 'price', 'description',
        'image_path', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'group_id');
    }

    // Aman walau bucket belum dibuat: hasilnya null
    public function getImageUrlAttribute(): ?string
    {
        $baseUrl = config('filesystems.disks.supabase.url');

        if (! $this->image_path || ! $baseUrl) {
            return null;
        }

        return rtrim($baseUrl, '/') . '/' . ltrim($this->image_path, '/');
    }

    // safe / low / mto
    public function getStockStatusAttribute(): string
    {
        if (strtoupper($this->production_method) === 'MTO') {
            return 'mto';
        }

        return $this->products->contains(fn ($p) => $p->stock_status === 'low')
            ? 'low'
            : 'safe';
    }

    public function materials(): HasMany
    {
        return $this->hasMany(ProductMaterial::class, 'group_id');
    }
}