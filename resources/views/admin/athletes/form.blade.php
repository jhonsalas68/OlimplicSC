<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- ── SECCIÓN FOTO Y DATOS ── --}}
    <div class="md:col-span-2 bg-white p-8 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/40 relative overflow-hidden mb-4">
        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-50/30 rounded-full -mr-32 -mt-32 blur-3xl"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center gap-8">
            <div class="flex-shrink-0 relative group">
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

    {{-- ── CONTACTO DE EMERGENCIA (se muestra según edad) ── --}}

    {{-- ── CONTACTO DE EMERGENCIA ── --}}
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

    <div id="bloque-mayor" class="md:col-span-2 bg-slate-50/50 p-8 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/40 space-y-6 {{ $esMenor ?? true ? 'hidden' : '' }}">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest">Contacto de Emergencia</h4>
                <p class="text-[11px] text-slate-400 font-bold mt-1 uppercase">Opcional para mayores de edad</p>
            </div>
            
            <div class="w-full sm:w-64">
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">Relación</label>
                <select name="contacto_relacion" class="block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-4 focus:ring-blue-600/10 focus:border-blue-600 text-sm font-bold bg-white text-slate-700 cursor-pointer">
                    <option value="">Seleccionar...</option>
                    @foreach(['Padre','Madre','Cónyuge','Hermano','Hermana','Amigo/a','Compañero/a de equipo','Otro'] as $rel)
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

    {{-- ── ESTADO Y HABILITACIÓN ── --}}
    <div class="md:col-span-2 bg-white p-8 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/40 relative overflow-hidden">
        <label class="relative flex items-center cursor-pointer group">
            <input type="checkbox" name="habilitado_booleano" value="1"
                {{ old('habilitado_booleano', $athlete->habilitado_booleano ?? true) ? 'checked' : '' }}
                class="sr-only peer">
            <div class="w-14 h-7 bg-slate-100 border border-slate-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-emerald-100 peer-checked:bg-emerald-600 peer-checked:border-emerald-600 transition-all
                after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:rounded-full after:h-[20px] after:w-[20px] after:transition-all after:shadow-sm peer-checked:after:translate-x-7"></div>
            <span class="ml-4 text-sm font-bold text-slate-700">Atleta habilitado para actividades y competencia militar/deportiva</span>
        </label>
    </div>
</div>

<div class="mt-12 flex items-center justify-end gap-4 p-8 bg-slate-900 rounded-[2rem] shadow-2xl relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-blue-900/50 to-transparent"></div>
    <div class="relative z-10 flex flex-wrap gap-4">
        <x-admin.button type="button" variant="secondary" onclick="window.history.back()" class="!bg-white/10 !text-white !border-white/20 hover:!bg-white/20 !rounded-2xl !px-8">
            Cancelar
        </x-admin.button>
        <x-admin.button type="submit" variant="danger" class="!px-10 !py-4 !text-base !rounded-2xl !shadow-red-900/40">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
            {{ isset($athlete) ? 'Guardar Cambios' : 'Registrar Atleta' }}
        </x-admin.button>
    </div>
</div>

<script>
const CATEGORIAS = [
    { nombre: 'Pre Infantil', min: 12, max: 13 },
    { nombre: 'Infantil',     min: 14, max: 15 },
    { nombre: 'Menores',      min: 16, max: 17 },
    { nombre: 'Juvenil',      min: 18, max: 19 },
    { nombre: 'Libre',        min: 20, max: 99 },
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
    // Mostrar bloque de contacto según si es menor (< 18)
    const esMenor = edad < 18;
    document.getElementById('bloque-menor').classList.toggle('hidden', !esMenor);
    document.getElementById('bloque-mayor').classList.toggle('hidden', esMenor);
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

document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('fecha_nacimiento');
    if (input && input.value) onFechaNacChange(input.value);
});
</script>
