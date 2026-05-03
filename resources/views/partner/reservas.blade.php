@extends('layouts.partner')
@section('title', 'Reservas | FutGo Partner')
@section('page-title', 'Reservas')
@section('page-subtitle', 'Complejo Deportivo El 10 · ' . date('d \d\e F, Y'))

@section('content')

@php
$reservas = [
    ['id' => 'R001', 'hora' => '07:00', 'hora_fin' => '08:00', 'cancha' => 'Cancha 1', 'tipo' => 'Fútbol 5', 'cliente' => 'Mario Quispe',    'telefono' => '+51 987 111 222', 'canal' => 'app',    'estado' => 'completada', 'monto' => 80.00,  'anticipo' => 30.00, 'checkin' => true,  'checkin_hora' => '07:04'],
    ['id' => 'R002', 'hora' => '08:00', 'hora_fin' => '09:00', 'cancha' => 'Cancha 2', 'tipo' => 'Fútbol 5', 'cliente' => 'Luis Torres',     'telefono' => '+51 987 333 444', 'canal' => 'app',    'estado' => 'completada', 'monto' => 80.00,  'anticipo' => 30.00, 'checkin' => true,  'checkin_hora' => '07:58'],
    ['id' => 'R003', 'hora' => '09:00', 'hora_fin' => '10:00', 'cancha' => 'Cancha 1', 'tipo' => 'Fútbol 5', 'cliente' => 'Presencial',         'telefono' => '–',               'canal' => 'staff',  'estado' => 'completada', 'monto' => 80.00,  'anticipo' => 80.00, 'checkin' => true,  'checkin_hora' => '09:02'],
    ['id' => 'R004', 'hora' => '10:00', 'hora_fin' => '11:00', 'cancha' => 'Cancha 3', 'tipo' => 'Fútbol 7', 'cliente' => 'Carlos Mamani',   'telefono' => '+51 987 555 666', 'canal' => 'app',    'estado' => 'noshow',     'monto' => 100.00, 'anticipo' => 30.00, 'checkin' => false, 'checkin_hora' => null],
    ['id' => 'R005', 'hora' => '18:00', 'hora_fin' => '19:00', 'cancha' => 'Cancha 1', 'tipo' => 'Fútbol 5', 'cliente' => 'Pedro Huanca',    'telefono' => '+51 987 777 888', 'canal' => 'app',    'estado' => 'confirmada', 'monto' => 90.00,  'anticipo' => 30.00, 'checkin' => false, 'checkin_hora' => null],
    ['id' => 'R006', 'hora' => '18:00', 'hora_fin' => '19:00', 'cancha' => 'Cancha 2', 'tipo' => 'Fútbol 5', 'cliente' => 'Ana Gutierrez',   'telefono' => '+51 987 999 000', 'canal' => 'app',    'estado' => 'confirmada', 'monto' => 90.00,  'anticipo' => 30.00, 'checkin' => false, 'checkin_hora' => null],
    ['id' => 'R007', 'hora' => '19:00', 'hora_fin' => '20:00', 'cancha' => 'Cancha 1', 'tipo' => 'Fútbol 5', 'cliente' => 'Roberto Silva',   'telefono' => '+51 987 112 233', 'canal' => 'app',    'estado' => 'confirmada', 'monto' => 90.00,  'anticipo' => 30.00, 'checkin' => false, 'checkin_hora' => null],
    ['id' => 'R008', 'hora' => '19:00', 'hora_fin' => '20:00', 'cancha' => 'Cancha 3', 'tipo' => 'Fútbol 7', 'cliente' => 'Jorge Flores',    'telefono' => '+51 987 445 566', 'canal' => 'app',    'estado' => 'confirmada', 'monto' => 120.00, 'anticipo' => 40.00, 'checkin' => false, 'checkin_hora' => null],
    ['id' => 'R009', 'hora' => '20:00', 'hora_fin' => '21:00', 'cancha' => 'Cancha 2', 'tipo' => 'Fútbol 5', 'cliente' => 'Miguel Castro',   'telefono' => '+51 987 778 899', 'canal' => 'app',    'estado' => 'confirmada', 'monto' => 90.00,  'anticipo' => 30.00, 'checkin' => false, 'checkin_hora' => null],
    ['id' => 'R010', 'hora' => '21:00', 'hora_fin' => '22:00', 'cancha' => 'Cancha 1', 'tipo' => 'Fútbol 5', 'cliente' => '–',               'telefono' => '–',               'canal' => '–',      'estado' => 'disponible', 'monto' => 0,      'anticipo' => 0,     'checkin' => false, 'checkin_hora' => null],
];

