<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\City;
use App\Models\Field;
use App\Models\OperatingHour;
use App\Models\Slot;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Venue;
use App\Models\VenueStaff;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ExtraDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── PARTNERS EXTRA ──────────────────────────────────────────
        $partners = User::where('role', 'partner')->orderBy('id')->get();
        $cities   = City::whereIn('slug', ['cusco', 'lima', 'arequipa', 'trujillo'])->get()->keyBy('slug');

        // Nuevos partners
        $newPartners = [
            ['name' => 'Fernando Huanca',  'email' => 'fernando.huanca@gmail.com',  'phone' => '+51 984 301 001'],
            ['name' => 'Cecilia Vargas',   'email' => 'cecilia.vargas@gmail.com',   'phone' => '+51 984 301 002'],
            ['name' => 'Rodrigo Apaza',    'email' => 'rodrigo.apaza@gmail.com',    'phone' => '+51 984 301 003'],
        ];

        foreach ($newPartners as $p) {
            User::create(array_merge($p, [
                'password'          => Hash::make('password'),
                'role'              => 'partner',
                'email_verified_at' => now(),
            ]));
        }

        // Recargar partners
        $partners = User::where('role', 'partner')->orderBy('id')->get();

        // ── VENUES EXTRA ────────────────────────────────────────────
        $nuevosVenues = [
            [
                'partner' => $partners[5], // Fernando
                'city'    => $cities['cusco'],
                'data'    => [
                    'name' => 'Canchas El Inca', 'slug' => 'canchas-el-inca',
                    'description' => 'Canchas en San Blas con vista al centro histórico.',
                    'address' => 'Jr. San Blas 240', 'district' => 'San Blas',
                    'latitude' => -13.5158, 'longitude' => -71.9790,
                    'status' => 'active', 'phone' => '+51 984 301 001',
                ],
                'fields' => [
                    ['name' => 'Cancha A', 'sport_type' => 'futbol5', 'surface' => 'sintetico', 'is_covered' => true,  'amenities' => ['Iluminación LED', 'Bar']],
                    ['name' => 'Cancha B', 'sport_type' => 'futbol5', 'surface' => 'sintetico', 'is_covered' => false, 'amenities' => ['Iluminación LED']],
                ],
            ],
            [
                'partner' => $partners[6], // Cecilia
                'city'    => $cities['lima'],
                'data'    => [
                    'name' => 'SportZone San Isidro', 'slug' => 'sportzone-san-isidro',
                    'description' => 'Moderno complejo en San Isidro con canchas de primer nivel.',
                    'address' => 'Av. Javier Prado Este 1200', 'district' => 'San Isidro',
                    'latitude' => -12.0953, 'longitude' => -77.0228,
                    'status' => 'active', 'phone' => '+51 984 301 002',
                ],
                'fields' => [
                    ['name' => 'Cancha 1', 'sport_type' => 'futbol5',  'surface' => 'sintetico', 'is_covered' => true,  'amenities' => ['Iluminación LED', 'Vestuarios', 'Duchas', 'WiFi']],
                    ['name' => 'Cancha 2', 'sport_type' => 'futbol7',  'surface' => 'sintetico', 'is_covered' => true,  'amenities' => ['Iluminación LED', 'Vestuarios']],
                    ['name' => 'Cancha 3', 'sport_type' => 'futbol11', 'surface' => 'natural',   'is_covered' => false, 'amenities' => ['Estacionamiento']],
                ],
            ],
            [
                'partner' => $partners[7], // Rodrigo
                'city'    => $cities['trujillo'],
                'data'    => [
                    'name' => 'Canchas La Libertad', 'slug' => 'canchas-la-libertad',
                    'description' => 'El mejor complejo de Trujillo, a pasos de la Plaza Mayor.',
                    'address' => 'Av. España 456', 'district' => 'Centro Histórico',
                    'latitude' => -8.1091, 'longitude' => -79.0275,
                    'status' => 'active', 'phone' => '+51 984 301 003',
                ],
                'fields' => [
                    ['name' => 'Cancha 1', 'sport_type' => 'futbol5', 'surface' => 'sintetico', 'is_covered' => true,  'amenities' => ['Iluminación LED', 'Bar']],
                    ['name' => 'Cancha 2', 'sport_type' => 'futbol7', 'surface' => 'sintetico', 'is_covered' => false, 'amenities' => ['Iluminación LED', 'Estacionamiento']],
                ],
            ],
        ];

        foreach ($nuevosVenues as $item) {
            $venue = Venue::create(array_merge($item['data'], [
                'user_id' => $item['partner']->id,
                'city_id' => $item['city']->id,
            ]));
            foreach ($item['fields'] as $fd) {
                $field = Field::create(array_merge($fd, ['venue_id' => $venue->id]));
                $this->crearHorarios($field);
                $this->crearSlots($field);
            }
        }

        // ── JUGADORES EXTRA ─────────────────────────────────────────
        $jugadoresExtra = [
            ['name' => 'Sebastián Ccalla',  'email' => 's.ccalla@gmail.com',   'phone' => '+51 987 500 001'],
            ['name' => 'Milagros Quispe',   'email' => 'm.quispe@gmail.com',   'phone' => '+51 987 500 002'],
            ['name' => 'Javier Huillca',    'email' => 'j.huillca@gmail.com',  'phone' => '+51 987 500 003'],
            ['name' => 'Carmen Puma',       'email' => 'c.puma@gmail.com',     'phone' => '+51 987 500 004'],
            ['name' => 'Antonio Ccorimanya','email' => 'a.ccorimanya@gmail.com','phone' => '+51 987 500 005'],
            ['name' => 'Gabriela Turpo',    'email' => 'g.turpo@gmail.com',    'phone' => '+51 987 500 006'],
            ['name' => 'Marco Condori',     'email' => 'm.condori@gmail.com',  'phone' => '+51 987 500 007'],
            ['name' => 'Patricia Hancco',   'email' => 'p.hancco@gmail.com',   'phone' => '+51 987 500 008'],
            ['name' => 'César Apaza',       'email' => 'c.apaza@gmail.com',    'phone' => '+51 987 500 009'],
            ['name' => 'Rosario Mamani',    'email' => 'r.mamani@gmail.com',   'phone' => '+51 987 500 010'],
        ];

        foreach ($jugadoresExtra as $j) {
            User::create(array_merge($j, [
                'password'          => Hash::make('password'),
                'role'              => 'user',
                'email_verified_at' => now(),
            ]));
        }

        // ── BOOKINGS EXTRA (más variedad) ───────────────────────────
        $jugadores = User::where('role', 'user')->orderBy('id')->get();
        $fields    = Field::all();

        // Muchas reservas completadas en los últimos 30 días
        $combos = [
            [$jugadores[0],  $fields[0],  -15, 19, 'yape',    2],
            [$jugadores[1],  $fields[1],  -14, 20, 'tarjeta', 1],
            [$jugadores[2],  $fields[2],  -13, 18, 'plin',    1],
            [$jugadores[3],  $fields[3],  -12, 20, 'yape',    1],
            [$jugadores[4],  $fields[4],  -11, 19, 'tarjeta', 2],
            [$jugadores[5],  $fields[0],  -10, 20, 'yape',    1],
            [$jugadores[6],  $fields[1],  -9,  19, 'plin',    1],
            [$jugadores[7],  $fields[2],  -8,  20, 'yape',    1],
            [$jugadores[8],  $fields[3],  -7,  18, 'tarjeta', 1],
            [$jugadores[9],  $fields[4],  -6,  19, 'yape',    2],
            [$jugadores[10], $fields[0],  -5,  20, 'plin',    1],
            [$jugadores[11], $fields[1],  -4,  19, 'yape',    1],
            [$jugadores[12], $fields[2],  -3,  20, 'tarjeta', 1],
            [$jugadores[13], $fields[3],  -2,  18, 'yape',    1],
            [$jugadores[14], $fields[0],  -1,  19, 'plin',    1],
            // No-shows para el análisis
            [$jugadores[15], $fields[1],  -5,  10, 'yape',    1, 'no_show'],
            [$jugadores[16], $fields[2],  -3,  8,  'tarjeta', 1, 'no_show'],
            // Reservas futuras
            [$jugadores[0],  $fields[5],  2,   19, 'yape',    1],
            [$jugadores[2],  $fields[6],  3,   20, 'tarjeta', 1],
            [$jugadores[4],  $fields[7],  4,   18, 'plin',    2],
            [$jugadores[6],  $fields[8],  5,   19, 'yape',    1],
            [$jugadores[8],  $fields[9],  6,   20, 'tarjeta', 1],
        ];

        foreach ($combos as $combo) {
            [$jugador, $field, $dayOffset, $hora, $metodo, $numSlots] = $combo;
            $status = $combo[6] ?? ($dayOffset < 0 ? 'completed' : 'confirmed');
            $this->crearBooking($jugador, $field, $status, $metodo, $numSlots, $dayOffset, $hora);
        }
    }

    private function crearHorarios(Field $field): void
    {
        $isF11 = $field->sport_type === 'futbol11';
        $isF7  = $field->sport_type === 'futbol7';
        $pDay  = $isF11 ? 120.00 : ($isF7 ? 90.00 : 70.00);
        $pNight= $isF11 ? 150.00 : ($isF7 ? 110.00 : 85.00);
        $dep   = round($pDay * 0.35, 2);

        for ($d = 1; $d <= 5; $d++) {
            OperatingHour::create(['field_id'=>$field->id,'day_of_week'=>$d,'opens_at'=>'07:00:00','closes_at'=>'22:00:00','price_day'=>$pDay,'price_night'=>$pNight,'deposit_amount'=>$dep,'is_active'=>true]);
        }
        OperatingHour::create(['field_id'=>$field->id,'day_of_week'=>6,'opens_at'=>'07:00:00','closes_at'=>'23:00:00','price_day'=>$pDay,'price_night'=>$pNight+10,'deposit_amount'=>$dep,'is_active'=>true]);
        OperatingHour::create(['field_id'=>$field->id,'day_of_week'=>0,'opens_at'=>'08:00:00','closes_at'=>'20:00:00','price_day'=>$pDay-5,'price_night'=>$pNight,'deposit_amount'=>$dep,'is_active'=>true]);
    }

    private function crearSlots(Field $field): void
    {
        $field->load('operatingHours');
        for ($offset = -7; $offset < 7; $offset++) {
            $date = Carbon::today()->addDays($offset);
            $dow  = (int) $date->dayOfWeek;
            $sched = $field->operatingHours->firstWhere('day_of_week', $dow);
            if (!$sched) continue;

            $open  = (int) substr($sched->opens_at, 0, 2);
            $close = (int) substr($sched->closes_at, 0, 2);

            for ($h = $open; $h < $close; $h++) {
                $status = $offset < 0 ? 'completed' : 'available';
                $price  = $h >= 18 ? (float)$sched->price_night : (float)$sched->price_day;
                Slot::create([
                    'field_id'   => $field->id,
                    'starts_at'  => $date->copy()->setTime($h, 0),
                    'ends_at'    => $date->copy()->setTime($h+1, 0),
                    'status'     => $status,
                    'unit_price' => $price,
                ]);
            }
        }
    }

    private function crearBooking($jugador, Field $field, string $status, string $metodo, int $numSlots, int $dayOffset, int $hora): void
    {
        $fecha       = Carbon::today()->addDays($dayOffset)->toDateString();
        $buscarStatus= $dayOffset < 0 ? 'completed' : 'available';

        $slots = Slot::where('field_id', $field->id)
            ->where('status', $buscarStatus)
            ->whereNull('booking_id')
            ->whereDate('starts_at', $fecha)
            ->where('starts_at', '>=', Carbon::today()->addDays($dayOffset)->setTime($hora, 0))
            ->orderBy('starts_at')
            ->take($numSlots)
            ->get();

        if ($slots->count() < $numSlots) return;

        $total   = $slots->sum('unit_price');
        $deposit = round($total * 0.35, 2);
        $balance = round($total - $deposit, 2);

        $booking = Booking::create([
            'user_id'        => $jugador->id,
            'field_id'       => $field->id,
            'status'         => $status,
            'total_price'    => $total,
            'deposit_amount' => $deposit,
            'balance_due'    => $balance,
            'payment_status' => 'paid',
            'payment_method' => $metodo,
            'is_walkin'      => false,
        ]);

        foreach ($slots as $slot) {
            DB::table('booking_slots')->insert(['booking_id'=>$booking->id,'slot_id'=>$slot->id,'unit_price'=>$slot->unit_price]);
            $slot->update(['status' => in_array($status,['completed','no_show']) ? 'completed' : 'reserved', 'booking_id' => $booking->id]);
        }

        Transaction::create(['booking_id'=>$booking->id,'amount'=>$deposit,'type'=>'deposit','payment_method'=>$metodo,'status'=>'approved']);

        if ($status === 'completed') {
            Transaction::create(['booking_id'=>$booking->id,'amount'=>$balance,'type'=>'balance','payment_method'=>'efectivo','status'=>'approved']);
        }
    }
}
