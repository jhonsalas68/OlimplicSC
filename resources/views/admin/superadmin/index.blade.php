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
            <div class="space-y-2" x-data="{ sending: false }">
                <a href="{{ route('superadmin.backup.sql') }}"
                   class="flex items-center justify-between w-full px-4 py-3 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-xl text-sm font-semibold transition-colors">
                    <span>Backup SQL (Descarga Directa)</span>
                    <span class="text-xs text-slate-400">.sql</span>
                </a>
                
                <a href="{{ route('superadmin.backup.email') }}" 
                   @click="sending = true"
                   class="flex items-center justify-between w-full px-4 py-3 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-xl text-sm font-semibold transition-all group relative overflow-hidden"
                   :class="{ 'opacity-50 pointer-events-none': sending }">
                    <span x-show="!sending" class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Enviar Respaldo a Gmail
                    </span>
                    <span x-show="sending" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-blue-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Enviando...
                    </span>
                    <span x-show="!sending" class="text-[10px] font-black uppercase tracking-tighter bg-blue-600 text-white px-2 py-0.5 rounded-md">Recomendado</span>
                </a>

                <a href="{{ route('superadmin.backup.excel') }}"
                   class="flex items-center justify-between w-full px-4 py-3 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-xl text-sm font-semibold transition-colors">
                    <span>Backup Excel (Todas las tablas)</span>
                    <span class="text-xs text-slate-400">.xlsx</span>
                </a>
            </div>
        </div>

        {{-- Restaurar (Restorage) --}}
        <div class="bg-amber-50 rounded-2xl border border-amber-200 shadow-sm p-6 overflow-hidden relative">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-200/50 rounded-full blur-xl"></div>
            
            <div class="flex items-center gap-3 mb-4 relative z-10">
                <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center border border-amber-100 text-amber-600 shadow-sm">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-800">Restorage / Restaurar</h2>
                    <p class="text-xs text-slate-500">Recupera una copia de seguridad SQL</p>
                </div>
            </div>
            <form action="{{ route('superadmin.restore.sql') }}" method="POST" enctype="multipart/form-data" 
                  onsubmit="return confirm('ATENCION PELIGRO: Esto borrará la base de datos actual completa y la reemplazará con el archivo que estás subiendo. Todos los cambios recientes desde tu backup se perderán. ¿Estás ABSOLUTAMENTE seguro de continuar?');" class="relative z-10">
                @csrf
                <div class="border-2 border-dashed border-amber-300 bg-white rounded-xl p-4 text-center mb-3 transition-colors">
                    <input type="file" name="file" accept=".sql" required
                           class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200 cursor-pointer">
                </div>
                <p class="text-[11px] text-amber-700 mb-3 font-semibold text-center uppercase tracking-wider">
                    ⚠️ Acción altamente destructiva
                </p>
                <button type="submit"
                        class="w-full py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-amber-600/20 transition-all hover:scale-[1.02]">
                    Restaurar Base de Datos
                </button>
            </form>
        </div>

    </div>

</div>
@endsection
