@extends('layouts.admin')

@section('title', isset($payment) ? 'Editar Pago' : 'Nuevo Pago')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-sm border border-slate-100 rounded-2xl overflow-hidden p-8">
        <form action="{{ isset($payment) ? route('payments.update', $payment) : route('payments.store') }}" method="POST" class="space-y-8">
            @csrf
            @if(isset($payment)) @method('PUT') @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Atleta</label>
                    <select name="athlete_id" required class="block w-full px-4 py-2.5 border border-slate-200 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all sm:text-sm bg-white">
                        <option value="">Seleccionar atleta...</option>
                        @foreach($athletes as $athlete)
                            <option value="{{ $athlete->id }}" {{ (old('athlete_id', $payment->athlete_id ?? '') == $athlete->id) ? 'selected' : '' }}>
                                {{ $athlete->nombre }} ({{ $athlete->ci }})
                            </option>
                        @endforeach
                    </select>
                    @error('athlete_id') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <x-admin.input label="Monto ($)" name="monto" type="number" step="0.01" :value="$payment->monto ?? ''" required placeholder="0.00" />
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Mes Correspondiente</label>
                    <select name="mes_correspondiente" required class="block w-full px-4 py-2.5 border border-slate-200 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all sm:text-sm bg-white">
                        @php
                            $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                            $anioActual = date('Y');
                        @endphp
                        <option value="">Seleccionar mes...</option>
                        @foreach($meses as $mes)
                            @php $val = "$mes $anioActual"; @endphp
                            <option value="{{ $val }}" {{ (old('mes_correspondiente', $payment->mes_correspondiente ?? '') == $val) ? 'selected' : '' }}>
                                {{ $val }}
                            </option>
                        @endforeach
                    </select>
                    @error('mes_correspondiente') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Estado del Pago</label>
                    <select name="estado_pago" required class="block w-full px-4 py-2.5 border border-slate-200 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all sm:text-sm bg-white">
                        <option value="pendiente" {{ (old('estado_pago', $payment->estado_pago ?? 'pendiente') == 'pendiente') ? 'selected' : '' }}>Pendiente</option>
                        <option value="pagado" {{ (old('estado_pago', $payment->estado_pago ?? '') == 'pagado') ? 'selected' : '' }}>Pagado</option>
                        <option value="vencido" {{ (old('estado_pago', $payment->estado_pago ?? '') == 'vencido') ? 'selected' : '' }}>Vencido</option>
                    </select>
                    @error('estado_pago') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-8 border-t border-slate-100">
                <x-admin.button type="button" variant="secondary" onclick="window.location.href='{{ route('payments.index') }}'">
                    Cancelar
                </x-admin.button>
                <x-admin.button type="submit">
                    {{ isset($payment) ? 'Actualizar Pago' : 'Registrar Pago' }}
                </x-admin.button>
            </div>
        </form>
    </div>
</div>
@endsection
