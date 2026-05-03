<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CitySeeder::class,
            VenueSeeder::class,
            OperatingHourSeeder::class,
            SlotSeeder::class,
            BookingSeeder::class,
        ]);
    }
}
