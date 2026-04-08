@props(['disabled' => false, 'label' => '', 'name' => '', 'type' => 'text', 'value' => '', 'placeholder' => ''])

<div>
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-bold text-slate-800 mb-1.5 tracking-tight">
            {{ $label }}
        </label>
    @endif
    <div class="relative group">
        <input 
            type="{{ $type }}" 
            name="{{ $name }}" 
            id="{{ $name }}" 
            value="{{ old($name, $value) }}"
            {{ $disabled ? 'disabled' : '' }}
            {!! $attributes->merge(['class' => 'appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-all sm:text-sm bg-white disabled:bg-slate-50 disabled:text-slate-500 group-hover:border-slate-300']) !!}
            placeholder="{{ $placeholder }}"
        >
    </div>
    @error($name)
        <p class="mt-1.5 text-xs text-red-600 font-semibold flex items-center">
            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
            {{ $message }}
        </p>
    @enderror
</div>