$resumen = [
    'total_reservas' => 9,
    'completadas'    => 3,
    'confirmadas'    => 5,
    'noshow'         => 1,
    'ingresos_hoy'   => 560.00,
    'anticipo_hoy'   => 230.00,
    'efectivo_hoy'   => 330.00,
];
@endphp

{{-- ============================================================
     MINI STATS + FILTROS
============================================================ --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    @foreach([
        ['label' => 'Total reservas', 'val' => $resumen['total_reservas'], 'icon' => 'ph-calendar-check', 'color' => 'text-brand-500',  'bg' => 'bg-brand-500/10'],
        ['label' => 'Completadas',    'val' => $resumen['completadas'],    'icon' => 'ph-check-circle',   'color' => 'text-slate-400',  'bg' => 'bg-slate-100 dark:bg-white/5'],
        ['label' => 'Pendientes',     'val' => $resumen['confirmadas'],    'icon' => 'ph-clock',          'color' => 'text-blue-500',   'bg' => 'bg-blue-500/10'],
        ['label' => 'No-shows',       'val' => $resumen['noshow'],         'icon' => 'ph-user-minus',     'color' => 'text-amber-500',  'bg' => 'bg-amber-500/10'],
    ] as $s)
    <div class="glass-card rounded-2xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl {{ $s['bg'] }} flex items-center justify-center shrink-0">
            <i class="ph-fill {{ $s['icon'] }} {{ $s['color'] }} text-xl"></i>
        </div>
        <div>
            <p class="text-xl font-extrabold text-slate-900 dark:text-white">{{ $s['val'] }}</p>
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">{{ $s['label'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- ============================================================
     CONTROLES
============================================================ --}}
<div class="flex flex-col sm:flex-row gap-3 mb-5">

    {{-- Selector de periodo --}}
    <div class="flex items-center gap-1 glass-card rounded-xl p-1 border border-slate-200 dark:border-white/10">
        @foreach(['Hoy' => 'hoy', 'Semana' => 'semana', 'Mes' => 'mes', 'Fecha' => 'fecha'] as $label => $val)
        <button onclick="selPeriodo('{{ $val }}', this)"
                class="periodo-btn px-3 py-1.5 rounded-lg text-sm font-semibold transition-all
                {{ $val === 'hoy' ? 'bg-brand-500 text-white shadow' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- Input fecha (solo visible cuando se elige "Fecha") --}}
    <div id="input-fecha" class="hidden items-center gap-2 glass-card rounded-xl px-4 py-2.5 border border-slate-200 dark:border-white/10 focus-within:border-brand-500 transition-all">
        <i class="ph-fill ph-calendar-blank text-brand-500"></i>
        <input type="date" id="fecha-especifica" value="{{ date('Y-m-d') }}"
               class="text-sm font-semibold bg-transparent outline-none text-slate-800 dark:text-white cursor-pointer">
    </div>

    {{-- Filtro cancha (dropdown custom) --}}
    <div class="relative" id="drop-cancha">
        <button onclick="toggleDrop('cancha')"
                class="flex items-center gap-2 glass-card rounded-xl px-4 py-2.5 border border-slate-200 dark:border-white/10 hover:border-brand-500 transition-all text-sm font-semibold text-slate-700 dark:text-slate-200 whitespace-nowrap">
            <i class="ph-fill ph-soccer-ball text-brand-500"></i>
            <span id="label-cancha">Todas las canchas</span>
            <i class="ph-bold ph-caret-down text-slate-400 text-xs ml-1"></i>
        </button>
        <div id="menu-cancha" class="hidden absolute top-full left-0 mt-1 w-48 bg-white dark:bg-dark-900 border border-slate-200 dark:border-white/10 rounded-2xl shadow-xl z-20 overflow-hidden py-1">
            @foreach(['Todas las canchas' => 'todas', 'Cancha 1' => 'Cancha 1', 'Cancha 2' => 'Cancha 2', 'Cancha 3' => 'Cancha 3'] as $label => $val)
            <button onclick="elegirCancha('{{ $val }}', '{{ $label }}')"
                    class="w-full text-left px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-brand-500/10 hover:text-brand-600 dark:hover:text-brand-400 transition-colors">
                {{ $label }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- Filtro estado (dropdown custom) --}}
    <div class="relative" id="drop-estado">
        <button onclick="toggleDrop('estado')"
                class="flex items-center gap-2 glass-card rounded-xl px-4 py-2.5 border border-slate-200 dark:border-white/10 hover:border-brand-500 transition-all text-sm font-semibold text-slate-700 dark:text-slate-200 whitespace-nowrap">
            <i class="ph-fill ph-funnel text-brand-500"></i>
            <span id="label-estado">Todos los estados</span>
            <i class="ph-bold ph-caret-down text-slate-400 text-xs ml-1"></i>
        </button>
        <div id="menu-estado" class="hidden absolute top-full left-0 mt-1 w-48 bg-white dark:bg-dark-900 border border-slate-200 dark:border-white/10 rounded-2xl shadow-xl z-20 overflow-hidden py-1">
            @foreach(['Todos los estados' => 'todos', 'Confirmada' => 'confirmada', 'Completada' => 'completada', 'No-show' => 'noshow'] as $label => $val)
            <button onclick="elegirEstado('{{ $val }}', '{{ $label }}')"
                    class="w-full text-left px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-brand-500/10 hover:text-brand-600 dark:hover:text-brand-400 transition-colors flex items-center gap-2">
                @if($val === 'confirmada')  <span class="w-2 h-2 rounded-full bg-brand-500 shrink-0"></span>
                @elseif($val === 'completada') <span class="w-2 h-2 rounded-full bg-slate-400 shrink-0"></span>
                @elseif($val === 'noshow')  <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                @else <span class="w-2 h-2 rounded-full bg-slate-200 shrink-0"></span>
                @endif
                {{ $label }}
            </button>
            @endforeach
        </div>
    </div>

    <div class="sm:ml-auto flex gap-2">
        {{-- Reserva presencial rápida --}}
        <button onclick="document.getElementById('modal-walkin').classList.remove('hidden')"
                class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-purple-500/10 border border-purple-500/20 text-purple-600 dark:text-purple-400 font-bold text-sm hover:bg-purple-500 hover:text-white transition-colors">
            <i class="ph-bold ph-user-plus"></i>
            <span class="hidden sm:inline">Presencial</span>
        </button>
        {{-- Exportar --}}
        <button onclick="alert('Función de exportación a Excel/PDF se implementará próximamente.')"
                class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 text-slate-500 dark:text-slate-400 font-semibold text-sm hover:border-brand-500 hover:text-brand-500 transition-colors">
            <i class="ph-bold ph-export"></i>
            <span class="hidden sm:inline">Exportar</span>
        </button>
    </div>
