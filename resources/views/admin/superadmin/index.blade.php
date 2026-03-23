@extends('layouts.admin')

@section('title', 'Super Admin — Herramientas')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Herramientas Super Admin</h1>
    <p class="text-sm text-slate-500 mt-0.5">Exportaciones, importaciones y respaldo de datos</p>
</div>

@if(session('success'))
    <div class="mb-4 p-4 bg-emerald-50 border border-emerald-100 rounded-xl text-emerald-700 text-sm font-medium flex items-center gap-2">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-4 p-4 bg-red-50 border border-red-100 rounded-xl text-red-700 text-sm font-medium">{{ session('error') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ESTADÍSTICAS --}}
    <div class="lg:col-span-3 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        @php
            $cards = [
                ['label' => 'Atletas', 'value' => $stats['atletas'], 'color' => 'blue'],
                ['label' => 'Pagos', 'value' => $stats['pagos'], 'color' => 'green'],
                ['label' => 'Usuarios', 'value' => $stats['usuarios'], 'color' => 'purple'],
                ['label' => 'Categorías', 'value' => $stats['categorias'], 'color' => 'orange'],
                ['label' => 'Planificaciones', 'value' => $stats['planificaciones'], 'color' => 'red'],
                ['label' => 'Total Cobrado', 'value' => 'Bs. ' . number_format($stats['total_cobrado'], 0), 'color' => 'teal'],
            ];
            $colors = ['blue'=>'bg-blue-50 text-blue-700','green'=>'bg-green-50 text-green-700','purple'=>'bg-purple-50 text-purple-700','orange'=>'bg-orange-50 text-orange-700','red'=>'bg-red-50 text-red-700','teal'=>'bg-teal-50 text-teal-700'];
        @endphp
        @foreach($cards as $card)
            <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 text-center">
                <p class="text-xl font-bold {{ explode(' ', $colors[$card['color']])[1] }}">{{ $card['value'] }}</p>
                <p class="text-xs text-slate-500 mt-0.5">{{ $card['label'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- EXPORTAR PDF --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-bold text-slate-800">Exportar PDF</h2>
                <p class="text-xs text-slate-400">Reportes listos para imprimir</p>
            </div>
        </div>
        <div class="space-y-2">
            <a href="{{ route('superadmin.export.atletas.pdf') }}"
               class="flex items-center justify-between w-full px-4 py-3 bg-red-50 hover:bg-red-100 text-red-700 rounded-xl text-sm font-semibold transition-colors">
                <span>Atletas (PDF)</span>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            </a>
            <a href="{{ route('superadmin.export.pagos.pdf') }}"
               class="flex items-center justify-between w-full px-4 py-3 bg-red-50 hover:bg-red-100 text-red-700 rounded-xl text-sm font-semibold transition-colors">
                <span>Pagos (PDF)</span>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            </a>
        </div>
    </div>

    {{-- EXPORTAR EXCEL --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-bold text-slate-800">Exportar Excel</h2>
                <p class="text-xs text-slate-400">Datos editables en .xlsx</p>
            </div>
        </div>
        <div class="space-y-2">
            <a href="{{ route('superadmin.export.atletas.excel') }}"
               class="flex items-center justify-between w-full px-4 py-3 bg-green-50 hover:bg-green-100 text-green-700 rounded-xl text-sm font-semibold transition-colors">
                <span>Atletas (Excel)</span>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            </a>
            <a href="{{ route('superadmin.export.pagos.excel') }}"
               class="flex items-center justify-between w-full px-4 py-3 bg-green-50 hover:bg-green-100 text-green-700 rounded-xl text-sm font-semibold transition-colors">
                <span>Pagos (Excel)</span>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            </a>
            <a href="{{ route('superadmin.export.usuarios.excel') }}"
               class="flex items-center justify-between w-full px-4 py-3 bg-green-50 hover:bg-green-100 text-green-700 rounded-xl text-sm font-semibold transition-colors">
                <span>Usuarios (Excel)</span>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            </a>
        </div>
    </div>

    {{-- IMPORTAR + BACKUP --}}
    <div class="space-y-4">

        {{-- Importar --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                    <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-800">Importar Atletas</h2>
                    <p class="text-xs text-slate-400">Sube un archivo .xlsx</p>
                </div>
            </div>
            <form action="{{ route('superadmin.import.atletas') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="border-2 border-dashed border-slate-200 rounded-xl p-4 text-center mb-3 hover:border-blue-300 transition-colors">
                    <input type="file" name="file" accept=".xlsx,.xls" required
                           class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                </div>
                <p class="text-xs text-slate-400 mb-3">
                    Usa el formato del Excel exportado.
                    <a href="{{ route('superadmin.export.atletas.excel') }}" class="text-blue-600 hover:underline">Descargar plantilla</a>
                </p>
                <button type="submit"
                        class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors">
                    Importar
                </button>
            </form>
        </div>

        {{-- Backup --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center">
                    <svg class="h-5 w-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-800">Backup de Datos</h2>
                    <p class="text-xs text-slate-400">Respaldo completo del sistema</p>
                </div>
            </div>
            <div class="space-y-2">
                <a href="{{ route('superadmin.backup.sql') }}"
                   class="flex items-center justify-between w-full px-4 py-3 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-xl text-sm font-semibold transition-colors">
                    <span>Backup SQL</span>
                    <span class="text-xs text-slate-400">.sql</span>
                </a>
                <a href="{{ route('superadmin.backup.excel') }}"
                   class="flex items-center justify-between w-full px-4 py-3 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-xl text-sm font-semibold transition-colors">
                    <span>Backup Excel (todas las tablas)</span>
                    <span class="text-xs text-slate-400">.xlsx</span>
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
