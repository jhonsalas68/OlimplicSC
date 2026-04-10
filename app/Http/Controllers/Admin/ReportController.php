<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $rango = $request->get('rango', 'mes'); 
        $metodo = $request->get('metodo', 'todos');
        
        $selectedMonth = $request->get('month', now()->month);
        $selectedYear = $request->get('year', now()->year);
        
        $query = \App\Models\Payment::query()->where('estado_pago', 'pagado');
        
        if ($rango === 'mes_especifico') {
            $query->whereYear('created_at', $selectedYear)
                  ->whereMonth('created_at', $selectedMonth);
        } elseif ($rango === 'hoy') {
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
        
        // Optimización: Cálculos de totales en una sola consulta de base de datos (Ultra rápido)
        $totales = (clone $query)->selectRaw("
            SUM(CASE WHEN concepto = 'mensualidad' THEN monto ELSE 0 END) as mensualidades,
            SUM(CASE WHEN concepto != 'mensualidad' THEN monto ELSE 0 END) as articulos,
            SUM(CASE WHEN metodo_pago = 'efectivo' THEN monto ELSE 0 END) as efectivo,
            SUM(CASE WHEN metodo_pago = 'qr' THEN monto ELSE 0 END) as qr,
            SUM(CASE WHEN metodo_pago = 'tarjeta' THEN monto ELSE 0 END) as tarjeta
        ")->first();

        $ingresosMensualidades = $totales->mensualidades ?? 0;
        $ingresosArticulos = $totales->articulos ?? 0;
        
        $porMetodo = [
            'efectivo' => $totales->efectivo ?? 0,
            'qr' => $totales->qr ?? 0,
            'tarjeta' => $totales->tarjeta ?? 0,
        ];

        // Traemos los registros paginados o limitados para la tabla (Optimizado)
        $pagosFiltrados = $query->with(['athlete.category', 'cobrador'])->latest()->get();

        $historialMeses = \App\Models\Payment::selectRaw('EXTRACT(YEAR FROM created_at) as anio, EXTRACT(MONTH FROM created_at) as mes, COUNT(*) as total_pagos, SUM(monto) as total_monto')
            ->where('estado_pago', 'pagado')
            ->groupBy('anio', 'mes')
            ->orderBy('anio', 'desc')
            ->orderBy('mes', 'desc')
            ->get();
        
        $stats = [
            'total_atletas' => \App\Models\Athlete::count(),
            'atletas_activos' => \App\Models\Athlete::where('habilitado_booleano', true)->count(),
            'por_categoria' => \App\Models\Category::withCount('athletes')->get(),
            
            'rango' => $rango,
            'month' => $selectedMonth,
            'year' => $selectedYear,
            'desde' => $request->desde,
            'hasta' => $request->hasta,
            'metodo' => $metodo,
            'ingresos_mensualidades' => $ingresosMensualidades,
            'ingresos_articulos' => $ingresosArticulos,
            'total_ingresos' => $ingresosMensualidades + $ingresosArticulos,
            'por_metodo' => $porMetodo,
            'pagos' => $pagosFiltrados,
            'historial_meses' => $historialMeses,
        ];

        return view('admin.reports.index', compact('stats'));
    }

    public function exportExcel(Request $request)
    {
        $rango = $request->get('rango', 'mes');
        $metodo = $request->get('metodo', 'todos');
        
        $query = \App\Models\Payment::query()->where('estado_pago', 'pagado');
        
        if ($rango === 'mes_especifico') {
            $query->whereYear('created_at', $request->get('year', now()->year))
                  ->whereMonth('created_at', $request->get('month', now()->month));
        } elseif ($rango === 'hoy') {
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

    public function exportPdf(Request $request)
    {
        $rango = $request->get('rango', 'mes');
        $metodo = $request->get('metodo', 'todos');
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        $query = \App\Models\Payment::query()->where('estado_pago', 'pagado');
        
        if ($rango === 'mes_especifico') {
            $query->whereYear('created_at', $year)->whereMonth('created_at', $month);
        } elseif ($rango === 'hoy') {
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
        
        $pagos = $query->with(['athlete', 'cobrador'])->latest()->get();
        
        $filtros = "Reporte: " . strtoupper($rango) . ($metodo !== 'todos' ? " | Método: " . ucfirst($metodo) : "");
        if ($rango === 'mes_especifico') {
            $filtros = "Mes: " . Carbon::create($year, $month)->translatedFormat('F Y');
        }

        $pdf = Pdf::loadView('admin.superadmin.pdf.pagos', compact('pagos', 'filtros'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('Reporte_OlimpicSC_' . now()->format('Ymd_His') . '.pdf');
    }
}
