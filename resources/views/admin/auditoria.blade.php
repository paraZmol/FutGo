@extends('layouts.admin')
@section('title', 'Auditoría | FutGo Admin')
@section('page-title', 'Auditoría')
@section('page-subtitle', 'Bitácora inmutable de acciones críticas en la plataforma')

@section('content')

@php
$logs = [
    ['id' => 'LOG-001', 'accion' => 'PARTNER_APROBADO',    'actor' => 'SuperAdmin',      'objetivo' => 'Arena Sports (ID: 12)',       'ip' => '190.42.11.5',  'fecha' => 'Hoy 14:32',    'nivel' => 'info'],
    ['id' => 'LOG-002', 'accion' => 'DISPUTA_RESUELTA',    'actor' => 'SuperAdmin',      'objetivo' => 'Disputa D001 — Reembolso',    'ip' => '190.42.11.5',  'fecha' => 'Hoy 13:10',    'nivel' => 'info'],
    ['id' => 'LOG-003', 'accion' => 'COMISION_MODIFICADA', 'actor' => 'SuperAdmin',      'objetivo' => 'Fee anticipo: 25→30',         'ip' => '190.42.11.5',  'fecha' => 'Ayer 18:45',   'nivel' => 'warning'],
    ['id' => 'LOG-004', 'accion' => 'USUARIO_SUSPENDIDO',  'actor' => 'SuperAdmin',      'objetivo' => 'Diego Vargas (ID: 6)',        'ip' => '190.42.11.5',  'fecha' => 'Ayer 11:20',   'nivel' => 'warning'],
    ['id' => 'LOG-005', 'accion' => 'LOGIN_ADMIN',         'actor' => 'SuperAdmin',      'objetivo' => 'Inicio de sesión exitoso',    'ip' => '190.42.11.5',  'fecha' => 'Ayer 09:00',   'nivel' => 'info'],
    ['id' => 'LOG-006', 'accion' => 'LOGIN_FALLIDO',       'actor' => 'Desconocido',     'objetivo' => '3 intentos fallidos',         'ip' => '201.55.77.12', 'fecha' => 'Hace 3d 22:14','nivel' => 'error'],
    ['id' => 'LOG-007', 'accion' => 'PARTNER_RECHAZADO',   'actor' => 'SuperAdmin',      'objetivo' => 'Canchas Sin Nombre (ID: 15)', 'ip' => '190.42.11.5',  'fecha' => 'Hace 4d 16:00','nivel' => 'info'],
    ['id' => 'LOG-008', 'accion' => 'BOOKING_REVERTIDO',   'actor' => 'SuperAdmin',      'objetivo' => 'Reserva R089 — Reembolso',    'ip' => '190.42.11.5',  'fecha' => 'Hace 5d 10:30','nivel' => 'warning'],
];
@endphp

{{-- Filtros --}}
<div class="flex flex-wrap gap-3 mb-5">
    <div class="flex items-center gap-1 glass-card rounded-xl p-1 border border-slate-200 dark:border-white/10">
        @foreach(['Todos' => 'all', 'Info' => 'info', 'Alertas' => 'warning', 'Errores' => 'error'] as $label => $val)
        <button onclick="filtrarLogs('{{ $val }}', this)"
                class="log-filter-btn px-3 py-1.5 rounded-lg text-sm font-semibold transition-all
            {{ $val === 'all' ? 'bg-admin-500 text-white shadow' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>
    <div class="flex items-center gap-2 glass-card border border-slate-200 dark:border-white/10 rounded-xl px-3 py-2 focus-within:border-admin-500 transition-all">
        <i class="ph-bold ph-magnifying-glass text-slate-400"></i>
        <input type="text" placeholder="Buscar en logs..."
               class="text-sm bg-transparent outline-none text-slate-800 dark:text-white placeholder-slate-400 w-40">
    </div>
    <button class="ml-auto flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-500 dark:text-slate-400 hover:border-admin-500 hover:text-admin-500 transition-colors">
        <i class="ph-bold ph-export"></i> Exportar
    </button>
</div>

{{-- Tabla de logs --}}
<div class="glass-card rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm font-mono">
            <thead>
                <tr class="bg-slate-50 dark:bg-white/3 border-b border-slate-100 dark:border-white/5">
                    <th class="text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider px-5 py-3 font-sans">ID</th>
                    <th class="text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 py-3 font-sans">Acción</th>
                    <th class="text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 py-3 font-sans hidden md:table-cell">Actor</th>
                    <th class="text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 py-3 font-sans hidden lg:table-cell">Detalle</th>
                    <th class="text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 py-3 font-sans hidden xl:table-cell">IP</th>
                    <th class="text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider px-5 py-3 font-sans">Fecha</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-white/5">
                @foreach($logs as $log)
                <tr class="log-row hover:bg-slate-50 dark:hover:bg-white/3 transition-colors
                    {{ $log['nivel'] === 'error' ? 'bg-red-50/50 dark:bg-red-500/5' : ($log['nivel'] === 'warning' ? 'bg-amber-50/30 dark:bg-amber-500/5' : '') }}"
                    data-nivel="{{ $log['nivel'] }}">
                    <td class="px-5 py-3 text-[10px] text-slate-400">{{ $log['id'] }}</td>
                    <td class="px-3 py-3">
                        <span class="text-xs font-bold px-2 py-1 rounded-lg
                            {{ $log['nivel'] === 'error'   ? 'bg-red-100 dark:bg-red-500/10 text-red-600 dark:text-red-400'
                            : ($log['nivel'] === 'warning' ? 'bg-amber-100 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400'
                            : 'bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-400') }}">
                            {{ $log['accion'] }}
                        </span>
                    </td>
                    <td class="px-3 py-3 text-slate-600 dark:text-slate-400 text-xs hidden md:table-cell font-sans">{{ $log['actor'] }}</td>
                    <td class="px-3 py-3 text-slate-500 dark:text-slate-400 text-xs hidden lg:table-cell font-sans">{{ $log['objetivo'] }}</td>
                    <td class="px-3 py-3 text-slate-400 text-[10px] hidden xl:table-cell">{{ $log['ip'] }}</td>
                    <td class="px-5 py-3 text-slate-400 text-xs font-sans">{{ $log['fecha'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3 border-t border-slate-100 dark:border-white/5 flex items-center justify-between text-xs text-slate-400">
        <span>Mostrando {{ count($logs) }} registros · Los logs son inmutables y no pueden eliminarse</span>
        <span class="flex items-center gap-1"><i class="ph-fill ph-shield-check text-brand-500"></i> Auditoría certificada</span>
    </div>
</div>

@push('scripts')
<script>
    function filtrarLogs(nivel, btn) {
        // Actualizar botones
        document.querySelectorAll('.log-filter-btn').forEach(b => {
            b.classList.remove('bg-admin-500', 'text-white', 'shadow');
            b.classList.add('text-slate-500', 'dark:text-slate-400');
            
            if (b === btn) {
                b.classList.add('bg-admin-500', 'text-white', 'shadow');
                b.classList.remove('text-slate-500', 'dark:text-slate-400');
            }
        });

        // Filtrar filas
        const rows = document.querySelectorAll('.log-row');
        rows.forEach(row => {
            if (nivel === 'all' || row.dataset.nivel === nivel) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>
@endpush

@endsection
