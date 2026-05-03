<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['name', 'department', 'slug', 'latitude', 'longitude', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function venues() { return $this->hasMany(Venue::class); }
    public function users()  { return $this->hasMany(User::class); }
}
