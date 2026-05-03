@extends('layouts.partner')
@section('title', 'Analítica | FutGo Partner')
@section('page-title', 'Analítica')
@section('page-subtitle', 'Complejo Deportivo El 10 · Historial y tendencias')

@section('content')

@php
// Datos del mes actual vs anterior
$kpis = [
    ['label' => 'Ingresos del mes',    'valor' => 'S/ 3,240', 'anterior' => 'S/ 2,740', 'pct' => '+18%',  'icon' => 'ph-money',           'color' => 'text-brand-500',  'bg' => 'bg-brand-500/10',  'trend' => 'up'],
    ['label' => 'Reservas del mes',    'valor' => '124',       'anterior' => '98',        'pct' => '+26%',  'icon' => 'ph-calendar-check',  'color' => 'text-blue-500',   'bg' => 'bg-blue-500/10',   'trend' => 'up'],
    ['label' => 'Tasa de ocupación',   'valor' => '72%',       'anterior' => '61%',       'pct' => '+11pp', 'icon' => 'ph-chart-pie-slice', 'color' => 'text-purple-500', 'bg' => 'bg-purple-500/10', 'trend' => 'up'],
    ['label' => 'Tasa de no-show',     'valor' => '4.8%',      'anterior' => '7.2%',      'pct' => '-2.4pp','icon' => 'ph-user-minus',      'color' => 'text-amber-500',  'bg' => 'bg-amber-500/10',  'trend' => 'up'],
];

// Ingresos por mes (últimos 6)
$meses = ['Nov', 'Dic', 'Ene', 'Feb', 'Mar', 'Abr', 'May'];
$ingresos_mes = [1840, 2100, 1650, 2380, 2740, 3100, 3240];
$max_ingreso = max($ingresos_mes);

// Ocupación por cancha y franja
$ocupacion_franjas = [
    'Cancha 1' => ['Mañana' => 82, 'Tarde' => 68, 'Noche' => 91],
    'Cancha 2' => ['Mañana' => 71, 'Tarde' => 55, 'Noche' => 88],
    'Cancha 3' => ['Mañana' => 60, 'Tarde' => 74, 'Noche' => 79],
    'Cancha 4' => ['Mañana' => 30, 'Tarde' => 42, 'Noche' => 65],
];

// Horarios más populares
$horarios_top = [
    ['hora' => '20:00', 'reservas' => 28, 'pct' => 100],
    ['hora' => '19:00', 'reservas' => 25, 'pct' => 89],
    ['hora' => '21:00', 'reservas' => 22, 'pct' => 79],
    ['hora' => '18:00', 'reservas' => 20, 'pct' => 71],
    ['hora' => '08:00', 'reservas' => 14, 'pct' => 50],
    ['hora' => '09:00', 'reservas' => 12, 'pct' => 43],
];

// Canal de reservas
$canales = [
    ['canal' => 'App móvil',   'pct' => 68, 'reservas' => 84, 'color' => 'bg-brand-500'],
    ['canal' => 'Presencial',  'pct' => 22, 'reservas' => 27, 'color' => 'bg-blue-500'],
    ['canal' => 'Telefónico',  'pct' => 10, 'reservas' => 13, 'color' => 'bg-purple-500'],
];

// Días de la semana más activos
$dias_semana = [
    ['dia' => 'Lun', 'pct' => 55, 'reservas' => 16],
    ['dia' => 'Mar', 'pct' => 48, 'reservas' => 14],
    ['dia' => 'Mié', 'pct' => 62, 'reservas' => 18],
    ['dia' => 'Jue', 'pct' => 58, 'reservas' => 17],
    ['dia' => 'Vie', 'pct' => 78, 'reservas' => 23],
    ['dia' => 'Sáb', 'pct' => 100,'reservas' => 29],
    ['dia' => 'Dom', 'pct' => 72, 'reservas' => 21],
];

