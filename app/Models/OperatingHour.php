<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperatingHour extends Model
{
    protected $fillable = [
        'field_id', 'day_of_week', 'opens_at', 'closes_at',
        'price_day', 'price_night', 'deposit_amount', 'is_active',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'price_day'      => 'decimal:2',
        'price_night'    => 'decimal:2',
        'deposit_amount' => 'decimal:2',
    ];

    const DAYS = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];

    public function field() { return $this->belongsTo(Field::class); }

    public function getDayNameAttribute(): string
    {
        return self::DAYS[$this->day_of_week] ?? '';
    }

    // Precio según la hora (día = antes de 18:00, noche = desde 18:00)
    public function priceForHour(int $hour): float
    {
        return $hour >= 18 ? (float)$this->price_night : (float)$this->price_day;
    }
}
