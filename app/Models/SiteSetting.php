<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'label', 'group'];

    // Obtiene un valor con caché (5 minutos)
    public static function get(string $key, $default = null): mixed
    {
        return Cache::remember("setting_{$key}", 300, function () use ($key, $default) {
            return static::where('key', $key)->value('value') ?? $default;
        });
    }

    // Guarda y limpia la caché
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting_{$key}");
    }

    // Obtiene todos los settings agrupados
    public static function allGrouped(): array
    {
        return static::all()->groupBy('group')->toArray();
    }
}
