@extends('layouts.app')
@section('title', 'Mis Reservas | FutGo')

@section('content')

@php
// $proxima, $activas, $historial vienen del controlador (routes/web.php)
// Helpers para renderizar datos del modelo Booking
$imgDefault = [
    'futbol5'  => 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?w=600&q=80',
    'futbol7'  => 'https://images.unsplash.com/photo-1551280857-2b9ebf262c62?w=600&q=80',
    'futbol11' => 'https://images.unsplash.com/photo-1529900748604-07564a03e7a6?w=600&q=80',
];
$getImg = fn($b) => $imgDefault[$b->field?->sport_type ?? 'futbol5'] ?? $imgDefault['futbol5'];
$getHora = fn($b) => $b->slots->sortBy('starts_at')->first()?->starts_at->format('H:i')
    . ' – ' . $b->slots->sortBy('starts_at')->last()?->ends_at->format('H:i');
$getFecha = function($b) {
    $slot = $b->slots->sortBy('starts_at')->first();
    if (!$slot) return '–';
    $d = $slot->starts_at->toDateString();
    if ($d === today()->toDateString()) return 'Hoy';
    if ($d === today()->addDay()->toDateString()) return 'Mañana';
    return $slot->starts_at->format('D d M');
};
@endphp

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Mis Reservas</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-0.5">Gestioná tus turnos y accedé a tus QR</p>
        </div>
        <a href="/canchas"
           class="bg-brand-500 hover:bg-brand-600 text-white font-bold px-4 py-2.5 rounded-xl flex items-center gap-2 text-sm transition-all hover:scale-105 shadow-lg shadow-brand-500/20">
            <i class="ph-bold ph-plus"></i>
            <span class="hidden sm:inline">Nueva reserva</span>
        </a>
    </div>

    {{-- ============================================================
         PRÓXIMA RESERVA (destacada)
    ============================================================ --}}
    @if($proxima)
    <div>
        <h2 class="text-xs font-bold text-brand-500 uppercase tracking-widest mb-3 flex items-center gap-2">
            <i class="ph-fill ph-lightning"></i> Próxima reserva
        </h2>
        @php
            $pSlot = $proxima->slots->sortBy('starts_at')->first();
            $pFecha = $pSlot ? ($pSlot->starts_at->isToday() ? 'Hoy' : ($pSlot->starts_at->isTomorrow() ? 'Mañana' : $pSlot->starts_at->format('d M'))) : '–';
        @endphp
        <div class="glass-card rounded-3xl overflow-hidden">
            <div class="flex flex-col md:flex-row">
                <div class="relative h-48 md:h-auto md:w-56 shrink-0 overflow-hidden">
                    <img src="{{ $getImg($proxima) }}" alt="{{ $proxima->field?->venue?->name }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t md:bg-gradient-to-r from-slate-900/80 to-transparent"></div>
                    <div class="absolute top-3 left-3 bg-brand-500 text-white text-xs font-bold px-3 py-1.5 rounded-full flex items-center gap-1.5 shadow-lg">
                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse inline-block"></span>
                        {{ strtoupper($pFecha) }}
                    </div>
                </div>
                <div class="flex-1 p-6 flex flex-col sm:flex-row gap-6">
                    <div class="flex-1 space-y-3">
                        <div>
                            <span class="text-xs font-bold text-brand-500 uppercase tracking-wider">{{ $proxima->field?->sport_type }}</span>
                            <h3 class="text-xl font-extrabold text-slate-900 dark:text-white mt-0.5">{{ $proxima->field?->venue?->name }}</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-1">
                                <i class="ph-fill ph-map-pin text-brand-500 shrink-0"></i>
                                {{ $proxima->field?->venue?->address }}, {{ $proxima->field?->venue?->city?->name }}
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <div class="flex items-center gap-2 bg-slate-100 dark:bg-white/5 rounded-xl px-3 py-2">
                                <i class="ph-fill ph-calendar-blank text-brand-500"></i>
                                <span class="text-sm font-semibold text-slate-700 dark:text-white">{{ $pFecha }}</span>
                            </div>
                            <div class="flex items-center gap-2 bg-slate-100 dark:bg-white/5 rounded-xl px-3 py-2">
                                <i class="ph-fill ph-clock text-brand-500"></i>
                                <span class="text-sm font-semibold text-slate-700 dark:text-white">{{ $getHora($proxima) }}</span>
                            </div>
                        </div>
                        <div class="pt-2 border-t border-slate-100 dark:border-white/10 flex flex-wrap gap-4 text-sm">
                            <div><span class="text-slate-400 text-xs block">Total</span><span class="font-bold text-slate-900 dark:text-white">S/ {{ number_format($proxima->total_price, 2) }}</span></div>
                            <div><span class="text-slate-400 text-xs block">Anticipo pagado</span><span class="font-bold text-brand-500">S/ {{ number_format($proxima->deposit_amount, 2) }}</span></div>
                            <div><span class="text-slate-400 text-xs block">Saldo en cancha</span><span class="font-bold text-slate-700 dark:text-slate-300">S/ {{ number_format($proxima->balance_due, 2) }}</span></div>
                        </div>
                        <div class="text-xs bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 text-amber-700 dark:text-amber-400 rounded-xl px-3 py-2 flex items-center gap-2">
                            <i class="ph-bold ph-info shrink-0"></i> Presentá el QR al Staff al llegar. Tenés 15 min de tolerancia.
                        </div>
                    </div>
                    <div class="flex flex-col items-center gap-3 shrink-0">
                        <div class="bg-white dark:bg-dark-900 p-3 rounded-2xl shadow-lg border border-slate-100 dark:border-white/10">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ $proxima->qr_token }}&bgcolor=ffffff&color=22c55e&qzone=2" class="w-28 h-28 sm:w-36 sm:h-36 dark:hidden" alt="QR">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ $proxima->qr_token }}&bgcolor=0f172a&color=22c55e&qzone=2" class="w-28 h-28 sm:w-36 sm:h-36 hidden dark:block" alt="QR">
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $proxima->qr_token }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="glass-card rounded-2xl p-8 text-center">
        <i class="ph-fill ph-calendar-x text-slate-300 dark:text-slate-600 text-5xl mb-3"></i>
        <p class="font-bold text-slate-500 dark:text-slate-400">No tenés reservas próximas</p>
        <a href="/canchas" class="mt-4 inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white font-bold px-6 py-3 rounded-xl transition-colors text-sm">
            <i class="ph-bold ph-plus"></i> Reservar cancha
        </a>
    </div>
    @endif

    {{-- ============================================================
         RESERVAS CONFIRMADAS (próximas)
    ============================================================ --}}
    @if($activas->count() > 0)
    <div>
        <h2 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
            <i class="ph-fill ph-calendar-check text-brand-500"></i> Próximas confirmadas
        </h2>
        <div class="space-y-3">
            @foreach($activas as $reserva)
            <div class="glass-card rounded-2xl overflow-hidden flex flex-col sm:flex-row hover:border-brand-500/30 transition-all group" style="border: 1px solid transparent;">
                <div class="relative h-32 sm:h-auto sm:w-36 shrink-0 overflow-hidden">
                    <img src="{{ $getImg($reserva) }}" alt="{{ $reserva->field?->venue?->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                <div class="flex-1 p-4 flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] font-bold text-brand-500 uppercase tracking-wider mb-0.5">{{ $reserva->field?->sport_type }}</p>
                        <h3 class="font-bold text-slate-900 dark:text-white truncate">{{ $reserva->field?->venue?->name }}</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-2 mt-1">
                            <span class="flex items-center gap-1"><i class="ph-fill ph-calendar-blank"></i> {{ $getFecha($reserva) }}</span>
                            <span class="text-slate-300 dark:text-slate-600">·</span>
                            <span class="flex items-center gap-1"><i class="ph-fill ph-clock"></i> {{ $getHora($reserva) }}</span>
                        </p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <div class="text-right hidden sm:block">
                            <p class="text-xs text-slate-400">Total</p>
                            <p class="font-bold text-slate-900 dark:text-white">S/ {{ number_format($reserva->total_price, 2) }}</p>
                        </div>
                        <button onclick="abrirQR('{{ $reserva->qr_token }}')"
                                class="flex flex-col items-center gap-1 p-2 rounded-xl bg-slate-100 dark:bg-white/5 hover:bg-brand-500/10 border border-slate-200 dark:border-white/10 hover:border-brand-500/30 transition-all group/qr">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ $reserva->qr_token }}&bgcolor=ffffff&color=22c55e&qzone=1" class="w-10 h-10 rounded-md dark:hidden" alt="QR">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ $reserva->qr_token }}&bgcolor=0f172a&color=22c55e&qzone=1" class="w-10 h-10 rounded-md hidden dark:block" alt="QR">
                            <span class="text-[9px] font-bold text-slate-400 group-hover/qr:text-brand-500 transition-colors">VER QR</span>
                        </button>
                        <span class="bg-brand-500/10 text-brand-600 dark:text-brand-400 text-[10px] font-bold px-2.5 py-1 rounded-full border border-brand-500/20">Confirmada</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ============================================================
         HISTORIAL
    ============================================================ --}}
    <div>
        <h2 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
            <i class="ph-fill ph-clock-counter-clockwise text-slate-400"></i> Historial
        </h2>

        @if($historial->count() > 0)
        <div class="glass-card rounded-3xl overflow-hidden divide-y divide-slate-100 dark:divide-white/5">
            @foreach($historial as $item)
            <div class="flex items-center gap-4 px-5 py-4 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                <div class="w-12 h-12 rounded-xl overflow-hidden shrink-0">
                    <img src="{{ $getImg($item) }}" alt="{{ $item->field?->venue?->name }}"
                         class="w-full h-full object-cover {{ $item->status === 'cancelled' ? 'grayscale' : '' }}">
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 dark:text-white truncate">{{ $item->field?->venue?->name }}</p>
                    <p class="text-xs text-slate-400 flex items-center gap-2 mt-0.5">
                        <span>{{ $getFecha($item) }}</span>
                        <span class="text-slate-300 dark:text-slate-600">·</span>
                        <span>{{ $getHora($item) }}</span>
                        <span class="text-slate-300 dark:text-slate-600">·</span>
                        <span>{{ $item->field?->sport_type }}</span>
                    </p>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-sm font-bold text-slate-800 dark:text-white mb-1">
                        {{ $item->status === 'cancelled' ? '–' : 'S/ ' . number_format($item->total_price, 2) }}
                    </p>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full
                        @if($item->status === 'completed') bg-slate-100 dark:bg-white/10 text-slate-500 dark:text-slate-400
                        @elseif($item->status === 'no_show') bg-amber-100 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400
                        @elseif($item->status === 'cancelled') bg-red-100 dark:bg-red-500/10 text-red-500
                        @endif">
                        {{ match($item->status) { 'completed'=>'Completada','no_show'=>'No-show','cancelled'=>'Cancelada',default=>ucfirst($item->status) } }}
                    </span>
                </div>
                @if($item->status === 'completed')
                <a href="/canchas/{{ $item->field?->venue_id }}" class="hidden sm:flex items-center gap-1 text-xs font-semibold text-brand-500 hover:text-brand-600 transition-colors shrink-0 ml-2">
                    <i class="ph-bold ph-arrow-clockwise"></i> Repetir
                </a>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <div class="glass-card rounded-2xl p-6 text-center text-slate-400 dark:text-slate-500 text-sm">
            Aún no tenés reservas en el historial
        </div>
        @endif
    </div>

