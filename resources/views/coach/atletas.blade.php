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
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex items-center justify-between hover:shadow-md transition-shadow">
        <div>
            <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight">{{ $catData['category']->nombre }}</h3>
            <p class="text-xs font-bold text-blue-600">{{ $catData['count'] }} atletas registrados</p>
        </div>
        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        </div>
    </div>
    @endforeach
</div>

<div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 mb-8">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <form action="{{ route('coach.atletas') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
            <select name="deuda" onchange="this.form.submit()" 
                    class="block w-full sm:w-44 px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-600 focus:outline-none focus:ring-4 focus:ring-blue-500/10 text-xs font-bold transition-all cursor-pointer">
                <option value="">Mensualidades: Todas</option>
                <option value="al_dia" {{ request('deuda') === 'al_dia' ? 'selected' : '' }}>✅ Al Día</option>
                <option value="deudores" {{ request('deuda') === 'deudores' ? 'selected' : '' }}>❌ Deudores</option>
            </select>

            <select name="genero" onchange="this.form.submit()" 
                    class="block w-full sm:w-44 px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-600 focus:outline-none focus:ring-4 focus:ring-blue-500/10 text-xs font-bold transition-all cursor-pointer">
                <option value="">Género: Todos</option>
                <option value="Masculino" {{ request('genero') === 'Masculino' ? 'selected' : '' }}>♂️ Masculino</option>
                <option value="Femenino" {{ request('genero') === 'Femenino' ? 'selected' : '' }}>♀️ Femenino</option>
                <option value="Otro" {{ request('genero') === 'Otro' ? 'selected' : '' }}>⚧️ Otro</option>
            </select>
        </form>

        <div id="selection-panel" x-show="selectedIds.length > 0 && !openConvocar" x-cloak 
             class="fixed bottom-8 right-8 z-[40] flex flex-col sm:flex-row gap-3 animate-in slide-in-from-bottom-8 duration-500">
            
            <button @click="openConvocar = true" 
                    class="inline-flex items-center gap-3 px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl transition-all shadow-2xl shadow-blue-200 group whitespace-nowrap border-b-4 border-blue-800 active:border-b-0 active:translate-y-1">
                <svg class="h-5 w-5 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Generar Convocatoria (<span x-text="selectedIds.length"></span>)
            </button>

            <button @click="$refs.exportForm.submit()" 
                    class="inline-flex items-center gap-3 px-8 py-4 bg-red-600 hover:bg-red-700 text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl transition-all shadow-2xl shadow-red-200 group whitespace-nowrap border-b-4 border-red-800 active:border-b-0 active:translate-y-1">
                <svg class="h-5 w-5 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Exportar Lista PDF (<span x-text="selectedIds.length"></span>)
            </button>
        </div>
    </div>
</div>

{{-- MODAL DE CONVOCATORIA --}}
<div x-show="openConvocar" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="openConvocar" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" @click="openConvocar = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div x-show="openConvocar" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100">
            <div class="bg-white p-6 sm:p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-black text-slate-800 tracking-tight">Subir Convocatoria</h3>
                    <button @click="openConvocar = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form action="{{ route('trainings.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <input type="hidden" name="ids" id="selected-ids-input">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Categoría asignada</label>
                        <select name="category_id" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold focus:border-blue-600 transition-all outline-none">
                            @foreach($myCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Fecha del Evento</label>
                        <input type="date" name="fecha" required value="{{ date('Y-m-d') }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold focus:border-blue-600 transition-all outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Planificación (PDF)</label>
                        <input type="file" name="pdf" accept=".pdf" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-500 file:mr-4 file:py-1 file:px-4 file:rounded-lg file:border-0 file:text-[10px] file:font-black file:bg-blue-600 file:text-white file:uppercase hover:file:bg-blue-700 cursor-pointer">
                    </div>
                    <div class="pt-4 flex flex-col gap-3">
                        <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black uppercase tracking-widest rounded-2xl transition-all shadow-xl shadow-blue-200 flex items-center justify-center gap-2 group">
                            <svg class="h-5 w-5 group-hover:translate-y-[-2px] transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                            Registrar Planificación
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

@if($atletasPropios->isEmpty())
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-12 text-center">
        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        </div>
        <h3 class="text-lg font-bold text-slate-800">No hay atletas registrados</h3>
        <p class="text-slate-400 text-sm mt-1">No se encontraron atletas en tus categorías asignadas.</p>
    </div>
@else
    <div class="mb-10 p-8 bg-white rounded-3xl border border-slate-100 shadow-sm">
        <div class="flex items-center gap-4 mb-8">
            <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-100">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
            <div>
                <h2 class="text-xl font-black text-slate-800 tracking-tight leading-none">Mis Atletas</h2>
                <div class="mt-1 flex items-center gap-2">
                    @foreach($myCategories as $cat)
                        <span class="text-[9px] font-black px-2 py-0.5 bg-blue-50 text-blue-600 rounded border border-blue-100 uppercase">{{ $cat->nombre }}</span>
                    @endforeach
                </div>
            </div>
            <div class="ml-auto">
                <span class="bg-slate-900 text-white text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-tighter shadow-xl">
                    {{ $atletasPropios->count() }} ALUMNOS
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 xxl:grid-cols-5 gap-6">
            @foreach($atletasPropios as $atleta)
                @include('coach.partials.athlete_card', ['atleta' => $atleta])
            @endforeach
        </div>
    </div>
@endif

{{-- SECCIÓN: BUSCADOR GENERAL (TODAS LAS CATEGORÍAS) --}}
<div class="mt-16 pt-12 border-t-4 border-slate-50">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 bg-slate-900 rounded-3xl flex items-center justify-center text-white shadow-2xl shadow-slate-200">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            <div>
                <h2 class="text-3xl font-black text-slate-800 tracking-tight leading-none">Todas las Categorías</h2>
                <p class="text-xs text-slate-400 mt-2 uppercase font-black tracking-[0.2em]">Buscador Global del Club Olimpic</p>
            </div>
        </div>
    </div>

    {{-- Formulario de Búsqueda General --}}
    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-100/50 p-8 mb-12">
        <form action="{{ route('coach.atletas') }}#general-search" method="GET" id="general-search" class="flex flex-col lg:flex-row items-stretch lg:items-center gap-6">
            {{-- Input de búsqueda --}}
            <div class="relative flex-1 group">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre, apellido o CI..." 
                       class="w-full pl-14 pr-6 py-4 bg-slate-50 border-2 border-slate-50 rounded-2xl text-sm font-bold focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-500/5 transition-all outline-none">
                <div class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-600 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </div>
            
            <div class="flex flex-wrap items-center gap-4">
                {{-- Filtro Deuda --}}
                <div class="flex-1 sm:flex-none">
                    <select name="deuda_gen" onchange="this.form.submit()" 
                            class="w-full sm:w-48 px-5 py-4 bg-slate-50 border-2 border-slate-50 rounded-2xl text-[10px] font-black uppercase tracking-widest text-slate-600 focus:bg-white focus:border-blue-600 transition-all outline-none cursor-pointer">
                        <option value="">Estado de Pago</option>
                        <option value="al_dia" {{ request('deuda_gen') === 'al_dia' ? 'selected' : '' }}>✅ Al Día</option>
                        <option value="deudores" {{ request('deuda_gen') === 'deudores' ? 'selected' : '' }}>❌ Deudores</option>
                    </select>
                </div>

                {{-- Filtro Género --}}
                <div class="flex-1 sm:flex-none">
                    <select name="genero_gen" onchange="this.form.submit()" 
                            class="w-full sm:w-40 px-5 py-4 bg-slate-50 border-2 border-slate-50 rounded-2xl text-[10px] font-black uppercase tracking-widest text-slate-600 focus:bg-white focus:border-blue-600 transition-all outline-none cursor-pointer">
                        <option value="">Género</option>
                        <option value="Masculino" {{ request('genero_gen') === 'Masculino' ? 'selected' : '' }}>♂️ Masculino</option>
                        <option value="Femenino" {{ request('genero_gen') === 'Femenino' ? 'selected' : '' }}>♀️ Femenino</option>
                    </select>
                </div>

                {{-- Botón Buscar --}}
                <button type="submit" class="px-10 py-4 bg-blue-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-blue-700 transition-all shadow-xl shadow-blue-200 active:scale-95">
                    Filtrar Club
                </button>

                @if(request()->anyFilled(['search', 'genero_gen', 'deuda_gen']))
                    <a href="{{ route('coach.atletas') }}#general-search" class="p-4 bg-rose-50 text-rose-600 rounded-2xl hover:bg-rose-100 transition-colors" title="Limpiar filtros">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Resultados del Buscador General --}}
    @if($atletasGeneral->isEmpty())
        <div class="bg-white rounded-[2rem] p-16 text-center border-2 border-dashed border-slate-100">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-200">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800">No hay resultados</h3>
            <p class="text-slate-400 text-sm mt-2 max-w-sm mx-auto">No se encontraron atletas en el club que coincidan con los criterios de búsqueda actuales.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 xxl:grid-cols-5 gap-8">
            @foreach($atletasGeneral as $atleta)
                @include('coach.partials.athlete_card', ['atleta' => $atleta])
            @endforeach
        </div>
        
        @if($atletasGeneral instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-12 px-4">
                {{ $atletasGeneral->links() }}
            </div>
        @endif
    @endif
</div>

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

        window.dispatchEvent(new CustomEvent('athlete-selected'));
    }
</script>
@endpush
@endsection
