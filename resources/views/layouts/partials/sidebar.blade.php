<aside class="sidebar-gradient flex-shrink-0 flex flex-col transition-all duration-300 overflow-hidden group/sidebar w-16 hover:w-64 hover:shadow-2xl absolute md:relative z-50 h-full bg-[#0b2d69]">
    {{-- Header del logo --}}
    <div class="h-20 flex-shrink-0 bg-white shadow-sm border-b border-gray-100 relative transition-all duration-300">
        {{-- Contenedor interior fijo. El aside (overflow-hidden) recorta esto a 64px o lo muestra a 256px --}}
        <div class="absolute left-0 top-0 h-full w-[240px]" 
             style="background-image: url('{{ asset('images/banner-login.jpg') }}'); background-size: 520px auto; background-position: -20px 45%; background-repeat: no-repeat;">
        </div>
    </div>

    <div class="flex-1 flex flex-col overflow-y-auto mt-4 overflow-x-hidden">
        <nav class="px-2 space-y-2">

            @if(auth()->user()->hasRole('Coach'))
            {{-- ===== SIDEBAR COACH ===== --}}
            <h3 class="px-3 text-[10px] font-semibold text-blue-200 uppercase tracking-wider mb-2 opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300 whitespace-nowrap">
                Mi Panel
            </h3>

            <a href="{{ route('coach.dashboard') }}"
               class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('coach.dashboard') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="flex-shrink-0 h-5 w-5 {{ request()->routeIs('coach.dashboard') ? 'text-white' : 'text-blue-200' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="ml-4 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">Inicio</span>
            </a>

            <a href="{{ route('coach.planificaciones') }}"
               class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('coach.planificaciones') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="flex-shrink-0 h-5 w-5 {{ request()->routeIs('coach.planificaciones') ? 'text-white' : 'text-blue-200' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <span class="ml-4 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">Planificaciones</span>
            </a>

            <a href="{{ route('coach.atletas') }}"
               class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('coach.atletas') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="flex-shrink-0 h-5 w-5 {{ request()->routeIs('coach.atletas') ? 'text-white' : 'text-blue-200' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span class="ml-4 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">{{ auth()->user()->category->nombre ?? 'Mi Categoría' }}</span>
            </a>

            @else
            {{-- ===== SIDEBAR ADMIN ===== --}}
            <h3 class="px-3 text-[10px] font-semibold text-blue-200 uppercase tracking-wider mb-2 opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300 whitespace-nowrap">
                Gestión
            </h3>

            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-blue-200' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <title>Dashboard</title>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="ml-4 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">Dashboard</span>
            </a>

            <a href="{{ route('athletes.index') }}"
               class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('athletes.*') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="flex-shrink-0 h-5 w-5 {{ request()->routeIs('athletes.*') ? 'text-white' : 'text-blue-200' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <title>Atletas Olímpicos</title>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span class="ml-4 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">Atletas Olímpicos</span>
            </a>

            <a href="{{ route('users.index') }}"
               class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('users.*') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="flex-shrink-0 h-5 w-5 {{ request()->routeIs('users.*') ? 'text-white' : 'text-blue-200' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <title>Usuarios y Roles</title>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="ml-4 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">Usuarios y Roles</span>
            </a>

            <div class="pt-4">
                <h3 class="px-3 text-[10px] font-semibold text-blue-200 uppercase tracking-wider mb-2 opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300 whitespace-nowrap">
                    Cancha y Tesorería
                </h3>

                <a href="{{ route('cobros.index') }}"
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('cobros.*') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                    <svg class="flex-shrink-0 h-5 w-5 {{ request()->routeIs('cobros.*') ? 'text-white' : 'text-blue-200' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <title>Cobros</title>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span class="ml-4 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">Cobros</span>
                </a>

                <a href="{{ route('payments.index') }}"
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('payments.*') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                    <svg class="flex-shrink-0 h-5 w-5 {{ request()->routeIs('payments.*') ? 'text-white' : 'text-blue-200' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <title>Historial de Pagos</title>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="ml-4 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">Historial de Pagos</span>
                </a>

                <a href="{{ route('trainings.index') }}"
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('trainings.*') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                    <svg class="flex-shrink-0 h-5 w-5 {{ request()->routeIs('trainings.*') ? 'text-white' : 'text-blue-200' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <title>Planificaciones</title>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span class="ml-4 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">Planificaciones</span>
                </a>
            </div>

            @if(auth()->user()->hasRole('SuperAdmin'))
            <div class="pt-4">
                <a href="{{ route('superadmin.index') }}"
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('superadmin.*') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                    <svg class="flex-shrink-0 h-5 w-5 {{ request()->routeIs('superadmin.*') ? 'text-white' : 'text-blue-200' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <title>Exportar / Backup</title>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                    </svg>
                    <span class="ml-4 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">Exportar / Backup</span>
                </a>
            </div>
            @endif
            @endif

        </nav>
    </div>

    <!-- User Profile Mini -->
    <div class="flex-shrink-0 flex bg-black/20 p-3 pr-2 overflow-hidden transition-all duration-300 h-[72px]">
        <div class="flex items-center w-full">
            <img class="flex-shrink-0 h-10 w-10 rounded-full border-2 border-white/20 object-cover"
                 src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&color=0b2d69&background=EBF4FF" alt="">
            <div class="ml-3 min-w-0 opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300 flex-1">
                <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name ?? 'Usuario' }}</p>
                <form method="POST" action="{{ route('logout') }}" class="mt-0.5">
                    @csrf
                    <button type="submit" class="text-[10px] uppercase tracking-wider font-bold text-red-300 hover:text-red-400 cursor-pointer">
                        Salir del Sistema
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
