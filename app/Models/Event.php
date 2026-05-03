<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'venue_id', 'field_id', 'title', 'type',
        'starts_at', 'ends_at', 'created_by',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    public function venue()   { return $this->belongsTo(Venue::class); }
    public function field()   { return $this->belongsTo(Field::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
