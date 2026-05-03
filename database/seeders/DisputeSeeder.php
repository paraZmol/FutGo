<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Dispute;
use App\Models\User;
use Illuminate\Database\Seeder;

class DisputeSeeder extends Seeder
{
    public function run(): void
    {
        $admin    = User::where('email', 'admin@futgo.app')->first();
        $noshows  = Booking::where('status', 'no_show')->with('user')->get();
        $completadas = Booking::where('status', 'completed')->with('user')->take(2)->get();

        // Disputas abiertas desde no-shows
        foreach ($noshows->take(2) as $booking) {
            Dispute::create([
                'booking_id'      => $booking->id,
                'user_id'         => $booking->user_id,
                'tipo'            => 'noshow',
                'estado'          => 'abierta',
                'prioridad'       => 'alta',
                'motivo'          => 'El cliente reclama que sí se presentó a tiempo pero el staff no registró su ingreso. Solicita devolución del anticipo.',
                'monto_reclamado' => $booking->deposit_amount,
                'resolved_by'     => null,
                'resolved_at'     => null,
            ]);
        }

        // Disputa de reembolso desde una reserva completada
        if ($completadas->count() > 0) {
            $b = $completadas->first();
            Dispute::create([
                'booking_id'      => $b->id,
                'user_id'         => $b->user_id,
                'tipo'            => 'reembolso',
                'estado'          => 'abierta',
                'prioridad'       => 'alta',
                'motivo'          => 'La cancha estaba en malas condiciones (pasto deteriorado). El cliente tiene fotos como evidencia y solicita reembolso parcial.',
                'monto_reclamado' => $b->deposit_amount,
                'resolved_by'     => null,
                'resolved_at'     => null,
            ]);
        }

        // Disputa resuelta (histórica)
        if ($completadas->count() > 1) {
            $b = $completadas->last();
            Dispute::create([
                'booking_id'      => $b->id,
                'user_id'         => $b->user_id,
                'tipo'            => 'pago',
                'estado'          => 'resuelta',
                'prioridad'       => 'baja',
                'motivo'          => 'Doble cobro detectado en la pasarela.',
                'resolucion'      => 'Se verificó el doble cobro y se procesó el reembolso del anticipo duplicado.',
                'monto_reclamado' => $b->deposit_amount,
                'resolved_by'     => $admin?->id,
                'resolved_at'     => now()->subDays(5),
            ]);
        }
    }
}
