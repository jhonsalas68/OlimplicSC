@extends('layouts.admin')

@section('title', 'Reporte de Operaciones')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-800">Alcances y Costos de Operación</h1>
    <p class="text-sm text-slate-500 mt-1">Resumen financiero y de impacto del club al {{ now()->translatedFormat('d \d\e F, Y') }}</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    {{-- Alcance Total --}}
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
        <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center mb-4">
            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </div>
        <p class="text-sm font-bold text-slate-400 uppercase tracking-wider">Alcance Total</p>
        <h3 class="text-3xl font-black text-slate-800 mt-1">{{ $stats['total_atletas'] }}</h3>
        <p class="text-xs text-slate-500 mt-2">
            <span class="text-emerald-500 font-bold">{{ $stats['atletas_activos'] }}</span> atletas activos actualmente.
        </p>
    </div>

    {{-- Recaudación Mensual --}}
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
        <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center mb-4">
            <svg class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <p class="text-sm font-bold text-slate-400 uppercase tracking-wider">Ingresos del Mes</p>
        <h3 class="text-3xl font-black text-slate-800 mt-1">Bs. {{ number_format($stats['recaudacion_mes'], 2) }}</h3>
        <p class="text-xs text-slate-500 mt-2">Basado en mensualidades y artículos deportivos.</p>
    </div>

    {{-- Costos de Operación (Placeholder/Input) --}}
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
        <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center mb-4">
            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <p class="text-sm font-bold text-slate-400 uppercase tracking-wider">Costos Estimados</p>
        <h3 class="text-3xl font-black text-slate-800 mt-1">Bs. 0.00</h3>
        <p class="text-xs text-slate-500 mt-2">Margen operativo estimado: 100%</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Distribución por Categoría --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <h2 class="text-lg font-bold text-slate-800 mb-6">Alcance por Categoría</h2>
        <div class="space-y-4">
            @foreach($stats['por_categoria'] as $cat)
                @php $porcentaje = $stats['total_atletas'] > 0 ? ($cat->athletes_count / $stats['total_atletas']) * 100 : 0; @endphp
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <span class="text-sm font-semibold text-slate-600">{{ $cat->nombre }}</span>
                        <span class="text-sm font-bold text-slate-900">{{ $cat->athletes_count }} atletas</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $porcentaje }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Nota Informativa --}}
    <div class="bg-blue-600 rounded-3xl p-8 text-white relative overflow-hidden shadow-xl">
        <div class="relative z-10">
            <h2 class="text-2xl font-black mb-4">Visión de Impacto</h2>
            <p class="text-blue-100 leading-relaxed mb-6">
                Este reporte permite visualizar el alcance social del club y la sostenibilidad financiera mensual. 
                Utilice estos datos para planificar la expansión de categorías y la optimización de costos operativos.
            </p>
            <div class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-md rounded-xl text-sm font-bold border border-white/20">
                <svg class="h-4 w-4 mr-2 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                Actualizado en tiempo real
            </div>
        </div>
        {{-- Decoración --}}
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/10 rounded-full blur-3xl"></div>
    </div>
</div>
@endsection
