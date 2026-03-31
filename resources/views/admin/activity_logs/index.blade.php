@extends('layouts.admin')

@section('title', 'Bitácora de Actividades (Activity Log)')

@section('content')
<div class="container-fluid px-4 py-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 leading-tight">Bitácora de Actividades</h1>
            <p class="text-gray-500 mt-2">Seguimiento histórico de las acciones críticas realizadas en el sistema.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-all duration-200 shadow-sm leading-6">
                <i class="fas fa-arrow-left mr-2"></i> Volver al Dashboard
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
        <form action="{{ route('admin.activity-logs.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Usuario</label>
                <select name="user_id" class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-all duration-200">
                    <option value="">Cualquier usuario</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->username }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Acción</label>
                <select name="action" class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-all duration-200">
                    <option value="">Cualquier acción</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $action)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha</label>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-all duration-200">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all duration-200 font-semibold shadow-md shadow-indigo-100">
                    <i class="fas fa-filter mr-2"></i> Filtrar
                </button>
                <a href="{{ route('admin.activity-logs.index') }}" class="p-2 text-gray-500 hover:text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-xl transition-all duration-200 shadow-sm" title="Limpiar filtros">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest text-wrap">Usuario</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest text-wrap">Acción</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest text-wrap">Descripción</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest text-wrap">Fecha / Hora</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest text-wrap">IP / Navegador</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($logs as $log)
                    <tr class="hover:bg-indigo-50/20 transition-colors duration-150">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0 rounded-full bg-indigo-100 flex items-center justify-center">
                                    @if($log->user && $log->user->avatar)
                                        <img src="{{ $log->user->avatar }}" class="h-10 w-10 rounded-full" alt="Avatar">
                                    @else
                                        <span class="text-indigo-600 font-bold">{{ substr($log->user ? $log->user->name : 'G', 0, 1) }}</span>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-semibold text-gray-900">{{ $log->user ? $log->user->name : 'Sistema / Invitado' }}</div>
                                    <div class="text-xs text-gray-500">{{ $log->user ? $log->user->username : 'Sin registro' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $badgeColor = match($log->action) {
                                    'inicio_sesion' => 'bg-green-100 text-green-700 border-green-200',
                                    'login_fallido' => 'bg-red-100 text-red-700 border-red-200',
                                    'venta_realizada' => 'bg-blue-100 text-blue-700 border-blue-200',
                                    'eliminacion_usuario', 'eliminacion_atleta', 'eliminacion_planificacion' => 'bg-red-100 text-red-700 border-red-200',
                                    'creacion_usuario', 'inscripcion_atleta', 'subida_planificacion' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                    default => 'bg-gray-100 text-gray-700 border-gray-200',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $badgeColor }}">
                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-800 break-words whitespace-pre-wrap max-w-xs md:max-w-md">
                                {{ $log->description }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $log->created_at->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $log->created_at->format('H:i:s') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-mono font-bold leading-6">{{ $log->ip_address ?? '—' }}</span>
                                <span class="text-xs text-gray-400" title="{{ $log->user_agent }}">
                                    @if(str_contains($log->user_agent, 'Chrome')) <i class="fab fa-chrome"></i>
                                    @elseif(str_contains($log->user_agent, 'Firefox')) <i class="fab fa-firefox"></i>
                                    @elseif(str_contains($log->user_agent, 'Safari')) <i class="fab fa-safari"></i>
                                    @else <i class="fas fa-globe"></i>
                                    @endif
                                </span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <div class="bg-gray-50 h-16 w-16 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-search text-gray-300 text-2xl"></i>
                                </div>
                                <p class="text-lg font-medium text-gray-400">No se encontraron registros en la bitácora.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
