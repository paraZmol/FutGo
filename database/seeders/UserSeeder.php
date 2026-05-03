<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── ADMIN ──────────────────────────────────────────────
        User::create([
            'name'              => 'Super Admin',
            'email'             => 'admin@futgo.app',
            'password'          => Hash::make('password'),
            'role'              => 'admin',
            'phone'             => '+51 999 000 001',
            'email_verified_at' => now(),
        ]);

        // ── MODERADORES ────────────────────────────────────────
        User::create([
            'name'              => 'Carlos Ríos',
            'email'             => 'carlos.rios@futgo.app',
            'password'          => Hash::make('password'),
            'role'              => 'admin',
            'phone'             => '+51 987 100 001',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name'              => 'Luisa Vega',
            'email'             => 'luisa.vega@futgo.app',
            'password'          => Hash::make('password'),
            'role'              => 'admin',
            'phone'             => '+51 987 100 002',
            'email_verified_at' => now(),
        ]);

        // ── PARTNERS ───────────────────────────────────────────
        $partners = [
            ['name' => 'Juan Quispe',    'email' => 'juan.quispe@gmail.com',   'phone' => '+51 984 201 001'],
            ['name' => 'María López',    'email' => 'maria.lopez@gmail.com',   'phone' => '+51 984 201 002'],
            ['name' => 'Pedro Vargas',   'email' => 'pedro.vargas@gmail.com',  'phone' => '+51 984 201 003'],
            ['name' => 'Rosa Mamani',    'email' => 'rosa.mamani@gmail.com',   'phone' => '+51 984 201 004'],
            ['name' => 'Jorge Condori',  'email' => 'jorge.condori@gmail.com', 'phone' => '+51 984 201 005'],
        ];

        foreach ($partners as $p) {
            User::create([
                'name'              => $p['name'],
                'email'             => $p['email'],
                'password'          => Hash::make('password'),
                'role'              => 'partner',
                'phone'             => $p['phone'],
                'email_verified_at' => now(),
            ]);
        }

        // ── STAFF ──────────────────────────────────────────────
        $staff = [
            ['name' => 'Pedro Mamani',      'email' => 'pedro.staff@futgo.app',  'phone' => '+51 987 300 001'],
            ['name' => 'Rosa Quispe',       'email' => 'rosa.staff@futgo.app',   'phone' => '+51 987 300 002'],
            ['name' => 'Juan Carlos Flores','email' => 'jcarlos.staff@futgo.app','phone' => '+51 987 300 003'],
        ];

        foreach ($staff as $s) {
            User::create([
                'name'              => $s['name'],
                'email'             => $s['email'],
                'password'          => Hash::make('password'),
                'role'              => 'staff',
                'phone'             => $s['phone'],
                'email_verified_at' => now(),
            ]);
        }

        // ── JUGADORES ──────────────────────────────────────────
        $jugadores = [
            ['name' => 'Mario Quispe',      'email' => 'mario.quispe@gmail.com',    'phone' => '+51 987 400 001'],
            ['name' => 'Luis Torres',       'email' => 'luis.torres@gmail.com',     'phone' => '+51 987 400 002'],
            ['name' => 'Carlos Mamani',     'email' => 'carlos.mamani@gmail.com',   'phone' => '+51 987 400 003'],
            ['name' => 'Ana Gutierrez',     'email' => 'ana.gutierrez@gmail.com',   'phone' => '+51 987 400 004'],
            ['name' => 'Pedro Huanca',      'email' => 'pedro.huanca@gmail.com',    'phone' => '+51 987 400 005'],
            ['name' => 'Roberto Silva',     'email' => 'roberto.silva@gmail.com',   'phone' => '+51 987 400 006'],
            ['name' => 'Jorge Flores',      'email' => 'jorge.flores@gmail.com',    'phone' => '+51 987 400 007'],
            ['name' => 'Miguel Castro',     'email' => 'miguel.castro@gmail.com',   'phone' => '+51 987 400 008'],
            ['name' => 'Sofía Ríos',        'email' => 'sofia.rios@gmail.com',      'phone' => '+51 987 400 009'],
            ['name' => 'Diego Vargas',      'email' => 'diego.vargas@gmail.com',    'phone' => '+51 987 400 010'],
            ['name' => 'Paola Huanca',      'email' => 'paola.huanca@gmail.com',    'phone' => '+51 987 400 011'],
            ['name' => 'Raúl Condori',      'email' => 'raul.condori@gmail.com',    'phone' => '+51 987 400 012'],
            ['name' => 'Valeria Paredes',   'email' => 'valeria.paredes@gmail.com', 'phone' => '+51 987 400 013'],
            ['name' => 'Kevin Apaza',       'email' => 'kevin.apaza@gmail.com',     'phone' => '+51 987 400 014'],
            ['name' => 'Lucía Puma',        'email' => 'lucia.puma@gmail.com',      'phone' => '+51 987 400 015'],
            ['name' => 'Álvaro Turpo',      'email' => 'alvaro.turpo@gmail.com',    'phone' => '+51 987 400 016'],
            ['name' => 'Claudia Ccama',     'email' => 'claudia.ccama@gmail.com',   'phone' => '+51 987 400 017'],
            ['name' => 'Erick Huillca',     'email' => 'erick.huillca@gmail.com',   'phone' => '+51 987 400 018'],
            ['name' => 'Nataly Ccorimanya', 'email' => 'nataly.ccorimanya@gmail.com','phone' => '+51 987 400 019'],
            ['name' => 'Frank Hancco',      'email' => 'frank.hancco@gmail.com',    'phone' => '+51 987 400 020'],
        ];

        foreach ($jugadores as $j) {
            User::create([
                'name'              => $j['name'],
                'email'             => $j['email'],
                'password'          => Hash::make('password'),
                'role'              => 'user',
                'phone'             => $j['phone'],
                'email_verified_at' => now(),
            ]);
        }
    }
}
