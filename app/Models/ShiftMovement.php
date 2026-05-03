<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftMovement extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'shift_log_id', 'booking_id', 'type', 'amount', 'description',
    ];

    protected $casts = [
        'amount'     => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function shiftLog() { return $this->belongsTo(ShiftLog::class); }
    public function booking()  { return $this->belongsTo(Booking::class); }
}
