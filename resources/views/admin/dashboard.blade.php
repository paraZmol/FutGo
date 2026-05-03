@extends('layouts.admin')
@section('title', 'Panel Principal | FutGo Admin')
@section('page-title', 'Panel Principal')
@section('page-subtitle', 'Visión global de la plataforma · ' . date('d M Y'))

@section('content')

@php
$kpis = [
    ['label' => 'Complejos activos',  'val' => 48,        'sub' => '+3 este mes',      'icon' => 'ph-buildings',       'color' => 'text-admin-500',  'bg' => 'bg-admin-500/10',  'trend' => 'up'],
    ['label' => 'Usuarios activos',   'val' => '5,284',   'sub' => '+312 este mes',    'icon' => 'ph-users',           'color' => 'text-brand-500',  'bg' => 'bg-brand-500/10',  'trend' => 'up'],
    ['label' => 'Reservas del mes',   'val' => '3,847',   'sub' => '+18% vs anterior', 'icon' => 'ph-calendar-check',  'color' => 'text-blue-500',   'bg' => 'bg-blue-500/10',   'trend' => 'up'],
    ['label' => 'Vol. transaccional', 'val' => 'S/ 287k', 'sub' => 'Comisión: S/ 0',   'icon' => 'ph-money',           'color' => 'text-purple-500', 'bg' => 'bg-purple-500/10', 'trend' => 'up'],
    ['label' => 'Partners pendientes','val' => 3,          'sub' => 'Requieren aprobación','icon' => 'ph-clock',        'color' => 'text-amber-500',  'bg' => 'bg-amber-500/10',  'trend' => 'down'],
    ['label' => 'Disputas abiertas',  'val' => 2,          'sub' => 'Requieren revisión','icon' => 'ph-warning',        'color' => 'text-red-500',    'bg' => 'bg-red-500/10',    'trend' => 'down'],
    ['label' => 'Tasa de ausencias',  'val' => '5.2%',     'sub' => '-0.8pp vs anterior','icon' => 'ph-user-minus',    'color' => 'text-orange-500', 'bg' => 'bg-orange-500/10', 'trend' => 'up'],
    ['label' => 'Disponibilidad global','val' => '99.8%',    'sub' => 'Último incidente: hace 14d','icon' => 'ph-pulse','color' => 'text-brand-500',  'bg' => 'bg-brand-500/10',  'trend' => 'up'],
];

$partners_pendientes = [
    ['nombre' => 'Canchas El Diamante', 'ciudad' => 'Arequipa', 'canchas' => 3, 'solicitado' => 'Hace 2 días',  'contacto' => 'Luis Paredes'],
    ['nombre' => 'Complejo Los Andes',  'ciudad' => 'Lima',     'canchas' => 5, 'solicitado' => 'Hace 4 días',  'contacto' => 'Maria Torres'],
    ['nombre' => 'SportPlex Trujillo',  'ciudad' => 'Trujillo', 'canchas' => 2, 'solicitado' => 'Hace 1 semana','contacto' => 'Jorge Ríos'],
];

$disputas_recientes = [
    ['id' => 'D001', 'tipo' => 'noshow',    'cliente' => 'Carlos Mamani', 'partner' => 'Complejo El 10',    'monto' => 30.00, 'estado' => 'abierta',  'fecha' => 'Hace 1 día'],
    ['id' => 'D002', 'tipo' => 'reembolso', 'cliente' => 'Sofia Ríos',   'partner' => 'Canchas La Losa',   'monto' => 80.00, 'estado' => 'abierta',  'fecha' => 'Hace 3 días'],
];

$actividad_reciente = [
    ['icono' => 'ph-buildings',      'color' => 'text-admin-500',  'bg' => 'bg-admin-500/10',  'desc' => 'Nuevo partner aprobado: Arena Sports Cusco',     'tiempo' => 'Hace 2h'],
    ['icono' => 'ph-calendar-check', 'color' => 'text-brand-500',  'bg' => 'bg-brand-500/10',  'desc' => '3,847 reservas completadas este mes',             'tiempo' => 'Hoy'],
    ['icono' => 'ph-warning',        'color' => 'text-red-500',    'bg' => 'bg-red-500/10',    'desc' => 'Nueva disputa abierta: D002 (reembolso S/ 80)',    'tiempo' => 'Hace 3d'],
    ['icono' => 'ph-user-plus',      'color' => 'text-blue-500',   'bg' => 'bg-blue-500/10',   'desc' => '312 nuevos usuarios registrados este mes',        'tiempo' => 'Este mes'],
    ['icono' => 'ph-percent',        'color' => 'text-purple-500', 'bg' => 'bg-purple-500/10', 'desc' => 'Comisión plataforma: 0% (fase penetración)',       'tiempo' => 'Vigente'],
    ['icono' => 'ph-shield-check',   'color' => 'text-brand-500',  'bg' => 'bg-brand-500/10',  'desc' => 'Auditoría de seguridad completada sin alertas',   'tiempo' => 'Hace 5d'],
];

