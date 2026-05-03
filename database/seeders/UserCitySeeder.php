<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserCitySeeder extends Seeder
{
    public function run(): void
    {
        $cusco    = City::where('slug', 'cusco')->first();
        $lima     = City::where('slug', 'lima')->first();
        $huaraz   = City::where('slug', 'huaraz')->first();
        $arequipa = City::where('slug', 'arequipa')->first();
        $trujillo = City::where('slug', 'trujillo')->first();

        // Asignar ciudades a jugadores por email
        $asignaciones = [
            // Partners
            'juan.quispe@gmail.com'   => $huaraz,
            'maria.lopez@gmail.com'   => $cusco,
            'pedro.vargas@gmail.com'  => $cusco,
            'rosa.mamani@gmail.com'   => $lima,
            'jorge.condori@gmail.com' => $arequipa,
            // Staff
            'pedro.staff@futgo.app'   => $huaraz,
            'rosa.staff@futgo.app'    => $cusco,
            'jcarlos.staff@futgo.app' => $cusco,
            // Jugadores — distribución realista
            'mario.quispe@gmail.com'  => $huaraz,
            'luis.torres@gmail.com'   => $cusco,
            'carlos.mamani@gmail.com' => $cusco,
            'ana.gutierrez@gmail.com' => $lima,
            'pedro.huanca@gmail.com'  => $arequipa,
            'roberto.silva@gmail.com' => $lima,
            'jorge.flores@gmail.com'  => $cusco,
            'miguel.castro@gmail.com' => $lima,
            'sofia.rios@gmail.com'    => $cusco,
            'diego.vargas@gmail.com'  => $trujillo,
            'paola.huanca@gmail.com'  => $arequipa,
            'raul.condori@gmail.com'  => $cusco,
            'valeria.paredes@gmail.com'=> $lima,
            'kevin.apaza@gmail.com'   => $huaraz,
            'lucia.puma@gmail.com'    => $cusco,
            'alvaro.turpo@gmail.com'  => $cusco,
            'claudia.ccama@gmail.com' => $lima,
            'erick.huillca@gmail.com' => $arequipa,
            'nataly.ccorimanya@gmail.com' => $cusco,
            'frank.hancco@gmail.com'  => $huaraz,
        ];

        foreach ($asignaciones as $email => $city) {
            if (!$city) continue;
            User::where('email', $email)->update(['city_id' => $city->id]);
        }
    }
}
