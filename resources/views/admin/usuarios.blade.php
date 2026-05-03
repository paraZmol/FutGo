@extends('layouts.admin')
@section('title', 'Usuarios y Moderadores | FutGo Admin')
@section('page-title', 'Usuarios y Moderadores')
@section('page-subtitle', 'Gestión de jugadores y equipo de moderación')

@section('content')

@php
$moderadores = [
    ['id' => 1, 'nombre' => 'Carlos Ríos',  'email' => 'carlos.rios@futgo.app',  'ciudad' => 'Lima',     'estado' => 'activo',    'desde' => 'Ene 2024', 'ultimo' => 'Hoy',       'acciones_mes' => 48, 'permisos' => ['partners','disputas','reservas','usuarios_ver','usuarios_suspend','usuarios_del','comisiones','auditoria']],
    ['id' => 2, 'nombre' => 'Luisa Vega',  'email' => 'luisa.vega@futgo.app',   'ciudad' => 'Cusco',    'estado' => 'activo',    'desde' => 'Mar 2024', 'ultimo' => 'Ayer',      'acciones_mes' => 31, 'permisos' => ['partners','disputas','reservas','usuarios_ver','usuarios_suspend','usuarios_del','comisiones','auditoria']],
    ['id' => 3, 'nombre' => 'Marco Salas', 'email' => 'marco.salas@futgo.app',  'ciudad' => 'Arequipa', 'estado' => 'suspendido','desde' => 'May 2024', 'ultimo' => 'Hace 2sem', 'acciones_mes' => 0,  'permisos' => []],
];

$jugadores = [
    ['id' => 1, 'nombre' => 'Mario Quispe',   'email' => 'mario@email.com',  'ciudad' => 'Cusco',    'reservas' => 34, 'gasto' => 2720.00, 'estado' => 'activo',    'registro' => 'Ene 2024', 'ultimo' => 'Hoy'],
    ['id' => 2, 'nombre' => 'Luis Torres',    'email' => 'luis@email.com',   'ciudad' => 'Lima',     'reservas' => 28, 'gasto' => 2240.00, 'estado' => 'activo',    'registro' => 'Feb 2024', 'ultimo' => 'Ayer'],
    ['id' => 3, 'nombre' => 'Carlos Mamani',  'email' => 'carlos@email.com', 'ciudad' => 'Cusco',    'reservas' => 22, 'gasto' => 1760.00, 'estado' => 'activo',    'registro' => 'Feb 2024', 'ultimo' => 'Hace 3d'],
    ['id' => 4, 'nombre' => 'Ana Gutierrez',  'email' => 'ana@email.com',    'ciudad' => 'Lima',     'reservas' => 18, 'gasto' => 1440.00, 'estado' => 'activo',    'registro' => 'Mar 2024', 'ultimo' => 'Hace 1sem'],
    ['id' => 5, 'nombre' => 'Pedro Huanca',   'email' => 'pedro@email.com',  'ciudad' => 'Arequipa', 'reservas' => 15, 'gasto' => 1200.00, 'estado' => 'activo',    'registro' => 'Mar 2024', 'ultimo' => 'Hace 2sem'],
    ['id' => 6, 'nombre' => 'Diego Vargas',   'email' => 'diego@email.com',  'ciudad' => 'Trujillo', 'reservas' => 8,  'gasto' =>  640.00, 'estado' => 'suspendido','registro' => 'Abr 2024', 'ultimo' => 'Hace 1mes'],
];

$todos_permisos = [
    'partners'         => ['label' => 'Aprobar Partners',       'icon' => 'ph-buildings',      'color' => 'text-admin-500'],
    'disputas'         => ['label' => 'Resolver Disputas',      'icon' => 'ph-warning',        'color' => 'text-red-500'],
    'reservas'         => ['label' => 'Ver Reservas',           'icon' => 'ph-calendar-check', 'color' => 'text-blue-500'],
    'usuarios_ver'     => ['label' => 'Ver Jugadores',          'icon' => 'ph-users',          'color' => 'text-brand-500'],
    'usuarios_suspend' => ['label' => 'Suspender Jugadores',    'icon' => 'ph-user-minus',     'color' => 'text-amber-500'],
    'usuarios_del'     => ['label' => 'Eliminar Jugadores',     'icon' => 'ph-trash',          'color' => 'text-red-500'],
    'comisiones'       => ['label' => 'Configurar Comisiones',  'icon' => 'ph-percent',        'color' => 'text-purple-500'],
    'auditoria'        => ['label' => 'Ver Auditoría',          'icon' => 'ph-list-magnifying-glass', 'color' => 'text-slate-500'],
];
@endphp

