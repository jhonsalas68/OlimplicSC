@extends('layouts.admin')

@section('title', 'Reporte de Operaciones')

@section('content')
<div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
    <div>
        <div class="flex items-center gap-3 mb-2">
            <div class="p-2 bg-blue-600 rounded-xl shadow-lg shadow-blue-200">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Caja y Reportes</h1>
        </div>
        <p class="text-slate-500 font-medium">Control financiero y registro histórico de ingresos</p>
    </div>
    
    <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('admin.reports.export.excel', request()->all()) }}" 
           class="inline-flex items-center px-6 py-3 bg-white text-emerald-600 hover:bg-emerald-50 rounded-2xl text-sm font-black transition-all shadow-xl shadow-slate-200/50 border border-slate-100 group">
            <svg class="h-5 w-5 mr-2 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Excel
        </a>
        <a href="{{ route('admin.reports.export.pdf', request()->all()) }}" 
           class="inline-flex items-center px-6 py-3 bg-white text-rose-600 hover:bg-rose-50 rounded-2xl text-sm font-black transition-all shadow-xl shadow-slate-200/50 border border-slate-100 group">
            <svg class="h-5 w-5 mr-2 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h1.5m1.5 0H13m-4 4h4m-4 4h4" /></svg>
            PDF (Imprimible)
        </a>
    </div>
</div>

{{-- ── NAVEGACIÓN HISTÓRICA POR MESES ── --}}
<div class="mb-10">
    <label class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 ml-1">Historial de Operaciones</label>
    <div class="flex flex-wrap gap-3">
        @foreach($stats['historial_meses'] as $hm)
            @php
                $esSeleccionado = ($stats['month'] == $hm->mes && $stats['year'] == $hm->anio);
                $nombreMes = \Carbon\Carbon::create(null, $hm->mes)->translatedFormat('F');
            @endphp
            <a href="{{ route('admin.reports.index', ['rango' => 'mes_especifico', 'month' => $hm->mes, 'year' => $hm->anio]) }}"
               class="px-5 py-3 rounded-2xl text-sm font-bold transition-all border {{ $esSeleccionado ? 'bg-blue-600 border-blue-600 text-white shadow-lg shadow-blue-200 scale-105 z-10' : 'bg-white border-slate-100 text-slate-600 hover:border-blue-200 hover:bg-blue-50' }}">
                <span class="capitalize">{{ $nombreMes }}</span>
                <span class="opacity-60 text-[10px] block font-black leading-none mt-1">{{ $hm->anio }}</span>
            </a>
        @endforeach
        
        <a href="{{ route('admin.reports.index', ['rango' => 'semana']) }}" 
           class="px-5 py-3 rounded-2xl text-sm font-bold bg-slate-100 text-slate-600 border border-transparent hover:bg-slate-200 transition-all flex items-center">
            Ver Esta Semana
        </a>
    </div>
</div>