$top_partners = [
    ['nombre' => 'Complejo Deportivo El 10', 'ciudad' => 'Cusco',    'reservas' => 124, 'volumen' => 9840.00,  'rating' => 4.8],
    ['nombre' => 'Arena Sports',             'ciudad' => 'Lima',     'reservas' => 98,  'volumen' => 7840.00,  'rating' => 4.7],
    ['nombre' => 'SportCenter Norte',        'ciudad' => 'Cusco',    'reservas' => 87,  'volumen' => 6960.00,  'rating' => 4.6],
    ['nombre' => 'La Bombonera FC',          'ciudad' => 'Lima',     'reservas' => 74,  'volumen' => 5920.00,  'rating' => 4.5],
    ['nombre' => 'Canchas El Sol',           'ciudad' => 'Arequipa', 'reservas' => 61,  'volumen' => 4880.00,  'rating' => 4.4],
];

$ciudades = [
    ['ciudad' => 'Lima',      'partners' => 18, 'reservas' => 1420, 'pct' => 100],
    ['ciudad' => 'Cusco',     'partners' => 12, 'reservas' =>  980, 'pct' => 69],
    ['ciudad' => 'Arequipa',  'partners' =>  8, 'reservas' =>  640, 'pct' => 45],
    ['ciudad' => 'Trujillo',  'partners' =>  6, 'reservas' =>  480, 'pct' => 34],
    ['ciudad' => 'Piura',     'partners' =>  4, 'reservas' =>  327, 'pct' => 23],
];
@endphp

{{-- KPIs --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach($kpis as $k)
    <div class="glass-card rounded-2xl p-4">
        <div class="flex items-start justify-between mb-3">
            <div class="w-9 h-9 rounded-xl {{ $k['bg'] }} flex items-center justify-center">
                <i class="ph-fill {{ $k['icon'] }} {{ $k['color'] }}"></i>
            </div>
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-1
                {{ $k['trend'] === 'up' ? 'bg-brand-500/10 text-brand-600 dark:text-brand-400' : 'bg-amber-500/10 text-amber-600 dark:text-amber-400' }}">
                <i class="ph-bold {{ $k['trend'] === 'up' ? 'ph-trend-up' : 'ph-trend-down' }}"></i>
                {{ $k['trend'] === 'up' ? 'OK' : 'Atención' }}
            </span>
        </div>
        <p class="text-xl font-extrabold text-slate-900 dark:text-white">{{ $k['val'] }}</p>
        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 leading-tight">{{ $k['label'] }}</p>
        <p class="text-[10px] text-slate-400 mt-0.5">{{ $k['sub'] }}</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-5">

    {{-- PARTNERS PENDIENTES --}}
    <div class="xl:col-span-2 glass-card rounded-2xl p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="ph-fill ph-clock text-amber-500"></i> Partners pendientes de aprobación
                <span class="bg-amber-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ count($partners_pendientes) }}</span>
            </h2>
            <a href="/admin/partners" class="text-xs font-bold text-admin-500 hover:text-admin-600 transition-colors">Ver todos →</a>
        </div>

        <div class="space-y-3">
            @foreach($partners_pendientes as $p)
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 p-4 bg-amber-50 dark:bg-amber-500/5 border border-amber-200 dark:border-amber-500/20 rounded-2xl">
                <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-500/10 flex items-center justify-center shrink-0">
                    <i class="ph-fill ph-buildings text-amber-600 dark:text-amber-400 text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-slate-900 dark:text-white text-sm">{{ $p['nombre'] }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-2 flex-wrap">
                        <span class="flex items-center gap-1"><i class="ph-fill ph-map-pin"></i> {{ $p['ciudad'] }}</span>
                        <span>·</span>
                        <span>{{ $p['canchas'] }} canchas</span>
                        <span>·</span>
                        <span>{{ $p['contacto'] }}</span>
                        <span>·</span>
                        <span class="text-amber-600 dark:text-amber-400 font-medium">{{ $p['solicitado'] }}</span>
                    </p>
                </div>
                <div class="flex gap-2 shrink-0">
                    <button class="px-3 py-1.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold transition-colors flex items-center gap-1">
                        <i class="ph-bold ph-check"></i> Aprobar
                    </button>
                    <button class="px-3 py-1.5 rounded-xl border border-red-200 dark:border-red-500/30 text-red-500 hover:bg-red-500 hover:text-white text-xs font-bold transition-colors flex items-center gap-1">
                        <i class="ph-bold ph-x"></i> Rechazar
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- DISPUTAS ABIERTAS --}}
    <div class="glass-card rounded-2xl p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="ph-fill ph-warning text-red-500"></i> Disputas abiertas
            </h2>
            <a href="/admin/disputas" class="text-xs font-bold text-admin-500 hover:text-admin-600 transition-colors">Ver →</a>
        </div>

        <div class="space-y-3">
            @foreach($disputas_recientes as $d)
            <div class="p-4 bg-red-50 dark:bg-red-500/5 border border-red-200 dark:border-red-500/20 rounded-2xl">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-bold text-red-500 uppercase tracking-wider">{{ $d['id'] }}</span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full
                        {{ $d['tipo'] === 'noshow' ? 'bg-amber-100 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400' : 'bg-blue-100 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400' }}">
                        {{ $d['tipo'] === 'noshow' ? 'No-show' : 'Reembolso' }}
                    </span>
                </div>
                <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $d['cliente'] }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $d['partner'] }} · S/ {{ number_format($d['monto'],2) }}</p>
                <p class="text-[10px] text-slate-400 mt-1">{{ $d['fecha'] }}</p>
                <button class="mt-2 w-full py-1.5 rounded-xl border border-admin-500/30 text-admin-500 hover:bg-admin-500 hover:text-white text-xs font-bold transition-colors">
                    Revisar disputa
                </button>
            </div>
            @endforeach
        </div>
    </div>

