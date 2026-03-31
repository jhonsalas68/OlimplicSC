<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\Payment;
use App\Models\Training;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalAtletas = Athlete::count();
        
        $recaudacionMes = Payment::where(function($q) {
            $q->where('mes_correspondiente', 'ilike', now()->format('Y-m') . '%')
              ->orWhereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
        })->where('estado_pago', 'pagado')->sum('monto');
        
        $totalEntrenamientos = Training::count();
        
        $usuariosInactivos = User::where('is_active', false)->count();

        return view('admin.dashboard', compact(
            'totalAtletas', 
            'recaudacionMes', 
            'totalEntrenamientos', 
            'usuariosInactivos'
        ));
    }
}