{{-- FILTROS AVANZADOS --}}
<div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/40 mb-10" x-data="{ rango: '{{ $stats['rango'] }}' }">
    <form method="GET" action="{{ route('admin.reports.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Modalidad de Filtro</label>
            <select name="rango" x-model="rango" class="w-full bg-slate-50 border border-slate-100 text-slate-700 rounded-2xl px-4 py-3 outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-600 font-bold appearance-none cursor-pointer">
                <option value="mes_especifico">📅 Selección por Mes</option>
                <option value="hoy">Hoy</option>
                <option value="semana">Esta Semana</option>
                <option value="mes">Mes Actual</option>
                <option value="anio">Este Año</option>
                <option value="personalizado">🛠️ Rango Personalizado</option>
            </select>
        </div>

        <div x-show="rango === 'personalizado'" class="flex gap-3 col-span-2">
            <div class="flex-1">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Desde</label>
                <input type="date" name="desde" value="{{ $stats['desde'] }}" class="w-full bg-slate-50 border border-slate-100 text-slate-700 rounded-2xl px-4 py-3 outline-none focus:ring-4 focus:ring-blue-100 font-bold">
            </div>
            <div class="flex-1">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Hasta</label>
                <input type="date" name="hasta" value="{{ $stats['hasta'] }}" class="w-full bg-slate-50 border border-slate-100 text-slate-700 rounded-2xl px-4 py-3 outline-none focus:ring-4 focus:ring-blue-100 font-bold">
            </div>
        </div>

        <div x-show="rango === 'mes_especifico'" class="flex gap-3 col-span-2">
            <div class="flex-1">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Año</label>
                <select name="year" class="w-full bg-slate-50 border border-slate-100 text-slate-700 rounded-2xl px-4 py-3 outline-none focus:ring-4 focus:ring-blue-100 font-bold">
                    @for($y = now()->year; $y >= 2024; $y--)
                        <option value="{{ $y }}" {{ $stats['year'] == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Mes</label>
                <select name="month" class="w-full bg-slate-50 border border-slate-100 text-slate-700 rounded-2xl px-4 py-3 outline-none focus:ring-4 focus:ring-blue-100 font-bold">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $stats['month'] == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Método</label>
            <select name="metodo" class="w-full bg-slate-50 border border-slate-100 text-slate-700 rounded-2xl px-4 py-3 outline-none focus:ring-4 focus:ring-blue-100 font-bold appearance-none cursor-pointer">
                <option value="todos">Todos los métodos</option>
                <option value="efectivo" {{ $stats['metodo'] === 'efectivo' ? 'selected' : '' }}>💵 Efectivo</option>
                <option value="qr" {{ $stats['metodo'] === 'qr' ? 'selected' : '' }}>📱 QR</option>
                <option value="tarjeta" {{ $stats['metodo'] === 'tarjeta' ? 'selected' : '' }}>💳 Tarjeta</option>
            </select>
        </div>

        <div class="grow">
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-3.5 rounded-2xl transition-all shadow-lg shadow-blue-200 group flex items-center justify-center gap-2">
                <svg class="h-5 w-5 group-hover:rotate-180 transition-transform duration-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                Actualizar Vista
            </button>
        </div>
    </form>
</div>

{{-- DASHBOARD FINANCIERO --}}
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

{{-- TABLA DE TRANSACCIONES --}}
<div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden mb-12">
    <div class="p-8 border-b border-slate-50 flex items-center justify-between bg-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-full bg-blue-50/50 skew-x-12 translate-x-16"></div>
        <div class="relative z-10">
            <h2 class="text-sm font-black text-slate-800 uppercase tracking-[0.1em]">Detalle de Transacciones</h2>
            <p class="text-[11px] text-slate-400 font-bold mt-1 uppercase">Mostrando registros del periodo seleccionado</p>
        </div>
        <span class="bg-blue-600 text-white text-[10px] font-black px-4 py-1.5 rounded-xl shadow-lg shadow-blue-200 relative z-10">
            {{ $stats['pagos']->count() }} REGISTROS
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50">
                    <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Fecha y Hora</th>
                    <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Atleta / Estudiante</th>
                    <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Concepto</th>
                    <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Método</th>
                    <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Monto Neto</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($stats['pagos'] as $pago)
                    <tr class="hover:bg-slate-50/80 transition-all group">
                        <td class="px-8 py-5">
                            <div class="text-sm font-black text-slate-800">{{ $pago->created_at->format('d/m/Y') }}</div>
                            <div class="text-[10px] font-bold text-slate-400 mt-1">{{ $pago->created_at->format('H:i') }} hrs</div>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 font-black text-[10px]">
                                    {{ substr($pago->athlete->nombre, 0, 1) }}{{ substr($pago->athlete->apellido_paterno, 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-sm font-black text-slate-800 group-hover:text-blue-600 transition-colors capitalize">
                                        {{ $pago->athlete->nombre }} {{ $pago->athlete->apellido_paterno }}
                                    </div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">CI: {{ $pago->athlete->ci }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-center">
                            @if($pago->concepto === 'mensualidad')
                                <div class="inline-flex items-center px-2.5 py-1 bg-emerald-100/50 text-emerald-700 text-[10px] font-black rounded-lg uppercase tracking-wider border border-emerald-100">
                                    Mensualidad
                                </div>
                                <div class="text-[10px] font-bold text-slate-400 mt-1 uppercase">{{ $pago->mes_correspondiente }}</div>
                            @else
                                <div class="inline-flex items-center px-2.5 py-1 bg-amber-100/50 text-amber-700 text-[10px] font-black rounded-lg uppercase tracking-wider border border-amber-100">
                                    Artículo
                                </div>
                                <div class="text-[10px] font-bold text-slate-400 mt-1 uppercase truncate max-w-[120px]">{{ $pago->descripcion ?? 'Varios' }}</div>
                            @endif
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-2">
                                @if($pago->metodo_pago === 'efectivo')
                                    <span class="p-1.5 bg-emerald-50 rounded-lg text-emerald-600">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                    </span>
                                    <span class="text-xs font-bold text-slate-700">Efectivo</span>
                                @elseif($pago->metodo_pago === 'qr')
                                    <span class="p-1.5 bg-blue-50 rounded-lg text-blue-600">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                                    </span>
                                    <span class="text-xs font-bold text-slate-700">Pago QR</span>
                                @else
                                    <span class="p-1.5 bg-slate-50 rounded-lg text-slate-600">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                                    </span>
                                    <span class="text-xs font-bold text-slate-700">Tarjeta</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="text-base font-black text-slate-900 leading-none">Bs. {{ number_format($pago->monto, 2) }}</div>
                            <div class="text-[9px] font-black text-slate-400 mt-1 uppercase">Validado</div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <div class="p-4 bg-slate-50 rounded-full text-slate-300">
                                    <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                                </div>
                                <p class="text-slate-400 font-bold text-sm">No se encontraron transacciones para este periodo</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
