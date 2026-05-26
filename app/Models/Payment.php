<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $casts = ['paid_at' => 'datetime', 'confirmed_at' => 'datetime'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
