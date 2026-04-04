@extends('layouts.admin')

@section('title', 'Mi Perfil')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-sm border border-slate-100 rounded-2xl overflow-hidden p-8">
        @if(session('status'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-xl text-emerald-700 text-sm font-medium flex items-center">
                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('status') }}
            </div>
        @endif
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                <div class="md:col-span-2 flex items-center space-x-6 mb-4">
                    <div class="shrink-0">
                        <img id="preview-avatar" class="h-20 w-20 object-cover rounded-full border-2 border-slate-100 shadow-sm" src="{{ $user->avatar_url }}" alt="Avatar actual">
                    </div>
                    <label class="block">
                        <span class="block text-sm font-semibold text-slate-700 mb-1">Cambiar Avatar</span>
                        <input type="file" name="avatar" accept="image/*" onchange="handleAvatarChange(event, 'preview-avatar')" class="block w-full text-sm text-slate-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-full file:border-0
                            file:text-sm file:font-semibold
                            file:bg-blue-50 file:text-blue-700
                            hover:file:bg-blue-100
                        "/>
                        <p class="mt-1 text-xs text-slate-500">Recomendado: 200x200px. Máx 5MB.</p>
                        @error('avatar')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </label>
                </div>

                <x-admin.input label="Nombre completo" name="name" :value="$user->name" required />
                
                <div class="md:col-span-2 bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <p class="text-sm text-slate-600">
                        <span class="font-bold">Username:</span> {{ $user->username }} <br>
                        <span class="font-bold">Email:</span> {{ $user->email }}
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-8 border-t border-slate-100">
                <x-admin.button type="submit">
                    Actualizar Perfil
                </x-admin.button>
            </div>
        </form>
    </div>
</div>
<script>
function handleAvatarChange(event, previewId) {
    const file = event.target.files[0];
    if (!file || !file.type.match(/image.*/)) return;
    
    const reader = new FileReader();
    reader.onload = function(e) {
        const img = new Image();
        img.onload = function() {
            const canvas = document.createElement('canvas');
            const MAX_WIDTH = 500;
            const MAX_HEIGHT = 500;
            let width = img.width;
            let height = img.height;

            if (width > height) {
                if (width > MAX_WIDTH) { height *= MAX_WIDTH / width; width = MAX_WIDTH; }
            } else {
                if (height > MAX_HEIGHT) { width *= MAX_HEIGHT / height; height = MAX_HEIGHT; }
            }
            canvas.width = width;
            canvas.height = height;
            canvas.getContext('2d').drawImage(img, 0, 0, width, height);
            
            canvas.toBlob((blob) => {
                if (!blob) return;
                const newFile = new File([blob], file.name, { type: file.type, lastModified: Date.now() });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(newFile);
                event.target.files = dataTransfer.files;
                
                const previewElement = document.getElementById(previewId);
                const objUrl = URL.createObjectURL(blob);
                if (previewElement && previewElement.tagName === 'IMG') {
                    previewElement.src = objUrl;
                }
            }, file.type === 'image/png' ? 'image/png' : 'image/jpeg', 0.85);
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}
</script>
@endsection
