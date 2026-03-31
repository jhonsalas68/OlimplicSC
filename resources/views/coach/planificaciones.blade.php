@extends('layouts.admin')

@section('title', 'Mis Planificaciones')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Planificaciones</h1>
        <p class="text-sm text-slate-500 mt-0.5 uppercase tracking-widest font-bold">
            Tu Categoría: <span class="text-blue-600">{{ $myCategory->nombre ?? 'Sin categoría' }}</span>
        </p>
    </div>
    <a href="{{ route('trainings.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-lg shadow-blue-100">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Nueva Planificación
    </a>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-xl text-emerald-700 text-sm font-medium flex items-center">
        <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        {{ session('success') }}
    </div>
@endif

@if($planificacionesPropias->isEmpty() && $planificacionesOtras->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
        <svg class="h-12 w-12 mx-auto text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <p class="text-slate-400 text-sm">No hay planificaciones registradas en el sistema.</p>
    </div>
@else

    @if($planificacionesPropias->isNotEmpty())
        <div class="mb-10">
            <h2 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-4 px-2">Tu Categoría ({{ $myCategory->nombre ?? '' }})</h2>
            <div class="space-y-3">
                @foreach($planificacionesPropias as $plan)
                    <div class="bg-white rounded-2xl border border-blue-100 shadow-sm p-5 flex items-center gap-4 relative overflow-hidden transition-all hover:shadow-md">
                        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-blue-500"></div>
                        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0 pl-1">
                            <p class="text-base font-bold text-slate-800">
                                Planificación — {{ $plan->category->nombre ?? 'Sin categoría' }}
                            </p>
                            <p class="text-xs font-medium text-slate-500 mt-1">
                                Fecha de Entrenamiento: {{ $plan->fecha ? $plan->fecha->format('d/m/Y') : '—' }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0">
                            @if($plan->file_path_pdf)
                                <a href="{{ str_starts_with($plan->file_path_pdf, 'http') ? $plan->file_path_pdf : asset('storage/' . $plan->file_path_pdf) }}" target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-50 text-blue-700 text-xs font-bold rounded-lg hover:bg-blue-600 hover:text-white transition-colors shadow-sm">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Abrir Archivo
                                </a>
                            @endif
                            <a href="{{ route('trainings.edit', $plan) }}" title="Editar"
                               class="inline-flex items-center justify-center w-8 h-8 bg-slate-100 text-slate-500 rounded-lg hover:bg-slate-200 transition-colors">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($planificacionesOtras->isNotEmpty())
        <div class="mt-8 mb-10">
            <div class="flex items-center gap-4 mb-8">
                <div class="h-px bg-slate-200 flex-1"></div>
                <h2 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] px-4">Otras Categorías</h2>
                <div class="h-px bg-slate-200 flex-1"></div>
            </div>
            
            @foreach($planificacionesOtras as $catName => $grupo)
                <div class="mb-8">
                    <h3 class="text-sm font-black text-slate-500 mb-4 px-2 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                        {{ $catName }}
                    </h3>
                    <div class="space-y-3">
                        @foreach($grupo as $plan)
                            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex items-center gap-4 opacity-80 hover:opacity-100 transition-opacity">
                                <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center flex-shrink-0">
                                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-slate-700 truncate">
                                        {{ $plan->coach->name ?? 'Admin' }} — {{ $plan->fecha ? $plan->fecha->format('d/m/Y') : 'Sin fecha' }}
                                    </p>
                                </div>
                                <div class="flex items-center flex-shrink-0">
                                    @if($plan->file_path_pdf)
                                        <a href="{{ str_starts_with($plan->file_path_pdf, 'http') ? $plan->file_path_pdf : asset('storage/' . $plan->file_path_pdf) }}" target="_blank" rel="noopener noreferrer"
                                           class="inline-flex items-center px-4 py-1.5 bg-slate-100 text-slate-600 text-xs font-bold rounded-lg hover:bg-slate-200 transition-colors">
                                            Ver documento
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif

@endif
@endsection
