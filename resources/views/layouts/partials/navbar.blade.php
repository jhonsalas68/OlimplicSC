<header class="h-16 flex items-center justify-between px-4 sm:px-6 flex-shrink-0 shadow-md transition-all duration-300 z-40" style="background-color: #c61c2c;">
    <div class="flex items-center">
        {{-- Mobile Menu Button --}}
        <button @click.stop="sidebarOpen = !sidebarOpen" class="mr-4 text-white/80 hover:text-white md:hidden cursor-pointer p-1 rounded-lg hover:bg-white/10 transition-colors">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path x-show="!sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path x-show="sidebarOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        
        <h1 class="text-lg sm:text-xl font-bold text-white tracking-wide truncate max-w-[200px] sm:max-w-none">
            @yield('title', 'Dashboard')
        </h1>
    </div>
    
    <div class="flex items-center space-x-2 sm:space-x-4">
        <!-- Date Display -->
        <span class="text-[11px] sm:text-xs text-white/90 font-black uppercase tracking-widest hidden md:inline-block">
            {{ now()->isoFormat('LL') }}
        </span>
        
        <!-- Notifications (Interactive) -->
        <div class="relative" x-data="notificationHandler()" x-init="fetchNotifications()">
            <button @click="open = !open" 
                    id="notification-bell"
                    class="p-2 text-white/80 hover:text-white transition-all hover:bg-white/10 rounded-xl relative group">
                <svg class="h-6 w-6 group-hover:rotate-12 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                
                {{-- Contador de no leídas --}}
                <template x-if="count > 0">
                    <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-4 w-4 bg-amber-500 border-2 border-[#c61c2c] text-[9px] font-black text-white items-center justify-center" x-text="count">
                        </span>
                    </span>
                </template>
            </button>

            {{-- Dropdown de Notificaciones --}}
            <div x-show="open" 
                 x-cloak
                 id="notification-dropdown"
                 @click.away="open = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-[-10px]"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 class="absolute right-0 mt-3 w-72 sm:w-80 bg-white rounded-2xl shadow-2xl border border-slate-100 py-2 z-50 overflow-hidden">
                
                <div class="px-4 py-2 border-b border-slate-50 flex items-center justify-between">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">Avisos Recientes</h3>
                    <template x-if="count > 0">
                        <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">Nuevos</span>
                    </template>
                </div>

                <div class="max-h-96 overflow-y-auto">
                    {{-- Lista Vacía --}}
                    <template x-if="notifications.length === 0">
                        <div class="p-8 text-center">
                            <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="h-6 w-6 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4a2 2 0 012-2m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                            </div>
                            <p class="text-xs font-bold text-slate-400">Sin avisos pendientes</p>
                        </div>
                    </template>

                    {{-- Lista de Items --}}
                    <template x-for="n in notifications" :key="n.id">
                        <div class="p-4 hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0 group">
                            <p class="text-xs font-semibold text-slate-700 leading-relaxed mb-3" x-text="n.message"></p>
                            <div class="flex items-center gap-2">
                                <button @click="view(n.id, n.url)" 
                                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-wider py-1.5 rounded-lg transition-all shadow-sm">
                                    Ir a verla
                                </button>
                                <button @click="dismiss(n.id)" 
                                        class="px-2 py-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Eliminar aviso">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                            <p class="text-[9px] text-slate-400 mt-2 font-bold uppercase" x-text="n.created_at"></p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
function notificationHandler() {
    return {
        open: false,
        notifications: [],
        count: 0,
        fetchNotifications() {
            fetch('{{ route('notifications.index') }}')
                .then(res => res.json())
                .then(data => {
                    this.notifications = data.notifications;
                    this.count = data.count;
                });
            
            // Auto refrescar cada 60 segundos
            setInterval(() => {
                if(!this.open) this.fetchNotifications();
            }, 60000);
        },
        view(id, url) {
            fetch(`/admin/notifications/${id}/read`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(() => {
                window.open(url, '_blank');
                this.fetchNotifications();
                this.open = false;
            });
        },
        dismiss(id) {
            fetch(`/admin/notifications/${id}/dismiss`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(() => {
                this.fetchNotifications();
            });
        }
    }
}
</script>
