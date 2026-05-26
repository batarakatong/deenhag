<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $casts = [
        'is_active' => 'boolean',
        'is_custom_size' => 'boolean',
        'sample_images' => 'array',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function options()
    {
        return $this->hasMany(ProductOption::class);
    }

    public function materialUsages()
    {
        return $this->hasMany(ProductMaterial::class);
    }
}
