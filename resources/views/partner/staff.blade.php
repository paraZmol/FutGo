@extends('layouts.partner')
@section('title', 'Mi Staff | FutGo Partner')
@section('page-title', 'Mi Staff')
@section('page-subtitle', 'Complejo Deportivo El 10 · Gestión de empleados')

@section('content')

@php
$staff = [
    [
        'id'         => 1,
        'nombre'     => 'Pedro Mamani',
        'email'      => 'pedro@complejo.com',
        'telefono'   => '+51 987 111 222',
        'turno'      => 'Mañana (07:00–15:00)',
        'estado'     => 'activo',
        'en_turno'   => true,
        'inicio_turno' => '07:00',
        'checkins'   => 6,
        'presenciales'    => 2,
        'desde'      => 'Ene 2024',
    ],
    [
        'id'         => 2,
        'nombre'     => 'Rosa Quispe',
        'email'      => 'rosa@complejo.com',
        'telefono'   => '+51 987 333 444',
        'turno'      => 'Tarde (15:00–23:00)',
        'estado'     => 'activo',
        'en_turno'   => false,
        'inicio_turno' => null,
        'checkins'   => 0,
        'presenciales'    => 0,
        'desde'      => 'Mar 2024',
    ],
    [
        'id'         => 3,
        'nombre'     => 'Juan Carlos Flores',
        'email'      => 'jcarlos@complejo.com',
        'telefono'   => '+51 987 555 666',
        'turno'      => 'Fin de semana',
        'estado'     => 'activo',
        'en_turno'   => false,
        'inicio_turno' => null,
        'checkins'   => 0,
        'presenciales'    => 0,
        'desde'      => 'Abr 2024',
    ],
    [
        'id'         => 4,
        'nombre'     => 'Maria Condori',
        'email'      => 'maria@complejo.com',
        'telefono'   => '+51 987 777 888',
        'turno'      => 'Mañana (07:00–15:00)',
        'estado'     => 'inactivo',
        'en_turno'   => false,
        'inicio_turno' => null,
        'checkins'   => 0,
        'presenciales'    => 0,
        'desde'      => 'Feb 2024',
    ],
];

$turnos_hoy = [
    ['staff' => 'Pedro Mamani',     'inicio' => '07:00', 'fin' => '15:00', 'estado' => 'en_curso',   'checkins' => 6, 'presenciales' => 2, 'efectivo' => 'S/ 400.00'],
    ['staff' => 'Rosa Quispe',      'inicio' => '15:00', 'fin' => '23:00', 'estado' => 'pendiente',  'checkins' => 0, 'presenciales' => 0, 'efectivo' => '–'],
];
@endphp

