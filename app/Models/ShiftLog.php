<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftLog extends Model
{
    protected $fillable = [
        'venue_id', 'user_id', 'opening_cash', 'opened_at', 'closed_at',
        'expected_cash', 'delivered_cash', 'difference', 'notes',
    ];

    protected $casts = [
        'opened_at'      => 'datetime',
        'closed_at'      => 'datetime',
        'expected_cash'  => 'decimal:2',
        'opening_cash'   => 'decimal:2',
        'difference'     => 'decimal:2',
        'delivered_cash' => 'decimal:2',
    ];

    public function venue()     { return $this->belongsTo(Venue::class); }
    public function staff()     { return $this->belongsTo(User::class, 'user_id'); }
    public function movements() { return $this->hasMany(ShiftMovement::class); }

    public function isOpen(): bool { return is_null($this->closed_at); }

    public function totalCash(): float
    {
        return (float) $this->movements->sum('amount');
    }
}
