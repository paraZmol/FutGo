<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    @php $__siteName = App\Models\SiteSetting::get('site_name', config('app.name', 'FutGo')); @endphp
    <title>@yield('title', $__siteName . ' | Reserva tu Cancha')</title>
    @php $__favicon = App\Models\SiteSetting::get('site_favicon'); @endphp
    @if($__favicon)
    <link rel="icon" type="image/png" href="{{ $__favicon }}">
    @endif

    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
        }
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] },
                    colors: {
                        brand: { 50: '#f0fdf4', 100: '#dcfce7', 500: '#22c55e', 600: '#16a34a', 900: '#14532d', DEFAULT: '#22c55e' },
                        dark: { 800: '#1e293b', 900: '#0f172a', 950: '#020617' }
                    },
                }
            }
        }
    </script>

    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <style>
        body { font-family: 'Outfit', sans-serif; }
        
        /* Glassmorphism - Light Mode */
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.5); }
        .glass-card { background: linear-gradient(145deg, rgba(255, 255, 255, 0.9) 0%, rgba(248, 250, 252, 0.7) 100%); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.8); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .glass-nav { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(16px); border-bottom: 1px solid rgba(226, 232, 240, 0.8); }

        /* Glassmorphism - Dark Mode */
        .dark .glass { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); }
        .dark .glass-card { background: linear-gradient(145deg, rgba(30, 41, 59, 0.7) 0%, rgba(15, 23, 42, 0.9) 100%); border: 1px solid rgba(255, 255, 255, 0.05); box-shadow: none; }
        .dark .glass-nav { background: rgba(15, 23, 42, 0.85); border-bottom: 1px solid rgba(255, 255, 255, 0.05); }

        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Custom Scrollbar Premium */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 20px; border: 2px solid transparent; background-clip: content-box; }
        .dark ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); }
        ::-webkit-scrollbar-thumb:hover { background: #cbd5e1; background-clip: content-box; }
        .dark ::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.2); }

        /* Firefox */
        * { scrollbar-width: thin; scrollbar-color: #e2e8f0 transparent; }
        .dark * { scrollbar-color: rgba(255, 255, 255, 0.1) transparent; }
    </style>

    @stack('styles')
</head>
<body class="antialiased pb-24 md:pb-0 min-h-screen flex flex-col bg-slate-50 dark:bg-dark-950 text-slate-900 dark:text-slate-100 transition-colors duration-300">

    <!-- NAVBAR TOP -->
    <nav class="fixed w-full z-50 glass-nav transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 md:h-20">
                <!-- Logo -->
                @php
                    $siteName = App\Models\SiteSetting::get('site_name', 'FutGo');
                    $siteLogo = App\Models\SiteSetting::get('site_logo');
                @endphp
                <a href="/" class="flex items-center gap-2 cursor-pointer group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-brand-500/30 group-hover:scale-105 transition-transform overflow-hidden">
                        @if($siteLogo)
                        <img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="w-full h-full object-cover">
                        @else
                        <i class="ph-bold ph-soccer-ball text-white text-xl"></i>
                        @endif
                    </div>
                    <span class="font-bold text-2xl tracking-tight text-slate-900 dark:text-white hidden sm:block">{{ $siteName }}</span>
                </a>

                <!-- Links Desktop -->
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="/" class="font-medium text-sm transition-colors {{ request()->is('/') ? 'text-brand-500 font-bold' : 'text-slate-500 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}">Inicio</a>
                    <a href="/canchas" class="font-medium text-sm transition-colors {{ request()->is('canchas*') ? 'text-brand-500 font-bold' : 'text-slate-500 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}">Ver Canchas</a>
                    <a href="/reservas" class="font-medium text-sm transition-colors {{ request()->is('reservas*') ? 'text-brand-500 font-bold' : 'text-slate-500 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}">Mis Reservas</a>
                </div>

                <!-- Perfil & Theme -->
                <div class="flex items-center gap-4">
                    <button onclick="toggleTheme()" class="w-10 h-10 rounded-full flex items-center justify-center text-slate-500 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-white/10 transition-colors">
                        <i class="ph-bold ph-moon block dark:hidden text-xl"></i>
                        <i class="ph-bold ph-sun hidden dark:block text-xl"></i>
                    </button>

                    @auth
                        <div class="hidden md:flex flex-col text-right">
                            <span class="text-sm font-bold text-slate-900 dark:text-white">Hola, {{ Auth::user()->name ?? 'Jugador' }}</span>
                            <span class="text-xs text-brand-500 font-medium">Nivel Amateur</span>
                        </div>
                        <a href="{{ url('/dashboard') }}" class="w-10 h-10 rounded-full border-2 border-brand-500 p-0.5 overflow-hidden hover:scale-105 transition-transform relative">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Jugador') }}&background=22c55e&color=fff" alt="Perfil" class="w-full h-full rounded-full object-cover">
                        </a>
                    @else
                        <a href="/login" class="px-5 py-2.5 rounded-xl bg-slate-100 dark:bg-white/5 text-slate-700 dark:text-white font-bold text-sm hover:bg-brand-500 hover:text-white transition-all hidden sm:flex items-center gap-2">
                            <i class="ph-bold ph-user"></i> Ingresar
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Espaciador -->
    <div class="h-16 md:h-20"></div>

    <!-- CONTENIDO -->
    <div class="flex-grow relative z-10">
        @yield('content')
    </div>

    <!-- BOTTOM NAV (Mobile) -->
    <div class="md:hidden fixed bottom-0 left-0 w-full bg-white dark:bg-dark-900 border-t border-slate-200 dark:border-white/10 px-6 py-2 flex justify-between items-center z-50 pb-safe transition-colors duration-300">
        <a href="/" class="flex flex-col items-center gap-1 p-2 {{ request()->is('/') ? 'text-brand-500' : 'text-slate-400 hover:text-slate-900 dark:hover:text-white' }} transition-colors">
            <i class="ph-fill ph-house text-2xl"></i>
            <span class="text-[10px] font-bold">Inicio</span>
        </a>
        <a href="/canchas" class="flex flex-col items-center gap-1 p-2 {{ request()->is('canchas*') ? 'text-brand-500' : 'text-slate-400 hover:text-slate-900 dark:hover:text-white' }} transition-colors">
            <i class="ph ph-magnifying-glass text-2xl"></i>
            <span class="text-[10px] font-medium">Buscar</span>
        </a>
        
        <div class="relative -top-5">
            <a href="/registro-partner"
               class="w-14 h-14 rounded-full bg-brand-500 text-white flex flex-col items-center justify-center shadow-[0_0_20px_rgba(34,197,94,0.4)] hover:bg-brand-400 transition-colors active:scale-95 gap-0.5">
                <i class="ph-bold ph-buildings text-xl"></i>
                <span class="text-[8px] font-bold leading-none">Complejo</span>
            </a>
        </div>

        @auth
        <a href="/reservas" class="flex flex-col items-center gap-1 p-2 {{ request()->is('reservas*') ? 'text-brand-500' : 'text-slate-400 hover:text-slate-900 dark:hover:text-white' }} transition-colors">
            <i class="ph ph-calendar-check text-2xl"></i>
            <span class="text-[10px] font-medium">Reservas</span>
        </a>
        <a href="/perfil" class="flex flex-col items-center gap-1 p-2 {{ request()->is('perfil*') ? 'text-brand-500' : 'text-slate-400 hover:text-slate-900 dark:hover:text-white' }} transition-colors">
            <i class="ph-fill ph-user-circle text-2xl"></i>
            <span class="text-[10px] font-medium">Perfil</span>
        </a>
        @else
        <a href="/reservas" class="flex flex-col items-center gap-1 p-2 text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
            <i class="ph ph-calendar-check text-2xl"></i>
            <span class="text-[10px] font-medium">Reservas</span>
        </a>
        <a href="/login" class="flex flex-col items-center gap-1 p-2 {{ request()->is('login*') ? 'text-brand-500' : 'text-slate-400 hover:text-slate-900 dark:hover:text-white' }} transition-colors">
            <i class="ph-bold ph-sign-in text-2xl"></i>
            <span class="text-[10px] font-medium">Ingresar</span>
        </a>
        @endauth
    </div>

    <!-- Footer -->
    <footer class="border-t border-slate-200 dark:border-white/10 bg-white dark:bg-dark-950 pt-10 pb-24 md:pb-8 mt-auto hidden md:block transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-brand-500 to-emerald-600 flex items-center justify-center">
                        <i class="ph-bold ph-soccer-ball text-white text-sm"></i>
                    </div>
                    <span class="font-bold text-slate-700 dark:text-white">Fut<span class="text-brand-500">Go</span></span>
                </div>
                <div class="flex items-center gap-6 text-sm text-slate-500 dark:text-slate-400">
                    @guest
                    <a href="/registro-partner" class="hover:text-brand-500 transition-colors font-semibold flex items-center gap-1">
                        <i class="ph-fill ph-buildings"></i> Registrá tu complejo
                    </a>
                    <span class="text-slate-300 dark:text-white/10">|</span>
                    @endguest
                    <p class="text-xs text-slate-400">&copy; {{ date('Y') }} FutGo. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
