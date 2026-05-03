@extends('layouts.app')
@section('title', 'Mi Perfil | FutGo')

@section('content')

@php
$user = Auth::user();
// $stats y $reservas_recientes vienen del controlador
$stats            = $stats ?? ['partidos' => 0, 'canchas' => 0, 'horas' => 0];
$reservas_recientes = $reservas_recientes ?? collect();

$imgDefault = [
    'futbol5'  => 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?w=100&q=80',
    'futbol7'  => 'https://images.unsplash.com/photo-1551280857-2b9ebf262c62?w=100&q=80',
    'futbol11' => 'https://images.unsplash.com/photo-1529900748604-07564a03e7a6?w=100&q=80',
];
@endphp

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- HEADER PERFIL --}}
    <div class="glass-card rounded-3xl p-6 sm:p-8 relative overflow-hidden">
        {{-- Patrón de fondo sutil --}}
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-brand-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 relative z-10">

            {{-- Avatar --}}
            <div class="relative shrink-0 group cursor-pointer">
                <div class="w-28 h-28 rounded-3xl overflow-hidden ring-4 ring-brand-500/20 shadow-xl group-hover:ring-brand-500/50 transition-all duration-300">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=22c55e&color=fff&size=200"
                         alt="Avatar" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                </div>
                <button class="absolute -bottom-2 -right-2 w-10 h-10 rounded-2xl bg-white dark:bg-dark-800 text-slate-700 dark:text-white flex items-center justify-center shadow-lg border border-slate-200 dark:border-white/10 hover:text-brand-500 dark:hover:text-brand-400 hover:-translate-y-1 transition-all">
                    <i class="ph-bold ph-camera text-lg"></i>
                </button>
            </div>

            {{-- Info --}}
            <div class="flex-1 text-center sm:text-left mt-2 sm:mt-0">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-2">
                    <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ $user->name }}</h1>
                    <span class="inline-flex items-center gap-1.5 bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 text-xs font-bold px-3 py-1.5 rounded-xl border border-brand-200 dark:border-brand-500/20 shadow-sm w-max mx-auto sm:mx-0">
                        <i class="ph-fill ph-shield-check text-sm"></i> {{ 'Jugador' }}
                    </span>
                </div>
                <p class="text-slate-500 dark:text-slate-400 text-sm mb-6 font-medium">
                    <i class="ph-fill ph-map-pin text-slate-400 dark:text-slate-500 mr-1"></i>{{ $user->city?->name ?? 'Perú' }}
                    <span class="mx-2 text-slate-300 dark:text-slate-600">·</span>
                    <i class="ph-fill ph-calendar-blank text-slate-400 dark:text-slate-500 mr-1"></i>Miembro desde {{ $user->created_at->format('M Y') }}
                </p>

                {{-- Stats --}}
                <div class="flex justify-center sm:justify-start gap-8">
                    <div class="text-center group">
                        <div class="text-2xl font-black text-slate-900 dark:text-white group-hover:text-brand-500 transition-colors">{{ $stats['partidos'] }}</div>
                        <div class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Partidos</div>
                    </div>
                    <div class="w-px bg-slate-200 dark:bg-white/10"></div>
                    <div class="text-center group">
                        <div class="text-2xl font-black text-slate-900 dark:text-white group-hover:text-blue-500 transition-colors">{{ $stats['canchas'] }}</div>
                        <div class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Canchas</div>
                    </div>
                    <div class="w-px bg-slate-200 dark:bg-white/10"></div>
                    <div class="text-center group">
                        <div class="text-2xl font-black text-slate-900 dark:text-white group-hover:text-purple-500 transition-colors">{{ $stats['horas'] }}h</div>
                        <div class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Jugadas</div>
                    </div>
                </div>
            </div>

            {{-- Botón editar (Desktop) --}}
            <button class="hidden sm:flex items-center gap-2 px-5 py-2.5 rounded-2xl glass border border-slate-200 dark:border-white/10 text-sm font-bold text-slate-700 dark:text-white hover:border-brand-500 dark:hover:border-brand-500 hover:text-brand-600 dark:hover:text-brand-400 shadow-sm transition-all hover:scale-105 active:scale-95 shrink-0">
                <i class="ph-bold ph-pencil-simple"></i> Editar Perfil
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- DATOS PERSONALES --}}
        <div class="glass-card rounded-3xl p-6 md:p-8 space-y-4">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="ph-fill ph-address-book text-brand-500 text-xl"></i> Contacto
                </h2>
                <button class="sm:hidden text-xs text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 font-bold transition-colors flex items-center gap-1">
                    <i class="ph-bold ph-pencil-simple"></i> Editar
                </button>
            </div>

            <div class="space-y-1">
                @foreach([
                    ['icon' => 'ph-user',         'label' => 'Nombre Completo', 'value' => $user->name],
                    ['icon' => 'ph-envelope',      'label' => 'Correo Electrónico', 'value' => $user->email],
                    ['icon' => 'ph-phone',         'label' => 'Número Telefónico', 'value' => $user->phone ?? '+51 —'],
                ] as $campo)
                <div class="flex items-center gap-4 py-3 group">
                    <div class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-dark-800 border border-slate-100 dark:border-white/5 flex items-center justify-center shrink-0 group-hover:bg-brand-50 dark:group-hover:bg-brand-500/10 group-hover:border-brand-200 dark:group-hover:border-brand-500/20 transition-colors">
                        <i class="ph-fill {{ $campo['icon'] }} text-slate-400 dark:text-slate-500 group-hover:text-brand-500 transition-colors text-lg"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5">{{ $campo['label'] }}</p>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white truncate">{{ $campo['value'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- RESERVAS RECIENTES --}}
        <div class="glass-card rounded-3xl p-6 md:p-8 flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="ph-fill ph-calendar-check text-brand-500 text-xl"></i> Últimas Reservas
                </h2>
                <a href="/reservas" class="text-xs text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 font-bold transition-colors flex items-center gap-1 group">
                    Ver todas <i class="ph-bold ph-arrow-right group-hover:translate-x-0.5 transition-transform"></i>
                </a>
            </div>

            <div class="space-y-3 flex-1">
                @forelse($reservas_recientes as $reserva)
                @php
                    $slot = $reserva->slots->sortBy('starts_at')->first();
                    $rImg = $imgDefault[$reserva->field?->sport_type ?? 'futbol5'] ?? $imgDefault['futbol5'];
                    $rFecha = $slot ? ($slot->starts_at->isToday() ? 'Hoy · '.$slot->starts_at->format('H:i') : $slot->starts_at->format('d M · H:i')) : '–';
                @endphp
                <a href="/reservas" class="flex items-center gap-4 p-3 rounded-2xl bg-white/50 dark:bg-dark-800/50 hover:bg-white dark:hover:bg-dark-800 border border-slate-100 dark:border-white/5 hover:border-brand-200 dark:hover:border-brand-500/30 transition-all cursor-pointer shadow-sm hover:shadow-md dark:shadow-none group">
                    <div class="w-12 h-12 rounded-xl overflow-hidden shrink-0 shadow-sm">
                        <img src="{{ $rImg }}" class="w-full h-full object-cover" alt="Cancha">
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-900 dark:text-white truncate group-hover:text-brand-500 transition-colors">{{ $reserva->field?->venue?->name ?? '–' }}</p>
                        <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-1">
                            <i class="ph-fill ph-clock"></i> {{ $rFecha }}
                        </p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-sm font-black text-slate-900 dark:text-white mb-1">S/ {{ number_format($reserva->total_price, 2) }}</p>
                        <span class="text-[9px] font-bold px-2 py-1 rounded-md uppercase tracking-wide
                            {{ $reserva->status === 'confirmed' ? 'bg-brand-100 dark:bg-brand-500/20 text-brand-700 dark:text-brand-300' : 'bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-300' }}">
                            {{ match($reserva->status) { 'confirmed'=>'Confirmada','completed'=>'Completada','no_show'=>'No-show',default=>$reserva->status } }}
                        </span>
                    </div>
                </a>
                @empty
                <p class="text-sm text-slate-400 text-center py-4">Aún no tenés reservas</p>
                @endforelse
            </div>
        </div>

    </div>

    {{-- OPCIONES DE CUENTA --}}
    <div class="glass-card rounded-3xl p-6 md:p-8">
        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
            <i class="ph-fill ph-gear text-brand-500 text-xl"></i> Configuración
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach([
                ['icon' => 'ph-lock-key',       'label' => 'Seguridad',           'desc' => 'Contraseña y accesos'],
                ['icon' => 'ph-bell-ringing',   'label' => 'Notificaciones',      'desc' => 'Alertas y recordatorios'],
                ['icon' => 'ph-credit-card',    'label' => 'Métodos de pago',     'desc' => 'Tarjetas y facturación'],
                ['icon' => 'ph-users',          'label' => 'Mi Equipo',           'desc' => 'Jugadores frecuentes'],
                ['icon' => 'ph-heart',          'label' => 'Favoritos',           'desc' => 'Canchas guardadas'],
                ['icon' => 'ph-question',       'label' => 'Ayuda y Soporte',     'desc' => 'Preguntas frecuentes'],
            ] as $opcion)
            <button class="flex items-center gap-4 p-4 rounded-2xl bg-white/30 dark:bg-dark-800/30 hover:bg-white dark:hover:bg-dark-800 transition-all text-left group w-full border border-slate-100 dark:border-white/5 hover:border-brand-200 dark:hover:border-brand-500/30 hover:shadow-md dark:shadow-none">
                <div class="w-12 h-12 rounded-xl bg-slate-50 dark:bg-dark-900 flex items-center justify-center shrink-0 group-hover:bg-brand-50 dark:group-hover:bg-brand-500/10 transition-colors border border-slate-100 dark:border-white/5 group-hover:border-brand-200 dark:group-hover:border-brand-500/20">
                    <i class="ph-fill {{ $opcion['icon'] }} text-slate-400 dark:text-slate-500 group-hover:text-brand-500 transition-colors text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-brand-500 transition-colors">{{ $opcion['label'] }}</p>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-0.5">{{ $opcion['desc'] }}</p>
                </div>
                <i class="ph-bold ph-caret-right text-slate-300 dark:text-slate-600 ml-auto group-hover:text-brand-500 group-hover:translate-x-1 transition-all"></i>
            </button>
            @endforeach
        </div>
    </div>

    {{-- CERRAR SESIÓN --}}
    <form method="POST" action="/logout" class="mt-8">
        @csrf
        <button type="submit" class="w-full sm:w-auto mx-auto flex items-center justify-center gap-2 px-8 py-4 rounded-2xl glass border border-red-200 dark:border-red-500/20 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 font-bold text-sm transition-all hover:scale-105 active:scale-95 shadow-sm">
            <i class="ph-bold ph-sign-out text-lg"></i>
            Cerrar sesión de forma segura
        </button>
    </form>

</div>

@endsection
