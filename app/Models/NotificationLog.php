<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    public $timestamps = false;

    protected $table = 'notifications_log';

    protected $fillable = [
        'user_id', 'channel', 'type', 'payload', 'status', 'sent_at',
    ];

    protected $casts = [
        'payload'    => 'array',
        'sent_at'    => 'datetime',
        'created_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
}
