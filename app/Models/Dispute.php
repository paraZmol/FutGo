<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    protected $fillable = [
        'booking_id', 'user_id', 'resolved_by',
        'tipo', 'estado', 'prioridad',
        'motivo', 'evidence_url', 'evidence_description', 'resolucion', 'monto_reclamado', 'resolved_at',
    ];

    protected $casts = [
        'monto_reclamado' => 'decimal:2',
        'resolved_at'     => 'datetime',
    ];

    public function booking()    { return $this->belongsTo(Booking::class); }
    public function user()       { return $this->belongsTo(User::class); }
    public function resolver()   { return $this->belongsTo(User::class, 'resolved_by'); }
}
