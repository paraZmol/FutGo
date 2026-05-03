<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Field extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'venue_id', 'name', 'sport_type', 'surface',
        'is_covered', 'amenities', 'status',
    ];

    protected $casts = [
        'is_covered' => 'boolean',
        'amenities'  => 'array',
    ];

    public function venue()          { return $this->belongsTo(Venue::class); }
    public function operatingHours() { return $this->hasMany(OperatingHour::class); }
    public function slots()          { return $this->hasMany(Slot::class); }
    public function events()         { return $this->hasMany(Event::class); }
    public function bookings()       { return $this->hasMany(Booking::class); }

    public function scheduleForDay(int $dayOfWeek): ?OperatingHour
    {
        return $this->operatingHours->firstWhere('day_of_week', $dayOfWeek);
    }

    public function availableToday()
    {
        return $this->slots()
            ->where('status', 'available')
            ->whereDate('starts_at', today())
            ->orderBy('starts_at')
            ->get();
    }
}
