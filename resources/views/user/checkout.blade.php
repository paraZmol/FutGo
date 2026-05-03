@extends('layouts.app')
@section('title', 'Confirmar reserva | FutGo')

@section('content')

@php
// $slotsParam, $total, $venueId, $venue vienen del controlador (routes/web.php)
// Parsear los slot IDs enviados desde el detalle de cancha
$slotIds = [];
if (!empty($slotsParam)) {
    $decoded = json_decode($slotsParam, true);
    $slotIds = is_array($decoded) ? $decoded : explode(',', $slotsParam);
}

$slots    = $slotIds ? App\Models\Slot::whereIn('id', $slotIds)->orderBy('starts_at')->get() : collect();
$primerSlot = $slots->first();
$ultimoSlot = $slots->last();

// Datos del venue/cancha reales
$imgDefault = 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?w=600&q=80';

$reserva = [
    'venue_id'  => $venue?->id ?? $venueId,
    'field_id'  => $primerSlot?->field_id,
    'cancha'    => $venue?->name ?? 'Cancha',
    'tipo'      => $primerSlot?->field?->sport_type ?? 'futbol5',
    'direccion' => $venue ? ($venue->address . ', ' . $venue->district) : '–',
    'fecha'     => $primerSlot ? $primerSlot->starts_at->format('d M Y') : date('d M Y'),
    'hora'      => $primerSlot && $ultimoSlot
        ? $primerSlot->starts_at->format('H:i') . ' – ' . $ultimoSlot->ends_at->format('H:i')
        : '–',
    'horas'     => $slots->count(),
    'imagen'    => $imgDefault,
    'precio'    => (float) $total,
    'anticipo'  => round((float) $total * 0.35, 2),
    'saldo'     => round((float) $total * 0.65, 2),
    'slot_ids'  => $slotIds,
];
@endphp

{{-- BREADCRUMB --}}
<div class="bg-white/50 dark:bg-dark-900/50 backdrop-blur-md border-b border-slate-200 dark:border-white/10 sticky top-16 md:top-20 z-30">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-3 flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400">
        <a href="/" class="hover:text-brand-500 transition-colors flex items-center gap-1"><i class="ph-bold ph-house"></i> Inicio</a>
        <i class="ph-bold ph-caret-right text-slate-300"></i>
        <a href="/canchas" class="hover:text-brand-500 transition-colors">Canchas</a>
        <i class="ph-bold ph-caret-right text-slate-300"></i>
        <a href="/canchas/1" class="hover:text-brand-500 transition-colors truncate">{{ $reserva['cancha'] }}</a>
        <i class="ph-bold ph-caret-right text-slate-300"></i>
        <span class="text-slate-900 dark:text-white font-bold">Confirmar reserva</span>
    </div>
</div>

{{-- PASOS --}}
<div class="max-w-4xl mx-auto px-4 sm:px-6 pt-6 mb-6">
    <div class="flex items-center justify-center gap-2">
        @foreach(['Elegir horario', 'Confirmar', 'Pago', '¡Listo!'] as $i => $paso)
        <div class="flex items-center gap-2">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                    {{ $i === 1 ? 'bg-brand-500 text-white' : ($i < 1 ? 'bg-brand-500/20 text-brand-500' : 'bg-slate-100 dark:bg-white/10 text-slate-400') }}">
                    {{ $i < 1 ? '✓' : $i + 1 }}
                </div>
                <span class="text-xs font-semibold hidden sm:block
                    {{ $i === 1 ? 'text-brand-500' : ($i < 1 ? 'text-brand-400' : 'text-slate-400') }}">
                    {{ $paso }}
                </span>
            </div>
            @if($i < 3)
            <div class="w-8 h-px {{ $i < 1 ? 'bg-brand-500' : 'bg-slate-200 dark:bg-white/10' }}"></div>
            @endif
        </div>
        @endforeach
    </div>
</div>

