<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Field;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VenueSeeder extends Seeder
{
    public function run(): void
    {
        $huaraz   = City::where('slug', 'huaraz')->first();
        $cusco    = City::where('slug', 'cusco')->first();
        $lima     = City::where('slug', 'lima')->first();
        $arequipa = City::where('slug', 'arequipa')->first();

        // Partners en orden de creación en UserSeeder
        $partners = User::where('role', 'partner')->orderBy('id')->get();
        $staff    = User::where('role', 'staff')->orderBy('id')->get();

        $venues = [
            // ── HUARAZ ─────────────────────────────────────────────────
            [
                'partner' => $partners[0], // Juan Quispe
                'city'    => $huaraz,
                'data'    => [
                    'name'        => 'Canchas Yungay',
                    'slug'        => 'canchas-yungay',
                    'description' => 'Complejo deportivo en el corazón de Huaraz con canchas de pasto sintético de última generación.',
                    'address'     => 'Jr. Yungay 342',
                    'district'    => 'Huaraz',
                    'latitude'    => -9.5278,
                    'longitude'   => -77.5269,
                    'status'      => 'active',
                    'phone'       => '+51 984 201 001',
                ],
                'fields'  => [
                    ['name' => 'Cancha 1', 'sport_type' => 'futbol5', 'surface' => 'sintetico', 'is_covered' => true,  'amenities' => ['Iluminación LED', 'Vestuarios', 'Bar']],
                    ['name' => 'Cancha 2', 'sport_type' => 'futbol5', 'surface' => 'sintetico', 'is_covered' => true,  'amenities' => ['Iluminación LED', 'Vestuarios']],
                    ['name' => 'Cancha 3', 'sport_type' => 'futbol7', 'surface' => 'sintetico', 'is_covered' => false, 'amenities' => ['Iluminación LED', 'Estacionamiento']],
                ],
                'staff'   => [$staff[0]], // Pedro Mamani
            ],

            // ── HUARAZ 2 (segundo venue de Juan Quispe) ────────────────
            [
                'partner' => $partners[0], // Juan Quispe — segundo complejo
                'city'    => $huaraz,
                'data'    => [
                    'name'        => 'Canchas Huaraz Centro',
                    'slug'        => 'canchas-huaraz-centro',
                    'description' => 'Canchas en el centro de Huaraz, a pasos de la Plaza de Armas.',
                    'address'     => 'Jr. San Martín 150',
                    'district'    => 'Huaraz',
                    'latitude'    => -9.5259,
                    'longitude'   => -77.5306,
                    'status'      => 'active',
                    'phone'       => '+51 984 201 006',
                ],
                'fields'  => [
                    ['name' => 'Cancha A', 'sport_type' => 'futbol5', 'surface' => 'sintetico', 'is_covered' => false, 'amenities' => ['Iluminación LED']],
                    ['name' => 'Cancha B', 'sport_type' => 'futbol7', 'surface' => 'sintetico', 'is_covered' => false, 'amenities' => ['Iluminación LED', 'Estacionamiento']],
                ],
                'staff'   => [],
            ],

            // ── CUSCO ──────────────────────────────────────────────────
            [
                'partner' => $partners[1], // María López
                'city'    => $cusco,
                'data'    => [
                    'name'        => 'Complejo Deportivo El 10',
                    'slug'        => 'complejo-deportivo-el-10',
                    'description' => 'El mejor complejo de Wanchaq con 4 canchas de pasto sintético. Iluminación LED para partidos nocturnos.',
                    'address'     => 'Av. Los Incas 342',
                    'district'    => 'Wanchaq',
                    'latitude'    => -13.5233,
                    'longitude'   => -71.9678,
                    'status'      => 'active',
                    'phone'       => '+51 984 201 002',
                ],
                'fields'  => [
                    ['name' => 'Cancha 1', 'sport_type' => 'futbol5', 'surface' => 'sintetico', 'is_covered' => true,  'amenities' => ['Iluminación LED', 'Vestuarios', 'WiFi']],
                    ['name' => 'Cancha 2', 'sport_type' => 'futbol5', 'surface' => 'sintetico', 'is_covered' => true,  'amenities' => ['Iluminación LED', 'Vestuarios']],
                    ['name' => 'Cancha 3', 'sport_type' => 'futbol7', 'surface' => 'natural',   'is_covered' => false, 'amenities' => ['Estacionamiento']],
                    ['name' => 'Cancha 4', 'sport_type' => 'futbol7', 'surface' => 'sintetico', 'is_covered' => false, 'amenities' => ['Iluminación LED']],
                ],
                'staff'   => [$staff[1]], // Rosa Quispe
            ],

            [
                'partner' => $partners[2], // Pedro Vargas
                'city'    => $cusco,
                'data'    => [
                    'name'        => 'El Rey del Gras',
                    'slug'        => 'el-rey-del-gras',
                    'description' => 'Canchas de pasto natural en San Sebastián. El favorito de los equipos de la zona.',
                    'address'     => 'Av. de la Cultura 890',
                    'district'    => 'San Sebastián',
                    'latitude'    => -13.5312,
                    'longitude'   => -71.9456,
                    'status'      => 'active',
                    'phone'       => '+51 984 201 003',
                ],
                'fields'  => [
                    ['name' => 'Cancha 1', 'sport_type' => 'futbol5', 'surface' => 'natural',   'is_covered' => false, 'amenities' => ['Vestuarios']],
                    ['name' => 'Cancha 2', 'sport_type' => 'futbol5', 'surface' => 'sintetico', 'is_covered' => true,  'amenities' => ['Iluminación LED', 'Bar']],
                ],
                'staff'   => [$staff[2]], // Juan Carlos Flores
            ],

            // ── LIMA ───────────────────────────────────────────────────
            [
                'partner' => $partners[3], // Rosa Mamani
                'city'    => $lima,
                'data'    => [
                    'name'        => 'Arena Sports Miraflores',
                    'slug'        => 'arena-sports-miraflores',
                    'description' => 'Complejo premium en Miraflores con canchas de última tecnología.',
                    'address'     => 'Av. Larco 780',
                    'district'    => 'Miraflores',
                    'latitude'    => -12.1219,
                    'longitude'   => -77.0282,
                    'status'      => 'active',
                    'phone'       => '+51 984 201 004',
                ],
                'fields'  => [
                    ['name' => 'Cancha 1', 'sport_type' => 'futbol5',  'surface' => 'sintetico', 'is_covered' => true,  'amenities' => ['Iluminación LED', 'Vestuarios', 'Duchas', 'WiFi']],
                    ['name' => 'Cancha 2', 'sport_type' => 'futbol5',  'surface' => 'sintetico', 'is_covered' => true,  'amenities' => ['Iluminación LED', 'Vestuarios', 'Duchas']],
                    ['name' => 'Cancha 3', 'sport_type' => 'futbol7',  'surface' => 'sintetico', 'is_covered' => false, 'amenities' => ['Iluminación LED', 'Estacionamiento']],
                    ['name' => 'Cancha 4', 'sport_type' => 'futbol11', 'surface' => 'natural',   'is_covered' => false, 'amenities' => ['Estacionamiento']],
                ],
                'staff'   => [],
            ],

            // ── AREQUIPA ───────────────────────────────────────────────
            [
                'partner' => $partners[4], // Jorge Condori
                'city'    => $arequipa,
                'data'    => [
                    'name'        => 'Canchas El Volcán',
                    'slug'        => 'canchas-el-volcan',
                    'description' => 'Complejo deportivo con vista al Misti. Las mejores canchas de Arequipa.',
                    'address'     => 'Av. Ejército 1200',
                    'district'    => 'Cayma',
                    'latitude'    => -16.3892,
                    'longitude'   => -71.5350,
                    'status'      => 'active',
                    'phone'       => '+51 984 201 005',
                ],
                'fields'  => [
                    ['name' => 'Cancha 1', 'sport_type' => 'futbol5', 'surface' => 'sintetico', 'is_covered' => true,  'amenities' => ['Iluminación LED', 'Vestuarios', 'Bar']],
                    ['name' => 'Cancha 2', 'sport_type' => 'futbol7', 'surface' => 'sintetico', 'is_covered' => false, 'amenities' => ['Iluminación LED']],
                ],
                'staff'   => [],
            ],
        ];

        foreach ($venues as $item) {
            $venue = Venue::create(array_merge($item['data'], [
                'user_id' => $item['partner']->id,
                'city_id' => $item['city']->id,
            ]));

            // Canchas
            foreach ($item['fields'] as $fieldData) {
                Field::create(array_merge($fieldData, ['venue_id' => $venue->id]));
            }

            // Staff asignado
            foreach ($item['staff'] as $staffUser) {
                $venue->staff()->attach($staffUser->id, ['active' => true]);
            }
        }
    }
}
