@extends('layouts.admin')

@section('title', 'Cobros')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Panel de Cobros</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Registra pagos de mensualidades y articulos deportivos</p>
    </div>
    <a href="{{ route('payments.index') }}" class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1 font-medium">
        Ver historial de pagos
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h2 class="text-base font-semibold text-slate-700 mb-4">Buscar Atleta</h2>
        <div class="relative">
            <input type="text" id="buscador" placeholder="Nombre, apellido o CI..." autocomplete="off"
                   class="w-full pl-4 pr-10 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
            <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                <svg id="search-spinner" class="hidden h-4 w-4 text-slate-400 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
            </div>
        </div>
        <div id="resultados" class="mt-3 space-y-2 max-h-96 overflow-y-auto"></div>
        <div id="sin-resultados" class="hidden mt-6 text-center text-slate-400 text-sm py-8">No se encontraron atletas</div>
        <div id="placeholder-busqueda" class="mt-6 text-center text-slate-300 text-sm py-8">Escribe para buscar un atleta</div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <div id="panel-vacio" class="flex flex-col items-center justify-center h-full py-16 text-center">
            <div class="w-16 h-16 rounded-full bg-blue-50 flex items-center justify-center mb-4">
                <svg class="h-8 w-8 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
            </div>
            <p class="text-slate-400 text-sm">Selecciona un atleta para registrar un cobro</p>
        </div>

        <div id="panel-cobro" class="hidden">
            <div class="flex items-center gap-4 mb-5 p-4 bg-slate-50 rounded-xl border border-slate-100">
                <div id="atleta-avatar" class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-600 to-red-600 flex items-center justify-center text-white font-bold text-lg flex-shrink-0 overflow-hidden"></div>
                <div class="flex-1 min-w-0">
                    <p id="atleta-nombre" class="font-semibold text-slate-800 text-sm truncate"></p>
                    <p id="atleta-meta" class="text-xs text-slate-500 mt-0.5"></p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p id="atleta-codigo" class="text-xs font-mono font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md"></p>
                    <p id="atleta-ultimo-pago" class="text-xs text-slate-400 mt-1"></p>
                </div>
            </div>

            <form action="{{ route('cobros.cobrar') }}" method="POST">
                @csrf
                <input type="hidden" name="athlete_id" id="athlete_id">

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Concepto</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="concepto-btn cursor-pointer">
                            <input type="radio" name="concepto" value="mensualidad" class="sr-only" required>
                            <div class="border-2 border-slate-200 rounded-xl p-3 text-center transition-all hover:border-blue-400 concepto-card">
                                <svg class="h-6 w-6 mx-auto mb-1 text-slate-400 concepto-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-xs font-semibold text-slate-600 concepto-label">Mensualidad</span>
                            </div>
                        </label>
                        <label class="concepto-btn cursor-pointer">
                            <input type="radio" name="concepto" value="articulo_deportivo" class="sr-only">
                            <div class="border-2 border-slate-200 rounded-xl p-3 text-center transition-all hover:border-blue-400 concepto-card">
                                <svg class="h-6 w-6 mx-auto mb-1 text-slate-400 concepto-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                <span class="text-xs font-semibold text-slate-600 concepto-label">Articulo Deportivo</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div id="campo-mes" class="mb-4 hidden">
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Mes Correspondiente</label>
                    <input type="month" name="mes_correspondiente" value="{{ now()->format('Y-m') }}"
                           class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">
                        Descripcion <span class="text-slate-400 font-normal normal-case">(opcional)</span>
                    </label>
                    <input type="text" name="descripcion" placeholder="Ej: Uniforme talla M, cuota enero..."
                           class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Monto (Bs.)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 text-sm font-semibold">Bs.</span>
                        <input type="number" name="monto" step="0.01" min="0.01" required placeholder="0.00"
                               class="w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-semibold">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Metodo de Pago</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="metodo-btn cursor-pointer">
                            <input type="radio" name="metodo_pago" value="efectivo" class="sr-only" required>
                            <div class="border-2 border-slate-200 rounded-xl p-2.5 text-center transition-all hover:border-green-400 metodo-card">
                                <svg class="h-5 w-5 mx-auto mb-1 text-slate-400 metodo-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span class="text-xs font-semibold text-slate-600 metodo-label">Efectivo</span>
                            </div>
                        </label>
                        <label class="metodo-btn cursor-pointer">
                            <input type="radio" name="metodo_pago" value="qr" class="sr-only">
                            <div class="border-2 border-slate-200 rounded-xl p-2.5 text-center transition-all hover:border-blue-400 metodo-card">
                                <svg class="h-5 w-5 mx-auto mb-1 text-slate-400 metodo-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                                <span class="text-xs font-semibold text-slate-600 metodo-label">QR</span>
                            </div>
                        </label>
                        <label class="metodo-btn cursor-pointer">
                            <input type="radio" name="metodo_pago" value="tarjeta" class="sr-only">
                            <div class="border-2 border-slate-200 rounded-xl p-2.5 text-center transition-all hover:border-purple-400 metodo-card">
                                <svg class="h-5 w-5 mx-auto mb-1 text-slate-400 metodo-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                <span class="text-xs font-semibold text-slate-600 metodo-label">Tarjeta</span>
                            </div>
                        </label>
                    </div>
                </div>

                <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-xl shadow-sm transition-all flex items-center justify-center gap-2">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Procesar Cobro
                </button>
            </form>
        </div>
    </div>
