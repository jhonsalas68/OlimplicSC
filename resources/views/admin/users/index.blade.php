@extends('layouts.admin')

@section('title', 'Usuarios y Roles')

@section('content')
<div class="mb-6">
    <div class="flex items-center space-x-4 mb-2">
        <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center h-10 w-10 min-w-[40px] rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition-colors shadow-sm">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            @if(isset($selectedRole))
                <h1 class="text-2xl font-bold text-slate-800">Usuarios: Rol {{ $selectedRole->name }}</h1>
            @elseif(request('search'))
                <h1 class="text-2xl font-bold text-slate-800">Resultados de búsqueda</h1>
            @else
                <h1 class="text-2xl font-bold text-slate-800">Todos los Usuarios</h1>
            @endif
        </div>
    </div>
</div>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div class="flex items-center space-x-4 w-full sm:w-auto">
        <form action="{{ route('users.index') }}" method="GET" class="relative group w-full">
            @if(request('role_id'))
                <input type="hidden" name="role_id" value="{{ request('role_id') }}">
            @endif
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" 
                   class="block w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm transition-all shadow-sm"
                   placeholder="Buscar usuario...">
        </form>
    </div>

    <x-admin.button onclick="window.location.href='{{ route('users.create') }}'" class="w-full sm:w-auto justify-center">
        <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Nuevo Usuario
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
        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nombre</th>
        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Usuario</th>
        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Rol</th>
        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Estado</th>
        <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Acciones</th>
    </x-slot>

    @foreach($users as $user)
        <tr class="hover:bg-slate-50/50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="shrink-0 h-8 w-8">
                        <img class="h-8 w-8 rounded-full object-cover border border-slate-200" src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
                    </div>
                    <div class="ml-3">
                        <div class="text-sm font-semibold text-slate-900">{{ $user->name }} {{ $user->apellido_paterno }} {{ $user->apellido_materno }}</div>
                        <div class="text-xs text-slate-500">{{ $user->email }}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 font-medium">
                {{ $user->username }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                @foreach($user->roles as $role)
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-blue-50 text-blue-700 uppercase">
                        {{ $role->name }}
                    </span>
                @endforeach
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                @if($user->is_active)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                        <span class="h-2 w-2 mr-1.5 rounded-full bg-emerald-400"></span>
                        Activo
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-500">
                        <span class="h-2 w-2 mr-1.5 rounded-full bg-slate-400"></span>
                        Inactivo
                    </span>
                @endif
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex items-center justify-end space-x-3">
                    @if($user->hasRole('SuperAdmin'))
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-100 px-2 py-1 rounded-md">Intocable</span>
                    @else
                        <a href="{{ route('users.edit', $user) }}" class="text-blue-600 hover:text-blue-900 transition-colors">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                        @if($user->id !== auth()->id())
                        <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('¿Eliminar usuario?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:text-red-900 transition-colors cursor-pointer">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                        @endif
                    @endif
                </div>
            </td>
        </tr>
    @endforeach

    <x-slot name="footer">
        {{ $users->links() }}
    </x-slot>
</x-admin.table>
@endsection
