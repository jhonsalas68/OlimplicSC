@extends('layouts.admin')
@section('title', 'Perfil del Atleta')

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- Barra superior --}}
    <div class="flex items-center justify-between mb-5">
        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-800 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver
        </a>

        @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('SuperAdmin') || auth()->user()->hasRole('Coach'))
        <button id="btn-toggle" onclick="toggleHabilitado({{ $athlete->id }})"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold border-2 transition-all
                       {{ $athlete->habilitado_booleano
                           ? 'bg-emerald-50 border-emerald-300 text-emerald-700 hover:bg-red-50 hover:border-red-300 hover:text-red-600'
                           : 'bg-red-50 border-red-300 text-red-600 hover:bg-emerald-50 hover:border-emerald-300 hover:text-emerald-700' }}">
            <span id="toggle-dot" class="w-2.5 h-2.5 rounded-full {{ $athlete->habilitado_booleano ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
            <span id="toggle-label">{{ $athlete->habilitado_booleano ? 'Habilitado' : 'Inhabilitado' }}</span>
            <svg class="h-4 w-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
        </button>
        @endif
    </div>

    {{-- Card perfil --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-4">

        {{-- Header Cover Temático del Club --}}
        <div class="h-32 w-full relative overflow-hidden bg-slate-100" 
             style="background-image: url('{{ asset('images/banner-login.jpg') }}'); background-size: cover; background-position: center;">
            {{-- Capa semi-transparente para mejorar el contraste --}}
            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
        </div>
        <div class="px-6 mb-6 relative z-10">
            <div class="flex flex-col sm:flex-row sm:items-end gap-5">
                <div class="-mt-12 w-24 h-24 rounded-2xl border-4 border-white shadow-md overflow-hidden bg-slate-50 flex-shrink-0 relative">
                    @if($athlete->foto)
                        <img src="{{ asset('storage/' . $athlete->foto) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-[#0b2d69] to-[#c61c2c] text-white font-black text-3xl">
                            {{ strtoupper(substr($athlete->nombre,0,1).substr($athlete->apellido_paterno??'',0,1)) }}
                        </div>
                    @endif
                </div>
                <div class="pb-1 min-w-0 flex-1">
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight mb-1">
                        {{ $athlete->nombre }} {{ $athlete->apellido_paterno }} {{ $athlete->apellido_materno }}
                    </h1>
                    <div class="flex items-center gap-2 flex-wrap">
                        @if($athlete->id_alfanumerico_unico)
                            <span class="text-xs font-bold font-mono text-[#0b2d69] bg-blue-50 px-2.5 py-1 rounded-md border border-blue-100 shadow-sm">
                                {{ $athlete->id_alfanumerico_unico }}
                            </span>
                        @endif
                        <span class="text-xs font-bold text-slate-700 bg-slate-100 px-2.5 py-1 rounded-md shadow-sm border border-slate-200 cursor-default">
                            {{ $athlete->category->nombre ?? 'Sin categoría' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Datos --}}
        <div class="px-6 pb-6 grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-4 border-t border-slate-100 pt-5">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Código de Atleta</p>
                <p class="text-sm font-semibold text-[#0b2d69] font-mono bg-blue-50 px-2 py-0.5 rounded border border-blue-100 inline-block">{{ $athlete->id_alfanumerico_unico ?? '—' }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">C.I.</p>
                <p class="text-sm font-semibold text-slate-800">{{ $athlete->ci }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Fecha de Nacimiento</p>
                <p class="text-sm font-semibold text-slate-800">{{ $athlete->fecha_nacimiento?->format('d/m/Y') ?? '—' }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Edad</p>
                <p class="text-sm font-semibold text-slate-800">{{ $athlete->edadActual() }} años</p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Género</p>
                <p class="text-sm font-semibold text-slate-800">{{ $athlete->genero ?? '—' }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Seguro Médico</p>
                <p class="text-sm font-semibold text-slate-800">
                    @if($athlete->tiene_seguro)
                        <span class="text-emerald-600">Sí</span>@if($athlete->seguro_compania) &mdash; {{ $athlete->seguro_compania }}@endif
                    @else
                        <span class="text-slate-400">No</span>
                    @endif
                </p>
            </div>
            @if($athlete->alergias)
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Alergias</p>
                <p class="text-sm font-semibold text-slate-800">{{ $athlete->alergias }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Contacto de emergencia --}}
    @php $esMenor = $athlete->edadActual() < 18; @endphp
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mb-4">
        <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">
            {{ $esMenor ? 'Padre / Tutor' : 'Contacto de Referencia' }}
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-4">
            @if($esMenor)
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Nombre</p>
                    <p class="text-sm font-semibold text-slate-800">
                        {{ trim(($athlete->nombre_padre ?? '') . ' ' . ($athlete->apellido_paterno_padre ?? '') . ' ' . ($athlete->apellido_materno_padre ?? '')) ?: '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Teléfono</p>
                    <p class="text-sm font-semibold text-slate-800">{{ $athlete->telefono_padre ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Relación</p>
                    <p class="text-sm font-semibold text-slate-800">{{ $athlete->relacion_contacto ?? '—' }}</p>
                </div>
            @else
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Nombre</p>
                    <p class="text-sm font-semibold text-slate-800">{{ $athlete->contacto_nombre ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Teléfono</p>
                    <p class="text-sm font-semibold text-slate-800">{{ $athlete->contacto_telefono ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Relación</p>
                    <p class="text-sm font-semibold text-slate-800">{{ $athlete->contacto_relacion ?? '—' }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Últimos pagos --}}
    @if($pagos->count())
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Últimos Pagos</h2>
        <div class="space-y-1">
            @foreach($pagos as $pago)
            <div class="flex items-center justify-between py-2.5 border-b border-slate-50 last:border-0">
                <div>
                    <p class="text-sm font-semibold text-slate-800">
                        {{ $pago->concepto === 'mensualidad' ? 'Mensualidad' : 'Artículo Deportivo' }}
                        @if($pago->mes_correspondiente)
                            <span class="text-slate-400 font-normal text-xs">— {{ $pago->mes_correspondiente }}</span>
                        @endif
                    </p>
                    <p class="text-xs text-slate-400">{{ $pago->created_at->format('d/m/Y') }}</p>
                </div>
                <span class="text-sm font-bold text-slate-800">Bs. {{ number_format($pago->monto, 2) }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

<script>
function toggleHabilitado(id) {
    fetch(`/admin/athletes/${id}/toggle-habilitado`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        const h = data.habilitado;
        const btn   = document.getElementById('btn-toggle');
        const dot   = document.getElementById('toggle-dot');
        const label = document.getElementById('toggle-label');

        btn.className = `inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold border-2 transition-all ${
            h ? 'bg-emerald-50 border-emerald-300 text-emerald-700 hover:bg-red-50 hover:border-red-300 hover:text-red-600'
              : 'bg-red-50 border-red-300 text-red-600 hover:bg-emerald-50 hover:border-emerald-300 hover:text-emerald-700'
        }`;
        dot.className   = `w-2.5 h-2.5 rounded-full ${h ? 'bg-emerald-500' : 'bg-red-500'}`;
        label.textContent = h ? 'Habilitado' : 'Inhabilitado';
    });
}
</script>
@endsection
