<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php $__sn = App\Models\SiteSetting::get('site_name','FutGo'); @endphp
    <title>@yield('title', 'Partner | ' . $__sn)</title>

    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else { document.documentElement.classList.remove('dark') }
        function toggleTheme() {
            document.documentElement.classList.toggle('dark');
            localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] },
                    colors: {
                        brand: { 50: '#f0fdf4', 100: '#dcfce7', 500: '#22c55e', 600: '#16a34a', DEFAULT: '#22c55e' },
                        dark:  { 800: '#1e293b', 900: '#0f172a', 950: '#020617' }
                    }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style type="text/tailwindcss">
        body { font-family: 'Outfit', sans-serif; }
        .glass-card { background: linear-gradient(145deg, rgba(255,255,255,0.9), rgba(248,250,252,0.7)); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.8); }
        .dark .glass-card { background: linear-gradient(145deg, rgba(30,41,59,0.7), rgba(15,23,42,0.9)); border: 1px solid rgba(255,255,255,0.05); }
        .sidebar-link { @apply flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all; }
        .sidebar-link.active { @apply bg-brand-500 text-white shadow-lg; }
        .sidebar-link:not(.active) { @apply text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5 hover:text-slate-900 dark:hover:text-white; }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.3); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(148, 163, 184, 0.5); }
        .dark ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); }
        .dark ::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.2); }
        * { scrollbar-width: thin; scrollbar-color: rgba(148, 163, 184, 0.3) transparent; }
        .dark * { scrollbar-color: rgba(255, 255, 255, 0.1) transparent; }
    </style>
    @stack('styles')
</head>
<body class="antialiased bg-slate-100 dark:bg-dark-950 text-slate-900 dark:text-slate-100 min-h-screen transition-colors duration-300">

