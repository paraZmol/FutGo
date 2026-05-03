@extends('layouts.admin')
@section('title', 'Disputas | FutGo Admin')
@section('page-title', 'Disputas')
@section('page-subtitle', 'Resolución de conflictos entre usuarios y partners')

@section('content')

@php
$disputas = [
    [
        'id'        => 'D001',
        'tipo'      => 'noshow',
        'estado'    => 'abierta',
        'cliente'   => 'Carlos Mamani',
        'partner'   => 'Complejo Deportivo El 10',
        'reserva'   => 'R004',
        'fecha_res' => 'Jue 25 Abr · 10:00',
        'monto'     => 100.00,
        'anticipo'  => 30.00,
        'motivo'    => 'El cliente reclama que sí se presentó a tiempo pero no encontró al staff para el check-in. El staff indica que el cliente llegó 25 minutos tarde.',
        'fecha'     => 'Hace 1 día',
        'prioridad' => 'alta',
    ],
    [
        'id'        => 'D002',
        'tipo'      => 'reembolso',
        'estado'    => 'abierta',
        'cliente'   => 'Sofía Ríos',
        'partner'   => 'Canchas La Losa',
        'reserva'   => 'R089',
        'fecha_res' => 'Dom 21 Abr · 16:00',
        'monto'     => 60.00,
        'anticipo'  => 30.00,
        'motivo'    => 'La cancha estaba en mal estado (pasto deteriorado). La usuaria tiene fotos como evidencia. El partner no reconoce el reclamo.',
        'fecha'     => 'Hace 3 días',
        'prioridad' => 'alta',
    ],
    [
        'id'        => 'D003',
        'tipo'      => 'pago',
        'estado'    => 'resuelta',
        'cliente'   => 'Mario Quispe',
        'partner'   => 'Arena Sports',
        'reserva'   => 'R201',
        'fecha_res' => 'Vie 19 Abr · 20:00',
        'monto'     => 80.00,
        'anticipo'  => 30.00,
        'motivo'    => 'Doble cobro detectado. Se procesó el reembolso del anticipo duplicado.',
        'fecha'     => 'Hace 1 sem',
        'prioridad' => 'baja',
    ],
];
@endphp

