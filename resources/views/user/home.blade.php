@extends('layouts.app')
@section('title', 'Inicio | FutGo')

@section('content')

@php
$eventos = [
    [
        'id'       => 1,
        'titulo'   => 'Gran Torneo Relámpago Fútbol 7',
        'venue'    => 'Canchas La Losa',
        'fecha'    => 'Sáb, 20 May · 09:00 AM',
        'precio'   => 'S/ 150.00 x equipo',
        'tipo'     => 'torneo',
        'icon'     => 'trophy',
        'gradient' => 'from-indigo-500 to-purple-600',
    ],
    [
        'id'       => 2,
        'titulo'   => 'Pollada Bailable Pro-Salud',
        'venue'    => 'Complejo El 10',
        'fecha'    => 'Dom, 21 May · 12:00 PM',
        'precio'   => 'S/ 20.00 x ticket',
        'tipo'     => 'social',
        'icon'     => 'music',
        'gradient' => 'from-amber-500 to-orange-600',
    ],
    [
        'id'       => 3,
        'titulo'   => 'FutGo Libre (Anota tu nombre)',
        'venue'    => 'El Rey del Gras',
        'fecha'    => 'Hoy · 20:00 PM',
        'precio'   => 'S/ 15.00 cupo',
        'tipo'     => 'pichanga',
        'icon'     => 'users',
        'gradient' => 'from-emerald-500 to-teal-600',
    ],
    [
        'id'       => 4,
        'titulo'   => 'Campeonato Nocturno Libre',
        'venue'    => 'La Bombonera FC',
        'fecha'    => 'Vie, 26 May · 19:00 PM',
        'precio'   => 'S/ 200.00 x equipo',
        'tipo'     => 'torneo',
        'icon'     => 'trophy',
        'gradient' => 'from-blue-500 to-cyan-600',
    ],
];

// Imágenes por tipo de cancha (hasta tener fotos reales)
$imagenesDefault = [
    'futbol5'  => 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?w=600&q=80',
    'futbol7'  => 'https://images.unsplash.com/photo-1551280857-2b9ebf262c62?w=600&q=80',
    'futbol11' => 'https://images.unsplash.com/photo-1529900748604-07564a03e7a6?w=600&q=80',
];
@endphp

{{-- ======================================================
     HERO & BUSCADOR
====================================================== --}}
<header class="bg-white dark:bg-dark-900 border-b border-slate-200 dark:border-white/5 pt-6 pb-8 md:pt-12 md:pb-16 px-4 relative transition-colors duration-300">
    <div class="max-w-4xl mx-auto text-center">
        <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-4">
            Juega sin complicaciones.
        </h1>
        <p class="text-slate-500 dark:text-slate-400 text-lg mb-8 max-w-2xl mx-auto">
            Encuentra canchas disponibles ahora mismo o únete a los mejores eventos de tu ciudad.
        </p>

        <!-- Buscador -->
        <form action="/canchas" method="GET"
              class="max-w-3xl mx-auto bg-white dark:bg-dark-800 rounded-3xl md:rounded-full shadow-[0_8px_30px_rgb(0,0,0,0.08)] dark:shadow-none border border-slate-100 dark:border-white/10 p-2 md:pl-6 flex flex-col md:flex-row items-center gap-2 relative z-10">

            <!-- ¿Dónde? -->
            <div class="w-full md:w-1/3 p-3 flex flex-col items-start hover:bg-slate-50 dark:hover:bg-white/5 rounded-full cursor-pointer transition-colors">
                <label class="text-xs font-bold text-slate-900 dark:text-slate-200 ml-1">¿Dónde?</label>
                <input type="text" name="ubicacion" placeholder="Ciudad, distrito o local..."
                       value="{{ request('ubicacion', 'Cusco, Wanchaq') }}"
                       class="w-full text-sm text-slate-600 dark:text-slate-300 bg-transparent outline-none truncate placeholder-slate-400 dark:placeholder-slate-500 ml-1">
            </div>

            <div class="hidden md:block w-px h-10 bg-slate-200 dark:bg-white/10"></div>

            <!-- ¿Cuándo? -->
            <div class="w-full md:w-1/3 p-3 flex flex-col items-start hover:bg-slate-50 dark:hover:bg-white/5 rounded-full cursor-pointer transition-colors border-t md:border-t-0 border-slate-100 dark:border-white/5">
                <label class="text-xs font-bold text-slate-900 dark:text-slate-200 ml-1">¿Cuándo?</label>
                <input type="date" name="fecha"
                       value="{{ request('fecha', date('Y-m-d')) }}"
                       min="{{ date('Y-m-d') }}"
                       class="w-full text-sm text-slate-600 dark:text-slate-300 bg-transparent outline-none ml-1 cursor-pointer [color-scheme:light] dark:[color-scheme:dark]">
            </div>

            <div class="hidden md:block w-px h-10 bg-slate-200 dark:bg-white/10"></div>

            <!-- Botón -->
            <div class="w-full md:w-auto mt-2 md:mt-0 md:ml-auto">
                <button type="submit"
                        class="w-full md:w-auto bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-4 md:py-3 px-8 rounded-2xl md:rounded-full flex items-center justify-center gap-2 transition-transform hover:scale-105 shadow-md shadow-emerald-500/30">
                    <i data-lucide="search" class="w-5 h-5"></i>
                    <span>Buscar</span>
                </button>
            </div>
        </form>
    </div>
