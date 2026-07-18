@extends('layouts.admin')

@section('title', 'Vista General')

@section('content')
<!-- Header Section -->
<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-black text-slate-800 tracking-tight">¡Hola, {{ auth()->user()->name }}!</h1>
        <p class="text-sm text-slate-500 mt-1">Aquí tienes el resumen del estado actual de tu club deportivo.</p>
    </div>
    <div class="text-xs text-slate-400 font-bold bg-white px-4 py-2 rounded-2xl border border-slate-100 shadow-sm self-start md:self-auto flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
        Actualizado: Hoy, {{ now()->format('H:i') }}
    </div>
</div>

<!-- Stat Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <!-- Total Atletas -->
    <div class="bg-gradient-to-br from-white to-slate-50/50 p-6 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/30 flex items-center justify-between group hover:border-blue-200 transition-all duration-300">
        <div>
            <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Atletas</span>
            <span class="block text-3xl font-black text-slate-800 mt-2 tracking-tight">{{ $totalAtletas }}</span>
            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full mt-2">
                Atletas inscritos
            </span>
        </div>
        <div class="p-4 bg-blue-50 text-blue-600 rounded-3xl group-hover:scale-110 transition-transform duration-300">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </div>
    </div>

    <!-- Recaudación Mes -->
    <div class="bg-gradient-to-br from-white to-slate-50/50 p-6 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/30 flex items-center justify-between group hover:border-emerald-200 transition-all duration-300">
        <div>
            <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Recaudación ({{ now()->translatedFormat('F') }})</span>
            <span class="block text-2xl font-black text-slate-800 mt-2 tracking-tight">Bs. {{ number_format($recaudacionMes, 2) }}</span>
            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full mt-2">
                Recabado este mes
            </span>
        </div>
        <div class="p-4 bg-emerald-50 text-emerald-600 rounded-3xl group-hover:scale-110 transition-transform duration-300">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
    </div>

    <!-- Entrenamientos -->
    <div class="bg-gradient-to-br from-white to-slate-50/50 p-6 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/30 flex items-center justify-between group hover:border-indigo-200 transition-all duration-300">
        <div>
            <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Entrenamientos</span>
            <span class="block text-3xl font-black text-slate-800 mt-2 tracking-tight">{{ $totalEntrenamientos }}</span>
            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full mt-2">
                Planificaciones
            </span>
        </div>
        <div class="p-4 bg-indigo-50 text-indigo-600 rounded-3xl group-hover:scale-110 transition-transform duration-300">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>
    </div>

    <!-- Usuarios Inactivos -->
    <div class="bg-gradient-to-br from-white to-slate-50/50 p-6 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/30 flex items-center justify-between group hover:border-amber-200 transition-all duration-300">
        <div>
            <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Usuarios Inactivos</span>
            <span class="block text-3xl font-black text-slate-800 mt-2 tracking-tight">{{ $usuariosInactivos }}</span>
            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full mt-2">
                Requieren revisión
            </span>
        </div>
        <div class="p-4 bg-amber-50 text-amber-600 rounded-3xl group-hover:scale-110 transition-transform duration-300">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
    </div>
</div>

<!-- Charts Section Row 1 -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    
    <!-- Tendencia de Recaudación (Línea/Área) -->
    <div class="lg:col-span-2 bg-white p-6 sm:p-8 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/40 relative overflow-hidden">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Flujo de Caja Histórico</h3>
                <p class="text-xs text-slate-400 mt-1">Recaudación mensual total de los últimos 6 meses</p>
            </div>
            <span class="text-[10px] font-black uppercase text-blue-600 bg-blue-50 px-3 py-1 rounded-xl">Moneda: Bs.</span>
        </div>
        <div class="h-[300px] w-full">
            <canvas id="recaudacionChart"></canvas>
        </div>
    </div>

    <!-- Estatus Administrativo (Dona) -->
    <div class="bg-white p-6 sm:p-8 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/40 relative overflow-hidden flex flex-col justify-between">
        <div class="mb-4">
            <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Estatus de Mensualidades</h3>
            <p class="text-xs text-slate-400 mt-1">Proporción de alumnos Al Día vs Deudores (Debe)</p>
        </div>
        <div class="h-[220px] w-full relative flex items-center justify-center">
            <canvas id="mensualidadChart"></canvas>
            @if($atletasAlDia == 0 && $atletasDeudores == 0)
                <div class="absolute inset-0 flex items-center justify-center text-slate-300 text-xs italic bg-white/90">Sin datos de atletas</div>
            @endif
        </div>
        <div class="flex justify-around items-center mt-4 pt-4 border-t border-slate-50 text-center font-bold">
            <div>
                <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 mr-1.5"></span>
                <span class="text-xs text-slate-500">Al Día: <span class="text-slate-800 font-extrabold">{{ $atletasAlDia }}</span></span>
            </div>
            <div>
                <span class="inline-block w-2.5 h-2.5 rounded-full bg-rose-500 mr-1.5"></span>
                <span class="text-xs text-slate-500">Debe: <span class="text-slate-800 font-extrabold">{{ $atletasDeudores }}</span></span>
            </div>
        </div>
    </div>

