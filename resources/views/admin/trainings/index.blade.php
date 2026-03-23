@extends('layouts.admin')

@section('title', 'Planificaciones Semanales')

@section('content')
<div class="flex items-center justify-between mb-8">
    <div class="flex items-center space-x-4">
        <form action="{{ route('trainings.index') }}" method="GET" class="flex items-center space-x-2">
            <select name="category_id" onchange="this.form.submit()" class="block w-64 px-3 py-2.5 border border-slate-200 rounded-xl bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todas las categorías</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->nombre }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <x-admin.button onclick="window.location.href='{{ route('trainings.create') }}'">
        <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Nueva Planificación
    </x-admin.button>
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
        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Fecha</th>
        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Categoría</th>
        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Entrenador</th>
        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Documento</th>
        <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Acciones</th>
    </x-slot>

    @forelse($trainings as $training)
        <tr class="hover:bg-slate-50/50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-900">
                {{ $training->fecha->format('d/m/Y') }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-blue-50 text-blue-700 uppercase italic">
                    {{ $training->category->nombre }}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                {{ $training->coach->name }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                @if($training->file_path_pdf)
                    <a href="{{ asset('storage/' . $training->file_path_pdf) }}" target="_blank" class="inline-flex items-center text-red-600 hover:text-red-700 font-bold text-xs group">
                        <svg class="h-4 w-4 mr-1 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm1 2.414L17.586 8H15V4.414zM18 20H6V4h7v5h5v11z"/>
                        </svg>
                        Planes.pdf
                    </a>
                @else
                    <span class="text-slate-300 italic text-xs">Sin archivo</span>
                @endif
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex items-center justify-end space-x-3">
                    <a href="{{ route('trainings.edit', $training) }}" class="text-blue-600 hover:text-blue-900 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </a>
                    <form action="{{ route('trainings.destroy', $training) }}" method="POST" onsubmit="return confirm('¿Eliminar planificación?')">
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
            <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">
                No hay planificaciones registradas.
            </td>
        </tr>
    @endforelse

    <x-slot name="footer">
        {{ $trainings->links() }}
    </x-slot>
</x-admin.table>
@endsection
