<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- ── SECCIÓN FOTO Y DATOS ── --}}
    <div class="md:col-span-2 bg-white p-8 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/40 relative overflow-hidden mb-4">
        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-50/30 rounded-full -mr-32 -mt-32 blur-3xl"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center gap-8">
            <div class="flex-shrink-0 flex flex-col items-center gap-4">

                <div class="relative group">
                    <div class="absolute inset-0 bg-blue-600 rounded-full blur-xl opacity-0 group-hover:opacity-20 transition-opacity"></div>
                    @if(isset($athlete) && $athlete->foto)
                        <img id="preview" class="h-32 w-32 rounded-3xl object-cover border-4 border-white shadow-2xl relative z-10"
                             src="{{ str_starts_with($athlete->foto, 'http') ? $athlete->foto : asset('storage/' . $athlete->foto) }}" alt="">
                    @else
                        <div id="preview-placeholder" class="h-32 w-32 rounded-3xl bg-slate-50 border-2 border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-400 relative z-10 transition-colors group-hover:border-blue-400 group-hover:bg-blue-50">
                            <svg class="h-10 w-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-[10px] font-black uppercase tracking-tighter">Sin Foto</span>
                        </div>
                    @endif
                    <button type="button" onclick="document.getElementById('foto').click()" 
                            class="absolute -bottom-2 -right-2 bg-blue-600 text-white p-2.5 rounded-xl shadow-lg hover:bg-blue-700 transition-all z-20 group-hover:scale-110">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </button>
                </div>
            </div>

            <div class="flex-1 w-full space-y-6">
                <input type="file" name="foto" id="foto" class="hidden" accept="image/*" onchange="previewImage(event)">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <x-admin.input label="Nombres" name="nombre" :value="$athlete->nombre ?? old('nombre')" required placeholder="Ej: Juan" />
                    <x-admin.input label="Ape. Paterno" name="apellido_paterno" :value="$athlete->apellido_paterno ?? old('apellido_paterno')" required placeholder="Paterno" />
                    <x-admin.input label="Ape. Materno" name="apellido_materno" :value="$athlete->apellido_materno ?? old('apellido_materno')" placeholder="Materno" />
                </div>
            </div>
        </div>
        @error('foto')
            <p class="mt-4 text-xs font-bold text-red-500 bg-red-50 p-2 rounded-lg inline-block border border-red-100">{{ $message }}</p>
        @enderror
    </div>

    {{-- ── IDENTIFICACIÓN Y CATEGORÍA ── --}}
    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-lg shadow-slate-200/30">
            <x-admin.input label="C.I. (Documento)" name="ci" :value="$athlete->ci ?? old('ci')" required placeholder="Ej: 12345678" />
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-lg shadow-slate-200/30">
            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-3">Género</label>
            <div class="flex items-center p-1 bg-slate-50 rounded-2xl border border-slate-100">
                @foreach(['Masculino', 'Femenino'] as $option)
                    <label class="flex-1 relative cursor-pointer group">
                        <input type="radio" name="genero" value="{{ $option }}"
                            {{ (old('genero', $athlete->genero ?? '') == $option) ? 'checked' : '' }}
                            class="sr-only peer">
                        <div class="py-2.5 text-center text-sm font-bold text-slate-500 rounded-xl transition-all peer-checked:bg-white peer-checked:text-blue-600 peer-checked:shadow-sm">
                            {{ $option }}
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="bg-blue-600 p-6 rounded-[2rem] shadow-lg shadow-blue-200 overflow-hidden relative group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -mr-12 -mt-12 group-hover:scale-150 transition-transform"></div>
            <label class="block text-[11px] font-black text-blue-100 uppercase tracking-widest mb-2 relative z-10">Categoría Asignada</label>
            <div class="flex items-center gap-3 relative z-10 mt-1">
                <div class="p-2 bg-white/20 rounded-xl backdrop-blur-sm">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.552 0 1.05.224 1.414.586l7 7a2 2 0 010 2.828l-5 5a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 10V5a2 2 0 012-2z"/>
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span id="categoria-nombre" class="text-lg font-black text-white leading-tight uppercase tracking-tight">
                        @if(isset($athlete) && $athlete->category)
                            {{ $athlete->category->nombre }}
                        @else
                            Pendiente
                        @endif
                    </span>
                    <span class="text-[10px] font-bold text-blue-100/80">Cálculo automático</span>
                </div>
            </div>
        </div>
    </div>

    <div class="md:col-span-1 bg-white p-6 rounded-[2rem] border border-slate-100 shadow-lg shadow-slate-200/30">
        <x-admin.input label="Fecha de Nacimiento" name="fecha_nacimiento" type="date"
            :value="isset($athlete->fecha_nacimiento) ? $athlete->fecha_nacimiento->format('Y-m-d') : old('fecha_nacimiento')"
            onchange="onFechaNacChange(this.value)" />
    </div>

    <div class="md:col-span-1 bg-white p-6 rounded-[2rem] border border-slate-100 shadow-lg shadow-slate-200/30">
        <x-admin.input label="Teléfono del Atleta" name="telefono"
            :value="$athlete->telefono ?? old('telefono')" placeholder="Ej: 77700000 (Opcional)" />
    </div>

    <div class="md:col-span-2 bg-white p-6 rounded-[2rem] border border-slate-100 shadow-lg shadow-slate-200/30">
        <x-admin.input label="Alergias u Observaciones Médicas" name="alergias"
            :value="$athlete->alergias ?? old('alergias')" placeholder="Ej: Penicilina, Asma, Lesión previa..." />
    </div>

    {{-- ── SEGURO MÉDICO ── --}}
    <div class="md:col-span-2 bg-white p-8 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/40 relative overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest">Seguro Médico</h4>
                <p class="text-xs text-slate-400 mt-1">Información de cobertura en caso de incidentes</p>
            </div>
            <label class="relative flex items-center cursor-pointer group">
                <input type="checkbox" name="tiene_seguro" id="tiene_seguro" value="1"
                    {{ old('tiene_seguro', $athlete->tiene_seguro ?? false) ? 'checked' : '' }}
                    class="sr-only peer" onchange="toggleSeguro(this.checked)">
                <div class="w-14 h-7 bg-slate-100 border border-slate-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-100 peer-checked:bg-blue-600 peer-checked:border-blue-600 transition-all
                    after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:rounded-full after:h-[20px] after:w-[20px] after:transition-all after:shadow-sm peer-checked:after:translate-x-7"></div>
                <span class="ml-3 text-sm font-bold text-slate-700" id="seguro-label">
                    {{ old('tiene_seguro', $athlete->tiene_seguro ?? false) ? 'Sí tiene' : 'No tiene' }}
                </span>
            </label>
        </div>

        <div id="seguro-detalle" class="{{ old('tiene_seguro', $athlete->tiene_seguro ?? false) ? '' : 'hidden' }} grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-50">
            <x-admin.input label="Compañía Aseguradora" name="seguro_compania"
                :value="$athlete->seguro_compania ?? old('seguro_compania')" placeholder="Ej: BUPA, Seguros Illimani..." />
            <x-admin.input label="Teléfono de Emergencia Seguro" name="seguro_contacto"
                :value="$athlete->seguro_contacto ?? old('seguro_contacto')" placeholder="Ej: 800-12345" />
        </div>
    </div>

    {{-- ── CONTACTO DE EMERGENCIA ── --}}
    <div id="bloque-mayor" class="md:col-span-2 bg-slate-50/50 p-8 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/40 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest">Contacto de Emergencia</h4>
                <p class="text-[11px] text-slate-400 font-bold mt-1 uppercase">Información de contacto en caso de emergencia (Todas las categorías)</p>
            </div>
            
            <div class="w-full sm:w-64">
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">Relación</label>
                <select name="contacto_relacion" class="block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-4 focus:ring-blue-600/10 focus:border-blue-600 text-sm font-bold bg-white text-slate-700 cursor-pointer">
                    <option value="">Seleccionar...</option>
                    @foreach(['Padre','Madre','Tutor legal','Cónyuge','Hermano','Hermana','Amigo/a','Compañero/a de equipo','Otro'] as $rel)
                        <option value="{{ $rel }}" {{ old('contacto_relacion', $athlete->contacto_relacion ?? '') == $rel ? 'selected' : '' }}>{{ $rel }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-slate-100">
            <x-admin.input label="Nombre completo del contacto" name="contacto_nombre" :value="$athlete->contacto_nombre ?? old('contacto_nombre')" placeholder="Nombre completo" />
            <x-admin.input label="Teléfono de Emergencia" name="contacto_telefono" :value="$athlete->contacto_telefono ?? old('contacto_telefono')" placeholder="Ej: 77700000" />
        </div>
    </div>

    {{-- ── INFORMACIÓN DEL TUTOR (Para menores de edad) ── --}}
    <div id="bloque-menor" class="md:col-span-2 bg-blue-50/50 p-8 rounded-[2rem] border border-blue-100 shadow-xl shadow-blue-600/5 space-y-6 {{ $esMenor ?? false ? '' : 'hidden' }}">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h4 class="text-sm font-black text-blue-800 uppercase tracking-widest">Información del Tutor</h4>
                <p class="text-[11px] text-blue-500 font-bold mt-1 uppercase">Requerido para menores de edad</p>
            </div>
            
            <div class="w-full sm:w-64">
                <label class="block text-[10px] font-black text-blue-400 uppercase mb-2">Relación</label>
                <select name="relacion_contacto" class="block w-full px-4 py-3 border border-blue-200 rounded-xl shadow-sm focus:outline-none focus:ring-4 focus:ring-blue-600/10 focus:border-blue-600 text-sm font-bold bg-white text-blue-900 cursor-pointer">
                    <option value="">Seleccionar...</option>
                    @foreach(['Padre','Madre','Tutor legal','Abuelo','Abuela','Tío','Tía','Hermano mayor','Hermana mayor','Otro'] as $rel)
                        <option value="{{ $rel }}" {{ old('relacion_contacto', $athlete->relacion_contacto ?? '') == $rel ? 'selected' : '' }}>{{ $rel }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-6 border-t border-blue-100/50">
            <x-admin.input label="Nombres Tutor" name="nombre_padre" :value="$athlete->nombre_padre ?? old('nombre_padre')" placeholder="Nombres" />
            <x-admin.input label="Ape. Paterno" name="apellido_paterno_padre" :value="$athlete->apellido_paterno_padre ?? old('apellido_paterno_padre')" placeholder="Paterno" />
            <x-admin.input label="Ape. Materno" name="apellido_materno_padre" :value="$athlete->apellido_materno_padre ?? old('apellido_materno_padre')" placeholder="Materno" />
        </div>
        <div class="pt-2">
            <x-admin.input label="Teléfono de Emergencia Tutor" name="telefono_padre" :value="$athlete->telefono_padre ?? old('telefono_padre')" placeholder="Ej: 77700000" />
        </div>
    </div>

    {{-- ── DOCUMENTACIÓN DE IDENTIDAD ── --}}
    <div class="md:col-span-2 bg-white p-8 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/40 relative overflow-hidden mb-4">
        <div class="absolute top-0 right-0 w-64 h-64 bg-red-50/20 rounded-full -mr-32 -mt-32 blur-3xl"></div>
        
        <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-2 relative z-10">Carnet de Identidad (C.I.)</h4>
        <p class="text-xs text-slate-400 mb-6 relative z-10">Sube fotos legibles del anverso y reverso del documento de identidad</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
            <!-- Anverso C.I. -->
            <div class="flex flex-col items-center p-6 bg-slate-50 border border-slate-100 rounded-2xl relative group">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Anverso (Frente)</label>
                <div class="relative w-full max-w-[240px] h-36 rounded-xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-400 bg-white overflow-hidden hover:border-blue-400 transition-colors">
                    
                    {{-- Placeholder --}}
                    <div id="ci_anverso_placeholder" class="flex flex-col items-center justify-center p-4 text-center cursor-pointer w-full h-full {{ (isset($athlete) && $athlete->ci_anverso) ? 'hidden' : '' }}" onclick="document.getElementById('ci_anverso').click()">
                        <svg class="h-8 w-8 mb-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-[10px] font-black uppercase tracking-tight text-slate-500">Subir Anverso</span>
                    </div>

                    {{-- Preview --}}
                    <div id="ci_anverso_preview_container" class="relative w-full h-full cursor-zoom-in {{ (isset($athlete) && $athlete->ci_anverso) ? '' : 'hidden' }}" onclick="verImagenGrande('ci_anverso_preview')">
                        <img id="ci_anverso_preview" class="w-full h-full object-cover" src="{{ $athlete->ci_anverso ?? '' }}" alt="C.I. Anverso">
                    </div>

                </div>

                {{-- Botones de Acción --}}
                <div id="ci_anverso_actions" class="flex gap-2 mt-3 {{ (isset($athlete) && $athlete->ci_anverso) ? '' : 'hidden' }}">
                    <button type="button" onclick="verImagenGrande('ci_anverso_preview')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-bold uppercase rounded-lg transition-colors cursor-pointer flex items-center gap-1.5 shadow-sm">
                        👁 Ver
                    </button>
                    <button type="button" onclick="document.getElementById('ci_anverso').click()" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 text-[10px] font-bold uppercase rounded-lg transition-colors cursor-pointer flex items-center gap-1.5 shadow-sm">
                        ✏ Cambiar
                    </button>
                </div>

                <input type="file" name="ci_anverso" id="ci_anverso" class="hidden" accept="image/*" onchange="previewDocument(event, 'ci_anverso_preview', 'ci_anverso_placeholder', 'ci_anverso_preview_container', 'ci_anverso_actions')">
                @error('ci_anverso')
                    <p class="mt-2 text-xs font-bold text-red-500 bg-red-50 p-1.5 rounded-lg border border-red-100">{{ $message }}</p>
                @enderror
            </div>

            <!-- Reverso C.I. -->
            <div class="flex flex-col items-center p-6 bg-slate-50 border border-slate-100 rounded-2xl relative group">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Reverso (Atrás)</label>
                <div class="relative w-full max-w-[240px] h-36 rounded-xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-400 bg-white overflow-hidden hover:border-blue-400 transition-colors">
                    
                    {{-- Placeholder --}}
                    <div id="ci_reverso_placeholder" class="flex flex-col items-center justify-center p-4 text-center cursor-pointer w-full h-full {{ (isset($athlete) && $athlete->ci_reverso) ? 'hidden' : '' }}" onclick="document.getElementById('ci_reverso').click()">
                        <svg class="h-8 w-8 mb-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-[10px] font-black uppercase tracking-tight text-slate-500">Subir Reverso</span>
                    </div>

                    {{-- Preview --}}
                    <div id="ci_reverso_preview_container" class="relative w-full h-full cursor-zoom-in {{ (isset($athlete) && $athlete->ci_reverso) ? '' : 'hidden' }}" onclick="verImagenGrande('ci_reverso_preview')">
                        <img id="ci_reverso_preview" class="w-full h-full object-cover" src="{{ $athlete->ci_reverso ?? '' }}" alt="C.I. Reverso">
                    </div>

                </div>

                {{-- Botones de Acción --}}
                <div id="ci_reverso_actions" class="flex gap-2 mt-3 {{ (isset($athlete) && $athlete->ci_reverso) ? '' : 'hidden' }}">
                    <button type="button" onclick="verImagenGrande('ci_reverso_preview')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-bold uppercase rounded-lg transition-colors cursor-pointer flex items-center gap-1.5 shadow-sm">
                        👁 Ver
                    </button>
                    <button type="button" onclick="document.getElementById('ci_reverso').click()" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 text-[10px] font-bold uppercase rounded-lg transition-colors cursor-pointer flex items-center gap-1.5 shadow-sm">
                        ✏ Cambiar
                    </button>
                </div>

                <input type="file" name="ci_reverso" id="ci_reverso" class="hidden" accept="image/*" onchange="previewDocument(event, 'ci_reverso_preview', 'ci_reverso_placeholder', 'ci_reverso_preview_container', 'ci_reverso_actions')">
                @error('ci_reverso')
                    <p class="mt-2 text-xs font-bold text-red-500 bg-red-50 p-1.5 rounded-lg border border-red-100">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- ── CARNET DE HABILITACIÓN ── --}}
    <div class="md:col-span-2 bg-white p-8 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/40 relative overflow-hidden mb-4">
        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-50/20 rounded-full -mr-32 -mt-32 blur-3xl"></div>
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 relative z-10">
            <div>
                <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest">Carnet de Habilitación</h4>
                <p class="text-xs text-slate-400 mt-1">Habilita y sube el carnet de habilitación del atleta</p>
            </div>
            <label class="relative flex items-center cursor-pointer group">
                <input type="checkbox" name="tiene_carnet_atleta" id="tiene_carnet_atleta" value="1"
                    {{ old('tiene_carnet_atleta', $athlete->tiene_carnet_atleta ?? false) ? 'checked' : '' }}
                    class="sr-only peer" onchange="toggleCarnetAtleta(this.checked)">
                <div class="w-14 h-7 bg-slate-100 border border-slate-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-100 peer-checked:bg-blue-600 peer-checked:border-blue-600 transition-all
                    after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:rounded-full after:h-[20px] after:w-[20px] after:transition-all after:shadow-sm peer-checked:after:translate-x-7"></div>
                <span class="ml-3 text-sm font-bold text-slate-700" id="carnet-atleta-label">
                    {{ old('tiene_carnet_atleta', $athlete->tiene_carnet_atleta ?? false) ? 'Sí tiene' : 'No tiene' }}
                </span>
            </label>
        </div>

        <div id="carnet-atleta-detalle" class="{{ old('tiene_carnet_atleta', $athlete->tiene_carnet_atleta ?? false) ? '' : 'hidden' }} grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-slate-50 relative z-10">
            <div class="md:col-span-2">
                <x-admin.input label="Número de Carnet de Habilitación" name="nro_carnet_atleta" id="nro_carnet_atleta" :value="$athlete->nro_carnet_atleta ?? old('nro_carnet_atleta')" placeholder="Ej: H-12345 (Opcional)" />
            </div>
            <!-- Anverso Carnet Atleta -->
            <div class="flex flex-col items-center p-6 bg-slate-50 border border-slate-100 rounded-2xl relative group">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Anverso Carnet de Habilitación</label>
                <div class="relative w-full max-w-[240px] h-36 rounded-xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-400 bg-white overflow-hidden hover:border-blue-400 transition-colors">
                    
                    {{-- Placeholder --}}
                    <div id="carnet_atleta_anverso_placeholder" class="flex flex-col items-center justify-center p-4 text-center cursor-pointer w-full h-full {{ (isset($athlete) && $athlete->carnet_atleta_anverso) ? 'hidden' : '' }}" onclick="document.getElementById('carnet_atleta_anverso').click()">
                        <svg class="h-8 w-8 mb-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-[10px] font-black uppercase tracking-tight text-slate-500">Subir Frente</span>
                    </div>

                    {{-- Preview --}}
                    <div id="carnet_atleta_anverso_preview_container" class="relative w-full h-full cursor-zoom-in {{ (isset($athlete) && $athlete->carnet_atleta_anverso) ? '' : 'hidden' }}" onclick="verImagenGrande('carnet_atleta_anverso_preview')">
                        <img id="carnet_atleta_anverso_preview" class="w-full h-full object-cover" src="{{ $athlete->carnet_atleta_anverso ?? '' }}" alt="Carnet de Habilitación Anverso">
                    </div>

                </div>

                {{-- Botones de Acción --}}
                <div id="carnet_atleta_anverso_actions" class="flex gap-2 mt-3 {{ (isset($athlete) && $athlete->carnet_atleta_anverso) ? '' : 'hidden' }}">
                    <button type="button" onclick="verImagenGrande('carnet_atleta_anverso_preview')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-bold uppercase rounded-lg transition-colors cursor-pointer flex items-center gap-1.5 shadow-sm">
                        👁 Ver
                    </button>
                    <button type="button" onclick="document.getElementById('carnet_atleta_anverso').click()" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 text-[10px] font-bold uppercase rounded-lg transition-colors cursor-pointer flex items-center gap-1.5 shadow-sm">
                        ✏ Cambiar
                    </button>
                </div>

                <input type="file" name="carnet_atleta_anverso" id="carnet_atleta_anverso" class="hidden" accept="image/*" onchange="previewDocument(event, 'carnet_atleta_anverso_preview', 'carnet_atleta_anverso_placeholder', 'carnet_atleta_anverso_preview_container', 'carnet_atleta_anverso_actions')">
                @error('carnet_atleta_anverso')
                    <p class="mt-2 text-xs font-bold text-red-500 bg-red-50 p-1.5 rounded-lg border border-red-100">{{ $message }}</p>
                @enderror
            </div>

            <!-- Reverso Carnet Atleta -->
            <div class="flex flex-col items-center p-6 bg-slate-50 border border-slate-100 rounded-2xl relative group">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Reverso Carnet de Habilitación</label>
                <div class="relative w-full max-w-[240px] h-36 rounded-xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-400 bg-white overflow-hidden hover:border-blue-400 transition-colors">
                    
                    {{-- Placeholder --}}
                    <div id="carnet_atleta_reverso_placeholder" class="flex flex-col items-center justify-center p-4 text-center cursor-pointer w-full h-full {{ (isset($athlete) && $athlete->carnet_atleta_reverso) ? 'hidden' : '' }}" onclick="document.getElementById('carnet_atleta_reverso').click()">
                        <svg class="h-8 w-8 mb-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-[10px] font-black uppercase tracking-tight text-slate-500">Subir Atrás</span>
                    </div>

                    {{-- Preview --}}
                    <div id="carnet_atleta_reverso_preview_container" class="relative w-full h-full cursor-zoom-in {{ (isset($athlete) && $athlete->carnet_atleta_reverso) ? '' : 'hidden' }}" onclick="verImagenGrande('carnet_atleta_reverso_preview')">
                        <img id="carnet_atleta_reverso_preview" class="w-full h-full object-cover" src="{{ $athlete->carnet_atleta_reverso ?? '' }}" alt="Carnet de Habilitación Reverso">
                    </div>

                </div>

                {{-- Botones de Acción --}}
                <div id="carnet_atleta_reverso_actions" class="flex gap-2 mt-3 {{ (isset($athlete) && $athlete->carnet_atleta_reverso) ? '' : 'hidden' }}">
                    <button type="button" onclick="verImagenGrande('carnet_atleta_reverso_preview')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-bold uppercase rounded-lg transition-colors cursor-pointer flex items-center gap-1.5 shadow-sm">
                        👁 Ver
                    </button>
                    <button type="button" onclick="document.getElementById('carnet_atleta_reverso').click()" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 text-[10px] font-bold uppercase rounded-lg transition-colors cursor-pointer flex items-center gap-1.5 shadow-sm">
                        ✏ Cambiar
                    </button>
                </div>

                <input type="file" name="carnet_atleta_reverso" id="carnet_atleta_reverso" class="hidden" accept="image/*" onchange="previewDocument(event, 'carnet_atleta_reverso_preview', 'carnet_atleta_reverso_placeholder', 'carnet_atleta_reverso_preview_container', 'carnet_atleta_reverso_actions')">
                @error('carnet_atleta_reverso')
                    <p class="mt-2 text-xs font-bold text-red-500 bg-red-50 p-1.5 rounded-lg border border-red-100">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- ── ESTADO Y HABILITACIÓN ── --}}
    <div class="md:col-span-1 bg-white p-8 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/40 relative overflow-hidden mb-4">
        <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-4">Estatus Deportivo</h4>
        <div class="space-y-4">
            <label class="relative flex items-center cursor-pointer group">
                <input type="checkbox" name="habilitado_booleano" id="habilitado_booleano" value="1"
                    {{ old('habilitado_booleano', $athlete->habilitado_booleano ?? true) ? 'checked' : '' }}
                    class="sr-only peer" onchange="toggleFechaHabilitacion(this.checked)">
                <div class="w-14 h-7 bg-slate-100 border border-slate-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-emerald-100 peer-checked:bg-emerald-600 peer-checked:border-emerald-600 transition-all
                    after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:rounded-full after:h-[20px] after:w-[20px] after:transition-all after:shadow-sm peer-checked:after:translate-x-7"></div>
                <span class="ml-4 text-sm font-bold text-slate-700">Habilitado para jugar</span>
            </label>

            <div id="fecha-habilitacion-container" class="{{ old('habilitado_booleano', $athlete->habilitado_booleano ?? true) ? '' : 'hidden' }}">
                <label for="fecha_habilitacion" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Fecha de Habilitación</label>
                <div class="relative">
                    <input type="date" name="fecha_habilitacion" id="fecha_habilitacion"
                        value="{{ old('fecha_habilitacion', isset($athlete->fecha_habilitacion) ? $athlete->fecha_habilitacion->format('Y-m-d') : '') }}"
                        class="w-full bg-slate-50 border border-slate-200 text-slate-700 text-sm font-semibold rounded-2xl px-4 py-3 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                </div>
                @error('fecha_habilitacion')
                    <p class="mt-2 text-xs font-bold text-red-500 bg-red-50 p-1.5 rounded-lg border border-red-100">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- ── CONTROL DE MENSUALIDAD ── --}}
    <div class="md:col-span-2 bg-white p-8 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/40 relative overflow-hidden mb-4">
        <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-2">Vigencia de Mensualidad</h4>
        <p class="text-xs text-slate-400 mb-6">Fechas administrativas del último pago recibido y su caducidad</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2 border-t border-slate-50">
            <x-admin.input label="Fecha de Pago" name="fecha_pago_habilitacion" type="date"
                :value="isset($athlete->fecha_pago_habilitacion) ? $athlete->fecha_pago_habilitacion->format('Y-m-d') : old('fecha_pago_habilitacion')" />
            <x-admin.input label="Vence el / Caduca" name="fecha_vencimiento_habilitacion" type="date"
                :value="isset($athlete->fecha_vencimiento_habilitacion) ? $athlete->fecha_vencimiento_habilitacion->format('Y-m-d') : old('fecha_vencimiento_habilitacion')" />
        </div>
    </div>
