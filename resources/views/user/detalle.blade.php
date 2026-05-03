@extends('layouts.app')
@section('title', 'Complejo Deportivo El 10 | FutGo')

@section('content')

@php
// $venue y $fecha vienen del controlador (routes/web.php GET /canchas/{id})
// Imágenes por tipo hasta tener fotos reales cargadas
$imgPool = [
    'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?w=800&q=80',
    'https://images.unsplash.com/photo-1529900748604-07564a03e7a6?w=800&q=80',
    'https://images.unsplash.com/photo-1551280857-2b9ebf262c62?w=800&q=80',
    'https://images.unsplash.com/photo-1518605368461-1ee7e53f191b?w=800&q=80',
];

// Mapeo de comodidades a íconos
$amenityIcons = [
    'Pasto sintético'  => 'ph-shield-check',
    'Iluminación LED'  => 'ph-lightbulb',
    'Vestuarios'       => 'ph-t-shirt',
    'Estacionamiento'  => 'ph-car',
    'Bar / Cafetería'  => 'ph-coffee',
    'Bar'              => 'ph-coffee',
    'WiFi'             => 'ph-wifi-high',
    'WiFi gratis'      => 'ph-wifi-high',
    'Duchas'           => 'ph-drop',
    'Techada'          => 'ph-house',
    'Pasto natural'    => 'ph-tree',
];

// Primera cancha activa para precios de referencia
$primeraCanchaActiva = $venue->fields->firstWhere('status','active');
$horarioRef = $primeraCanchaActiva
    ? $primeraCanchaActiva->operatingHours->sortBy('day_of_week')->first()
    : null;
$precioBase   = $horarioRef ? (float)$horarioRef->price_day   : 70.00;
$precioNoche  = $horarioRef ? (float)$horarioRef->price_night : 85.00;
$anticipoBase = $horarioRef ? (float)$horarioRef->deposit_amount : round($precioBase * 0.35, 2);

// Comodidades: unión de todas las canchas del venue
$todasComodidades = collect($venue->fields->flatMap(fn($f) => $f->amenities ?? [])->unique()->values());
$comodidades = $todasComodidades->map(fn($label) => [
    'icon'  => $amenityIcons[$label] ?? 'ph-check-circle',
    'label' => $label,
])->values()->all();

// Slots del día seleccionado para TODAS las canchas
$fechaSeleccionada = $fecha ?? today()->toDateString();
$slotsConFechas = $venue->fields->flatMap(fn($f) => $f->slots)->sortBy('starts_at');

// Agrupar slots por cancha para la grilla
$slotsPorCancha = $venue->fields->mapWithKeys(fn($f) => [
    $f->name => $f->slots->sortBy('starts_at')
]);

$cancha = [
    'id'               => $venue->id,
    'nombre'           => $venue->name,
    'tipo'             => $venue->fields->pluck('sport_type')->unique()->map(fn($t) => str_replace(['futbol5','futbol7','futbol11'],['Fútbol 5','Fútbol 7','Fútbol 11'],$t))->implode(' / '),
    'direccion'        => $venue->address . ', ' . $venue->district,
    'distancia'        => $venue->city?->name ?? 'Perú',
    'precio'           => $precioBase,
    'anticipo'         => $anticipoBase,
    'rating'           => 4.8,
    'reviews'          => rand(40, 200),
    'descripcion'      => $venue->description ?? 'Complejo deportivo con canchas de calidad. Reservá online y asegurá tu turno.',
    'fotos'            => $imgPool,
    'comodidades'      => $comodidades,
    'horario_apertura' => $horarioRef?->opens_at  ? substr($horarioRef->opens_at,  0, 5) : '07:00',
    'horario_cierre'   => $horarioRef?->closes_at ? substr($horarioRef->closes_at, 0, 5) : '22:00',
    'canchas_count'    => $venue->fields->count(),
];

