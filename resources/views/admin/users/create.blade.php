@extends('layouts.admin')

@section('title', 'Nuevo Usuario')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-sm border border-slate-100 rounded-2xl overflow-hidden p-8">
        <form action="{{ route('users.store') }}" method="POST" class="space-y-8" enctype="multipart/form-data">
            @csrf
            @include('admin.users.form')

            <div class="flex items-center justify-end space-x-4 pt-8 border-t border-slate-100">
                <x-admin.button type="button" variant="secondary" onclick="window.location.href='{{ route('users.index') }}'">
                    Cancelar
                </x-admin.button>
                <x-admin.button type="submit">
                    Crear Usuario
                </x-admin.button>
            </div>
        </form>
    </div>
</div>
@endsection
