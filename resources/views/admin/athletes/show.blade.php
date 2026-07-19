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

        @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('SuperAdmin'))
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
        @else
        <div class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold border-2 
                    {{ $athlete->habilitado_booleano ? 'bg-emerald-50 border-emerald-300 text-emerald-700' : 'bg-red-50 border-red-300 text-red-600' }}">
            <span class="w-2.5 h-2.5 rounded-full {{ $athlete->habilitado_booleano ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
            <span>{{ $athlete->habilitado_booleano ? 'Habilitado' : 'Inhabilitado' }}</span>
        </div>
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
                <div class="-mt-12 w-24 h-24 rounded-2xl border-4 border-white shadow-md overflow-hidden bg-slate-50 flex-shrink-0 relative {{ $athlete->foto ? 'cursor-zoom-in hover:shadow-lg transition-shadow' : '' }}">
                    @if($athlete->foto)
                        @php
                            $optimizedFoto = str_starts_with($athlete->foto, 'http') 
                                 ? $athlete->foto 
                                 : asset('storage/' . $athlete->foto);
                        @endphp
                        <img src="{{ $optimizedFoto }}" data-ci="{{ $athlete->ci }}" class="w-full h-full object-cover lightbox-trigger" alt="Foto de Perfil">
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
                    <div class="flex items-center gap-2 flex-wrap mt-2">
                        <span class="text-xs font-bold text-slate-700 bg-slate-100 px-2.5 py-1 rounded-md shadow-sm border border-slate-200 cursor-default">
                            {{ $athlete->category->nombre ?? 'Sin categoría' }}
                        </span>
                        
                        {{-- Badge 1: Estado Deportivo (Habilitado / Inhabilitado) --}}
                        @if($athlete->habilitado_booleano)
                            <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md shadow-sm border border-emerald-200 cursor-default flex items-center gap-1.5" title="Habilitado para jugar">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Habilitado{{ $athlete->fecha_habilitacion ? ' el ' . $athlete->fecha_habilitacion->format('d/m/Y') : '' }}
                            </span>
                        @else
                            <span class="text-xs font-bold text-red-700 bg-red-50 px-2.5 py-1 rounded-md shadow-sm border border-red-200 cursor-default flex items-center gap-1.5" title="Inhabilitado para jugar">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Inhabilitado
                            </span>
                        @endif

                        {{-- Badge 2: Estado Administrativo (Vigencia de Mensualidad) --}}
                        @php
                            $mensualidadInfo = $athlete->estadoMensualidadInfo();
                        @endphp
                        
                        @if($mensualidadInfo['status'] === 'al_dia')
                            <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md shadow-sm border border-emerald-200 cursor-default flex items-center gap-1.5" title="{{ $mensualidadInfo['desc'] }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> {{ $mensualidadInfo['desc'] }}
                            </span>
                        @else
                            <span class="text-xs font-bold text-red-700 bg-red-50 px-2.5 py-1 rounded-md shadow-sm border border-red-200 cursor-default flex items-center gap-1.5" title="{{ $mensualidadInfo['desc'] }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span> {{ $mensualidadInfo['desc'] }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Datos --}}
        <div class="px-6 pb-6 grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-4 border-t border-slate-100 pt-5">
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
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Teléfono</p>
                <p class="text-sm font-semibold text-slate-800">{{ $athlete->telefono ?? '—' }}</p>
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

    {{-- Documentos y Credenciales --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mb-4">
        <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Documentación y Credenciales</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Tarjeta C.I. --}}
            <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 flex flex-col">
                <div class="flex items-center gap-2 mb-3">
                    <span class="p-1.5 bg-blue-50 text-blue-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M21 12h-4m4 4h-4m4-8h-4"/></svg>
                    </span>
                    <h3 class="text-xs font-black text-slate-700 uppercase tracking-wider">Carnet de Identidad (C.I.)</h3>
                </div>

                <div class="grid grid-cols-2 gap-3 mt-1 flex-1">
                    @if($athlete->ci_anverso || $athlete->ci_reverso)
                        <div class="flex flex-col items-center">
                            @if($athlete->ci_anverso)
                                <div class="relative w-full h-24 rounded-lg overflow-hidden border border-slate-200 shadow-sm hover:shadow-md transition-shadow cursor-zoom-in group bg-white">
                                    <img src="{{ $athlete->ci_anverso }}" data-ci="{{ $athlete->ci }}" class="w-full h-full object-contain lightbox-trigger group-hover:scale-105 transition-transform duration-300" alt="C.I. Anverso">
                                    <div class="absolute inset-0 bg-black/5 group-hover:bg-transparent transition-colors pointer-events-none"></div>
                                </div>
                            @else
                                <div class="w-full h-24 rounded-lg bg-slate-100 border border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-400">
                                    <span class="text-[9px] font-bold uppercase">Sin Anverso</span>
                                </div>
                            @endif
                            <span class="text-[9px] font-bold text-slate-400 mt-1 uppercase">Anverso</span>
                        </div>

                        <div class="flex flex-col items-center">
                            @if($athlete->ci_reverso)
                                <div class="relative w-full h-24 rounded-lg overflow-hidden border border-slate-200 shadow-sm hover:shadow-md transition-shadow cursor-zoom-in group bg-white">
                                    <img src="{{ $athlete->ci_reverso }}" data-ci="{{ $athlete->ci }}" class="w-full h-full object-contain lightbox-trigger group-hover:scale-105 transition-transform duration-300" alt="C.I. Reverso">
                                    <div class="absolute inset-0 bg-black/5 group-hover:bg-transparent transition-colors pointer-events-none"></div>
                                </div>
                            @else
                                <div class="w-full h-24 rounded-lg bg-slate-100 border border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-400">
                                    <span class="text-[9px] font-bold uppercase">Sin Reverso</span>
                                </div>
                            @endif
                            <span class="text-[9px] font-bold text-slate-400 mt-1 uppercase">Reverso</span>
                        </div>
                    @else
                        <div class="col-span-2 py-6 text-center text-xs text-slate-400 italic">
                            No se han subido fotos del carnet.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Tarjeta Carnet Habilitación --}}
            <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 flex flex-col">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <span class="p-1.5 bg-red-50 text-red-600 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 009 11V5.071c.0-2.147-1.442-4.043-3.558-4.571m11.116 20.407A13.916 13.916 0 0015 11V5.071c0-2.147 1.442-4.043 3.558-4.571M12 11v6m0-6V5"/></svg>
                        </span>
                        <h3 class="text-xs font-black text-slate-700 uppercase tracking-wider">Carnet de Habilitación</h3>
                    </div>
                    @if($athlete->tiene_carnet_atleta)
                        <span class="px-2 py-0.5 text-[9px] font-bold bg-emerald-100 text-emerald-700 rounded border border-emerald-200">Activo</span>
                    @else
                        <span class="px-2 py-0.5 text-[9px] font-bold bg-slate-200 text-slate-500 rounded border border-slate-300">No aplica</span>
                    @endif
                </div>

                @if($athlete->tiene_carnet_atleta && $athlete->nro_carnet_atleta)
                    <div class="mb-3 px-1">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Número de Carnet</span>
                        <span class="text-sm font-bold text-slate-800">{{ $athlete->nro_carnet_atleta }}</span>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-3 mt-1 flex-1">
                    @if($athlete->tiene_carnet_atleta)
                        @if($athlete->carnet_atleta_anverso || $athlete->carnet_atleta_reverso)
                            <div class="flex flex-col items-center">
                                @if($athlete->carnet_atleta_anverso)
                                    <div class="relative w-full h-24 rounded-lg overflow-hidden border border-slate-200 shadow-sm hover:shadow-md transition-shadow cursor-zoom-in group bg-white">
                                        <img src="{{ $athlete->carnet_atleta_anverso }}" data-ci="{{ $athlete->ci }}" class="w-full h-full object-contain lightbox-trigger group-hover:scale-105 transition-transform duration-300" alt="Carnet de Habilitación Anverso">
                                        <div class="absolute inset-0 bg-black/5 group-hover:bg-transparent transition-colors pointer-events-none"></div>
                                    </div>
                                @else
                                    <div class="w-full h-24 rounded-lg bg-slate-100 border border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-400">
                                        <span class="text-[9px] font-bold uppercase">Sin Anverso</span>
                                    </div>
                                @endif
                                <span class="text-[9px] font-bold text-slate-400 mt-1 uppercase">Anverso</span>
                            </div>

                            <div class="flex flex-col items-center">
                                @if($athlete->carnet_atleta_reverso)
                                    <div class="relative w-full h-24 rounded-lg overflow-hidden border border-slate-200 shadow-sm hover:shadow-md transition-shadow cursor-zoom-in group bg-white">
                                        <img src="{{ $athlete->carnet_atleta_reverso }}" data-ci="{{ $athlete->ci }}" class="w-full h-full object-contain lightbox-trigger group-hover:scale-105 transition-transform duration-300" alt="Carnet de Habilitación Reverso">
                                        <div class="absolute inset-0 bg-black/5 group-hover:bg-transparent transition-colors pointer-events-none"></div>
                                    </div>
                                @else
                                    <div class="w-full h-24 rounded-lg bg-slate-100 border border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-400">
                                        <span class="text-[9px] font-bold uppercase">Sin Reverso</span>
                                    </div>
                                @endif
                                <span class="text-[9px] font-bold text-slate-400 mt-1 uppercase">Reverso</span>
                            </div>
                        @else
                            <div class="col-span-2 py-6 text-center text-xs text-slate-400 italic">
                                Carnet activo, pero no se han subido fotos.
                            </div>
                        @endif
                    @else
                        <div class="col-span-2 py-6 text-center text-xs text-slate-400 italic">
                            Este atleta no cuenta con carnet de habilitación.
                        </div>
                    @endif
                </div>
            </div>

            @if($athlete->foto_formulario)
            {{-- Tarjeta Foto del Formulario --}}
            <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 flex flex-col col-span-1 md:col-span-2">
                <div class="flex items-center gap-2 mb-3">
                    <span class="p-1.5 bg-blue-50 text-blue-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                    <h3 class="text-xs font-black text-slate-700 uppercase tracking-wider">Foto del Formulario</h3>
                </div>

                <div class="flex justify-center mt-1">
                    <div class="relative w-full max-w-sm h-48 rounded-lg overflow-hidden border border-slate-200 shadow-sm hover:shadow-md transition-shadow cursor-zoom-in group bg-white">
                        <img src="{{ $athlete->foto_formulario }}" data-ci="{{ $athlete->ci }}" class="w-full h-full object-contain lightbox-trigger group-hover:scale-105 transition-transform duration-300" alt="Foto del Formulario">
                        <div class="absolute inset-0 bg-black/5 group-hover:bg-transparent transition-colors pointer-events-none"></div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Últimos pagos (Solo para Admins) --}}
    @if($pagos->count() && (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('SuperAdmin')))
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Historial Contable</h2>
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

