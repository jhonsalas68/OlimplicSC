<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait FileStorageHelper
{
    /**
     * Delete a file from Cloudflare R2 given its full URL.
     *
     * @param string|null $url The full URL (https://...)
     * @return void
     */
    public function deleteFile(?string $url): void
    {
        if (!$url || !str_starts_with($url, 'http')) {
            return;
        }

        $urlBase = explode('?', $url)[0];

        // LÓGICA PARA CLOUDFLARE R2
        $r2Url = config('filesystems.disks.r2.url');
        if ($r2Url && str_contains($urlBase, parse_url($r2Url, PHP_URL_HOST))) {
            $path = str_replace(rtrim($r2Url, '/') . '/', '', $urlBase);
            try {
                Storage::disk('r2')->delete($path);
            } catch (\Exception $e) {
                Log::error("R2: Falló al eliminar path: $path. Error: " . $e->getMessage());
            }
        }
    }

    /**
     * Alias para compatibilidad parcial (opcional)
     */
    public function deleteFromCloudinary(?string $url): void
    {
        $this->deleteFile($url);
    }
}
