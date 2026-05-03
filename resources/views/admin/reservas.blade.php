@extends('layouts.admin')
@section('title', 'Reservas | FutGo Admin')
@section('page-title', 'Reservas')
@section('page-subtitle', 'Todas las reservas de la plataforma')

@section('content')

@php
$reservas = [
    ['id' => 'R001', 'fecha' => 'Hoy 20:00',    'cliente' => 'Pedro Huanca',   'cancha' => 'Complejo El 10',    'ciudad' => 'Cusco',    'monto' => 90.00,  'anticipo' => 30.00, 'canal' => 'app',   'estado' => 'confirmada'],
    ['id' => 'R002', 'fecha' => 'Hoy 19:00',    'cliente' => 'Roberto Silva',   'cancha' => 'Complejo El 10',    'ciudad' => 'Cusco',    'monto' => 90.00,  'anticipo' => 30.00, 'canal' => 'app',   'estado' => 'confirmada'],
    ['id' => 'R003', 'fecha' => 'Hoy 18:00',    'cliente' => 'Ana Gutierrez',   'cancha' => 'Canchas La Losa',   'ciudad' => 'Cusco',    'monto' => 60.00,  'anticipo' => 30.00, 'canal' => 'app',   'estado' => 'confirmada'],
    ['id' => 'R004', 'fecha' => 'Hoy 09:00',    'cliente' => 'Presencial',       'cancha' => 'Complejo El 10',    'ciudad' => 'Cusco',    'monto' => 80.00,  'anticipo' => 80.00, 'canal' => 'staff', 'estado' => 'completada'],
    ['id' => 'R005', 'fecha' => 'Ayer 21:00',   'cliente' => 'Jorge Flores',    'cancha' => 'Arena Sports',      'ciudad' => 'Lima',     'monto' => 120.00, 'anticipo' => 40.00, 'canal' => 'app',   'estado' => 'completada'],
    ['id' => 'R006', 'fecha' => 'Ayer 20:00',   'cliente' => 'Miguel Castro',   'cancha' => 'Arena Sports',      'ciudad' => 'Lima',     'monto' => 90.00,  'anticipo' => 30.00, 'canal' => 'app',   'estado' => 'completada'],
    ['id' => 'R007', 'fecha' => 'Ayer 10:00',   'cliente' => 'Carlos Mamani',   'cancha' => 'SportCenter Norte', 'ciudad' => 'Cusco',    'monto' => 100.00, 'anticipo' => 30.00, 'canal' => 'app',   'estado' => 'noshow'],
    ['id' => 'R008', 'fecha' => 'Hace 3d 19:00','cliente' => 'Sofia Ríos',      'cancha' => 'Canchas La Losa',   'ciudad' => 'Cusco',    'monto' => 60.00,  'anticipo' => 30.00, 'canal' => 'app',   'estado' => 'completada'],
];