</div>

{{-- ============================================================
     TABLA DE RESERVAS
============================================================ --}}
<div class="glass-card rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 dark:border-white/5 bg-slate-50/50 dark:bg-white/[0.02]">
                    <th class="text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider px-5 py-3">Hora</th>
                    <th class="text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 py-3">Cancha</th>
                    <th class="text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 py-3">Cliente</th>
                    <th class="text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 py-3 hidden md:table-cell">Canal</th>
                    <th class="text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 py-3">Estado</th>
                    <th class="text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 py-3 hidden lg:table-cell">Check-in</th>
                    <th class="text-right text-[10px] font-bold text-slate-400 uppercase tracking-wider px-5 py-3">Monto</th>
                    <th class="px-3 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-white/5">
                @foreach($reservas as $r)
                <tr class="reserva-row hover:bg-slate-100/80 dark:hover:bg-brand-500/5 transition-all duration-200 group
                    {{ $r['estado'] === 'noshow' ? 'bg-amber-50/30 dark:bg-amber-500/[0.03]' : '' }}
                    {{ $r['estado'] === 'disponible' ? 'opacity-40' : '' }}"
                    data-cancha="{{ $r['cancha'] }}" data-estado="{{ $r['estado'] }}">

                    {{-- Hora --}}
                    <td class="px-5 py-3.5">
                        <div class="font-bold text-slate-900 dark:text-slate-100">{{ $r['hora'] }}</div>
                        <div class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">{{ $r['hora_fin'] }}</div>
                    </td>

                    {{-- Cancha --}}
                    <td class="px-3 py-3.5">
                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $r['cancha'] }}</span>
                        <span class="block text-[10px] text-slate-500 dark:text-slate-400">{{ $r['tipo'] }}</span>
                    </td>

                    {{-- Cliente --}}
                    <td class="px-3 py-3.5">
                        <div class="flex items-center gap-2">
                            @if($r['cliente'] !== '–')
                            <div class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-white/10 flex items-center justify-center text-[10px] font-bold text-slate-500 dark:text-slate-400 shrink-0">
                                {{ strtoupper(substr($r['cliente'], 0, 2)) }}
                            </div>
                            @endif
                            <div>
                                <p class="font-semibold text-slate-800 dark:text-white text-sm">{{ $r['cliente'] }}</p>
                                @if($r['telefono'] !== '–')
                                <p class="text-[10px] text-slate-400">{{ $r['telefono'] }}</p>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- Canal --}}
                    <td class="px-3 py-3.5 hidden md:table-cell">
                        @if($r['canal'] === 'app')
                        <span class="bg-blue-500/10 text-blue-600 dark:text-blue-400 text-[10px] font-bold px-2.5 py-1 rounded-full border border-blue-500/20">
                            <i class="ph-fill ph-device-mobile"></i> App
                        </span>
                        @elseif($r['canal'] === 'staff')
                        <span class="bg-purple-500/10 text-purple-600 dark:text-purple-400 text-[10px] font-bold px-2.5 py-1 rounded-full border border-purple-500/20">
                            <i class="ph-fill ph-user"></i> Staff
                        </span>
                        @else
                        <span class="text-slate-300 text-[10px]">–</span>
                        @endif
                    </td>

                    {{-- Estado --}}
                    <td class="px-3 py-3.5">
                        @php
                        $badge = match($r['estado']) {
                            'confirmada'  => 'bg-brand-500/10 text-brand-600 dark:text-brand-400 border-brand-500/20',
                            'completada'  => 'bg-slate-100 dark:bg-white/10 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-white/10',
                            'noshow'      => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
                            'disponible'  => 'bg-slate-50 dark:bg-white/5 text-slate-300 border-slate-200 dark:border-white/5',
                            default       => '',
                        };
                        $label = match($r['estado']) {
                            'confirmada'  => 'Confirmada',
                            'completada'  => 'Completada',
                            'noshow'      => 'No-show',
                            'disponible'  => 'Disponible',
                            default       => $r['estado'],
                        };
                        @endphp
                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full border {{ $badge }}">
                            {{ $label }}
                        </span>
                    </td>

                    {{-- Check-in --}}
                    <td class="px-3 py-3.5 hidden lg:table-cell">
                        @if($r['checkin'])
                        <span class="flex items-center gap-1.5 text-brand-500 text-xs font-semibold">
                            <i class="ph-fill ph-check-circle text-base"></i> {{ $r['checkin_hora'] }}
                        </span>
                        @elseif($r['estado'] === 'noshow')
                        <span class="flex items-center gap-1.5 text-amber-500 text-xs font-semibold">
                            <i class="ph-fill ph-x-circle text-base"></i> No se presentó
                        </span>
                        @elseif($r['estado'] === 'confirmada')
                        <span class="text-slate-300 dark:text-slate-600 text-xs">Pendiente</span>
                        @else
                        <span class="text-slate-300 dark:text-slate-600 text-xs">–</span>
                        @endif
                    </td>

                    {{-- Monto --}}
                    <td class="px-5 py-3.5 text-right">
                        @if($r['monto'] > 0)
                        <p class="font-bold text-slate-900 dark:text-white">S/ {{ number_format($r['monto'], 2) }}</p>
                        <p class="text-[10px] text-brand-500">Anticipo S/ {{ number_format($r['anticipo'], 2) }}</p>
                        @else
                        <span class="text-slate-300">–</span>
                        @endif
                    </td>

                    {{-- Acciones --}}
                    <td class="px-3 py-3.5">
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            @if($r['estado'] === 'confirmada')
                            <button onclick="alert('Acción: Realizar Check-in manual. Esto confirmará la llegada del cliente y marcará la reserva como en curso/completada.')" 
                                    class="w-7 h-7 rounded-lg bg-brand-500/10 text-brand-500 hover:bg-brand-500 hover:text-white flex items-center justify-center transition-colors"
                                    title="Marcar check-in manual">
                                <i class="ph-bold ph-qr-code text-sm"></i>
                            </button>
                            <button onclick="alert('Acción: Marcar como No-show. Se registrará que el cliente no se presentó a su cita.')" 
                                    class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-500 hover:bg-amber-500 hover:text-white flex items-center justify-center transition-colors"
                                    title="Marcar no-show">
                                <i class="ph-bold ph-user-minus text-sm"></i>
                            </button>
                            @endif
                            @if($r['estado'] !== 'disponible')
                            <button class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-white/5 text-slate-400 hover:bg-slate-200 dark:hover:bg-white/10 flex items-center justify-center transition-colors"
                                    title="Ver detalle"
                                    onclick="abrirDetalle('{{ $r['id'] }}')">
                                <i class="ph-bold ph-eye text-sm"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Footer tabla --}}
    <div class="px-5 py-4 border-t border-slate-100 dark:border-white/5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <p class="text-xs text-slate-400">Mostrando {{ count($reservas) }} reservas del día</p>
        <div class="flex flex-wrap gap-4 text-sm">
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Total cobrado:</span>
                <span class="font-extrabold text-slate-900 dark:text-white">S/ {{ number_format($resumen['ingresos_hoy'], 2) }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Anticipo digital:</span>
                <span class="font-bold text-brand-500">S/ {{ number_format($resumen['anticipo_hoy'], 2) }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Efectivo en caja:</span>
                <span class="font-bold text-slate-700 dark:text-slate-300">S/ {{ number_format($resumen['efectivo_hoy'], 2) }}</span>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     MODAL RESERVA PRESENCIAL
============================================================ --}}
<div id="modal-walkin" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
         onclick="document.getElementById('modal-walkin').classList.add('hidden')"></div>

    <div class="relative glass-card rounded-3xl p-6 w-full max-w-md shadow-2xl">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">Nueva Reserva Presencial</h2>
                <p class="text-xs text-slate-400 mt-0.5">Cliente presencial sin reserva previa</p>
            </div>
            <button onclick="document.getElementById('modal-walkin').classList.add('hidden')"
                    class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-white/10 flex items-center justify-center text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors">
                <i class="ph-bold ph-x"></i>
            </button>
        </div>

        <form class="space-y-4">

            <div class="grid grid-cols-2 gap-3">
                <div class="relative">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Cancha</label>
                    <select class="w-full border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm bg-white dark:bg-dark-800 text-slate-900 dark:text-white outline-none focus:border-brand-500 transition-all appearance-none cursor-pointer [&>option]:text-slate-900 dark:[&>option]:text-white">
                        <option>Cancha 1 — F5</option>
                        <option>Cancha 2 — F5</option>
                        <option>Cancha 3 — F7</option>
                    </select>
                    <i class="ph-bold ph-caret-down text-slate-400 absolute right-4 bottom-4 pointer-events-none"></i>
                </div>
                <div class="relative">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Hora inicio</label>
                    <select class="w-full border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm bg-white dark:bg-dark-800 text-slate-900 dark:text-white outline-none focus:border-brand-500 transition-all appearance-none cursor-pointer [&>option]:text-slate-900 dark:[&>option]:text-white">
                        @for($h = 7; $h <= 22; $h++)
                        <option>{{ str_pad($h,2,'0',STR_PAD_LEFT) }}:00</option>
                        @endfor
                    </select>
                    <i class="ph-bold ph-caret-down text-slate-400 absolute right-4 bottom-4 pointer-events-none"></i>
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Nombre del cliente <span class="text-slate-300">(opcional)</span></label>
                <div class="flex items-center gap-3 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 bg-white dark:bg-white/5 focus-within:border-brand-500 focus-within:ring-2 focus-within:ring-brand-500/20 transition-all">
                    <i class="ph-fill ph-user text-slate-400 shrink-0"></i>
                    <input type="text" placeholder="Ej: Juan Pérez"
                           class="flex-1 bg-transparent text-sm outline-none text-slate-900 dark:text-white placeholder-slate-400">
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Monto a cobrar</label>
                <div class="flex items-center gap-3 border-2 border-brand-500 rounded-xl px-4 py-3 bg-brand-500/5">
                    <span class="text-brand-500 font-bold text-lg">S/</span>
                    <input type="number" value="80" min="0"
                           class="flex-1 bg-transparent text-xl font-extrabold outline-none text-slate-900 dark:text-white">
                    <span class="text-slate-400 text-sm">/ hora</span>
                </div>
            </div>

            <div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 rounded-xl px-4 py-3 flex items-start gap-2">
                <i class="ph-fill ph-info text-amber-500 shrink-0 mt-0.5"></i>
                <p class="text-xs text-amber-700 dark:text-amber-400">El pago es 100% en efectivo. Se registrará en el turno activo del Staff.</p>
            </div>

            <div class="flex gap-3 pt-1">
                <button type="button"
                        onclick="document.getElementById('modal-walkin').classList.add('hidden')"
                        class="flex-1 py-3 rounded-xl border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="flex-1 py-3 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-bold text-sm transition-all hover:scale-[1.02] shadow-lg shadow-brand-500/20 flex items-center justify-center gap-2">
                    <i class="ph-bold ph-check"></i> Registrar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL DETALLE RESERVA --}}
