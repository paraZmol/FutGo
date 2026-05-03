@extends('layouts.admin')
@section('title', 'Plataforma | FutGo Admin')
@section('page-title', 'Plataforma')
@section('page-subtitle', 'Configuración global del sistema')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    {{-- ESTADO DEL SISTEMA --}}
    <div class="glass-card rounded-2xl p-5">
        <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-4">
            <i class="ph-fill ph-pulse text-brand-500"></i> Estado del sistema
        </h2>
        <div class="space-y-3">
            @foreach([
                ['servicio' => 'API Principal',        'estado' => 'operativo', 'uptime' => '99.8%', 'latencia' => '42ms'],
                ['servicio' => 'Base de datos',        'estado' => 'operativo', 'uptime' => '99.9%', 'latencia' => '8ms'],
                ['servicio' => 'Pasarela de pago',     'estado' => 'operativo', 'uptime' => '99.5%', 'latencia' => '180ms'],
                ['servicio' => 'Notificaciones push',  'estado' => 'operativo', 'uptime' => '98.2%', 'latencia' => '95ms'],
                ['servicio' => 'Almacenamiento QR',    'estado' => 'operativo', 'uptime' => '100%',  'latencia' => '22ms'],
                ['servicio' => 'Workers (colas)',       'estado' => 'operativo', 'uptime' => '99.7%', 'latencia' => '–'],
            ] as $s)
            <div class="flex items-center gap-3 py-2.5 border-b border-slate-100 dark:border-white/5 last:border-0">
                <span class="relative flex h-2.5 w-2.5 shrink-0">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-500 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-brand-500"></span>
                </span>
                <span class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex-1">{{ $s['servicio'] }}</span>
                <span class="text-[10px] font-bold text-slate-400 hidden sm:block">{{ $s['latencia'] }}</span>
                <span class="text-[10px] font-bold text-brand-500">{{ $s['uptime'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- CONFIGURACIÓN GENERAL --}}
    <div class="glass-card rounded-2xl p-5">
        <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-4">
            <i class="ph-fill ph-gear text-admin-500"></i> Parámetros generales
        </h2>
        <div class="space-y-4">
            @foreach([
                ['label' => 'Nombre de la plataforma', 'val' => 'FutGo', 'tipo' => 'text'],
                ['label' => 'País principal',           'val' => 'Perú',  'tipo' => 'text'],
                ['label' => 'Moneda',                   'val' => 'PEN (S/)', 'tipo' => 'text'],
                ['label' => 'Tolerancia no-show (min)', 'val' => '15',    'tipo' => 'number'],
                ['label' => 'Ventana de slots (días)',  'val' => '30',    'tipo' => 'number'],
                ['label' => 'TTL lock de pago (seg)',   'val' => '600',   'tipo' => 'number'],
            ] as $cfg)
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1 block">{{ $cfg['label'] }}</label>
                <input type="{{ $cfg['tipo'] }}" value="{{ $cfg['val'] }}"
                       class="w-full border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm bg-white dark:bg-white/5 text-slate-900 dark:text-white outline-none focus:border-admin-500 focus:ring-2 focus:ring-admin-500/20 transition-all font-semibold">
            </div>
            @endforeach
        </div>
        <div class="flex justify-end gap-3 mt-5 pt-4 border-t border-slate-100 dark:border-white/5">
            <button class="px-5 py-2.5 rounded-xl bg-admin-500 hover:bg-admin-600 text-white font-bold text-sm transition-all hover:scale-105 shadow-lg shadow-admin-500/20 flex items-center gap-2">
                <i class="ph-bold ph-floppy-disk"></i> Guardar
            </button>
        </div>
    </div>

    {{-- MANTENIMIENTO --}}
    <div class="glass-card rounded-2xl p-5">
        <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-4">
            <i class="ph-fill ph-wrench text-amber-500"></i> Mantenimiento
        </h2>
        <div class="space-y-3">
            @foreach([
                ['label' => 'Limpiar caché del sistema',      'desc' => 'Limpia Redis y caché de vistas',           'icon' => 'ph-trash',       'color' => 'text-slate-500',  'bg' => 'bg-slate-100 dark:bg-white/5'],
                ['label' => 'Re-generar slots (30 días)',      'desc' => 'Corre el job slots:roll-window ahora',     'icon' => 'ph-calendar-blank','color' => 'text-blue-500', 'bg' => 'bg-blue-500/10'],
                ['label' => 'Reconciliar pagos pendientes',    'desc' => 'Verifica pagos con la pasarela',           'icon' => 'ph-arrows-clockwise','color' => 'text-purple-500','bg' => 'bg-purple-500/10'],
                ['label' => 'Modo mantenimiento',             'desc' => 'Pone el sitio offline para usuarios',      'icon' => 'ph-warning',     'color' => 'text-red-500',    'bg' => 'bg-red-500/10'],
            ] as $m)
            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl {{ $m['bg'] }} flex items-center justify-center shrink-0">
                        <i class="ph-fill {{ $m['icon'] }} {{ $m['color'] }}"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800 dark:text-white">{{ $m['label'] }}</p>
                        <p class="text-[10px] text-slate-400">{{ $m['desc'] }}</p>
                    </div>
                </div>
                <button class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-white/10 text-xs font-bold text-slate-500 hover:border-admin-500 hover:text-admin-500 transition-colors shrink-0">
                    Ejecutar
                </button>
            </div>
            @endforeach
        </div>
    </div>

    {{-- NOTIFICACIONES --}}
    <div class="glass-card rounded-2xl p-5">
        <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-4">
            <i class="ph-fill ph-bell text-admin-500"></i> Notificaciones globales
        </h2>
        <div class="space-y-4">
            @foreach([
                ['label' => 'Emails de confirmación',      'activo' => true],
                ['label' => 'Recordatorio 1h antes',       'activo' => true],
                ['label' => 'Notificación no-show',        'activo' => true],
                ['label' => 'WhatsApp al partner',         'activo' => false],
                ['label' => 'Push notifications',          'activo' => true],
                ['label' => 'Email semanal al partner',    'activo' => false],
            ] as $n)
            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $n['label'] }}</span>
                <button onclick="this.classList.toggle('bg-brand-500'); this.classList.toggle('bg-slate-200'); this.classList.toggle('dark:bg-white/10');"
                        class="relative w-11 h-6 rounded-full transition-colors {{ $n['activo'] ? 'bg-brand-500' : 'bg-slate-200 dark:bg-white/10' }} focus:outline-none">
                    <span class="absolute top-0.5 {{ $n['activo'] ? 'left-[22px]' : 'left-0.5' }} w-5 h-5 bg-white rounded-full shadow transition-all"></span>
                </button>
            </div>
            @endforeach
        </div>
    </div>

</div>

@endsection