</div>

<style>
.concepto-btn input:checked ~ .concepto-card{border-color:#2563eb;background-color:#eff6ff}
.concepto-btn input:checked ~ .concepto-card .concepto-icon{color:#2563eb}
.concepto-btn input:checked ~ .concepto-card .concepto-label{color:#1d4ed8}
.metodo-btn input[value="efectivo"]:checked ~ .metodo-card{border-color:#16a34a;background-color:#f0fdf4}
.metodo-btn input[value="efectivo"]:checked ~ .metodo-card .metodo-icon{color:#16a34a}
.metodo-btn input[value="efectivo"]:checked ~ .metodo-card .metodo-label{color:#15803d}
.metodo-btn input[value="qr"]:checked ~ .metodo-card{border-color:#2563eb;background-color:#eff6ff}
.metodo-btn input[value="qr"]:checked ~ .metodo-card .metodo-icon{color:#2563eb}
.metodo-btn input[value="qr"]:checked ~ .metodo-card .metodo-label{color:#1d4ed8}
.metodo-btn input[value="tarjeta"]:checked ~ .metodo-card{border-color:#7c3aed;background-color:#f5f3ff}
.metodo-btn input[value="tarjeta"]:checked ~ .metodo-card .metodo-icon{color:#7c3aed}
.metodo-btn input[value="tarjeta"]:checked ~ .metodo-card .metodo-label{color:#6d28d9}
.atleta-item{transition:all 0.15s ease}
.atleta-item:hover{background-color:#eff6ff;border-color:#93c5fd}
.atleta-item.selected{background-color:#dbeafe;border-color:#2563eb}
</style>

<script>
let searchTimeout=null;
const buscador=document.getElementById('buscador');
const resultados=document.getElementById('resultados');
const sinResultados=document.getElementById('sin-resultados');
const placeholderBusqueda=document.getElementById('placeholder-busqueda');
const panelVacio=document.getElementById('panel-vacio');
const panelCobro=document.getElementById('panel-cobro');
const spinner=document.getElementById('search-spinner');

buscador.addEventListener('input',function(){
    const q=this.value.trim();
    clearTimeout(searchTimeout);
    if(q.length<2){resultados.innerHTML='';sinResultados.classList.add('hidden');placeholderBusqueda.classList.remove('hidden');return;}
    placeholderBusqueda.classList.add('hidden');
    spinner.classList.remove('hidden');
    searchTimeout=setTimeout(()=>{
        fetch(`{{ route('cobros.search') }}?q=${encodeURIComponent(q)}`)
            .then(r=>r.json())
            .then(data=>{
                spinner.classList.add('hidden');
                resultados.innerHTML='';
                if(data.length===0){sinResultados.classList.remove('hidden');return;}
                sinResultados.classList.add('hidden');
                data.forEach(atleta=>{
                    const div=document.createElement('div');
                    div.className='atleta-item flex items-center gap-3 p-3 border border-slate-100 rounded-xl cursor-pointer';
                    const avatarSrc = atleta.foto ? (atleta.foto.startsWith('http') ? atleta.foto : '/storage/' + atleta.foto) : null;
                    const fotoHtml=avatarSrc
                        ?`<img src="${avatarSrc}" class="w-10 h-10 rounded-full object-cover flex-shrink-0">`
                        :`<div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-600 to-red-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">${atleta.iniciales}</div>`;
                    div.innerHTML=`${fotoHtml}<div class="flex-1 min-w-0"><p class="text-sm font-semibold text-slate-800 truncate">${atleta.nombre_completo}</p><p class="text-xs text-slate-500">CI: ${atleta.ci} &middot; ${atleta.categoria}</p></div><span class="text-xs font-mono text-blue-600 bg-blue-50 px-2 py-0.5 rounded flex-shrink-0">${atleta.codigo??''}</span>`;
                    div.addEventListener('click',()=>seleccionarAtleta(atleta,div));
                    resultados.appendChild(div);
                });
            })
            .catch(()=>{spinner.classList.add('hidden');});
    },300);
});

function seleccionarAtleta(atleta,el){
    document.querySelectorAll('.atleta-item').forEach(i=>i.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('athlete_id').value=atleta.id;
    const avatarEl=document.getElementById('atleta-avatar');
    const avatarSrc = atleta.foto ? (atleta.foto.startsWith('http') ? atleta.foto : '/storage/' + atleta.foto) : null;
    avatarEl.innerHTML=avatarSrc?`<img src="${avatarSrc}" class="w-full h-full object-cover">`:atleta.iniciales;
    document.getElementById('atleta-nombre').textContent=atleta.nombre_completo;
    document.getElementById('atleta-meta').textContent=`CI: ${atleta.ci} · ${atleta.categoria}`;
    document.getElementById('atleta-codigo').textContent=atleta.codigo??'';
    document.getElementById('atleta-ultimo-pago').textContent=atleta.ultimo_pago?`Ultimo pago: ${atleta.ultimo_pago}`:'Sin pagos registrados';
    panelVacio.classList.add('hidden');
    panelCobro.classList.remove('hidden');
}

document.querySelectorAll('input[name="concepto"]').forEach(radio=>{
    radio.addEventListener('change',function(){
        document.getElementById('campo-mes').classList.toggle('hidden',this.value!=='mensualidad');
    });
});
</script>
@endsection
