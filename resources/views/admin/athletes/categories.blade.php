@extends('layouts.admin')

@section('title', 'Categorías de Atletas')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Atletas Olímpicos</h1>
            <p class="text-sm text-slate-500 mt-1">Selecciona una categoría para ver a los atletas correspondientes.</p>
        </div>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <form action="{{ route('athletes.index') }}" method="GET" class="relative w-full sm:w-64">
                <input type="text" name="search" placeholder="Buscar por C.I. o Nombre..."
                       class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                       value="{{ request('search') }}">
                <svg class="h-4 w-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </form>
            <a href="{{ route('athletes.create') }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm whitespace-nowrap">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo Atleta
            </a>
        </div>
    </div>

    {{-- Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($categories as $category)
            <a href="{{ route('athletes.index', ['category_id' => $category->id]) }}" 
               class="group relative bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 overflow-hidden flex flex-col h-full">
                
                <div class="h-2 w-full bg-gradient-to-r from-blue-600 to-red-600"></div>
                
                <div class="p-6 flex-1 flex flex-col relative z-10">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <span class="inline-flex items-center justify-center bg-slate-100 text-slate-600 text-xs font-bold px-2.5 py-1 rounded-full group-hover:bg-blue-100 group-hover:text-blue-700 transition-colors">
                            {{ $category->athletes_count }} atletas
                        </span>
                    </div>
                    
                    <h3 class="text-xl font-black text-slate-800 tracking-tight group-hover:text-blue-600 transition-colors mb-1">
                        {{ $category->nombre }}
                    </h3>
                    <p class="text-sm font-medium text-slate-500 mb-4">
                        Edades: {{ $category->edad_min }} - {{ $category->edad_max }} años
                    </p>

                    <div class="mt-auto flex items-center text-sm font-semibold text-blue-600 group-hover:text-red-500 transition-colors">
                        Ver listado completo
                        <svg class="h-4 w-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    @if($categories->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center max-w-2xl mx-auto">
            <svg class="h-12 w-12 mx-auto text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <h3 class="text-lg font-bold text-slate-800 mb-1">No hay categorías configuradas</h3>
            <p class="text-slate-500 text-sm">Necesitas correr los seeders o crear categorías en la base de datos para verlas aquí.</p>
        </div>
    @endif
</div>
@endsection
