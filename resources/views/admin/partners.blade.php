@extends('layouts.admin')
@section('title', 'Partners | FutGo Admin')
@section('page-title', 'Partners')
@section('page-subtitle', 'Gestión y aprobación de complejos deportivos')

@section('content')

@php
$partners = [
    ['id' => 1, 'nombre' => 'Complejo Deportivo El 10', 'ciudad' => 'Wanchaq, Cusco',    'contacto' => 'Juan Quispe',   'email' => 'juan@el10.com',    'canchas' => 4, 'estado' => 'activo',    'reservas_mes' => 124, 'volumen' => 9840.00, 'rating' => 4.8, 'miembro_desde' => 'Ene 2024'],
    ['id' => 2, 'nombre' => 'Arena Sports',             'ciudad' => 'Miraflores, Lima',  'contacto' => 'María López',   'email' => 'maria@arena.com',  'canchas' => 6, 'estado' => 'activo',    'reservas_mes' => 98,  'volumen' => 7840.00, 'rating' => 4.7, 'miembro_desde' => 'Feb 2024'],
    ['id' => 3, 'nombre' => 'SportCenter Norte',        'ciudad' => 'Ttio, Cusco',       'contacto' => 'Pedro Vargas',  'email' => 'pedro@sc.com',     'canchas' => 3, 'estado' => 'activo',    'reservas_mes' => 87,  'volumen' => 6960.00, 'rating' => 4.6, 'miembro_desde' => 'Mar 2024'],
    ['id' => 4, 'nombre' => 'Canchas El Diamante',      'ciudad' => 'Arequipa',          'contacto' => 'Luis Paredes',  'email' => 'luis@diamante.com','canchas' => 3, 'estado' => 'pendiente', 'reservas_mes' => 0,   'volumen' => 0,       'rating' => 0,   'miembro_desde' => 'Solicitud hace 2d'],
    ['id' => 5, 'nombre' => 'Complejo Los Andes',       'ciudad' => 'Lima',              'contacto' => 'Maria Torres',  'email' => 'maria@andes.com',  'canchas' => 5, 'estado' => 'pendiente', 'reservas_mes' => 0,   'volumen' => 0,       'rating' => 0,   'miembro_desde' => 'Solicitud hace 4d'],
    ['id' => 6, 'nombre' => 'SportPlex Trujillo',       'ciudad' => 'Trujillo',          'contacto' => 'Jorge Ríos',    'email' => 'jorge@splex.com',  'canchas' => 2, 'estado' => 'pendiente', 'reservas_mes' => 0,   'volumen' => 0,       'rating' => 0,   'miembro_desde' => 'Solicitud hace 1sem'],
    ['id' => 7, 'nombre' => 'Canchas La Losa',          'ciudad' => 'San Jerónimo, Cusco','contacto' => 'Rosa Mamani', 'email' => 'rosa@lalosa.com',  'canchas' => 2, 'estado' => 'suspendido','reservas_mes' => 0,   'volumen' => 0,       'rating' => 4.2, 'miembro_desde' => 'Jun 2024'],
];
@endphp

