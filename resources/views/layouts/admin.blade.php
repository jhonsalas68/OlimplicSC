<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 premium-bg">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'OlimpicSC') }} - Panel Administrativo</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Global App Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Pre-initialization to avoid flicker -->
    <script>
        (function() {
            const isOpen = localStorage.getItem('sidebar-open') === 'true';
            document.documentElement.classList.toggle('sidebar-is-open', isOpen);
        })();
    </script>

    {{-- Turbo View Transitions, Morphing & Prefetching --}}
    <meta name="turbo-prefetch" content="true">
    <meta name="turbo-refresh-method" content="morph">
    <meta name="turbo-refresh-scroll" content="preserve">
    <meta name="view-transition" content="same-origin">
    
    <style>
        body { font-family: 'Inter', sans-serif; -webkit-tap-highlight-color: transparent; }
        [x-cloak] { display: none !important; }
        
        /* Fix for mobile height issues */
        .min-h-screen-safe {
            min-height: 100vh;
            min-height: 100dvh;
        }

        .no-transition, .no-transition * {
            transition: none !important;
        }

        .glass-header {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px) saturate(180%);
            border-bottom: 1px solid rgba(226, 232, 240, 0.5);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
        }
        
        .sidebar-gradient {
            background: linear-gradient(180deg, #1e3a8a 0%, #1e1b4b 100%);
        }
        
        .premium-bg {
            background: radial-gradient(circle at top right, #f8fafc, #eff6ff);
        }

        /* Turbo Progress Bar */
        .turbo-progress-bar {
            height: 3px;
            background: linear-gradient(90deg, #1e3a8a, #ef4444);
            box-shadow: 0 0 10px rgba(30, 58, 138, 0.5);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            body { overflow-y: auto !important; height: auto !important; }
            .flex-h-full-mobile { height: auto !important; min-height: 100dvh; }
        }
    </style>
</head>
<body class="h-full bg-slate-50 no-transition overflow-x-hidden" 
      x-data="{ sidebarOpen: localStorage.getItem('sidebar-open') === 'true' }" 
      x-init="
        $watch('sidebarOpen', val => localStorage.setItem('sidebar-open', val));
        setTimeout(() => $el.classList.remove('no-transition'), 100);
      ">
    
    <div class="flex h-full min-h-screen-safe w-full bg-slate-50 overflow-hidden flex-h-full-mobile">
        <!-- Sidebar -->
        @include('layouts.partials.sidebar')

        <!-- Main Content Wrapper -->
        <div class="flex flex-col flex-1 min-w-0 h-full transition-all duration-300 relative"
             :class="sidebarOpen ? 'md:pl-64 w-full' : 'md:pl-16 w-full'">
            
            <!-- Navbar (Sticky) -->
            <div class="sticky top-0 z-30">
                @include('layouts.partials.navbar')
            </div>

            <!-- Content Area (Scrollable) -->
            <main class="flex-1 relative overflow-y-auto focus:outline-none p-4 sm:p-8">
                <div class="max-w-7xl mx-auto">
                    {{-- Alert Messages --}}
                    @if(session('success'))
                        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl text-emerald-700 text-sm font-bold flex items-center shadow-sm animate-in fade-in slide-in-from-top-4">
                            <svg class="h-5 w-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 p-4 bg-rose-50 border border-rose-100 rounded-2xl text-rose-700 text-sm font-bold flex items-center shadow-sm animate-in fade-in slide-in-from-top-4">
                            <svg class="h-5 w-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-6 p-5 bg-amber-50 border border-amber-100 rounded-2xl text-amber-700 text-sm font-medium shadow-sm animate-in fade-in slide-in-from-top-4">
                            <div class="flex items-center mb-3">
                                <svg class="h-5 w-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span class="font-bold uppercase tracking-tight">Errores detectados:</span>
                            </div>
                            <ul class="list-disc list-inside ml-8 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html>