// Top clientes
$top_clientes = [
    ['nombre' => 'Mario Quispe',   'reservas' => 8, 'gasto' => 640.00],
    ['nombre' => 'Luis Torres',    'reservas' => 7, 'gasto' => 560.00],
    ['nombre' => 'Carlos Mamani', 'reservas' => 6, 'gasto' => 600.00],
    ['nombre' => 'Ana Gutierrez', 'reservas' => 5, 'gasto' => 450.00],
    ['nombre' => 'Pedro Huanca',  'reservas' => 5, 'gasto' => 450.00],
];
@endphp

{{-- SELECTOR DE PERIODO --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div class="flex items-center gap-1 glass-card rounded-xl p-1 border border-slate-200 dark:border-white/10 overflow-x-auto no-scrollbar pb-1 w-full sm:w-auto">
        @foreach(['Este mes' => 'mes', 'Últimos 3m' => '3m', 'Últimos 6m' => '6m', 'Este año' => 'anio'] as $label => $val)
        <button onclick="seleccionarPeriodo(this)"
                class="periodo-btn shrink-0 px-3 py-1.5 rounded-lg text-sm font-semibold transition-all
            {{ $val === 'mes' ? 'bg-brand-500 text-white shadow' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>
    <button class="flex items-center justify-center gap-2 px-4 py-2 rounded-xl border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-500 dark:text-slate-400 hover:border-brand-500 hover:text-brand-500 transition-colors w-full sm:w-auto shrink-0">
        <i class="ph-bold ph-export"></i> Exportar reporte
    </button>
</div>

{{-- KPIs --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach($kpis as $k)
    <div class="glass-card rounded-2xl p-5">
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl {{ $k['bg'] }} flex items-center justify-center">
                <i class="ph-fill {{ $k['icon'] }} {{ $k['color'] }} text-xl"></i>
            </div>
            <span class="text-[10px] font-bold px-2 py-1 rounded-full flex items-center gap-1
                {{ $k['trend'] === 'up' ? 'bg-brand-500/10 text-brand-600 dark:text-brand-400' : 'bg-red-500/10 text-red-500' }}">
                <i class="ph-bold {{ $k['trend'] === 'up' ? 'ph-trend-up' : 'ph-trend-down' }}"></i>
                {{ $k['pct'] }}
            </span>
        </div>
        <p class="text-2xl font-extrabold text-slate-900 dark:text-white mb-0.5">{{ $k['valor'] }}</p>
        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $k['label'] }}</p>
        <p class="text-[10px] text-slate-400 mt-1">vs {{ $k['anterior'] }} mes anterior</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-5">
    {{-- GRÁFICO INGRESOS MENSUALES (AREA CHART) --}}
    <div class="xl:col-span-3 glass-card rounded-2xl p-5">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="ph-fill ph-chart-line-up text-brand-500"></i> Ingresos mensuales
                </h2>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Tendencia de crecimiento</p>
            </div>
            <div class="text-right">
                <span class="text-xs font-bold text-brand-500 bg-brand-500/10 px-2 py-1 rounded-lg">+24% vs año pasado</span>
            </div>
        </div>
        
        <div class="relative h-48 w-full mt-2">
            {{-- SVG para el gráfico de área --}}
            <svg class="w-full h-full" viewBox="0 0 700 200" preserveAspectRatio="none">
                <defs>
                    <linearGradient id="grad-income" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" style="stop-color:rgba(34, 197, 94, 0.2); stop-opacity:1" />
                        <stop offset="100%" style="stop-color:rgba(34, 197, 94, 0); stop-opacity:1" />
                    </linearGradient>
                </defs>
                {{-- Área --}}
                <path d="M0,160 Q100,140 200,155 T400,120 T600,80 T700,60 L700,200 L0,200 Z" fill="url(#grad-income)" />
                {{-- Línea --}}
                <path d="M0,160 Q100,140 200,155 T400,120 T600,80 T700,60" fill="none" stroke="#22c55e" stroke-width="3" stroke-linecap="round" />
                {{-- Puntos de datos --}}
                <circle cx="100" cy="143" r="4" fill="#22c55e" class="animate-pulse" />
                <circle cx="200" cy="155" r="4" fill="#22c55e" />
                <circle cx="400" cy="120" r="4" fill="#22c55e" />
                <circle cx="600" cy="80" r="4" fill="#22c55e" />
                <circle cx="700" cy="60" r="6" fill="#22c55e" stroke="white" stroke-width="2" />
            </svg>

            {{-- Etiquetas de meses abajo --}}
            <div class="flex justify-between mt-4 px-2">
                @foreach(['Nov', 'Dic', 'Ene', 'Feb', 'Mar', 'Abr', 'May'] as $m)
                <span class="text-[10px] font-bold text-slate-400">{{ $m }}</span>
                @endforeach
            </div>
        </div>

        <div class="flex justify-between items-center mt-8 pt-4 border-t border-slate-100 dark:border-white/5">
            <div class="flex items-center gap-4">
                <div class="flex flex-col">
                    <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Promedio</span>
                    <span class="text-sm font-bold text-slate-700 dark:text-slate-200">S/ 2,450</span>
                </div>
                <div class="w-px h-6 bg-slate-100 dark:bg-white/10"></div>
                <div class="flex flex-col">
                    <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Pico</span>
                    <span class="text-sm font-bold text-slate-700 dark:text-slate-200">S/ 3,240</span>
                </div>
            </div>
            <a href="#" class="text-xs font-bold text-brand-600 dark:text-brand-400 hover:underline flex items-center gap-1">
                Ver detalle <i class="ph-bold ph-caret-right"></i>
            </a>
        </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-5 mb-5">

    {{-- DÍAS Y HORAS MÁS ACTIVOS (HEATMAP) --}}
    <div class="glass-card rounded-2xl p-5">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="ph-fill ph-grid-four text-brand-500"></i> Mapa de ocupación
            </h2>
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-brand-500"></div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Pico</span>
            </div>
        </div>
        
        <div class="space-y-1.5">
            @php
                $franjas = ['07-11', '12-16', '17-20', '21-23'];
                $intensidad = [
                    [20, 30, 40, 30, 50, 70, 60], // Mañana
                    [40, 50, 30, 60, 80, 95, 85], // Tarde
                    [90, 85, 95, 90, 100, 100, 95], // Noche (Pico)
                    [60, 40, 50, 60, 90, 95, 70], // Madrugada/Cierre
                ];
                $dias_h = ['L', 'M', 'M', 'J', 'V', 'S', 'D'];
            @endphp

            <div class="grid grid-cols-8 gap-1.5 mb-1 text-center">
                <div class="text-[9px] text-slate-400 font-bold"></div>
                @foreach($dias_h as $d)
                    <div class="text-[10px] text-slate-400 font-bold">{{ $d }}</div>
                @endforeach
            </div>

            @foreach($franjas as $idx => $f)
            <div class="grid grid-cols-8 gap-1.5 items-center">
                <div class="text-[9px] text-slate-400 font-bold leading-none">{{ $f }}</div>
                @foreach($intensidad[$idx] as $val)
                <div class="aspect-square rounded-md transition-all hover:scale-110 cursor-help
                    {{ $val >= 90 ? 'bg-brand-600' : ($val >= 70 ? 'bg-brand-500' : ($val >= 50 ? 'bg-brand-500/60' : ($val >= 30 ? 'bg-brand-500/30' : 'bg-slate-100 dark:bg-white/5'))) }}"
                    title="Ocupación: {{ $val }}%"></div>
                @endforeach
            </div>
            @endforeach
        </div>

        <div class="mt-5 pt-4 border-t border-slate-100 dark:border-white/5 flex justify-between items-center text-[10px]">
            <span class="text-slate-400">Menos</span>
            <div class="flex gap-1">
                <div class="w-3 h-3 rounded-sm bg-slate-100 dark:bg-white/5"></div>
                <div class="w-3 h-3 rounded-sm bg-brand-500/30"></div>
                <div class="w-3 h-3 rounded-sm bg-brand-500/60"></div>
                <div class="w-3 h-3 rounded-sm bg-brand-500"></div>
                <div class="w-3 h-3 rounded-sm bg-brand-600"></div>
            </div>
            <span class="text-slate-400">Más</span>
        </div>
    </div>

    {{-- HORARIOS MÁS POPULARES --}}
    <div class="glass-card rounded-2xl p-5">
        <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-4">
            <i class="ph-fill ph-clock text-brand-500"></i> Horarios más populares
        </h2>
        <div class="space-y-2.5">
            @foreach($horarios_top as $h)
            <div class="flex items-center gap-3">
                <span class="text-sm font-bold text-slate-700 dark:text-slate-300 w-12 shrink-0">{{ $h['hora'] }}</span>
                <div class="flex-1 h-2 bg-slate-100 dark:bg-white/10 rounded-full overflow-hidden">
                    <div class="h-full rounded-full bg-brand-500 transition-all"
                         style="width: {{ $h['pct'] }}%"></div>
                </div>
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 w-8 text-right">{{ $h['reservas'] }}</span>
            </div>
            @endforeach
        </div>
        <p class="text-xs text-slate-400 text-center mt-3">
            <span class="font-bold text-brand-500">20:00 – 21:00</span> es el horario estrella
        </p>
    </div>

    {{-- OCUPACIÓN POR CANCHA Y FRANJA --}}
    <div class="glass-card rounded-2xl p-5">
        <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-4">
            <i class="ph-fill ph-soccer-ball text-brand-500"></i> Ocupación por franja
        </h2>
        <div class="space-y-4">
            @foreach($ocupacion_franjas as $cancha => $franjas)
            <div>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-2">{{ $cancha }}</p>
                <div class="grid grid-cols-3 gap-1.5">
                    @foreach($franjas as $franja => $pct)
                    <div class="text-center">
                        <div class="h-1.5 rounded-full mb-1 {{ $pct >= 80 ? 'bg-brand-500' : ($pct >= 60 ? 'bg-amber-500' : 'bg-slate-300 dark:bg-white/20') }}"
                             style="width: 100%"></div>
                        <span class="text-[10px] font-bold {{ $pct >= 80 ? 'text-brand-500' : ($pct >= 60 ? 'text-amber-500' : 'text-slate-400') }}">
                            {{ $pct }}%
                        </span>
                        <span class="text-[9px] text-slate-400 block">{{ $franja }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>

{{-- TOP CLIENTES --}}
<div class="glass-card rounded-2xl p-5">
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <i class="ph-fill ph-trophy text-amber-500"></i> Top clientes del mes
        </h2>
        <span class="text-xs text-slate-400">Por frecuencia de reserva</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-white/5">
                    <th class="text-left pb-3 w-8">#</th>
                    <th class="text-left pb-3">Cliente</th>
                    <th class="text-center pb-3">Reservas</th>
                    <th class="text-center pb-3 hidden sm:table-cell">Frecuencia</th>
                    <th class="text-right pb-3">Gasto total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-white/5">
                @foreach($top_clientes as $i => $c)
                <tr class="hover:bg-slate-50 dark:hover:bg-white/3 transition-colors">
                    <td class="py-3">
                        <span class="text-sm font-bold {{ $i === 0 ? 'text-amber-500' : ($i === 1 ? 'text-slate-400' : ($i === 2 ? 'text-orange-400' : 'text-slate-300')) }}">
                            {{ $i === 0 ? '🥇' : ($i === 1 ? '🥈' : ($i === 2 ? '🥉' : $i + 1)) }}
                        </span>
                    </td>
                    <td class="py-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-white/10 flex items-center justify-center text-xs font-bold text-slate-500 dark:text-slate-400 shrink-0">
                                {{ strtoupper(substr($c['nombre'], 0, 2)) }}
                            </div>
                            <span class="font-semibold text-slate-800 dark:text-white">{{ $c['nombre'] }}</span>
                        </div>
                    </td>
                    <td class="py-3 text-center">
                        <span class="font-bold text-slate-900 dark:text-white">{{ $c['reservas'] }}</span>
                    </td>
                    <td class="py-3 hidden sm:table-cell">
                        <div class="flex justify-center">
                            <div class="w-24 h-1.5 bg-slate-100 dark:bg-white/10 rounded-full overflow-hidden">
                                <div class="h-full rounded-full bg-brand-500"
                                     style="width: {{ round(($c['reservas'] / $top_clientes[0]['reservas']) * 100) }}%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 text-right">
                        <span class="font-bold text-slate-900 dark:text-white">S/ {{ number_format($c['gasto'], 2) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- TOP INCUMPLIDORES --}}
<div class="glass-card rounded-2xl p-5 mt-5">
    <div class="flex items-center justify-between mb-1">
        <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <i class="ph-fill ph-warning text-red-500"></i> Riesgo de Inasistencia
        </h2>
        <span class="text-xs text-slate-400">Basado en historial de faltas</span>
    </div>
    <p class="text-xs text-slate-400 mb-4 flex items-center gap-1.5">
        <i class="ph-fill ph-info text-slate-400"></i>
        Clientes con mayor probabilidad de no presentarse según su historial. Considerá exigirles anticipo mayor o confirmar por WhatsApp.
    </p>

    @php
    $incumplidores = [
        [
            'nombre'      => 'Carlos Mamani',
            'reservas'    => 12,
            'noshow'      => 4,
            'tasa'        => 33,
            'ultimo_ns'   => 'Hace 3 días',
            'riesgo'      => 'alto',
            'anticipo_actual' => 30,
            'anticipo_sug'    => 60,
        ],
        [
            'nombre'      => 'Diego Vargas',
            'reservas'    => 8,
            'noshow'      => 2,
            'tasa'        => 25,
            'ultimo_ns'   => 'Hace 1 semana',
            'riesgo'      => 'alto',
            'anticipo_actual' => 30,
            'anticipo_sug'    => 50,
        ],
        [
            'nombre'      => 'Sofía Ríos',
            'reservas'    => 9,
            'noshow'      => 2,
            'tasa'        => 22,
            'ultimo_ns'   => 'Hace 2 semanas',
            'riesgo'      => 'medio',
            'anticipo_actual' => 30,
            'anticipo_sug'    => 45,
        ],
        [
            'nombre'      => 'Raúl Condori',
            'reservas'    => 6,
            'noshow'      => 1,
            'tasa'        => 17,
            'ultimo_ns'   => 'Hace 3 semanas',
            'riesgo'      => 'medio',
            'anticipo_actual' => 30,
            'anticipo_sug'    => 40,
        ],
        [
            'nombre'      => 'Paola Huanca',
            'reservas'    => 7,
            'noshow'      => 1,
            'tasa'        => 14,
            'ultimo_ns'   => 'Hace 1 mes',
            'riesgo'      => 'bajo',
            'anticipo_actual' => 30,
            'anticipo_sug'    => 30,
        ],
    ];
    @endphp

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-white/5">
                    <th class="text-left pb-3">Cliente</th>
                    <th class="text-center pb-3">Faltas</th>
                    <th class="text-center pb-3 hidden sm:table-cell">Total Reservas</th>
                    <th class="text-center pb-3">Tasa de Falta</th>
                    <th class="text-center pb-3 hidden md:table-cell">Última Falta</th>
                    <th class="text-center pb-3">Riesgo</th>
                    <th class="text-right pb-3 hidden lg:table-cell">Anticipo sugerido</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-white/5">
                @foreach($incumplidores as $inc)
                <tr class="hover:bg-slate-50 dark:hover:bg-white/3 transition-colors">

                    {{-- Cliente --}}
                    <td class="py-3 pr-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-bold shrink-0
                                {{ $inc['riesgo'] === 'alto' ? 'bg-red-100 dark:bg-red-500/10 text-red-500'
                                : ($inc['riesgo'] === 'medio' ? 'bg-amber-100 dark:bg-amber-500/10 text-amber-500'
                                : 'bg-slate-100 dark:bg-white/10 text-slate-400') }}">
                                {{ strtoupper(substr($inc['nombre'], 0, 2)) }}
                            </div>
                            <span class="font-semibold text-slate-800 dark:text-white">{{ $inc['nombre'] }}</span>
                        </div>
                    </td>

                    {{-- No-shows --}}
                    <td class="py-3 text-center">
                        <span class="font-extrabold text-red-500 text-base">{{ $inc['noshow'] }}</span>
                    </td>

                    {{-- Total reservas --}}
                    <td class="py-3 text-center hidden sm:table-cell">
                        <span class="text-slate-500 dark:text-slate-400">{{ $inc['reservas'] }} reservas</span>
                    </td>

                    {{-- Tasa --}}
                    <td class="py-3 text-center">
                        <div class="inline-flex flex-col items-center gap-1">
                            <span class="font-bold text-sm
                                {{ $inc['tasa'] >= 25 ? 'text-red-500' : ($inc['tasa'] >= 15 ? 'text-amber-500' : 'text-slate-500') }}">
                                {{ $inc['tasa'] }}%
                            </span>
                            <div class="w-16 h-1.5 bg-slate-100 dark:bg-white/10 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all
                                    {{ $inc['tasa'] >= 25 ? 'bg-red-500' : ($inc['tasa'] >= 15 ? 'bg-amber-500' : 'bg-slate-400') }}"
                                     style="width: {{ min($inc['tasa'] * 2, 100) }}%"></div>
                            </div>
                        </div>
                    </td>

                    {{-- Último no-show --}}
                    <td class="py-3 text-center hidden md:table-cell">
                        <span class="text-xs text-slate-400">{{ $inc['ultimo_ns'] }}</span>
                    </td>

                    {{-- Riesgo badge --}}
                    <td class="py-3 text-center">
                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full border
                            {{ $inc['riesgo'] === 'alto'
                                ? 'bg-red-100 dark:bg-red-500/10 text-red-600 dark:text-red-400 border-red-200 dark:border-red-500/20'
                                : ($inc['riesgo'] === 'medio'
                                    ? 'bg-amber-100 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-500/20'
                                    : 'bg-slate-100 dark:bg-white/10 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-white/10') }}">
                            {{ ucfirst($inc['riesgo']) }}
                        </span>
                    </td>

                    {{-- Anticipo sugerido --}}
                    <td class="py-3 text-right hidden lg:table-cell">
                        <div class="flex items-center justify-end gap-2">
                            <span class="text-slate-400 text-xs line-through">S/ {{ $inc['anticipo_actual'] }}</span>
                            <span class="font-bold text-slate-900 dark:text-white">
                                S/ {{ $inc['anticipo_sug'] }}
                            </span>
                            @if($inc['anticipo_sug'] > $inc['anticipo_actual'])
                            <i class="ph-fill ph-arrow-up text-red-500 text-xs"></i>
                            @endif
                        </div>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Nota informativa --}}
    <div class="mt-4 p-3 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 rounded-xl flex items-start gap-2">
        <i class="ph-fill ph-lightbulb text-amber-500 shrink-0 mt-0.5"></i>
        <p class="text-xs text-amber-700 dark:text-amber-400">
            <span class="font-bold">Tip:</span> Podés configurar anticipos personalizados por cliente desde
            <a href="/partner/horarios" class="underline font-bold hover:text-amber-600 transition-colors">Horarios y Precios</a>.
            Los clientes de riesgo alto verán automáticamente un anticipo mayor al reservar.
        </p>
    </div>
</div>

@push('scripts')
<script>
    function seleccionarPeriodo(btn) {
        document.querySelectorAll('.periodo-btn').forEach(b => {
            b.classList.remove('bg-brand-500', 'text-white', 'shadow');
            b.classList.add('text-slate-500', 'dark:text-slate-400', 'hover:text-slate-900', 'dark:hover:text-white');
            
            if (b === btn) {
                b.classList.add('bg-brand-500', 'text-white', 'shadow');
                b.classList.remove('text-slate-500', 'dark:text-slate-400', 'hover:text-slate-900', 'dark:hover:text-white');
            }
        });
    }
</script>
@endpush

@endsection
