@extends('layouts.admin')
@section('title', 'Comisiones | FutGo Admin')
@section('page-title', 'Comisiones')
@section('page-subtitle', 'Configuración global de tarifas y comisiones de la plataforma')

@section('content')

@php
$fases = [
    ['fase' => 1, 'nombre' => 'Penetración',   'comision' => 0,   'activa' => true,  'desc' => 'Captación de masa crítica de partners y usuarios. Sin costo.'],
    ['fase' => 2, 'nombre' => 'Fidelización',  'comision' => 0,   'activa' => false, 'desc' => 'Funciones premium opcionales. Analítica avanzada, WhatsApp marketing.'],
    ['fase' => 3, 'nombre' => 'Monetización',  'comision' => 5,   'activa' => false, 'desc' => 'Activación gradual de comisión transaccional (3–7%).'],
];
@endphp

{{-- ALERTA FASE ACTIVA --}}
<div class="bg-brand-500/10 border border-brand-500/20 rounded-2xl p-4 mb-6 flex items-start gap-3">
    <i class="ph-fill ph-info text-brand-500 text-xl shrink-0 mt-0.5"></i>
    <div>
        <p class="font-bold text-brand-700 dark:text-brand-300">Fase 1 activa — Comisión: 0%</p>
        <p class="text-sm text-brand-600 dark:text-brand-400 mt-0.5">
            El motor de comisiones está activo y configurado a cero. Activar una fase nueva es irreversible sin aprobación del directorio.
        </p>
    </div>
</div>

{{-- FASES --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
    @foreach($fases as $f)
    <div class="glass-card rounded-2xl p-5 {{ $f['activa'] ? 'ring-2 ring-brand-500' : '' }}">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Fase {{ $f['fase'] }}</span>
            @if($f['activa'])
            <span class="bg-brand-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full flex items-center gap-1">
                <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse inline-block"></span> Activa
            </span>
            @else
            <span class="bg-slate-100 dark:bg-white/10 text-slate-400 text-[10px] font-bold px-2.5 py-1 rounded-full">Inactiva</span>
            @endif
        </div>
        <h3 class="font-extrabold text-slate-900 dark:text-white text-xl mb-1">{{ $f['nombre'] }}</h3>
        <div class="text-3xl font-black {{ $f['activa'] ? 'text-brand-500' : 'text-slate-300 dark:text-slate-600' }} mb-3">
            {{ $f['comision'] }}%
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">{{ $f['desc'] }}</p>
        @if(!$f['activa'])
        <button class="mt-4 w-full py-2.5 rounded-xl border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-400 hover:border-admin-500 hover:text-admin-500 transition-colors">
            Activar fase
        </button>
        @endif
    </div>
    @endforeach
</div>

{{-- CONFIGURACIÓN DETALLADA --}}
<div class="glass-card rounded-2xl p-5">
    <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-5">
        <i class="ph-fill ph-sliders text-admin-500"></i> Configuración de comisiones
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @foreach([
            ['label' => 'Comisión transaccional', 'val' => '0',   'unit' => '%', 'desc' => 'Por cada reserva completada. Se activa en Fase 3.', 'disabled' => true],
            ['label' => 'Comisión por no-show',   'val' => '100', 'unit' => '%', 'desc' => '% del anticipo que retiene la plataforma al registrarse un no-show.', 'disabled' => false],
            ['label' => 'Fee pasarela de pago',   'val' => '3.5', 'unit' => '%', 'desc' => 'Cobrado por la pasarela de pagos (no modificable).', 'disabled' => true],
        ] as $cfg)
        <div>
            <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">{{ $cfg['label'] }}</label>
            <div class="flex items-center gap-2 border {{ $cfg['disabled'] ? 'border-slate-100 dark:border-white/5 bg-slate-50 dark:bg-white/3' : 'border-slate-200 dark:border-white/10 bg-white dark:bg-white/5 focus-within:border-admin-500' }} rounded-xl px-4 py-3 transition-all">
                <span class="font-bold text-slate-400 shrink-0">{{ $cfg['unit'] }}</span>
                <input type="number" value="{{ $cfg['val'] }}" {{ $cfg['disabled'] ? 'disabled' : '' }}
                       class="flex-1 bg-transparent text-xl font-extrabold outline-none {{ $cfg['disabled'] ? 'text-slate-400 cursor-not-allowed' : 'text-slate-900 dark:text-white' }}">
            </div>
            <p class="text-[10px] text-slate-400 mt-1">{{ $cfg['desc'] }}</p>
        </div>
        @endforeach
    </div>
    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-100 dark:border-white/5">
        <button class="px-6 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-500 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
            Descartar
        </button>
        <button class="px-6 py-2.5 rounded-xl bg-admin-500 hover:bg-admin-600 text-white font-bold text-sm transition-all hover:scale-105 shadow-lg shadow-admin-500/20 flex items-center gap-2">
            <i class="ph-bold ph-floppy-disk"></i> Guardar configuración
        </button>
    </div>
</div>

@endsection
