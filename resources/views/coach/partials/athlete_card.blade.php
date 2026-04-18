<div onclick="toggleAthlete({{ $atleta->id }}, this)" 
     class="group bg-white rounded-2xl border-2 border-slate-100 p-4 transition-all duration-300 cursor-pointer hover:shadow-lg relative overflow-hidden flex flex-col items-center text-center">
    
    {{-- Checkbox Indicator --}}
    <div class="absolute top-3 right-3 w-5 h-5 rounded-full border-2 border-slate-200 transition-all flex items-center justify-center group-[.border-blue-500]:bg-blue-600 group-[.border-blue-500]:border-blue-600">
        <svg class="h-3 w-3 text-white opacity-0 group-[.border-blue-500]:opacity-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" />
        </svg>
    </div>

    {{-- Imagen --}}
    <div class="relative w-20 h-20 mb-3">
        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-[#0b2d69] to-[#c61c2c] flex items-center justify-center text-white font-black text-2xl flex-shrink-0 overflow-hidden shadow-md">
            @if($atleta->foto)
                <img src="{{ str_starts_with($atleta->foto, 'http') ? $atleta->foto : asset('storage/' . $atleta->foto) }}" 
                     class="w-full h-full object-cover" loading="lazy">
            @else
                {{ strtoupper(substr($atleta->nombre,0,1).substr($atleta->apellido_paterno??'',0,1)) }}
            @endif
        </div>
        
        {{-- Badge de Habilitado/Inhabilitado prominente --}}
        <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-lg border-2 border-white shadow-sm {{ $atleta->habilitado_booleano ? 'bg-emerald-500' : 'bg-red-500' }}" 
             title="{{ $atleta->habilitado_booleano ? 'Habilitado' : 'Inhabilitado' }}">
        </div>
    </div>

    {{-- Info Core --}}
    <h4 class="text-sm font-black text-slate-800 leading-tight mb-1">
        {{ $atleta->nombre }} {{ $atleta->apellido_paterno }}
    </h4>
    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-tighter mb-3">
        CI: {{ $atleta->ci }}
    </p>

    {{-- Metadata --}}
    <div class="flex flex-wrap justify-center gap-1.5 mb-4">
        @if($atleta->fecha_nacimiento)
            <span class="text-[10px] font-bold bg-slate-100 text-slate-500 px-2 py-0.5 rounded-md">{{ \Carbon\Carbon::parse($atleta->fecha_nacimiento)->age }} años</span>
        @endif
        <span class="text-[10px] font-bold bg-slate-100 text-slate-500 px-2 py-0.5 rounded-md uppercase">{{ $atleta->genero ?? 'N/A' }}</span>
        
        @if(isset($atleta->pagado_mes_actual))
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md uppercase border {{ $atleta->pagado_mes_actual ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-100' }}">
                {{ $atleta->pagado_mes_actual ? 'Al Día' : 'Debe' }}
            </span>
        @endif
    </div>

    <div class="mt-auto pt-2 w-full border-t border-slate-50">
        <a href="{{ route('athletes.show', $atleta) }}" onclick="event.stopPropagation()" 
           class="text-[11px] font-black text-blue-600 hover:text-red-500 uppercase tracking-widest transition-colors flex items-center justify-center gap-1">
            Ver Perfil Completo
            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
</div>