$stats = [
    ['label' => 'Total del mes',  'val' => '3,847',  'icon' => 'ph-calendar-check', 'color' => 'text-brand-500',  'bg' => 'bg-brand-500/10'],
    ['label' => 'Completadas',    'val' => '3,612',  'icon' => 'ph-check-circle',   'color' => 'text-slate-400',  'bg' => 'bg-slate-100 dark:bg-white/5'],
    ['label' => 'No-shows',       'val' => '198',    'icon' => 'ph-user-minus',     'color' => 'text-amber-500',  'bg' => 'bg-amber-500/10'],
    ['label' => 'Volumen total',  'val' => 'S/ 287k','icon' => 'ph-money',          'color' => 'text-purple-500', 'bg' => 'bg-purple-500/10'],
];
@endphp

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach($stats as $s)
    <div class="glass-card rounded-2xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl {{ $s['bg'] }} flex items-center justify-center shrink-0">
            <i class="ph-fill {{ $s['icon'] }} {{ $s['color'] }} text-xl"></i>
        </div>
        <div>
            <p class="text-xl font-extrabold text-slate-900 dark:text-white">{{ $s['val'] }}</p>
            <p class="text-xs text-slate-400 font-semibold">{{ $s['label'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- Filtros --}}
<div class="flex flex-wrap gap-3 mb-4">
    <div class="flex items-center gap-1 glass-card rounded-xl p-1 border border-slate-200 dark:border-white/10">
        @foreach(['Hoy' => 'hoy', 'Semana' => 'semana', 'Mes' => 'mes'] as $label => $val)
        <button onclick="seleccionarPeriodo('{{ $val }}', this)"
                class="periodo-btn px-3 py-1.5 rounded-lg text-sm font-semibold transition-all
            {{ $val === 'hoy' ? 'bg-admin-500 text-white shadow' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>
    <div class="relative">
        <button onclick="document.getElementById('filtro-estados-dropdown').classList.toggle('hidden')" class="flex items-center justify-between gap-2 glass-card border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:border-admin-500 transition-all focus:outline-none w-48">
            <span class="flex items-center gap-2"><i class="ph-fill ph-funnel text-admin-500"></i> <span id="label-estado">Todos</span></span>
            <i class="ph-bold ph-caret-down text-xs text-slate-400"></i>
        </button>
        <div id="filtro-estados-dropdown" class="hidden absolute left-0 mt-2 w-full bg-white dark:bg-dark-800 border border-slate-200 dark:border-white/10 shadow-xl rounded-2xl z-50 overflow-hidden transform origin-top transition-all">
            @foreach(['todos' => 'Todos', 'confirmada' => 'Confirmadas', 'completada' => 'Completadas', 'noshow' => 'No-shows'] as $val => $label)
            <a href="#" onclick="event.preventDefault(); seleccionarEstado('{{ $val }}', '{{ $label }}')" class="block px-4 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                {{ $label }}
            </a>
            @endforeach
        </div>
    </div>
    <div class="flex items-center gap-2 glass-card border border-slate-200 dark:border-white/10 rounded-xl px-3 py-2 focus-within:border-admin-500 transition-all">
        <i class="ph-bold ph-magnifying-glass text-slate-400"></i>
        <input type="text" placeholder="Buscar reserva, cliente..."
               class="text-sm bg-transparent outline-none text-slate-800 dark:text-white placeholder-slate-400 w-44">
    </div>
    <button class="ml-auto flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-500 hover:border-admin-500 hover:text-admin-500 transition-colors">
        <i class="ph-bold ph-export"></i> Exportar
    </button>
</div>

{{-- Tabla --}}
<div class="glass-card rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 dark:bg-white/3 border-b border-slate-100 dark:border-white/5">
                    <th class="text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider px-5 py-3">ID</th>
                    <th class="text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 py-3">Cliente</th>
                    <th class="text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 py-3 hidden md:table-cell">Cancha</th>
                    <th class="text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 py-3 hidden lg:table-cell">Ciudad</th>
                    <th class="text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 py-3 hidden md:table-cell">Canal</th>
                    <th class="text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 py-3">Estado</th>
                    <th class="text-right text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 py-3">Monto</th>
                    <th class="text-right text-[10px] font-bold text-slate-400 uppercase tracking-wider px-5 py-3 hidden sm:table-cell">Fecha</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-white/5">
                @foreach($reservas as $r)
                <tr class="reserva-row hover:bg-slate-50 dark:hover:bg-white/3 transition-colors
                    {{ $r['estado'] === 'noshow' ? 'bg-amber-50/40 dark:bg-amber-500/5' : '' }}"
                    data-periodo="{{ str_contains($r['fecha'],'Hoy') ? 'hoy' : (str_contains($r['fecha'],'Ayer') || str_contains($r['fecha'],'Hace 3d') ? 'semana' : 'mes') }}"
                    data-estado="{{ strtolower($r['estado']) }}">
                    <td class="px-5 py-3.5 font-mono text-[10px] text-slate-400">{{ $r['id'] }}</td>
                    <td class="px-3 py-3.5">
                        <span class="font-semibold text-slate-800 dark:text-white">{{ $r['cliente'] }}</span>
                    </td>
                    <td class="px-3 py-3.5 text-slate-600 dark:text-slate-400 hidden md:table-cell">{{ $r['cancha'] }}</td>
                    <td class="px-3 py-3.5 text-slate-500 dark:text-slate-400 hidden lg:table-cell">{{ $r['ciudad'] }}</td>
                    <td class="px-3 py-3.5 hidden md:table-cell">
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full
                            {{ $r['canal'] === 'app' ? 'bg-blue-500/10 text-blue-500' : 'bg-purple-500/10 text-purple-500' }}">
                            {{ ucfirst($r['canal']) }}
                        </span>
                    </td>
                    <td class="px-3 py-3.5">
                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full border
                            @if($r['estado'] === 'confirmada')  bg-brand-500/10 text-brand-600 dark:text-brand-400 border-brand-500/20
                            @elseif($r['estado'] === 'completada') bg-slate-100 dark:bg-white/10 text-slate-500 border-slate-200 dark:border-white/10
                            @else bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20
                            @endif">
                            {{ ucfirst($r['estado']) }}
                        </span>
                    </td>
                    <td class="px-3 py-3.5 text-right">
                        <span class="font-bold text-slate-900 dark:text-white">S/ {{ number_format($r['monto'],2) }}</span>
                        <span class="block text-[10px] text-brand-500">S/ {{ number_format($r['anticipo'],2) }} anticipo</span>
                    </td>
                    <td class="px-5 py-3.5 text-xs text-slate-400 text-right hidden sm:table-cell">{{ $r['fecha'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3 border-t border-slate-100 dark:border-white/5 text-xs text-slate-400">
        Mostrando {{ count($reservas) }} reservas · Página 1 de 481
    </div>
</div>

@push('scripts')
<script>
    let filtroPeriodoActual = 'hoy';
    let filtroEstadoActual = 'todos';

    function seleccionarPeriodo(periodo, btn) {
        filtroPeriodoActual = periodo;
        
        document.querySelectorAll('.periodo-btn').forEach(b => {
            b.classList.remove('bg-admin-500', 'text-white', 'shadow');
            b.classList.add('text-slate-500', 'dark:text-slate-400', 'hover:text-slate-900', 'dark:hover:text-white');
            
            if (b === btn) {
                b.classList.add('bg-admin-500', 'text-white', 'shadow');
                b.classList.remove('text-slate-500', 'dark:text-slate-400', 'hover:text-slate-900', 'dark:hover:text-white');
            }
        });
        
        aplicarFiltros();
    }

    function seleccionarEstado(estado, label) {
        filtroEstadoActual = estado;
        document.getElementById('label-estado').innerText = label;
        document.getElementById('filtro-estados-dropdown').classList.add('hidden');
        aplicarFiltros();
    }

    function aplicarFiltros() {
        const rows = document.querySelectorAll('.reserva-row');
        rows.forEach(row => {
            const p = row.dataset.periodo;
            const e = row.dataset.estado;
            
            // "mes" muestra todo. "semana" muestra semana y hoy. "hoy" muestra solo hoy.
            let showPeriodo = (filtroPeriodoActual === 'mes') || 
                              (filtroPeriodoActual === 'semana' && (p === 'hoy' || p === 'semana')) || 
                              (filtroPeriodoActual === 'hoy' && p === 'hoy');
                              
            let showEstado = (filtroEstadoActual === 'todos') || (filtroEstadoActual === e);
            
            if (showPeriodo && showEstado) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Cerrar dropdown estados al hacer clic afuera
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('filtro-estados-dropdown');
        if (dropdown && !dropdown.classList.contains('hidden')) {
            const button = dropdown.previousElementSibling;
            if (!dropdown.contains(event.target) && !button.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        }
    });
</script>
@endpush

@endsection
