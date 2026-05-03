<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\ShiftLog;
use App\Models\ShiftMovement;
use App\Models\User;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        // Staff asignados por venue_staff
        $staffAsignados = DB::table('venue_staff')
            ->where('active', true)
            ->get();

        foreach ($staffAsignados as $vs) {
            $staff = User::find($vs->user_id);
            $venue = Venue::find($vs->venue_id);
            if (!$staff || !$venue) continue;

            // ── TURNOS PASADOS (últimos 7 días) ────────────────────
            for ($dia = 6; $dia >= 1; $dia--) {
                $fecha = Carbon::today()->subDays($dia);

                // Turno mañana
                $shift = ShiftLog::create([
                    'venue_id'       => $venue->id,
                    'user_id'        => $staff->id,
                    'opened_at'      => $fecha->copy()->setTime(7, 0),
                    'closed_at'      => $fecha->copy()->setTime(15, 0),
                    'expected_cash'  => rand(200, 600),
                    'delivered_cash' => rand(180, 580),
                    'notes'          => null,
                ]);

                // Movimientos del turno — buscar bookings de ese día en ese venue
                $bookingsDelDia = Booking::whereHas('field', fn($q) =>
                    $q->where('venue_id', $venue->id)
                )->whereHas('slots', fn($q) =>
                    $q->whereDate('starts_at', $fecha->toDateString())
                )->whereIn('status', ['completed', 'no_show'])
                ->with('slots')
                ->get();

                foreach ($bookingsDelDia as $booking) {
                    $tipo   = $booking->status === 'no_show' ? 'noshow_retention' : 'checkin';
                    $monto  = $tipo === 'checkin'
                        ? (float) $booking->balance_due
                        : (float) $booking->deposit_amount;

                    ShiftMovement::create([
                        'shift_log_id' => $shift->id,
                        'booking_id'   => $booking->id,
                        'type'         => $tipo,
                        'amount'       => $monto,
                        'description'  => $tipo === 'checkin'
                            ? 'Cobro saldo en cancha'
                            : 'Anticipo retenido por no-show',
                    ]);
                }

                // Algunos presenciales simulados
                $numPresenciales = rand(1, 3);
                for ($i = 0; $i < $numPresenciales; $i++) {
                    ShiftMovement::create([
                        'shift_log_id' => $shift->id,
                        'booking_id'   => null,
                        'type'         => 'walkin',
                        'amount'       => rand(60, 100),
                        'description'  => 'Cliente presencial sin reserva',
                    ]);
                }

                // Recalcular expected_cash
                $totalReal = $shift->movements()->sum('amount');
                $shift->update(['expected_cash' => $totalReal]);
            }

            // ── TURNO DE HOY (abierto) ──────────────────────────────
            $turnoHoy = ShiftLog::create([
                'venue_id'  => $venue->id,
                'user_id'   => $staff->id,
                'opened_at' => Carbon::today()->setTime(7, 0),
                'closed_at' => null,  // turno abierto
                'notes'     => null,
            ]);

            // Check-ins de hoy
            $bookingsHoy = Booking::whereHas('field', fn($q) =>
                $q->where('venue_id', $venue->id)
            )->whereHas('slots', fn($q) =>
                $q->whereDate('starts_at', today()->toDateString())
                  ->where('starts_at', '<', now())
            )->where('status', 'confirmed')
            ->with('slots')
            ->get();

            foreach ($bookingsHoy as $booking) {
                ShiftMovement::create([
                    'shift_log_id' => $turnoHoy->id,
                    'booking_id'   => $booking->id,
                    'type'         => 'checkin',
                    'amount'       => (float) $booking->balance_due,
                    'description'  => 'Cobro saldo al ingresar',
                ]);
            }
        }
    }
}