</div>

<!-- Row 2 -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    
    <!-- Atletas por Categorías (Barras) -->
    <div class="bg-white p-6 sm:p-8 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/40 relative overflow-hidden">
        <div class="mb-6">
            <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Atletas por Categorías</h3>
            <p class="text-xs text-slate-400 mt-1">Cantidad de deportistas registrados en cada categoría del club</p>
        </div>
        <div class="h-[280px] w-full">
            <canvas id="categoriasChart"></canvas>
            @if(empty($categoriasCounts) || array_sum($categoriasCounts) == 0)
                <div class="absolute inset-0 flex items-center justify-center text-slate-300 text-xs italic bg-white/90">Registra categorías y atletas para graficar</div>
            @endif
        </div>
    </div>

    <!-- Actividad Reciente (Pagos Reales) -->
    <div class="bg-white p-6 sm:p-8 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/40 relative overflow-hidden flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Últimas Transacciones</h3>
                    <p class="text-xs text-slate-400 mt-1">Últimos cobros registrados y aprobados en el sistema</p>
                </div>
                <a href="{{ route('payments.index') }}" class="text-[10px] font-black uppercase text-blue-600 hover:text-blue-800 transition-colors">Ver Todo</a>
            </div>
            
            <div class="space-y-4">
                @forelse($ultimosPagos as $pago)
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-2xl border border-slate-100 hover:border-blue-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <!-- Avatar circular de iniciales -->
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                                {{ strtoupper(substr($pago->athlete->nombre ?? 'P', 0, 1) . substr($pago->athlete->apellido_paterno ?? '', 0, 1)) }}
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-800">{{ $pago->athlete->nombre ?? 'N/A' }} {{ $pago->athlete->apellido_paterno ?? '' }}</h4>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider mt-0.5">
                                    {{ $pago->concepto }} 
                                    @if($pago->mes_correspondiente)
                                        <span class="text-[8px] font-normal lowercase">({{ $pago->mes_correspondiente }})</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="block text-xs font-black text-slate-800">Bs. {{ number_format($pago->monto, 2) }}</span>
                            <span class="block text-[8px] text-slate-400 mt-0.5">{{ $pago->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-12 text-slate-300">
                        <svg class="w-12 h-12 mb-2 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-xs italic">Aún no se han registrado cobros.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Chart: Recaudación Histórica (Líneas con Área)
    const ctxRecaudacion = document.getElementById('recaudacionChart');
    if (ctxRecaudacion) {
        const months = @json($meses);
        const amounts = @json($recaudaciones);
        
        // Crear gradiente para el relleno
        const canvasCtx = ctxRecaudacion.getContext('2d');
        const gradient = canvasCtx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.4)');
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

        new Chart(ctxRecaudacion, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Ingresos Mensuales',
                    data: amounts,
                    borderColor: '#2563eb',
                    borderWidth: 3.5,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#2563eb',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        padding: 12,
                        backgroundColor: '#0f172a',
                        titleFont: { size: 11, weight: 'bold' },
                        bodyFont: { size: 12 },
                        callbacks: {
                            label: function(context) {
                                return ' Recibido: Bs. ' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5], color: '#f1f5f9' },
                        ticks: {
                            color: '#64748b',
                            font: { size: 10, weight: '500' },
                            callback: function(val) { return 'Bs. ' + val; }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { size: 10, weight: '500' } }
                    }
                }
            }
        });
    }

    // 2. Chart: Mensualidad Al Día vs Debe (Dona)
    const ctxMensualidad = document.getElementById('mensualidadChart');
    if (ctxMensualidad) {
        new Chart(ctxMensualidad, {
            type: 'doughnut',
            data: {
                labels: ['Al Día', 'Debe'],
                datasets: [{
                    data: [{{ $atletasAlDia }}, {{ $atletasDeudores }}],
                    backgroundColor: ['#10b981', '#f43f5e'],
                    borderWidth: 4,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        padding: 10,
                        backgroundColor: '#0f172a',
                        callbacks: {
                            label: function(context) {
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let val = context.raw;
                                let pct = total > 0 ? ((val / total) * 100).toFixed(0) : 0;
                                return ` Atletas: ${val} (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // 3. Chart: Atletas por Categoría (Barras)
    const ctxCategorias = document.getElementById('categoriasChart');
    if (ctxCategorias) {
        const catLabels = @json($categoriasLabels);
        const catCounts = @json($categoriasCounts);

        new Chart(ctxCategorias, {
            type: 'bar',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catCounts,
                    backgroundColor: '#4f46e5',
                    borderRadius: 12,
                    borderSkipped: false,
                    barThickness: 24
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        padding: 10,
                        backgroundColor: '#0f172a',
                        callbacks: {
                            label: function(context) {
                                return ` Atletas: ${context.parsed.y}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5], color: '#f1f5f9' },
                        ticks: {
                            color: '#64748b',
                            font: { size: 10, weight: '500' },
                            stepSize: 1
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { size: 10, weight: '500' } }
                    }
                }
            }
        });
    }
});
</script>
@endpush
