<header class="h-16 flex items-center justify-between px-4 sm:px-6 flex-shrink-0 shadow-md transition-all duration-300 z-40" style="background-color: #c61c2c;">
    <div class="flex items-center">
        {{-- Mobile Menu Button --}}
        <button @click="sidebarOpen = !sidebarOpen" class="mr-4 text-white/80 hover:text-white md:hidden cursor-pointer p-1 rounded-lg hover:bg-white/10 transition-colors">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path x-show="!sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path x-show="sidebarOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        
        <h1 class="text-lg sm:text-xl font-bold text-white tracking-wide truncate max-w-[200px] sm:max-w-none">
            @yield('title', 'Dashboard')
        </h1>
    </div>
    
    <div class="flex items-center space-x-4">
        <!-- Date Display -->
        <span class="text-sm text-white/90 font-medium hidden md:inline-block">
            {{ now()->isoFormat('LL') }}
        </span>
        
        <!-- Notifications (Static for now) -->
        <button class="p-2 text-white/80 hover:text-white transition-colors hover:bg-white/10 rounded-full">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
        </button>
    </div>
</header>
