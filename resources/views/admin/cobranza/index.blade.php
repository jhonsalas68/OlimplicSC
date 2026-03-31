@extends('layouts.admin')
@section('title', 'Cobranza')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-blue-900 via-blue-800 to-red-700 rounded-2xl px-8 py-7 flex items-center gap-5">
        <div class="h-14 w-14 bg-white/15 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">Módulo de Cobranza</h1>
            <p class="text-blue-200 text-sm mt-0.5">Busca al atleta por nombre o C.I. para registrar un cobro</p>
        </div>
    </div>

    {{-- Buscador --}}
    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-8">

        @if($errors->any())
            <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Campo de búsqueda con autocompletado --}}
        <div class="relative" x-data="buscador()">
            <label class="block text-sm font-semibold text-slate-700 mb-2">
                Buscar atleta por nombre o C.I.
            </label>
            <div class="relative">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                </svg>
                <input
                    type="text"
                    id="search-input"
                    placeholder="Escribe nombre o número de C.I..."
                    autocomplete="off"
                    class="block w-full pl-12 pr-4 py-3.5 border border-slate-200 rounded-xl shadow-sm text-base
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                    oninput="buscarAtletas(this.value)"
                    onkeydown="if(event.key==='Escape') cerrarResultados()">
                <div id="spinner" class="hidden absolute right-4 top-1/2 -translate-y-1/2">
                    <svg class="animate-spin h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                    </svg>
                </div>
            </div>

            {{-- Resultados dropdown --}}
            <div id="resultados"
                 class="hidden absolute z-50 w-full mt-2 bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden">
            </div>
        </div>

        {{-- Formulario oculto que se envía al seleccionar --}}
        <form id="form-buscar" action="{{ route('cobranza.buscar') }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="ci" id="ci-seleccionado">
        </form>

        <p class="mt-3 text-xs text-slate-400 flex items-center gap-1.5">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Escribe al menos 2 caracteres para ver resultados
        </p>
    </div>
</div>

<script>
let debounceTimer = null;

function buscarAtletas(query) {
    clearTimeout(debounceTimer);
    const resultados = document.getElementById('resultados');
    const spinner    = document.getElementById('spinner');

    if (query.trim().length < 2) {
        resultados.classList.add('hidden');
        resultados.innerHTML = '';
        return;
    }

    spinner.classList.remove('hidden');
    debounceTimer = setTimeout(() => {
        fetch(`{{ route('cobranza.search') }}?q=${encodeURIComponent(query)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            spinner.classList.add('hidden');
            renderResultados(data);
        })
        .catch(() => spinner.classList.add('hidden'));
    }, 280);
}

function renderResultados(atletas) {
    const box = document.getElementById('resultados');
    if (!atletas.length) {
        box.innerHTML = `
            <div class="px-5 py-6 text-center text-slate-400 text-sm">
                <svg class="h-8 w-8 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                No se encontró ningún atleta
            </div>`;
        box.classList.remove('hidden');
        return;
    }

    box.innerHTML = atletas.map(a => `
        <button type="button" onclick="seleccionar('${a.ci}')"
            class="w-full flex items-center gap-4 px-5 py-3.5 hover:bg-blue-50 transition-colors border-b border-slate-100 last:border-0 text-left">
            <div class="h-10 w-10 rounded-full flex-shrink-0 flex items-center justify-center font-bold text-white text-sm
                        ${a.foto ? '' : 'bg-gradient-to-br from-blue-700 to-red-600'}">
                ${a.foto
                    ? `<img src="${a.foto.startsWith('http') ? a.foto : '/storage/' + a.foto}" class="h-10 w-10 rounded-full object-cover">`
                    : a.iniciales}
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-slate-800 text-sm truncate">${a.nombre_completo}</p>
                <p class="text-xs text-slate-400 mt-0.5">
                    C.I. ${a.ci}
                    &nbsp;·&nbsp; ${a.categoria}
                </p>
            </div>
            <div class="flex-shrink-0">
                ${a.ultimo_pago
                    ? `<span class="text-xs text-emerald-600 font-medium">${a.ultimo_pago}</span>`
                    : `<span class="text-xs text-red-400 font-medium">Sin pagos</span>`}
            </div>
        </button>
    `).join('');
    box.classList.remove('hidden');
}

function seleccionar(ci) {
    document.getElementById('ci-seleccionado').value = ci;
    document.getElementById('form-buscar').submit();
}

function cerrarResultados() {
    document.getElementById('resultados').classList.add('hidden');
}

document.addEventListener('click', e => {
    if (!e.target.closest('#search-input') && !e.target.closest('#resultados')) {
        cerrarResultados();
    }
});
</script>
@endsection
