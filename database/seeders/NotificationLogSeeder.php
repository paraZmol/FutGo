<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\NotificationLog;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class NotificationLogSeeder extends Seeder
{
    public function run(): void
    {
        $bookings = Booking::with('user')->get();

        foreach ($bookings as $booking) {
            if (!$booking->user) continue;

            // Confirmación de reserva — siempre se envía
            NotificationLog::create([
                'user_id' => $booking->user_id,
                'channel' => 'email',
                'type'    => 'booking_confirmed',
                'payload' => json_encode([
                    'booking_id' => $booking->id,
                    'qr_token'   => $booking->qr_token,
                    'monto'      => $booking->deposit_amount,
                ]),
                'status'  => 'sent',
                'sent_at' => $booking->created_at->addSeconds(rand(5, 30)),
                'created_at' => $booking->created_at,
            ]);

            // Push notification también
            NotificationLog::create([
                'user_id' => $booking->user_id,
                'channel' => 'push',
                'type'    => 'booking_confirmed',
                'payload' => json_encode([
                    'title'   => '¡Reserva confirmada!',
                    'body'    => 'Tu cancha está asegurada. Mostrá el QR al llegar.',
                    'booking_id' => $booking->id,
                ]),
                'status'  => 'sent',
                'sent_at' => $booking->created_at->addSeconds(rand(2, 10)),
                'created_at' => $booking->created_at,
            ]);

            // Recordatorio 1 hora antes (solo reservas confirmadas o completadas)
            if (in_array($booking->status, ['confirmed', 'completed'])) {
                $slot = $booking->slots->sortBy('starts_at')->first();
                if ($slot) {
                    $horaRecordatorio = $slot->starts_at->subHour();
                    NotificationLog::create([
                        'user_id' => $booking->user_id,
                        'channel' => 'push',
                        'type'    => 'checkin_reminder',
                        'payload' => json_encode([
                            'title'   => 'Tu partido empieza en 1 hora',
                            'body'    => 'Recordá llevar el QR. Saldo a pagar: S/ '.$booking->balance_due,
                            'booking_id' => $booking->id,
                        ]),
                        'status'  => $horaRecordatorio->isPast() ? 'sent' : 'pending',
                        'sent_at' => $horaRecordatorio->isPast() ? $horaRecordatorio : null,
                        'created_at' => $horaRecordatorio->subMinutes(5),
                    ]);
                }
            }

            // Alerta de no-show al partner
            if ($booking->status === 'no_show') {
                NotificationLog::create([
                    'user_id' => $booking->field?->venue?->user_id ?? $booking->user_id,
                    'channel' => 'email',
                    'type'    => 'noshow_alert',
                    'payload' => json_encode([
                        'booking_id'   => $booking->id,
                        'anticipo'     => $booking->deposit_amount,
                        'cliente'      => $booking->user->name,
                    ]),
                    'status'  => 'sent',
                    'sent_at' => $booking->updated_at,
                    'created_at' => $booking->updated_at,
                ]);
            }
        }

        // Algunas notificaciones fallidas (para realismo)
        $primeraReserva = $bookings->first();
        if ($primeraReserva) {
            NotificationLog::create([
                'user_id' => $primeraReserva->user_id,
                'channel' => 'whatsapp',
                'type'    => 'booking_confirmed',
                'payload' => json_encode(['error' => 'WhatsApp API timeout']),
                'status'  => 'failed',
                'sent_at' => null,
                'created_at' => $primeraReserva->created_at,
            ]);
        }
    }
}
