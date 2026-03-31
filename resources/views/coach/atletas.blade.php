@extends('layouts.admin')

@section('title', 'Gestión de Atletas')

@section('content')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-black text-slate-800 tracking-tight">Atletas y Selección</h1>
        <p class="text-sm text-slate-500 mt-1 uppercase font-bold tracking-widest">Panel de Entrenador</p>
    </div>
    
    <div id="selection-panel" class="hidden animate-in fade-in slide-in-from-right-4 duration-300">
        <form id="convocar-form" action="{{ route('athletes.export.selected') }}" method="POST">
            @csrf
            <input type="hidden" name="ids" id="selected-ids">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg hover:shadow-blue-200 group">
                <svg class="h-5 w-5 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Generar Convocatoria (<span id="count-selected">0</span>)
            </button>
        </form>
    </div>
</div>

@if($atletasPropios->isEmpty() && $atletasOtros->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
        <p class="text-slate-400 text-sm">No hay atletas registrados en el sistema.</p>
    </div>
@else

    {{-- MI CATEGORIA --}}
    @if($atletasPropios->isNotEmpty())
        <div class="mb-10 p-6 bg-white rounded-3xl border border-slate-100 shadow-sm">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 shadow-sm border border-blue-100">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-800 tracking-tight leading-none">Mi Categoría</h2>
                    <p class="text-xs font-bold text-blue-600 mt-1 uppercase tracking-widest">{{ $myCategory?->nombre ?? 'Sin Categoría' }}</p>
                </div>
                <div class="ml-auto flex flex-col items-end">
                    <span class="bg-slate-900 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-tighter">
                        {{ $atletasPropios->count() }} Atletas
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 xxl:grid-cols-5 gap-6">
                @foreach($atletasPropios as $atleta)
                    @include('coach.partials.athlete_card', ['atleta' => $atleta])
                @endforeach
            </div>
        </div>
    @elseif(!$myCategory)
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center mb-8">
            <p class="text-slate-400 text-sm">No tienes una categoría asignada. Asigna una categoría a tu usuario para ver tus atletas.</p>
        </div>
    @endif

    {{-- OTRAS CATEGORIAS --}}
    @if(!$verTodas)
        <div class="flex justify-center my-12">
            <a href="{{ route('coach.atletas', ['ver_todas' => 1]) }}" 
               class="inline-flex items-center gap-2 px-8 py-4 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-black rounded-2xl transition-all uppercase tracking-widest">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                Explorar Otras Categorías (Cargar más)
            </a>
        </div>
    @elseif($atletasOtros->isNotEmpty())
        <div class="mt-16 mb-10">
            <div class="flex items-center gap-4 mb-10">
                <div class="h-px bg-slate-200 flex-1"></div>
                <h2 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] px-4">Explorando Todas las Categorías</h2>
                <div class="h-px bg-slate-200 flex-1"></div>
            </div>
            
            @foreach($atletasOtros as $catName => $grupo)
                <div class="mb-12">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-8 h-8 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                        </div>
                        <h3 class="text-lg font-black text-slate-700 tracking-tight uppercase tracking-widest">
                            {{ $catName }}
                        </h3>
                        <div class="h-px bg-slate-100 flex-1 ml-2"></div>
                        <span class="text-[10px] font-bold text-slate-400">{{ $grupo->count() }} ALUMNOS</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 xxl:grid-cols-5 gap-5">
                        @foreach($grupo as $atleta)
                            @include('coach.partials.athlete_card', ['atleta' => $atleta])
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif

@endif

@push('scripts')
<script>
    const selectedIds = new Set();
    const selectionPanel = document.getElementById('selection-panel');
    const countDisplay = document.getElementById('count-selected');
    const hiddenInput = document.getElementById('selected-ids');

    function toggleAthlete(id, cardElement) {
        if (selectedIds.has(id)) {
            selectedIds.delete(id);
            cardElement.classList.remove('ring-4', 'ring-blue-500/30', 'border-blue-600', 'shadow-blue-100');
            cardElement.classList.add('border-slate-100');
        } else {
            selectedIds.add(id);
            cardElement.classList.remove('border-slate-100');
            cardElement.classList.add('ring-4', 'ring-blue-500/30', 'border-blue-600', 'shadow-blue-100');
        }

        const count = selectedIds.size;
        countDisplay.textContent = count;
        hiddenInput.value = JSON.stringify(Array.from(selectedIds));
        
        if (count > 0) {
            selectionPanel.classList.remove('hidden');
        } else {
            selectionPanel.classList.add('hidden');
        }
    }
</script>
@endpush
@endsection
