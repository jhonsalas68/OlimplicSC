@props(['variant' => 'primary'])

@php
    $baseClasses = 'inline-flex items-center px-5 py-2.5 border rounded-xl shadow-sm text-sm font-bold transition-all transform hover:scale-[1.02] active:scale-[0.98] outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';
    $variants = [
        'primary' => 'border-transparent text-white bg-gradient-to-br from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:ring-blue-500 shadow-blue-200/50',
        'danger' => 'border-transparent text-white bg-gradient-to-br from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 focus:ring-red-500 shadow-red-200/50',
        'secondary' => 'border-slate-200 text-slate-700 bg-white hover:bg-slate-50 focus:ring-blue-500 hover:border-slate-300',
        'success' => 'border-transparent text-white bg-gradient-to-br from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 focus:ring-emerald-500 shadow-emerald-200/50',
    ];
    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

<button {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
