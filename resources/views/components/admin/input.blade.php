@props(['disabled' => false, 'label' => '', 'name' => '', 'type' => 'text', 'value' => '', 'placeholder' => ''])

<div>
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-semibold text-slate-700 mb-1">
            {{ $label }}
        </label>
    @endif
    <input 
        type="{{ $type }}" 
        name="{{ $name }}" 
        id="{{ $name }}" 
        value="{{ old($name, $value) }}"
        {{ $disabled ? 'disabled' : '' }}
        {!! $attributes->merge(['class' => 'appearance-none block w-full px-4 py-2.5 border border-slate-200 rounded-lg shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all sm:text-sm bg-white disabled:bg-slate-50 disabled:text-slate-500']) !!}
        placeholder="{{ $placeholder }}"
    >
    @error($name)
        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
    @enderror
</div>
