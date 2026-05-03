@extends('layouts.app')
@section('title', 'Buscar Canchas | FutGo')

@section('content')

@php
$canchas = [
    ['id' => 1, 'nombre' => 'Complejo Deportivo El 10',  'direccion' => 'Wanchaq, Cusco',          'distancia' => '1.2 km', 'precio' => 80.00, 'rating' => 4.8, 'reviews' => 124, 'tipo' => 'Fútbol 5',  'imagen' => 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?w=600&q=80', 'disponible' => true,  'tags' => ['Techada','Vestuarios','Bar'], 'promo' => '20% OFF'],
    ['id' => 2, 'nombre' => 'Canchas La Losa',           'direccion' => 'San Jerónimo, Cusco',      'distancia' => '3.5 km', 'precio' => 60.00, 'rating' => 4.5, 'reviews' => 89,  'tipo' => 'Fútbol 7',  'imagen' => 'https://images.unsplash.com/photo-1518605368461-1ee7e53f191b?w=600&q=80', 'disponible' => false, 'tags' => ['Pasto sintético','Iluminación'], 'promo' => null],
    ['id' => 3, 'nombre' => 'El Rey del Gras',           'direccion' => 'San Sebastián, Cusco',     'distancia' => '2.1 km', 'precio' => 90.00, 'rating' => 4.9, 'reviews' => 210, 'tipo' => 'Fútbol 5',  'imagen' => 'https://images.unsplash.com/photo-1551280857-2b9ebf262c62?w=600&q=80', 'disponible' => true,  'tags' => ['Techada','Estacionamiento'], 'promo' => null],
    ['id' => 4, 'nombre' => 'La Bombonera FC',           'direccion' => 'Centro Histórico, Cusco',  'distancia' => '0.8 km', 'precio' => 70.00, 'rating' => 4.6, 'reviews' => 56,  'tipo' => 'Fútbol 11', 'imagen' => 'https://images.unsplash.com/photo-1524015368236-bbf6f72545b6?w=600&q=80', 'disponible' => false, 'tags' => ['Pasto natural'], 'promo' => null],
    ['id' => 5, 'nombre' => 'SportCenter Norte',         'direccion' => 'Ttio, Cusco',              'distancia' => '2.8 km', 'precio' => 85.00, 'rating' => 4.7, 'reviews' => 98,  'tipo' => 'Fútbol 5',  'imagen' => 'https://images.unsplash.com/photo-1529900748604-07564a03e7a6?w=600&q=80', 'disponible' => true,  'tags' => ['Techada','Vestuarios','WiFi'], 'promo' => null],
    ['id' => 6, 'nombre' => 'Complejo Olimpo',           'direccion' => 'Santiago, Cusco',          'distancia' => '4.2 km', 'precio' => 55.00, 'rating' => 4.3, 'reviews' => 41,  'tipo' => 'Fútbol 7',  'imagen' => 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=600&q=80', 'disponible' => true,  'tags' => ['Iluminación','Bar'], 'promo' => 'NUEVO'],
];
@endphp

{{-- BARRA DE BÚSQUEDA --}}
<div class="bg-white dark:bg-dark-900 border-b border-slate-200 dark:border-white/5 sticky top-16 md:top-20 z-30 shadow-sm dark:shadow-black/20 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- FILA SUPERIOR: resumen compacto en móvil + botón toggle --}}
        <div class="flex items-center justify-between py-3 md:hidden">
            <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300 min-w-0">
                <i class="ph-fill ph-magnifying-glass text-brand-500 shrink-0"></i>
                <span class="truncate font-medium">
                    {{ request('ubicacion', 'Cusco') }}
                    @if(request('fecha')) · {{ \Carbon\Carbon::parse(request('fecha'))->format('d M') }} @endif
                    @if(request('tipo')) · {{ request('tipo') }} @endif
                </span>
            </div>
            <button onclick="toggleBuscador()"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-brand-500/10 text-brand-600 dark:text-brand-400 text-xs font-bold shrink-0 ml-2"
                    id="btn-toggle-buscador">
                <i class="ph-bold ph-sliders" id="icon-toggle"></i>
                <span id="label-toggle">Filtrar</span>
            </button>
        </div>

        {{-- FORMULARIO: siempre visible en desktop, colapsable en móvil --}}
        <form action="/canchas" method="GET"
              id="form-buscador"
              class="hidden md:flex flex-col md:flex-row gap-3 items-stretch md:items-center py-3 md:py-4">

            {{-- Ubicación --}}
            <div class="flex items-center gap-3 bg-slate-50 dark:bg-dark-800 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3 flex-1 focus-within:border-brand-500 focus-within:ring-1 focus-within:ring-brand-500 transition-all">
                <i class="ph-fill ph-map-pin text-xl text-brand-500"></i>
                <div class="flex flex-col w-full">
                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider leading-none mb-1">¿Dónde?</span>
                    <input type="text" name="ubicacion" placeholder="Ciudad, distrito o local..."
                           value="{{ request('ubicacion', 'Cusco, Wanchaq') }}"
                           class="bg-transparent text-sm font-medium outline-none w-full text-slate-900 dark:text-white placeholder-slate-400 leading-none">
                </div>
            </div>

            {{-- Fecha --}}
            <div class="flex items-center gap-3 bg-slate-50 dark:bg-dark-800 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3 flex-1 focus-within:border-brand-500 focus-within:ring-1 focus-within:ring-brand-500 transition-all">
                <i class="ph-fill ph-calendar-blank text-xl text-blue-500"></i>
                <div class="flex flex-col w-full">
                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider leading-none mb-1">¿Cuándo?</span>
                    <input type="date" name="fecha"
                           value="{{ request('fecha', date('Y-m-d')) }}"
                           min="{{ date('Y-m-d') }}"
                           class="bg-transparent text-sm font-medium outline-none text-slate-900 dark:text-white cursor-pointer leading-none [color-scheme:light] dark:[color-scheme:dark]">
                </div>
            </div>

            {{-- Tipo --}}
            <div class="flex items-center gap-3 bg-slate-50 dark:bg-dark-800 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3 flex-1 focus-within:border-brand-500 transition-all relative">
                <i class="ph-fill ph-soccer-ball text-xl text-purple-500"></i>
                <div class="flex flex-col w-full">
                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider leading-none mb-1">Tipo</span>
                    <select name="tipo" class="bg-transparent text-sm font-medium outline-none text-slate-900 dark:text-white cursor-pointer leading-none appearance-none w-full pr-6">
                        <option value="">Cualquiera</option>
                        <option value="futbol5"  {{ request('tipo') === 'futbol5'  ? 'selected' : '' }}>Fútbol 5</option>
                        <option value="futbol7"  {{ request('tipo') === 'futbol7'  ? 'selected' : '' }}>Fútbol 7</option>
                        <option value="futbol11" {{ request('tipo') === 'futbol11' ? 'selected' : '' }}>Fútbol 11</option>
                    </select>
                </div>
                <i class="ph-bold ph-caret-down text-slate-400 pointer-events-none absolute right-4 top-1/2 -translate-y-1/2"></i>
            </div>

            <button type="submit"
                    class="bg-brand-500 hover:bg-brand-600 text-white font-bold h-14 px-8 rounded-2xl flex items-center justify-center gap-2 transition-all active:scale-95 shadow-lg shadow-brand-500/30 shrink-0">
                <i class="ph-bold ph-magnifying-glass text-lg"></i>
                <span class="hidden md:inline">Buscar</span>
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let abierto = false;
    function toggleBuscador() {
        const form  = document.getElementById('form-buscador');
        const icon  = document.getElementById('icon-toggle');
        const label = document.getElementById('label-toggle');
        abierto = !abierto;
        if (abierto) {
            form.classList.remove('hidden');
            form.classList.add('flex');
            icon.className  = 'ph-bold ph-x';
            label.textContent = 'Cerrar';
        } else {
            form.classList.add('hidden');
            form.classList.remove('flex');
            icon.className  = 'ph-bold ph-sliders';
            label.textContent = 'Filtrar';
        }
    }
</script>
@endpush

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-8">

        {{-- SIDEBAR FILTROS --}}
        <aside class="hidden lg:block w-64 shrink-0">
            <div class="glass-card rounded-3xl p-6 sticky top-44 space-y-8">

                <div class="flex items-center gap-2 text-slate-900 dark:text-white font-bold text-lg mb-2">
                    <i class="ph-bold ph-faders"></i> Filtros
                </div>

                {{-- Precio --}}
                <div>
                    <label class="text-[10px] font-bold text-brand-600 dark:text-brand-400 uppercase tracking-wider mb-4 block">
                        Precio por hora
                    </label>
                    <div class="space-y-3">
                        @foreach(['Hasta S/ 60' => '60', 'S/ 60 – S/ 90' => '60-90', 'Más de S/ 90' => '90+'] as $label => $val)
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative flex items-center">
                                <input type="checkbox" value="{{ $val }}" class="peer appearance-none w-5 h-5 border-2 border-slate-300 dark:border-white/20 rounded-md bg-white dark:bg-dark-800 checked:bg-brand-500 checked:border-brand-500 transition-all cursor-pointer">
                                <i class="ph-bold ph-check text-white absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 opacity-0 peer-checked:opacity-100 pointer-events-none text-xs"></i>
                            </div>
                            <span class="text-sm font-medium text-slate-600 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="border-t border-slate-200 dark:border-white/5"></div>

                {{-- Comodidades --}}
                <div>
                    <label class="text-[10px] font-bold text-brand-600 dark:text-brand-400 uppercase tracking-wider mb-4 block">
                        Comodidades
                    </label>
                    <div class="space-y-3">
                        @foreach(['Techada' => 'techada', 'Vestuarios' => 'vestuarios', 'Estacionamiento' => 'estacionamiento'] as $label => $val)
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative flex items-center">
                                <input type="checkbox" value="{{ $val }}" class="peer appearance-none w-5 h-5 border-2 border-slate-300 dark:border-white/20 rounded-md bg-white dark:bg-dark-800 checked:bg-brand-500 checked:border-brand-500 transition-all cursor-pointer">
                                <i class="ph-bold ph-check text-white absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 opacity-0 peer-checked:opacity-100 pointer-events-none text-xs"></i>
                            </div>
                            <span class="text-sm font-medium text-slate-600 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <button class="w-full py-3 text-sm font-bold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white bg-slate-100 hover:bg-slate-200 dark:bg-white/5 dark:hover:bg-white/10 rounded-xl transition-colors">
                    Limpiar filtros
                </button>
            </div>
        </aside>

        {{-- RESULTADOS --}}
        <div class="flex-1 min-w-0">

            {{-- Header resultados + ordenar --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-8 gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-1">Resultados</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        <span class="font-bold text-brand-600 dark:text-brand-400">{{ count($canchas) }} canchas</span> encontradas en <span class="text-slate-700 dark:text-slate-300 font-medium">Cusco</span>
                    </p>
                </div>

                <div class="flex items-center gap-3 w-full sm:w-auto">
                    {{-- Filtros móvil --}}
                    <button class="lg:hidden flex-1 flex items-center justify-center gap-2 h-10 px-4 glass rounded-xl text-sm font-medium text-slate-700 dark:text-white hover:bg-slate-50 dark:hover:bg-white/10 transition-colors">
                        <i class="ph-bold ph-faders"></i> Filtros
                    </button>

                    {{-- Ordenar --}}
                    <div class="flex-1 sm:flex-none flex items-center gap-2 glass rounded-xl h-10 px-4 focus-within:border-brand-500 transition-colors relative">
                        <i class="ph-bold ph-arrows-down-up text-slate-400"></i>
                        <select class="text-sm font-medium outline-none text-slate-900 dark:text-white bg-transparent cursor-pointer appearance-none pr-4">
                            <option class="bg-white dark:bg-dark-800">Más cercanos</option>
                            <option class="bg-white dark:bg-dark-800">Menor precio</option>
                        </select>
                        <i class="ph-bold ph-caret-down text-slate-400 pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs"></i>
                    </div>
                </div>
            </div>

            {{-- Grid de canchas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($canchas as $cancha)
                <a href="/canchas/{{ $cancha['id'] }}" class="bg-white dark:bg-dark-900 rounded-3xl overflow-hidden border border-slate-200 dark:border-white/5 hover:border-brand-500/50 group cursor-pointer transition-all duration-300 shadow-sm hover:shadow-xl dark:shadow-none flex flex-col relative block">
                    
                    {{-- Imagen --}}
                    <div class="relative h-48 overflow-hidden shrink-0">
                        <img src="{{ $cancha['imagen'] }}" alt="{{ $cancha['nombre'] }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-dark-900 to-transparent opacity-60 dark:opacity-100"></div>

                        {{-- Badges --}}
                        <div class="absolute top-3 left-3 flex flex-col gap-2">
                            @if($cancha['promo'])
                                <span class="bg-rose-500 text-white text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wide shadow-lg">{{ $cancha['promo'] }}</span>
                            @endif
                            @if($cancha['disponible'])
                                <span class="bg-brand-500 text-white text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wide flex items-center gap-1 shadow-lg">
                                    <i class="ph-bold ph-lightning"></i> Libre Ahora
                                </span>
                            @endif
                        </div>

                        {{-- Favorito --}}
                        <button class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/20 dark:bg-dark-900/40 backdrop-blur-md flex items-center justify-center text-white hover:text-rose-500 hover:bg-white transition-colors z-10">
                            <i class="ph-bold ph-heart"></i>
                        </button>
                        
                        {{-- Avatar Icono --}}
                        <div class="absolute -bottom-5 right-4 w-10 h-10 rounded-xl bg-slate-50 dark:bg-dark-800 border-2 border-white dark:border-dark-900 flex items-center justify-center text-brand-500 dark:text-brand-400 shadow-lg z-10">
                            <i class="ph-bold ph-soccer-ball text-xl"></i>
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="p-5 flex flex-col flex-grow relative">
                        <div class="flex items-center gap-2 text-brand-600 dark:text-brand-400 text-[10px] font-bold uppercase tracking-wider mb-2">
                            <i class="ph-fill ph-users"></i> {{ $cancha['tipo'] }}
                        </div>
                        
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white line-clamp-1 mb-2 group-hover:text-brand-500 transition-colors">
                            {{ $cancha['nombre'] }}
                        </h3>

                        <p class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-1 mb-4">
                            <i class="ph-fill ph-map-pin shrink-0 text-slate-400 dark:text-slate-500"></i>
                            <span class="truncate">{{ $cancha['direccion'] }}</span>
                            <span class="text-slate-300 dark:text-slate-600 shrink-0 mx-1">•</span>
                            <span class="font-medium text-slate-700 dark:text-slate-300 shrink-0">{{ $cancha['distancia'] }}</span>
                        </p>

                        {{-- Tags --}}
                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach(array_slice($cancha['tags'], 0, 2) as $tag)
                            <span class="bg-slate-100 dark:bg-dark-800 text-slate-600 dark:text-slate-400 text-[10px] font-medium uppercase tracking-wide px-2 py-1 rounded-lg border border-slate-200 dark:border-white/5">
                                {{ $tag }}
                            </span>
                            @endforeach
                        </div>

                        {{-- Footer precio --}}
                        <div class="mt-auto flex justify-between items-center pt-4 border-t border-slate-100 dark:border-white/5">
                            <div>
                                <span class="text-xs text-slate-500 block leading-none mb-1">Desde</span>
                                <div class="font-bold text-slate-900 dark:text-white text-lg leading-none">
                                    S/ {{ number_format($cancha['precio'], 2) }}<span class="text-xs font-normal text-slate-500">/hr</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-brand-500 text-white flex items-center justify-center group-hover:bg-brand-600 transition-colors shadow-lg shadow-brand-500/20">
                                    <i class="ph-bold ph-arrow-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            {{-- Paginación --}}
            <div class="mt-12 flex justify-center items-center gap-2">
                <button class="w-10 h-10 flex items-center justify-center rounded-xl glass text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                    <i class="ph-bold ph-caret-left text-lg"></i>
                </button>
                @foreach([1,2,3] as $page)
                <button class="w-10 h-10 flex items-center justify-center rounded-xl text-sm font-bold transition-colors
                    {{ $page === 1 ? 'bg-brand-500 text-white shadow-lg shadow-brand-500/20 border-none' : 'glass text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    {{ $page }}
                </button>
                @endforeach
                <button class="w-10 h-10 flex items-center justify-center rounded-xl glass text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                    <i class="ph-bold ph-caret-right text-lg"></i>
                </button>
            </div>

        </div>
    </div>
</main>

@endsection
