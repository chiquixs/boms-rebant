<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Platform extends Model
{
    protected $fillable = ['name'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // Class CSS badge: tiktok / shopee
    public function getBadgeClassAttribute(): string
    {
        return str_contains(strtolower($this->name), 'tiktok') ? 'tiktok' : 'shopee';
    }
}