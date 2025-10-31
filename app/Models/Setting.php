<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    use HasUuids;

    protected $guarded = [];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        return cache()->remember("settings:{$key}", 60, function () use ($key, $default) {
            return static::query()->where('key', $key)->value('value') ?? $default;
        });
    }
}
