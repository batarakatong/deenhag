<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionStep extends Model
{
    protected $casts = ['is_active' => 'boolean'];
}