// Slots reales del día para la grilla (primera cancha por defecto)
$slots = $primeraCanchaActiva
    ? $primeraCanchaActiva->slots->sortBy('starts_at')->map(fn($s) => [
        'id'     => $s->id,
        'hora'   => $s->starts_at->format('H:i'),
        'estado' => $s->status,
        'precio' => (float) $s->unit_price,
      ])->values()->all()
    : [];
@endphp

{{-- BREADCRUMB --}}
<div class="bg-white/50 dark:bg-dark-900/50 backdrop-blur-md border-b border-slate-200 dark:border-white/10 sticky top-16 md:top-20 z-30 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center gap-2 text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400">
        <a href="/" class="hover:text-brand-500 transition-colors flex items-center gap-1"><i class="ph-bold ph-house"></i> Inicio</a>
        <i class="ph-bold ph-caret-right text-slate-300 dark:text-slate-600"></i>
        <a href="/canchas" class="hover:text-brand-500 transition-colors">Canchas</a>
        <i class="ph-bold ph-caret-right text-slate-300 dark:text-slate-600"></i>
        <span class="text-slate-900 dark:text-white truncate">{{ $cancha['nombre'] }}</span>
    </div>
</div>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-36 lg:pb-8">
    <div class="flex flex-col lg:flex-row gap-8">

        {{-- ============================================
             COLUMNA IZQUIERDA
        ============================================ --}}
        <div class="flex-1 min-w-0 space-y-8">

            {{-- GALERÍA --}}
            <div class="grid grid-cols-4 grid-rows-2 gap-2 h-64 sm:h-96 rounded-3xl overflow-hidden shadow-lg">
                {{-- Foto principal --}}
                <div class="col-span-4 sm:col-span-2 row-span-2 relative group cursor-pointer overflow-hidden">
                    <img src="{{ $cancha['fotos'][0] }}" alt="{{ $cancha['nombre'] }}"
                         class="w-full h-full object-cover group-hover:scale-105 group-hover:brightness-90 transition-all duration-500">
                </div>
                {{-- Fotos secundarias (solo desktop) --}}
                @foreach(array_slice($cancha['fotos'], 1, 3) as $i => $foto)
                <div class="hidden sm:block relative group cursor-pointer overflow-hidden {{ $i === 2 ? 'relative' : '' }}">
                    <img src="{{ $foto }}" alt="Foto {{ $i+2 }}"
                         class="w-full h-full object-cover group-hover:scale-105 group-hover:brightness-90 transition-all duration-500">
                    @if($i === 2)
                    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center group-hover:bg-slate-900/80 transition-colors">
                        <span class="text-white font-bold text-lg flex items-center gap-2"><i class="ph-bold ph-image"></i> +{{ count($cancha['fotos']) - 3 }}</span>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            {{-- HEADER --}}
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="bg-brand-500/10 text-brand-600 dark:text-brand-400 text-xs font-bold px-3 py-1 rounded-xl border border-brand-500/20">
                            {{ $cancha['tipo'] }}
                        </span>
                        <span class="bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-300 text-xs font-semibold px-3 py-1 rounded-xl">
                            {{ $cancha['canchas_count'] }} canchas
                        </span>
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ $cancha['nombre'] }}</h1>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-2 flex items-center gap-1.5 font-medium">
                        <i class="ph-fill ph-map-pin text-brand-500 text-lg"></i>
                        {{ $cancha['direccion'] }}
                        <span class="text-slate-300 dark:text-slate-600 mx-1">·</span>
                        <span class="text-brand-600 dark:text-brand-400">{{ $cancha['distancia'] }}</span>
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">

                    {{-- Compartir / Favorito --}}
                    <div class="flex flex-col gap-2">
                        <button class="w-10 h-10 rounded-full border border-slate-200 dark:border-white/10 bg-white dark:bg-dark-800 hover:border-brand-500 hover:text-brand-500 dark:text-slate-300 dark:hover:text-brand-400 transition-colors flex items-center justify-center shadow-sm">
                            <i class="ph-bold ph-heart text-lg"></i>
                        </button>
                        <button class="w-10 h-10 rounded-full border border-slate-200 dark:border-white/10 bg-white dark:bg-dark-800 hover:border-brand-500 hover:text-brand-500 dark:text-slate-300 dark:hover:text-brand-400 transition-colors flex items-center justify-center shadow-sm">
                            <i class="ph-bold ph-share-network text-lg"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- DESCRIPCIÓN --}}
            <div class="glass-card rounded-3xl p-6 sm:p-8">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="ph-fill ph-info text-brand-500 text-xl"></i> Sobre el complejo
                </h2>
                <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed">{{ $cancha['descripcion'] }}</p>

                <div class="flex flex-wrap gap-4 mt-6 pt-6 border-t border-slate-200 dark:border-white/10">
                    <div class="flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-white/5 px-3 py-2 rounded-xl">
                        <i class="ph-fill ph-clock text-brand-500 text-lg"></i>
                        <span>{{ $cancha['horario_apertura'] }} – {{ $cancha['horario_cierre'] }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-white/5 px-3 py-2 rounded-xl">
                        <i class="ph-fill ph-soccer-ball text-brand-500 text-lg"></i>
                        <span>{{ $cancha['canchas_count'] }} canchas disponibles</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-white/5 px-3 py-2 rounded-xl">
                        <i class="ph-fill ph-money text-brand-500 text-lg"></i>
                        <span>Anticipo S/ {{ number_format($cancha['anticipo'], 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- COMODIDADES --}}
            <div class="glass-card rounded-3xl p-6 sm:p-8">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                    <i class="ph-fill ph-sparkle text-brand-500 text-xl"></i> Comodidades
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach($cancha['comodidades'] as $item)
                    <div class="flex flex-col items-center justify-center gap-2 p-4 bg-white/50 dark:bg-dark-800/50 border border-slate-100 dark:border-white/5 rounded-2xl hover:border-brand-500/30 transition-colors text-center group">
                        <i class="ph-fill {{ $item['icon'] }} text-2xl text-brand-500 group-hover:scale-110 transition-transform"></i>
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $item['label'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- GRILLA DE HORARIOS --}}
            <div class="glass-card rounded-3xl p-6 sm:p-8 relative overflow-hidden" id="reservar-section">
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-brand-500/10 rounded-full blur-3xl pointer-events-none"></div>
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 relative z-10">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="ph-fill ph-calendar-check text-brand-500 text-2xl"></i> Horarios disponibles
                    </h2>
                    {{-- Selector de fecha --}}
                    <div class="flex items-center gap-2 bg-white dark:bg-dark-800 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2.5 shadow-sm">
                        <i class="ph-bold ph-calendar-blank text-brand-500"></i>
                        <input type="date" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}"
                               class="text-sm font-semibold bg-transparent outline-none text-slate-700 dark:text-white cursor-pointer w-full">
                    </div>
                </div>

                {{-- Leyenda --}}
                <div class="flex items-center gap-6 mb-6 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 relative z-10">
                    <span class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-brand-500 shadow-sm shadow-brand-500/50"></span> Disponible
                    </span>
                    <span class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-slate-200 dark:bg-white/10"></span> Ocupado
                    </span>
                    <span class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-brand-900 dark:bg-brand-400"></span> Seleccionado
                    </span>
                </div>

                {{-- Slots --}}
                <div class="grid grid-cols-4 sm:grid-cols-6 lg:grid-cols-8 gap-3 relative z-10">
                    @foreach($slots as $slot)
                    @if($slot['estado'] === 'available')
                    <button onclick="toggleSlot(this)"
                            data-hora="{{ $slot['hora'] }}"
                            data-precio="{{ $slot['precio'] }}"
                            class="slot-btn border-2 border-brand-200 dark:border-brand-500/30 bg-brand-50 dark:bg-brand-500/10 hover:bg-brand-500 hover:text-white hover:border-brand-500 text-brand-700 dark:text-brand-300 rounded-2xl py-3 text-center transition-all duration-300 cursor-pointer shadow-sm active:scale-95 group flex flex-col items-center justify-center">
                        <div class="text-sm font-black">{{ $slot['hora'] }}</div>
                        <div class="text-[10px] font-bold opacity-80 group-hover:opacity-100 mt-0.5">S/ {{ $slot['precio'] }}</div>
                    </button>
                    @else
                    <div class="border-2 border-slate-100 dark:border-white/5 bg-slate-50 dark:bg-white/5 text-slate-400 dark:text-slate-500 rounded-2xl py-3 text-center cursor-not-allowed flex flex-col items-center justify-center opacity-70">
                        <div class="text-sm font-bold line-through decoration-slate-300 dark:decoration-slate-600">{{ $slot['hora'] }}</div>
                        <div class="text-[10px] font-bold mt-0.5">Ocupado</div>
                    </div>
                    @endif
                    @endforeach
                </div>

                {{-- Resumen selección (Hidden by default) --}}
                <div id="seleccion-resumen" class="hidden mt-6 p-5 bg-brand-50 dark:bg-brand-500/10 border border-brand-200 dark:border-brand-500/30 rounded-2xl relative z-10">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-brand-600 dark:text-brand-400 uppercase tracking-wider mb-1">Horarios seleccionados</p>
                            <p id="seleccion-horas" class="text-lg font-black text-brand-800 dark:text-brand-300"></p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-brand-600 dark:text-brand-400 uppercase tracking-wider mb-1">Total</p>
                            <p id="seleccion-total" class="text-2xl font-black text-brand-800 dark:text-brand-300"></p>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- fin columna izquierda --}}

        {{-- ============================================
             CARD RESERVA (sticky desktop)
        ============================================ --}}
        <aside class="lg:w-96 shrink-0 hidden lg:block self-start sticky top-28">
            <div class="glass-card rounded-3xl border border-slate-200 dark:border-white/10 shadow-xl p-6">

                {{-- Precio Header --}}
                <div class="flex items-baseline justify-between mb-6 pb-6 border-b border-slate-100 dark:border-white/10">
                    <div>
                        <span class="text-3xl font-black text-slate-900 dark:text-white">
                            S/ {{ number_format($cancha['precio'], 2) }}
                        </span>
                        <span class="text-slate-400 text-sm font-medium"> / hora</span>
                    </div>

                </div>



                {{-- Horario Info --}}
                <div class="mb-6">
                    <label class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2 block">Horario</label>
                    <div id="horario-selected-display"
                         class="border-2 border-dashed border-slate-300 dark:border-white/20 rounded-2xl px-4 py-4 text-sm font-bold text-slate-400 dark:text-slate-500 text-center transition-colors">
                        Aún no seleccionaste horario
                    </div>
                </div>

                {{-- Desglose precios --}}
                <div class="space-y-3 text-sm mb-6 bg-slate-50 dark:bg-white/5 p-4 rounded-2xl border border-slate-100 dark:border-white/5">
                    <div class="flex justify-between text-slate-600 dark:text-slate-300 font-medium">
                        <span id="subtotal-label">S/ {{ number_format($cancha['precio'], 2) }} x 1 hora</span>
                        <span id="subtotal-precio" class="font-bold text-slate-900 dark:text-white">S/ {{ number_format($cancha['precio'], 2) }}</span>
                    </div>
                    <div class="border-t border-slate-200 dark:border-white/10 my-2"></div>
                    <div class="flex justify-between text-brand-600 dark:text-brand-400 font-bold">
                        <span>Anticipo (Pagar ahora)</span>
                        <span id="anticipo-precio">S/ {{ number_format($cancha['anticipo'], 2) }}</span>
                    </div>
                    <div class="flex justify-between text-slate-500 dark:text-slate-400 text-xs font-semibold">
                        <span>Saldo en cancha</span>
                        <span id="saldo-precio">S/ {{ number_format($cancha['precio'] - $cancha['anticipo'], 2) }}</span>
                    </div>
                </div>

                <div class="flex justify-between items-center font-black text-slate-900 dark:text-white mb-6 text-xl">
                    <span>Total a pagar</span>
                    <span id="total-precio">S/ {{ number_format($cancha['precio'], 2) }}</span>
                </div>

                {{-- Botón reservar --}}
                @auth
                <button onclick="irAlCheckout()"
                        class="w-full bg-brand-500 hover:bg-brand-600 text-white font-bold py-4 rounded-2xl flex items-center justify-center gap-2 transition-all hover:scale-[1.02] active:scale-95 shadow-lg shadow-brand-500/30 text-lg group">
                    <i class="ph-bold ph-check-circle text-xl group-hover:rotate-12 transition-transform"></i>
                    Confirmar Reserva
                </button>
                @else
                <a href="/login?redirect={{ urlencode(request()->fullUrl()) }}"
                   class="w-full bg-brand-500 hover:bg-brand-600 text-white font-bold py-4 rounded-2xl flex items-center justify-center gap-2 transition-all hover:scale-[1.02] active:scale-95 shadow-lg shadow-brand-500/30 text-lg">
                    <i class="ph-bold ph-sign-in text-xl"></i>
                    Iniciá sesión para reservar
                </a>
                <p class="text-center text-xs text-slate-400 mt-2">
                    ¿No tenés cuenta?
                    <a href="/registro" class="text-brand-500 font-bold hover:underline">Registrate gratis</a>
                </p>
                @endauth

                <div class="flex items-start gap-2 mt-4 text-[11px] text-slate-500 dark:text-slate-400 leading-tight">
                    <i class="ph-fill ph-shield-check text-brand-500 text-lg shrink-0"></i>
                    <p>Tu pago es seguro y procesado mediante MercadoPago. Se cobrará solo el anticipo para asegurar tu lugar.</p>
                </div>
            </div>
        </aside>

    </div>{{-- fin flex --}}
