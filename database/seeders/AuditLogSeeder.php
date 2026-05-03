<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\User;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AuditLogSeeder extends Seeder
{
    public function run(): void
    {
        $admin  = User::where('email', 'admin@futgo.app')->first();
        $venues = Venue::all();
        $bookings = Booking::take(5)->get();

        // Aprobaciones de venues (simulan el flujo de onboarding)
        foreach ($venues as $venue) {
            AuditLog::create([
                'user_id'     => $admin?->id,
                'action'      => 'PARTNER_APROBADO',
                'target_type' => 'App\Models\Venue',
                'target_id'   => $venue->id,
                'payload'     => json_encode([
                    'venue'  => $venue->name,
                    'ciudad' => $venue->city?->name,
                    'estado_anterior' => 'pending',
                    'estado_nuevo'    => 'active',
                ]),
                'ip_address'  => '190.42.11.5',
                'created_at'  => Carbon::now()->subDays(rand(5, 30)),
            ]);
        }

        // Login del admin
        AuditLog::create([
            'user_id'    => $admin?->id,
            'action'     => 'LOGIN_ADMIN',
            'payload'    => json_encode(['resultado' => 'exitoso']),
            'ip_address' => '190.42.11.5',
            'created_at' => Carbon::today()->setTime(9, 0),
        ]);

        // Intento de login fallido (sin user_id — atacante desconocido)
        AuditLog::create([
            'user_id'    => null,
            'action'     => 'LOGIN_FALLIDO',
            'payload'    => json_encode(['intentos' => 3, 'email_intentado' => 'admin@futgo.app']),
            'ip_address' => '201.55.77.12',
            'created_at' => Carbon::now()->subDays(3)->setTime(22, 14),
        ]);

        // Bookings revertidos
        foreach ($bookings->take(2) as $booking) {
            AuditLog::create([
                'user_id'     => $admin?->id,
                'action'      => 'BOOKING_REVERTIDO',
                'target_type' => 'App\Models\Booking',
                'target_id'   => $booking->id,
                'payload'     => json_encode([
                    'qr_token'  => $booking->qr_token,
                    'motivo'    => 'Solicitud de reembolso aprobada',
                    'monto'     => $booking->deposit_amount,
                ]),
                'ip_address'  => '190.42.11.5',
                'created_at'  => Carbon::now()->subDays(rand(1, 10)),
            ]);
        }

        // Configuración de comisión modificada
        AuditLog::create([
            'user_id'    => $admin?->id,
            'action'     => 'COMISION_MODIFICADA',
            'payload'    => json_encode([
                'campo'    => 'platform_fee',
                'anterior' => '0.00',
                'nuevo'    => '0.00',
                'fase'     => 'Penetración — sin cambio real',
            ]),
            'ip_address' => '190.42.11.5',
            'created_at' => Carbon::now()->subDays(2)->setTime(18, 45),
        ]);

        // Usuario suspendido
        $jugador = User::where('role', 'user')->orderBy('id', 'desc')->first();
        if ($jugador) {
            AuditLog::create([
                'user_id'     => $admin?->id,
                'action'      => 'USUARIO_SUSPENDIDO',
                'target_type' => 'App\Models\User',
                'target_id'   => $jugador->id,
                'payload'     => json_encode([
                    'usuario'  => $jugador->name,
                    'motivo'   => 'Múltiples no-shows sin justificación',
                    'noshow_count' => 3,
                ]),
                'ip_address'  => '190.42.11.5',
                'created_at'  => Carbon::now()->subDays(1)->setTime(11, 20),
            ]);
        }
    }
}
