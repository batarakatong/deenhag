<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $casts = ['invoice_date' => 'date', 'due_date' => 'date'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
