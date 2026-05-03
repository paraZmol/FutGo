@extends('layouts.partner')
@section('title', 'Ingresos | FutGo Partner')
@section('page-title', 'Ingresos')
@section('page-subtitle', 'Complejo Deportivo El 10 · Resumen financiero')

@section('content')

@php
$resumen = [
    'mes_actual'    => 3240.00,
    'mes_anterior'  => 2740.00,
    'anticipo_mes'  => 1240.00,
    'efectivo_mes'  => 2000.00,
    'comision_plat' => 0.00,
    'neto_mes'      => 3240.00,
];

$meses = [
    ['mes' => 'Nov 2024', 'bruto' => 1840.00, 'anticipo' => 720.00, 'efectivo' => 1120.00, 'reservas' => 76,  'comision' => 0.00, 'neto' => 1840.00],
    ['mes' => 'Dic 2024', 'bruto' => 2100.00, 'anticipo' => 810.00, 'efectivo' => 1290.00, 'reservas' => 87,  'comision' => 0.00, 'neto' => 2100.00],
    ['mes' => 'Ene 2025', 'bruto' => 1650.00, 'anticipo' => 630.00, 'efectivo' => 1020.00, 'reservas' => 68,  'comision' => 0.00, 'neto' => 1650.00],
    ['mes' => 'Feb 2025', 'bruto' => 2380.00, 'anticipo' => 920.00, 'efectivo' => 1460.00, 'reservas' => 98,  'comision' => 0.00, 'neto' => 2380.00],
    ['mes' => 'Mar 2025', 'bruto' => 2740.00, 'anticipo' => 1060.00,'efectivo' => 1680.00, 'reservas' => 113, 'comision' => 0.00, 'neto' => 2740.00],
    ['mes' => 'Abr 2025', 'bruto' => 3100.00, 'anticipo' => 1190.00,'efectivo' => 1910.00, 'reservas' => 128, 'comision' => 0.00, 'neto' => 3100.00],
    ['mes' => 'May 2025', 'bruto' => 3240.00, 'anticipo' => 1240.00,'efectivo' => 2000.00, 'reservas' => 124, 'comision' => 0.00, 'neto' => 3240.00],
];
$max_bruto = max(array_column($meses, 'bruto'));

$transacciones = [
    ['id' => 'T001', 'fecha' => 'Hoy 20:00', 'cliente' => 'Pedro Huanca',   'cancha' => 'Cancha 1', 'tipo' => 'anticipo', 'canal' => 'app',   'monto' => 30.00],
    ['id' => 'T002', 'fecha' => 'Hoy 19:00', 'cliente' => 'Roberto Silva',  'cancha' => 'Cancha 1', 'tipo' => 'anticipo', 'canal' => 'app',   'monto' => 30.00],
    ['id' => 'T003', 'fecha' => 'Hoy 18:00', 'cliente' => 'Ana Gutierrez',  'cancha' => 'Cancha 2', 'tipo' => 'anticipo', 'canal' => 'app',   'monto' => 30.00],
    ['id' => 'T004', 'fecha' => 'Hoy 09:00', 'cliente' => 'Presencial',      'cancha' => 'Cancha 1', 'tipo' => 'efectivo', 'canal' => 'staff', 'monto' => 80.00],
    ['id' => 'T005', 'fecha' => 'Hoy 08:00', 'cliente' => 'Luis Torres',    'cancha' => 'Cancha 2', 'tipo' => 'saldo',    'canal' => 'caja',  'monto' => 50.00],
    ['id' => 'T006', 'fecha' => 'Hoy 07:00', 'cliente' => 'Mario Quispe',   'cancha' => 'Cancha 1', 'tipo' => 'saldo',    'canal' => 'caja',  'monto' => 50.00],
    ['id' => 'T007', 'fecha' => 'Ayer 21:00', 'cliente' => 'Jorge Flores',  'cancha' => 'Cancha 3', 'tipo' => 'anticipo', 'canal' => 'app',   'monto' => 40.00],
    ['id' => 'T008', 'fecha' => 'Ayer 20:00', 'cliente' => 'Miguel Castro', 'cancha' => 'Cancha 2', 'tipo' => 'anticipo', 'canal' => 'app',   'monto' => 30.00],
];

