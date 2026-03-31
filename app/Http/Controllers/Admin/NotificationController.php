<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /** Obtener notificaciones no leídas para el navbar */
    public function index()
    {
        $notifications = Auth::user()->unreadNotifications->map(function($n) {
            return [
                'id' => $n->id,
                'message' => $n->data['message'] ?? 'Nueva planificación disponible',
                'url' => $n->data['file_path_pdf'] ?? '#',
                'created_at' => $n->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'notifications' => $notifications,
            'count' => $notifications->count()
        ]);
    }

    /** Marcar como leída y retornar el enlace */
    public function read($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'url' => $notification->data['file_path_pdf'] ?? '#'
        ]);
    }

    /** Eliminar/Descartar la notificación */
    public function dismiss($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();

        return response()->json(['success' => true]);
    }
}
