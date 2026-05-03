<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Grupo: marca
            ['key' => 'site_name',        'value' => 'FutGo',              'type' => 'text',    'label' => 'Nombre del sitio',        'group' => 'marca'],
            ['key' => 'site_tagline',      'value' => 'Reservá tu cancha',  'type' => 'text',    'label' => 'Eslogan',                 'group' => 'marca'],
            ['key' => 'site_logo',         'value' => null,                 'type' => 'image',   'label' => 'Logo principal',          'group' => 'marca'],
            ['key' => 'site_logo_dark',    'value' => null,                 'type' => 'image',   'label' => 'Logo modo oscuro',        'group' => 'marca'],
            ['key' => 'site_favicon',      'value' => null,                 'type' => 'image',   'label' => 'Favicon (.ico / .png)',   'group' => 'marca'],
            ['key' => 'site_color',        'value' => '#22c55e',            'type' => 'text',    'label' => 'Color principal (hex)',   'group' => 'marca'],
            // Grupo: general
            ['key' => 'site_email',        'value' => 'hola@futgo.app',     'type' => 'text',    'label' => 'Email de contacto',       'group' => 'general'],
            ['key' => 'site_phone',        'value' => '+51 999 000 000',    'type' => 'text',    'label' => 'Teléfono de contacto',    'group' => 'general'],
            ['key' => 'site_country',      'value' => 'Perú',               'type' => 'text',    'label' => 'País de operación',       'group' => 'general'],
            ['key' => 'site_currency',     'value' => 'S/',                 'type' => 'text',    'label' => 'Símbolo de moneda',       'group' => 'general'],
            ['key' => 'maintenance_mode',  'value' => '0',                  'type' => 'boolean', 'label' => 'Modo mantenimiento',      'group' => 'general'],
        ];

        foreach ($settings as $s) {
            SiteSetting::updateOrCreate(['key' => $s['key']], $s);
        }
    }
}