</div>



<script>
const CATEGORIAS = [
    { nombre: 'Pre Infantil', min: 12, max: 13 },
    { nombre: 'Infantil',     min: 14, max: 15 },
    { nombre: 'Menores',      min: 16, max: 17 },
    { nombre: 'Juvenil',      min: 18, max: 19 },
    { nombre: 'Ascenso',      min: 20, max: 99 },
];

function calcularEdad(fechaNac) {
    const hoy = new Date();
    const nac = new Date(fechaNac);
    let edad = hoy.getFullYear() - nac.getFullYear();
    const m = hoy.getMonth() - nac.getMonth();
    if (m < 0 || (m === 0 && hoy.getDate() < nac.getDate())) edad--;
    return edad;
}

function onFechaNacChange(fechaNac) {
    const display = document.getElementById('categoria-nombre');
    if (!fechaNac) {
        display.textContent = 'Ingresa la fecha de nacimiento';
        return;
    }
    const edad = calcularEdad(fechaNac);
    const cat  = CATEGORIAS.find(c => edad >= c.min && edad <= c.max);
    if (cat) {
        display.innerHTML = `<strong>${cat.nombre}</strong> <span style="font-weight:normal;opacity:.7">(${cat.min}–${cat.max} años · ${edad} años cumplidos)</span>`;
    }
    // Mostrar bloque de tutor solo si es menor (< 18)
    const esMenor = edad < 18;
    const bloqueMenor = document.getElementById('bloque-menor');
    if (bloqueMenor) {
        bloqueMenor.classList.toggle('hidden', !esMenor);
    }
}

