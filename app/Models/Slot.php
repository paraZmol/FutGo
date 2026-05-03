<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    protected $fillable = [
        'field_id', 'booking_id', 'starts_at', 'ends_at',
        'status', 'unit_price', 'lock_expires_at',
    ];

    protected $casts = [
        'starts_at'      => 'datetime',
        'ends_at'        => 'datetime',
        'lock_expires_at'=> 'datetime',
        'unit_price'     => 'decimal:2',
    ];

    public function field()   { return $this->belongsTo(Field::class); }
    public function booking() { return $this->belongsTo(Booking::class); }

    public function isAvailable(): bool  { return $this->status === 'available'; }
    public function isReserved(): bool   { return $this->status === 'reserved'; }
    public function isNightSlot(): bool  { return $this->starts_at->hour >= 18; }
}
