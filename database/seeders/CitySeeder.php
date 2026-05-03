<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            // Activas
            ['name' => 'Huaraz',    'department' => 'Áncash',     'slug' => 'huaraz',    'latitude' => -9.5300,  'longitude' => -77.5283, 'is_active' => true],
            ['name' => 'Cusco',     'department' => 'Cusco',      'slug' => 'cusco',     'latitude' => -13.5170, 'longitude' => -71.9785, 'is_active' => true],
            ['name' => 'Lima',      'department' => 'Lima',       'slug' => 'lima',      'latitude' => -12.0464, 'longitude' => -77.0428, 'is_active' => true],
            ['name' => 'Arequipa',  'department' => 'Arequipa',   'slug' => 'arequipa',  'latitude' => -16.4090, 'longitude' => -71.5375, 'is_active' => true],
            ['name' => 'Trujillo',  'department' => 'La Libertad','slug' => 'trujillo',  'latitude' => -8.1116,  'longitude' => -79.0288, 'is_active' => true],
            // Próximamente
            ['name' => 'Piura',     'department' => 'Piura',      'slug' => 'piura',     'latitude' => -5.1945,  'longitude' => -80.6328, 'is_active' => false],
            ['name' => 'Iquitos',   'department' => 'Loreto',     'slug' => 'iquitos',   'latitude' => -3.7437,  'longitude' => -73.2516, 'is_active' => false],
            ['name' => 'Chiclayo',  'department' => 'Lambayeque', 'slug' => 'chiclayo',  'latitude' => -6.7764,  'longitude' => -79.8408, 'is_active' => false],
            ['name' => 'Puno',      'department' => 'Puno',       'slug' => 'puno',      'latitude' => -15.8402, 'longitude' => -70.0219, 'is_active' => false],
            ['name' => 'Tacna',     'department' => 'Tacna',      'slug' => 'tacna',     'latitude' => -18.0146, 'longitude' => -70.2536, 'is_active' => false],
        ];

        foreach ($cities as $city) {
            City::create($city);
        }
    }
}