{{-- TABS --}}
<div class="flex items-center gap-1 glass-card rounded-xl p-1 border border-slate-200 dark:border-white/10 mb-6 w-fit">
    <button onclick="mostrarTab('moderadores', this)"
            class="tab-btn px-4 py-2 rounded-lg text-sm font-semibold transition-all bg-admin-500 text-white shadow">
        <i class="ph-bold ph-shield-check mr-1"></i> Moderadores
    </button>
    <button onclick="mostrarTab('jugadores', this)"
            class="tab-btn px-4 py-2 rounded-lg text-sm font-semibold transition-all text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
        <i class="ph-bold ph-users mr-1"></i> Jugadores
    </button>
</div>

{{-- ============================================================
     PANEL MODERADORES
============================================================ --}}
<div id="panel-moderadores" class="space-y-5">

    {{-- Info de permisos --}}
    <div class="bg-admin-500/5 border border-admin-500/20 rounded-2xl p-4 flex items-start gap-3">
        <i class="ph-fill ph-shield-check text-admin-500 text-xl shrink-0 mt-0.5"></i>
        <div>
            <p class="font-bold text-admin-600 dark:text-admin-400 text-sm">Moderadores vs Super Admin</p>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Los moderadores pueden aprobar partners, resolver disputas y ver datos de la plataforma.
                <strong class="text-slate-700 dark:text-slate-300">No pueden</strong> eliminar usuarios, gestionar comisiones, crear/eliminar moderadores ni acceder a la configuración de la plataforma.
            </p>
        </div>
    </div>

    {{-- Lista de moderadores --}}
    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-white/5">
            <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="ph-fill ph-shield-check text-admin-500"></i>
                Equipo de moderación
                <span class="text-[10px] font-bold bg-admin-500/10 text-admin-500 px-2 py-0.5 rounded-full">{{ count($moderadores) }}</span>
            </h2>
            <button onclick="document.getElementById('modal-moderador').classList.remove('hidden')"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl bg-admin-500 hover:bg-admin-600 text-white font-bold text-sm transition-all hover:scale-105 shadow-md shadow-admin-500/20">
                <i class="ph-bold ph-plus"></i> Nuevo moderador
            </button>
        </div>

        <div class="divide-y divide-slate-50 dark:divide-white/5">
            @foreach($moderadores as $mod)
            <div class="mod-card flex flex-col sm:flex-row sm:items-center gap-4 px-5 py-4 {{ $mod['estado'] === 'suspendido' ? 'opacity-60 bg-red-50/30 dark:bg-red-500/5' : '' }}"
                 data-estado="{{ $mod['estado'] }}">

                {{-- Avatar --}}
                <div class="relative shrink-0 flex items-center justify-center sm:justify-start">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-admin-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                        {{ strtoupper(substr($mod['nombre'], 0, 2)) }}
                    </div>
                    @if($mod['estado'] === 'activo')
                    <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-brand-500 border-2 border-white dark:border-dark-900 rounded-full"></span>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0 text-center sm:text-left">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mb-1">
                        <p class="font-bold text-slate-900 dark:text-white text-base">{{ $mod['nombre'] }}</p>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full
                            {{ $mod['estado'] === 'activo' ? 'bg-brand-500/10 text-brand-600 dark:text-brand-400 border border-brand-500/20' : 'bg-red-500/10 text-red-500 border border-red-500/20' }}">
                            {{ ucfirst($mod['estado']) }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center justify-center sm:justify-start gap-3 flex-wrap">
                        <span class="flex items-center gap-1"><i class="ph-fill ph-envelope"></i>{{ $mod['email'] }}</span>
                        <span class="flex items-center gap-1"><i class="ph-fill ph-map-pin"></i>{{ $mod['ciudad'] }}</span>
                        <span class="flex items-center gap-1"><i class="ph-fill ph-clock"></i>Desde {{ $mod['desde'] }}</span>
                    </p>
                    <p class="text-[10px] font-semibold text-admin-500 mt-1">Último: {{ $mod['ultimo'] }} · {{ $mod['acciones_mes'] }} acciones este mes</p>

                    {{-- Permisos activos --}}
                    @if(count($mod['permisos']) > 0)
                    <div class="flex flex-wrap justify-center sm:justify-start gap-1.5 mt-2">
                        @foreach($mod['permisos'] as $perm)
                        @if(isset($todos_permisos[$perm]))
                        <span class="flex items-center gap-1 text-[10px] font-semibold bg-slate-100 dark:bg-white/5 text-slate-500 dark:text-slate-400 px-2 py-0.5 rounded-lg border border-slate-200 dark:border-white/5">
                            <i class="ph-fill {{ $todos_permisos[$perm]['icon'] }} {{ $todos_permisos[$perm]['color'] }}"></i>
                            {{ $todos_permisos[$perm]['label'] }}
                        </span>
                        @endif
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Acciones --}}
                <div class="flex sm:flex-col justify-center gap-2 shrink-0 border-t sm:border-t-0 border-slate-50 dark:border-white/5 pt-3 sm:pt-0">
                    <button class="flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 dark:border-white/10 text-xs font-semibold text-slate-500 hover:border-admin-500 hover:text-admin-500 transition-all flex-1 sm:flex-initial">
                        <i class="ph-bold ph-pencil"></i> <span class="sm:hidden lg:inline">Editar</span>
                    </button>
                    @if($mod['estado'] === 'activo')
                    <button class="flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl border border-red-200 dark:border-red-500/20 text-xs font-semibold text-red-400 hover:bg-red-500 hover:text-white hover:border-red-500 transition-all flex-1 sm:flex-initial">
                        <i class="ph-bold ph-pause"></i> <span class="sm:hidden lg:inline">Suspender</span>
                    </button>
                    @else
                    <button class="flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl border border-brand-200 dark:border-brand-500/20 text-xs font-semibold text-brand-500 hover:bg-brand-500 hover:text-white hover:border-brand-500 transition-all flex-1 sm:flex-initial">
                        <i class="ph-bold ph-play"></i> <span class="sm:hidden lg:inline">Reactivar</span>
                    </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Tabla de permisos --}}
    <div class="glass-card rounded-2xl p-5">
        <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-4">
            <i class="ph-fill ph-key text-admin-500"></i> Matriz de permisos
        </h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-white/5">
                        <th class="text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider pb-3 pr-4">Acción</th>
                        <th class="text-center text-[10px] font-bold text-admin-500 uppercase tracking-wider pb-3 px-4">Super Admin</th>
                        <th class="text-center text-[10px] font-bold text-slate-400 uppercase tracking-wider pb-3 px-4">Moderador</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-white/5">
                    @foreach([
                        ['accion' => 'Aprobar / rechazar Partners',    'admin' => true, 'mod' => true],
                        ['accion' => 'Resolver disputas',              'admin' => true, 'mod' => true],
                        ['accion' => 'Ver reservas globales',          'admin' => true, 'mod' => true],
                        ['accion' => 'Ver jugadores',                  'admin' => true, 'mod' => true],
                        ['accion' => 'Suspender jugadores',            'admin' => true, 'mod' => true],
                        ['accion' => 'Eliminar jugadores',             'admin' => true, 'mod' => true],
                        ['accion' => 'Configurar comisiones',          'admin' => true, 'mod' => true],
                        ['accion' => 'Ver auditoría',                  'admin' => true, 'mod' => true],
                        ['accion' => 'Gestionar moderadores',          'admin' => true, 'mod' => false],
                        ['accion' => 'Configurar plataforma',          'admin' => true, 'mod' => false],
                    ] as $perm)
                    <tr class="hover:bg-slate-50 dark:hover:bg-white/3 transition-colors">
                        <td class="py-2.5 pr-4 text-slate-700 dark:text-slate-300 font-medium">{{ $perm['accion'] }}</td>
                        <td class="py-2.5 px-4 text-center">
                            <i class="ph-fill ph-check-circle text-brand-500 text-lg"></i>
                        </td>
                        <td class="py-2.5 px-4 text-center">
                            @if($perm['mod'])
                            <i class="ph-fill ph-check-circle text-brand-500 text-lg"></i>
                            @else
                            <i class="ph-fill ph-x-circle text-red-400 text-lg"></i>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ============================================================
     PANEL JUGADORES (oculto por defecto)