{{-- Lightbox Modal --}}
<div id="lightbox-modal" class="hidden fixed inset-0 z-[100] bg-slate-950/90 backdrop-blur-md flex items-center justify-center p-4 transition-all duration-300 opacity-0">
    <button id="lightbox-close" class="absolute top-4 right-4 text-white hover:text-slate-300 p-3 hover:bg-white/10 rounded-full transition-colors cursor-pointer focus:outline-none">
        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
    <div class="max-w-4xl max-h-[85vh] w-full flex flex-col items-center justify-center relative">
        <img id="lightbox-img" class="max-w-full max-h-[80vh] rounded-xl object-contain shadow-2xl scale-95 transition-transform duration-300" src="" alt="Vista ampliada">
        
        <div class="flex flex-col sm:flex-row items-center gap-4 mt-4 bg-slate-900/60 px-6 py-3 rounded-2xl backdrop-blur-sm">
            <p id="lightbox-caption" class="text-slate-300 text-sm font-bold uppercase tracking-wider"></p>
            <span class="hidden sm:inline text-slate-600">|</span>
            <a id="lightbox-download" href="" download target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-blue-500/20 hover:shadow-blue-500/40">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Descargar Imagen
            </a>
        </div>
    </div>
</div>

<script>
// Usar delegación de eventos para evitar duplicación y soportar navegación Turbo
if (!window.lightboxInitialized) {
    window.lightboxInitialized = true;
    
    document.addEventListener('click', (e) => {
        // 1. Mostrar/abrir el Lightbox al hacer click en un disparador
        const trigger = e.target.closest('.lightbox-trigger');
        if (trigger) {
            const modal = document.getElementById('lightbox-modal');
            const modalImg = document.getElementById('lightbox-img');
            const caption = document.getElementById('lightbox-caption');
            const downloadBtn = document.getElementById('lightbox-download');
            
            if (modal && modalImg) {
                const src = trigger.src;
                const alt = trigger.alt || 'Documento';
                
                modalImg.src = src;
                caption.textContent = alt;
                
                if (downloadBtn) {
                    downloadBtn.href = src;
                    const fileExtension = src.split('.').pop().split('?')[0] || 'jpg';
                    const formattedName = alt.toLowerCase().replace(/[^a-z0-9]/g, '_');
                    const filename = `atleta_${formattedName}_${trigger.dataset.ci || 'doc'}.${fileExtension}`;
                    downloadBtn.download = filename;

                    // Forzar descarga para dominios externos (R2/S3) usando Blobs
                    downloadBtn.onclick = (event) => {
                        event.preventDefault();
                        fetch(src)
                            .then(response => response.blob())
                            .then(blob => {
                                const blobUrl = URL.createObjectURL(blob);
                                const tempLink = document.createElement('a');
                                tempLink.href = blobUrl;
                                tempLink.download = filename;
                                document.body.appendChild(tempLink);
                                tempLink.click();
                                document.body.removeChild(tempLink);
                                URL.revokeObjectURL(blobUrl);
                            })
                            .catch(err => {
                                // Plan B: si falla por CORS, abrir en pestaña nueva
                                window.open(src, '_blank');
                            });
                    };
                }
                
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                modal.style.opacity = '1';
                modalImg.classList.remove('scale-95');
                modalImg.classList.add('scale-100');
            }
            return;
        }

        // 2. Cerrar el Lightbox al hacer click fuera o en el botón de cerrar
        const modal = document.getElementById('lightbox-modal');
        if (modal && !modal.classList.contains('hidden')) {
            if (e.target === modal || e.target.closest('#lightbox-close')) {
                closeLightbox();
            }
        }
    });

    // Cerrar al presionar Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeLightbox();
        }
    });

    function closeLightbox() {
        const modal = document.getElementById('lightbox-modal');
        const modalImg = document.getElementById('lightbox-img');
        const caption = document.getElementById('lightbox-caption');
        if (modal && modalImg) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modalImg.src = '';
            caption.textContent = '';
        }
    }
}
</script>

@if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('SuperAdmin'))
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
        window.location.reload();
    });
}
</script>
@endif
@endsection