</div>

{{-- MODAL QR --}}
<div id="qr-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm" onclick="cerrarQR()"></div>
    <div class="relative glass-card rounded-3xl p-8 max-w-xs w-full text-center shadow-2xl">
        <button onclick="cerrarQR()" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 dark:bg-white/10 flex items-center justify-center text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors">
            <i class="ph-bold ph-x"></i>
        </button>
        <p class="text-xs font-bold text-brand-500 uppercase tracking-widest mb-1">Tu QR de acceso</p>
        <p id="qr-modal-id" class="text-xs text-slate-400 mb-4"></p>
        <div class="bg-white dark:bg-dark-900 p-4 rounded-2xl inline-block shadow-lg border border-slate-100 dark:border-white/10 mb-4">
            <img id="qr-modal-img-light" src="" alt="QR" class="w-48 h-48 dark:hidden">
            <img id="qr-modal-img-dark" src="" alt="QR" class="w-48 h-48 hidden dark:block">
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Mostrá este código al Staff al ingresar</p>
        <button onclick="descargarQRModal()" class="w-full flex items-center justify-center gap-2 py-3 rounded-2xl bg-brand-500 hover:bg-brand-600 text-white font-bold text-sm transition-colors">
            <i class="ph-bold ph-download-simple"></i> Descargar QR
        </button>
    </div>
