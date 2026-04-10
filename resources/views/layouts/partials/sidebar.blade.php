<!-- Overlay for mobile when sidebar is open -->
<div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/50 z-40 md:hidden" @click="sidebarOpen = false"></div>

<aside 
    id="admin-sidebar"
    data-turbo-permanent
    class="sidebar-gradient flex-shrink-0 flex flex-col transition-all duration-300 overflow-hidden group/sidebar absolute md:relative z-50 h-full bg-[#0b2d69] shadow-2xl"
    :class="{ 
        'w-64 left-0 sidebar-is-open': sidebarOpen, 
        '-left-64 md:left-0 w-16 md:hover:w-64': !sidebarOpen 
    }"
    @click.away="sidebarOpen = false"
>

    {{-- Header del logo --}}
    <div class="h-20 flex-shrink-0 bg-white relative transition-all duration-300 overflow-hidden shadow-inner">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-900/10 to-transparent z-10"></div>
        <div class="absolute left-0 top-0 h-full w-[240px]" 
             style="background-image: url('{{ asset('images/banner-login.jpg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
        </div>
    </div>

    <div class="flex-1 flex flex-col overflow-y-auto mt-6 overflow-x-hidden px-3">
        <nav class="space-y-1.5">

            @if(auth()->user()->hasRole('Coach'))
            {{-- ===== SIDEBAR COACH ===== --}}
            <h3 class="px-4 text-[10px] font-black text-blue-300 uppercase tracking-[0.2em] mb-3 opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300 whitespace-nowrap">
                Estrategia
            </h3>

            <x-sidebar-link href="{{ route('coach.dashboard') }}" icon="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" label="Inicio" :active="request()->routeIs('coach.dashboard')" />

            <x-sidebar-link href="{{ route('coach.planificaciones') }}" icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" label="Planificaciones" :active="request()->routeIs('coach.planificaciones')" />

            <x-sidebar-link href="{{ route('coach.atletas') }}" icon="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" :label="auth()->user()->category?->nombre ?? 'Mi Categoría'" :active="request()->routeIs('coach.atletas')" />

            @else
            {{-- ===== SIDEBAR ADMIN ===== --}}
            <h3 class="px-4 text-[10px] font-black text-blue-300 uppercase tracking-[0.2em] mb-3 opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300 whitespace-nowrap">
                Administración
            </h3>

            <x-sidebar-link href="{{ route('admin.dashboard') }}" icon="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" label="Dashboard" :active="request()->routeIs('admin.dashboard')" />

            <x-sidebar-link href="{{ route('athletes.index') }}" icon="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" label="Atletas Olimpic" :active="request()->routeIs('athletes.*')" />

            <x-sidebar-link href="{{ route('users.index') }}" icon="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" label="Usuarios y Roles" :active="request()->routeIs('users.*')" />

            <div class="pt-6">
                <h3 class="px-4 text-[10px] font-black text-blue-300 uppercase tracking-[0.2em] mb-3 opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300 whitespace-nowrap">
                    Finanzas & Operación
                </h3>

                <x-sidebar-link href="{{ route('cobros.index') }}" icon="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" label="Cobros" :active="request()->routeIs('cobros.*')" />

                <x-sidebar-link href="{{ route('payments.index') }}" icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" label="Historial" :active="request()->routeIs('payments.*')" />

                <x-sidebar-link href="{{ route('trainings.index') }}" icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" label="Planificaciones" :active="request()->routeIs('trainings.*')" />

                <x-sidebar-link href="{{ route('admin.reports.index') }}" icon="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" label="Reportes" :active="request()->routeIs('admin.reports.index')" />
            </div>

            @if(auth()->user()->hasRole('SuperAdmin'))
            <div class="pt-6">
                <x-sidebar-link href="{{ route('superadmin.index') }}" icon="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" label="Mantenimiento" :active="request()->routeIs('superadmin.*')" />

                <x-sidebar-link href="{{ route('admin.activity-logs.index') }}" icon="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" label="Bitácora" :active="request()->routeIs('admin.activity-logs.*')" />
            </div>
            @endif
            @endif

        </nav>
    </div>

    <!-- User Profile Premium -->
    <div class="flex-shrink-0 p-4 border-t border-white/5 bg-black/10 backdrop-blur-sm">
        <div class="flex items-center w-full">
            <div class="relative">
                <img class="flex-shrink-0 h-11 w-11 rounded-xl border-2 border-white/10 object-cover shadow-lg"
                     src="{{ auth()->user()->avatar_url }}" alt="Avatar">
                <span class="absolute -bottom-1 -right-1 h-4 w-4 bg-emerald-500 border-2 border-[#0b2d69] rounded-full"></span>
            </div>
            <div class="ml-3 min-w-0 opacity-0 group-hover/sidebar:opacity-100 transition-all duration-300 flex-1">
                <p class="text-sm font-bold text-white truncate leading-none mb-1">{{ auth()->user()->name ?? 'Usuario' }}</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-[10px] uppercase tracking-widest font-black text-red-400 hover:text-red-300 transition-colors cursor-pointer flex items-center">
                        Cerrar Sesión
                        <svg class="w-3 h-3 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
