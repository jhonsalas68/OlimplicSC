@extends('layouts.admin')

@section('title', 'Panel del Coach')

@section('content')
<div class="relative mb-8 p-8 rounded-3xl bg-gradient-to-br from-blue-700 via-blue-800 to-slate-900 overflow-hidden shadow-2xl shadow-blue-200">
    {{-- Decoración de fondo --}}
    <div class="absolute top-0 right-0 -mt-20 -mr-20 w-96 h-96 bg-blue-400/10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-64 h-64 bg-red-500/10 rounded-full blur-3xl"></div>

    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight">
                Bienvenido, <span class="text-blue-200">{{ explode(' ', $user->name)[0] }}</span> 👋
            </h1>
            <div class="mt-4 flex flex-wrap items-center gap-2">
                <span class="text-blue-100/80 text-sm font-medium uppercase tracking-wider">Coach de:</span>
                @forelse($myCategories as $cat)
                    <span class="px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white text-xs font-bold shadow-sm">
                        {{ $cat->nombre }}
                    </span>
                @empty
                    <span class="px-3 py-1 rounded-full bg-red-500/20 backdrop-blur-md border border-red-500/30 text-red-200 text-xs font-bold">
                        Sin categorías asignadas
                    </span>
                @endforelse
            </div>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="text-right hidden md:block">
                <p class="text-white/60 text-xs font-bold uppercase tracking-widest">Estado del Sistema</p>
                <p class="text-emerald-400 text-sm font-bold flex items-center justify-end gap-1.5">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    En línea
                </p>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 00-2 2z"></path></svg>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    {{-- Card: Atletas --}}
    <div class="group bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-blue-500/5 transition-all duration-300">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Atletas</span>
        </div>
        <h3 class="text-4xl font-black text-slate-800">{{ $atletas->count() }}</h3>
        <p class="text-sm text-slate-500 mt-1">Bajo tu supervisión</p>
    </div>

    {{-- Card: Planificaciones --}}
    <div class="group bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-red-500/5 transition-all duration-300">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center text-red-600 group-hover:bg-red-600 group-hover:text-white transition-colors duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            </div>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Entrenamientos</span>
        </div>
        <h3 class="text-4xl font-black text-slate-800">{{ $planificaciones->count() }}</h3>
        <p class="text-sm text-slate-500 mt-1">Planificaciones activas</p>
    </div>

    {{-- Card: Atletas Al Día --}}
    @php
        $alDia = $atletas->where('pagado_mes_actual', true)->count();
        $porcentaje = $atletas->count() > 0 ? round(($alDia / $atletas->count()) * 100) : 0;
    @endphp
    <div class="group bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-emerald-500/5 transition-all duration-300">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pagos</span>
        </div>
        <h3 class="text-4xl font-black text-slate-800">{{ $porcentaje }}%</h3>
        <p class="text-sm text-slate-500 mt-1">Atletas al día este mes</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    {{-- Tabla: Últimas Planificaciones --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/50">
            <div>
                <h2 class="text-lg font-black text-slate-800">Planificaciones</h2>
                <p class="text-xs text-slate-500">Documentos de entrenamiento recientes</p>
            </div>
            <a href="{{ route('coach.planificaciones') }}" class="px-4 py-2 rounded-xl bg-blue-50 text-blue-600 text-xs font-bold hover:bg-blue-600 hover:text-white transition-all">Ver todas</a>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @forelse($planificaciones->take(5) as $plan)
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50/30 border border-transparent hover:border-slate-100 hover:bg-white transition-all group">
                        <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0 group-hover:rotate-6 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-slate-700 truncate">{{ $plan->category->nombre ?? 'Sin categoría' }}</h4>
                            <p class="text-[10px] text-slate-400 font-medium uppercase tracking-tighter">{{ $plan->fecha ? $plan->fecha->translatedFormat('d M, Y') : 'Sin fecha' }}</p>
                        </div>
                        @if($plan->file_path_pdf)
                            <a href="{{ str_starts_with($plan->file_path_pdf, 'http') ? $plan->file_path_pdf : asset('storage/' . $plan->file_path_pdf) }}" 
                               target="_blank" class="p-2 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            </a>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-10">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <p class="text-sm text-slate-400 font-medium">No hay planificaciones aún</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Lista: Atletas Recientes --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/50">
            <div>
                <h2 class="text-lg font-black text-slate-800">Atletas</h2>
                <p class="text-xs text-slate-500">Últimos registros de tus categorías</p>
            </div>
            <a href="{{ route('coach.atletas') }}" class="px-4 py-2 rounded-xl bg-blue-50 text-blue-600 text-xs font-bold hover:bg-blue-600 hover:text-white transition-all">Ver todos</a>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 gap-4">
                @forelse($atletas->take(6) as $atleta)
                    <div class="flex items-center gap-4 p-3 rounded-2xl border border-slate-50 hover:border-blue-100 transition-all">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-blue-600 to-blue-400 flex items-center justify-center text-white text-sm font-black shadow-md shadow-blue-100">
                            {{ strtoupper(substr($atleta->nombre,0,1).substr($atleta->apellido_paterno??'',0,1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-slate-800 truncate">{{ $atleta->nombre }} {{ $atleta->apellido_paterno }}</h4>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">CI: {{ $atleta->ci }}</span>
                                <span class="text-[9px] font-black px-2 py-0.5 rounded-full border {{ $atleta->pagado_mes_actual ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-100' }}">
                                    {{ $atleta->pagado_mes_actual ? 'PAGADO' : 'PENDIENTE' }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-bold text-blue-600 uppercase">{{ $atleta->category->nombre ?? 'S/C' }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <p class="text-sm text-slate-400 font-medium">No hay atletas registrados</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
