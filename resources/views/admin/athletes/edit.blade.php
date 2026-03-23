@extends('layouts.admin')

@section('title', 'Editar Atleta')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-sm border border-slate-100 rounded-2xl overflow-hidden p-8">
        <form action="{{ route('athletes.update', $athlete) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')
            
            @include('admin.athletes.form')

            <div class="flex items-center justify-end space-x-4 pt-8 border-t border-slate-100">
                <x-admin.button type="button" variant="secondary" onclick="window.location.href='{{ route('athletes.index') }}'">
                    Cancelar
                </x-admin.button>
                <x-admin.button type="submit">
                    Guardar Cambios
                </x-admin.button>
            </div>
        </form>
    </div>
</div>
@endsection
