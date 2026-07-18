<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\Payment;
use App\Models\Training;
use App\Models\User;
use App\Models\Category;
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

        // 1. Recaudación histórica de los últimos 6 meses
        $meses = [];
        $recaudaciones = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $meses[] = $date->translatedFormat('F Y');
            
            $recaudacion = Payment::where('estado_pago', 'pagado')
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('monto');
            $recaudaciones[] = (float) $recaudacion;
        }

        // 2. Distribución de atletas por categoría
        $categorias = Category::withCount('athletes')->get();
        $categoriasLabels = $categorias->pluck('nombre')->toArray();
        $categoriasCounts = $categorias->pluck('athletes_count')->toArray();

        // 3. Estatus de mensualidades (Al Día vs Debe)
        $hoy = now()->toDateString();
        $atletasAlDia = Athlete::where('fecha_vencimiento_habilitacion', '>=', $hoy)->count();
        $atletasDeudores = Athlete::where(function($q) use ($hoy) {
            $q->where('fecha_vencimiento_habilitacion', '<', $hoy)
              ->orWhereNull('fecha_vencimiento_habilitacion');
        })->count();

        // 4. Actividad reciente (Últimos 5 pagos procesados)
        $ultimosPagos = Payment::with('athlete')
            ->where('estado_pago', 'pagado')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalAtletas', 
            'recaudacionMes', 
            'totalEntrenamientos', 
            'usuariosInactivos',
            'meses',
            'recaudaciones',
            'categoriasLabels',
            'categoriasCounts',
            'atletasAlDia',
            'atletasDeudores',
            'ultimosPagos'
        ));
    }
}
