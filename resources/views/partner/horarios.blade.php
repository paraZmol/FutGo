@extends('layouts.partner')
@section('title', 'Horarios y Precios | FutGo Partner')
@section('page-title', 'Horarios y Precios')
@section('page-subtitle', ($venue?->name ?? 'Mi complejo') . ' · Configurá tu matriz de turnos')

@section('content')

@php
// $venue y $canchas_real vienen del controlador
$dias  = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb']; // 0=Dom
$horas = [];
for ($h = 6; $h <= 23; $h++) {
    $horas[] = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
}

// Construye la estructura de config desde operating_hours reales
$canchas = collect();
$config  = [];
foreach (($canchas_real ?? collect()) as $field) {
    $tipoLabel = ['futbol5'=>'Fútbol 5','futbol7'=>'Fútbol 7','futbol11'=>'Fútbol 11'];
    $canchas->push([
        'id'     => $field->id,
        'nombre' => $field->name,
        'tipo'   => $tipoLabel[$field->sport_type] ?? $field->sport_type,
    ]);

    // Horario de referencia (lunes)
    $ref = $field->operatingHours->firstWhere('day_of_week', 1)
        ?? $field->operatingHours->first();

    $diasActivos = array_fill(0, 7, 0);
    foreach ($field->operatingHours as $oh) {
        $diasActivos[$oh->day_of_week] = $oh->is_active ? 1 : 0;
    }

    $config[$field->id] = [
        'apertura'    => $ref ? substr($ref->opens_at, 0, 5)  : '07:00',
        'cierre'      => $ref ? substr($ref->closes_at, 0, 5) : '22:00',
        'precio_dia'  => $ref ? (float)$ref->price_day        : 70.00,
        'precio_noche'=> $ref ? (float)$ref->price_night      : 85.00,
        'anticipo'    => $ref ? (float)$ref->deposit_amount   : 25.00,
        'dias'        => $diasActivos,
    ];
}
@endphp

{{-- TABS DE CANCHAS --}}
<div class="flex items-center gap-2 mb-6 overflow-x-auto no-scrollbar pb-1">
    @foreach($canchas as $c)
    <button onclick="verCancha({{ $c['id'] }}, this)"
            class="cancha-tab shrink-0 px-4 py-2 rounded-xl text-sm font-semibold transition-all
            {{ $c['id'] === 1 ? 'bg-brand-500 text-white shadow-md shadow-brand-500/20' : 'glass-card border border-slate-200 dark:border-white/10 text-slate-500 dark:text-slate-400 hover:border-brand-500 hover:text-brand-500' }}">
        {{ $c['nombre'] }} · {{ $c['tipo'] }}
    </button>
    @endforeach
</div>

