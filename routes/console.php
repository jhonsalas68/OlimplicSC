<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Recalcular categorías de atletas cada día a medianoche
Schedule::command('atletas:recalcular-categorias')->dailyAt('00:05');

// Copia de seguridad diaria de Postgres via Email
Schedule::command('db:backup')->dailyAt('03:00')->withoutOverlapping();
