<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = ActivityLog::with('user')->latest();

            // Filtro por usuario
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            // Filtro por acción
            if ($request->filled('action')) {
                $query->where('action', $request->action);
            }

            // Filtro por fecha
            if ($request->filled('date')) {
                $query->whereDate('created_at', $request->date);
            }

            $logs = $query->paginate(30)->withQueryString();
            $users = User::whereHas('logs')->get(); // Solo usuarios que tienen logs
            $actions = ActivityLog::select('action')->distinct()->pluck('action');

            return view('admin.activity_logs.index', compact('logs', 'users', 'actions'));
        } catch (\Exception $e) {
            // Si la tabla no existe o hay error, mostrar una vista vacía o con error amigable
            if (str_contains($e->getMessage(), 'Relation "activity_logs" does not exist') || 
                str_contains($e->getMessage(), 'Table \'activity_logs\' doesn\'t exist')) {
                return view('admin.activity_logs.index', [
                    'logs' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 30),
                    'users' => collect(),
                    'actions' => collect(),
                    'error' => 'La tabla de bitácora no existe. Por favor, ejecuta las migraciones: php artisan migrate'
                ]);
            }
            throw $e; // Re-lanzar si es otro tipo de error
        }
    }
}
