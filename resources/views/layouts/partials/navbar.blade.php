<header class="glass-header h-16 flex items-center justify-between px-6 flex-shrink-0">
    <div class="flex items-center">
        <h1 class="text-xl font-semibold text-slate-800">
            @yield('title', 'Dashboard')
        </h1>
    </div>
    
    <div class="flex items-center space-x-4">
        <!-- Date Display -->
        <span class="text-sm text-slate-500 font-medium hidden md:inline-block">
            {{ now()->isoFormat('LL') }}
        </span>
        
        <!-- Notifications (Static for now) -->
        <button class="p-2 text-slate-400 hover:text-blue-600 transition-colors">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
        </button>
    </div>
</header>
