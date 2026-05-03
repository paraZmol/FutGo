@extends('layouts.partner')
@section('title', 'Mis Canchas | FutGo Partner')
@section('page-title', 'Mis Canchas')
@php
    $totalCanchas = $venue?->fields->count() ?? 0;
    $nombreVenue  = $venue?->name ?? 'Mi complejo';
@endphp
@section('page-subtitle', "$nombreVenue · $totalCanchas canchas registradas")

@section('content')

@php
// Imágenes de ejemplo por tipo de cancha
$imagenes = [
    'futbol5'  => 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?w=600&q=80',
    'futbol7'  => 'https://images.unsplash.com/photo-1551280857-2b9ebf262c62?w=600&q=80',
    'futbol11' => 'https://images.unsplash.com/photo-1529900748604-07564a03e7a6?w=600&q=80',
];
$tipoLabel = ['futbol5' => 'Fútbol 5', 'futbol7' => 'Fútbol 7', 'futbol11' => 'Fútbol 11'];
$pastoLabel = ['sintetico' => 'Sintético', 'natural' => 'Natural'];

// Canchas del venue activo
$canchas = collect($venue?->fields ?? [])->map(fn($f) => [
    'id'           => $f->id,
    'nombre'       => $f->name,
    'venue'        => $venue->name,
    'tipo'         => $tipoLabel[$f->sport_type] ?? $f->sport_type,
    'pasto'        => $pastoLabel[$f->surface] ?? $f->surface,
    'techada'      => $f->is_covered,
    'estado'       => $f->status === 'active' ? 'activa' : 'mantenimiento',
    'precio_dia'   => 80.00,
    'precio_noche' => 90.00,
    'ocupacion_hoy'=> 0,
    'reservas_mes' => 0,
    'imagen'       => $imagenes[$f->sport_type] ?? $imagenes['futbol5'],
    'comodidades'  => $f->amenities ?? [],
])->values()->all();
@endphp