============================================================ --}}
<div id="panel-jugadores" class="hidden space-y-4">

    <div class="flex items-center gap-2 glass-card border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2.5 focus-within:border-admin-500 transition-all max-w-sm">
        <i class="ph-bold ph-magnifying-glass text-slate-400"></i>
        <input type="text" id="search-jugadores" placeholder="Buscar por nombre o email..."
               class="text-sm bg-transparent outline-none text-slate-800 dark:text-white placeholder-slate-400 flex-1">
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-white/3 border-b border-slate-100 dark:border-white/5">
                        <th class="text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider px-5 py-3">Jugador</th>
                        <th class="text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 py-3 hidden md:table-cell">Ciudad</th>
                        <th class="text-center text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 py-3 hidden sm:table-cell">Reservas</th>
                        <th class="text-right text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 py-3 hidden lg:table-cell">Gasto total</th>
                        <th class="text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 py-3">Estado</th>
                        <th class="text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 py-3 hidden xl:table-cell">Último acceso</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-white/5">
                    @foreach($jugadores as $u)
                    <tr class="jugador-row hover:bg-slate-50 dark:hover:bg-white/3 transition-colors group {{ $u['estado'] === 'suspendido' ? 'opacity-60' : '' }}"
                        data-estado="{{ $u['estado'] }}">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-brand-500/10 flex items-center justify-center text-xs font-bold text-brand-600 dark:text-brand-400 shrink-0">
                                    {{ strtoupper(substr($u['nombre'],0,2)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 dark:text-white">{{ $u['nombre'] }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $u['email'] }} · Desde {{ $u['registro'] }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-3.5 hidden md:table-cell text-slate-600 dark:text-slate-400">{{ $u['ciudad'] }}</td>
                        <td class="px-3 py-3.5 text-center hidden sm:table-cell font-bold text-slate-800 dark:text-white">{{ $u['reservas'] }}</td>
                        <td class="px-3 py-3.5 text-right hidden lg:table-cell font-bold text-slate-800 dark:text-white">S/ {{ number_format($u['gasto'],2) }}</td>
                        <td class="px-3 py-3.5">
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full border
                                {{ $u['estado'] === 'activo' ? 'bg-brand-500/10 text-brand-600 dark:text-brand-400 border-brand-500/20' : 'bg-red-500/10 text-red-500 border-red-500/20' }}">
                                {{ ucfirst($u['estado']) }}
                            </span>
                        </td>
                        <td class="px-3 py-3.5 text-xs text-slate-400 hidden xl:table-cell">{{ $u['ultimo'] }}</td>
                        <td class="px-5 py-3.5">
                            {{-- Solo Super Admin puede suspender/eliminar --}}
                            <div class="flex gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-white/5 text-slate-400 hover:bg-admin-500/10 hover:text-admin-500 flex items-center justify-center transition-colors" title="Ver perfil">
                                    <i class="ph-bold ph-eye text-sm"></i>
                                </button>
                                @if($u['estado'] === 'activo')
                                <button class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-500 hover:bg-amber-500 hover:text-white flex items-center justify-center transition-colors" title="Suspender (solo Super Admin)">
                                    <i class="ph-bold ph-pause text-sm"></i>
                                </button>
                                @else
                                <button class="w-7 h-7 rounded-lg bg-brand-500/10 text-brand-500 hover:bg-brand-500 hover:text-white flex items-center justify-center transition-colors" title="Reactivar (solo Super Admin)">
                                    <i class="ph-bold ph-play text-sm"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-slate-100 dark:border-white/5 text-xs text-slate-400">
            Mostrando {{ count($jugadores) }} de 5,284 jugadores
        </div>
    </div>
</div>

{{-- MODAL NUEVO MODERADOR --}}
<div id="modal-moderador" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
         onclick="document.getElementById('modal-moderador').classList.add('hidden')"></div>
    <div class="relative glass-card rounded-3xl p-6 w-full max-w-md shadow-2xl">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">Nuevo moderador</h2>
                <p class="text-xs text-slate-400 mt-0.5">Solo el Super Admin puede crear moderadores</p>
            </div>
            <button onclick="document.getElementById('modal-moderador').classList.add('hidden')"
                    class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-white/10 flex items-center justify-center text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors">
                <i class="ph-bold ph-x"></i>
            </button>
        </div>
        <form class="space-y-4">
            <div>
                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Nombre completo</label>
                <div class="flex items-center gap-3 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 bg-white dark:bg-white/5 focus-within:border-admin-500 transition-all">
                    <i class="ph-fill ph-user text-slate-400 shrink-0"></i>
                    <input type="text" placeholder="Nombre del moderador"
                           class="flex-1 bg-transparent text-sm outline-none text-slate-900 dark:text-white placeholder-slate-400">
                </div>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Email corporativo</label>
                <div class="flex items-center gap-3 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 bg-white dark:bg-white/5 focus-within:border-admin-500 transition-all">
                    <i class="ph-fill ph-envelope text-slate-400 shrink-0"></i>
                    <input type="email" placeholder="nombre@futgo.app"
                           class="flex-1 bg-transparent text-sm outline-none text-slate-900 dark:text-white placeholder-slate-400">
                </div>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3 block">Permisos</label>
                <div class="space-y-2">
                    @foreach($todos_permisos as $key => $perm)
                    <label class="flex items-center gap-3 cursor-pointer p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                        <div class="relative flex items-center">
                            <input type="checkbox" value="{{ $key }}" checked
                                   class="peer appearance-none w-5 h-5 border-2 border-slate-300 dark:border-white/20 rounded-md bg-white dark:bg-white/5 checked:bg-admin-500 checked:border-admin-500 transition-all cursor-pointer">
                            <i class="ph-bold ph-check text-white absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 opacity-0 peer-checked:opacity-100 pointer-events-none text-xs"></i>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="ph-fill {{ $perm['icon'] }} {{ $perm['color'] }}"></i>
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $perm['label'] }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-moderador').classList.add('hidden')"
                        class="flex-1 py-3 rounded-xl border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="flex-1 py-3 rounded-xl bg-admin-500 hover:bg-admin-600 text-white font-bold text-sm transition-all hover:scale-[1.02] shadow-lg shadow-admin-500/20 flex items-center justify-center gap-2">
                    <i class="ph-bold ph-paper-plane-tilt"></i> Crear y enviar acceso
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function mostrarTab(tab, btn) {
        document.getElementById('panel-moderadores').classList.add('hidden');
        document.getElementById('panel-jugadores').classList.add('hidden');
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('bg-admin-500', 'text-white', 'shadow');
            b.classList.add('text-slate-500', 'dark:text-slate-400');
        });
        document.getElementById('panel-' + tab).classList.remove('hidden');
        btn.classList.add('bg-admin-500', 'text-white', 'shadow');
        btn.classList.remove('text-slate-500', 'dark:text-slate-400');
    }

    // Filtrado de jugadores por búsqueda
    document.getElementById('search-jugadores')?.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        document.querySelectorAll('.jugador-row').forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });

    // Función global para filtrar por estado (se puede llamar desde consola o botones si se agregan)
    function filtrarPorEstado(tab, estado) {
        const className = tab === 'moderadores' ? '.mod-card' : '.jugador-row';
        document.querySelectorAll(className).forEach(el => {
            if (estado === 'todos' || el.dataset.estado === estado) {
                el.style.display = '';
            } else {
                el.style.display = 'none';
            }
        });
    }
</script>
@endpush

@endsection
