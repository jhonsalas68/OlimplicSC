@if(isset($user))
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <x-admin.input label="Nombres" name="name" :value="$user->name ?? ''" required placeholder="Ej: Administrador" />
    <div class="grid grid-cols-2 gap-4">
        <x-admin.input label="Ape. Paterno" name="apellido_paterno" :value="$user->apellido_paterno ?? ''" required />
        <x-admin.input label="Ape. Materno" name="apellido_materno" :value="$user->apellido_materno ?? ''" />
    </div>
    
    <x-admin.input label="C.I. (Contraseña inicial)" name="ci" :value="$user->ci ?? ''" required placeholder="Contraseña por defecto" />
    <x-admin.input label="Correo Electrónico" name="email" type="email" :value="$user->email ?? ''" required placeholder="admin@olimpicsc.com" />

    <div class="md:col-span-2 bg-slate-50 p-4 rounded-xl border border-slate-100 italic text-xs text-slate-500">
        Username actual: <span class="font-bold text-blue-600">{{ $user->username }}</span>
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-slate-700 mb-2">Rol del Usuario</label>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($roles as $role)
                <label class="relative flex items-center p-3 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer transition-colors">
                    <input type="radio" name="role" value="{{ $role->name }}"
                        {{ old('role', $user->roles->first()?->name) === $role->name ? 'checked' : '' }}
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300 role-checkbox">
                    <span class="ml-3 text-sm font-medium text-slate-700">{{ $role->name }}</span>
                </label>
            @endforeach
        </div>
        @error('role') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
    </div>

    {{-- Categoría (solo para Coach) --}}
    <div class="md:col-span-2" id="campo-categoria" style="{{ $user->hasRole('Coach') ? '' : 'display:none' }}">
        <label class="block text-sm font-semibold text-slate-700 mb-2">
            Categoría del Coach
            <span class="text-slate-400 font-normal text-xs ml-1">(requerido para rol Coach)</span>
        </label>
        <select name="category_id" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
            <option value="">— Seleccionar categoría —</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id', $user->category_id) == $cat->id ? 'selected' : '' }}>
                    {{ $cat->nombre }} ({{ $cat->edad_min }}–{{ $cat->edad_max }} años)
                </option>
            @endforeach
        </select>
        @error('category_id') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
        <x-admin.input label="Nueva Contraseña (Opcional)" name="password" type="password" placeholder="Cambiar para el usuario" />
        <x-admin.input label="Confirmar Contraseña" name="password_confirmation" type="password" />
    </div>

    <div class="md:col-span-2">
        <label class="relative flex items-center cursor-pointer">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }} class="sr-only peer">
            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
            <span class="ml-3 text-sm font-semibold text-slate-700 italic">Usuario habilitado para el sistema</span>
        </label>
    </div>
</div>
@else
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <x-admin.input label="Nombres" name="name" :value="old('name')" required placeholder="Ej: Juan" />
    <div class="grid grid-cols-2 gap-4">
        <x-admin.input label="Ape. Paterno" name="apellido_paterno" :value="old('apellido_paterno')" required />
        <x-admin.input label="Ape. Materno" name="apellido_materno" :value="old('apellido_materno')" />
    </div>
    
    <x-admin.input label="C.I. (Contraseña inicial)" name="ci" :value="old('ci')" required placeholder="Contraseña por defecto" />
    <x-admin.input label="Correo Electrónico" name="email" type="email" :value="old('email')" required placeholder="juan@olimpicsc.com" />

    <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-slate-700 mb-2">Rol del Usuario</label>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($roles as $role)
                <label class="relative flex items-center p-3 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer transition-colors">
                    <input type="radio" name="role" value="{{ $role->name }}"
                        {{ old('role') === $role->name ? 'checked' : '' }}
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300 role-checkbox">
                    <span class="ml-3 text-sm font-medium text-slate-700">{{ $role->name }}</span>
                </label>
            @endforeach
        </div>
        @error('role') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
    </div>

    {{-- Categoría (solo para Coach) --}}
    <div class="md:col-span-2" id="campo-categoria" style="display:none">
        <label class="block text-sm font-semibold text-slate-700 mb-2">
            Categoría del Coach
            <span class="text-slate-400 font-normal text-xs ml-1">(requerido para rol Coach)</span>
        </label>
        <select name="category_id" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
            <option value="">— Seleccionar categoría —</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->nombre }} ({{ $cat->edad_min }}–{{ $cat->edad_max }} años)
                </option>
            @endforeach
        </select>
        @error('category_id') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="relative flex items-center cursor-pointer">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="sr-only peer">
            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
            <span class="ml-3 text-sm font-semibold text-slate-700 italic">Usuario habilitado para el sistema</span>
        </label>
    </div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    function toggleCategoria() {
        const isCoach = Array.from(document.querySelectorAll('.role-checkbox'))
            .some(cb => cb.value === 'Coach' && cb.checked);
        const campo = document.getElementById('campo-categoria');
        if (campo) campo.style.display = isCoach ? '' : 'none';
    }
    document.querySelectorAll('.role-checkbox').forEach(cb => cb.addEventListener('change', toggleCategoria));
    toggleCategoria();
});
</script>
