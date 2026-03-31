@extends('layouts.admin')

@section('title', 'Roles de Usuario')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Usuarios y Roles</h1>
            <p class="text-sm text-slate-500 mt-1">Selecciona un rol para ver y gestionar los usuarios del sistema.</p>
        </div>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <form action="{{ route('users.index') }}" method="GET" class="relative w-full sm:w-64">
                <input type="text" name="search" placeholder="Buscar por Nombre o C.I..."
                       class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                       value="{{ request('search') }}">
                <svg class="h-4 w-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </form>
            <a href="{{ route('users.create') }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm whitespace-nowrap">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo Usuario
            </a>
        </div>
    </div>

    {{-- Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($roles as $role)
            <a href="{{ route('users.index', ['role_id' => $role->id]) }}" 
               class="group relative bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 overflow-hidden flex flex-col h-full">
                
                <div class="h-2 w-full {{ $role->name === 'SuperAdmin' ? 'bg-gradient-to-r from-amber-400 to-amber-600' : 'bg-gradient-to-r from-blue-600 to-red-600' }}"></div>
                
                <div class="p-6 flex-1 flex flex-col relative z-10">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 {{ $role->name === 'SuperAdmin' ? 'bg-amber-50 text-amber-600' : 'bg-blue-50 text-blue-600' }} rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                            @if($role->name === 'SuperAdmin')
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                            @elseif($role->name === 'Admin')
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            @else
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            @endif
                        </div>
                        <span class="inline-flex items-center justify-center bg-slate-100 text-slate-600 text-xs font-bold px-2.5 py-1 rounded-full group-hover:bg-blue-100 group-hover:text-blue-700 transition-colors">
                            {{ $role->users_count }} usuarios
                        </span>
                    </div>
                    
                    <h3 class="text-xl font-black text-slate-800 tracking-tight group-hover:text-blue-600 transition-colors mb-1">
                        {{ $role->name }}
                    </h3>
                    <p class="text-sm font-medium text-slate-500 mb-4">
                        Gestionar permisos y cuentas
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

    @if($roles->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center max-w-2xl mx-auto">
            <svg class="h-12 w-12 mx-auto text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            <h3 class="text-lg font-bold text-slate-800 mb-1">No hay roles configurados</h3>
            <p class="text-slate-500 text-sm">Necesitas correr los seeders para ver los roles aquí.</p>
        </div>
    @endif
</div>
@endsection