</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

    {{-- TOP PARTNERS --}}
    <div class="glass-card rounded-2xl p-5">
        <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-4">
            <i class="ph-fill ph-trophy text-amber-500"></i> Top Partners del mes
        </h2>
        <div class="space-y-3">
            @foreach($top_partners as $i => $p)
            <div class="flex items-center gap-3">
                <span class="text-sm font-bold w-5 text-center shrink-0
                    {{ $i === 0 ? 'text-amber-500' : ($i === 1 ? 'text-slate-400' : 'text-slate-300') }}">
                    {{ $i + 1 }}
                </span>
                <div class="w-8 h-8 rounded-xl bg-admin-500/10 flex items-center justify-center shrink-0">
                    <i class="ph-fill ph-buildings text-admin-500 text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 dark:text-white truncate">{{ $p['nombre'] }}</p>
                    <p class="text-[10px] text-slate-400 flex items-center gap-1">
                        <i class="ph-fill ph-map-pin"></i> {{ $p['ciudad'] }}
                    </p>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-sm font-bold text-slate-900 dark:text-white">S/ {{ number_format($p['volumen'],0) }}</p>
                    <p class="text-[10px] text-slate-400">{{ $p['reservas'] }} reservas</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- COBERTURA POR CIUDAD --}}
    <div class="glass-card rounded-2xl p-5">
        <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-4">
            <i class="ph-fill ph-map-pin text-admin-500"></i> Cobertura por ciudad
        </h2>
        <div class="space-y-4">
            @foreach($ciudades as $c)
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold text-slate-800 dark:text-white">{{ $c['ciudad'] }}</span>
                        <span class="text-[10px] text-slate-400">{{ $c['partners'] }} complejos</span>
                    </div>
                    <span class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ $c['reservas'] }}</span>
                </div>
                <div class="h-2 bg-slate-100 dark:bg-white/10 rounded-full overflow-hidden">
                    <div class="h-full rounded-full bg-admin-500 transition-all" style="width: {{ $c['pct'] }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
        <p class="text-xs text-slate-400 text-center mt-4">Total: <span class="font-bold text-slate-700 dark:text-white">3,847 reservas</span> este mes</p>
    </div>

</div>

{{-- ACTIVIDAD RECIENTE --}}
<div class="glass-card rounded-2xl p-5">
    <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-4">
        <i class="ph-fill ph-activity text-admin-500"></i> Actividad reciente
    </h2>
    <div class="space-y-3">
        @foreach($actividad_reciente as $act)
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-xl {{ $act['bg'] }} flex items-center justify-center shrink-0 mt-0.5">
                <i class="ph-fill {{ $act['icono'] }} {{ $act['color'] }} text-sm"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm text-slate-700 dark:text-slate-300">{{ $act['desc'] }}</p>
            </div>
            <span class="text-[10px] text-slate-400 shrink-0">{{ $act['tiempo'] }}</span>
        </div>
        @endforeach
    </div>
</div>

@endsection
