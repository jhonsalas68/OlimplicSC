@extends('layouts.admin')

@section('title', isset($training) ? 'Editar Planificación' : 'Nueva Planificación')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-sm border border-slate-100 rounded-2xl overflow-hidden p-8">
        <form action="{{ isset($training) ? route('trainings.update', $training) : route('trainings.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @if(isset($training)) @method('PUT') @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Categoría</label>
                    <select name="category_id" required class="block w-full px-4 py-2.5 border border-slate-200 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all sm:text-sm bg-white">
                        <option value="">Seleccionar categoría...</option>
                        @foreach($categories as $category)
                            @php
                                $user = auth()->user();
                                $isCoach = $user->hasRole('Coach');
                                $shouldShow = !$isCoach || ($user->category_id == $category->id);
                            @endphp
                            @if($shouldShow)
                                <option value="{{ $category->id }}" {{ (old('category_id', $training->category_id ?? ($isCoach ? $user->category_id : '')) == $category->id) ? 'selected' : '' }}>
                                    {{ $category->nombre }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    @error('category_id') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <x-admin.input label="Fecha" name="fecha" type="date" :value="isset($training) ? $training->fecha->format('Y-m-d') : date('Y-m-d')" required />
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Documento de Planificación (PDF)</label>
                    <div class="flex items-center justify-center w-full">
                        <label for="pdf" class="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-200 border-dashed rounded-xl cursor-pointer bg-slate-50 hover:bg-slate-100 transition-colors">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <p class="mb-2 text-sm text-slate-500 font-medium" id="pdf-filename">Haga clic o arrastre el archivo PDF aquí</p>
                                <p class="text-xs text-slate-400">Sólo PDF (Máx. 5MB)</p>
                            </div>
                            <input id="pdf" name="pdf" type="file" class="hidden" accept="application/pdf" onchange="document.getElementById('pdf-filename').textContent = this.files.length ? this.files[0].name : 'Haga clic o arrastre el archivo PDF aquí';" />
                        </label>
                    </div>
                    @if(isset($training) && $training->file_path_pdf)
                        <div class="mt-2 flex items-center text-xs text-emerald-600 font-bold">
                            <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                            Archivo actual: {{ basename($training->file_path_pdf) }}
                        </div>
                    @endif
                    @error('pdf') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-8 border-t border-slate-100">
                <x-admin.button type="button" variant="secondary" onclick="window.location.href='{{ auth()->user()->hasRole('Coach') ? route('coach.planificaciones') : route('trainings.index') }}'">
                    Cancelar
                </x-admin.button>
                <x-admin.button type="submit">
                    {{ isset($training) ? 'Actualizar Plan' : 'Publicar Plan' }}
                </x-admin.button>
            </div>
        </form>
    </div>
</div>
@endsection
