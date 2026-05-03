<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ config('app.name', 'FutGo') }} | Reserva tu Cancha al Instante</title>
    
    <!-- Script de Tema (Light/Dark Mode) -->
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
                    animation: {
                        'float': 'float 3s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        }
                    }
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
        
        .text-gradient { background: linear-gradient(to right, #22c55e, #10b981); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

        .hero-bg { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: url('https://images.unsplash.com/photo-1518605368461-1ee7e53f191b?q=80&w=2070&auto=format&fit=crop'); background-size: cover; background-position: center; z-index: -2; }
        .hero-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to bottom, rgba(248,250,252,0.85) 0%, rgba(248,250,252,1) 100%); z-index: -1; transition: background 0.3s; }
        .dark .hero-overlay { background: linear-gradient(to bottom, rgba(15,23,42,0.7) 0%, rgba(15,23,42,1) 100%); }
    </style>
</head>
<body class="antialiased pb-24 md:pb-0 relative min-h-screen bg-slate-50 dark:bg-dark-950 text-slate-900 dark:text-slate-100 transition-colors duration-300">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 glass-nav transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 md:h-20">
                <!-- Logo -->
                <div class="flex items-center gap-2 cursor-pointer group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-brand-500/30 group-hover:scale-105 transition-transform">
                        <i class="ph-bold ph-soccer-ball text-white text-xl"></i>
                    </div>
                    <span class="font-bold text-2xl tracking-tight text-slate-900 dark:text-white">Fut<span class="text-brand-500">Bo</span></span>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="#" class="text-slate-900 dark:text-white font-bold text-sm transition-colors">Inicio</a>
                    <a href="/canchas" class="text-slate-500 dark:text-slate-300 font-medium text-sm hover:text-brand-500 dark:hover:text-white transition-colors">Canchas</a>
                    <a href="/reservas" class="text-slate-500 dark:text-slate-300 font-medium text-sm hover:text-brand-500 dark:hover:text-white transition-colors">Torneos</a>
                </div>

                <!-- Auth / Profile & Theme Toggle -->
                <div class="flex items-center gap-4">
                    <!-- Botón Tema -->
                    <button onclick="toggleTheme()" class="w-10 h-10 rounded-full flex items-center justify-center text-slate-500 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-white/10 transition-colors" title="Cambiar tema">
                        <i class="ph-bold ph-moon block dark:hidden text-xl"></i>
                        <i class="ph-bold ph-sun hidden dark:block text-xl"></i>
                    </button>

                    @auth
                        <div class="hidden md:flex flex-col text-right">
                            <span class="text-sm font-bold text-slate-900 dark:text-white">Hola, {{ Auth::user()->name }}</span>
                            <span class="text-xs text-brand-500 font-medium">Jugador</span>
                        </div>
                        <a href="{{ url('/dashboard') }}" class="w-10 h-10 rounded-full border-2 border-brand-500 p-0.5 overflow-hidden hover:scale-105 transition-transform relative">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=22c55e&color=fff" alt="Perfil" class="w-full h-full rounded-full object-cover">
                        </a>
                    @else
                        <a href="/login" class="text-slate-700 dark:text-white font-semibold text-sm hover:text-brand-500 transition-colors hidden sm:block">Entrar</a>
                        <a href="/registro" class="bg-brand-500 hover:bg-brand-600 text-white px-5 py-2 rounded-xl text-sm font-bold transition-all hover:scale-105">Registrarse</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative pt-32 pb-20 md:pt-48 md:pb-32 px-4 overflow-hidden min-h-[85vh] flex flex-col justify-center">
        <div class="hero-bg"></div>
        <div class="hero-overlay"></div>
        
        <div class="max-w-5xl mx-auto w-full relative z-10">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/90 dark:bg-white/10 backdrop-blur-md border border-brand-500/30 mb-6 mx-auto md:mx-0 w-max animate-float shadow-sm dark:shadow-none">
                <span class="relative flex h-2.5 w-2.5">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-brand-500"></span>
                </span>
                <span class="text-xs font-bold text-brand-700 dark:text-brand-300 tracking-wide uppercase">Más de 50 canchas disponibles hoy</span>
            </div>

            <!-- Title -->
            <h1 class="text-5xl md:text-7xl font-extrabold text-slate-900 dark:text-white mb-6 leading-tight tracking-tight text-center md:text-left">
                Tu próxima pichanga <br class="hidden md:block" />
                <span class="text-gradient">a un clic de distancia.</span>
            </h1>
            
            <p class="text-slate-600 dark:text-slate-300 text-lg md:text-xl mb-10 max-w-2xl text-center md:text-left font-medium dark:font-light">
                Olvídate de las llamadas interminables. Encuentra, reserva y paga tu cancha de fútbol en segundos. La pasión no espera.
            </p>

            <!-- Search Bar -->
            <form action="/canchas" method="GET" class="glass rounded-3xl md:rounded-full p-2 md:p-3 flex flex-col md:flex-row items-center gap-2 w-full max-w-4xl shadow-xl dark:shadow-2xl shadow-black/5 dark:shadow-black/50">
                
                <!-- Location -->
                <div class="w-full md:w-2/5 p-3 md:px-5 flex items-center gap-3 hover:bg-slate-100/50 dark:hover:bg-white/5 rounded-2xl md:rounded-full cursor-pointer transition-colors group">
                    <div class="w-10 h-10 rounded-full bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center text-brand-500 dark:text-brand-400 group-hover:bg-brand-500 group-hover:text-white transition-colors">
                        <i class="ph-fill ph-map-pin text-xl"></i>
                    </div>
                    <div class="flex-1 flex flex-col">
                        <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Ubicación</span>
                        <input type="text" name="ubicacion" placeholder="¿Dónde quieres jugar?" class="w-full bg-transparent text-slate-900 dark:text-white font-bold dark:font-medium outline-none placeholder-slate-400 dark:placeholder-slate-500 text-sm mt-0.5" value="Lima, Miraflores">
                    </div>
                </div>

                <div class="hidden md:block w-px h-12 bg-slate-200 dark:bg-white/10"></div>

                <!-- Date -->
                <div class="w-full md:w-2/5 p-3 md:px-5 flex items-center gap-3 hover:bg-slate-100/50 dark:hover:bg-white/5 rounded-2xl md:rounded-full cursor-pointer transition-colors group border-t border-slate-100 dark:border-white/5 md:border-t-0">
                    <div class="w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-500 dark:text-blue-400 group-hover:bg-blue-500 group-hover:text-white transition-colors">
                        <i class="ph-fill ph-calendar-blank text-xl"></i>
                    </div>
                    <div class="flex-1 flex flex-col">
                        <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Fecha</span>
                        <input type="date" name="fecha" class="w-full bg-transparent text-slate-900 dark:text-white font-bold dark:font-medium outline-none placeholder-slate-400 dark:placeholder-slate-500 text-sm mt-0.5 [color-scheme:light] dark:[color-scheme:dark]">
                    </div>
                </div>

                <!-- Search Button -->
                <div class="w-full md:w-1/5 mt-2 md:mt-0">
                    <button type="submit" class="w-full h-14 bg-brand-500 hover:bg-brand-600 text-white font-bold rounded-2xl md:rounded-full flex items-center justify-center gap-2 transition-all hover:scale-[1.02] active:scale-95 shadow-lg shadow-brand-500/40 group">
                        <i class="ph-bold ph-magnifying-glass text-lg group-hover:rotate-12 transition-transform"></i>
                        <span>Buscar</span>
                    </button>
                </div>
            </form>
            
            <!-- Quick Filters -->
            <div class="mt-8 flex flex-wrap gap-3 justify-center md:justify-start">
                <button class="px-4 py-2 rounded-full bg-white dark:bg-transparent dark:glass border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-700 dark:text-white shadow-sm hover:bg-slate-50 dark:hover:bg-white/10 transition-colors flex items-center gap-2">
                    <i class="ph-fill ph-soccer-ball text-brand-500 dark:text-brand-400"></i> Fútbol 7
                </button>
                <button class="px-4 py-2 rounded-full bg-white dark:bg-transparent dark:glass border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-700 dark:text-white shadow-sm hover:bg-slate-50 dark:hover:bg-white/10 transition-colors flex items-center gap-2">
                    <i class="ph-fill ph-soccer-ball text-blue-500 dark:text-blue-400"></i> Fútbol 5
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-20 relative z-10">
        
        <!-- Features -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="glass-card p-6 rounded-3xl hover:-translate-y-2 transition-transform duration-300">
                <div class="w-12 h-12 rounded-2xl bg-brand-100 dark:bg-brand-500/20 flex items-center justify-center text-brand-600 dark:text-brand-400 mb-4">
                    <i class="ph-bold ph-clock-fast text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Reserva al instante</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">Confirma tu cancha en tiempo real. Sin esperas, sin confirmaciones manuales.</p>
            </div>
            <div class="glass-card p-6 rounded-3xl hover:-translate-y-2 transition-transform duration-300">
                <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center text-blue-600 dark:text-blue-400 mb-4">
                    <i class="ph-bold ph-shield-check text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Pago 100% Seguro</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">Paga con Yape, Plin o tarjeta. Tu reserva queda garantizada inmediatamente.</p>
            </div>
            <div class="glass-card p-6 rounded-3xl hover:-translate-y-2 transition-transform duration-300">
                <div class="w-12 h-12 rounded-2xl bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center text-purple-600 dark:text-purple-400 mb-4">
                    <i class="ph-bold ph-users-three text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Divide la cuenta</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">Comparte el link de pago con tu equipo y que cada uno ponga su parte.</p>
            </div>
        </section>

        <!-- Trending Venues -->
        <section>
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">Canchas Top 🔥</h2>
                    <p class="text-slate-500 dark:text-slate-400">Las más reservadas esta semana en tu zona.</p>
                </div>
                <a href="/canchas" class="hidden md:flex items-center gap-2 text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 font-semibold group">
                    Ver todas <i class="ph-bold ph-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6" id="venues-grid">
                <!-- JS Injected -->
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 dark:border-white/10 bg-white dark:bg-dark-950 pt-16 pb-24 md:pb-12 mt-20 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-brand-500 flex items-center justify-center">
                            <i class="ph-bold ph-soccer-ball text-white text-lg"></i>
                        </div>
                        <span class="font-bold text-xl text-slate-900 dark:text-white">Fut<span class="text-brand-500">Bo</span></span>
                    </div>
                    <p class="text-slate-500 dark:text-slate-400 text-sm max-w-sm mb-6">La plataforma número uno para organizar tus partidos, encontrar canchas y vivir la pasión del fútbol sin complicaciones.</p>
                </div>
                <div>
                    <h4 class="text-slate-900 dark:text-white font-bold mb-4">Jugadores</h4>
                    <ul class="space-y-2 text-sm text-slate-500 dark:text-slate-400">
                        <li><a href="/canchas" class="hover:text-brand-500 transition-colors">Buscar Canchas</a></li>
                        <li><a href="#" class="hover:text-brand-500 transition-colors">Torneos Locales</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-slate-900 dark:text-white font-bold mb-4">Propietarios</h4>
                    <ul class="space-y-2 text-sm text-slate-500 dark:text-slate-400">
                        <li><a href="#" class="hover:text-brand-500 transition-colors">Registrar mi Complejo</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-slate-200 dark:border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-slate-500 dark:text-slate-500">
                <p>&copy; 2026 FutGo. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Mobile Nav -->
    <div class="md:hidden fixed bottom-0 left-0 w-full bg-white dark:bg-dark-900 border-t border-slate-200 dark:border-white/10 px-6 py-2 flex justify-between items-center z-50 pb-safe transition-colors duration-300">
        <a href="/" class="flex flex-col items-center gap-1 p-2 text-brand-500 transition-colors">
            <i class="ph-fill ph-house text-2xl"></i>
            <span class="text-[10px] font-bold">Inicio</span>
        </a>
        <a href="/canchas" class="flex flex-col items-center gap-1 p-2 text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
            <i class="ph ph-magnifying-glass text-2xl"></i>
            <span class="text-[10px] font-medium">Buscar</span>
        </a>
        
        <div class="relative -top-5">
            <a href="/canchas" class="w-14 h-14 rounded-full bg-brand-500 text-white flex items-center justify-center shadow-[0_0_20px_rgba(34,197,94,0.4)] hover:bg-brand-400 transition-colors active:scale-95">
                <i class="ph-bold ph-plus text-2xl"></i>
            </a>
        </div>

        <a href="/reservas" class="flex flex-col items-center gap-1 p-2 text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
            <i class="ph ph-calendar-check text-2xl"></i>
            <span class="text-[10px] font-medium">Reservas</span>
        </a>
        <a href="/perfil" class="flex flex-col items-center gap-1 p-2 text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
            <i class="ph ph-user text-2xl"></i>
            <span class="text-[10px] font-medium">Perfil</span>
        </a>
    </div>

    <script>
        const venues = [
            { id: 1, name: "La Bombonera FC", location: "Miraflores", type: "Fútbol 7", price: "S/ 120", rating: 4.9, img: "https://images.unsplash.com/photo-1575361204480-aadea25e6e68?q=80&w=600&auto=format&fit=crop" },
            { id: 2, name: "Complejo El 10", location: "Surco", type: "Fútbol 5", price: "S/ 80", rating: 4.7, img: "https://images.unsplash.com/photo-1551280857-2b9ebf262c62?q=80&w=600&auto=format&fit=crop" },
            { id: 3, name: "Canchas Monumental", location: "La Molina", type: "Fútbol 7", price: "S/ 150", rating: 5.0, img: "https://images.unsplash.com/photo-1524015368236-bbf6f72545b6?q=80&w=600&auto=format&fit=crop" },
            { id: 4, name: "FutGo Park", location: "San Borja", type: "Fútbol 6", price: "S/ 100", rating: 4.6, img: "https://images.unsplash.com/photo-1459865264687-595d652de67e?q=80&w=600&auto=format&fit=crop" }
        ];

        function renderVenues() {
            const container = document.getElementById('venues-grid');
            let html = '';
            venues.forEach(venue => {
                html += `
                    <article class="bg-white dark:bg-dark-900 rounded-3xl overflow-hidden border border-slate-100 dark:border-white/5 hover:border-brand-500/50 group cursor-pointer transition-all duration-300 shadow-sm hover:shadow-xl dark:shadow-none">
                        <div class="relative h-40 overflow-hidden">
                            <img src="${venue.img}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt="${venue.name}">
                            <div class="absolute inset-0 bg-gradient-to-t from-dark-900/80 to-transparent dark:opacity-100 opacity-60"></div>
                        </div>
                        <div class="p-5 relative">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1 group-hover:text-brand-500 transition-colors">${venue.name}</h3>
                            <p class="text-slate-500 dark:text-slate-400 text-xs flex items-center gap-1 mb-4"><i class="ph ph-map-pin"></i> ${venue.location}</p>
                            <div class="flex justify-between items-center pt-3 border-t border-slate-100 dark:border-white/5">
                                <div class="font-bold text-slate-900 dark:text-white text-base">${venue.price}<span class="text-xs font-normal text-slate-500">/hr</span></div>
                                <div class="flex items-center gap-1 bg-slate-50 dark:bg-dark-800 px-2 py-1 rounded-md"><i class="ph-fill ph-star text-yellow-400 text-xs"></i><span class="text-xs font-bold text-slate-700 dark:text-white">${venue.rating}</span></div>
                            </div>
                        </div>
                    </article>`;
            });
            container.innerHTML = html;
        }

        document.addEventListener('DOMContentLoaded', () => renderVenues());
    </script>
</body>
</html>
