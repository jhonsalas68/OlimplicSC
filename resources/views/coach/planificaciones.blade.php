@extends('layouts.admin')

@section('title', 'Mis Planificaciones')

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-black text-slate-800 tracking-tight">Mis Planificaciones</h1>
        <div class="mt-1 flex items-center gap-2">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Categorías:</span>
            @foreach($myCategories as $cat)
                <span class="text-[9px] font-black px-2 py-0.5 bg-blue-50 text-blue-600 rounded border border-blue-100 uppercase">{{ $cat->nombre }}</span>
            @endforeach
        </div>
    </div>
    <button @click="window.location.href='{{ route('trainings.create') }}'"
       class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black uppercase tracking-widest rounded-2xl transition-all shadow-xl shadow-blue-100 group">
        <svg class="h-5 w-5 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Nueva Planificación
    </button>
</div>

@if(session('success'))
    <div class="mb-8 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl text-emerald-700 text-sm font-bold flex items-center shadow-sm animate-in fade-in slide-in-from-top-4">
        <svg class="h-5 w-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        {{ session('success') }}
    </div>
@endif

<div class="space-y-4">
    @forelse($planificacionesPropias as $plan)
        <div class="group bg-white rounded-3xl border border-slate-100 shadow-sm p-5 flex items-center gap-5 relative overflow-hidden transition-all hover:shadow-xl hover:shadow-blue-500/5">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-blue-600"></div>
            
            <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center flex-shrink-0 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>

            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[10px] font-black px-2 py-0.5 bg-slate-100 text-slate-500 rounded uppercase tracking-tighter">{{ $plan->category->nombre ?? 'S/C' }}</span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $plan->fecha ? $plan->fecha->translatedFormat('d M, Y') : 'Sin fecha' }}</span>
                </div>
                <h3 class="text-base font-black text-slate-800 truncate">Planificación de Entrenamiento</h3>
                <p class="text-xs text-slate-400 mt-0.5 italic">Documento registrado por {{ explode(' ', $plan->coach->name ?? 'Admin')[0] }}</p>
            </div>

            <div class="flex items-center gap-3 flex-shrink-0">
                @if($plan->file_path_pdf)
                    <a href="{{ str_starts_with($plan->file_path_pdf, 'http') ? $plan->file_path_pdf : asset('storage/' . $plan->file_path_pdf) }}" 
                       target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-blue-600 transition-all shadow-lg shadow-slate-200">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        PDF
                    </a>
                @endif
                
                <div class="flex items-center gap-1">
                    <a href="{{ route('trainings.edit', $plan) }}" 
                       class="w-10 h-10 flex items-center justify-center bg-slate-50 text-slate-400 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    </a>
                    
                    <form action="{{ route('trainings.destroy', $plan) }}" method="POST" onsubmit="return confirm('¿Eliminar esta planificación?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-10 h-10 flex items-center justify-center bg-slate-50 text-slate-400 rounded-xl hover:bg-red-50 hover:text-red-600 transition-all">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1v3M4 7h16" /></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-16 text-center">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800">No hay planificaciones</h3>
            <p class="text-slate-400 text-sm mt-1">Aún no has registrado planificaciones para tus categorías.</p>
        </div>
    @endforelse
</div>
@endsection