$ingresos_cancha = [
    ['cancha' => 'Cancha 1', 'tipo' => 'Fútbol 5', 'bruto' => 1380.00, 'reservas' => 42, 'pct' => 43],
    ['cancha' => 'Cancha 2', 'tipo' => 'Fútbol 5', 'bruto' => 1040.00, 'reservas' => 34, 'pct' => 32],
    ['cancha' => 'Cancha 3', 'tipo' => 'Fútbol 7', 'bruto' =>  620.00, 'reservas' => 18, 'pct' => 19],
    ['cancha' => 'Cancha 4', 'tipo' => 'Fútbol 7', 'bruto' =>  200.00, 'reservas' =>  6, 'pct' =>  6],
];
@endphp

{{-- CONTROLES --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div class="flex items-center gap-1 glass-card rounded-xl p-1 border border-slate-200 dark:border-white/10 overflow-x-auto no-scrollbar pb-1 w-full sm:w-auto">
        @foreach(['Este mes' => 'mes', 'Trimestre' => 'trim', 'Semestre' => 'sem', 'Año' => 'anio'] as $label => $val)
        <button onclick="seleccionarPeriodo(this)"
                class="periodo-btn shrink-0 px-3 py-1.5 rounded-lg text-sm font-semibold transition-all
            {{ $val === 'mes' ? 'bg-brand-500 text-white shadow' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>
    <div class="flex gap-2 w-full sm:w-auto">
        <button class="flex-1 sm:flex-none justify-center flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-500 dark:text-slate-400 hover:border-brand-500 hover:text-brand-500 transition-colors">
            <i class="ph-bold ph-file-csv"></i> Exportar CSV
        </button>
        <button class="flex-1 sm:flex-none justify-center flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-500 dark:text-slate-400 hover:border-brand-500 hover:text-brand-500 transition-colors">
            <i class="ph-bold ph-file-pdf"></i> PDF
        </button>
    </div>
</div>

{{-- KPIs FINANCIEROS --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['label' => 'Ingresos brutos',   'val' => 'S/ '.number_format($resumen['mes_actual'],2),   'sub' => '+S/ '.number_format($resumen['mes_actual']-$resumen['mes_anterior'],2).' vs mes ant.', 'icon' => 'ph-money',        'color' => 'text-brand-500',  'bg' => 'bg-brand-500/10',  'trend' => 'up'],
        ['label' => 'Anticipo digital',  'val' => 'S/ '.number_format($resumen['anticipo_mes'],2),  'sub' => round($resumen['anticipo_mes']/$resumen['mes_actual']*100).'% del total',              'icon' => 'ph-credit-card',  'color' => 'text-blue-500',   'bg' => 'bg-blue-500/10',   'trend' => 'up'],
        ['label' => 'Efectivo en caja',  'val' => 'S/ '.number_format($resumen['efectivo_mes'],2),  'sub' => round($resumen['efectivo_mes']/$resumen['mes_actual']*100).'% del total',              'icon' => 'ph-coins',        'color' => 'text-purple-500', 'bg' => 'bg-purple-500/10', 'trend' => 'up'],
        ['label' => 'Comisión plataforma','val' => 'S/ '.number_format($resumen['comision_plat'],2), 'sub' => 'Fase de penetración — 0%',                                                           'icon' => 'ph-percent',      'color' => 'text-slate-400',  'bg' => 'bg-slate-100 dark:bg-white/5', 'trend' => 'neutral'],
    ] as $k)
    <div class="glass-card rounded-2xl p-5">
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl {{ $k['bg'] }} flex items-center justify-center">
                <i class="ph-fill {{ $k['icon'] }} {{ $k['color'] }} text-xl"></i>
            </div>
            @if($k['trend'] === 'up')
            <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-brand-500/10 text-brand-600 dark:text-brand-400 flex items-center gap-1">
                <i class="ph-bold ph-trend-up"></i> Subió
            </span>
            @endif
        </div>
        <p class="text-2xl font-extrabold text-slate-900 dark:text-white mb-0.5">{{ $k['val'] }}</p>
        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $k['label'] }}</p>
        <p class="text-[10px] text-slate-400 mt-1">{{ $k['sub'] }}</p>
    </div>
    @endforeach
</div>
    {{-- PROYECCIÓN DE INGRESOS (DATO FICTICIO / BI) --}}
    <div class="glass-card rounded-2xl p-6 mb-6 relative overflow-hidden group">
        {{-- Efecto de fondo --}}
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 bg-brand-500/5 rounded-full blur-3xl transition-all group-hover:bg-brand-500/10"></div>
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-3">
                    <span class="flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-brand-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-brand-500"></span>
                    </span>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-widest">Proyección de Crecimiento</h2>
                </div>
                <p class="text-2xl font-black text-slate-900 dark:text-white mb-2">S/ 4,500.00 <span class="text-xs font-bold text-brand-500 ml-1">Potencial estimado</span></p>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md">
                    Basado en tus tendencias actuales y la demanda de la zona, podrías alcanzar este ingreso mensual optimizando tus horarios de noche.
                </p>
            </div>

            <div class="flex-1 h-20">
                {{-- SVG Mini Chart Trend --}}
                <svg class="w-full h-full" viewBox="0 0 400 100" preserveAspectRatio="none">
                    <path d="M0,80 L50,75 L100,85 L150,60 L200,65 L250,40 L300,45 L350,20 L400,10" 
                          fill="none" stroke="#22c55e" stroke-width="3" stroke-linecap="round" stroke-dasharray="0" />
                    <path d="M200,65 L250,40 L300,45 L350,20 L400,10 L400,100 L200,100 Z" 
                          fill="url(#grad-project)" opacity="0.1" />
                    <defs>
                        <linearGradient id="grad-project" x1="0%" y1="0%" x2="0%" y2="100%">
                            <stop offset="0%" style="stop-color:#22c55e;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#22c55e;stop-opacity:0" />
                        </linearGradient>
                    </defs>
                    {{-- Punto de hoy --}}
                    <circle cx="200" cy="65" r="5" fill="#22c55e" stroke="white" stroke-width="2" />
                    <text x="180" y="95" fill="#22c55e" class="text-[10px] font-bold">Hoy</text>
                    <text x="360" y="95" fill="#94a3b8" class="text-[10px] font-bold">Junio</text>
                </svg>
            </div>

            <button class="shrink-0 bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-6 py-3 rounded-2xl font-bold text-sm hover:scale-105 transition-all shadow-xl">
                Ver estrategias
            </button>
        </div>
    </div>
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-5">

    {{-- GRÁFICO HISTÓRICO (BARS + LINE OVERLAY) --}}
    <div class="xl:col-span-2 glass-card rounded-2xl p-5 relative overflow-hidden">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="ph-fill ph-chart-bar text-brand-500"></i> Ingresos históricos
                </h2>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Consolidado mensual</p>
            </div>
            <div class="flex items-center gap-4 text-[10px] font-bold uppercase tracking-wider">
                <span class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                    <span class="w-3 h-3 rounded-sm bg-brand-500 inline-block"></span> Anticipo
                </span>
                <span class="flex items-center gap-1.5 text-slate-400">
                    <span class="w-3 h-3 rounded-sm bg-slate-200 dark:bg-white/10 inline-block"></span> Efectivo
                </span>
            </div>
        </div>

        <div class="relative h-48 mb-6">
            {{-- Líneas de fondo (Grid) --}}
            <div class="absolute inset-0 flex flex-col justify-between pointer-events-none">
                <div class="border-b border-slate-100 dark:border-white/5 w-full h-0"></div>
                <div class="border-b border-slate-100 dark:border-white/5 w-full h-0"></div>
                <div class="border-b border-slate-100 dark:border-white/5 w-full h-0"></div>
                <div class="border-b border-slate-100 dark:border-white/5 w-full h-0"></div>
            </div>

            {{-- Contenedor de barras --}}
            <div class="absolute inset-0 flex items-end gap-3 sm:gap-4 px-2">
                @foreach($meses as $m)
                @php
                    $pctBruto    = round(($m['bruto'] / $max_bruto) * 100);
                    $pctAnticipo = round(($m['anticipo'] / $m['bruto']) * 100);
                    $esMesActual = $m['mes'] === 'May 2025';
                @endphp
                <div class="flex-1 h-full flex flex-col justify-end items-center group relative">
                    {{-- Tooltip simple --}}
                    <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-20">
                        S/ {{ number_format($m['bruto']) }}
                    </div>
                    
                    {{-- Barra --}}
                    <div class="w-full max-w-[32px] rounded-t-md overflow-hidden flex flex-col-reverse shadow-sm transition-all group-hover:scale-105" 
                         style="height: {{ $pctBruto }}%">
                        {{-- Parte Anticipo (Verde) --}}
                        <div class="w-full bg-brand-500" style="height: {{ $pctAnticipo }}%"></div>
                        {{-- Parte Efectivo (Gris/Azul) --}}
                        <div class="w-full bg-slate-200 dark:bg-white/10 flex-1"></div>
                    </div>
                    
                    <span class="text-[10px] font-bold mt-2 {{ $esMesActual ? 'text-brand-500' : 'text-slate-400' }}">
                        {{ substr($m['mes'], 0, 3) }}
                    </span>
                </div>
                @endforeach
            </div>

            {{-- SVG Line Overlay (Para darle el toque "BI") --}}
            <svg class="absolute inset-0 pointer-events-none h-full w-full" viewBox="0 0 700 200" preserveAspectRatio="none">
                <path d="M50,160 Q150,140 250,150 T450,100 T650,60" fill="none" stroke="rgba(34, 197, 94, 0.3)" stroke-width="2" stroke-dasharray="4" />
            </svg>
        </div>

        <div class="flex justify-between items-center pt-4 border-t border-slate-100 dark:border-white/5">
            <div class="flex items-center gap-6">
                <div>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Total acumulado</p>
                    <p class="text-xl font-black text-slate-900 dark:text-white">S/ 17,050.00</p>
                </div>
                <div class="w-px h-8 bg-slate-100 dark:bg-white/10"></div>
                <div>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Reservas</p>
                    <p class="text-xl font-black text-slate-700 dark:text-slate-300">694</p>
                </div>
            </div>
            <div class="flex items-center gap-2 text-brand-500 text-xs font-bold bg-brand-500/10 px-3 py-1.5 rounded-xl">
                <i class="ph-bold ph-trend-up"></i>
                +12.5% crec. anual
            </div>
        </div>
    </div>

    {{-- INGRESOS POR CANCHA --}}
    <div class="glass-card rounded-2xl p-5">
        <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-5">
            <i class="ph-fill ph-soccer-ball text-brand-500"></i> Por cancha
        </h2>
        <div class="space-y-4">
            @foreach($ingresos_cancha as $ic)
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <div>
                        <span class="text-sm font-bold text-slate-800 dark:text-white">{{ $ic['cancha'] }}</span>
                        <span class="text-xs text-slate-400 ml-1.5">{{ $ic['tipo'] }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-sm font-extrabold text-slate-900 dark:text-white">S/ {{ number_format($ic['bruto'],2) }}</span>
                        <span class="text-[10px] text-slate-400 block">{{ $ic['reservas'] }} reservas</span>
                    </div>
                </div>
                <div class="h-2 bg-slate-100 dark:bg-white/10 rounded-full overflow-hidden">
                    <div class="h-full rounded-full bg-brand-500 transition-all"
                         style="width: {{ $ic['pct'] }}%"></div>
                </div>
                <p class="text-[10px] text-slate-400 mt-1 text-right">{{ $ic['pct'] }}% del total</p>
            </div>
            @endforeach
        </div>

        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-white/5 flex justify-between text-sm">
            <span class="text-slate-500 dark:text-slate-400 font-medium">Total</span>
            <span class="font-extrabold text-brand-500">S/ {{ number_format($resumen['mes_actual'],2) }}</span>
        </div>
    </div>

</div>

{{-- TABLA HISTÓRICA POR MES --}}
<div class="glass-card rounded-2xl p-5 mb-5">
    <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-4">
        <i class="ph-fill ph-table text-brand-500"></i> Detalle mensual
    </h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-white/5">
                    <th class="text-left pb-3">Mes</th>
                    <th class="text-right pb-3">Reservas</th>
                    <th class="text-right pb-3">Anticipo</th>
                    <th class="text-right pb-3 hidden sm:table-cell">Efectivo</th>
                    <th class="text-right pb-3 hidden md:table-cell">Comisión</th>
                    <th class="text-right pb-3">Neto</th>
                    <th class="text-center pb-3 hidden lg:table-cell">Tendencia</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-white/5">
                @foreach(array_reverse($meses) as $i => $m)
                <tr class="hover:bg-slate-50 dark:hover:bg-white/3 transition-colors {{ $i === 0 ? 'font-semibold' : '' }}">
                    <td class="py-3">
                        <div class="flex items-center gap-2">
                            @if($i === 0)
                            <span class="w-2 h-2 rounded-full bg-brand-500 inline-block animate-pulse"></span>
                            @endif
                            <span class="{{ $i === 0 ? 'text-brand-600 dark:text-brand-400 font-bold' : 'text-slate-700 dark:text-slate-300' }}">
                                {{ $m['mes'] }}
                            </span>
                            @if($i === 0)
                            <span class="text-[10px] bg-brand-500/10 text-brand-500 px-1.5 py-0.5 rounded-md font-bold">Actual</span>
                            @endif
                        </div>
                    </td>
                    <td class="py-3 text-right text-slate-600 dark:text-slate-400">{{ $m['reservas'] }}</td>
                    <td class="py-3 text-right text-blue-600 dark:text-blue-400 font-semibold">S/ {{ number_format($m['anticipo'],2) }}</td>
                    <td class="py-3 text-right text-slate-600 dark:text-slate-400 hidden sm:table-cell">S/ {{ number_format($m['efectivo'],2) }}</td>
                    <td class="py-3 text-right text-slate-400 hidden md:table-cell">
                        {{ $m['comision'] > 0 ? 'S/ '.number_format($m['comision'],2) : '–' }}
                    </td>
                    <td class="py-3 text-right font-extrabold text-slate-900 dark:text-white">S/ {{ number_format($m['neto'],2) }}</td>
                    <td class="py-3 text-center hidden lg:table-cell">
                        @if($i < count($meses)-1)
                        @php
                            $anterior = array_reverse($meses)[$i+1]['bruto'];
                            $diff = $m['bruto'] - $anterior;
                        @endphp
                        <span class="text-xs font-bold flex items-center justify-center gap-1
                            {{ $diff >= 0 ? 'text-brand-500' : 'text-red-500' }}">
                            <i class="ph-bold {{ $diff >= 0 ? 'ph-trend-up' : 'ph-trend-down' }}"></i>
                            {{ $diff >= 0 ? '+' : '' }}S/ {{ number_format(abs($diff),0) }}
                        </span>
                        @else
                        <span class="text-slate-300 text-xs">–</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t-2 border-slate-200 dark:border-white/10">
                    <td class="pt-3 font-bold text-slate-900 dark:text-white">Total período</td>
                    <td class="pt-3 text-right font-bold text-slate-700 dark:text-slate-300">{{ array_sum(array_column($meses,'reservas')) }}</td>
                    <td class="pt-3 text-right font-bold text-blue-600 dark:text-blue-400">S/ {{ number_format(array_sum(array_column($meses,'anticipo')),2) }}</td>
                    <td class="pt-3 text-right font-bold text-slate-600 dark:text-slate-400 hidden sm:table-cell">S/ {{ number_format(array_sum(array_column($meses,'efectivo')),2) }}</td>
                    <td class="pt-3 text-right text-slate-400 hidden md:table-cell">S/ 0.00</td>
                    <td class="pt-3 text-right font-extrabold text-brand-500 text-lg">S/ {{ number_format(array_sum(array_column($meses,'neto')),2) }}</td>
                    <td class="hidden lg:table-cell"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{-- ÚLTIMAS TRANSACCIONES --}}
<div class="glass-card rounded-2xl p-5">
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <i class="ph-fill ph-receipt text-brand-500"></i> Últimas transacciones
        </h2>
        <a href="/partner/reservas" class="text-xs font-bold text-brand-500 hover:text-brand-600 transition-colors">
            Ver todas →
        </a>
    </div>
    <div class="divide-y divide-slate-50 dark:divide-white/5">
        @foreach($transacciones as $t)
        <div class="flex items-center gap-4 py-3">
            {{-- Tipo icono --}}
            <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0
                {{ $t['tipo'] === 'anticipo' ? 'bg-blue-500/10' : ($t['tipo'] === 'efectivo' ? 'bg-purple-500/10' : 'bg-brand-500/10') }}">
                <i class="ph-fill
                    {{ $t['tipo'] === 'anticipo' ? 'ph-credit-card text-blue-500' : ($t['tipo'] === 'efectivo' ? 'ph-coins text-purple-500' : 'ph-cash-register text-brand-500') }}
                    text-lg"></i>
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-slate-800 dark:text-white truncate">{{ $t['cliente'] }}</p>
                <p class="text-xs text-slate-400">{{ $t['fecha'] }} · {{ $t['cancha'] }}</p>
            </div>

            {{-- Canal --}}
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full hidden sm:block
                {{ $t['canal'] === 'app'   ? 'bg-blue-500/10 text-blue-500'
                : ($t['canal'] === 'staff' ? 'bg-purple-500/10 text-purple-500'
                : 'bg-slate-100 dark:bg-white/10 text-slate-400') }}">
                {{ ucfirst($t['canal']) }}
            </span>

            {{-- Tipo --}}
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full
                {{ $t['tipo'] === 'anticipo' ? 'bg-blue-500/10 text-blue-500'
                : ($t['tipo'] === 'efectivo' ? 'bg-purple-500/10 text-purple-500'
                : 'bg-brand-500/10 text-brand-500') }}">
                {{ ucfirst($t['tipo']) }}
            </span>

            {{-- Monto --}}
            <span class="font-extrabold text-slate-900 dark:text-white shrink-0">
                +S/ {{ number_format($t['monto'],2) }}
            </span>
        </div>
        @endforeach
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
