@extends('layouts.admin')

@section('title', 'Reporte de Operaciones')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Caja y Reportes</h1>
        <p class="text-sm text-slate-500 mt-1">Control de ingresos por mensualidades y artículos deportivos</p>
    </div>
    
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.reports.export.excel', request()->all()) }}" class="inline-flex items-center px-4 py-2 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-xl text-sm font-bold transition-colors shadow-sm border border-emerald-200">
            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Exportar Excel
        </a>
    </div>
</div>

{{-- FILTROS --}}
<div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm mb-6" x-data="{ rango: '{{ $stats['rango'] }}' }">
    <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-col sm:flex-row flex-wrap items-end gap-4">
        <div class="w-full sm:w-1/4">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Periodo</label>
            <select name="rango" x-model="rango" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-medium appearance-none">
                <option value="hoy" {{ $stats['rango'] === 'hoy' ? 'selected' : '' }}>Hoy</option>
                <option value="semana" {{ $stats['rango'] === 'semana' ? 'selected' : '' }}>Esta Semana</option>
                <option value="mes" {{ $stats['rango'] === 'mes' ? 'selected' : '' }}>Este Mes</option>
                <option value="anio" {{ $stats['rango'] === 'anio' ? 'selected' : '' }}>Este Año</option>
                <option value="personalizado" {{ $stats['rango'] === 'personalizado' ? 'selected' : '' }}>📅 Personalizado</option>
            </select>
        </div>

        {{-- Campos de Fecha Personalizada (Alpine.js) --}}
        <div class="w-full sm:w-1/4" x-show="rango === 'personalizado'" x-cloak x-transition>
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Desde</label>
            <input type="date" name="desde" value="{{ $stats['desde'] }}" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-4 py-2 outline-none focus:ring-2 focus:ring-blue-500 font-medium">
        </div>
        <div class="w-full sm:w-1/4" x-show="rango === 'personalizado'" x-cloak x-transition>
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Hasta</label>
            <input type="date" name="hasta" value="{{ $stats['hasta'] }}" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-4 py-2 outline-none focus:ring-2 focus:ring-blue-500 font-medium">
        </div>

        <div class="w-full sm:w-1/4">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Método de Pago</label>
            <select name="metodo" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-medium appearance-none">
                <option value="todos" {{ $stats['metodo'] === 'todos' ? 'selected' : '' }}>Todos los métodos</option>
                <option value="efectivo" {{ $stats['metodo'] === 'efectivo' ? 'selected' : '' }}>💵 Efectivo</option>
                <option value="qr" {{ $stats['metodo'] === 'qr' ? 'selected' : '' }}>📱 QR</option>
                <option value="tarjeta" {{ $stats['metodo'] === 'tarjeta' ? 'selected' : '' }}>💳 Tarjeta</option>
            </select>
        </div>
        <div class="w-full sm:w-1/4 grow">
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl transition-colors shadow-sm">
                Aplicar Filtros
            </button>
        </div>
    </form>
</div>

{{-- TARJETAS FINANCIERAS FINANCIERAS --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    {{-- Total Ingreso --}}
    <div class="bg-blue-600 p-6 rounded-2xl border border-blue-700 shadow-lg relative overflow-hidden text-white">
        <div class="relative z-10">
            <p class="text-sm font-bold text-blue-200 uppercase tracking-wider">Total Ingresos</p>
            <h3 class="text-3xl font-black mt-1">Bs. {{ number_format($stats['total_ingresos'], 2) }}</h3>
            <p class="text-[10px] text-blue-100 mt-2 font-black uppercase tracking-widest bg-blue-700/50 inline-block px-2.5 py-1 rounded-lg border border-blue-500/30">
                Filtro: 
                @if($stats['rango'] === 'personalizado')
                    {{ \Carbon\Carbon::parse($stats['desde'])->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($stats['hasta'])->format('d/m/Y') }}
                @else
                    {{ strtoupper($stats['rango']) }}
                @endif
            </p>
        </div>
        <svg class="absolute -right-6 -bottom-6 w-32 h-32 text-blue-500 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
    </div>

    {{-- Ingreso Mensualidades --}}
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm relative text-slate-800">
        <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center mb-4 border border-emerald-100">
            <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Por Mensualidades</p>
        <h3 class="text-2xl font-black mt-1 text-emerald-600">Bs. {{ number_format($stats['ingresos_mensualidades'], 2) }}</h3>
    </div>

    {{-- Ingreso Articulos --}}
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm relative text-slate-800">
        <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center mb-4 border border-amber-100">
            <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
        </div>
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Por Artículos Deportivos</p>
        <h3 class="text-2xl font-black mt-1 text-amber-600">Bs. {{ number_format($stats['ingresos_articulos'], 2) }}</h3>
    </div>
</div>

{{-- TABLA DE TRANSACCIONES DEL PERIODO --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-8">
    <div class="p-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Transacciones Encontradas</h2>
        <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">{{ $stats['pagos']->count() }} registros</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white border-b border-slate-100">
                    <th class="px-5 py-3 text-xs font-bold text-slate-400 uppercase">Fecha</th>
                    <th class="px-5 py-3 text-xs font-bold text-slate-400 uppercase">Estudiante / Atleta</th>
                    <th class="px-5 py-3 text-xs font-bold text-slate-400 uppercase">Concepto</th>
                    <th class="px-5 py-3 text-xs font-bold text-slate-400 uppercase">Método</th>
                    <th class="px-5 py-3 text-right text-xs font-bold text-slate-400 uppercase">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stats['pagos'] as $pago)
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-500">
                            {{ $pago->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-slate-800">{{ $pago->athlete->nombre }} {{ $pago->athlete->apellido_paterno }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">CI: {{ $pago->athlete->ci }}</div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            @if($pago->concepto === 'mensualidad')
                                <span class="bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider">Mensualidad</span>
                                <div class="text-xs text-slate-500 mt-1">Mes: {{ $pago->mes_correspondiente }}</div>
                            @else
                                <span class="bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider">Artículo</span>
                                <div class="text-xs text-slate-500 mt-1">{{ str($pago->descripcion)->limit(20) ?? 'Venta' }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-sm font-semibold text-slate-700">
                            @if($pago->metodo_pago === 'efectivo')
                                💵 Efectivo
                            @elseif($pago->metodo_pago === 'qr')
                                📱 QR
                            @else
                                💳 Tarjeta
                            @endif
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-right text-sm font-black text-slate-800">
                            Bs. {{ number_format($pago->monto, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-slate-400">
                            No se encontraron transacciones para este filtro.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
