@extends('layouts.admin')

@section('title', 'Atletas de mi Categoria')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">
            {{ $category->nombre ?? 'Mi Categoria' }}
        </h1>
        <p class="text-sm text-slate-500 mt-0.5">
            @if($category)
                Atletas de {{ $category->edad_min }} a {{ $category->edad_max }} años &middot;
            @endif
            {{ $atletas->count() }} atleta(s) registrado(s)
        </p>
    </div>
</div>

@if($atletas->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
        <svg class="h-12 w-12 mx-auto text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <p class="text-slate-400 text-sm">No hay atletas registrados en esta categoria.</p>
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($atletas as $atleta)
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-start gap-4 hover:shadow-md transition-shadow">

            {{-- Avatar --}}
            <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-600 to-red-600 flex items-center justify-center text-white font-bold text-lg flex-shrink-0 overflow-hidden">
                @if($atleta->foto)
                    <img src="{{ Storage::url($atleta->foto) }}" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($atleta->nombre,0,1).substr($atleta->apellido_paterno??'',0,1)) }}
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2">
                    <p class="font-semibold text-slate-800 text-sm leading-tight truncate">
                        {{ $atleta->nombre }} {{ $atleta->apellido_paterno }} {{ $atleta->apellido_materno }}
                    </p>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold flex-shrink-0
                        {{ $atleta->habilitado_booleano ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $atleta->habilitado_booleano ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                        {{ $atleta->habilitado_booleano ? 'Habilitado' : 'Inhabilitado' }}
                    </span>
                </div>

                <p class="text-xs text-slate-500 mt-1">CI: {{ $atleta->ci }}</p>

                @if($atleta->id_alfanumerico_unico)
                    <span class="inline-block mt-1 text-xs font-mono text-blue-600 bg-blue-50 px-2 py-0.5 rounded">
                        {{ $atleta->id_alfanumerico_unico }}
                    </span>
                @endif

                <div class="mt-1.5 flex flex-wrap gap-2">
                    @if($atleta->fecha_nacimiento)
                        <span class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($atleta->fecha_nacimiento)->age }} años</span>
                    @endif
                    @if($atleta->genero)
                        <span class="text-xs text-slate-400">{{ $atleta->genero }}</span>
                    @endif
                </div>

                <a href="{{ route('athletes.show', $atleta) }}"
                   class="inline-flex items-center gap-1 mt-2 text-xs text-blue-600 hover:text-blue-800 font-semibold transition-colors">
                    Ver perfil
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

        </div>
        @endforeach
    </div>
@endif
@endsection