{{-- HEADER ACCIONES --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div class="flex items-center gap-3">
        {{-- Filtros rápidos --}}
        @foreach(['Todas', 'Activas', 'Mantenimiento'] as $f)
        @php
            $filterValue = strtolower($f);
            if ($filterValue === 'activas') $filterValue = 'activa';
        @endphp
        <button onclick="filtrarCanchas('{{ $filterValue }}', this)" 
                class="filtro-btn px-4 py-2 rounded-xl text-sm font-semibold transition-all
            {{ $f === 'Todas'
                ? 'bg-brand-500 text-white shadow-md shadow-brand-500/20 border border-transparent'
                : 'bg-white dark:bg-dark-900 border border-slate-200 dark:border-white/10 text-slate-500 dark:text-slate-400 hover:border-brand-500 hover:text-brand-500' }}">
            {{ $f }}
        </button>
        @endforeach
    </div>

    <button onclick="document.getElementById('modal-nueva').classList.remove('hidden')"
            class="bg-brand-500 hover:bg-brand-600 text-white font-bold px-5 py-2.5 rounded-xl flex items-center gap-2 text-sm transition-all hover:scale-105 shadow-lg shadow-brand-500/20 shrink-0">
        <i class="ph-bold ph-plus"></i> Agregar cancha
    </button>
</div>

{{-- GRID DE CANCHAS --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-5" id="canchas-grid">
    @foreach($canchas as $c)
    <div class="cancha-card glass-card rounded-2xl overflow-hidden flex flex-col
        {{ $c['estado'] === 'mantenimiento' ? 'opacity-75' : '' }}"
        data-estado="{{ $c['estado'] }}">

        {{-- Imagen --}}
        <div class="relative h-44 overflow-hidden">
            <img src="{{ $c['imagen'] }}" alt="{{ $c['nombre'] }}"
                 class="w-full h-full object-cover {{ $c['estado'] === 'mantenimiento' ? 'grayscale' : '' }} hover:scale-105 transition-transform duration-500">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent"></div>

            {{-- Estado badge --}}
            <div class="absolute top-3 left-3">
                @if($c['estado'] === 'activa')
                <span class="bg-brand-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span> Activa
                </span>
                @else
                <span class="bg-amber-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full flex items-center gap-1">
                    <i class="ph-bold ph-wrench"></i> Mantenimiento
                </span>
                @endif
            </div>

            {{-- Acciones rápidas --}}
            <div class="absolute top-3 right-3 flex gap-2">
                <button class="w-8 h-8 rounded-xl bg-white/20 backdrop-blur-sm text-white hover:bg-white hover:text-slate-700 transition-colors flex items-center justify-center"
                        title="Editar">
                    <i class="ph-bold ph-pencil text-sm"></i>
                </button>
                <button class="w-8 h-8 rounded-xl bg-white/20 backdrop-blur-sm text-white hover:bg-amber-500 transition-colors flex items-center justify-center"
                        title="{{ $c['estado'] === 'activa' ? 'Poner en mantenimiento' : 'Activar' }}">
                    <i class="ph-bold {{ $c['estado'] === 'activa' ? 'ph-wrench' : 'ph-check' }} text-sm"></i>
                </button>
            </div>

            {{-- Nombre sobre imagen --}}
            <div class="absolute bottom-3 left-4">
                <h3 class="text-white font-extrabold text-xl">{{ $c['nombre'] }}</h3>
                <p class="text-white/70 text-xs flex items-center gap-2 mt-0.5">
                    <span>{{ $c['tipo'] }}</span>
                    <span>·</span>
                    <span>Pasto {{ $c['pasto'] }}</span>
                    @if($c['techada'])
                    <span>·</span>
                    <span class="flex items-center gap-1"><i class="ph-fill ph-house"></i> Techada</span>
                    @endif
                </p>
            </div>
        </div>

        {{-- Info --}}
        <div class="p-5 flex flex-col gap-4">

            {{-- Precios --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-slate-50 dark:bg-white/5 rounded-xl p-3 text-center">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                        <i class="ph-fill ph-sun text-amber-400"></i> Día (07–18h)
                    </p>
                    <p class="text-xl font-extrabold text-slate-900 dark:text-white">
                        S/ {{ number_format($c['precio_dia'], 0) }}
                    </p>
                    <p class="text-[10px] text-slate-400">por hora</p>
                </div>
                <div class="bg-slate-50 dark:bg-white/5 rounded-xl p-3 text-center">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                        <i class="ph-fill ph-moon text-blue-400"></i> Noche (18–23h)
                    </p>
                    <p class="text-xl font-extrabold text-slate-900 dark:text-white">
                        S/ {{ number_format($c['precio_noche'], 0) }}
                    </p>
                    <p class="text-[10px] text-slate-400">por hora</p>
                </div>
            </div>

            {{-- Stats --}}
            <div class="flex items-center gap-4 py-3 border-t border-slate-100 dark:border-white/5">
                <div class="flex-1">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Ocupación hoy</p>
                    <div class="flex items-center gap-2">
                        <div class="flex-1 h-2 bg-slate-100 dark:bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all
                                {{ $c['ocupacion_hoy'] >= 80 ? 'bg-brand-500' : ($c['ocupacion_hoy'] >= 50 ? 'bg-amber-500' : 'bg-slate-300 dark:bg-slate-600') }}"
                                 style="width: {{ $c['ocupacion_hoy'] }}%"></div>
                        </div>
                        <span class="text-sm font-bold {{ $c['ocupacion_hoy'] >= 80 ? 'text-brand-500' : ($c['ocupacion_hoy'] >= 50 ? 'text-amber-500' : 'text-slate-400') }}">
                            {{ $c['ocupacion_hoy'] }}%
                        </span>
                    </div>
                </div>
                <div class="text-center shrink-0">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Reservas mes</p>
                    <p class="text-lg font-extrabold text-slate-900 dark:text-white">{{ $c['reservas_mes'] }}</p>
                </div>
            </div>

            {{-- Comodidades --}}
            <div class="flex flex-wrap gap-2">
                @foreach($c['comodidades'] as $com)
                <span class="bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-500 dark:text-slate-400 text-[10px] font-semibold uppercase tracking-wide px-2.5 py-1 rounded-lg">
                    {{ $com }}
                </span>
                @endforeach
            </div>

            {{-- Acciones --}}
            <div class="flex gap-2 pt-1">
                <a href="/partner/horarios"
                   class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:border-brand-500 hover:text-brand-500 transition-colors">
                    <i class="ph-bold ph-clock"></i> Horarios
                </a>
                <a href="/partner/reservas"
                   class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl bg-brand-500/10 border border-brand-500/20 text-sm font-semibold text-brand-600 dark:text-brand-400 hover:bg-brand-500 hover:text-white transition-colors">
                    <i class="ph-bold ph-calendar-check"></i> Ver reservas
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ============================================================
     MODAL NUEVA CANCHA
============================================================ --}}
<div id="modal-nueva" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="document.getElementById('modal-nueva').classList.add('hidden')"></div>

    <div class="relative glass-card rounded-3xl p-6 w-full max-w-lg shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">Nueva cancha</h2>
            <button onclick="document.getElementById('modal-nueva').classList.add('hidden')"
                    class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-white/10 flex items-center justify-center text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors">
                <i class="ph-bold ph-x"></i>
            </button>
        </div>

        <form class="space-y-4">

            <div>
                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Nombre</label>
                <input type="text" placeholder="Ej: Cancha 5"
                       class="w-full border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm bg-white dark:bg-white/5 text-slate-900 dark:text-white outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition-all placeholder-slate-400">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="relative">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Tipo</label>
                    <select class="w-full border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm bg-white dark:bg-dark-800 text-slate-900 dark:text-white outline-none focus:border-brand-500 transition-all appearance-none cursor-pointer [&>option]:text-slate-900 dark:[&>option]:text-white">
                        <option>Fútbol 5</option>
                        <option>Fútbol 7</option>
                        <option>Fútbol 11</option>
                    </select>
                    <i class="ph-bold ph-caret-down text-slate-400 absolute right-4 bottom-4 pointer-events-none"></i>
                </div>
                <div class="relative">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Pasto</label>
                    <select class="w-full border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm bg-white dark:bg-dark-800 text-slate-900 dark:text-white outline-none focus:border-brand-500 transition-all appearance-none cursor-pointer [&>option]:text-slate-900 dark:[&>option]:text-white">
                        <option>Sintético</option>
                        <option>Natural</option>
                    </select>
                    <i class="ph-bold ph-caret-down text-slate-400 absolute right-4 bottom-4 pointer-events-none"></i>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Precio día (S/)</label>
                    <input type="number" placeholder="80" min="0"
                           class="w-full border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm bg-white dark:bg-white/5 text-slate-900 dark:text-white outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition-all placeholder-slate-400">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Precio noche (S/)</label>
                    <input type="number" placeholder="90" min="0"
                           class="w-full border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm bg-white dark:bg-white/5 text-slate-900 dark:text-white outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition-all placeholder-slate-400">
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3 block">Comodidades</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach(['Iluminación LED','Vestuarios','Estacionamiento','Bar / Cafetería','WiFi','Duchas','Techada'] as $com)
                    <label class="flex items-center gap-2.5 cursor-pointer group p-2 rounded-xl hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                        <div class="relative flex items-center shrink-0">
                            <input type="checkbox" class="peer appearance-none w-5 h-5 border-2 border-slate-300 dark:border-white/20 rounded-md bg-white dark:bg-white/5 checked:bg-brand-500 checked:border-brand-500 transition-all cursor-pointer">
                            <i class="ph-bold ph-check text-white absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 opacity-0 peer-checked:opacity-100 pointer-events-none text-xs"></i>
                        </div>
                        <span class="text-sm text-slate-600 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">{{ $com }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button"
                        onclick="document.getElementById('modal-nueva').classList.add('hidden')"
                        class="flex-1 py-3 rounded-xl border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="flex-1 py-3 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-bold text-sm transition-all hover:scale-[1.02] shadow-lg shadow-brand-500/20">
                    Guardar cancha
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function filtrarCanchas(estado, btn) {
        // Actualizar diseño de botones
        document.querySelectorAll('.filtro-btn').forEach(b => {
            b.classList.remove('bg-brand-500', 'text-white', 'shadow-md', 'shadow-brand-500/20', 'border-transparent');
            b.classList.add('bg-white', 'dark:bg-dark-900', 'border', 'border-slate-200', 'dark:border-white/10', 'text-slate-500', 'dark:text-slate-400', 'hover:border-brand-500', 'hover:text-brand-500');
            
            if (b === btn) {
                b.classList.add('bg-brand-500', 'text-white', 'shadow-md', 'shadow-brand-500/20', 'border-transparent');
                b.classList.remove('bg-white', 'dark:bg-dark-900', 'border', 'border-slate-200', 'dark:border-white/10', 'text-slate-500', 'dark:text-slate-400', 'hover:border-brand-500', 'hover:text-brand-500');
            }
        });

        // Filtrar tarjetas
        const cards = document.querySelectorAll('.cancha-card');
        cards.forEach(card => {
            if (estado === 'todas' || card.dataset.estado === estado) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>
@endpush

@endsection
