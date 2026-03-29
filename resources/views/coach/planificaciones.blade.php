@extends('layouts.admin')

@section('title', 'Mis Planificaciones')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Planificaciones</h1>
        <p class="text-sm text-slate-500 mt-0.5">
            Categoria: <span class="font-semibold text-blue-600">{{ $category->nombre ?? 'Sin categoria' }}</span>
        </p>
    </div>
    <a href="{{ route('trainings.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Nueva Planificacion
    </a>
</div>

@if(session('success'))
    <div class="mb-4 p-4 bg-emerald-50 border border-emerald-100 rounded-xl text-emerald-700 text-sm font-medium">
        {{ session('success') }}
    </div>
@endif

@if($planificaciones->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
        <svg class="h-12 w-12 mx-auto text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <p class="text-slate-400 text-sm">No hay planificaciones registradas aun.</p>
        <a href="{{ route('trainings.create') }}" class="mt-3 inline-block text-sm text-blue-600 hover:text-blue-800">
            Crear la primera planificacion
        </a>
    </div>
@else
    <div class="space-y-3">
        @foreach($planificaciones as $plan)
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800">
                        Planificacion — {{ $plan->category->nombre ?? 'Sin categoria' }}
                    </p>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Fecha: {{ $plan->fecha ? $plan->fecha->format('d/m/Y') : '—' }}
                        &middot; Subido: {{ $plan->created_at ? $plan->created_at->format('d/m/Y') : '—' }}
                    </p>
                </div>
                <div class="flex items-center gap-3 flex-shrink-0">
                    @if($plan->file_path_pdf)
                        <a href="{{ str_starts_with($plan->file_path_pdf, 'http') ? $plan->file_path_pdf : asset('storage/' . $plan->file_path_pdf) }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-700 text-xs font-semibold rounded-lg hover:bg-blue-100 transition-colors">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Ver PDF
                        </a>
                    @endif
                    <a href="{{ route('trainings.edit', $plan) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 text-slate-600 text-xs font-semibold rounded-lg hover:bg-slate-100 transition-colors">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Editar
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
