<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    protected $fillable = [
        'user_id', 'city_id', 'name', 'slug', 'description',
        'address', 'district', 'latitude', 'longitude',
        'status', 'cover_image', 'phone',
    ];

    public function city()    { return $this->belongsTo(City::class); }
    public function owner()   { return $this->belongsTo(User::class, 'user_id'); }
    public function fields()  { return $this->hasMany(Field::class); }
    public function staff()   { return $this->belongsToMany(User::class, 'venue_staff')->withPivot('active')->withTimestamps(); }
}
