<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
