<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    public function category()
    {
        return $this->belongsTo(MaterialCategory::class, 'material_category_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
