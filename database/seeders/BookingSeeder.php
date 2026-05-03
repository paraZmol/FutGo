<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\Field;
use App\Models\Slot;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $jugadores = User::where('role', 'user')->get();
        $fields    = Field::with('slots')->get();

        // Reservas del pasado (completadas y no-shows)
        $this->crearReserva($jugadores[0],  $fields[0],  'completed', 'yape',    1, -2);
        $this->crearReserva($jugadores[1],  $fields[0],  'completed', 'tarjeta', 2, -2);
        $this->crearReserva($jugadores[2],  $fields[1],  'no_show',   'yape',    1, -1);
        $this->crearReserva($jugadores[3],  $fields[2],  'completed', 'plin',    1, -1);
        $this->crearReserva($jugadores[4],  $fields[3],  'completed', 'yape',    1, -3);
        $this->crearReserva($jugadores[5],  $fields[0],  'completed', 'efectivo',1, -4, true); // walkin
        $this->crearReserva($jugadores[6],  $fields[4],  'completed', 'tarjeta', 1, -5);
        $this->crearReserva($jugadores[7],  $fields[1],  'no_show',   'yape',    1, -6);

        // Reservas de hoy (confirmadas — próximas a jugarse)
        $this->crearReserva($jugadores[0],  $fields[0],  'confirmed', 'yape',    1,  0, false, 20);
        $this->crearReserva($jugadores[1],  $fields[1],  'confirmed', 'tarjeta', 1,  0, false, 19);
        $this->crearReserva($jugadores[8],  $fields[2],  'confirmed', 'plin',    2,  0, false, 18);
        $this->crearReserva($jugadores[9],  $fields[3],  'confirmed', 'yape',    1,  0, false, 21);

        // Reservas futuras (próximos días)
        $this->crearReserva($jugadores[2],  $fields[0],  'confirmed', 'yape',    1,  1, false, 19);
        $this->crearReserva($jugadores[3],  $fields[1],  'confirmed', 'tarjeta', 2,  1, false, 20);
        $this->crearReserva($jugadores[10], $fields[4],  'confirmed', 'plin',    1,  2, false, 18);
        $this->crearReserva($jugadores[11], $fields[0],  'confirmed', 'yape',    1,  3, false, 20);
        $this->crearReserva($jugadores[4],  $fields[2],  'confirmed', 'tarjeta', 1,  4, false, 19);
        $this->crearReserva($jugadores[5],  $fields[3],  'confirmed', 'yape',    3,  5, false, 18); // 3 slots
    }

    private function crearReserva(
        $jugador, $field, string $status, string $metodo,
        int $numSlots = 1, int $dayOffset = 0,
        bool $isWalkin = false, int $hora = 10
    ): void {
        $fecha = Carbon::today()->addDays($dayOffset)->toDateString();

        $statusBuscar = $dayOffset < 0 ? 'completed' : 'available';

        $query = Slot::where('field_id', $field->id)
            ->where('status', $statusBuscar)
            ->whereNull('booking_id')
            ->whereDate('starts_at', $fecha)
            ->orderBy('starts_at');

        if ($dayOffset >= 0) {
            $query->where('starts_at', '>=',
                Carbon::today()->addDays($dayOffset)->setTime($hora, 0)
            );
        }

        $slots = $query->take($numSlots)->get();

        if ($slots->count() < $numSlots) return;

        $totalPrice    = $slots->sum('unit_price');
        $depositAmount = round($totalPrice * 0.35, 2);
        $balanceDue    = round($totalPrice - $depositAmount, 2);

        $booking = Booking::create([
            'user_id'        => $jugador->id,
            'field_id'       => $field->id,
            'status'         => $status,
            'total_price'    => $totalPrice,
            'deposit_amount' => $depositAmount,
            'balance_due'    => $balanceDue,
            'payment_status' => $status === 'cancelled' ? 'refunded' : 'paid',
            'payment_method' => $metodo,
            'is_walkin'      => $isWalkin,
        ]);

        // Relacionar slots con el booking (insert directo evita confusión de nombre de tabla)
        foreach ($slots as $slot) {
            \DB::table('booking_slots')->insert([
                'booking_id' => $booking->id,
                'slot_id'    => $slot->id,
                'unit_price' => $slot->unit_price,
            ]);

            // Actualizar estado del slot
            $isPast     = $dayOffset < 0;
            $slotStatus = match(true) {
                $isPast                   => 'completed',
                $status === 'cancelled'   => 'available',
                in_array($status, ['completed','no_show']) => 'completed',
                default                   => 'reserved',
            };
            $slot->update(['status' => $slotStatus, 'booking_id' => $booking->id]);
        }

        // Transacción del anticipo
        Transaction::create([
            'booking_id'     => $booking->id,
            'amount'         => $isWalkin ? $totalPrice : $depositAmount,
            'type'           => $isWalkin ? 'walkin' : 'deposit',
            'payment_method' => $metodo,
            'status'         => 'approved',
        ]);

        // Si completada, agregar transacción del saldo
        if ($status === 'completed' && !$isWalkin) {
            Transaction::create([
                'booking_id'     => $booking->id,
                'amount'         => $balanceDue,
                'type'           => 'balance',
                'payment_method' => 'efectivo',
                'status'         => 'approved',
            ]);
        }

        // Si no-show, retener anticipo (solo registro)
        if ($status === 'no_show') {
            Transaction::create([
                'booking_id'     => $booking->id,
                'amount'         => $depositAmount,
                'type'           => 'deposit',
                'payment_method' => $metodo,
                'status'         => 'approved',
                'notes'          => 'Anticipo retenido por no-show',
            ]);
        }
    }
}
