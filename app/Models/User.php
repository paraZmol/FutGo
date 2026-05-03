<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'avatar_url',
        'google_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'google_id',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // Helpers de rol
    public function isAdmin(): bool   { return $this->role === 'admin'; }
    public function isPartner(): bool { return $this->role === 'partner'; }
    public function isStaff(): bool   { return $this->role === 'staff'; }
    public function isUser(): bool    { return $this->role === 'user'; }

    // Relaciones (se completan en fases posteriores)
    public function venues()           { return $this->hasMany(Venue::class); }
    public function bookings()         { return $this->hasMany(Booking::class); }
    public function shiftLogs()        { return $this->hasMany(ShiftLog::class); }
    public function auditLogs()        { return $this->hasMany(AuditLog::class); }
    public function notificationLogs() { return $this->hasMany(NotificationLog::class); }
}