</main>

{{-- BARRA MÓVIL --}}
<div class="lg:hidden fixed bottom-24 left-0 w-full bg-white/80 dark:bg-dark-900/80 backdrop-blur-lg border-t border-slate-200 dark:border-white/10 px-4 py-3 z-[100] shadow-[0_-4px_20px_rgba(0,0,0,0.1)]">
    <div>
        <div class="text-xl font-extrabold text-slate-900 dark:text-white" id="mobile-total">
            S/ {{ number_format($cancha['precio'], 2) }}
            <span class="text-xs font-semibold text-slate-400 dark:text-slate-500">total</span>
        </div>
        <div class="text-[10px] font-bold text-brand-500 uppercase tracking-wider" id="mobile-anticipo">Abono S/ {{ number_format($cancha['anticipo'], 2) }}</div>
    </div>
    @auth
    <button onclick="irAlCheckout()"
            class="bg-brand-500 hover:bg-brand-600 text-white font-bold px-6 py-3.5 rounded-2xl flex items-center gap-2 transition-all shadow-lg shadow-brand-500/30 active:scale-95 text-sm">
        Reservar <i class="ph-bold ph-arrow-right"></i>
    </button>
    @else
    <a href="/login?redirect={{ urlencode(request()->fullUrl()) }}"
       class="bg-brand-500 hover:bg-brand-600 text-white font-bold px-6 py-3.5 rounded-2xl flex items-center gap-2 transition-all shadow-lg shadow-brand-500/30 active:scale-95 text-sm">
        <i class="ph-bold ph-sign-in"></i> Ingresá para reservar
    </a>
    @endauth
