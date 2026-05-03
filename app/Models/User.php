<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'role',
        'phone', 'avatar_url', 'google_id', 'city_id',
        'fcm_token', 'push_opt_in', 'whatsapp_opt_in', 'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token', 'google_id'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'password'          => 'hashed',
            'push_opt_in'       => 'boolean',
            'whatsapp_opt_in'   => 'boolean',
        ];
    }

    public function isAdmin(): bool   { return $this->role === 'admin'; }
    public function isPartner(): bool { return $this->role === 'partner'; }
    public function isStaff(): bool   { return $this->role === 'staff'; }
    public function isUser(): bool    { return $this->role === 'user'; }

    public function city()             { return $this->belongsTo(City::class); }
    public function venues()           { return $this->hasMany(Venue::class); }
    public function venuesAsignados()  { return $this->belongsToMany(Venue::class, 'venue_staff')->withPivot('active')->withTimestamps(); }
    public function bookings()         { return $this->hasMany(Booking::class); }
    public function shiftLogs()        { return $this->hasMany(ShiftLog::class); }
    public function auditLogs()        { return $this->hasMany(AuditLog::class); }
    public function notificationLogs() { return $this->hasMany(NotificationLog::class); }
}
