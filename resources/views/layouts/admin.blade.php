<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php $__sn = App\Models\SiteSetting::get('site_name','FutGo'); @endphp
    <title>@yield('title', 'Admin | ' . $__sn)</title>

    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else { document.documentElement.classList.remove('dark') }
        function toggleTheme() {
            document.documentElement.classList.toggle('dark');
            localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] },
                    colors: {
                        brand:  { 50: '#f0fdf4', 500: '#22c55e', 600: '#16a34a', DEFAULT: '#22c55e' },
                        admin:  { 500: '#6366f1', 600: '#4f46e5', DEFAULT: '#6366f1' },
                        dark:   { 800: '#1e293b', 900: '#0f172a', 950: '#020617' }
                    }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass-card { background: linear-gradient(145deg, rgba(255,255,255,0.9), rgba(248,250,252,0.7)); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.8); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .dark .glass-card { background: linear-gradient(145deg, rgba(30,41,59,0.7), rgba(15,23,42,0.9)); border: 1px solid rgba(255,255,255,0.05); }
        .sidebar-link { display: flex; align-items: center; gap: 12px; padding: 10px 14px; border-radius: 14px; font-size: 14px; font-weight: 600; transition: all .2s; }
        .sidebar-link.active { background: #6366f1; color: white; box-shadow: 0 8px 16px -4px rgba(99,102,241,0.4); }
        .sidebar-link:not(.active) { color: #64748b; }
        .dark .sidebar-link:not(.active) { color: #94a3b8; }
        .sidebar-link:not(.active):hover { background: rgba(99,102,241,0.08); color: #6366f1; transform: translateX(4px); }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .dark ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); }
        ::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
        
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    @stack('styles')
</head>
<body class="antialiased bg-slate-100 dark:bg-dark-950 text-slate-900 dark:text-slate-100 min-h-screen transition-colors duration-300">

<div class="flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    <aside id="sidebar"
           class="fixed lg:static inset-y-0 left-0 z-50 w-64 shrink-0 bg-white dark:bg-dark-900 border-r border-slate-200 dark:border-white/5 flex flex-col transition-transform duration-300 -translate-x-full lg:translate-x-0">

        {{-- Logo --}}
        <div class="p-5 border-b border-slate-100 dark:border-white/5">
            <a href="/" class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-admin-500 to-purple-600 flex items-center justify-center shadow-md">
                    <i class="ph-bold ph-shield-check text-white text-lg"></i>
                </div>
                <div>
                    <span class="font-bold text-lg text-slate-900 dark:text-white">Fut<span class="text-admin-500">Go</span></span>
                    <span class="block text-[10px] font-bold text-admin-500 uppercase tracking-widest -mt-0.5">Backoffice</span>
                </div>
            </a>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto p-4 space-y-1">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-3 mb-2">General</p>
            <a href="/admin" class="sidebar-link {{ request()->is('admin') ? 'active' : '' }}">
                <i class="ph-bold ph-squares-four text-lg"></i> Panel Principal
            </a>
            <a href="/admin/partners" class="sidebar-link {{ request()->is('admin/partners*') ? 'active' : '' }}">
                <i class="ph-bold ph-buildings text-lg"></i> Partners
                <span class="ml-auto bg-amber-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">3</span>
            </a>
            <a href="/admin/usuarios" class="sidebar-link {{ request()->is('admin/usuarios*') ? 'active' : '' }}">
                <i class="ph-bold ph-users text-lg"></i> Usuarios
            </a>
            <a href="/admin/reservas" class="sidebar-link {{ request()->is('admin/reservas*') ? 'active' : '' }}">
                <i class="ph-bold ph-calendar-check text-lg"></i> Reservas
            </a>
            <a href="/admin/disputas" class="sidebar-link {{ request()->is('admin/disputas*') ? 'active' : '' }}">
                <i class="ph-bold ph-warning text-lg"></i> Disputas
                <span class="ml-auto bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">2</span>
            </a>

            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-3 mb-2 mt-5">Configuración</p>
            <a href="/admin/fees" class="sidebar-link {{ request()->is('admin/fees*') ? 'active' : '' }}">
                <i class="ph-bold ph-percent text-lg"></i> Comisiones
            </a>
            <a href="/admin/plataforma" class="sidebar-link {{ request()->is('admin/plataforma*') ? 'active' : '' }}">
                <i class="ph-bold ph-gear text-lg"></i> Plataforma
            </a>
            <a href="/admin/auditoria" class="sidebar-link {{ request()->is('admin/auditoria*') ? 'active' : '' }}">
                <i class="ph-bold ph-list-magnifying-glass text-lg"></i> Auditoría
            </a>

            {{-- Solo Super Admin (email @futgo.app) --}}
            @if(Auth::check() && str_ends_with(Auth::user()->email, '@futgo.app'))
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-3 mb-2 mt-5">Super Admin</p>
            <a href="/admin/marca" class="sidebar-link {{ request()->is('admin/marca*') ? 'active' : '' }}">
                <i class="ph-bold ph-paint-brush text-lg"></i> Marca y Logo
            </a>
            @endif
        </nav>

        {{-- Admin user --}}
        <div class="p-4 border-t border-slate-100 dark:border-white/5 relative">
            <div onclick="document.getElementById('admin-profile-dropdown').classList.toggle('hidden')"
                 class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-white/5 transition-colors cursor-pointer group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-admin-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shrink-0 shadow-lg group-hover:scale-105 transition-transform">
                    SA
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-900 dark:text-white truncate">Super Admin</p>
                    <p class="text-xs text-admin-500 font-medium">Plataforma</p>
                </div>
                <div class="text-slate-400 group-hover:text-slate-600 dark:group-hover:text-white transition-colors">
                    <i class="ph-bold ph-caret-up"></i>
                </div>
            </div>

            {{-- Dropdown Perfil --}}
            <div id="admin-profile-dropdown" class="hidden absolute bottom-20 left-4 right-4 bg-white dark:bg-dark-800 border border-slate-200 dark:border-white/10 shadow-2xl rounded-2xl z-50 overflow-hidden transform origin-bottom transition-all">
                <div class="p-2">
                    <button onclick="toggleTheme()" class="w-full flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-white/5 text-sm font-semibold text-slate-600 dark:text-slate-300 transition-colors">
                        <i class="ph-bold ph-moon block dark:hidden"></i>
                        <i class="ph-bold ph-sun hidden dark:block"></i>
                        <span>Cambiar tema</span>
                    </button>
                    <a href="/admin/perfil" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-white/5 text-sm font-semibold text-slate-600 dark:text-slate-300 transition-colors">
                        <i class="ph-bold ph-user-circle"></i>
                        <span>Mi Perfil</span>
                    </a>
                    <div class="h-px bg-slate-100 dark:bg-white/5 my-1"></div>
                    <form action="/logout" method="POST" class="block">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 p-2.5 rounded-xl hover:bg-red-50 dark:hover:bg-red-500/10 text-sm font-bold text-red-500 transition-colors text-left">
                            <i class="ph-bold ph-sign-out"></i>
                            <span>Cerrar sesión</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>

    {{-- MAIN --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Topbar --}}
        <header class="h-16 bg-white dark:bg-dark-900 border-b border-slate-200 dark:border-white/5 flex items-center justify-between px-4 sm:px-6 shrink-0">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 transition-colors text-slate-500">
                    <i class="ph-bold ph-list text-xl"></i>
                </button>
                <div>
                    <h1 class="text-base font-bold text-slate-900 dark:text-white">@yield('page-title', 'Panel Principal')</h1>
                    <p class="text-xs text-slate-400 hidden sm:block">@yield('page-subtitle', 'Panel de administración global')</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                {{-- Notificaciones Admin --}}
                <div class="relative">
                    <button onclick="document.getElementById('admin-notif-dropdown').classList.toggle('hidden')" class="relative p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 transition-colors text-slate-500 dark:text-slate-400 focus:outline-none">
                        <i class="ph-bold ph-bell text-xl"></i>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                    </button>
                    
                    {{-- Dropdown Notificaciones --}}
                    <div id="admin-notif-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white dark:bg-dark-800 border border-slate-200 dark:border-white/10 shadow-2xl rounded-2xl z-50 overflow-hidden transform origin-top-right transition-all">
                        <div class="p-4 border-b border-slate-100 dark:border-white/5 flex justify-between items-center">
                            <h3 class="font-bold text-slate-900 dark:text-white">Alertas Sistema</h3>
                            <button class="text-xs text-admin-500 font-semibold hover:text-admin-600 transition-colors">Marcar leídas</button>
                        </div>
                        <div class="max-h-80 overflow-y-auto">
                            {{-- Item 1 (No leída) --}}
                            <div class="p-4 border-b border-slate-50 dark:border-white/5 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors cursor-pointer bg-amber-50/50 dark:bg-amber-500/5">
                                <div class="flex gap-3">
                                    <div class="w-8 h-8 rounded-full bg-amber-500/10 text-amber-500 flex items-center justify-center shrink-0">
                                        <i class="ph-bold ph-buildings"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-slate-900 dark:text-white font-medium"><span class="font-bold">Nuevo Partner:</span> Canchas El Diamante</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Arequipa. Requiere revisión de documentos y aprobación.</p>
                                        <p class="text-[10px] text-amber-600 font-semibold mt-1">Hace 10 min</p>
                                    </div>
                                </div>
                            </div>
                            {{-- Item 2 --}}
                            <div class="p-4 border-b border-slate-50 dark:border-white/5 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors cursor-pointer opacity-75">
                                <div class="flex gap-3">
                                    <div class="w-8 h-8 rounded-full bg-red-500/10 text-red-500 flex items-center justify-center shrink-0">
                                        <i class="ph-bold ph-warning"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-slate-900 dark:text-white font-medium"><span class="font-bold">Nueva Disputa:</span> D002 (Reembolso)</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">El cliente Sofía Ríos solicita devolución por lluvia. Canchas La Losa.</p>
                                        <p class="text-[10px] text-slate-400 mt-1">Hace 2 horas</p>
                                    </div>
                                </div>
                            </div>
                            {{-- Item 3 --}}
                            <div class="p-4 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors cursor-pointer opacity-75">
                                <div class="flex gap-3">
                                    <div class="w-8 h-8 rounded-full bg-admin-500/10 text-admin-500 flex items-center justify-center shrink-0">
                                        <i class="ph-bold ph-money"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-slate-900 dark:text-white font-medium"><span class="font-bold">Liquidación automática</span></p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Se transfirieron los fondos a 48 partners de manera exitosa.</p>
                                        <p class="text-[10px] text-slate-400 mt-1">Ayer 23:55</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="#" class="block w-full text-center p-3 text-xs font-bold text-slate-500 hover:text-slate-700 dark:hover:text-white bg-slate-50 dark:bg-dark-900 transition-colors border-t border-slate-100 dark:border-white/5">
                            Ver registro de eventos
                        </a>
                    </div>
                </div>
                <a href="/" class="flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-500 dark:text-slate-400 hover:border-admin-500 hover:text-admin-500 transition-colors">
                    <i class="ph-bold ph-arrow-square-out"></i>
                    <span class="hidden sm:inline">Ver sitio</span>
                </a>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 sm:p-6 animate-fade-in">
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
        const notifDropdown = document.getElementById('admin-notif-dropdown');
        const profileDropdown = document.getElementById('admin-profile-dropdown');
        
        // Lógica notificaciones
        if (notifDropdown && !notifDropdown.classList.contains('hidden')) {
            const button = notifDropdown.previousElementSibling;
            if (!notifDropdown.contains(event.target) && !button.contains(event.target)) {
                notifDropdown.classList.add('hidden');
            }
        }

        // Lógica perfil
        if (profileDropdown && !profileDropdown.classList.contains('hidden')) {
            const profileSection = profileDropdown.previousElementSibling;
            if (!profileDropdown.contains(event.target) && !profileSection.contains(event.target)) {
                profileDropdown.classList.add('hidden');
            }
        }
    });
</script>
@stack('scripts')
</body>
</html>
