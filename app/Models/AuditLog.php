<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'action', 'target_type', 'target_id', 'payload', 'ip_address',
    ];

    protected $casts = [
        'payload'    => 'array',
        'created_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }

    // Helper estático para registrar fácilmente
    public static function record(string $action, $target = null, array $payload = []): self
    {
        return self::create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'target_type' => $target ? get_class($target) : null,
            'target_id'   => $target?->id,
            'payload'     => $payload,
            'ip_address'  => request()->ip(),
        ]);
    }
}
