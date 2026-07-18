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
        <div class="flex items-center gap-2">
            <div class="relative flex-1">
                <input type="text" id="buscador" placeholder="Nombre, apellido o CI..." autocomplete="off"
                       class="w-full pl-4 pr-10 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition-all">
                <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                    <svg id="search-spinner" class="hidden h-4 w-4 text-blue-500 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <svg id="search-icon" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
            <button type="button" id="btn-buscar" class="px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-blue-200/50 flex-shrink-0">
                Buscar
            </button>
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
                    <p id="atleta-ultimo-pago" class="text-xs text-slate-400 mt-1"></p>
                </div>
            </div>

            <form action="{{ route('cobros.cobrar') }}" method="POST" onsubmit="return prepararEnvio(event)">
                @csrf
                <input type="hidden" name="athlete_id" id="athlete_id">
                <input type="hidden" name="items_json" id="items_json">

                <!-- Sección A: Constructor de Ítems (Fuera del Formulario Principal) -->
                <div class="mb-6 p-4 border border-blue-100 bg-blue-50/10 rounded-2xl">
                    <h3 class="text-xs font-bold text-blue-700 uppercase tracking-widest mb-3 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Agregar Ítem al Cobro
                    </h3>
                    
                    <div class="mb-3">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2">Concepto</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="concepto-btn cursor-pointer">
                                <input type="radio" name="add_concepto_radio" value="mensualidad" checked class="sr-only">
                                <div class="border-2 border-slate-200 rounded-xl p-2.5 text-center transition-all hover:border-blue-400 concepto-card">
                                    <span class="text-xs font-semibold text-slate-600 concepto-label">Mensualidad</span>
                                </div>
                            </label>
                            <label class="concepto-btn cursor-pointer">
                                <input type="radio" name="add_concepto_radio" value="articulo_deportivo" class="sr-only">
                                <div class="border-2 border-slate-200 rounded-xl p-2.5 text-center transition-all hover:border-blue-400 concepto-card">
                                    <span class="text-xs font-semibold text-slate-600 concepto-label">Artículo Deportivo</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div id="campo-mes" class="mb-3">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1.5">Mes Correspondiente</label>
                        <input type="month" id="add_mes_correspondiente" value="{{ now()->format('Y-m') }}"
                               class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="mb-3">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1.5">
                            Descripción <span class="text-slate-400 font-normal normal-case">(opcional)</span>
                        </label>
                        <input type="text" id="add_descripcion" placeholder="Ej: Polera talla G, matrícula, etc..."
                               class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="grid grid-cols-3 gap-3 items-end">
                        <div class="col-span-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1.5">Monto (Bs.)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 text-xs font-bold">Bs.</span>
                                <input type="number" id="add_monto" step="0.01" min="0.01" placeholder="0.00"
                                       class="w-full pl-9 pr-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-semibold">
                            </div>
                        </div>
                        <button type="button" onclick="agregarItemAlCarrito()" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-md shadow-blue-200 flex items-center justify-center gap-1 h-[38px] cursor-pointer">
                            ➕ Añadir
                        </button>
                    </div>
                </div>



                    <!-- Sección B: Carrito de Ítems (Tabla) -->
                    <div id="carrito-items-wrapper" class="hidden mb-5">
                        <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Resumen de Cobro</label>
                        <div class="border border-slate-150 rounded-xl overflow-hidden bg-slate-50 shadow-inner">
                            <table class="w-full text-xs text-left text-slate-600">
                                <thead class="bg-slate-100 text-[9px] uppercase font-black text-slate-500 border-b border-slate-200">
                                    <tr>
                                        <th class="p-2.5">Concepto</th>
                                        <th class="p-2.5">Detalle / Período</th>
                                        <th class="p-2.5 text-right">Monto</th>
                                        <th class="p-2.5 text-center w-10"></th>
                                    </tr>
                                </thead>
                                <tbody id="carrito-tbody">
                                    <!-- Ítems dinámicos -->
                                </tbody>
                            </table>
                            <div class="p-3 bg-slate-100/70 border-t border-slate-200 flex items-center justify-between font-black text-slate-800 text-xs">
                                <span class="uppercase tracking-wider">Total a Cobrar:</span>
                                <span id="carrito-total-label" class="text-sm text-blue-700 font-extrabold">Bs. 0.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Método de Pago</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="metodo-btn cursor-pointer">
                                <input type="radio" name="metodo_pago" value="efectivo" class="sr-only" required checked>
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
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">
                            WhatsApp para Nota <span class="text-slate-400 font-normal normal-case">(opcional)</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.237 3.483 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.308 1.652zm6.799-3.814c1.543.917 3.31 1.398 5.103 1.399h.005c5.454 0 9.893-4.438 9.895-9.892.001-2.641-1.027-5.127-2.896-6.996s-4.355-2.896-6.998-2.897c-5.453 0-9.891 4.439-9.894 9.894-.001 1.756.459 3.468 1.329 4.972l-.875 3.195 3.268-.857zm11.361-4.947c-.273-.137-1.62-.8-1.87-.891-.249-.09-.431-.137-.613.137-.182.273-.706.891-.865 1.072-.158.182-.317.204-.59.068-.273-.137-1.15-.424-2.19-1.353-.809-.721-1.355-1.612-1.513-1.886-.158-.273-.017-.422.12-.558.122-.122.273-.318.409-.477.136-.159.182-.273.272-.455.09-.181.046-.341-.023-.477-.068-.137-.613-1.477-.841-2.022-.222-.533-.448-.46-.613-.468h-.523c-.182 0-.477.067-.727.341-.25.272-.954.932-.954 2.271 0 1.34.977 2.636 1.114 2.818.136.182 1.921 2.934 4.653 4.111.649.279 1.157.446 1.552.571.652.207 1.245.178 1.713.108.522-.078 1.62-.662 1.848-1.27.227-.609.227-1.133.159-1.272-.068-.138-.25-.227-.523-.364z"/>
                                </svg>
                            </span>
                            <input type="tel" name="whatsapp_number" id="whatsapp_number" placeholder="7XXXXXXXX"
                                   class="w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                                   title="Ingresa el número de celular para enviar la nota">
                        </div>
                    </div>

                    <button type="submit" id="btn-procesar-cobro" disabled class="w-full py-3 px-4 bg-slate-200 text-slate-400 font-semibold rounded-xl shadow-sm transition-all flex items-center justify-center gap-2 cursor-not-allowed">
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
.atleta-item{transition:all 0.15s ease}
.atleta-item:hover{background-color:#eff6ff;border-color:#93c5fd}
.atleta-item.selected{background-color:#dbeafe;border-color:#2563eb}
</style>

<script>
(function() {
    let searchTimeout = null;
    const buscador = document.getElementById('buscador');
    const resultados = document.getElementById('resultados');
    const sinResultados = document.getElementById('sin-resultados');
    const placeholderBusqueda = document.getElementById('placeholder-busqueda');
    const panelVacio = document.getElementById('panel-vacio');
    const panelCobro = document.getElementById('panel-cobro');
    const spinner = document.getElementById('search-spinner');
    const btnBuscar = document.getElementById('btn-buscar');
    const searchIcon = document.getElementById('search-icon');

    if (!buscador) return;

    function performSearch() {
        const q = buscador.value.trim();
        if (q.length < 2) {
            resultados.innerHTML = '';
            sinResultados.classList.add('hidden');
            placeholderBusqueda.classList.remove('hidden');
            return;
        }
        
        placeholderBusqueda.classList.add('hidden');
        spinner.classList.remove('hidden');
        searchIcon.classList.add('hidden');

        fetch(`{{ route('cobros.search') }}?q=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(data => {
                spinner.classList.add('hidden');
                searchIcon.classList.remove('hidden');
                resultados.innerHTML = '';
                if (data.length === 0) {
                    sinResultados.classList.remove('hidden');
                    return;
                }
                sinResultados.classList.add('hidden');
                data.forEach(atleta => {
                    const div = document.createElement('div');
                    div.className = 'atleta-item flex items-center gap-3 p-3 border border-slate-100 rounded-xl cursor-pointer shadow-sm hover:shadow-md transition-all';
                    const avatarSrc = atleta.foto ? (atleta.foto.startsWith('http') ? atleta.foto : '/storage/' + atleta.foto) : null;
                    const fotoHtml = avatarSrc
                        ? `<img src="${avatarSrc}" class="w-10 h-10 rounded-full object-cover flex-shrink-0 border border-slate-100">`
                        : `<div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-600 to-red-600 flex items-center justify-center text-white font-bold text-[10px] flex-shrink-0">${atleta.iniciales}</div>`;
                    
                    const statusBadge = atleta.pagado_mes_actual 
                        ? '<span class="px-2 py-0.5 rounded text-[8px] font-black uppercase bg-emerald-100 text-emerald-700 border border-emerald-200">Al Día</span>'
                        : '<span class="px-2 py-0.5 rounded text-[8px] font-black uppercase bg-rose-100 text-rose-700 border border-rose-200">Debe</span>';

                    div.innerHTML = `${fotoHtml}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm font-semibold text-slate-800 truncate">${atleta.nombre_completo}</p>
                                ${statusBadge}
                            </div>
                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">CI: ${atleta.ci} &middot; ${atleta.categoria}</p>
                        </div>`;
                    
                    div.addEventListener('click', () => seleccionarAtleta(atleta, div));
                    resultados.appendChild(div);
                });
            })
            .catch(() => {
                spinner.classList.add('hidden');
                searchIcon.classList.remove('hidden');
            });
    }

    function seleccionarAtleta(atleta, el) {
        document.querySelectorAll('.atleta-item').forEach(i => i.classList.remove('selected'));
        el.classList.add('selected');
        document.getElementById('athlete_id').value = atleta.id;
        const avatarEl = document.getElementById('atleta-avatar');
        const avatarSrc = atleta.foto ? (atleta.foto.startsWith('http') ? atleta.foto : '/storage/' + atleta.foto) : null;
        
        avatarEl.innerHTML = avatarSrc 
            ? `<img src="${avatarSrc}" class="w-full h-full object-cover">` 
            : atleta.iniciales;
            
        document.getElementById('atleta-nombre').textContent = atleta.nombre_completo;
        document.getElementById('atleta-meta').textContent = `CI: ${atleta.ci} · ${atleta.categoria}`;
        
        const ultimoLabel = atleta.ultimo_pago ? `Último: ${atleta.ultimo_pago}` : 'Sin pagos';
        const statusHtml = atleta.pagado_mes_actual 
            ? `<span class="text-emerald-600 font-bold flex items-center gap-1 justify-end"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg> AL DÍA</span>`
            : `<span class="text-rose-600 font-bold flex items-center gap-1 justify-end"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg> DEBE</span>`;
            
        document.getElementById('atleta-ultimo-pago').innerHTML = `${statusHtml} <p class="text-[10px] text-slate-400 mt-0.5">${ultimoLabel}</p>`;
        
        // Reset carrito al cambiar de atleta
        carrito = [];
        renderCarrito();
        
        panelVacio.classList.add('hidden');
        panelCobro.classList.remove('hidden');
    }

    buscador.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 500);
    });

    buscador.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(searchTimeout);
            performSearch();
        }
    });

    btnBuscar.addEventListener('click', function() {
        clearTimeout(searchTimeout);
        performSearch();
    });

    // LÓGICA DEL CARRITO
    let carrito = [];

    // Toggles de concepto del subformulario
    document.querySelectorAll('input[name="add_concepto_radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('campo-mes').classList.toggle('hidden', this.value !== 'mensualidad');
        });
    });

    window.agregarItemAlCarrito = function() {
        const concepto = document.querySelector('input[name="add_concepto_radio"]:checked').value;
        const mes = document.getElementById('add_mes_correspondiente').value;
        const descripcion = document.getElementById('add_descripcion').value.trim();
        const montoInput = document.getElementById('add_monto');
        const monto = parseFloat(montoInput.value);

        if (isNaN(monto) || monto <= 0) {
            alert('Por favor, ingresa un monto válido mayor a 0.');
            return;
        }

        if (concepto === 'mensualidad' && !mes) {
            alert('Por favor, selecciona el mes correspondiente para la mensualidad.');
            return;
        }

        const item = {
            id: Date.now(),
            concepto: concepto,
            mes_correspondiente: concepto === 'mensualidad' ? mes : null,
            descripcion: descripcion !== '' ? descripcion : null,
            monto: monto
        };

        carrito.push(item);
        renderCarrito();

        // Limpiar subformulario
        document.getElementById('add_descripcion').value = '';
        montoInput.value = '';
    }

    window.removerItem = function(itemId) {
        carrito = carrito.filter(item => item.id !== itemId);
        renderCarrito();
    }

    function renderCarrito() {
        const tbody = document.getElementById('carrito-tbody');
        const wrapper = document.getElementById('carrito-items-wrapper');
        const totalLabel = document.getElementById('carrito-total-label');
        const btnProcesar = document.getElementById('btn-procesar-cobro');

        tbody.innerHTML = '';
        let total = 0;

        if (carrito.length === 0) {
            wrapper.classList.add('hidden');
            btnProcesar.disabled = true;
            btnProcesar.className = "w-full py-3 px-4 bg-slate-200 text-slate-400 font-semibold rounded-xl shadow-sm transition-all flex items-center justify-center gap-2 cursor-not-allowed";
            return;
        }

        wrapper.classList.remove('hidden');
        btnProcesar.disabled = false;
        btnProcesar.className = "w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-xl shadow-sm transition-all flex items-center justify-center gap-2 cursor-pointer";

        carrito.forEach(item => {
            total += item.monto;
            const tr = document.createElement('tr');
            tr.className = "border-b border-slate-200";

            const conceptoLabel = item.concepto === 'mensualidad' ? 'Mensualidad' : 'Artículo';
            const detalleLabel = item.concepto === 'mensualidad' ? item.mes_correspondiente : (item.descripcion || 'Artículo Deportivo');

            tr.innerHTML = `
                <td class="p-2.5 font-bold text-slate-800">${conceptoLabel}</td>
                <td class="p-2.5 text-slate-500">${detalleLabel} ${item.concepto === 'mensualidad' && item.descripcion ? ' - ' + item.descripcion : ''}</td>
                <td class="p-2.5 text-right font-semibold text-slate-800">Bs. ${item.monto.toFixed(2)}</td>
                <td class="p-2.5 text-center">
                    <button type="button" onclick="removerItem(${item.id})" class="text-rose-500 hover:text-rose-700 transition-colors p-1" title="Eliminar ítem">
                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        totalLabel.textContent = `Bs. ${total.toFixed(2)}`;
    }

    window.prepararEnvio = function(e) {
        if (carrito.length === 0) {
            alert('Debes agregar al menos un ítem al recibo.');
            e.preventDefault();
            return false;
        }
        document.getElementById('items_json').value = JSON.stringify(carrito);
        return true;
    }
})();
</script>
@endsection
