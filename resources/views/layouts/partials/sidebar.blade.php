<aside class="w-64 sidebar-gradient flex-shrink-0 flex flex-col transition-all duration-300">
    <div class="flex items-center h-16 flex-shrink-0 px-4 bg-black/10">
        <img src="{{ asset('images/logo-olimpicsc-full.png') }}" alt="OlympicSC" class="h-12 w-auto object-contain">
    </div>

    <div class="flex-1 flex flex-col overflow-y-auto mt-4">
        <nav class="px-4 space-y-1">

            @if(auth()->user()->hasRole('Coach'))
            {{-- ===== SIDEBAR COACH ===== --}}
            <h3 class="px-3 text-xs font-semibold text-blue-200 uppercase tracking-wider mb-2">
                Mi Panel
            </h3>

            <a href="{{ route('coach.dashboard') }}"
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.dashboard') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/5 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Inicio
            </a>

            <a href="{{ route('coach.planificaciones') }}"
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.planificaciones') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/5 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                Planificaciones
            </a>

            <a href="{{ route('coach.atletas') }}"
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.atletas') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/5 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                {{ auth()->user()->category->nombre ?? 'Mi Categoría' }}
            </a>

            @else
            {{-- ===== SIDEBAR ADMIN ===== --}}
            <h3 class="px-3 text-xs font-semibold text-blue-200 uppercase tracking-wider mb-2">
                Gestión Administrativa
            </h3>

            <a href="{{ route('admin.dashboard') }}"
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/5 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>

            <a href="{{ route('athletes.index') }}"
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('athletes.*') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/5 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Atletas Olímpicos
            </a>

            <a href="{{ route('users.index') }}"
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('users.*') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/5 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Usuarios y Roles
            </a>

            <div class="pt-4">
                <h3 class="px-3 text-xs font-semibold text-blue-200 uppercase tracking-wider mb-2">
                    Cancha y Tesorería
                </h3>

                <a href="{{ route('cobros.index') }}"
                   class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('cobros.*') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/5 hover:text-white' }}">
                    <svg class="mr-3 h-5 w-5 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Cobros
                </a>

                <a href="{{ route('payments.index') }}"
                   class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('payments.*') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/5 hover:text-white' }}">
                    <svg class="mr-3 h-5 w-5 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    Historial de Pagos
                </a>

                <a href="{{ route('trainings.index') }}"
                   class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('trainings.*') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/5 hover:text-white' }}">
                    <svg class="mr-3 h-5 w-5 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    Planificaciones
                </a>
            </div>

            @if(auth()->user()->hasRole('SuperAdmin'))
            <div class="pt-4">
                <h3 class="px-3 text-xs font-semibold text-blue-200 uppercase tracking-wider mb-2">
                    Super Admin
                </h3>
                <a href="{{ route('superadmin.index') }}"
                   class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('superadmin.*') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/5 hover:text-white' }}">
                    <svg class="mr-3 h-5 w-5 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                    </svg>
                    Exportar / Backup
                </a>
            </div>
            @endif
            @endif

        </nav>
    </div>

    <!-- User Profile Mini -->
    <div class="flex-shrink-0 flex bg-black/20 p-4">
        <div class="flex items-center">
            <img class="inline-block h-9 w-9 rounded-md object-cover"
                 src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&color=7F9CF5&background=EBF4FF" alt="">
            <div class="ml-3">
                <p class="text-sm font-medium text-white">{{ auth()->user()->name ?? 'Usuario' }}</p>
                @if(auth()->user()->hasRole('Coach') && auth()->user()->category)
                    <p class="text-xs text-blue-300">{{ auth()->user()->category->nombre }}</p>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs font-medium text-blue-200 hover:text-white cursor-pointer">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
