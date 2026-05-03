<?php

namespace Database\Seeders;

use App\Models\Field;
use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SlotSeeder extends Seeder
{
    public function run(): void
    {
        $fields = Field::with('operatingHours')->get();
        $today  = Carbon::today();

        // Genera slots: 7 días pasados + hoy + 7 días futuros
        for ($dayOffset = -7; $dayOffset < 7; $dayOffset++) {
            $date      = $today->copy()->addDays($dayOffset);
            $dayOfWeek = (int) $date->dayOfWeek; // 0=domingo

            foreach ($fields as $field) {
                $schedule = $field->operatingHours
                    ->firstWhere('day_of_week', $dayOfWeek);

                if (!$schedule || !$schedule->is_active) continue;

                $openHour  = (int) substr($schedule->opens_at, 0, 2);
                $closeHour = (int) substr($schedule->closes_at, 0, 2);

                for ($hour = $openHour; $hour < $closeHour; $hour++) {
                    $startsAt = $date->copy()->setTime($hour, 0);
                    $endsAt   = $date->copy()->setTime($hour + 1, 0);
                    $price    = $hour >= 18
                        ? (float) $schedule->price_night
                        : (float) $schedule->price_day;

                    // Slots pasados (hoy antes de la hora actual) → completed
                    // Algunos del pasado reciente → reserved (para simular reservas)
                    // Resto → available
                    $status = 'available';

                    if ($dayOffset < 0) {
                        // Días pasados — todos completados (el BookingSeeder los marcará como reserved donde haya booking)
                        $status = 'completed';
                    } elseif ($dayOffset === 0 && $hour < now()->hour) {
                        $status = 'completed';
                    } elseif ($dayOffset === 0 && $hour >= now()->hour) {
                        $status = ($hour === 20 || $hour === 21) ? 'reserved' : 'available';
                    } elseif ($dayOffset === 1) {
                        $status = ($hour >= 19 && $hour <= 21) ? 'reserved' : 'available';
                    } elseif ($dayOffset >= 2) {
                        $status = (($field->id + $hour + $dayOffset) % 5 === 0) ? 'reserved' : 'available';
                    }

                    Slot::create([
                        'field_id'   => $field->id,
                        'booking_id' => null,
                        'starts_at'  => $startsAt,
                        'ends_at'    => $endsAt,
                        'status'     => $status,
                        'unit_price' => $price,
                    ]);
                }
            }
        }
    }
}
