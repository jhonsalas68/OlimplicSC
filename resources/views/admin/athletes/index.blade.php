@extends('layouts.admin')

@section('title', isset($selectedCategory) ? 'Atletas: ' . $selectedCategory->nombre : 'Atletas Olímpicos')

@section('content')
@if(isset($selectedCategory))
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
    <div>
        <h1 class="text-2xl font-black text-slate-800 tracking-tight">{{ $selectedCategory->nombre }}</h1>
        <p class="text-sm font-medium text-slate-500 mt-1">Edades permitidas: {{ $selectedCategory->edad_min }} a {{ $selectedCategory->edad_max }} años</p>
    </div>
    <a href="{{ route('athletes.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition-colors shadow-sm whitespace-nowrap">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Volver a Categorías
    </a>
</div>
@endif

<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-8">
    <div class="flex items-center space-x-4 w-full lg:w-auto">
        <form action="{{ route('athletes.index') }}" method="GET" id="filter-form" class="flex flex-col sm:flex-row items-center gap-3 w-full">
            @if(request('category_id'))
                <input type="hidden" name="category_id" value="{{ request('category_id') }}">
            @endif
            
            <div class="relative group w-full sm:w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="block w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm transition-all shadow-sm"
                       placeholder="Buscar por nombre o DNI...">
            </div>

            <select name="deuda" onchange="this.form.submit()" 
                    class="block w-full sm:w-44 px-3 py-2.5 border border-slate-200 rounded-xl leading-5 bg-white text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500 sm:text-sm transition-all shadow-sm cursor-pointer">
                <option value="">Todas las mensualidades</option>
                <option value="al_dia" {{ request('deuda') === 'al_dia' ? 'selected' : '' }}>✅ Al Día</option>
                <option value="deudores" {{ request('deuda') === 'deudores' ? 'selected' : '' }}>❌ Deudores</option>
            </select>
        </form>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <!-- Export Button -->
        <a href="{{ route('athletes.export') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition-colors shadow-sm border border-slate-200">
            <svg class="w-4 h-4 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Exportar
        </a>
 
        <!-- Convocados Button (Selective Export) -->
        <button id="btn-convocados" onclick="exportSelected()" class="hidden flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm border border-blue-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            Convocados (<span id="selected-count">0</span>)
        </button>

        <!-- Import Button (Triggers Modal) -->
        <button onclick="document.getElementById('importModal').classList.remove('hidden')" class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition-colors shadow-sm border border-slate-200">
            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
            Importar
        </button>

        <x-admin.button onclick="window.location.href='{{ route('athletes.create') }}'" class="w-full sm:w-auto justify-center">
            <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Nuevo Atleta
        </x-admin.button>
    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in duration-200">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="text-lg font-bold text-slate-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                Importar Atletas
            </h3>
            <button onclick="document.getElementById('importModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-2 hover:bg-slate-100 rounded-full transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('athletes.import') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-tight">Archivo Excel (.xlsx únicamente)</label>
                <div class="relative">
                    <input type="file" name="file" required class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer border border-slate-200 rounded-xl p-2 bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            
            <div class="bg-amber-50 border border-amber-100 p-4 rounded-xl">
                <div class="flex">
                    <svg class="h-5 w-5 text-amber-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="text-xs text-amber-800 leading-relaxed font-medium">
                        Asegúrese de que las columnas coincidan con la plantilla oficial para evitar errores en el registro.
                    </p>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <x-admin.button type="button" variant="secondary" onclick="document.getElementById('importModal').classList.add('hidden')">
                    Cancelar
                </x-admin.button>
                <x-admin.button type="submit">
                    Importar Datos
                </x-admin.button>
            </div>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-xl text-emerald-700 text-sm font-medium flex items-center">
        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ session('success') }}
    </div>
@endif