<main class="max-w-4xl mx-auto px-4 sm:px-6 pb-32 md:pb-10">
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- ============================================================
             FORMULARIO (izquierda)
        ============================================================ --}}
        <div class="lg:col-span-3 space-y-5">

            {{-- Resumen de la reserva --}}
            <div class="glass-card rounded-2xl overflow-hidden">
                <div class="flex gap-4 p-5">
                    <img src="{{ $reserva['imagen'] }}" class="w-20 h-20 rounded-xl object-cover shrink-0" alt="{{ $reserva['cancha'] }}">
                    <div class="flex-1 min-w-0">
                        <span class="text-xs font-bold text-brand-500 uppercase tracking-wider">{{ $reserva['tipo'] }}</span>
                        <h2 class="font-extrabold text-slate-900 dark:text-white text-lg leading-tight mt-0.5">{{ $reserva['cancha'] }}</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-1">
                            <i class="ph-fill ph-map-pin text-brand-500"></i> {{ $reserva['direccion'] }}
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3 px-5 pb-5">
                    <div class="flex items-center gap-2 bg-slate-50 dark:bg-white/5 rounded-xl px-3 py-2">
                        <i class="ph-fill ph-calendar-blank text-brand-500"></i>
                        <span class="text-sm font-semibold text-slate-700 dark:text-white">{{ $reserva['fecha'] }}</span>
                    </div>
                    <div class="flex items-center gap-2 bg-slate-50 dark:bg-white/5 rounded-xl px-3 py-2">
                        <i class="ph-fill ph-clock text-brand-500"></i>
                        <span class="text-sm font-semibold text-slate-700 dark:text-white">{{ $reserva['hora'] }}</span>
                    </div>
                    <div class="flex items-center gap-2 bg-slate-50 dark:bg-white/5 rounded-xl px-3 py-2">
                        <i class="ph-fill ph-timer text-brand-500"></i>
                        <span class="text-sm font-semibold text-slate-700 dark:text-white">{{ $reserva['horas'] }} hora{{ $reserva['horas'] > 1 ? 's' : '' }}</span>
                    </div>
                </div>
            </div>

            {{-- Datos del jugador — pre-llenados si está logueado --}}
            <div class="glass-card rounded-2xl p-5">
                <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-4">
                    <i class="ph-fill ph-user-circle text-brand-500"></i> Tus datos
                </h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Nombre</label>
                        <div class="flex items-center gap-3 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 bg-white dark:bg-white/5 focus-within:border-brand-500 transition-all">
                            <i class="ph-fill ph-user text-slate-400 shrink-0"></i>
                            <input type="text" value="{{ Auth::user()->name ?? '' }}" placeholder="Tu nombre completo"
                                   class="flex-1 bg-transparent text-sm outline-none text-slate-900 dark:text-white placeholder-slate-400" readonly>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Email</label>
                            <div class="flex items-center gap-3 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 bg-white dark:bg-white/5 focus-within:border-brand-500 transition-all">
                                <i class="ph-fill ph-envelope text-slate-400 shrink-0"></i>
                                <input type="email" value="{{ Auth::user()->email ?? '' }}" placeholder="correo@email.com"
                                       class="flex-1 bg-transparent text-sm outline-none text-slate-900 dark:text-white placeholder-slate-400 min-w-0" readonly>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Teléfono</label>
                            <div class="flex items-center gap-3 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 bg-white dark:bg-white/5 focus-within:border-brand-500 transition-all">
                                <i class="ph-fill ph-phone text-slate-400 shrink-0"></i>
                                <input type="tel" placeholder="+51 987..."
                                       class="flex-1 bg-transparent text-sm outline-none text-slate-900 dark:text-white placeholder-slate-400 min-w-0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Método de pago del anticipo --}}
            <div class="glass-card rounded-2xl p-5">
                <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-4">
                    <i class="ph-fill ph-credit-card text-brand-500"></i> Pago del anticipo
                    <span class="ml-auto text-brand-500 font-extrabold">S/ {{ number_format($reserva['anticipo'],2) }}</span>
                </h3>

                {{-- Métodos --}}
                <div class="space-y-2 mb-4">
                    @foreach([
                        ['val' => 'tarjeta', 'icon' => 'ph-credit-card',  'label' => 'Tarjeta de crédito / débito', 'badge' => null],
                        ['val' => 'yape',    'icon' => 'ph-device-mobile', 'label' => 'Yape',                        'badge' => 'Popular'],
                        ['val' => 'plin',    'icon' => 'ph-device-mobile', 'label' => 'Plin',                        'badge' => null],
                    ] as $metodo)
                    <label class="flex items-center gap-3 cursor-pointer p-3 rounded-xl border-2 border-slate-200 dark:border-white/10 hover:border-brand-500/50 transition-all peer-checked:border-brand-500 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-500/5">
                        <input type="radio" name="metodo_pago" value="{{ $metodo['val'] }}"
                               class="peer sr-only" {{ $metodo['val'] === 'yape' ? 'checked' : '' }}>
                        <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-white/10 flex items-center justify-center shrink-0">
                            <i class="ph-fill {{ $metodo['icon'] }} text-slate-500 dark:text-slate-400"></i>
                        </div>
                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex-1">{{ $metodo['label'] }}</span>
                        @if($metodo['badge'])
                        <span class="text-[10px] font-bold bg-brand-500/10 text-brand-600 dark:text-brand-400 px-2 py-0.5 rounded-full">{{ $metodo['badge'] }}</span>
                        @endif
                        <div class="w-4 h-4 rounded-full border-2 border-slate-300 dark:border-white/20 peer-checked:border-brand-500 flex items-center justify-center">
                            <div class="w-2 h-2 rounded-full bg-brand-500 opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                        </div>
                    </label>
                    @endforeach
                </div>

                {{-- Aviso --}}
                <div class="bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 rounded-xl px-4 py-3 flex items-start gap-2">
                    <i class="ph-fill ph-info text-blue-500 shrink-0 mt-0.5"></i>
                    <p class="text-xs text-blue-700 dark:text-blue-400">
                        Solo se cobra <strong>S/ {{ number_format($reserva['anticipo'],2) }}</strong> ahora.
                        El saldo de <strong>S/ {{ number_format($reserva['saldo'],2) }}</strong> se paga en efectivo al llegar al complejo.
                    </p>
                </div>
            </div>

        </div>

        {{-- ============================================================
             RESUMEN (derecha sticky)
        ============================================================ --}}
        <aside class="lg:col-span-2 self-start sticky top-36">
            <div class="glass-card rounded-2xl p-5">
                <h3 class="font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="ph-fill ph-receipt text-brand-500"></i> Resumen
                </h3>

                <div class="space-y-2.5 text-sm mb-4">
                    <div class="flex justify-between text-slate-600 dark:text-slate-400">
                        <span>Precio por hora</span>
                        <span>S/ {{ number_format($reserva['precio'],2) }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600 dark:text-slate-400">
                        <span>Horas</span>
                        <span>× 1</span>
                    </div>
                    <div class="border-t border-slate-100 dark:border-white/10 pt-2.5 flex justify-between font-bold text-slate-900 dark:text-white">
                        <span>Total</span>
                        <span>S/ {{ number_format($reserva['precio'],2) }}</span>
                    </div>
                </div>

                <div class="bg-slate-50 dark:bg-white/5 rounded-xl p-3 space-y-2 mb-5 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Anticipo ahora</span>
                        <span class="font-bold text-brand-500">S/ {{ number_format($reserva['anticipo'],2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Saldo en cancha</span>
                        <span class="font-semibold text-slate-700 dark:text-slate-300">S/ {{ number_format($reserva['saldo'],2) }}</span>
                    </div>
                </div>

                <form id="form-booking" action="/booking/crear" method="POST">
                    @csrf
                    <input type="hidden" name="field_id"    value="{{ $reserva['field_id'] }}">
                    <input type="hidden" name="slot_ids"    value="{{ json_encode($reserva['slot_ids']) }}">
                    <input type="hidden" name="total"       value="{{ $reserva['precio'] }}">
                    <input type="hidden" name="anticipo"    value="{{ $reserva['anticipo'] }}">
                    <input type="hidden" name="metodo"      id="metodo-pago" value="yape">
                    <button type="submit"
                            class="w-full bg-brand-500 hover:bg-brand-600 text-white font-bold py-4 rounded-2xl flex items-center justify-center gap-2 transition-all hover:scale-[1.02] active:scale-95 shadow-lg shadow-brand-500/20 text-base mb-3">
                        <i class="ph-bold ph-lock-simple"></i> Confirmar — S/ {{ number_format($reserva['anticipo'],2) }}
                    </button>
                </form>

                <div class="flex items-center justify-center gap-4 text-[10px] text-slate-400">
                    <span class="flex items-center gap-1"><i class="ph-fill ph-shield-check text-brand-500"></i> Pago seguro</span>
                    <span class="flex items-center gap-1"><i class="ph-fill ph-lock-simple text-brand-500"></i> SSL</span>
                    <span class="flex items-center gap-1"><i class="ph-fill ph-arrow-counter-clockwise text-brand-500"></i> Cancelación</span>
                </div>
            </div>
        </aside>

    </div>
</main>

{{-- BARRA MÓVIL --}}
<div class="lg:hidden fixed bottom-24 left-0 w-full bg-white/80 dark:bg-dark-900/80 backdrop-blur-lg border-t border-slate-200 dark:border-white/10 px-4 py-3 z-[100] shadow-[0_-4px_20px_rgba(0,0,0,0.1)]">
    <div class="flex items-center justify-between mb-2">
        <span class="text-xs text-slate-500 dark:text-slate-400">Anticipo a pagar</span>
        <span class="font-extrabold text-brand-500 text-lg">S/ {{ number_format($reserva['anticipo'],2) }}</span>
    </div>
    <button onclick="document.getElementById('form-booking').submit()"
            class="w-full bg-brand-500 hover:bg-brand-600 text-white font-bold py-3.5 rounded-2xl flex items-center justify-center gap-2 transition-colors shadow-lg shadow-brand-500/20">
        <i class="ph-bold ph-lock-simple"></i> Confirmar y pagar
    </button>
</div>

@push('scripts')
<script>
    // Sincronizar método de pago seleccionado con el form
    document.querySelectorAll('input[name=metodo_pago]').forEach(r => {
        r.addEventListener('change', () => {
            document.getElementById('metodo-pago').value = r.value;
        });
    });
</script>
@endpush

@endsection
