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

        // Extractor de public_id de la URL.
        // Ej: https://res.cloudinary.com/.../upload/v1234/folder/file.ext -> folder/file
        if (preg_match('/upload\/(?:v\d+\/)?([^\.]+)/', $url, $matches)) {
            $publicId = $matches[1];
            try {
                // Cloudinary soporta destrucción automática para imágenes. 
                // Para documentos crudos o PDFs que no fueron transformados, 
                // podría requerir resource_type => raw, por defecto prueba image.
                $ext = pathinfo($url, PATHINFO_EXTENSION);
                $resourceType = in_array(strtolower($ext), ['pdf', 'doc', 'docx']) ? 'raw' : 'image';
                
                // Intenta eliminarlo asumiendo el tipo de recurso correspondiente con la API
                Cloudinary::uploadApi()->destroy($publicId, ['resource_type' => $resourceType]);
            } catch (\Exception $e) {
                // Si falla (ej. era auto pero se guardo distinto), intenta fallback:
                try {
                    $fallback = $resourceType === 'image' ? 'raw' : 'image';
                    Cloudinary::uploadApi()->destroy($publicId, ['resource_type' => $fallback]);
                } catch (\Exception $e2) {
                    Log::error("CloudinaryHelper: Falló al eliminar $url. Error: " . $e2->getMessage());
                }
            }
        }
    }
}