</div>

@push('scripts')
<script>
    function abrirQR(token) {
        document.getElementById('qr-modal-id').textContent = token;
        document.getElementById('qr-modal-img-light').src = `https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=${token}&bgcolor=ffffff&color=22c55e&qzone=2`;
        document.getElementById('qr-modal-img-dark').src  = `https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=${token}&bgcolor=0f172a&color=22c55e&qzone=2`;
        document.getElementById('qr-modal').classList.remove('hidden');
    }
    
    function cerrarQR() {
        document.getElementById('qr-modal').classList.add('hidden');
    }

    function descargarImagen(url, filename) {
        fetch(url)
            .then(response => response.blob())
            .then(blob => {
                const blobUrl = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = blobUrl;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(blobUrl);
                document.body.removeChild(a);
            })
            .catch(() => alert('Error al descargar el QR. Intentá nuevamente.'));
    }

    function descargarQR(id, qrData) {
        const isDark = document.documentElement.classList.contains('dark');
        const bg = isDark ? '0f172a' : 'ffffff';
        const url = `https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=${qrData}&bgcolor=${bg}&color=22c55e&qzone=2`;
        descargarImagen(url, `QR-${id}.png`);
    }

    function descargarQRModal() {
        const id = document.getElementById('qr-modal-id').textContent;
        const isDark = document.documentElement.classList.contains('dark');
        const img = document.getElementById(isDark ? 'qr-modal-img-dark' : 'qr-modal-img-light');
        descargarImagen(img.src, `QR-${id}.png`);
    }
</script>
@endpush

@endsection
