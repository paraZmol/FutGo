<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'booking_id', 'gateway', 'gateway_reference',
        'amount', 'currency', 'type',
        'payment_method', 'status', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function booking() { return $this->belongsTo(Booking::class); }
}
