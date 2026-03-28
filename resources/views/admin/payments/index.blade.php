@extends('layouts.admin')
@section('title', 'Historial de Pagos')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Historial de Pagos</h1>
        <p class="text-sm text-slate-500 mt-0.5">Registro mensual de cobros realizados</p>
    </div>
    <a href="{{ route('cobros.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        Nuevo Cobro
    </a>
</div>

{{-- FILTROS --}}
<form action="{{ route('payments.index') }}" method="GET" id="filtros-form">
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 mb-5">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 items-end">

        {{-- Búsqueda --}}
        <div class="lg:col-span-2">
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Atleta / CI</label>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Buscar atleta o CI..."
                       class="w-full pl-9 pr-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        {{-- Mes --}}
        <div class="sm:col-span-1">
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Mes</label>
            <input type="month" name="mes" value="{{ request('mes', now()->format('Y-m')) }}"
                   class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        {{-- Método de pago --}}
        <div class="sm:col-span-1">
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Método</label>
            <select name="metodo" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todos</option>
                <option value="efectivo" {{ request('metodo') == 'efectivo' ? 'selected' : '' }}>💵 Efectivo</option>
                <option value="qr"       {{ request('metodo') == 'qr'       ? 'selected' : '' }}>📱 QR</option>
                <option value="tarjeta"  {{ request('metodo') == 'tarjeta'  ? 'selected' : '' }}>💳 Tarjeta</option>
            </select>
        </div>

        {{-- Concepto --}}
        <div class="sm:col-span-1">
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Concepto</label>
            <select name="concepto" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todos</option>
                <option value="mensualidad"       {{ request('concepto') == 'mensualidad'       ? 'selected' : '' }}>Mensualidad</option>
                <option value="articulo_deportivo" {{ request('concepto') == 'articulo_deportivo' ? 'selected' : '' }}>Artículo Deportivo</option>
            </select>
        </div>

        {{-- Botones --}}
        <div class="flex gap-2 sm:col-span-1 lg:col-span-1">
            <button type="submit"
                    class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors">
                Filtrar
            </button>
            <a href="{{ route('payments.index') }}"
               class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-semibold rounded-xl transition-colors">
                ✕
            </a>
        </div>
    </div>
</div>
</form>

{{-- RESUMEN + EXPORTAR --}}
@php
    $totalMonto   = $payments->sum('monto');
    $mesLabel     = request('mes') ? \Carbon\Carbon::createFromFormat('Y-m', request('mes'))->translatedFormat('F Y') : 'Todos los meses';
    $queryString  = http_build_query(array_filter(request()->only(['search','mes','metodo','concepto'])));
@endphp
<div class="flex items-center justify-between mb-4 flex-wrap gap-3">
    <div class="flex items-center gap-3 flex-wrap">
        <span class="text-sm text-slate-600 font-medium">
            {{ $payments->total() }} registro(s) &mdash; Total: <span class="font-bold text-slate-900">Bs. {{ number_format($totalMonto, 2) }}</span>
        </span>
        @if(request('mes'))
            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-semibold">{{ $mesLabel }}</span>
        @endif
        @if(request('metodo'))
            <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full font-semibold">{{ ucfirst(request('metodo')) }}</span>
        @endif
    </div>
    <div class="flex gap-2">
        <a href="{{ route('payments.export.pdf') }}?{{ $queryString }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-bold rounded-xl transition-colors border border-red-200">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            PDF
        </a>
        <a href="{{ route('payments.export.excel') }}?{{ $queryString }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 hover:bg-green-100 text-green-700 text-xs font-bold rounded-xl transition-colors border border-green-200">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Excel
        </a>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 p-4 bg-emerald-50 border border-emerald-100 rounded-xl text-emerald-700 text-sm font-medium flex items-center gap-2">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
@endif

<x-admin.table>
    <x-slot name="header">
        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Atleta</th>
        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Concepto</th>
        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Monto</th>
        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Método</th>
        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Fecha</th>
        <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Acciones</th>
    </x-slot>

    @forelse($payments as $payment)
    <tr class="hover:bg-slate-50/50 transition-colors">
        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-900">
            {{ trim($payment->athlete->nombre . ' ' . $payment->athlete->apellido_paterno) }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
            <span class="font-medium">{{ $payment->concepto === 'mensualidad' ? 'Mensualidad' : 'Artículo Deportivo' }}</span>
            @if($payment->mes_correspondiente)
                <span class="text-slate-400 text-xs block">{{ $payment->mes_correspondiente }}</span>
            @endif
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900">
            Bs. {{ number_format($payment->monto, 2) }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            @php
                $ml = ['efectivo'=>'💵 Efectivo','qr'=>'📱 QR','tarjeta'=>'💳 Tarjeta'][$payment->metodo_pago] ?? ($payment->metodo_pago ?? '—');
                $mc = ['efectivo'=>'bg-green-100 text-green-800','qr'=>'bg-blue-100 text-blue-800','tarjeta'=>'bg-purple-100 text-purple-800'][$payment->metodo_pago] ?? 'bg-slate-100 text-slate-800';
            @endphp
            <span class="px-2 py-1 text-[10px] font-bold rounded-full {{ $mc }}">{{ $ml }}</span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
            {{ $payment->created_at->format('d/m/Y H:i') }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <div class="flex items-center justify-end space-x-3">
                <a href="{{ route('cobros.nota', $payment) }}" class="text-blue-600 hover:text-blue-900 transition-colors" title="Ver nota">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </a>
                <form action="{{ route('payments.destroy', $payment) }}" method="POST" onsubmit="return confirm('¿Eliminar registro?')">
                    @csrf @method('DELETE')
                    <button class="text-red-600 hover:text-red-900 transition-colors cursor-pointer">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </form>
            </div>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">No hay pagos para los filtros seleccionados.</td>
    </tr>
    @endforelse

    <x-slot name="footer">
        {{ $payments->appends(request()->query())->links() }}
    </x-slot>
</x-admin.table>
@endsection
