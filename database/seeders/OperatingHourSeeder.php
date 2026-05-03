<?php

namespace Database\Seeders;

use App\Models\Field;
use App\Models\OperatingHour;
use Illuminate\Database\Seeder;

class OperatingHourSeeder extends Seeder
{
    public function run(): void
    {
        $fields = Field::all();

        foreach ($fields as $field) {
            $isF11 = $field->sport_type === 'futbol11';
            $isF7  = $field->sport_type === 'futbol7';

            // Precio base según tipo
            $priceDay   = $isF11 ? 120.00 : ($isF7 ? 90.00 : 70.00);
            $priceNight = $isF11 ? 150.00 : ($isF7 ? 110.00 : 85.00);
            $deposit    = round($priceDay * 0.35, 2); // 35% anticipo

            // Lunes a viernes (1-5): 07:00 - 22:00
            for ($day = 1; $day <= 5; $day++) {
                OperatingHour::create([
                    'field_id'       => $field->id,
                    'day_of_week'    => $day,
                    'opens_at'       => '07:00:00',
                    'closes_at'      => '22:00:00',
                    'price_day'      => $priceDay,
                    'price_night'    => $priceNight,
                    'deposit_amount' => $deposit,
                    'is_active'      => true,
                ]);
            }

            // Sábado (6): 07:00 - 23:00 — precio noche más caro
            OperatingHour::create([
                'field_id'       => $field->id,
                'day_of_week'    => 6,
                'opens_at'       => '07:00:00',
                'closes_at'      => '23:00:00',
                'price_day'      => $priceDay,
                'price_night'    => $priceNight + 10,
                'deposit_amount' => $deposit,
                'is_active'      => true,
            ]);

            // Domingo (0): 08:00 - 20:00
            OperatingHour::create([
                'field_id'       => $field->id,
                'day_of_week'    => 0,
                'opens_at'       => '08:00:00',
                'closes_at'      => '20:00:00',
                'price_day'      => $priceDay - 5,
                'price_night'    => $priceNight,
                'deposit_amount' => $deposit,
                'is_active'      => true,
            ]);
        }
    }
}