<x-admin.table>
    <x-slot name="header">
        <th class="px-6 py-3 text-left">
            <input type="checkbox" id="select-all" class="h-4 w-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
        </th>
        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Atleta</th>
        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">C.I.</th>
        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Categoría</th>
        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Estado</th>
        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Mensualidad</th>
        <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Acciones</th>
    </x-slot>

    @forelse($athletes as $athlete)
        <tr class="hover:bg-slate-50/50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap">
                <input type="checkbox" name="selected_athletes[]" value="{{ $athlete->id }}" class="athlete-checkbox h-4 w-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                        @if($athlete->foto)
                            <img class="h-10 w-10 rounded-full object-cover shadow-sm bg-slate-100" 
                                 src="{{ str_starts_with($athlete->foto, 'http') ? str_replace('/upload/', '/upload/c_fill,w_100,h_100,q_auto,f_auto/', $athlete->foto) : asset('storage/' . $athlete->foto) }}" alt="">
                        @else
                            <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 font-bold text-xs border border-slate-200">
                                {{ strtoupper(substr($athlete->nombre,0,1).substr($athlete->apellido_paterno??'',0,1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-semibold text-slate-900">{{ $athlete->nombre }} {{ $athlete->apellido_paterno }} {{ $athlete->apellido_materno }}</div>
                        <div class="text-xs text-slate-500 mt-0.5">Nacimiento: {{ $athlete->fecha_nacimiento?->format('d/m/Y') ?? 'Sin fecha' }}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 font-medium">
                {{ $athlete->ci }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-bold rounded-full bg-slate-100 text-slate-700">
                    {{ $athlete->category?->nombre ?? 'N/A' }}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('SuperAdmin'))
                <button onclick="toggleHabilitado({{ $athlete->id }}, this)"
                        data-habilitado="{{ $athlete->habilitado_booleano ? '1' : '0' }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold transition-all
                               {{ $athlete->habilitado_booleano
                                   ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200'
                                   : 'bg-red-100 text-red-600 hover:bg-red-200' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $athlete->habilitado_booleano ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                    {{ $athlete->habilitado_booleano ? 'Habilitado' : 'Inhabilitado' }}
                </button>
                @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold
                             {{ $athlete->habilitado_booleano ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $athlete->habilitado_booleano ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                    {{ $athlete->habilitado_booleano ? 'Habilitado' : 'Inhabilitado' }}
                </span>
                @endif
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                @if($athlete->pagado_mes_actual)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        Al Día
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-100">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                        Debe
                    </span>
                @endif
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex items-center justify-end space-x-3">
                    <a href="{{ route('athletes.show', $athlete) }}" class="inline-flex items-center px-4 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white rounded-lg text-xs font-bold transition-colors shadow-sm">
                        Ver Perfil
                    </a>
                    <a href="{{ route('athletes.edit', $athlete) }}" class="text-blue-600 hover:text-blue-900 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </a>
                    <form action="{{ route('athletes.destroy', $athlete) }}" method="POST" onsubmit="return confirm('¿Eliminar atleta?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:text-red-900 transition-colors cursor-pointer">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">
                <div class="flex flex-col items-center">
                    <svg class="h-12 w-12 mb-3 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    No se encontraron atletas.
                </div>
            </td>
        </tr>
    @endforelse

    <x-slot name="footer">
        {{ $athletes->links() }}
    </x-slot>
</x-admin.table>

<script>
document.getElementById('select-all').addEventListener('change', function() {
    const checked = this.checked;
    document.querySelectorAll('.athlete-checkbox').forEach(cb => {
        cb.checked = checked;
    });
    updateSelectedCount();
});

document.querySelectorAll('.athlete-checkbox').forEach(cb => {
    cb.addEventListener('change', updateSelectedCount);
});

function updateSelectedCount() {
    const selected = document.querySelectorAll('.athlete-checkbox:checked').length;
    document.getElementById('selected-count').textContent = selected;
    document.getElementById('btn-convocados').classList.toggle('hidden', selected === 0);
}

function exportSelected() {
    const ids = Array.from(document.querySelectorAll('.athlete-checkbox:checked')).map(cb => cb.value);
    if (ids.length === 0) return;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = "{{ route('athletes.export.selected') }}";
    
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = "{{ csrf_token() }}";
    form.appendChild(csrf);
    
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'ids';
    input.value = JSON.stringify(ids);
    form.appendChild(input);
    
    document.body.appendChild(form);
    form.submit();
}

@if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('SuperAdmin'))
function toggleHabilitado(id, btn) {
    fetch(`/admin/athletes/${id}/toggle-habilitado`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        const habilitado = data.habilitado;
        btn.dataset.habilitado = habilitado ? '1' : '0';
        btn.className = `inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold transition-all ${
            habilitado ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200'
                       : 'bg-red-100 text-red-600 hover:bg-red-200'
        }`;
        btn.innerHTML = `<span class="w-1.5 h-1.5 rounded-full ${habilitado ? 'bg-emerald-500' : 'bg-red-500'}"></span>
                         ${habilitado ? 'Habilitado' : 'Inhabilitado'}`;
    });
}
@endif
</script>
@endsection
