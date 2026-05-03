@extends('layouts.partner')
@section('title', 'Dashboard | FutGo Partner')
@section('page-title', 'Dashboard')
@php
    $venueNombre = $venue?->name ?? 'Mi complejo';
    $venueCiudad = $venue?->city?->name ?? '';
    $subtitulo   = $venueCiudad ? "$venueNombre · $venueCiudad" : $venueNombre;
@endphp
@section('page-subtitle', $subtitulo)

@section('content')

@php
$stats = [
    ['label' => 'Reservas hoy',      'valor' => 8,       'sub' => '+2 vs ayer',        'icon' => 'ph-calendar-check',  'color' => 'text-brand-500',   'bg' => 'bg-brand-500/10',   'trend' => 'up'],
    ['label' => 'Ingresos del mes',  'valor' => 'S/ 3,240', 'sub' => '+18% vs mes anterior','icon' => 'ph-money',        'color' => 'text-blue-500',    'bg' => 'bg-blue-500/10',    'trend' => 'up'],
    ['label' => 'Ocupación hoy',     'valor' => '74%',   'sub' => '12 de 16 slots',    'icon' => 'ph-chart-pie-slice', 'color' => 'text-purple-500',  'bg' => 'bg-purple-500/10',  'trend' => 'up'],
    ['label' => 'No-shows',          'valor' => 1,       'sub' => 'Anticipo retenido',  'icon' => 'ph-user-minus',      'color' => 'text-amber-500',   'bg' => 'bg-amber-500/10',   'trend' => 'down'],
];

$reservas_hoy = [
    ['hora' => '08:00', 'cancha' => 'Cancha 1', 'cliente' => 'Mario R.',   'tipo' => 'App',    'estado' => 'completada',  'monto' => 80.00, 'checkin' => true],
    ['hora' => '09:00', 'cancha' => 'Cancha 2', 'cliente' => 'Luis T.',    'tipo' => 'App',    'estado' => 'completada',  'monto' => 80.00, 'checkin' => true],
    ['hora' => '10:00', 'cancha' => 'Cancha 1', 'cliente' => 'Presencial',    'tipo' => 'Staff',  'estado' => 'completada',  'monto' => 80.00, 'checkin' => true],
    ['hora' => '18:00', 'cancha' => 'Cancha 1', 'cliente' => 'Carlos P.',  'tipo' => 'App',    'estado' => 'confirmada',  'monto' => 80.00, 'checkin' => false],
    ['hora' => '18:00', 'cancha' => 'Cancha 2', 'cliente' => 'Pedro M.',   'tipo' => 'App',    'estado' => 'confirmada',  'monto' => 80.00, 'checkin' => false],
    ['hora' => '19:00', 'cancha' => 'Cancha 1', 'cliente' => 'Roberto S.', 'tipo' => 'App',    'estado' => 'confirmada',  'monto' => 80.00, 'checkin' => false],
    ['hora' => '20:00', 'cancha' => 'Cancha 2', 'cliente' => 'Ana G.',     'tipo' => 'App',    'estado' => 'confirmada',  'monto' => 80.00, 'checkin' => false],
    ['hora' => '21:00', 'cancha' => 'Cancha 1', 'cliente' => '–',          'tipo' => '–',      'estado' => 'disponible',  'monto' => 0,     'checkin' => false],
];

// Canchas del venue activo
$canchas = $venue ? $venue->fields->pluck('name')->toArray() : [];
$horas       = ['07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22'];
// Ocupación simulada por ahora — Fase 3 la conectará con slots reales
$ocupacion = [];
foreach ($canchas as $cancha) {
    $ocupacion[$cancha] = [];
}
@endphp