<div id="modal-detalle" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
         onclick="document.getElementById('modal-detalle').classList.add('hidden')"></div>
    <div class="relative glass-card rounded-3xl p-6 w-full max-w-sm shadow-2xl text-center">
        <button onclick="document.getElementById('modal-detalle').classList.add('hidden')"
                class="absolute top-4 right-4 w-8 h-8 rounded-xl bg-slate-100 dark:bg-white/10 flex items-center justify-center text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors">
            <i class="ph-bold ph-x"></i>
        </button>
        <p class="text-xs font-bold text-brand-500 uppercase tracking-widest mb-1">Detalle de reserva</p>
        <p id="detalle-id" class="text-xs text-slate-400 mb-4"></p>
        <div class="bg-white dark:bg-dark-900 p-3 rounded-2xl inline-block border border-slate-100 dark:border-white/10 mb-3">
            <img id="detalle-qr" src="" alt="QR" class="w-40 h-40">
        </div>
        <p class="text-xs text-slate-400">QR de acceso del cliente</p>
    </div>
</div>

@push('scripts')
<script>
    function abrirDetalle(id) {
        document.getElementById('detalle-id').textContent = id;
        
        const isDark = document.documentElement.classList.contains('dark');
        const bg = isDark ? '0f172a' : 'ffffff';
        const fg = isDark ? '22c55e' : '000000';
        
        document.getElementById('detalle-qr').src =
            `https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=FUTBO-${id}&bgcolor=${bg}&color=${fg}&qzone=2`;
            
        document.getElementById('modal-detalle').classList.remove('hidden');
    }

    // --- Periodo ---
    function selPeriodo(val, btn) {
        document.querySelectorAll('.periodo-btn').forEach(b => {
            b.classList.remove('bg-brand-500','text-white','shadow');
            b.classList.add('text-slate-500','dark:text-slate-400');
        });
        btn.classList.add('bg-brand-500','text-white','shadow');
        btn.classList.remove('text-slate-500','dark:text-slate-400');
        const inputFecha = document.getElementById('input-fecha');
        if (val === 'fecha') {
            inputFecha.classList.remove('hidden'); inputFecha.classList.add('flex');
        } else {
            inputFecha.classList.add('hidden'); inputFecha.classList.remove('flex');
        }
    }

    // --- Dropdowns custom ---
    let canchaVal = 'todas', estadoVal = 'todos';

    function toggleDrop(tipo) {
        const menu = document.getElementById(`menu-${tipo}`);
        const otro = tipo === 'cancha' ? 'estado' : 'cancha';
        document.getElementById(`menu-${otro}`).classList.add('hidden');
        menu.classList.toggle('hidden');
    }
    function elegirCancha(val, label) {
        canchaVal = val;
        document.getElementById('label-cancha').textContent = label;
        document.getElementById('menu-cancha').classList.add('hidden');
        filtrarReservas();
    }
    function elegirEstado(val, label) {
        estadoVal = val;
        document.getElementById('label-estado').textContent = label;
        document.getElementById('menu-estado').classList.add('hidden');
        filtrarReservas();
    }
    document.addEventListener('click', e => {
        ['cancha','estado'].forEach(tipo => {
            if (!document.getElementById(`drop-${tipo}`)?.contains(e.target))
                document.getElementById(`menu-${tipo}`)?.classList.add('hidden');
        });
    });

    function filtrarReservas() {
        document.querySelectorAll('.reserva-row').forEach(row => {
            const ok = (canchaVal === 'todas' || canchaVal === row.dataset.cancha)
                    && (estadoVal === 'todos'  || estadoVal === row.dataset.estado);
            row.style.display = ok ? '' : 'none';
        });
    }
</script>
@endpush

@endsection
