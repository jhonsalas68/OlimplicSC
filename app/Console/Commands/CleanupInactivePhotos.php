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

            // Si es URL de Cloudinary (ej: empieza con http), idealmente borraríamos vía API 
            // pero si usamos el driver de Cloudinary como disco 'public', el comando anterior funcionaría.
            // Para ser seguros con el paquete cloudinary-laravel:
            if (str_contains($athlete->foto, 'cloudinary')) {
                // Aquí se podría implementar el borrado vía public_id si se guarda
                // Por ahora simplemente quitamos la referencia si es muy antiguo
            }

            $athlete->update(['foto' => null]);
            $count++;
        }

        $this->info("Se han eliminado $count imágenes de atletas inactivos.");
    }
}
