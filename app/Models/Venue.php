<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venue extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'city_id', 'name', 'slug', 'description',
        'address', 'district', 'latitude', 'longitude',
        'status', 'cover_image', 'phone', 'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function city()    { return $this->belongsTo(City::class); }
    public function owner()   { return $this->belongsTo(User::class, 'user_id'); }
    public function fields()  { return $this->hasMany(Field::class); }
    public function staff()   { return $this->belongsToMany(User::class, 'venue_staff')->withPivot('active')->withTimestamps(); }
}
