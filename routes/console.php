<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// RF-03: Ventana móvil de 30 días — corre a las 03:00 UTC diariamente
Schedule::command('slots:roll-window')->dailyAt('03:00');
