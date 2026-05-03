<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class BookingSlot extends Pivot
{
    public $timestamps = false;

    protected $fillable = ['booking_id', 'slot_id', 'unit_price'];

    protected $casts = ['unit_price' => 'decimal:2'];
}