{{-- HEADER --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 w-full sm:w-auto">
        @foreach(['Todos', 'Activos', 'Inactivos'] as $f)
        @php
            $filterValue = strtolower($f);
            if ($filterValue === 'activos') $filterValue = 'activo';
            if ($filterValue === 'inactivos') $filterValue = 'inactivo';
        @endphp
        <button onclick="filtrarStaff('{{ $filterValue }}', this)"
                class="filtro-btn shrink-0 px-4 py-2 rounded-xl text-sm font-semibold transition-all
            {{ $f === 'Todos'
                ? 'bg-brand-500 text-white shadow-md shadow-brand-500/20 border border-transparent'
                : 'glass-card border border-slate-200 dark:border-white/10 text-slate-500 dark:text-slate-400 hover:border-brand-500 hover:text-brand-500' }}">
            {{ $f }}
        </button>
        @endforeach
    </div>
    <button onclick="document.getElementById('modal-staff').classList.remove('hidden')"
            class="bg-brand-500 hover:bg-brand-600 text-white font-bold px-5 py-2.5 rounded-xl flex items-center gap-2 text-sm transition-all hover:scale-105 shadow-lg shadow-brand-500/20 shrink-0">
        <i class="ph-bold ph-plus"></i> Agregar Staff
    </button>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ============================================================
         LISTA DE STAFF
    ============================================================ --}}
    <div class="lg:col-span-2 space-y-4">

        @foreach($staff as $s)
        <div class="staff-card glass-card rounded-2xl p-5 flex flex-col sm:flex-row gap-4
            {{ $s['estado'] === 'inactivo' ? 'opacity-60' : '' }}"
            data-estado="{{ $s['estado'] }}">

            {{-- Avatar + estado --}}
            <div class="relative shrink-0">
                <div class="w-14 h-14 rounded-2xl overflow-hidden ring-2
                    {{ $s['en_turno'] ? 'ring-brand-500' : 'ring-slate-200 dark:ring-white/10' }}">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($s['nombre']) }}&background=22c55e&color=fff&size=100"
                         class="w-full h-full object-cover" alt="{{ $s['nombre'] }}">
                </div>
                @if($s['en_turno'])
                <div class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-brand-500 border-2 border-white dark:border-dark-900">
                    <span class="absolute inset-0 rounded-full bg-brand-500 animate-ping opacity-75"></span>
                </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <h3 class="font-bold text-slate-900 dark:text-white">{{ $s['nombre'] }}</h3>
                    @if($s['en_turno'])
                    <span class="bg-brand-500/10 text-brand-600 dark:text-brand-400 text-[10px] font-bold px-2 py-0.5 rounded-full border border-brand-500/20 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 bg-brand-500 rounded-full animate-pulse inline-block"></span>
                        En turno desde {{ $s['inicio_turno'] }}
                    </span>
                    @elseif($s['estado'] === 'inactivo')
                    <span class="bg-slate-100 dark:bg-white/10 text-slate-500 dark:text-slate-400 text-[10px] font-bold px-2 py-0.5 rounded-full">
                        Inactivo
                    </span>
                    @endif
                </div>

                <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500 dark:text-slate-400 mb-3">
                    <span class="flex items-center gap-1">
                        <i class="ph-fill ph-envelope text-slate-400"></i> {{ $s['email'] }}
                    </span>
                    <span class="flex items-center gap-1">
                        <i class="ph-fill ph-phone text-slate-400"></i> {{ $s['telefono'] }}
                    </span>
                    <span class="flex items-center gap-1">
                        <i class="ph-fill ph-clock text-slate-400"></i> {{ $s['turno'] }}
                    </span>
                    <span class="flex items-center gap-1">
                        <i class="ph-fill ph-calendar-blank text-slate-400"></i> Desde {{ $s['desde'] }}
                    </span>
                </div>

                {{-- Stats si está en turno --}}
                @if($s['en_turno'])
                <div class="flex gap-4">
                    <div class="flex items-center gap-1.5 bg-brand-500/10 rounded-lg px-3 py-1.5">
                        <i class="ph-fill ph-check-circle text-brand-500 text-sm"></i>
                        <span class="text-xs font-bold text-brand-600 dark:text-brand-400">{{ $s['checkins'] }} check-ins</span>
                    </div>
                    <div class="flex items-center gap-1.5 bg-blue-500/10 rounded-lg px-3 py-1.5">
                        <i class="ph-fill ph-user-plus text-blue-500 text-sm"></i>
                        <span class="text-xs font-bold text-blue-600 dark:text-blue-400">{{ $s['presenciales'] }} presenciales</span>
                    </div>
                </div>
                @endif
            </div>

            {{-- Acciones --}}
            <div class="flex sm:flex-col gap-2 shrink-0 justify-end">
                <button class="flex items-center gap-1.5 px-3 py-2 rounded-xl border border-slate-200 dark:border-white/10 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:border-brand-500 hover:text-brand-500 transition-colors">
                    <i class="ph-bold ph-pencil"></i>
                    <span class="hidden sm:inline">Editar</span>
                </button>
                @if($s['estado'] === 'activo')
                <button class="flex items-center gap-1.5 px-3 py-2 rounded-xl border border-red-200 dark:border-red-500/20 text-xs font-semibold text-red-500 dark:text-red-400 hover:bg-red-500 hover:text-white hover:border-red-500 transition-colors">
                    <i class="ph-bold ph-user-minus"></i>
                    <span class="hidden sm:inline">Desactivar</span>
                </button>
                @else
                <button class="flex items-center gap-1.5 px-3 py-2 rounded-xl border border-brand-200 dark:border-brand-500/20 text-xs font-semibold text-brand-500 hover:bg-brand-500 hover:text-white hover:border-brand-500 transition-colors">
                    <i class="ph-bold ph-user-check"></i>
                    <span class="hidden sm:inline">Activar</span>
                </button>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- ============================================================
         SIDEBAR: turnos de hoy + info
    ============================================================ --}}
    <div class="space-y-5">

        {{-- Turnos de hoy --}}
        <div class="glass-card rounded-2xl p-5">
            <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-4">
                <i class="ph-fill ph-clock text-brand-500"></i> Turnos de hoy
            </h3>
            <div class="space-y-3">
                @foreach($turnos_hoy as $t)
                <div class="p-3 rounded-xl {{ $t['estado'] === 'en_curso' ? 'bg-brand-500/10 border border-brand-500/20' : 'bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5' }}">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-bold {{ $t['estado'] === 'en_curso' ? 'text-brand-600 dark:text-brand-400' : 'text-slate-700 dark:text-slate-300' }}">
                            {{ $t['staff'] }}
                        </p>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full
                            {{ $t['estado'] === 'en_curso'
                                ? 'bg-brand-500 text-white'
                                : 'bg-slate-200 dark:bg-white/10 text-slate-500 dark:text-slate-400' }}">
                            {{ $t['estado'] === 'en_curso' ? 'En curso' : 'Pendiente' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span class="flex items-center gap-1">
                            <i class="ph-fill ph-clock"></i>
                            {{ $t['inicio'] }} – {{ $t['fin'] }}
                        </span>
                        @if($t['estado'] === 'en_curso')
                        <span class="font-bold text-brand-500">{{ $t['efectivo'] }}</span>
                        @endif
                    </div>
                    @if($t['estado'] === 'en_curso')
                    <div class="flex gap-2 mt-2 pt-2 border-t border-brand-500/20 text-[10px] font-bold text-brand-600 dark:text-brand-400">
                        <span>{{ $t['checkins'] }} check-ins</span>
                        <span>·</span>
                        <span>{{ $t['presenciales'] }} presenciales</span>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        {{-- Accesos rápidos --}}
        <div class="glass-card rounded-2xl p-5">
            <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-4">
                <i class="ph-fill ph-link text-brand-500"></i> Acceso Staff
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">
                Compartí este link con tu staff para que accedan a la PWA operativa.
            </p>
            <div class="flex items-center gap-2 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-3 py-2.5">
                <span class="text-xs text-slate-500 dark:text-slate-400 truncate flex-1">futgo.app/staff</span>
                <button onclick="copiarLink()" class="shrink-0 text-brand-500 hover:text-brand-600 transition-colors">
                    <i class="ph-bold ph-copy" id="copy-icon"></i>
                </button>
            </div>
            <a href="/staff" target="_blank"
               class="mt-3 w-full flex items-center justify-center gap-2 py-2.5 rounded-xl bg-brand-500/10 border border-brand-500/20 text-brand-600 dark:text-brand-400 text-sm font-bold hover:bg-brand-500 hover:text-white transition-colors">
                <i class="ph-bold ph-device-mobile"></i> Abrir PWA Staff
            </a>
        </div>

        {{-- Historial de turnos --}}
        <div class="glass-card rounded-2xl p-5">
            <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-4">
                <i class="ph-fill ph-clock-counter-clockwise text-brand-500"></i> Últimos turnos
            </h3>
            <div class="space-y-2">
                @foreach([
                    ['staff' => 'Rosa Q.',   'fecha' => 'Ayer',      'turno' => '15:00–23:00', 'efectivo' => 'S/ 560.00'],
                    ['staff' => 'Pedro M.',  'fecha' => 'Ayer',      'turno' => '07:00–15:00', 'efectivo' => 'S/ 320.00'],
                    ['staff' => 'J. Carlos', 'fecha' => 'Sáb 27',   'turno' => '09:00–21:00', 'efectivo' => 'S/ 840.00'],
                ] as $h)
                <div class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-white/5 last:border-0">
                    <div>
                        <p class="text-sm font-semibold text-slate-800 dark:text-white">{{ $h['staff'] }}</p>
                        <p class="text-[10px] text-slate-400">{{ $h['fecha'] }} · {{ $h['turno'] }}</p>
                    </div>
                    <span class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ $h['efectivo'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

{{-- MODAL NUEVO STAFF --}}
<div id="modal-staff" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
         onclick="document.getElementById('modal-staff').classList.add('hidden')"></div>
    <div class="relative glass-card rounded-3xl p-6 w-full max-w-md shadow-2xl">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">Nuevo Staff</h2>
                <p class="text-xs text-slate-400 mt-0.5">Le llegará un email con sus credenciales</p>
            </div>
            <button onclick="document.getElementById('modal-staff').classList.add('hidden')"
                    class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-white/10 flex items-center justify-center text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors">
                <i class="ph-bold ph-x"></i>
            </button>
        </div>
        <form class="space-y-4">
            <div>
                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Nombre completo</label>
                <div class="flex items-center gap-3 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 bg-white dark:bg-white/5 focus-within:border-brand-500 focus-within:ring-2 focus-within:ring-brand-500/20 transition-all">
                    <i class="ph-fill ph-user text-slate-400 shrink-0"></i>
                    <input type="text" placeholder="Ej: Carlos Mamani"
                           class="flex-1 min-w-0 w-full bg-transparent text-sm outline-none text-slate-900 dark:text-white placeholder-slate-400">
                </div>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Email</label>
                <div class="flex items-center gap-3 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 bg-white dark:bg-white/5 focus-within:border-brand-500 focus-within:ring-2 focus-within:ring-brand-500/20 transition-all">
                    <i class="ph-fill ph-envelope text-slate-400 shrink-0"></i>
                    <input type="email" placeholder="correo@email.com"
                           class="flex-1 min-w-0 w-full bg-transparent text-sm outline-none text-slate-900 dark:text-white placeholder-slate-400">
                </div>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Teléfono</label>
                <div class="flex items-center gap-3 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 bg-white dark:bg-white/5 focus-within:border-brand-500 focus-within:ring-2 focus-within:ring-brand-500/20 transition-all">
                    <i class="ph-fill ph-phone text-slate-400 shrink-0"></i>
                    <input type="tel" placeholder="+51 987 000 000"
                           class="flex-1 min-w-0 w-full bg-transparent text-sm outline-none text-slate-900 dark:text-white placeholder-slate-400">
                </div>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Turno habitual</label>
                <div class="space-y-2">
                    @foreach(['Mañana (07:00–15:00)' => 'manana', 'Tarde (15:00–23:00)' => 'tarde', 'Fin de semana' => 'finde', 'Flexible' => 'flexible'] as $label => $val)
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="radio" name="turno" value="{{ $val }}"
                               class="peer sr-only" {{ $val === 'manana' ? 'checked' : '' }}>
                        <div class="w-5 h-5 shrink-0 rounded-full border-2 border-slate-300 dark:border-white/20 flex items-center justify-center peer-checked:border-brand-500 transition-all">
                            <div class="w-2.5 h-2.5 rounded-full bg-brand-500 opacity-0 peer-checked:opacity-100 transition-all scale-0 peer-checked:scale-100"></div>
                        </div>
                        <span class="text-sm text-slate-600 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-staff').classList.add('hidden')"
                        class="flex-1 py-3 rounded-xl border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="flex-1 py-3 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-bold text-sm transition-all hover:scale-[1.02] shadow-lg shadow-brand-500/20 flex items-center justify-center gap-2">
                    <i class="ph-bold ph-paper-plane-tilt"></i> Enviar invitación
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function copiarLink() {
        navigator.clipboard.writeText('https://futgo.app/staff').then(() => {
            const icon = document.getElementById('copy-icon');
            icon.className = 'ph-fill ph-check text-brand-500';
            setTimeout(() => icon.className = 'ph-bold ph-copy', 2000);
        });
    }

    function filtrarStaff(estado, btn) {
        // Actualizar diseño de botones
        document.querySelectorAll('.filtro-btn').forEach(b => {
            b.classList.remove('bg-brand-500', 'text-white', 'shadow-md', 'shadow-brand-500/20', 'border-transparent');
            b.classList.add('glass-card', 'border', 'border-slate-200', 'dark:border-white/10', 'text-slate-500', 'dark:text-slate-400');
            
            if (b === btn) {
                b.classList.add('bg-brand-500', 'text-white', 'shadow-md', 'shadow-brand-500/20', 'border-transparent');
                b.classList.remove('glass-card', 'border', 'border-slate-200', 'dark:border-white/10', 'text-slate-500', 'dark:text-slate-400');
            }
        });

        // Filtrar tarjetas
        const cards = document.querySelectorAll('.staff-card');
        cards.forEach(card => {
            if (estado === 'todos' || card.dataset.estado === estado) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>
@endpush

@endsection