function toggleSeguro(checked) {
    document.getElementById('seguro-detalle').classList.toggle('hidden', !checked);
    document.getElementById('seguro-label').textContent = checked ? 'Sí tiene seguro' : 'No tiene seguro';
    if (!checked) {
        document.querySelector('[name=seguro_compania]').value = '';
        document.querySelector('[name=seguro_contacto]').value = '';
    }
}

function compressAndPreviewImage(file, inputElement, previewCallback) {
    if (!file || !file.type.match(/image.*/)) return;

    if (file.size > 5 * 1024 * 1024) {
        alert("El peso superado. La imagen supera el peso admitido de 5MB.");
        inputElement.value = "";
        return;
    }
    const reader = new FileReader();
    reader.onload = function(e) {
        const img = new Image();
        img.onload = function() {
            const canvas = document.createElement('canvas');
            const MAX_WIDTH = 800;
            const MAX_HEIGHT = 800;
            let width = img.width;
            let height = img.height;

            if (width > height) {
                if (width > MAX_WIDTH) {
                    height *= MAX_WIDTH / width;
                    width = MAX_WIDTH;
                }
            } else {
                if (height > MAX_HEIGHT) {
                    width *= MAX_HEIGHT / height;
                    height = MAX_HEIGHT;
                }
            }
            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, width, height);
            
            canvas.toBlob((blob) => {
                if (!blob) return;
                const newFile = new File([blob], file.name, {
                    type: file.type,
                    lastModified: Date.now()
                });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(newFile);
                inputElement.files = dataTransfer.files;
                previewCallback(URL.createObjectURL(blob));
            }, file.type === 'image/png' ? 'image/png' : 'image/jpeg', 0.85);
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

function previewImage(event) {
    const file = event.target.files[0];
    if (!file) return;
    compressAndPreviewImage(file, event.target, function(previewUrl) {
        const ph = document.getElementById('preview-placeholder');
        const prev = document.getElementById('preview');
        if (ph) {
            const img = document.createElement('img');
            img.id = 'preview';
            img.className = 'h-24 w-24 rounded-full object-cover border-2 border-slate-100';
            img.src = previewUrl;
            ph.parentNode.replaceChild(img, ph);
        } else if (prev) {
            prev.src = previewUrl;
        }
    });
}

function toggleCarnetAtleta(checked) {
    document.getElementById('carnet-atleta-detalle').classList.toggle('hidden', !checked);
    document.getElementById('carnet-atleta-label').textContent = checked ? 'Sí tiene' : 'No tiene';
    if (!checked) {
        document.getElementById('carnet_atleta_anverso').value = '';
        document.getElementById('carnet_atleta_reverso').value = '';
        const nroInput = document.getElementById('nro_carnet_atleta');
        if (nroInput) nroInput.value = '';
        
        const previewAnverso = document.getElementById('carnet_atleta_anverso_preview');
        const placeholderAnverso = document.getElementById('carnet_atleta_anverso_placeholder');
        const containerAnverso = document.getElementById('carnet_atleta_anverso_preview_container');
        const actionsAnverso = document.getElementById('carnet_atleta_anverso_actions');
        if (placeholderAnverso) {
            placeholderAnverso.classList.remove('hidden');
            if (containerAnverso) containerAnverso.classList.add('hidden');
            if (actionsAnverso) actionsAnverso.classList.add('hidden');
            previewAnverso.src = '';
        }
        
        const previewReverso = document.getElementById('carnet_atleta_reverso_preview');
        const placeholderReverso = document.getElementById('carnet_atleta_reverso_placeholder');
        const containerReverso = document.getElementById('carnet_atleta_reverso_preview_container');
        const actionsReverso = document.getElementById('carnet_atleta_reverso_actions');
        if (placeholderReverso) {
            placeholderReverso.classList.remove('hidden');
            if (containerReverso) containerReverso.classList.add('hidden');
            if (actionsReverso) actionsReverso.classList.add('hidden');
            previewReverso.src = '';
        }
    }
}

function previewDocument(event, previewId, placeholderId, containerId, actionsId) {
    const file = event.target.files[0];
    if (!file) return;
    compressAndPreviewImage(file, event.target, function(previewUrl) {
        const previewImg = document.getElementById(previewId);
        const placeholder = document.getElementById(placeholderId);
        const container = document.getElementById(containerId);
        const actions = document.getElementById(actionsId);
        if (previewImg) {
            previewImg.src = previewUrl;
        }
        if (placeholder) {
            placeholder.classList.add('hidden');
        }
        if (container) {
            container.classList.remove('hidden');
        }
        if (actions) {
            actions.classList.remove('hidden');
        }
    });
}

function verImagenGrande(imgId) {
    const img = document.getElementById(imgId);
    if (img && img.src && img.src !== '') {
        window.open(img.src, '_blank');
    } else {
        alert('No hay ninguna foto cargada para visualizar.');
    }
}

function toggleFechaHabilitacion(checked) {
    const container = document.getElementById('fecha-habilitacion-container');
    if (container) {
        container.classList.toggle('hidden', !checked);
    }
    if (!checked) {
        const input = document.getElementById('fecha_habilitacion');
        if (input) input.value = '';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('fecha_nacimiento');
    if (input && input.value) onFechaNacChange(input.value);
});
</script>
