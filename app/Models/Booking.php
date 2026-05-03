<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    protected $fillable = [
        'user_id', 'field_id', 'qr_token', 'status',
        'total_price', 'deposit_amount', 'balance_due', 'platform_fee',
        'payment_status', 'payment_method', 'is_walkin', 'notes',
    ];

    protected $casts = [
        'total_price'    => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'balance_due'    => 'decimal:2',
        'platform_fee'   => 'decimal:2',
        'is_walkin'      => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $booking) {
            if (!$booking->qr_token) {
                $booking->qr_token = strtoupper('FUTGO-' . Str::random(8));
            }
        });
    }

    public function user()         { return $this->belongsTo(User::class); }
    public function field()        { return $this->belongsTo(Field::class); }
    public function slots()        { return $this->belongsToMany(Slot::class, 'booking_slots')->using(BookingSlot::class)->withPivot('unit_price'); }
    public function transactions() { return $this->hasMany(Transaction::class); }

    public function isConfirmed(): bool  { return $this->status === 'confirmed'; }
    public function isCompleted(): bool  { return $this->status === 'completed'; }
    public function isNoShow(): bool     { return $this->status === 'no_show'; }

    // Rango horario legible: "20:00 – 22:00"
    public function getTimeRangeAttribute(): string
    {
        $slots = $this->slots->sortBy('starts_at');
        if ($slots->isEmpty()) return '–';
        return $slots->first()->starts_at->format('H:i')
            . ' – '
            . $slots->last()->ends_at->format('H:i');
    }
}
