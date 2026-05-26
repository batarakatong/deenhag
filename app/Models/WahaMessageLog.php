<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WahaMessageLog extends Model
{
    protected $casts = [
        'sent_at' => 'datetime',
        'last_attempt_at' => 'datetime',
    ];
}
