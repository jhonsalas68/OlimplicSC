<div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
    {{-- Foto de Perfil --}}
    <div class="md:col-span-2 flex items-center space-x-6 mb-4">
        <div class="shrink-0">
            @if(isset($user) && $user->avatar)
                <img id="preview-avatar" class="h-16 w-16 object-cover rounded-full border-2 border-slate-100 shadow-sm" src="{{ $user->avatar_url }}" alt="Avatar">
            @else
                <div id="preview-placeholder" class="h-16 w-16 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 border-2 border-dashed border-slate-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
            @endif
        </div>
        <label class="block">
            <span class="block text-sm font-semibold text-slate-700 mb-1">Foto de Perfil (Opcional)</span>
            <input type="file" name="avatar" accept="image/*" onchange="handleAvatarChange(event, '{{ isset($user) ? 'preview-avatar' : 'preview-placeholder' }}')" class="block w-full text-sm text-slate-500
                file:mr-4 file:py-2 file:px-4
                file:rounded-full file:border-0
                file:text-sm file:font-semibold
                file:bg-blue-50 file:text-blue-700
                hover:file:bg-blue-100
            "/>
        </label>
        @error('avatar')
            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Datos Personales --}}
    <x-admin.input label="Nombres" name="name" :value="old('name', $user->name ?? '')" required placeholder="Ej: Juan" />
    <div class="grid grid-cols-2 gap-4">
        <x-admin.input label="Ape. Paterno" name="apellido_paterno" :value="old('apellido_paterno', $user->apellido_paterno ?? '')" required />
        <x-admin.input label="Ape. Materno" name="apellido_materno" :value="old('apellido_materno', $user->apellido_materno ?? '')" />
    </div>
    
    <x-admin.input label="C.I. (Contraseña inicial)" name="ci" :value="old('ci', $user->ci ?? '')" required placeholder="Contraseña por defecto" />
    <x-admin.input label="Correo Electrónico" name="email" type="email" :value="old('email', $user->email ?? '')" required placeholder="correo@ejemplo.com" />

    @if(isset($user))
        <div class="md:col-span-2 bg-slate-50 p-4 rounded-xl border border-slate-100 italic text-xs text-slate-500">
            Username actual: <span class="font-bold text-blue-600">{{ $user->username }}</span>
        </div>
    @endif

    {{-- Roles --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-slate-700 mb-2">Rol del Usuario</label>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($roles as $role)
                <label class="relative flex items-center p-3 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer transition-colors">
                    <input type="radio" name="role" value="{{ $role->name }}"
                        {{ old('role', isset($user) ? $user->roles->first()?->name : '') === $role->name ? 'checked' : '' }}
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300 role-checkbox">
                    <span class="ml-3 text-sm font-medium text-slate-700">{{ $role->name }}</span>
                </label>
            @endforeach
        </div>
        @error('role') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
    </div>

    {{-- Categorías (solo para Coach) --}}
    @php
        $isCoach = old('role', isset($user) ? $user->roles->first()?->name : '') === 'Coach';
    @endphp
    <div class="md:col-span-2" id="campo-categorias" style="display: {{ $isCoach ? 'block' : 'none' }};">
        <div class="flex items-center justify-between mb-2">
            <label class="block text-sm font-semibold text-slate-700">
                Categorías del Coach
                <span class="text-slate-400 font-normal text-xs ml-1">(requerido para rol Coach)</span>
            </label>
            <button type="button" id="btn-add-categoria" class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Añadir Categoría
            </button>
        </div>
        
        <div id="categorias-container" class="space-y-2">
            @php
                $selectedCategories = old('category_ids', isset($user) ? $user->categories->pluck('id')->toArray() : []);
            @endphp

            @if(count($selectedCategories) > 0)
                @foreach($selectedCategories as $index => $selectedId)
                    <div class="flex items-center gap-2 categoria-row">
                        <select name="category_ids[]" class="flex-1 px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="">— Seleccionar categoría —</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" {{ $selectedId == $c->id ? 'selected' : '' }}>
                                    {{ $c->nombre }} ({{ $c->edad_min }}–{{ $c->edad_max }} años)
                                </option>
                            @endforeach
                        </select>
                        <button type="button" class="btn-remove-categoria px-3 py-2.5 text-red-600 hover:bg-red-50 rounded-xl transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                @endforeach
            @else
                <div class="flex items-center gap-2 categoria-row">
                    <select name="category_ids[]" class="flex-1 px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">— Seleccionar categoría —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">
                                {{ $cat->nombre }} ({{ $cat->edad_min }}–{{ $cat->edad_max }} años)
                            </option>
                        @endforeach
                    </select>
                    <button type="button" class="btn-remove-categoria px-3 py-2.5 text-red-600 hover:bg-red-50 rounded-xl transition-colors" style="display: none;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            @endif
        </div>
        @error('category_ids') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
    </div>

    {{-- Password (solo visible al editar, o siempre si decides permitirlo al crear) --}}
    @if(isset($user))
        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
            <x-admin.input label="Nueva Contraseña (Opcional)" name="password" type="password" placeholder="Dejar en blanco para mantener" />
            <x-admin.input label="Confirmar Contraseña" name="password_confirmation" type="password" />
        </div>
    @endif

    <div class="md:col-span-2">
        <label class="relative flex items-center cursor-pointer">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }} class="sr-only peer">
            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
            <span class="ml-3 text-sm font-semibold text-slate-700 italic">Usuario habilitado para el sistema</span>
        </label>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const roleCheckboxes = document.querySelectorAll('.role-checkbox');
    const campoCategorias = document.getElementById('campo-categorias');

    function toggleCategorias() {
        let isCoach = false;
        roleCheckboxes.forEach(rb => {
            if (rb.checked && rb.value === 'Coach') isCoach = true;
        });
        campoCategorias.style.display = isCoach ? 'block' : 'none';
    }

    roleCheckboxes.forEach(rb => rb.addEventListener('change', toggleCategorias));
    toggleCategorias(); // Ejecutar al cargar

    // Lógica para añadir/remover categorías
    const container = document.getElementById('categorias-container');
    const btnAdd = document.getElementById('btn-add-categoria');

    function updateRemoveButtons() {
        const rows = container.querySelectorAll('.categoria-row');
        rows.forEach(row => {
            const btn = row.querySelector('.btn-remove-categoria');
            btn.style.display = rows.length > 1 ? 'block' : 'none';
        });
    }

    btnAdd.addEventListener('click', () => {
        const firstRow = container.querySelector('.categoria-row');
        const newRow = firstRow.cloneNode(true);
        newRow.querySelector('select').value = '';
        container.appendChild(newRow);
        updateRemoveButtons();
    });

    container.addEventListener('click', (e) => {
        if (e.target.closest('.btn-remove-categoria')) {
            const row = e.target.closest('.categoria-row');
            if (container.querySelectorAll('.categoria-row').length > 1) {
                row.remove();
                updateRemoveButtons();
            }
        }
    });

    updateRemoveButtons();
});

function handleAvatarChange(event, previewId) {
    const file = event.target.files[0];
    if (!file) return;
    
    const reader = new FileReader();
    reader.onload = (e) => {
        const preview = document.getElementById(previewId);
        if (preview.tagName === 'IMG') {
            preview.src = e.target.result;
        } else {
            const img = document.createElement('img');
            img.id = previewId;
            img.src = e.target.result;
            img.className = "h-16 w-16 object-cover rounded-full border-2 border-slate-100 shadow-sm";
            preview.parentNode.replaceChild(img, preview);
        }
    };
    reader.readAsDataURL(file);
}
</script>
