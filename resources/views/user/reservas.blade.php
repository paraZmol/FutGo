@extends('layouts.app')
@section('title', 'Mis Reservas | FutGo')

@section('content')

@php
$proxima = [
    'id'         => 'RES-2024-001',
    'cancha'     => 'Complejo Deportivo El 10',
    'tipo'       => 'Fútbol 5',
    'direccion'  => 'Av. Los Incas 342, Wanchaq, Cusco',
    'fecha'      => 'Hoy',
    'hora'       => '20:00 – 21:00',
    'fecha_raw'  => date('d M Y'),
    'precio'     => 80.00,
    'anticipo'   => 30.00,
    'saldo'      => 50.00,
    'imagen'     => 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?w=800&q=80',
    'qr'         => 'FUTBO-RES-2024-001',
];

$activas = [
    [
        'id'        => 'RES-2024-002',
        'cancha'    => 'El Rey del Gras',
        'tipo'      => 'Fútbol 5',
        'direccion' => 'San Sebastián, Cusco',
        'fecha'     => 'Mañana',
        'hora'      => '18:00 – 19:00',
        'precio'    => 90.00,
        'anticipo'  => 30.00,
        'saldo'     => 60.00,
        'estado'    => 'confirmada',
        'imagen'    => 'https://images.unsplash.com/photo-1551280857-2b9ebf262c62?w=600&q=80',
        'qr'        => 'FUTBO-RES-2024-002',
    ],
    [
        'id'        => 'RES-2024-003',
        'cancha'    => 'SportCenter Norte',
        'tipo'      => 'Fútbol 7',
        'direccion' => 'Ttio, Cusco',
        'fecha'     => 'Sáb 10 May',
        'hora'      => '10:00 – 11:00',
        'precio'    => 85.00,
        'anticipo'  => 30.00,
        'saldo'     => 55.00,
        'estado'    => 'confirmada',
        'imagen'    => 'https://images.unsplash.com/photo-1529900748604-07564a03e7a6?w=600&q=80',
        'qr'        => 'FUTBO-RES-2024-003',
    ],
];

