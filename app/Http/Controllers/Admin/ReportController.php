<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $rango = $request->get('rango', 'mes'); // hoy, semana, mes, anio
        $metodo = $request->get('metodo', 'todos'); // efectivo, qr, todos
        
        $query = \App\Models\Payment::query()->where('estado_pago', 'pagado');
        
        // Filtro de Tiempo
        if ($rango === 'hoy') {
            $query->whereDate('created_at', now()->toDateString());
        } elseif ($rango === 'semana') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($rango === 'mes') {
            $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
        } elseif ($rango === 'anio') {
            $query->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()]);
        } elseif ($rango === 'personalizado' && $request->filled('desde') && $request->filled('hasta')) {
            $desde = Carbon::parse($request->desde)->startOfDay();
            $hasta = Carbon::parse($request->hasta)->endOfDay();
            $query->whereBetween('created_at', [$desde, $hasta]);
        }
        
        // Filtro Método de Pago
        if ($metodo !== 'todos') {
            $query->where('metodo_pago', $metodo);
        }
        
        $pagosFiltrados = $query->with(['athlete', 'cobrador'])->latest()->get();
        
        $ingresosMensualidades = $pagosFiltrados->where('concepto', 'mensualidad')->sum('monto');
        $ingresosArticulos = $pagosFiltrados->where('concepto', '!=', 'mensualidad')->sum('monto');
        $totalIngresos = $ingresosMensualidades + $ingresosArticulos;

        $stats = [
            'total_atletas' => \App\Models\Athlete::count(),
            'atletas_activos' => \App\Models\Athlete::where('habilitado_booleano', true)->count(),
            'por_categoria' => \App\Models\Category::withCount('athletes')->get(),
            
            // Nuevas variables financieras
            'rango' => $rango,
            'desde' => $request->desde,
            'hasta' => $request->hasta,
            'metodo' => $metodo,
            'ingresos_mensualidades' => $ingresosMensualidades,
            'ingresos_articulos' => $ingresosArticulos,
            'total_ingresos' => $totalIngresos,
            'pagos' => $pagosFiltrados,
        ];

        return view('admin.reports.index', compact('stats'));
    }

    public function exportExcel(Request $request)
    {
        $rango = $request->get('rango', 'mes');
        $metodo = $request->get('metodo', 'todos');
        
        $query = \App\Models\Payment::query()->where('estado_pago', 'pagado');
        
        if ($rango === 'hoy') {
            $query->whereDate('created_at', now()->toDateString());
        } elseif ($rango === 'semana') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($rango === 'mes') {
            $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
        } elseif ($rango === 'anio') {
            $query->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()]);
        } elseif ($rango === 'personalizado' && $request->filled('desde') && $request->filled('hasta')) {
            $desde = Carbon::parse($request->desde)->startOfDay();
            $hasta = Carbon::parse($request->hasta)->endOfDay();
            $query->whereBetween('created_at', [$desde, $hasta]);
        }
        
        if ($metodo !== 'todos') {
            $query->where('metodo_pago', $metodo);
        }
        
        $pagosFiltrados = $query->with(['athlete.category'])->latest()->get();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ReportExport($pagosFiltrados, $rango, $metodo),
            'Reporte_Financiero_' . now()->format('Y_m_d_H_i') . '.xlsx'
        );
    }
}
