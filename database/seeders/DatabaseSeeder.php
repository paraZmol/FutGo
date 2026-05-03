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
            UserCitySeeder::class,   // asigna city_id a users después de crear cities
            VenueSeeder::class,
            OperatingHourSeeder::class,
            SlotSeeder::class,
            BookingSeeder::class,
            ExtraDataSeeder::class,
            ShiftSeeder::class,
            AuditLogSeeder::class,
            NotificationLogSeeder::class,
            SiteSettingSeeder::class,
            DisputeSeeder::class,
        ]);
    }
}
