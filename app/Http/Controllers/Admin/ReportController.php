<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $mesActual = now()->format('Y-m');
        
        $stats = [
            'total_atletas' => \App\Models\Athlete::count(),
            'atletas_activos' => \App\Models\Athlete::where('habilitado_booleano', true)->count(),
            'por_categoria' => \App\Models\Category::withCount('athletes')->get(),
            'recaudacion_mes' => \App\Models\Payment::where('mes_correspondiente', 'like', $mesActual . '%')
                ->orWhereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('monto'),
        ];

        return view('admin.reports.index', compact('stats'));
    }
}
