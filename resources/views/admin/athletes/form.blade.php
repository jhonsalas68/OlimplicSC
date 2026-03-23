<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- ── FOTO ── --}}
    <div class="md:col-span-2 flex items-center space-x-6 pb-6 border-b border-slate-100">
        <div class="flex-shrink-0 h-24 w-24 relative">
            @if(isset($athlete) && $athlete->foto)
                <img id="preview" class="h-24 w-24 rounded-full object-cover border-2 border-slate-100"
                     src="{{ asset('storage/' . $athlete->foto) }}" alt="">
            @else
                <div id="preview-placeholder" class="h-24 w-24 rounded-full bg-slate-100 flex items-center justify-center text-slate-300">
                    <svg class="h-12 w-12" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            @endif
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Fotografía del Atleta</label>
            <input type="file" name="foto" id="foto" class="hidden" accept="image/*" onchange="previewImage(event)">
            <x-admin.button type="button" variant="secondary" onclick="document.getElementById('foto').click()">
                {{ isset($athlete) && $athlete->foto ? 'Cambiar Foto' : 'Subir Foto' }}
            </x-admin.button>
            <p class="mt-2 text-xs text-slate-400">JPG, PNG o WEBP · Máx. 2MB</p>
        </div>
    </div>

    {{-- ── DATOS PERSONALES ── --}}
    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-admin.input label="Nombres" name="nombre" :value="$athlete->nombre ?? old('nombre')" required placeholder="Ej: Juan" />
        <x-admin.input label="Ape. Paterno" name="apellido_paterno" :value="$athlete->apellido_paterno ?? old('apellido_paterno')" required placeholder="Paterno" />
        <x-admin.input label="Ape. Materno" name="apellido_materno" :value="$athlete->apellido_materno ?? old('apellido_materno')" placeholder="Materno" />
    </div>

    <x-admin.input label="Documento de Identidad (C.I.)" name="ci" :value="$athlete->ci ?? old('ci')" required placeholder="Ej: 12345678" />

    {{-- Categoría auto-calculada --}}
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Categoría</label>
        <div class="flex items-center gap-3 px-4 py-2.5 bg-blue-50 border border-blue-200 rounded-lg">
            <svg class="h-4 w-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.552 0 1.05.224 1.414.586l7 7a2 2 0 010 2.828l-5 5a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 10V5a2 2 0 012-2z"/>
            </svg>
            <span id="categoria-nombre" class="text-sm font-semibold text-blue-700">
                @if(isset($athlete) && $athlete->category)
                    {{ $athlete->category->nombre }}
                    <span class="font-normal text-blue-500">({{ $athlete->category->edad_min }}–{{ $athlete->category->edad_max }} años)</span>
                @else
                    Ingresa la fecha de nacimiento
                @endif
            </span>
        </div>
        <p class="mt-1 text-xs text-slate-400">Se asigna automáticamente según la edad</p>
    </div>

    <x-admin.input label="Fecha de Nacimiento" name="fecha_nacimiento" type="date"
        :value="isset($athlete->fecha_nacimiento) ? $athlete->fecha_nacimiento->format('Y-m-d') : old('fecha_nacimiento')"
        onchange="onFechaNacChange(this.value)" />

    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Género</label>
        <div class="flex items-center space-x-6 h-10">
            @foreach(['Masculino', 'Femenino'] as $option)
                <label class="inline-flex items-center">
                    <input type="radio" name="genero" value="{{ $option }}"
                        {{ (old('genero', $athlete->genero ?? '') == $option) ? 'checked' : '' }}
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300">
                    <span class="ml-2 text-sm text-slate-600">{{ $option }}</span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- ── ALERGIAS ── --}}
    <div class="md:col-span-2 pt-4 border-t border-slate-100">
        <x-admin.input label="Alergias / Observaciones Médicas" name="alergias"
            :value="$athlete->alergias ?? old('alergias')" placeholder="Ej: Penicilina, Asma..." />
    </div>

    {{-- ── SEGURO MÉDICO ── --}}
    <div class="md:col-span-2 p-5 bg-slate-50 rounded-2xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between">
            <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Seguro Médico</h4>
            <label class="relative flex items-center cursor-pointer gap-3">
                <input type="checkbox" name="tiene_seguro" id="tiene_seguro" value="1"
                    {{ old('tiene_seguro', $athlete->tiene_seguro ?? false) ? 'checked' : '' }}
                    class="sr-only peer" onchange="toggleSeguro(this.checked)">
                <div class="w-11 h-6 bg-slate-200 peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer
                    peer-checked:after:translate-x-full peer-checked:after:border-white
                    after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                    after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all
                    peer-checked:bg-emerald-600"></div>
                <span class="text-sm font-semibold text-slate-700" id="seguro-label">
                    {{ old('tiene_seguro', $athlete->tiene_seguro ?? false) ? 'Sí tiene seguro' : 'No tiene seguro' }}
                </span>
            </label>
        </div>

        <div id="seguro-detalle" class="{{ old('tiene_seguro', $athlete->tiene_seguro ?? false) ? '' : 'hidden' }} grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-admin.input label="Compañía Aseguradora" name="seguro_compania"
                :value="$athlete->seguro_compania ?? old('seguro_compania')" placeholder="Ej: BUPA, Seguros Illimani..." />
            <x-admin.input label="Teléfono / Contacto de la Aseguradora" name="seguro_contacto"
                :value="$athlete->seguro_contacto ?? old('seguro_contacto')" placeholder="Ej: 800-12345" />
        </div>
    </div>

    {{-- ── CONTACTO DE EMERGENCIA (se muestra según edad) ── --}}

    {{-- MENOR DE EDAD --}}
    <div id="bloque-menor" class="md:col-span-2 p-5 bg-blue-50 rounded-2xl border border-blue-100 space-y-4
        {{ $esMenor ?? false ? '' : 'hidden' }}">
        <h4 class="text-sm font-bold text-blue-800 uppercase tracking-wider">Información del Padre / Madre / Tutor</h4>
        <p class="text-xs text-blue-500">El atleta es menor de edad. Se requieren datos del responsable legal.</p>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Relación con el atleta</label>
            <select name="relacion_contacto" class="block w-full px-4 py-2.5 border border-slate-200 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 sm:text-sm bg-white">
                <option value="">Seleccionar...</option>
                @foreach(['Padre','Madre','Tutor legal','Abuelo','Abuela','Tío','Tía','Hermano mayor','Hermana mayor','Otro'] as $rel)
                    <option value="{{ $rel }}" {{ old('relacion_contacto', $athlete->relacion_contacto ?? '') == $rel ? 'selected' : '' }}>{{ $rel }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-admin.input label="Nombres" name="nombre_padre"
                :value="$athlete->nombre_padre ?? old('nombre_padre')" placeholder="Nombres" />
            <x-admin.input label="Ape. Paterno" name="apellido_paterno_padre"
                :value="$athlete->apellido_paterno_padre ?? old('apellido_paterno_padre')" placeholder="Paterno" />
            <x-admin.input label="Ape. Materno" name="apellido_materno_padre"
                :value="$athlete->apellido_materno_padre ?? old('apellido_materno_padre')" placeholder="Materno" />
        </div>
        <x-admin.input label="Teléfono de Contacto / Emergencia" name="telefono_padre"
            :value="$athlete->telefono_padre ?? old('telefono_padre')" placeholder="Ej: 77700000" />
    </div>

    {{-- MAYOR DE EDAD --}}
    <div id="bloque-mayor" class="md:col-span-2 p-5 bg-slate-50 rounded-2xl border border-slate-100 space-y-4
        {{ $esMenor ?? true ? 'hidden' : '' }}">
        <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Contacto de Emergencia</h4>
        <p class="text-xs text-slate-400">El atleta es mayor de edad. Ingresa un contacto de referencia.</p>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Relación con el atleta</label>
            <select name="contacto_relacion" class="block w-full px-4 py-2.5 border border-slate-200 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 sm:text-sm bg-white">
                <option value="">Seleccionar...</option>
                @foreach(['Padre','Madre','Cónyuge','Hermano','Hermana','Amigo/a','Compañero/a de equipo','Otro'] as $rel)
                    <option value="{{ $rel }}" {{ old('contacto_relacion', $athlete->contacto_relacion ?? '') == $rel ? 'selected' : '' }}>{{ $rel }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-admin.input label="Nombre completo" name="contacto_nombre"
                :value="$athlete->contacto_nombre ?? old('contacto_nombre')" placeholder="Nombre del contacto" />
            <x-admin.input label="Teléfono de Emergencia" name="contacto_telefono"
                :value="$athlete->contacto_telefono ?? old('contacto_telefono')" placeholder="Ej: 77700000" />
        </div>
    </div>

    {{-- ── ESTADO ── --}}
    <div class="md:col-span-2 pt-6 border-t border-slate-100">
        <label class="relative flex items-center cursor-pointer">
            <input type="checkbox" name="habilitado_booleano" value="1"
                {{ old('habilitado_booleano', $athlete->habilitado_booleano ?? true) ? 'checked' : '' }}
                class="sr-only peer">
            <div class="w-11 h-6 bg-slate-200 peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer
                peer-checked:after:translate-x-full peer-checked:after:border-white
                after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all
                peer-checked:bg-emerald-600"></div>
            <span class="ml-3 text-sm font-semibold text-slate-700 italic">Estudiante habilitado para jugar</span>
        </label>
    </div>
</div>

<script>
const CATEGORIAS = [
    { nombre: 'Pre Infantil', min: 0,  max: 13 },
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

function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const ph = document.getElementById('preview-placeholder');
        const prev = document.getElementById('preview');
        if (ph) {
            const img = document.createElement('img');
            img.id = 'preview';
            img.className = 'h-24 w-24 rounded-full object-cover border-2 border-slate-100';
            img.src = reader.result;
            ph.parentNode.replaceChild(img, ph);
        } else if (prev) {
            prev.src = reader.result;
        }
    };
    if (event.target.files[0]) reader.readAsDataURL(event.target.files[0]);
}

document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('fecha_nacimiento');
    if (input && input.value) onFechaNacChange(input.value);
});
</script>
