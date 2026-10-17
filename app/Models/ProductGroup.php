<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductGroup extends Model
{
    protected $table = 'product_groups';

    protected $fillable = ['name', 'price']; // <-- sesuaikan

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'group_id');
    }
}