<div class="flex h-screen overflow-hidden">

    {{-- ================================================
         SIDEBAR
    ================================================ --}}
    <aside id="sidebar"
           class="fixed lg:static inset-y-0 left-0 z-50 w-64 shrink-0 bg-white dark:bg-dark-900 border-r border-slate-200 dark:border-white/5 flex flex-col transition-transform duration-300 -translate-x-full lg:translate-x-0">

        {{-- Logo + Selector de venue --}}
        @php
            $todosVenues  = Auth::user()?->venues()->with('city')->get() ?? collect();
            $activeVenueId = session('active_venue_id') ?? $todosVenues->first()?->id;
            $miVenue      = $todosVenues->firstWhere('id', $activeVenueId) ?? $todosVenues->first();
        @endphp
        <div class="p-4 border-b border-slate-100 dark:border-white/5 space-y-3">
            {{-- Logo --}}
            <a href="/" class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-500 to-emerald-600 flex items-center justify-center shadow-md shrink-0">
                    <i class="ph-bold ph-soccer-ball text-white text-lg"></i>
                </div>
                <div>
                    <span class="font-bold text-lg text-slate-900 dark:text-white">{{ App\Models\SiteSetting::get('site_name','FutGo') }}</span>
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest -mt-0.5">Partner</span>
                </div>
            </a>

            {{-- Selector de venue activo --}}
            @if($todosVenues->count() > 1)
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                        class="w-full flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 hover:border-brand-500 transition-all text-left">
                    <div class="w-7 h-7 rounded-lg bg-brand-500/10 flex items-center justify-center shrink-0">
                        <i class="ph-fill ph-buildings text-brand-500 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $miVenue?->name ?? 'Seleccionar' }}</p>
                        <p class="text-[10px] text-slate-400">{{ $miVenue?->city?->name ?? '' }}</p>
                    </div>
                    <i class="ph-bold ph-caret-down text-slate-400 text-xs shrink-0" :class="open ? 'rotate-180' : ''" style="transition: transform .2s"></i>
                </button>

                <div x-show="open" @click.outside="open = false"
                     x-transition
                     class="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-dark-900 border border-slate-200 dark:border-white/10 rounded-xl shadow-xl z-50 overflow-hidden py-1">
                    @foreach($todosVenues as $v)
                    <form action="/partner/switch-venue" method="POST">
                        @csrf
                        <input type="hidden" name="venue_id" value="{{ $v->id }}">
                        <button type="submit"
                                class="w-full flex items-center gap-2.5 px-3 py-2.5 text-left hover:bg-slate-50 dark:hover:bg-white/5 transition-colors
                                {{ $v->id == $activeVenueId ? 'bg-brand-500/5' : '' }}">
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0
                                {{ $v->id == $activeVenueId ? 'bg-brand-500 text-white' : 'bg-slate-100 dark:bg-white/10 text-slate-400' }}">
                                <i class="ph-fill ph-buildings text-xs"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-800 dark:text-white truncate {{ $v->id == $activeVenueId ? 'text-brand-600 dark:text-brand-400' : '' }}">
                                    {{ $v->name }}
                                </p>
                                <p class="text-[10px] text-slate-400">{{ $v->city?->name }}</p>
                            </div>
                            @if($v->id == $activeVenueId)
                            <i class="ph-fill ph-check-circle text-brand-500 ml-auto shrink-0"></i>
                            @endif
                        </button>
                    </form>
                    @endforeach
                </div>
            </div>
            @else
            {{-- Solo un venue: lo muestra sin dropdown --}}
            <div class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10">
                <div class="w-7 h-7 rounded-lg bg-brand-500/10 flex items-center justify-center shrink-0">
                    <i class="ph-fill ph-buildings text-brand-500 text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $miVenue?->name ?? 'Mi complejo' }}</p>
                    <p class="text-[10px] text-slate-400">{{ $miVenue?->city?->name ?? '' }}</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto p-4 space-y-1">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-3 mb-2">General</p>

            <a href="/partner" class="sidebar-link {{ request()->is('partner') ? 'active' : '' }}">
                <i class="ph-bold ph-squares-four text-lg"></i> Dashboard
            </a>
            <a href="/partner/reservas" class="sidebar-link {{ request()->is('partner/reservas*') ? 'active' : '' }}">
                <i class="ph-bold ph-calendar-check text-lg"></i> Reservas
                <span class="ml-auto bg-brand-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">8</span>
            </a>
            <a href="/partner/canchas" class="sidebar-link {{ request()->is('partner/canchas*') ? 'active' : '' }}">
                <i class="ph-bold ph-soccer-ball text-lg"></i> Mis Canchas
            </a>
            <a href="/partner/horarios" class="sidebar-link {{ request()->is('partner/horarios*') ? 'active' : '' }}">
                <i class="ph-bold ph-clock text-lg"></i> Horarios y Precios
            </a>
            <a href="/partner/staff" class="sidebar-link {{ request()->is('partner/staff*') ? 'active' : '' }}">
                <i class="ph-bold ph-users text-lg"></i> Mi Staff
            </a>

            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-3 mb-2 mt-5">Análisis</p>

            <a href="/partner/analitica" class="sidebar-link {{ request()->is('partner/analitica*') ? 'active' : '' }}">
                <i class="ph-bold ph-chart-bar text-lg"></i> Analítica
            </a>
            <a href="/partner/ingresos" class="sidebar-link {{ request()->is('partner/ingresos*') ? 'active' : '' }}">
                <i class="ph-bold ph-money text-lg"></i> Ingresos
            </a>
        </nav>

        {{-- Usuario --}}
        <div class="p-4 border-t border-slate-100 dark:border-white/5 relative">
            <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-white/5 transition-colors cursor-pointer"
                 onclick="document.getElementById('user-dropdown').classList.toggle('hidden')">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Partner') }}&background=22c55e&color=fff"
                     class="w-9 h-9 rounded-xl object-cover" alt="Partner">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ Auth::user()->name ?? 'Partner' }}</p>
                    <p class="text-xs text-brand-500 font-medium truncate">{{ $miVenue?->name ?? 'Mi complejo' }}</p>
                </div>
                <button onclick="event.stopPropagation(); toggleTheme()" class="text-slate-400 hover:text-slate-700 dark:hover:text-white transition-colors p-1">
                    <i class="ph-bold ph-moon block dark:hidden"></i>
                    <i class="ph-bold ph-sun hidden dark:block"></i>
                </button>
            </div>
            
            {{-- Dropdown de Usuario --}}
            <div id="user-dropdown" class="hidden absolute bottom-full left-4 right-4 mb-2 bg-white dark:bg-dark-800 border border-slate-200 dark:border-white/10 shadow-2xl rounded-2xl z-50 overflow-hidden transform origin-bottom-left transition-all">
                <a href="/perfil" class="flex items-center gap-3 p-3 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 hover:text-brand-500 transition-colors">
                    <i class="ph-bold ph-user text-brand-500"></i> Mi Perfil
                </a>
                <a href="#" class="flex items-center gap-3 p-3 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 hover:text-brand-500 transition-colors">
                    <i class="ph-bold ph-gear text-brand-500"></i> Configuración
                </a>
                <div class="h-px bg-slate-100 dark:bg-white/10"></div>
                <form action="/logout" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 p-3 text-sm font-semibold text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors rounded-xl">
                        <i class="ph-bold ph-sign-out"></i> Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Overlay móvil --}}
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>

    {{-- ================================================
         CONTENIDO PRINCIPAL
    ================================================ --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Topbar --}}
        <header class="h-16 bg-white dark:bg-dark-900 border-b border-slate-200 dark:border-white/5 flex items-center justify-between px-4 sm:px-6 shrink-0">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 transition-colors text-slate-500">
                    <i class="ph-bold ph-list text-xl"></i>
                </button>
                <div>
                    <h1 class="text-base font-bold text-slate-900 dark:text-white">@yield('page-title', 'Dashboard')</h1>
                    <p class="text-xs text-slate-400 hidden sm:block">@yield('page-subtitle', 'Complejo Deportivo El 10')</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                {{-- Notificaciones --}}
                <div class="relative">
                    <button onclick="document.getElementById('notif-dropdown').classList.toggle('hidden')" class="relative p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 transition-colors text-slate-500 dark:text-slate-400 focus:outline-none">
                        <i class="ph-bold ph-bell text-xl"></i>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-brand-500 rounded-full animate-pulse"></span>
                    </button>

                    {{-- Dropdown Notificaciones --}}
                    <div id="notif-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white dark:bg-dark-800 border border-slate-200 dark:border-white/10 shadow-2xl rounded-2xl z-50 overflow-hidden">
                        <div class="p-4 border-b border-slate-100 dark:border-white/5 flex justify-between items-center">
                            <h3 class="font-bold text-slate-900 dark:text-white">Notificaciones</h3>
                            <button class="text-xs text-brand-500 font-semibold hover:text-brand-600 transition-colors">Marcar leídas</button>
                        </div>
                        <div class="max-h-80 overflow-y-auto">
                            {{-- Item 1 (No leída) --}}
                            <div class="p-4 border-b border-slate-50 dark:border-white/5 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors cursor-pointer bg-brand-50/30 dark:bg-brand-500/5">
                                <div class="flex gap-3">
                                    <div class="w-8 h-8 rounded-full bg-brand-500/10 text-brand-500 flex items-center justify-center shrink-0">
                                        <i class="ph-bold ph-calendar-plus"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-slate-900 dark:text-white font-medium"><span class="font-bold">Nueva reserva:</span> Cancha 1 a las 20:00</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Carlos P. ha pagado el anticipo (S/ 30.00)</p>
                                        <p class="text-[10px] text-brand-500 font-semibold mt-1">Hace 5 min</p>
                                    </div>
                                </div>
                            </div>
                            {{-- Item 2 --}}
                            <div class="p-4 border-b border-slate-50 dark:border-white/5 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors cursor-pointer opacity-75">
                                <div class="flex gap-3">
                                    <div class="w-8 h-8 rounded-full bg-rose-500/10 text-rose-500 flex items-center justify-center shrink-0">
                                        <i class="ph-bold ph-calendar-x"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-slate-900 dark:text-white font-medium"><span class="font-bold">Cancelación:</span> Cancha 2 a las 18:00</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">El cliente canceló la reserva. Slot liberado para venta.</p>
                                        <p class="text-[10px] text-slate-400 mt-1">Hace 1 hora</p>
                                    </div>
                                </div>
                            </div>
                            {{-- Item 3 --}}
                            <div class="p-4 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors cursor-pointer opacity-75">
                                <div class="flex gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-500/10 text-blue-500 flex items-center justify-center shrink-0">
                                        <i class="ph-bold ph-money"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-slate-900 dark:text-white font-medium"><span class="font-bold">Liquidación:</span> MercadoPago</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Se transfirieron S/ 450.00 a tu cuenta bancaria.</p>
                                        <p class="text-[10px] text-slate-400 mt-1">Ayer</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="#" class="block w-full text-center p-3 text-xs font-bold text-slate-500 hover:text-slate-700 dark:hover:text-white bg-slate-50 dark:bg-dark-900 transition-colors border-t border-slate-100 dark:border-white/5">
                            Ver todo el historial
                        </a>
                    </div>
                </div>
                {{-- Nuevo turno --}}
                <a href="/partner/reservas/nueva"
                   class="bg-brand-500 hover:bg-brand-600 text-white font-bold px-4 py-2 rounded-xl text-sm flex items-center gap-1.5 transition-all hover:scale-105 shadow-md shadow-brand-500/20">
                    <i class="ph-bold ph-plus"></i>
                    <span class="hidden sm:inline">Nueva reserva</span>
                </a>
            </div>
        </header>

        {{-- Contenido --}}
        <main class="flex-1 overflow-y-auto p-4 sm:p-6">
            @yield('content')
        </main>
    </div>
</div>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('-translate-x-full');
        document.getElementById('sidebar-overlay').classList.toggle('hidden');
    }

    // Cerrar dropdowns al hacer clic afuera
    document.addEventListener('click', function(event) {
        // Notificaciones
        const notifDropdown = document.getElementById('notif-dropdown');
        if (notifDropdown && !notifDropdown.classList.contains('hidden')) {
            const button = notifDropdown.previousElementSibling;
            if (!notifDropdown.contains(event.target) && !button.contains(event.target)) {
                notifDropdown.classList.add('hidden');
            }
        }
        
        // Menú de usuario
        const userDropdown = document.getElementById('user-dropdown');
        if (userDropdown && !userDropdown.classList.contains('hidden')) {
            const userButton = userDropdown.previousElementSibling;
            if (!userDropdown.contains(event.target) && !userButton.contains(event.target)) {
                userDropdown.classList.add('hidden');
            }
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });
</script>
@stack('scripts')
</body>
</html>
