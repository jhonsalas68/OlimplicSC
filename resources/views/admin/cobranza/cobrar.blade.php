@extends('layouts.admin')
@section('title', 'Registrar Cobro')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-slate-400">
        <a href="{{ route('cobranza.index') }}" class="hover:text-blue-600 transition-colors">Cobranza</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-slate-600 font-medium">Registrar cobro</span>
    </div>

    {{-- ── TARJETA DEL ATLETA ── --}}
    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-blue-900 via-blue-800 to-red-700 px-6 py-5 flex items-center gap-4">
            @if($athlete->foto)
                <img src="{{ asset('storage/' . $athlete->foto) }}"
                     class="h-16 w-16 rounded-full object-cover border-2 border-white/30 shadow-md flex-shrink-0" alt="">
            @else
                <div class="h-16 w-16 rounded-full bg-white/20 border-2 border-white/30 flex items-center justify-center text-white font-black text-2xl flex-shrink-0">
                    {{ strtoupper(substr($athlete->nombre, 0, 1)) }}
                </div>
            @endif
            <div class="flex-1 min-w-0">
                <p class="text-white font-black text-xl leading-tight truncate">
                    {{ $athlete->nombre }} {{ $athlete->apellido_paterno }} {{ $athlete->apellido_materno }}
                </p>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1">
                    <span class="text-blue-200 text-sm font-mono font-semibold">{{ $athlete->id_alfanumerico_unico }}</span>
                    <span class="text-blue-300 text-xs">·</span>
                    <span class="text-blue-200 text-sm">C.I. {{ $athlete->ci }}</span>
                    <span class="text-blue-300 text-xs">·</span>
                    <span class="inline-flex items-center px-2 py-0.5 bg-white/20 text-white text-xs font-semibold rounded-full">
                        {{ $athlete->category->nombre ?? '—' }}
                    </span>
                </div>
            </div>
            <a href="{{ route('cobranza.index') }}"
               class="flex-shrink-0 h-8 w-8 bg-white/10 hover:bg-white/20 rounded-lg flex items-center justify-center transition-colors"
               title="Cambiar atleta">
                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </a>
        </div>

        {{-- Historial rápido --}}
        @php
            $pagos = $athlete->payments()->latest()->take(3)->get();
        @endphp
        <div class="px-6 py-3 bg-slate-50 border-b border-slate-100">
            @if($pagos->isEmpty())
                <div class="flex items-center gap-2 text-sm">
                    <span class="inline-block w-2 h-2 rounded-full bg-red-400 flex-shrink-0"></span>
                    <span class="text-red-500 font-semibold">Sin pagos registrados</span>
                </div>
            @else
                <div class="flex items-center gap-4 flex-wrap">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Últimos pagos:</span>
                    @foreach($pagos as $p)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-xs text-slate-600 shadow-sm">
                            @if($p->concepto === 'mensualidad')
                                <svg class="h-3 w-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            @else
                                <svg class="h-3 w-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            @endif
                            {{ $p->mes_correspondiente }} — <strong>Bs. {{ number_format($p->monto, 2) }}</strong>
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ── FORMULARIO DE COBRO ── --}}
    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-8 py-5 border-b border-slate-100 flex items-center gap-3">
            <div class="h-9 w-9 bg-blue-50 rounded-lg flex items-center justify-center">
                <svg class="h-5 w-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-base font-bold text-slate-800">Nuevo Cobro</h2>
                <p class="text-xs text-slate-400">Completa los datos del cobro a registrar</p>
            </div>
        </div>

        <form action="{{ route('cobranza.cobrar') }}" method="POST" class="px-8 py-6 space-y-6" id="form-cobro">
            @csrf
            <input type="hidden" name="athlete_id" value="{{ $athlete->id }}">

            {{-- ── CONCEPTO ── --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-3">
                    Tipo de cobro <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-3">

                    {{-- Mensualidad --}}
                    <label class="cursor-pointer group">
                        <input type="radio" name="concepto" value="mensualidad" class="sr-only peer"
                               id="radio-mensualidad"
                               {{ old('concepto', 'mensualidad') === 'mensualidad' ? 'checked' : '' }}>
                        <div class="relative flex flex-col items-center gap-2 px-4 py-5 border-2 border-slate-200 rounded-xl
                                    peer-checked:border-blue-600 peer-checked:bg-blue-50 transition-all group-hover:border-blue-300">
                            <div class="h-10 w-10 rounded-xl bg-blue-100 peer-checked:bg-blue-200 flex items-center justify-center">
                                <svg class="h-5 w-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <span class="text-sm font-bold text-slate-700">Mensualidad</span>
                            <span class="text-xs text-slate-400 text-center">Cuota mensual del atleta</span>
                            <div class="absolute top-3 right-3 h-4 w-4 rounded-full border-2 border-slate-300
                                        peer-checked:border-blue-600 peer-checked:bg-blue-600 transition-all
                                        flex items-center justify-center" id="check-mensualidad">
                            </div>
                        </div>
                    </label>

                    {{-- Artículo deportivo --}}
                    <label class="cursor-pointer group">
                        <input type="radio" name="concepto" value="articulo_deportivo" class="sr-only peer"
                               id="radio-articulo"
                               {{ old('concepto') === 'articulo_deportivo' ? 'checked' : '' }}>
                        <div class="relative flex flex-col items-center gap-2 px-4 py-5 border-2 border-slate-200 rounded-xl
                                    peer-checked:border-red-600 peer-checked:bg-red-50 transition-all group-hover:border-red-300">
                            <div class="h-10 w-10 rounded-xl bg-red-100 flex items-center justify-center">
                                <svg class="h-5 w-5 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </div>
                            <span class="text-sm font-bold text-slate-700">Artículo Deportivo</span>
                            <span class="text-xs text-slate-400 text-center">Venta de equipamiento</span>
                        </div>
                    </label>
                </div>
            </div>

            {{-- ── CAMPOS DINÁMICOS ── --}}

            {{-- Mes (mensualidad) --}}
            <div id="campo-mes">
                <label class="block text-sm font-semibold text-slate-700 mb-1">
                    Mes correspondiente <span class="text-red-500">*</span>
                </label>
                <input type="month" name="mes_correspondiente" id="mes_correspondiente"
                    value="{{ old('mes_correspondiente', now()->format('Y-m')) }}"
                    class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-500 sm:text-sm">
                <p class="mt-1 text-xs text-slate-400">Selecciona el mes al que corresponde este pago</p>
            </div>

            {{-- Descripción (artículo) --}}
            <div id="campo-descripcion" class="hidden">
                <label class="block text-sm font-semibold text-slate-700 mb-1">
                    Descripción del artículo <span class="text-red-500">*</span>
                </label>
                <input type="text" name="descripcion" id="descripcion"
                    value="{{ old('descripcion') }}"
                    placeholder="Ej: Camiseta oficial talla M, Balón de voleibol, Rodilleras..."
                    class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm
                           focus:outline-none focus:ring-2 focus:ring-red-500 sm:text-sm">
                <p class="mt-1 text-xs text-slate-400">Describe el artículo vendido</p>
            </div>

            {{-- ── MONTO ── --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">
                    Monto <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <span class="text-slate-500 font-bold text-sm">Bs.</span>
                    </div>
                    <input type="number" name="monto" value="{{ old('monto') }}"
                        min="0.01" step="0.01" placeholder="0.00" required
                        class="block w-full pl-14 pr-4 py-3 border border-slate-200 rounded-xl shadow-sm text-lg font-bold
                               focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>
            </div>

            {{-- ── RESUMEN PREVIO ── --}}
            <div id="resumen" class="bg-gradient-to-r from-blue-900 to-blue-800 rounded-xl p-5 text-white hidden">
                <p class="text-xs text-blue-300 uppercase tracking-wider font-semibold mb-3">Resumen del cobro</p>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-blue-200">Atleta:</span>
                        <span class="font-semibold">{{ $athlete->nombre }} {{ $athlete->apellido_paterno }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-blue-200">Concepto:</span>
                        <span class="font-semibold" id="resumen-concepto">—</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-blue-200">Detalle:</span>
                        <span class="font-semibold" id="resumen-detalle">—</span>
                    </div>
                    <div class="flex justify-between border-t border-blue-700 pt-2 mt-2">
                        <span class="text-blue-200 font-bold">Total:</span>
                        <span class="font-black text-lg text-white" id="resumen-monto">Bs. 0.00</span>
                    </div>
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex gap-3 pt-2">
                <a href="{{ route('cobranza.index') }}"
                   class="flex-none px-6 py-3 border border-slate-200 text-slate-600 font-semibold rounded-xl
                          hover:bg-slate-50 transition-all text-sm flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Volver
                </a>
                <button type="submit"
                    class="flex-1 py-3 bg-blue-900 hover:bg-blue-800 text-white font-bold rounded-xl
                           transition-all text-sm flex items-center justify-center gap-2 shadow-lg shadow-blue-900/20">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Registrar Cobro y Generar Nota de Venta
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleConcepto(val) {
    const esMensualidad = val === 'mensualidad';
    document.getElementById('campo-mes').classList.toggle('hidden', !esMensualidad);
    document.getElementById('campo-descripcion').classList.toggle('hidden', esMensualidad);

    const mesInput = document.getElementById('mes_correspondiente');
    const descInput = document.getElementById('descripcion');
    if (esMensualidad) {
        mesInput.setAttribute('required', '');
        descInput.removeAttribute('required');
    } else {
        mesInput.removeAttribute('required');
        descInput.setAttribute('required', '');
    }
    actualizarResumen();
}

function actualizarResumen() {
    const concepto = document.querySelector('[name=concepto]:checked')?.value;
    const monto    = parseFloat(document.querySelector('[name=monto]').value) || 0;
    const mes      = document.getElementById('mes_correspondiente').value;
    const desc     = document.getElementById('descripcion').value;
    const resumen  = document.getElementById('resumen');

    if (!concepto || monto <= 0) { resumen.classList.add('hidden'); return; }

    resumen.classList.remove('hidden');
    document.getElementById('resumen-concepto').textContent =
        concepto === 'mensualidad' ? 'Mensualidad' : 'Artículo Deportivo';
    document.getElementById('resumen-detalle').textContent =
        concepto === 'mensualidad'
            ? (mes ? formatMes(mes) : '—')
            : (desc || '—');
    document.getElementById('resumen-monto').textContent =
        'Bs. ' + monto.toFixed(2);
}

function formatMes(ym) {
    const [y, m] = ym.split('-');
    const meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                   'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    return `${meses[parseInt(m)-1]} ${y}`;
}

// Escuchar cambios en todos los campos para actualizar resumen
document.addEventListener('DOMContentLoaded', () => {
    // Inicializar estado según radio seleccionado
    const checked = document.querySelector('[name=concepto]:checked');
    if (checked) toggleConcepto(checked.value);

    // Listeners
    document.querySelectorAll('[name=concepto]').forEach(r =>
        r.addEventListener('change', () => toggleConcepto(r.value)));
    document.querySelector('[name=monto]').addEventListener('input', actualizarResumen);
    document.getElementById('mes_correspondiente').addEventListener('change', actualizarResumen);
    document.getElementById('descripcion').addEventListener('input', actualizarResumen);
});
</script>
@endsection
