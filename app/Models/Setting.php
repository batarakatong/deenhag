<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public static function valueOf(string $key, mixed $default = null): mixed
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function put(string $key, mixed $value, string $group = 'general', string $type = 'string'): void
    {
        static::updateOrCreate(['key' => $key], [
            'value' => is_array($value) ? json_encode($value) : $value,
            'type' => $type,
            'group' => $group,
        ]);
    }
}