{{-- FILTROS --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div class="flex items-center gap-1 glass-card rounded-xl p-1 border border-slate-200 dark:border-white/10">
        @foreach(['Todos' => 'todos', 'Activos' => 'activo', 'Pendientes' => 'pendiente', 'Suspendidos' => 'suspendido'] as $label => $val)
        @php
            $count = count(array_filter($partners, fn($p) => $val === 'todos' || $p['estado'] === $val));
        @endphp
        <button onclick="filtrarPartners('{{ $val }}', this)" 
                class="filtro-btn px-3 py-1.5 rounded-lg text-sm font-semibold transition-all flex items-center gap-1.5 shrink-0
            {{ $val === 'todos' ? 'bg-admin-500 text-white shadow' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
            {{ $label }}
            <span class="badge text-[10px] {{ $val === 'todos' ? 'bg-white/20' : 'bg-slate-200 dark:bg-white/10' }} px-1.5 py-0.5 rounded-full font-bold transition-colors">
                {{ $count }}
            </span>
        </button>
        @endforeach
    </div>
    <div class="flex gap-2 items-center">
        <div class="flex items-center gap-2 glass-card border border-slate-200 dark:border-white/10 rounded-xl px-3 py-2 focus-within:border-admin-500 transition-all">
            <i class="ph-bold ph-magnifying-glass text-slate-400"></i>
            <input type="text" placeholder="Buscar complejo..."
                   class="text-sm bg-transparent outline-none text-slate-800 dark:text-white placeholder-slate-400 w-40">
        </div>
    </div>
</div>

{{-- TABLA --}}
<div class="glass-card rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 dark:bg-white/3 border-b border-slate-100 dark:border-white/5">
                    <th class="text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider px-5 py-3">Complejo</th>
                    <th class="text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 py-3 hidden md:table-cell">Contacto</th>
                    <th class="text-center text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 py-3 hidden sm:table-cell">Canchas</th>
                    <th class="text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 py-3">Estado</th>
                    <th class="text-right text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 py-3 hidden lg:table-cell">Reservas/mes</th>
                    <th class="text-right text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 py-3 hidden lg:table-cell">Volumen</th>
                    <th class="text-center text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 py-3 hidden xl:table-cell">Rating</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-white/5">
                @foreach($partners as $p)
                <tr class="partner-row hover:bg-slate-50 dark:hover:bg-white/3 transition-colors group
                    {{ $p['estado'] === 'pendiente' ? 'bg-amber-50/50 dark:bg-amber-500/5' : '' }}
                    {{ $p['estado'] === 'suspendido' ? 'opacity-60' : '' }}"
                    data-estado="{{ $p['estado'] }}">

                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-admin-500/10 flex items-center justify-center shrink-0">
                                <i class="ph-fill ph-buildings text-admin-500"></i>
                            </div>
                            <div>
                                <p class="font-bold text-slate-900 dark:text-white">{{ $p['nombre'] }}</p>
                                <p class="text-[10px] text-slate-400 flex items-center gap-1">
                                    <i class="ph-fill ph-map-pin"></i> {{ $p['ciudad'] }}
                                    <span class="mx-1">·</span>
                                    {{ $p['miembro_desde'] }}
                                </p>
                            </div>
                        </div>
                    </td>

                    <td class="px-3 py-4 hidden md:table-cell">
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $p['contacto'] }}</p>
                        <p class="text-[10px] text-slate-400">{{ $p['email'] }}</p>
                    </td>

                    <td class="px-3 py-4 text-center hidden sm:table-cell">
                        <span class="font-bold text-slate-700 dark:text-slate-300">{{ $p['canchas'] }}</span>
                    </td>

                    <td class="px-3 py-4">
                        @php
                        $badge = match($p['estado']) {
                            'activo'     => 'bg-brand-500/10 text-brand-600 dark:text-brand-400 border-brand-500/20',
                            'pendiente'  => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
                            'suspendido' => 'bg-red-500/10 text-red-500 border-red-500/20',
                            default      => '',
                        };
                        @endphp
                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full border {{ $badge }}">
                            {{ ucfirst($p['estado']) }}
                        </span>
                    </td>

                    <td class="px-3 py-4 text-right hidden lg:table-cell">
                        <span class="font-bold text-slate-800 dark:text-white">{{ $p['reservas_mes'] ?: '–' }}</span>
                    </td>

                    <td class="px-3 py-4 text-right hidden lg:table-cell">
                        <span class="font-bold text-slate-800 dark:text-white">
                            {{ $p['volumen'] > 0 ? 'S/ '.number_format($p['volumen'],0) : '–' }}
                        </span>
                    </td>

                    <td class="px-3 py-4 text-center hidden xl:table-cell">
                        @if($p['rating'] > 0)
                        <span class="flex items-center justify-center gap-1">
                            <i class="ph-fill ph-star text-amber-400 text-xs"></i>
                            <span class="font-bold text-slate-800 dark:text-white text-sm">{{ $p['rating'] }}</span>
                        </span>
                        @else
                        <span class="text-slate-300">–</span>
                        @endif
                    </td>

                    <td class="px-5 py-4">
                        <div class="flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                            @if($p['estado'] === 'pendiente')
                            <button class="px-2.5 py-1.5 rounded-lg bg-brand-500 text-white text-[10px] font-bold hover:bg-brand-600 transition-colors flex items-center gap-1">
                                <i class="ph-bold ph-check"></i> Aprobar
                            </button>
                            <button class="px-2.5 py-1.5 rounded-lg border border-red-300 dark:border-red-500/30 text-red-500 text-[10px] font-bold hover:bg-red-500 hover:text-white transition-colors flex items-center gap-1">
                                <i class="ph-bold ph-x"></i> Rechazar
                            </button>
                            @elseif($p['estado'] === 'activo')
                            <button class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-white/5 text-slate-400 hover:bg-admin-500/10 hover:text-admin-500 flex items-center justify-center transition-colors" title="Ver detalles">
                                <i class="ph-bold ph-eye text-sm"></i>
                            </button>
                            <button class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-500 hover:bg-amber-500 hover:text-white flex items-center justify-center transition-colors" title="Suspender">
                                <i class="ph-bold ph-pause text-sm"></i>
                            </button>
                            @else
                            <button class="px-2.5 py-1.5 rounded-lg bg-brand-500/10 text-brand-500 text-[10px] font-bold hover:bg-brand-500 hover:text-white transition-colors flex items-center gap-1">
                                <i class="ph-bold ph-play"></i> Reactivar
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    function filtrarPartners(estado, btn) {
        // Actualizar diseño de botones
        document.querySelectorAll('.filtro-btn').forEach(b => {
            b.classList.remove('bg-admin-500', 'text-white', 'shadow');
            b.classList.add('text-slate-500', 'dark:text-slate-400', 'hover:text-slate-900', 'dark:hover:text-white');
            
            const badge = b.querySelector('.badge');
            if (badge) {
                badge.classList.remove('bg-white/20');
                badge.classList.add('bg-slate-200', 'dark:bg-white/10');
            }
            
            if (b === btn) {
                b.classList.add('bg-admin-500', 'text-white', 'shadow');
                b.classList.remove('text-slate-500', 'dark:text-slate-400', 'hover:text-slate-900', 'dark:hover:text-white');
                if (badge) {
                    badge.classList.add('bg-white/20');
                    badge.classList.remove('bg-slate-200', 'dark:bg-white/10');
                }
            }
        });

        // Filtrar filas
        const rows = document.querySelectorAll('.partner-row');
        rows.forEach(row => {
            if (estado === 'todos' || row.dataset.estado === estado) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>
@endpush

@endsection
