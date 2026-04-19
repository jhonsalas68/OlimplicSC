<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupInactivePhotos extends Command
{
    protected $signature = 'app:cleanup-inactive-photos';
    protected $description = 'Borra imágenes de atletas inhabilitados por más de 1 año';

    public function handle()
    {
        $count = 0;
        $oneYearAgo = now()->subYear();

        $athletes = \App\Models\Athlete::where('habilitado_booleano', false)
            ->where('updated_at', '<', $oneYearAgo)
            ->whereNotNull('foto')
            ->get();

        foreach ($athletes as $athlete) {
            // Borrar de storage local si existe
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($athlete->foto)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($athlete->foto);
            }

            // Si es una URL externa antigua (ej: empieza con http), no podemos eliminarla automáticamente desde el disco R2.
            // Por ahora simplemente quitamos la referencia si es muy antiguo.

            $athlete->update(['foto' => null]);
            $count++;
        }

        $this->info("Se han eliminado $count imágenes de atletas inactivos.");
    }
}