</header>

{{-- ======================================================
     CONTENIDO PRINCIPAL
====================================================== --}}
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 space-y-12">

    {{-- SECCIÓN 1: EVENTOS --}}
    <section>
        <div class="flex justify-between items-end mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Eventos cerca de ti</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Torneos, polladas y pichangas libres en Cusco.</p>
            </div>
            <a href="/eventos" class="hidden sm:flex text-emerald-600 hover:text-emerald-700 text-sm font-semibold items-center gap-1">
                Ver todos <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </a>
        </div>

        <!-- Carrusel horizontal -->
        <div class="flex gap-4 overflow-x-auto hide-scrollbar snap-x snap-mandatory pb-4">
            @foreach($eventos as $ev)
            <div class="snap-start shrink-0 w-72 md:w-80 h-48 rounded-2xl p-5 bg-gradient-to-br {{ $ev['gradient'] }} text-white flex flex-col justify-between shadow-lg hover:-translate-y-1 transition-transform cursor-pointer relative overflow-hidden group">

                <!-- Efecto brillo -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-2xl transform translate-x-10 -translate-y-10 group-hover:bg-white/20 transition-all"></div>

                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="bg-white/20 backdrop-blur-md px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider flex items-center gap-1">
                            <i data-lucide="{{ $ev['icon'] }}" class="w-3 h-3"></i>
                            {{ $ev['tipo'] }}
                        </span>
                    </div>
                    <h3 class="font-bold text-lg leading-tight mb-1 line-clamp-2">{{ $ev['titulo'] }}</h3>
                    <p class="text-white/80 text-xs flex items-center gap-1">
                        <i data-lucide="map-pin" class="w-3 h-3"></i> {{ $ev['venue'] }}
                    </p>
                </div>

                <div class="relative z-10 flex justify-between items-end">
                    <div class="flex flex-col">
                        <span class="text-white/80 text-[10px] uppercase font-semibold tracking-wide">Fecha</span>
                        <span class="text-sm font-bold flex items-center gap-1">
                            <i data-lucide="calendar" class="w-4 h-4"></i> {{ $ev['fecha'] }}
                        </span>
                    </div>
                    <div class="bg-white text-slate-900 px-3 py-1.5 rounded-xl text-xs font-bold shadow-sm">
                        {{ $ev['precio'] }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    {{-- SECCIÓN 2: CANCHAS --}}
    <section>
        <div class="flex justify-between items-end mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Complejos disponibles hoy</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    {{ $venues->count() }} complejos activos en la plataforma
                </p>
            </div>
            <a href="/canchas" class="text-brand-500 hover:text-brand-600 text-sm font-semibold transition-colors">
                Ver todos →
            </a>
        </div>

        <!-- Grid de venues reales -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($venues->take(4) as $venue)
            @php
                $tipoIcono = $venue->fields->first()?->sport_type ?? 'futbol5';
                $imagen    = $imagenesDefault[$tipoIcono] ?? $imagenesDefault['futbol5'];
                $precioMin = $venue->fields->flatMap(fn($f) =>
                    $f->slots->pluck('unit_price')
                )->min();
            @endphp
            <a href="/canchas/{{ $venue->id }}"
               class="bg-white dark:bg-dark-800 rounded-2xl overflow-hidden shadow-sm border border-slate-100 dark:border-white/10 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-pointer group flex flex-col">

                <!-- Imagen y badges -->
                <div class="relative h-48 overflow-hidden">
                    <img src="{{ $imagen }}" alt="{{ $venue->name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

                    @if($venue->disponible_hoy)
                    <div class="absolute top-3 left-3 bg-slate-900/80 backdrop-blur-sm text-white px-3 py-1.5 rounded-full text-xs font-bold flex items-center gap-1.5 shadow-sm">
                        <i class="ph-fill ph-lightning text-yellow-400"></i>
                        Disponible hoy
                    </div>
                    @else
                    <div class="absolute top-3 left-3 bg-slate-500/80 text-white px-3 py-1.5 rounded-full text-xs font-medium">
                        Sin turnos hoy
                    </div>
                    @endif
                </div>

                <!-- Info -->
                <div class="p-4 flex flex-col flex-grow">
                    <h3 class="font-bold text-slate-900 dark:text-white line-clamp-1 mb-1">{{ $venue->name }}</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-1 mb-3">
                        <i class="ph-fill ph-map-pin text-brand-500 shrink-0"></i>
                        <span class="truncate">{{ $venue->district }}, {{ $venue->city?->name }}</span>
                    </p>
                    <div class="flex flex-wrap gap-1.5 mb-3">
                        @foreach($venue->fields->groupBy('sport_type')->keys() as $tipo)
                        <span class="text-[10px] font-semibold bg-slate-100 dark:bg-white/5 text-slate-500 dark:text-slate-400 px-2 py-0.5 rounded-md uppercase">
                            {{ str_replace(['futbol5','futbol7','futbol11'], ['F5','F7','F11'], $tipo) }}
                        </span>
                        @endforeach
                        <span class="text-[10px] font-semibold bg-slate-100 dark:bg-white/5 text-slate-500 dark:text-slate-400 px-2 py-0.5 rounded-md">
                            {{ $venue->fields->count() }} canchas
                        </span>
                    </div>
                    <div class="mt-auto flex justify-between items-center pt-3 border-t border-slate-50 dark:border-white/5">
                        <div>
                            <span class="text-xs text-slate-400">Desde</span><br>
                            <span class="font-bold text-slate-900 dark:text-white">
                                S/ {{ $precioMin ? number_format($precioMin, 2) : '—' }}
                            </span>
                            <span class="text-xs text-slate-500">/ hr</span>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 flex items-center justify-center text-brand-500 group-hover:bg-brand-500 group-hover:text-white group-hover:border-brand-500 transition-colors">
                            <i class="ph-bold ph-arrow-right"></i>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        <!-- Mostrar más -->
        <div class="mt-10 text-center">
            <a href="/canchas"
               class="inline-block px-6 py-3 bg-white dark:bg-dark-800 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-slate-300 font-semibold rounded-full hover:bg-slate-50 dark:hover:bg-white/5 hover:border-slate-300 dark:hover:border-white/20 transition-colors shadow-sm">
                Mostrar más canchas
            </a>
        </div>
    </section>

    {{-- BANNER PARTNER (Solo para visitantes no logueados) --}}
    @guest
    <section class="mt-8 mx-4 sm:mx-0">
        <div class="bg-slate-900 dark:bg-dark-800 rounded-3xl px-6 py-8 md:py-10 flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute -top-10 -right-10 w-64 h-64 bg-brand-500 rounded-full blur-3xl"></div>
            </div>
            <div class="relative text-center md:text-left">
                <div class="flex items-center justify-center md:justify-start gap-2 mb-2">
                    <i class="ph-fill ph-buildings text-brand-500 text-2xl"></i>
                    <span class="text-brand-500 text-xs font-bold uppercase tracking-widest">Para dueños de canchas</span>
                </div>
                <h2 class="text-2xl font-extrabold text-white mb-1">¿Tenés un complejo deportivo?</h2>
                <p class="text-slate-400 text-sm">Digitalizá tus reservas gratis. Sin costo de alta, sin comisiones por ahora.</p>
            </div>
            <a href="/registro-partner"
               class="relative shrink-0 bg-brand-500 hover:bg-brand-600 text-white font-bold px-8 py-3.5 rounded-2xl flex items-center gap-2 transition-all hover:scale-105 shadow-lg shadow-brand-500/30 whitespace-nowrap">
                <i class="ph-bold ph-plus"></i> Registrar mi complejo
            </a>
        </div>
    </section>
    @endguest

</main>

@endsection
