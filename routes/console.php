<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Recalcular categorías de atletas cada día a medianoche
Schedule::command('atletas:recalcular-categorias')->dailyAt('00:05');

// Eliminar PDFs de planificaciones pasadas para no almacenar basura (se ejecuta a medianoche)
Schedule::call(function () {
    // Buscar planificaciones cuya fecha ya pasó y tienen PDF
    $trainings = \App\Models\Training::whereNotNull('file_path_pdf')
        ->whereDate('fecha', '<', now()->toDateString())
        ->get();
        
    foreach ($trainings as $training) {
        \Illuminate\Support\Facades\Storage::disk('public')->delete($training->file_path_pdf);
        $training->update(['file_path_pdf' => null]);
    }
})->dailyAt('00:00')->name('delete-old-training-pdfs')->withoutOverlapping();
