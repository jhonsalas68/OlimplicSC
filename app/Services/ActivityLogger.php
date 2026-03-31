<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    public static function log(string $action, string $description, ?Model $model = null, array $properties = []): ?ActivityLog
    {
        try {
            return ActivityLog::create([
                'user_id'     => Auth::id(), // Registrar el usuario actual (si existe)
                'action'      => $action,
                'description' => $description,
                'model_type'  => $model ? get_class($model) : null,
                'model_id'    => $model ? $model->id : null,
                'properties'  => $properties ?: null,
                'ip_address'  => Request::ip(),
                'user_agent'  => Request::userAgent(),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error en bitácora (log): " . $e->getMessage());
            return null;
        }
    }

    /**
     * Versión para cuando el usuario no está autenticado (ej. login fallido)
     */
    public static function logGuest(string $action, string $description, array $properties = []): ?ActivityLog
    {
        try {
            return ActivityLog::create([
                'user_id'     => null,
                'action'      => $action,
                'description' => $description,
                'properties'  => $properties ?: null,
                'ip_address'  => Request::ip(),
                'user_agent'  => Request::userAgent(),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error en bitácora (logGuest): " . $e->getMessage());
            return null;
        }
    }
}