{{-- PANEL POR CANCHA --}}
@foreach($canchas as $c)
@php $cfg = $config[$c['id']]; @endphp
<div class="cancha-panel space-y-5 {{ $c['id'] !== 1 ? 'hidden' : '' }}" data-cancha="{{ $c['id'] }}">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ============================================================
             COLUMNA IZQUIERDA: configuración general
        ============================================================ --}}
        <div class="space-y-5">

            {{-- Horario apertura/cierre --}}
            <div class="glass-card rounded-2xl p-5">
                <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-4">
                    <i class="ph-fill ph-clock text-brand-500"></i> Horario de operación
                </h3>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 block">Apertura</label>
                        <div class="flex items-center gap-2 border border-slate-200 dark:border-white/10 rounded-xl px-3 py-2.5 focus-within:border-brand-500 transition-all bg-white dark:bg-white/5">
                            <i class="ph-fill ph-sun text-amber-400 shrink-0"></i>
                            <input type="time" value="{{ $cfg['apertura'] }}"
                                   class="flex-1 bg-transparent text-sm font-semibold outline-none text-slate-800 dark:text-white cursor-pointer [color-scheme:light] dark:[color-scheme:dark]">
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 block">Cierre</label>
                        <div class="flex items-center gap-2 border border-slate-200 dark:border-white/10 rounded-xl px-3 py-2.5 focus-within:border-brand-500 transition-all bg-white dark:bg-white/5">
                            <i class="ph-fill ph-moon text-blue-400 shrink-0"></i>
                            <input type="time" value="{{ $cfg['cierre'] }}"
                                   class="flex-1 bg-transparent text-sm font-semibold outline-none text-slate-800 dark:text-white cursor-pointer [color-scheme:light] dark:[color-scheme:dark]">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Precios --}}
            <div class="glass-card rounded-2xl p-5">
                <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-4">
                    <i class="ph-fill ph-money text-brand-500"></i> Precios por hora
                </h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 block">
                            <i class="ph-fill ph-sun text-amber-400"></i> Día (07:00 – 18:00)
                        </label>
                        <div class="flex items-center gap-2 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 focus-within:border-brand-500 transition-all bg-white dark:bg-white/5">
                            <span class="font-bold text-slate-500 dark:text-slate-400">S/</span>
                            <input type="number" value="{{ $cfg['precio_dia'] }}" min="0"
                                   class="flex-1 min-w-0 w-full bg-transparent text-xl font-extrabold outline-none text-slate-900 dark:text-white">
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 block">
                            <i class="ph-fill ph-moon text-blue-400"></i> Noche (18:00 – cierre)
                        </label>
                        <div class="flex items-center gap-2 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 focus-within:border-brand-500 transition-all bg-white dark:bg-white/5">
                            <span class="font-bold text-slate-500 dark:text-slate-400">S/</span>
                            <input type="number" value="{{ $cfg['precio_noche'] }}" min="0"
                                   class="flex-1 min-w-0 w-full bg-transparent text-xl font-extrabold outline-none text-slate-900 dark:text-white">
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 block">
                            <i class="ph-fill ph-credit-card text-purple-400"></i> Anticipo online
                        </label>
                        <div class="flex items-center gap-2 border border-brand-500/30 rounded-xl px-4 py-3 bg-brand-500/5 focus-within:border-brand-500 transition-all">
                            <span class="font-bold text-brand-500">S/</span>
                            <input type="number" value="{{ $cfg['anticipo'] }}" min="0"
                                   class="flex-1 min-w-0 w-full bg-transparent text-xl font-extrabold outline-none text-brand-600 dark:text-brand-400">
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1.5 flex items-center gap-1">
                            <i class="ph-fill ph-info text-slate-400"></i>
                            El resto se cobra en efectivo en la cancha
                        </p>
                    </div>
                </div>
            </div>

            {{-- Días activos --}}
            <div class="glass-card rounded-2xl p-5">
                <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-4">
                    <i class="ph-fill ph-calendar-blank text-brand-500"></i> Días activos
                </h3>
                <div class="grid grid-cols-7 gap-1.5">
                    @foreach($dias as $i => $dia)
                    <label class="cursor-pointer">
                        <input type="checkbox" class="peer sr-only"
                               {{ $cfg['dias'][$i] ? 'checked' : '' }}>
                        <div class="text-center py-2 rounded-xl border-2 transition-all text-xs font-bold
                                    peer-checked:border-brand-500 peer-checked:bg-brand-500/10 peer-checked:text-brand-600 dark:peer-checked:text-brand-400
                                    border-slate-200 dark:border-white/10 text-slate-400 hover:border-brand-500/50 cursor-pointer">
                            {{ $dia }}
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- ============================================================
             COLUMNA DERECHA: grilla visual de slots
        ============================================================ --}}
        <div class="lg:col-span-2 glass-card rounded-2xl p-5">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="ph-fill ph-grid-four text-brand-500"></i> Vista semanal de slots
                </h3>
                <div class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    <span class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded bg-brand-500/20 border border-brand-500/40 inline-block"></span> Activo
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded bg-amber-500/20 border border-amber-500/40 inline-block"></span> Precio noche
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded bg-slate-100 dark:bg-white/5 inline-block"></span> Cerrado
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr>
                            <th class="text-left text-slate-400 font-semibold pb-2 pr-2 w-14">Hora</th>
                            @foreach($dias as $dia)
                            <th class="text-center text-slate-400 font-semibold pb-2 min-w-[3.5rem]">{{ $dia }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($horas as $hora)
                        @php
                            $h = (int) $hora;
                            $apert = (int) explode(':', $cfg['apertura'])[0];
                            $cierr = (int) explode(':', $cfg['cierre'])[0];
                            $esNoche = $h >= 18;
                            $estaActivo = $h >= $apert && $h < $cierr;
                        @endphp
                        <tr>
                            <td class="py-0.5 pr-2">
                                <span class="font-bold {{ $esNoche ? 'text-blue-400' : 'text-slate-500 dark:text-slate-400' }}">
                                    {{ $hora }}
                                </span>
                            </td>
                            @foreach($dias as $i => $dia)
                            @php $diaActivo = $cfg['dias'][$i] && $estaActivo; @endphp
                            <td class="py-0.5 px-0.5">
                                <div class="h-8 rounded-lg mx-auto flex items-center justify-center text-[10px] font-bold transition-all cursor-pointer hover:opacity-80 px-1
                                    @if(!$diaActivo) bg-slate-100 dark:bg-white/5 text-slate-400 dark:text-slate-500
                                    @elseif($esNoche) bg-amber-500/20 border border-amber-500/30 text-amber-700 dark:text-amber-400
                                    @else bg-brand-500/20 border border-brand-500/30 text-brand-700 dark:text-brand-400
                                    @endif">
                                    @if($diaActivo)
                                        S/ {{ $esNoche ? $cfg['precio_noche'] : $cfg['precio_dia'] }}
                                    @else
                                        –
                                    @endif
                                </div>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- BLOQUEOS ESPECIALES --}}
    <div class="glass-card rounded-2xl p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="ph-fill ph-lock text-amber-500"></i> Bloqueos y cierres especiales
            </h3>
            <button onclick="document.getElementById('modal-bloqueo').classList.remove('hidden')"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-600 dark:text-amber-400 text-sm font-bold hover:bg-amber-500 hover:text-white transition-colors">
                <i class="ph-bold ph-plus"></i> Agregar bloqueo
            </button>
        </div>

        <div class="space-y-2">
            @foreach([
                ['fecha' => 'Sáb 17 May', 'hora_ini' => '07:00', 'hora_fin' => '23:00', 'motivo' => 'Torneo Relámpago Interempresas', 'tipo' => 'evento'],
                ['fecha' => 'Dom 18 May', 'hora_ini' => '07:00', 'hora_fin' => '20:00', 'motivo' => 'Campeonato Regional Sub-15', 'tipo' => 'evento'],
                ['fecha' => 'Lun 19 May', 'hora_ini' => '07:00', 'hora_fin' => '12:00', 'motivo' => 'Mantenimiento de pasto sintético', 'tipo' => 'mantenimiento'],
            ] as $bloqueo)
            <div class="flex items-center gap-4 p-3 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0
                    {{ $bloqueo['tipo'] === 'evento' ? 'bg-purple-500/10' : 'bg-amber-500/10' }}">
                    <i class="ph-fill {{ $bloqueo['tipo'] === 'evento' ? 'ph-trophy text-purple-500' : 'ph-wrench text-amber-500' }} text-lg"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 dark:text-white">{{ $bloqueo['motivo'] }}</p>
                    <p class="text-xs text-slate-400">{{ $bloqueo['fecha'] }} · {{ $bloqueo['hora_ini'] }} – {{ $bloqueo['hora_fin'] }}</p>
                </div>
                <span class="text-[10px] font-bold px-2.5 py-1 rounded-full
                    {{ $bloqueo['tipo'] === 'evento' ? 'bg-purple-500/10 text-purple-500 border border-purple-500/20' : 'bg-amber-500/10 text-amber-500 border border-amber-500/20' }}">
                    {{ ucfirst($bloqueo['tipo']) }}
                </span>
                <button class="w-7 h-7 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white flex items-center justify-center transition-colors shrink-0">
                    <i class="ph-bold ph-trash text-sm"></i>
                </button>
            </div>
            @endforeach

            @if(count([]) === 0)
            <p class="text-center text-slate-400 text-sm py-4">Sin bloqueos programados</p>
            @endif
        </div>
    </div>

    {{-- BOTÓN GUARDAR --}}
    <div class="flex justify-end gap-3">
        <button class="px-6 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
            Descartar cambios
        </button>
        <button onclick="guardarConfig({{ $c['id'] }})"
                class="px-8 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-bold text-sm transition-all hover:scale-105 shadow-lg shadow-brand-500/20 flex items-center gap-2">
            <i class="ph-bold ph-floppy-disk"></i> Guardar configuración
        </button>
    </div>