{{-- STATS --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    @foreach([
        ['label' => 'Abiertas',  'val' => 2, 'icon' => 'ph-warning',      'color' => 'text-red-500',   'bg' => 'bg-red-500/10'],
        ['label' => 'Resueltas este mes', 'val' => 8, 'icon' => 'ph-check-circle', 'color' => 'text-brand-500', 'bg' => 'bg-brand-500/10'],
        ['label' => 'Tiempo promedio', 'val' => '2.4d', 'icon' => 'ph-clock', 'color' => 'text-blue-500', 'bg' => 'bg-blue-500/10'],
    ] as $s)
    <div class="glass-card rounded-2xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl {{ $s['bg'] }} flex items-center justify-center shrink-0">
            <i class="ph-fill {{ $s['icon'] }} {{ $s['color'] }} text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $s['val'] }}</p>
            <p class="text-xs text-slate-400 font-semibold">{{ $s['label'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- FILTROS --}}
<div class="flex items-center gap-2 overflow-x-auto no-scrollbar mb-6 pb-1">
    @foreach(['Todas', 'Abiertas', 'Resueltas'] as $f)
    @php
        $filterVal = strtolower($f);
        if ($filterVal === 'todas') $filterVal = 'todos';
        if ($filterVal === 'resueltas') $filterVal = 'resuelta';
        if ($filterVal === 'abiertas') $filterVal = 'abierta';
    @endphp
    <button onclick="filtrarDisputas('{{ $filterVal }}', this)"
            class="filtro-btn shrink-0 px-4 py-2 rounded-xl text-sm font-semibold transition-all
        {{ $f === 'Todas'
            ? 'bg-admin-500 text-white shadow-md shadow-admin-500/20'
            : 'glass-card border border-slate-200 dark:border-white/10 text-slate-500 dark:text-slate-400 hover:border-admin-500 hover:text-admin-500' }}">
        {{ $f }}
    </button>
    @endforeach
</div>

{{-- DISPUTAS --}}
<div class="space-y-4">
    @foreach($disputas as $d)
    <div class="disputa-card glass-card rounded-2xl overflow-hidden transition-all duration-300
        {{ $d['estado'] === 'abierta' ? 'border-l-4 '.($d['prioridad'] === 'alta' ? 'border-l-red-500' : 'border-l-amber-500') : 'border-l-4 border-l-brand-500' }}"
        data-estado="{{ $d['estado'] }}">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-5 border-b border-slate-100 dark:border-white/5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0
                    {{ $d['tipo'] === 'noshow' ? 'bg-amber-500/10' : ($d['tipo'] === 'reembolso' ? 'bg-blue-500/10' : 'bg-purple-500/10') }}">
                    <i class="ph-fill
                        {{ $d['tipo'] === 'noshow' ? 'ph-user-minus text-amber-500' : ($d['tipo'] === 'reembolso' ? 'ph-arrow-counter-clockwise text-blue-500' : 'ph-credit-card text-purple-500') }}
                        text-xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-bold text-slate-900 dark:text-white">{{ $d['id'] }}</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border
                            {{ $d['tipo'] === 'noshow' ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20'
                            : ($d['tipo'] === 'reembolso' ? 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20'
                            : 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20') }}">
                            {{ $d['tipo'] === 'noshow' ? 'No-show' : ucfirst($d['tipo']) }}
                        </span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border
                            {{ $d['estado'] === 'abierta' ? 'bg-red-500/10 text-red-500 border-red-500/20' : 'bg-brand-500/10 text-brand-600 dark:text-brand-400 border-brand-500/20' }}">
                            {{ ucfirst($d['estado']) }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $d['fecha'] }} · Reserva {{ $d['reserva'] }} · {{ $d['fecha_res'] }}</p>
                </div>
            </div>
            <div class="text-right shrink-0">
                <p class="text-xl font-extrabold text-slate-900 dark:text-white">S/ {{ number_format($d['monto'],2) }}</p>
                <p class="text-[10px] text-slate-400">Anticipo: S/ {{ number_format($d['anticipo'],2) }}</p>
            </div>
        </div>

        {{-- Partes --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-5 border-b border-slate-100 dark:border-white/5">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-white/10 flex items-center justify-center text-xs font-bold text-slate-500 dark:text-slate-400 shrink-0">
                    {{ strtoupper(substr($d['cliente'], 0, 2)) }}
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Cliente</p>
                    <p class="text-sm font-semibold text-slate-800 dark:text-white">{{ $d['cliente'] }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-admin-500/10 flex items-center justify-center shrink-0">
                    <i class="ph-fill ph-buildings text-admin-500"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Partner</p>
                    <p class="text-sm font-semibold text-slate-800 dark:text-white">{{ $d['partner'] }}</p>
                </div>
            </div>
        </div>

        {{-- Motivo --}}
        <div class="p-5 border-b border-slate-100 dark:border-white/5">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Descripción del conflicto</p>
            <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">{{ $d['motivo'] }}</p>
        </div>

        {{-- Acciones --}}
        @if($d['estado'] === 'abierta')
        <div class="p-5 flex flex-col sm:flex-row flex-wrap gap-2">
            <button class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-sm font-bold transition-colors shadow-md shadow-brand-500/20 w-full sm:w-auto">
                <i class="ph-bold ph-arrow-counter-clockwise"></i> Reembolsar anticipo
            </button>
            <button class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-600 dark:text-amber-400 text-sm font-bold hover:bg-amber-500 hover:text-white transition-colors w-full sm:w-auto">
                <i class="ph-bold ph-lock-simple"></i> Retener anticipo
            </button>
            <button class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 text-slate-500 dark:text-slate-400 text-sm font-semibold hover:border-admin-500 hover:text-admin-500 transition-colors w-full sm:w-auto">
                <i class="ph-bold ph-chat-circle-text"></i> Contactar partes
            </button>
            <button class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 text-slate-500 dark:text-slate-400 text-sm font-semibold hover:border-slate-300 transition-colors w-full sm:w-auto sm:ml-auto">
                <i class="ph-bold ph-check-circle"></i> Marcar resuelta
            </button>
        </div>
        @else
        <div class="p-5">
            <span class="flex items-center gap-2 text-sm text-brand-600 dark:text-brand-400 font-semibold bg-brand-500/10 p-3 rounded-xl border border-brand-500/20">
                <i class="ph-fill ph-check-circle text-xl"></i> Conflicto resuelto — El anticipo ha sido gestionado exitosamente.
            </span>
        </div>
        @endif

    </div>
    @endforeach
</div>

@push('scripts')
<script>
    function filtrarDisputas(estado, btn) {
        // Actualizar botones
        document.querySelectorAll('.filtro-btn').forEach(b => {
            b.classList.remove('bg-admin-500', 'text-white', 'shadow-md', 'shadow-admin-500/20');
            b.classList.add('glass-card', 'border', 'border-slate-200', 'dark:border-white/10', 'text-slate-500', 'dark:text-slate-400');
            
            if (b === btn) {
                b.classList.add('bg-admin-500', 'text-white', 'shadow-md', 'shadow-admin-500/20');
                b.classList.remove('glass-card', 'border', 'border-slate-200', 'dark:border-white/10', 'text-slate-500', 'dark:text-slate-400');
            }
        });

        // Filtrar tarjetas
        const cards = document.querySelectorAll('.disputa-card');
        cards.forEach(card => {
            if (estado === 'todos' || card.dataset.estado === estado) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>
@endpush

@endsection
