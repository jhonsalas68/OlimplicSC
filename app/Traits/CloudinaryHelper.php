<?php

namespace App\Traits;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Log;

trait CloudinaryHelper
{
    /**
     * Delete a file from Cloudinary given its full URL.
     *
     * @param string|null $url The Cloudinary full URL (https://...)
     * @return void
     */
    public function deleteFromCloudinary(?string $url): void
    {
        if (!$url || !str_starts_with($url, 'http')) {
            return;
        }

        // Remove query params if any
        $urlBase = explode('?', $url)[0];

        // Extractor de resource_type y public_id de la URL.
        // Ej: https://res.cloudinary.com/demo/image/upload/v1234/folder/file.jpg
        // Ej: https://res.cloudinary.com/demo/raw/upload/v1234/folder/file.pdf
        if (preg_match('/\/([a-z]+)\/upload\/(?:v\d+\/)?(.+)$/i', $urlBase, $matches)) {
            $resourceType = strtolower($matches[1]); // image, raw, video
            $fullPathWithExt = $matches[2];

            // En Cloudinary, los archivos 'raw' (como PDFs por defecto) requieren la extensión
            // como parte de su public_id. Para 'image' o 'video', no se requiere ni acepta.
            if ($resourceType === 'raw') {
                $publicId = $fullPathWithExt;
            } else {
                // Remover extensión para image/video
                $dir = pathinfo($fullPathWithExt, PATHINFO_DIRNAME);
                $file = pathinfo($fullPathWithExt, PATHINFO_FILENAME);
                $publicId = ($dir === '.') ? $file : $dir . '/' . $file;
            }

            try {
                Cloudinary::uploadApi()->destroy($publicId, ['resource_type' => $resourceType]);
            } catch (\Exception $e) {
                // Si falla, intenta fallback por si hubo una mutación inusual en la API
                try {
                    $fallback = $resourceType === 'image' ? 'raw' : 'image';
                    Cloudinary::uploadApi()->destroy($publicId, ['resource_type' => $fallback]);
                } catch (\Exception $e2) {
                    Log::error("CloudinaryHelper: Falló al eliminar publicId: $publicId de URL $url. Error: " . $e2->getMessage());
                }
            }
        }
    }
}
