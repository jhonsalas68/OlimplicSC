@props(['variant' => 'primary'])

@php
    $baseClasses = 'inline-flex items-center px-4 py-2 border rounded-lg shadow-sm text-sm font-bold transition-all transform hover:scale-[1.02] active:scale-[0.98] outline-none focus:ring-2 focus:ring-offset-2';
    $variants = [
        'primary' => 'border-transparent text-white bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
        'danger' => 'border-transparent text-white bg-red-600 hover:bg-red-700 focus:ring-red-500',
        'secondary' => 'border-slate-300 text-slate-700 bg-white hover:bg-slate-50 focus:ring-blue-500',
        'success' => 'border-transparent text-white bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500',
    ];
    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

<button {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
