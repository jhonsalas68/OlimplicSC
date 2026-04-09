<?php

namespace App\Jobs;

use App\Models\Athlete;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class UploadAthletePhotoToCloudinary implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $athlete;
    public $localFilePath;

    /**
     * Create a new job instance.
     */
    public function __construct(Athlete $athlete, string $localFilePath)
    {
        $this->athlete = $athlete;
        $this->localFilePath = $localFilePath;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            if (Storage::disk('local')->exists($this->localFilePath)) {
                $fullPath = storage_path('app/' . $this->localFilePath);
                
                \Illuminate\Support\Facades\Log::info('Subiendo foto a Cloudinary en segundo plano... ' . $fullPath);
                
                $response = Cloudinary::uploadApi()->upload($fullPath, [
                    'folder' => 'athletes'
                ]);

                // Actualizar la url de la foto
                $this->athlete->update([
                    'foto' => $response['secure_url']
                ]);

                // Eliminar el archivo local temporal
                Storage::disk('local')->delete($this->localFilePath);
                
                \Illuminate\Support\Facades\Log::info('Foto subida en segundo plano y guardada: ' . $response['secure_url']);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al subir foto a Cloudinary en job: ' . $e->getMessage());
        }
    }
}
