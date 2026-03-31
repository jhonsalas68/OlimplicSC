@extends('layouts.admin')

@section('title', 'Panel del Coach')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Bienvenido, {{ $user->name }}</h1>
    <p class="text-sm text-slate-500 mt-0.5">
        Coach de categoria:
        <span class="font-semibold text-blue-600">{{ $category->nombre ?? 'Sin categoria asignada' }}</span>
        @if($category)
            &middot; {{ $category->edad_min }}–{{ $category->edad_max }} años
        @endif
    </p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        </div>
        <div>
            <p class="text-2xl font-bold text-slate-800">{{ $atletas->count() }}</p>
            <p class="text-sm text-slate-500">Atletas en mi categoria</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
        </div>
        <div>
            <p class="text-2xl font-bold text-slate-800">{{ $planificaciones->count() }}</p>
            <p class="text-sm text-slate-500">Planificaciones registradas</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Ultimas planificaciones --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-slate-700">Ultimas Planificaciones</h2>
            <a href="{{ route('coach.planificaciones') }}" class="text-xs text-blue-600 hover:text-blue-800">Ver todas</a>
        </div>
        @forelse($planificaciones->take(5) as $plan)
            <div class="flex items-center gap-3 py-3 border-b border-slate-50 last:border-0">
                <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
                    <svg class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-700 truncate">
                        {{ $plan->category->nombre ?? 'Sin categoria' }}
                    </p>
                    <p class="text-xs text-slate-400">{{ $plan->fecha ? $plan->fecha->format('d/m/Y') : '—' }}</p>
                </div>
                @if($plan->file_path_pdf)
                    <a href="{{ str_starts_with($plan->file_path_pdf, 'http') ? $plan->file_path_pdf : asset('storage/' . $plan->file_path_pdf) }}" target="_blank" rel="noopener noreferrer"
                       class="text-xs text-blue-600 hover:text-blue-800 flex-shrink-0">Ver PDF</a>
                @endif
            </div>
        @empty
            <p class="text-sm text-slate-400 text-center py-6">No hay planificaciones aun.</p>
        @endforelse
    </div>

    {{-- Atletas recientes --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-slate-700">
                Atletas — {{ $category->nombre ?? 'Mi Categoria' }}
            </h2>
            <a href="{{ route('coach.atletas') }}" class="text-xs text-blue-600 hover:text-blue-800">Ver todos</a>
        </div>
        @forelse($atletas->take(6) as $atleta)
            <div class="flex items-center gap-3 py-2.5 border-b border-slate-50 last:border-0">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-600 to-red-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0 overflow-hidden shadow-sm">
                    {{ strtoupper(substr($atleta->nombre,0,1).substr($atleta->apellido_paterno??'',0,1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-700 truncate">
                        {{ $atleta->nombre }} {{ $atleta->apellido_paterno }}
                    </p>
                    <div class="flex items-center gap-2">
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">CI: {{ $atleta->ci }}</p>
                        @if(isset($atleta->pagado_mes_actual))
                            <span class="text-[9px] font-black px-1.5 py-0.5 rounded uppercase border {{ $atleta->pagado_mes_actual ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-100' }}">
                                {{ $atleta->pagado_mes_actual ? 'Al Día' : 'Debe' }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-400 text-center py-6">No hay atletas en esta categoria.</p>
        @endforelse
    </div>

</div>
@endsection