$historial = [
    [
        'id'       => 'RES-2024-H01',
        'cancha'   => 'La Bombonera FC',
        'tipo'     => 'Fútbol 5',
        'fecha'    => 'Jue 25 Abr',
        'hora'     => '20:00 – 21:00',
        'precio'   => 70.00,
        'estado'   => 'completada',
        'imagen'   => 'https://images.unsplash.com/photo-1524015368236-bbf6f72545b6?w=600&q=80',
    ],
    [
        'id'       => 'RES-2024-H02',
        'cancha'   => 'Canchas La Losa',
        'tipo'     => 'Fútbol 7',
        'fecha'    => 'Dom 21 Abr',
        'hora'     => '16:00 – 17:00',
        'precio'   => 60.00,
        'estado'   => 'completada',
        'imagen'   => 'https://images.unsplash.com/photo-1518605368461-1ee7e53f191b?w=600&q=80',
    ],
    [
        'id'       => 'RES-2024-H03',
        'cancha'   => 'Complejo Olimpo',
        'tipo'     => 'Fútbol 5',
        'fecha'    => 'Vie 19 Abr',
        'hora'     => '19:00 – 20:00',
        'precio'   => 55.00,
        'estado'   => 'completada',
        'imagen'   => 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=600&q=80',
    ],
    [
        'id'       => 'RES-2024-H04',
        'cancha'   => 'El Rey del Gras',
        'tipo'     => 'Fútbol 5',
        'fecha'    => 'Mar 15 Abr',
        'hora'     => '20:00 – 21:00',
        'precio'   => 90.00,
        'estado'   => 'cancelada',
        'imagen'   => 'https://images.unsplash.com/photo-1551280857-2b9ebf262c62?w=600&q=80',
    ],
    [
        'id'       => 'RES-2024-H05',
        'cancha'   => 'Complejo Deportivo El 10',
        'tipo'     => 'Fútbol 5',
        'fecha'    => 'Sáb 13 Abr',
        'hora'     => '08:00 – 09:00',
        'precio'   => 80.00,
        'estado'   => 'completada',
        'imagen'   => 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?w=600&q=80',
    ],
];
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
    <div>
        <h2 class="text-xs font-bold text-brand-500 uppercase tracking-widest mb-3 flex items-center gap-2">
            <i class="ph-fill ph-lightning"></i> Próxima reserva
        </h2>

        <div class="glass-card rounded-3xl overflow-hidden">
            <div class="flex flex-col md:flex-row">

                {{-- Imagen --}}
                <div class="relative h-48 md:h-auto md:w-56 shrink-0 overflow-hidden">
                    <img src="{{ $proxima['imagen'] }}" alt="{{ $proxima['cancha'] }}"
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t md:bg-gradient-to-r from-slate-900/80 to-transparent"></div>
                    {{-- Badge HOY --}}
                    <div class="absolute top-3 left-3 bg-brand-500 text-white text-xs font-bold px-3 py-1.5 rounded-full flex items-center gap-1.5 shadow-lg">
                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse inline-block"></span>
                        HOY
                    </div>
                </div>

                {{-- Info + QR --}}
                <div class="flex-1 p-6 flex flex-col sm:flex-row gap-6">

                    {{-- Datos --}}
                    <div class="flex-1 space-y-3">
                        <div>
                            <span class="text-xs font-bold text-brand-500 uppercase tracking-wider">{{ $proxima['tipo'] }}</span>
                            <h3 class="text-xl font-extrabold text-slate-900 dark:text-white mt-0.5">{{ $proxima['cancha'] }}</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-1">
                                <i class="ph-fill ph-map-pin text-brand-500 shrink-0"></i>
                                {{ $proxima['direccion'] }}
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <div class="flex items-center gap-2 bg-slate-100 dark:bg-white/5 rounded-xl px-3 py-2">
                                <i class="ph-fill ph-calendar-blank text-brand-500"></i>
                                <span class="text-sm font-semibold text-slate-700 dark:text-white">{{ $proxima['fecha'] }}</span>
                            </div>
                            <div class="flex items-center gap-2 bg-slate-100 dark:bg-white/5 rounded-xl px-3 py-2">
                                <i class="ph-fill ph-clock text-brand-500"></i>
                                <span class="text-sm font-semibold text-slate-700 dark:text-white">{{ $proxima['hora'] }}</span>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-100 dark:border-white/10 flex flex-wrap gap-4 text-sm">
                            <div>
                                <span class="text-slate-400 text-xs block">Total</span>
                                <span class="font-bold text-slate-900 dark:text-white">S/ {{ number_format($proxima['precio'], 2) }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 text-xs block">Anticipo pagado</span>
                                <span class="font-bold text-brand-500">S/ {{ number_format($proxima['anticipo'], 2) }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 text-xs block">Saldo en cancha</span>
                                <span class="font-bold text-slate-700 dark:text-slate-300">S/ {{ number_format($proxima['saldo'], 2) }}</span>
                            </div>
                        </div>

                        <div class="text-xs text-slate-400 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 text-amber-700 dark:text-amber-400 rounded-xl px-3 py-2 flex items-center gap-2">
                            <i class="ph-bold ph-info shrink-0"></i>
                            Presentá el QR al Staff al llegar.
                        </div>
                    </div>

                    {{-- QR --}}
                    <div class="flex flex-col items-center gap-3 shrink-0">
                        <div class="bg-white dark:bg-dark-900 p-3 rounded-2xl shadow-lg border border-slate-100 dark:border-white/10">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ $proxima['qr'] }}&bgcolor=ffffff&color=22c55e&qzone=2" alt="QR Reserva"
                                 class="w-28 h-28 sm:w-36 sm:h-36 dark:hidden">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ $proxima['qr'] }}&bgcolor=0f172a&color=22c55e&qzone=2" alt="QR Reserva"
                                 class="w-28 h-28 sm:w-36 sm:h-36 hidden dark:block">
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $proxima['id'] }}</span>
                        <button onclick="descargarQR('{{ $proxima['id'] }}', '{{ $proxima['qr'] }}')" class="flex items-center gap-1.5 text-xs font-semibold text-brand-500 hover:text-brand-600 transition-colors">
                            <i class="ph-bold ph-download-simple"></i> Descargar QR
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         RESERVAS CONFIRMADAS (próximas)
    ============================================================ --}}
    @if(count($activas) > 0)
    <div>
        <h2 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
            <i class="ph-fill ph-calendar-check text-brand-500"></i> Próximas confirmadas
        </h2>

        <div class="space-y-3">
            @foreach($activas as $reserva)
            <div class="glass-card rounded-2xl overflow-hidden flex flex-col sm:flex-row hover:border-brand-500/30 transition-all group"
                 style="border: 1px solid transparent;">

                {{-- Imagen pequeña --}}
                <div class="relative h-32 sm:h-auto sm:w-36 shrink-0 overflow-hidden">
                    <img src="{{ $reserva['imagen'] }}" alt="{{ $reserva['cancha'] }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>

                {{-- Info --}}
                <div class="flex-1 p-4 flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] font-bold text-brand-500 uppercase tracking-wider mb-0.5">{{ $reserva['tipo'] }}</p>
                        <h3 class="font-bold text-slate-900 dark:text-white truncate">{{ $reserva['cancha'] }}</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-2 mt-1">
                            <span class="flex items-center gap-1">
                                <i class="ph-fill ph-calendar-blank"></i> {{ $reserva['fecha'] }}
                            </span>
                            <span class="text-slate-300 dark:text-slate-600">·</span>
                            <span class="flex items-center gap-1">
                                <i class="ph-fill ph-clock"></i> {{ $reserva['hora'] }}
                            </span>
                        </p>
                    </div>

                    <div class="flex items-center gap-3 shrink-0">
                        <div class="text-right hidden sm:block">
                            <p class="text-xs text-slate-400">Total</p>
                            <p class="font-bold text-slate-900 dark:text-white">S/ {{ number_format($reserva['precio'], 2) }}</p>
                        </div>
                        {{-- Mini QR --}}
                        <button onclick="abrirQR('{{ $reserva['id'] }}', '{{ $reserva['qr'] }}')"
                                class="flex flex-col items-center gap-1 p-2 rounded-xl bg-slate-100 dark:bg-white/5 hover:bg-brand-500/10 border border-slate-200 dark:border-white/10 hover:border-brand-500/30 transition-all group/qr">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ $reserva['qr'] }}&bgcolor=ffffff&color=22c55e&qzone=2" class="w-10 h-10 rounded-md dark:hidden" alt="QR">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ $reserva['qr'] }}&bgcolor=0f172a&color=22c55e&qzone=2" class="w-10 h-10 rounded-md hidden dark:block" alt="QR">
                            <span class="text-[9px] font-bold text-slate-400 group-hover/qr:text-brand-500 transition-colors">VER QR</span>
                        </button>
                        <span class="bg-brand-500/10 text-brand-600 dark:text-brand-400 text-[10px] font-bold px-2.5 py-1 rounded-full border border-brand-500/20">
                            Confirmada
                        </span>
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

        <div class="glass-card rounded-3xl overflow-hidden divide-y divide-slate-100 dark:divide-white/5">
            @foreach($historial as $item)
            <div class="flex items-center gap-4 px-5 py-4 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">

                {{-- Imagen circular --}}
                <div class="w-12 h-12 rounded-xl overflow-hidden shrink-0">
                    <img src="{{ $item['imagen'] }}" alt="{{ $item['cancha'] }}"
                         class="w-full h-full object-cover {{ $item['estado'] === 'cancelada' ? 'grayscale' : '' }}">
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 dark:text-white truncate">{{ $item['cancha'] }}</p>
                    <p class="text-xs text-slate-400 flex items-center gap-2 mt-0.5">
                        <span>{{ $item['fecha'] }}</span>
                        <span class="text-slate-300 dark:text-slate-600">·</span>
                        <span>{{ $item['hora'] }}</span>
                        <span class="text-slate-300 dark:text-slate-600">·</span>
                        <span>{{ $item['tipo'] }}</span>
                    </p>
                </div>

                {{-- Estado + precio --}}
                <div class="text-right shrink-0">
                    <p class="text-sm font-bold text-slate-800 dark:text-white mb-1">
                        {{ $item['estado'] === 'cancelada' ? '–' : 'S/ ' . number_format($item['precio'], 2) }}
                    </p>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full
                        @if($item['estado'] === 'completada') bg-slate-100 dark:bg-white/10 text-slate-500 dark:text-slate-400
                        @elseif($item['estado'] === 'cancelada') bg-red-100 dark:bg-red-500/10 text-red-500
                        @endif">
                        {{ ucfirst($item['estado']) }}
                    </span>
                </div>

                {{-- Repetir --}}
                @if($item['estado'] === 'completada')
                <a href="/canchas" class="hidden sm:flex items-center gap-1 text-xs font-semibold text-brand-500 hover:text-brand-600 transition-colors shrink-0 ml-2">
                    <i class="ph-bold ph-arrow-clockwise"></i> Repetir
                </a>
                @endif
            </div>
            @endforeach
        </div>
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
    function abrirQR(id, qrData) {
        document.getElementById('qr-modal-id').textContent = id;
        document.getElementById('qr-modal-img-light').src = `https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=${qrData}&bgcolor=ffffff&color=22c55e&qzone=2`;
        document.getElementById('qr-modal-img-dark').src = `https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=${qrData}&bgcolor=0f172a&color=22c55e&qzone=2`;
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