</div>
@endforeach

{{-- MODAL BLOQUEO --}}
<div id="modal-bloqueo" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
         onclick="document.getElementById('modal-bloqueo').classList.add('hidden')"></div>
    <div class="relative glass-card rounded-3xl p-6 w-full max-w-md shadow-2xl">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">Nuevo bloqueo</h2>
            <button onclick="document.getElementById('modal-bloqueo').classList.add('hidden')"
                    class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-white/10 flex items-center justify-center text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors">
                <i class="ph-bold ph-x"></i>
            </button>
        </div>
        <form class="space-y-4">
            <div>
                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Motivo</label>
                <input type="text" placeholder="Ej: Torneo, mantenimiento..."
                       class="w-full border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm bg-white dark:bg-white/5 text-slate-900 dark:text-white outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition-all placeholder-slate-400">
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Tipo</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach(['Evento / Torneo' => 'evento', 'Mantenimiento' => 'mantenimiento'] as $label => $val)
                    <label class="cursor-pointer">
                        <input type="radio" name="tipo_bloqueo" value="{{ $val }}" class="peer sr-only" {{ $val === 'evento' ? 'checked' : '' }}>
                        <div class="text-center py-3 rounded-xl border-2 border-slate-200 dark:border-white/10
                                    peer-checked:border-brand-500 peer-checked:bg-brand-500/10 peer-checked:text-brand-600 dark:peer-checked:text-brand-400
                                    text-slate-500 dark:text-slate-400 text-sm font-semibold transition-all cursor-pointer">
                            {{ $label }}
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Fecha inicio</label>
                    <input type="date" min="{{ date('Y-m-d') }}"
                           class="w-full border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm bg-white dark:bg-white/5 text-slate-900 dark:text-white outline-none focus:border-brand-500 transition-all cursor-pointer [color-scheme:light] dark:[color-scheme:dark]">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Fecha fin</label>
                    <input type="date" min="{{ date('Y-m-d') }}"
                           class="w-full border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm bg-white dark:bg-white/5 text-slate-900 dark:text-white outline-none focus:border-brand-500 transition-all cursor-pointer [color-scheme:light] dark:[color-scheme:dark]">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Hora inicio</label>
                    <input type="time" value="07:00"
                           class="w-full border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm bg-white dark:bg-white/5 text-slate-900 dark:text-white outline-none focus:border-brand-500 transition-all cursor-pointer [color-scheme:light] dark:[color-scheme:dark]">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Hora fin</label>
                    <input type="time" value="23:00"
                           class="w-full border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm bg-white dark:bg-white/5 text-slate-900 dark:text-white outline-none focus:border-brand-500 transition-all cursor-pointer [color-scheme:light] dark:[color-scheme:dark]">
                </div>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="document.getElementById('modal-bloqueo').classList.add('hidden')"
                        class="flex-1 py-3 rounded-xl border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="flex-1 py-3 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-bold text-sm transition-all hover:scale-[1.02] shadow-lg shadow-brand-500/20">
                    Guardar bloqueo
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function verCancha(id, btn) {
        document.querySelectorAll('.cancha-panel').forEach(p => p.classList.add('hidden'));
        document.querySelectorAll('.cancha-tab').forEach(b => {
            b.classList.remove('bg-brand-500','text-white','shadow-md','shadow-brand-500/20');
            b.classList.add('glass-card','border','border-slate-200','dark:border-white/10','text-slate-500','dark:text-slate-400');
        });
        document.querySelector(`.cancha-panel[data-cancha="${id}"]`).classList.remove('hidden');
        btn.classList.add('bg-brand-500','text-white','shadow-md','shadow-brand-500/20');
        btn.classList.remove('glass-card','border','border-slate-200','dark:border-white/10','text-slate-500','dark:text-slate-400');
    }

    function guardarConfig(id) {
        const btn = event.target;
        btn.innerHTML = '<i class="ph-fill ph-check-circle text-lg"></i> Guardado';
        btn.classList.add('bg-emerald-600');
        setTimeout(() => {
            btn.innerHTML = '<i class="ph-bold ph-floppy-disk"></i> Guardar configuración';
            btn.classList.remove('bg-emerald-600');
        }, 2000);
    }
</script>
@endpush

@endsection