{{-- =============================================
     STATS
============================================= --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach($stats as $stat)
    <div class="glass-card rounded-2xl p-5">
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl {{ $stat['bg'] }} flex items-center justify-center">
                <i class="ph-fill {{ $stat['icon'] }} {{ $stat['color'] }} text-xl"></i>
            </div>
            <span class="text-[10px] font-bold px-2 py-1 rounded-full flex items-center gap-1
                {{ $stat['trend'] === 'up' ? 'bg-brand-500/10 text-brand-600 dark:text-brand-400' : 'bg-amber-500/10 text-amber-600 dark:text-amber-400' }}">
                <i class="ph-bold {{ $stat['trend'] === 'up' ? 'ph-trend-up' : 'ph-trend-down' }}"></i>
                {{ $stat['trend'] === 'up' ? 'Bien' : 'Atención' }}
            </span>
        </div>
        <p class="text-2xl font-extrabold text-slate-900 dark:text-white mb-0.5">{{ $stat['valor'] }}</p>
        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $stat['label'] }}</p>
        <p class="text-[10px] text-slate-400 mt-0.5">{{ $stat['sub'] }}</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- =============================================
         GRILLA DE OCUPACIÓN
    ============================================= --}}
    <div class="xl:col-span-2 glass-card rounded-2xl p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="ph-bold ph-grid-four text-brand-500"></i> Ocupación de hoy
            </h2>
            <div class="flex items-center gap-3 text-[10px] font-bold uppercase tracking-wider">
                <span class="flex items-center gap-1.5 text-slate-400">
                    <span class="w-3 h-3 rounded bg-slate-200 dark:bg-white/10 inline-block"></span> Libre
                </span>
                <span class="flex items-center gap-1.5 text-brand-500">
                    <span class="w-3 h-3 rounded bg-brand-500/30 inline-block"></span> Reservado
                </span>
                <span class="flex items-center gap-1.5 text-slate-500">
                    <span class="w-3 h-3 rounded bg-slate-400/40 inline-block"></span> Hecho
                </span>
                <span class="flex items-center gap-1.5 text-amber-500">
                    <span class="w-3 h-3 rounded bg-amber-500/30 inline-block"></span> No-show
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr>
                        <th class="text-left text-slate-400 font-semibold pb-2 pr-3 w-24">Cancha</th>
                        @foreach($horas as $h)
                        <th class="text-center text-slate-400 font-medium pb-2 w-8">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="space-y-1">
                    @foreach($canchas as $cancha)
                    <tr>
                        <td class="pr-3 py-1">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 whitespace-nowrap">{{ $cancha }}</span>
                        </td>
                        @foreach($horas as $h)
                        @php $estado = $ocupacion[$cancha][$h] ?? 'libre'; @endphp
                        <td class="py-1 px-0.5">
                            <div class="h-7 w-7 rounded-lg mx-auto flex items-center justify-center
                                @if($estado === 'reserved') bg-brand-500/20 border border-brand-500/40
                                @elseif($estado === 'done')  bg-slate-300/40 dark:bg-white/10 border border-slate-300 dark:border-white/10
                                @elseif($estado === 'noshow') bg-amber-500/20 border border-amber-500/40
                                @else bg-slate-100 dark:bg-white/5 border border-transparent
                                @endif">
                                @if($estado === 'reserved')
                                    <i class="ph-fill ph-check text-brand-500 text-[10px]"></i>
                                @elseif($estado === 'done')
                                    <i class="ph-fill ph-check-circle text-slate-400 text-[10px]"></i>
                                @elseif($estado === 'noshow')
                                    <i class="ph-fill ph-x text-amber-500 text-[10px]"></i>
                                @endif
                            </div>
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- =============================================
         TURNO ACTIVO
    ============================================= --}}
    <div class="glass-card rounded-2xl p-5 flex flex-col gap-4">
        <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <i class="ph-bold ph-timer text-brand-500"></i> Turno activo
        </h2>

        <div class="bg-brand-500/10 border border-brand-500/20 rounded-2xl p-4 text-center">
            <p class="text-[10px] font-bold text-brand-500 uppercase tracking-widest mb-1">Staff de turno</p>
            <div class="w-12 h-12 rounded-xl overflow-hidden mx-auto mb-2 ring-2 ring-brand-500/30">
                <img src="https://ui-avatars.com/api/?name=Pedro+Staff&background=22c55e&color=fff" class="w-full h-full object-cover">
            </div>
            <p class="font-bold text-slate-900 dark:text-white">Pedro Mamani</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">Turno desde las 07:00</p>
        </div>

        <div class="space-y-3">
            @foreach([
                ['label' => 'Efectivo en caja', 'valor' => 'S/ 240.00', 'icon' => 'ph-money',        'color' => 'text-brand-500'],
                ['label' => 'Presenciales hoy',  'valor' => '3',         'icon' => 'ph-user-plus',    'color' => 'text-blue-500'],
                ['label' => 'Anticipo cobrado',  'valor' => 'S/ 90.00',  'icon' => 'ph-credit-card',  'color' => 'text-purple-500'],
            ] as $item)
            <div class="flex items-center justify-between py-2.5 border-b border-slate-100 dark:border-white/5 last:border-0">
                <div class="flex items-center gap-2.5">
                    <i class="ph-fill {{ $item['icon'] }} {{ $item['color'] }}"></i>
                    <span class="text-sm text-slate-600 dark:text-slate-400">{{ $item['label'] }}</span>
                </div>
                <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $item['valor'] }}</span>
            </div>
            @endforeach
        </div>

        <a href="/partner/reservas"
           class="mt-auto w-full bg-brand-500 hover:bg-brand-600 text-white font-bold py-3 rounded-xl flex items-center justify-center gap-2 text-sm transition-all hover:scale-[1.02]">
            <i class="ph-bold ph-calendar-check"></i> Ver reservas del día
        </a>
    </div>