</div>

@push('scripts')
<script>
    const selected = new Map(); // hora -> precio
    const precioBase   = {{ $cancha['precio'] }};
    const anticipoBase = {{ $cancha['anticipo'] }};

    // Devuelve la hora como número entero (ej: "08:00" → 8)
    const toHour = h => parseInt(h.split(':')[0]);

    // Verifica que las horas seleccionadas sean contiguas
    function esContiguo(nuevaHora) {
        if (selected.size === 0) return true;
        const horas = Array.from(selected.keys()).map(toHour).sort((a,b) => a-b);
        const nueva = toHour(nuevaHora);
        // La nueva hora debe ser inmediatamente antes o después del bloque actual
        return nueva === horas[0] - 1 || nueva === horas[horas.length - 1] + 1;
    }

    // Marca visualmente los slots no contiguos como deshabilitados
    function actualizarEstadoBotones() {
        document.querySelectorAll('.slot-btn').forEach(btn => {
            const hora = btn.dataset.hora;
            if (selected.has(hora)) return; // ya seleccionado, no tocar

            if (selected.size === 0 || selected.size >= 4) {
                // Sin selección: todos habilitados | 4 slots: todos deshabilitados
                btn.disabled = selected.size >= 4;
                btn.classList.toggle('opacity-40', selected.size >= 4);
                btn.classList.toggle('cursor-not-allowed', selected.size >= 4);
            } else {
                const contiguo = esContiguo(hora);
                btn.disabled = !contiguo;
                btn.classList.toggle('opacity-40', !contiguo);
                btn.classList.toggle('cursor-not-allowed', !contiguo);
            }
        });
    }

    function toggleSlot(btn) {
        const hora   = btn.dataset.hora;
        const precio = parseFloat(btn.dataset.precio);

        if (selected.has(hora)) {
            // Deseleccionar — solo permitir quitar los extremos del bloque
            const horas = Array.from(selected.keys()).map(toHour).sort((a,b) => a-b);
            const h = toHour(hora);
            if (h !== horas[0] && h !== horas[horas.length - 1]) {
                // Es un slot del medio — no se puede deseleccionar sin romper la cadena
                return;
            }
            selected.delete(hora);
            btn.classList.remove('bg-brand-600', 'text-white', 'border-brand-600', 'dark:bg-brand-500');
            btn.classList.add('bg-brand-50', 'text-brand-700', 'border-brand-200', 'dark:bg-brand-500/10', 'dark:text-brand-300', 'dark:border-brand-500/30');
        } else {
            if (selected.size >= 4) return;
            if (!esContiguo(hora)) return; // no contiguo, ignorar
            selected.set(hora, precio);
            btn.classList.add('bg-brand-600', 'text-white', 'border-brand-600', 'dark:bg-brand-500');
            btn.classList.remove('bg-brand-50', 'text-brand-700', 'border-brand-200', 'dark:bg-brand-500/10', 'dark:text-brand-300', 'dark:border-brand-500/30');
        }

        actualizarEstadoBotones();
        updateResumen();
    }

    function updateResumen() {
        const resumen  = document.getElementById('seleccion-resumen');
        const display  = document.getElementById('horario-selected-display');
        
        const totalPrecioEl = document.getElementById('total-precio');
        const subtotalEl = document.getElementById('subtotal-precio');
        const subtotalLabel = document.getElementById('subtotal-label');
        const anticipoEl = document.getElementById('anticipo-precio');
        const saldoEl = document.getElementById('saldo-precio');

        const mobileTotal = document.getElementById('mobile-total');
        const mobileAnticipo = document.getElementById('mobile-anticipo');
        const mobileBtn = document.getElementById('mobile-btn');

        if (selected.size === 0) {
            resumen.classList.add('hidden');
            display.textContent = 'Aún no seleccionaste horario';
            display.classList.add('text-slate-400', 'border-dashed');
            display.classList.remove('text-brand-700', 'dark:text-brand-400', 'border-brand-300', 'border-solid', 'bg-brand-50', 'dark:bg-brand-500/10');
            
            // Reset to 1 hour default
            subtotalLabel.textContent = `S/ ${precioBase.toFixed(2)} x 1 hora`;
            subtotalEl.textContent = `S/ ${precioBase.toFixed(2)}`;
            totalPrecioEl.textContent = `S/ ${precioBase.toFixed(2)}`;
            anticipoEl.textContent = `S/ ${anticipoBase.toFixed(2)}`;
            saldoEl.textContent = `S/ ${(precioBase - anticipoBase).toFixed(2)}`;
            
            mobileTotal.innerHTML = `S/ ${precioBase.toFixed(2)} <span class="text-xs font-semibold text-slate-400 dark:text-slate-500">total</span>`;
            mobileAnticipo.textContent = `Abono S/ ${anticipoBase.toFixed(2)}`;
            
            mobileBtn.href = "#reservar-section";
            mobileBtn.innerHTML = 'Elegir Horario <i class="ph-bold ph-arrow-right"></i>';
            
            return;
        }

        const horasArr = Array.from(selected.keys()).sort();
        const total    = Array.from(selected.values()).reduce((a, b) => a + b, 0);
        const horasCount = selected.size;
        const totalAnticipo = anticipoBase * horasCount;
        const saldo = total - totalAnticipo;

        resumen.classList.remove('hidden');
        document.getElementById('seleccion-horas').textContent = horasArr.join(' · ');
        document.getElementById('seleccion-total').textContent = 'S/ ' + total.toFixed(2);

        display.textContent = horasArr.join(' · ');
        display.classList.remove('text-slate-400', 'border-dashed');
        display.classList.add('text-brand-700', 'dark:text-brand-400', 'border-brand-300', 'border-solid', 'bg-brand-50', 'dark:bg-brand-500/10');

        subtotalLabel.textContent = `Varias horas seleccionadas x ${horasCount}`;
        subtotalEl.textContent = 'S/ ' + total.toFixed(2);
        totalPrecioEl.textContent = 'S/ ' + total.toFixed(2);
        anticipoEl.textContent = 'S/ ' + totalAnticipo.toFixed(2);
        saldoEl.textContent = 'S/ ' + saldo.toFixed(2);

        mobileTotal.innerHTML = `S/ ${total.toFixed(2)} <span class="text-xs font-semibold text-slate-400 dark:text-slate-500">total</span>`;
        mobileAnticipo.textContent = `Abono S/ ${totalAnticipo.toFixed(2)}`;

        mobileBtn.href = "/checkout";
        mobileBtn.innerHTML = 'Confirmar Reserva <i class="ph-bold ph-check-circle"></i>';
    }

    function irAlCheckout() {
        if (selected.size === 0) {
            document.getElementById('reservar-section')?.scrollIntoView({ behavior: 'smooth' });
            const grilla = document.getElementById('reservar-section');
            if (grilla) {
                grilla.classList.add('ring-2','ring-brand-500','ring-offset-2','rounded-2xl');
                setTimeout(() => grilla.classList.remove('ring-2','ring-brand-500','ring-offset-2','rounded-2xl'), 1500);
            }
            return;
        }
        // POST seguro — los slot IDs van en el body, no en la URL
        const slotIds = Array.from(selected.keys()).sort();
        const total   = Array.from(selected.values()).reduce((a,b) => a+b, 0).toFixed(2);
        const form    = document.getElementById('form-checkout-hidden');
        document.getElementById('checkout-slots').value  = JSON.stringify(slotIds);
        document.getElementById('checkout-total').value  = total;
        document.getElementById('checkout-venue').value  = '{{ $venue->id }}';
        form.submit();
    }
</script>
@endpush

{{-- Formulario oculto para POST seguro al checkout --}}
<form id="form-checkout-hidden" action="/checkout" method="POST" class="hidden">
    @csrf
    <input type="hidden" id="checkout-slots" name="slots">
    <input type="hidden" id="checkout-total" name="total">
    <input type="hidden" id="checkout-venue" name="venue_id">
</form>

@endsection
