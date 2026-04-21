@extends('layouts.admin')

@section('title', 'Alumnos y Convocatoria')

@section('content')
<div x-data="{ 
    selectedIds: [], 
    openConvocar: false,
    updateSelected() {
        const ids = Array.from(document.querySelectorAll('.athlete-checkbox-data:checked')).map(cb => cb.value);
        this.selectedIds = ids;
        const sIds = document.getElementById('selected-ids-input');
        const eIds = document.getElementById('export-ids-input');
        if(sIds) sIds.value = JSON.stringify(ids);
        if(eIds) eIds.value = JSON.stringify(ids);
    }
}" @athlete-selected.window="updateSelected()">

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-black text-slate-800 tracking-tight">Atletas y Selección</h1>
        <p class="text-sm text-slate-500 mt-1 uppercase font-bold tracking-widest">Panel de Entrenador</p>
    </div>
</div>

{{-- Widgets de Categorías --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @foreach($categories as $catData)
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 {{ $catData['is_mine'] ? 'ring-2 ring-blue-500' : '' }}">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-800">{{ $catData['category']->nombre }}</h3>
                <p class="text-xs text-slate-500">{{ $catData['count'] }} atletas</p>
            </div>
            @if($catData['is_mine'])
                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">Tu Categoría</span>
            @else
                <span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded-full">Otra</span>
            @endif
        </div>
    </div>
    @endforeach
</div>

<div class="flex flex-col sm:flex-row items-center gap-3">
        <form action="{{ route('coach.atletas') }}" method="GET" class="flex items-center gap-3 w-full sm:w-auto">
            @if(request('ver_todas'))
                <input type="hidden" name="ver_todas" value="1">
            @endif
            <select name="deuda" onchange="this.form.submit()" 
                    class="block w-full sm:w-44 px-3 py-2 border border-slate-200 rounded-xl leading-5 bg-white text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs font-bold transition-all shadow-sm cursor-pointer whitespace-nowrap">
                <option value="">Mensualidades: Todas</option>
                <option value="al_dia" {{ request('deuda') === 'al_dia' ? 'selected' : '' }}>✅ Al Día</option>
                <option value="deudores" {{ request('deuda') === 'deudores' ? 'selected' : '' }}>❌ Deudores</option>
            </select>
        </form>

        <div id="selection-panel" x-show="selectedIds.length > 0" x-cloak class="animate-in fade-in slide-in-from-right-4 duration-300">
            <button @click="openConvocar = true" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition-all shadow-lg hover:shadow-blue-200 group whitespace-nowrap">
                <svg class="h-4 w-4 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Convocatoria (<span x-text="selectedIds.length">0</span>)
            </button>
        </div>
    </div>
</div>

{{-- MODAL DE CONVOCATORIA --}}
<div x-show="openConvocar" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="openConvocar" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" @click="openConvocar = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="openConvocar" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100">
            
            <div class="bg-white p-6 sm:p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-black text-slate-800 tracking-tight" id="modal-title">Subir Convocatoria</h3>
                    <button @click="openConvocar = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="mb-8 p-4 bg-blue-50 rounded-2xl border border-blue-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </div>
                        <div>
                            <p class="text-xs font-black text-blue-700 uppercase tracking-widest">Alumnos Seleccionados</p>
                            <p class="text-sm font-bold text-blue-900"><span x-text="selectedIds.length"></span> Alumnos para esta planificación</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('trainings.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <input type="hidden" name="ids" id="selected-ids-input">
                    
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 px-1">Categoría asignada</label>
                        <select name="category_id" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-600 transition-all outline-none">
                            @if($myCategory)
                                <option value="{{ $myCategory->id }}">{{ $myCategory->nombre }} (Mía)</option>
                            @endif
                            {{-- Otras categorías si es necesario --}}
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 px-1">Fecha de Campeonato / Evento</label>
                        <input type="date" name="fecha" required value="{{ date('Y-m-d') }}"
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-600 transition-all outline-none">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 px-1">Planificación de Convocados (PDF)</label>
                        <div class="relative group">
                            <input type="file" name="pdf" accept=".pdf" required
                                   class="w-full px-4 py-3 bg-slate-50 border border-dash border-slate-200 rounded-2xl text-xs font-bold text-slate-500 file:mr-4 file:py-1 file:px-4 file:rounded-lg file:border-0 file:text-[10px] file:font-black file:bg-blue-600 file:text-white file:uppercase hover:file:bg-blue-700 cursor-pointer">
                        </div>
                        <p class="mt-2 text-[10px] text-slate-400 px-1 font-medium italic">* Sube el archivo con los detalles del entrenamiento o campeonato.</p>
                    </div>

                    <div class="pt-4 flex flex-col gap-3">
                        <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black uppercase tracking-widest rounded-2xl transition-all shadow-xl shadow-blue-200 flex items-center justify-center gap-2 group">
                            <svg class="h-5 w-5 group-hover:translate-y-[-2px] transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                            Subir y Registrar Planificación
                        </button>
                        
                        <div class="flex items-center gap-2">
                            <div class="h-px bg-slate-100 flex-1"></div>
                            <span class="text-[9px] font-black text-slate-300 uppercase tracking-tighter">O también puedes</span>
                            <div class="h-px bg-slate-100 flex-1"></div>
                        </div>

                        <button type="button" @click="$refs.exportForm.submit()" class="w-full py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-black uppercase tracking-widest rounded-2xl transition-all flex items-center justify-center gap-2">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Solo descargar lista PDF
                        </button>
                    </div>
                </form>
                
                {{-- Hidden Export Form --}}
                <form x-ref="exportForm" action="{{ route('athletes.export.selected') }}" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="ids" id="export-ids-input">
                </form>
            </div>
        </div>
    </div>
</div>

@if($atletasPropios->isEmpty() && $atletasOtros->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
        <p class="text-slate-400 text-sm">No hay alumnos registrados en tu categoría.</p>
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
                Ver Alumnos de Otras Categorías
            </a>
        </div>
    @elseif($atletasOtros->isNotEmpty())
        <div class="mt-16 mb-10">
            <div class="flex items-center gap-4 mb-10">
                <div class="h-px bg-slate-200 flex-1"></div>
                <h2 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] px-4">Otras Categorías</h2>
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

</div>

@push('scripts')
<script>
    function toggleAthlete(id, cardElement) {
        const checkbox = cardElement.querySelector('.athlete-checkbox-data');
        checkbox.checked = !checkbox.checked;
        
        if (checkbox.checked) {
            cardElement.classList.add('ring-4', 'ring-blue-500/30', 'border-blue-600', 'shadow-blue-100');
            cardElement.classList.remove('border-slate-100');
        } else {
            cardElement.classList.remove('ring-4', 'ring-blue-500/30', 'border-blue-600', 'shadow-blue-100');
            cardElement.classList.add('border-slate-100');
        }

        // Notify Alpine component
        window.dispatchEvent(new CustomEvent('athlete-selected'));
    }
</script>
@endpush
@endsection