</div>

{{-- =============================================
     RESERVAS DE HOY (tabla)
============================================= --}}
<div class="glass-card rounded-2xl p-5 mt-6">
    <div class="flex items-center justify-between mb-5">
        <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <i class="ph-bold ph-list-checks text-brand-500"></i> Reservas de hoy
        </h2>
        <a href="/partner/reservas" class="text-xs font-bold text-brand-500 hover:text-brand-600 transition-colors">
            Ver todas →
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-white/5">
                    <th class="text-left pb-3 pr-4">Hora</th>
                    <th class="text-left pb-3 pr-4">Cancha</th>
                    <th class="text-left pb-3 pr-4 hidden sm:table-cell">Cliente</th>
                    <th class="text-left pb-3 pr-4 hidden md:table-cell">Canal</th>
                    <th class="text-left pb-3 pr-4">Estado</th>
                    <th class="text-right pb-3">Monto</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-white/5">
                @foreach($reservas_hoy as $r)
                <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                    <td class="py-3 pr-4">
                        <span class="font-bold text-slate-900 dark:text-white">{{ $r['hora'] }}</span>
                    </td>
                    <td class="py-3 pr-4">
                        <span class="text-slate-600 dark:text-slate-300">{{ $r['cancha'] }}</span>
                    </td>
                    <td class="py-3 pr-4 hidden sm:table-cell">
                        <div class="flex items-center gap-2">
                            @if($r['cliente'] !== '–')
                            <div class="w-6 h-6 rounded-full bg-slate-200 dark:bg-white/10 flex items-center justify-center text-[9px] font-bold text-slate-500 dark:text-slate-400">
                                {{ strtoupper(substr($r['cliente'], 0, 1)) }}
                            </div>
                            @endif
                            <span class="text-slate-600 dark:text-slate-300">{{ $r['cliente'] }}</span>
                        </div>
                    </td>
                    <td class="py-3 pr-4 hidden md:table-cell">
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full
                            {{ $r['tipo'] === 'App' ? 'bg-blue-500/10 text-blue-500' : ($r['tipo'] === 'Staff' ? 'bg-purple-500/10 text-purple-500' : 'bg-slate-100 dark:bg-white/5 text-slate-400') }}">
                            {{ $r['tipo'] }}
                        </span>
                    </td>
                    <td class="py-3 pr-4">
                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full
                            @if($r['estado'] === 'confirmada')  bg-brand-500/10 text-brand-600 dark:text-brand-400
                            @elseif($r['estado'] === 'completada') bg-slate-100 dark:bg-white/10 text-slate-500
                            @elseif($r['estado'] === 'disponible') bg-slate-50 dark:bg-white/5 text-slate-300
                            @endif">
                            {{ ucfirst($r['estado']) }}
                        </span>
                    </td>
                    <td class="py-3 text-right">
                        <span class="font-bold {{ $r['monto'] > 0 ? 'text-slate-900 dark:text-white' : 'text-slate-300' }}">
                            {{ $r['monto'] > 0 ? 'S/ ' . number_format($r['monto'], 2) : '–' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t border-slate-100 dark:border-white/5">
                    <td colspan="5" class="pt-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Total cobrado hoy</td>
                    <td class="pt-3 text-right font-extrabold text-brand-500">S/ 560.00</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{-- =============================================
     FILA INFERIOR: Ingresos + Canchas
============================================= --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

    {{-- Ingresos semana --}}
    <div class="glass-card rounded-2xl p-5">
        <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-4">
            <i class="ph-bold ph-chart-line-up text-brand-500"></i> Ingresos esta semana
        </h2>
        <div class="flex items-end gap-2 h-24">
            @foreach([
                ['dia' => 'L', 'pct' => 60, 'monto' => '480'],
                ['dia' => 'M', 'pct' => 45, 'monto' => '360'],
                ['dia' => 'X', 'pct' => 80, 'monto' => '640'],
                ['dia' => 'J', 'pct' => 55, 'monto' => '440'],
                ['dia' => 'V', 'pct' => 90, 'monto' => '720'],
                ['dia' => 'S', 'pct' => 100,'monto' => '800'],
                ['dia' => 'D', 'pct' => 35, 'monto' => '280'],
            ] as $d)
            <div class="flex-1 flex flex-col items-center gap-1 group cursor-pointer">
                <div class="relative w-full">
                    <div class="w-full rounded-t-lg bg-brand-500/20 group-hover:bg-brand-500/40 transition-colors"
                         style="height: {{ $d['pct'] * 0.8 }}px">
                        <div class="w-full rounded-t-lg bg-brand-500 group-hover:bg-brand-400 transition-colors"
                             style="height: {{ $d['pct'] * 0.5 }}px"></div>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-slate-400 group-hover:text-brand-500 transition-colors">{{ $d['dia'] }}</span>
            </div>
            @endforeach
        </div>
        <div class="flex justify-between items-center mt-3 pt-3 border-t border-slate-100 dark:border-white/5">
            <span class="text-xs text-slate-400">Total semana</span>
            <span class="font-extrabold text-brand-500">S/ 3,720.00</span>
        </div>
    </div>

    {{-- Estado canchas --}}
    <div class="glass-card rounded-2xl p-5">
        <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-4">
            <i class="ph-bold ph-soccer-ball text-brand-500"></i> Estado de canchas
        </h2>
        <div class="space-y-3">
            @foreach([
                ['nombre' => 'Cancha 1 — Fútbol 5', 'pct' => 88, 'slots_libres' => 2],
                ['nombre' => 'Cancha 2 — Fútbol 5', 'pct' => 75, 'slots_libres' => 4],
                ['nombre' => 'Cancha 3 — Fútbol 7', 'pct' => 56, 'slots_libres' => 7],
                ['nombre' => 'Cancha 4 — Fútbol 7', 'pct' => 44, 'slots_libres' => 9],
            ] as $c)
            <div>
                <div class="flex justify-between items-center mb-1">
                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">{{ $c['nombre'] }}</span>
                    <span class="text-xs font-bold {{ $c['pct'] >= 80 ? 'text-brand-500' : ($c['pct'] >= 50 ? 'text-amber-500' : 'text-slate-400') }}">
                        {{ $c['pct'] }}%
                    </span>
                </div>
                <div class="h-2 bg-slate-100 dark:bg-white/10 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500
                        {{ $c['pct'] >= 80 ? 'bg-brand-500' : ($c['pct'] >= 50 ? 'bg-amber-500' : 'bg-slate-400') }}"
                         style="width: {{ $c['pct'] }}%"></div>
                </div>
                <p class="text-[10px] text-slate-400 mt-0.5">{{ $c['slots_libres'] }} slots libres hoy</p>
            </div>
            @endforeach
        </div>
    </div>

</div>

@endsection
