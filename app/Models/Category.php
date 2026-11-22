<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = ['name'];

    public function productGroups(): HasMany
    {
        return $this->hasMany(ProductGroup::class, 'category_id');
    }

    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(Product::class, ProductGroup::class, 'category_id', 'group_id', 'id', 'id');
    }
}
