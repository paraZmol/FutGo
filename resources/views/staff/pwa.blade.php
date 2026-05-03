<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#0f172a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>FutGo Staff</title>

    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] },
                    colors: {
                        brand: { 500: '#22c55e', 600: '#16a34a' },
                        dark:  { 800: '#1e293b', 900: '#0f172a', 950: '#020617' }
                    }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <style>
        body { font-family: 'Outfit', sans-serif; overscroll-behavior: none; }
        .tab-active  { color: #22c55e; }
        .tab-inactive { color: #64748b; }
        .card-base { transition: all 0.3s; }
        .pulse-ring { animation: pulseRing 1.5s ease-out infinite; }
        @keyframes pulseRing {
            0%   { transform: scale(1);   opacity: .8; }
            100% { transform: scale(1.6); opacity: 0; }
        }

        /* Custom Scrollbar for Staff PWA */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(100, 116, 139, 0.2); border-radius: 10px; }
        .dark ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); }
        
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none;  scrollbar-width: none; }
    </style>
</head>

@php
// Todos los datos vienen del controlador (routes/web.php)
$proximas = $proximas ?? collect();
$user     = $user     ?? Auth::user();
$venue    = $venue    ?? null;

// Turno activo del staff
$turnoActivo = $user
    ? App\Models\ShiftLog::where('user_id', $user->id)
        ->whereNull('closed_at')
        ->with('movements')
        ->latest('opened_at')
        ->first()
    : null;

// Stats del turno activo
$statsCheckins   = $turnoActivo ? $turnoActivo->movements->where('type','checkin')->count()    : 0;
$statsPresencial = $turnoActivo ? $turnoActivo->movements->where('type','walkin')->count()     : 0;
$statsNoshow     = $turnoActivo ? $turnoActivo->movements->where('type','noshow_retention')->count() : 0;
$statsEfectivo   = $turnoActivo ? (float)$turnoActivo->movements->sum('amount')               : 0;
$turnoInicio     = $turnoActivo ? $turnoActivo->opened_at->format('H:i')                      : '--:--';
@endphp

<body class="bg-slate-50 dark:bg-dark-950 text-slate-900 dark:text-slate-100 min-h-screen flex flex-col max-w-sm mx-auto relative transition-colors duration-300">

    {{-- ============================================================
         TOPBAR
    ============================================================ --}}
    <header class="sticky top-0 z-40 px-4 pt-4 pb-3 bg-slate-50/80 dark:bg-dark-950/80 backdrop-blur-md">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-500 to-emerald-600 flex items-center justify-center shadow-lg">
                    <i class="ph-bold ph-soccer-ball text-white text-lg"></i>
                </div>
                <div>
                    <p class="font-bold text-slate-900 dark:text-white text-sm leading-none">FutGo <span class="text-brand-500">Staff</span></p>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 leading-none mt-0.5">{{ $venue?->name ?? 'Sin complejo asignado' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="toggleTheme()" class="w-9 h-9 rounded-xl bg-white dark:bg-dark-800 border border-slate-200 dark:border-white/5 flex items-center justify-center text-slate-500 dark:text-slate-400 transition-colors">
                    <i class="ph-bold ph-moon block dark:hidden"></i>
                    <i class="ph-bold ph-sun hidden dark:block"></i>
                </button>
                {{-- Indicador turno activo --}}
                <div class="flex items-center gap-1.5 bg-brand-500/10 border border-brand-500/20 rounded-full px-3 py-1.5">
                    <span class="relative flex h-2 w-2">
                        <span class="pulse-ring absolute inline-flex h-full w-full rounded-full bg-brand-500 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-brand-500"></span>
                    </span>
                    <span class="text-brand-600 dark:text-brand-400 text-[10px] font-bold uppercase tracking-wider">Activo</span>
                </div>
            </div>
        </div>
    </header>

    {{-- ============================================================
         CONTENIDO (tabs)
    ============================================================ --}}
    <main class="flex-1 overflow-y-auto px-4 pb-28 space-y-4" id="main-content">

        {{-- === TAB: INICIO === --}}
        <div id="tab-inicio" class="space-y-4">

            {{-- Bienvenida + hora --}}
            <div class="bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-white/5 shadow-sm dark:shadow-none backdrop-blur-md rounded-2xl p-4 transition-all duration-300">
                <div>
                    <p class="text-slate-400 text-xs">Hola,</p>
                    <p class="text-slate-900 dark:text-white font-extrabold text-xl leading-tight">{{ $user?->name ?? 'Staff' }}</p>
                    <p class="text-slate-400 text-xs mt-1 flex items-center gap-1">
                        <i class="ph-fill ph-clock text-brand-500"></i>
                        Turno desde las {{ $turnoInicio }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-black text-slate-900 dark:text-white" id="reloj">{{ now()->format('H:i') }}</p>
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest" id="fecha-hoy"></p>
                </div>
            </div>

            {{-- Stats turno --}}
            <div class="grid grid-cols-2 gap-3">
                @foreach([
                    ['label' => 'Ingresos',     'val' => $statsCheckins, 'icon' => 'ph-check-circle', 'color' => 'text-brand-500',  'bg' => 'bg-brand-500/10'],
                    ['label' => 'Presenciales',  'val' => $statsPresencial,     'icon' => 'ph-user-plus',    'color' => 'text-blue-400',   'bg' => 'bg-blue-500/10'],
                    ['label' => 'Inasistencias', 'val' => $statsNoshow,      'icon' => 'ph-user-minus',   'color' => 'text-amber-400',  'bg' => 'bg-amber-500/10'],
                    ['label' => 'Efectivo',      'val' => 'S/'.number_format($statsEfectivo,0), 'icon' => 'ph-money', 'color' => 'text-emerald-400', 'bg' => 'bg-emerald-500/10'],
                ] as $s)
                <div class="card-dark rounded-2xl p-4">
                    <div class="w-9 h-9 rounded-xl {{ $s['bg'] }} flex items-center justify-center mb-2">
                        <i class="ph-fill {{ $s['icon'] }} {{ $s['color'] }} text-xl"></i>
                    </div>
                    <p class="text-xl font-extrabold text-slate-900 dark:text-white">{{ $s['val'] }}</p>
                    <p class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">{{ $s['label'] }}</p>
                </div>
                @endforeach
            </div>

            {{-- Acciones principales --}}
            <div class="grid grid-cols-1 gap-3">
                <button onclick="irTab('qr')"
                        class="w-full flex items-center justify-center gap-3 py-4 rounded-2xl font-extrabold text-lg transition-all active:scale-95 shadow-lg bg-gradient-to-r from-brand-500 to-emerald-600 text-white shadow-brand-500/20 hover:scale-[1.02]">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                        <i class="ph-bold ph-qr-code text-2xl text-white"></i>
                    </div>
                    <span class="flex-1 text-left ml-2">Escanear QR</span>
                    <i class="ph-bold ph-caret-right text-white/50 pr-2"></i>
                </button>
                <button onclick="irTab('walkin')"
                        class="w-full flex items-center justify-center gap-3 py-4 rounded-2xl font-extrabold text-lg transition-all active:scale-95 shadow-lg bg-slate-800 border border-white/5 text-white hover:bg-slate-700">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/20 flex items-center justify-center text-blue-400">
                        <i class="ph-bold ph-user-plus text-2xl"></i>
                    </div>
                    <span class="flex-1 text-left ml-2">Registrar Presencial</span>
                    <i class="ph-bold ph-caret-right text-white/30 pr-2"></i>
                </button>
            </div>

            {{-- Próximas reservas --}}
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                    <i class="ph-fill ph-clock text-brand-500"></i> Próximas reservas
                </p>
                <div class="space-y-2">
                    @forelse($proximas as $r)
                    @php
                        $slot = $r->slots->sortBy('starts_at')->first();
                        $hora = $slot?->starts_at->format('H:i') ?? '--:--';
                    @endphp
                    <div class="bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-white/5 shadow-sm dark:shadow-none backdrop-blur-md rounded-2xl p-4 transition-all duration-300 flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-dark-950 flex flex-col items-center justify-center shrink-0 border border-white/5">
                            <span class="text-brand-500 font-extrabold text-sm leading-none">{{ $hora }}</span>
                            <span class="text-slate-500 text-[9px] uppercase tracking-wide mt-0.5">{{ $r->field?->name }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $r->user?->name ?? 'Sin nombre' }}</p>
                            <p class="text-xs text-slate-400">Saldo a cobrar:
                                <span class="text-brand-400 font-bold">S/ {{ number_format($r->balance_due, 2) }}</span>
                            </p>
                        </div>
                        <button onclick="simularCheckin(this, '{{ $r->qr_token }}')"
                                class="w-10 h-10 rounded-xl bg-brand-500/10 border border-brand-500/20 text-brand-400 hover:bg-brand-500 hover:text-white transition-colors flex items-center justify-center shrink-0">
                            <i class="ph-bold ph-check-fat text-lg"></i>
                        </button>
                    </div>
                    @empty
                    <div class="text-center py-6 text-slate-500 dark:text-slate-400 text-sm">
                        <i class="ph-fill ph-calendar-x text-3xl mb-2 block"></i>
                        No hay reservas próximas
                    </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- === TAB: QR SCANNER === --}}
        <div id="tab-qr" class="hidden space-y-4">

            <div class="text-center pt-2">
                <h2 class="text-xl font-extrabold text-slate-900 dark:text-white mb-1">Escanear QR</h2>
                <p class="text-slate-400 text-sm">Apuntá la cámara al QR del cliente</p>
            </div>

            {{-- Simulación de cámara --}}
            <div class="relative rounded-3xl overflow-hidden bg-dark-950 border border-white/10 aspect-square">
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-56 h-56 relative">
                        {{-- Esquinas del visor --}}
                        <div class="absolute top-0 left-0 w-10 h-10 border-t-4 border-l-4 border-brand-500 rounded-tl-lg"></div>
                        <div class="absolute top-0 right-0 w-10 h-10 border-t-4 border-r-4 border-brand-500 rounded-tr-lg"></div>
                        <div class="absolute bottom-0 left-0 w-10 h-10 border-b-4 border-l-4 border-brand-500 rounded-bl-lg"></div>
                        <div class="absolute bottom-0 right-0 w-10 h-10 border-b-4 border-r-4 border-brand-500 rounded-br-lg"></div>
                        {{-- Línea de escaneo animada --}}
                        <div class="absolute inset-x-2 h-0.5 bg-brand-500 opacity-80 shadow-lg shadow-brand-500/50"
                             style="animation: scan 2s ease-in-out infinite; top: 50%;">
                        </div>
                        {{-- Ícono central --}}
                        <div class="absolute inset-0 flex items-center justify-center">
                            <i class="ph-bold ph-qr-code text-6xl text-white/10"></i>
                        </div>
                    </div>
                </div>
                {{-- Overlay oscuro en bordes --}}
                <div class="absolute inset-0"
                     style="background: radial-gradient(circle at center, transparent 35%, rgba(15,23,42,0.85) 70%)">
                </div>
            </div>

            <style>
                @keyframes scan {
                    0%, 100% { top: 10%; }
                    50%       { top: 90%; }
                }
            </style>

            {{-- Botón simular lectura --}}
            <button onclick="simularQR()"
                    class="btn-action bg-brand-500 hover:bg-brand-600 text-white shadow-xl shadow-brand-500/30">
                <i class="ph-bold ph-qr-code text-2xl"></i>
                Simular lectura QR
            </button>

            {{-- Resultado QR (oculto hasta simular) --}}
            <div id="qr-resultado" class="hidden">
                <div class="card-dark rounded-2xl p-4 border border-brand-500/30">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-brand-500/20 flex items-center justify-center">
                            <i class="ph-fill ph-check-circle text-brand-500 text-2xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900 dark:text-white text-sm">QR Válido</p>
                            <p class="text-xs text-brand-400">FUTBO-R005</p>
                        </div>
                        <span class="ml-auto text-[10px] font-bold bg-brand-500/10 text-brand-400 px-2 py-1 rounded-full border border-brand-500/20">
                            Confirmada
                        </span>
                    </div>
                    <div class="space-y-1.5 text-sm border-t border-white/5 pt-3">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Cliente</span>
                            <span class="font-semibold text-slate-900 dark:text-white">Pedro Huanca</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Cancha</span>
                            <span class="font-semibold text-slate-900 dark:text-white">Cancha 1 · Fútbol 5</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Horario</span>
                            <span class="font-semibold text-slate-900 dark:text-white">20:00 – 21:00</span>
                        </div>
                        <div class="flex justify-between border-t border-white/5 pt-2 mt-2">
                            <span class="text-slate-400">Saldo a cobrar</span>
                            <span class="font-extrabold text-brand-400 text-lg">S/ 60.00</span>
                        </div>
                    </div>
                    <button onclick="confirmarCheckin()"
                            class="btn-action mt-3 bg-brand-500 hover:bg-brand-600 text-white text-base py-3 shadow-lg shadow-brand-500/20">
                        <i class="ph-bold ph-check-fat text-xl"></i> Confirmar ingreso
                    </button>
                </div>
            </div>

            {{-- Ingreso manual --}}
            <div class="card-dark rounded-2xl p-4">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">
                    <i class="ph-fill ph-keyboard text-brand-500 mr-1"></i> Ingresar código manual
                </p>
                <div class="flex gap-2">
                    <input type="text" placeholder="Ej: FUTBO-R005"
                           class="flex-1 bg-dark-950 border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 outline-none focus:border-brand-500 transition-colors uppercase tracking-wider">
                    <button class="px-4 py-3 bg-brand-500 hover:bg-brand-600 text-white rounded-xl font-bold transition-colors">
                        <i class="ph-bold ph-magnifying-glass"></i>
                    </button>
                </div>
            </div>

        </div>

        {{-- === TAB: WALK-IN === --}}
        <div id="tab-walkin" class="hidden space-y-4">

            <div class="text-center pt-2">
                <h2 class="text-xl font-extrabold text-slate-900 dark:text-white mb-1">Nuevo Presencial</h2>
                <p class="text-slate-400 text-sm">Cliente presencial sin reserva previa</p>
            </div>

            <form class="space-y-4" id="form-walkin" onsubmit="registrarWalkin(event)">
                @csrf

                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 block">Cancha</label>
                    <div class="grid grid-cols-2 gap-2">
                        @if($venue && $venue->fields->count() > 0)
                            @foreach($venue->fields->where('status','active') as $i => $field)
                            <label class="cursor-pointer">
                                <input type="radio" name="field_id" value="{{ $field->id }}"
                                       class="peer sr-only" {{ $i === 0 ? 'checked' : '' }}>
                                <div class="text-center py-3 rounded-2xl border-2 border-white/10
                                            peer-checked:border-brand-500 peer-checked:bg-brand-500/10
                                            transition-all text-slate-400 peer-checked:text-brand-400 font-bold text-sm">
                                    {{ $field->name }}
                                    <span class="block text-[10px] font-medium opacity-70">{{ strtoupper($field->sport_type) }}</span>
                                </div>
                            </label>
                            @endforeach
                        @else
                            <p class="text-slate-500 text-sm col-span-2 text-center py-3">Sin canchas disponibles</p>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 block">Hora</label>
                    @php
                        $horasDisponibles = [];
                        $ahora = now()->hour;
                        for ($h = max($ahora, 7); $h <= 22; $h++) {
                            $horasDisponibles[] = str_pad($h, 2, '0', STR_PAD_LEFT).':00';
                        }
                    @endphp
                    <div class="grid grid-cols-4 gap-2">
                        @foreach($horasDisponibles as $i => $slot)
                        <label class="cursor-pointer">
                            <input type="radio" name="hora" value="{{ $slot }}"
                                   class="peer sr-only" {{ $i === 0 ? 'checked' : '' }}>
                            <div class="text-center py-2.5 rounded-xl border-2 border-white/10
                                        peer-checked:border-brand-500 peer-checked:bg-brand-500/10
                                        transition-all text-slate-400 peer-checked:text-brand-400 font-bold text-xs">
                                {{ $slot }}
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 block">
                        Nombre <span class="text-slate-600 normal-case font-normal">(opcional)</span>
                    </label>
                    <div class="flex items-center gap-3 bg-white dark:bg-dark-950 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3.5 focus-within:border-brand-500 transition-colors">
                        <i class="ph-fill ph-user text-slate-500 text-lg"></i>
                        <input type="text" placeholder="Nombre del cliente" name="nombre_walkin"
                               class="flex-1 bg-transparent text-sm text-slate-900 dark:text-white placeholder-slate-500 outline-none font-medium">
                    </div>
                </div>

                {{-- Monto --}}
                <div class="card-dark rounded-2xl p-4 border border-brand-500/20">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Monto a cobrar</p>
                    <div class="flex items-center gap-2">
                        <span class="text-brand-500 font-extrabold text-2xl">S/</span>
                        <input type="number" name="monto_walkin" value="80" min="0"
                               class="flex-1 bg-transparent text-3xl font-black text-slate-900 dark:text-white outline-none">
                        <span class="text-slate-400">/hr</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-2 flex items-center gap-1">
                        <i class="ph-fill ph-info text-amber-400"></i>
                        Cobro 100% en efectivo — se registra en tu turno
                    </p>
                </div>

                <button type="submit"
                        class="btn-action bg-brand-500 hover:bg-brand-600 text-white shadow-xl shadow-brand-500/30">
                    <i class="ph-bold ph-check-fat text-2xl"></i>
                    Registrar y cobrar
                </button>

            </form>

            {{-- Confirmación walk-in (oculta) --}}
            <div id="walkin-ok" class="hidden card-dark rounded-2xl p-5 text-center border border-brand-500/30">
                <div class="w-14 h-14 rounded-2xl bg-brand-500/20 flex items-center justify-center mx-auto mb-3">
                    <i class="ph-fill ph-check-circle text-brand-500 text-3xl"></i>
                </div>
                <p class="font-extrabold text-white text-lg mb-1">Presencial registrado</p>
                <p class="text-slate-400 text-sm">Cancha 1 · 20:00 · S/ 80.00</p>
                <button onclick="document.getElementById('walkin-ok').classList.add('hidden')"
                        class="mt-4 px-6 py-2.5 rounded-xl bg-brand-500/10 border border-brand-500/20 text-brand-400 font-bold text-sm">
                    Nuevo presencial
                </button>
            </div>

        </div>

        {{-- === TAB: TURNO/CAJA === --}}
        <div id="tab-caja" class="hidden space-y-4">

            <div class="text-center pt-2">
                <h2 class="text-xl font-extrabold text-slate-900 dark:text-white mb-1">Mi Turno</h2>
                <p class="text-slate-400 text-sm">Resumen de caja · {{ date('d M Y') }}</p>
            </div>

            {{-- Resumen caja --}}
            <div class="card-dark rounded-2xl p-5 space-y-3">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">
                    <i class="ph-fill ph-coin text-brand-500 mr-1"></i> Caja del turno
                </p>
                @foreach([
                    ['label' => 'Reservas completadas', 'val' => 'S/ 240.00', 'sub' => '3 pagos en efectivo',  'icon' => 'ph-calendar-check', 'color' => 'text-slate-300'],
                    ['label' => 'Presenciales cobrados', 'val' => 'S/ 160.00', 'sub' => '2 ingresos directos', 'icon' => 'ph-user-plus',       'color' => 'text-blue-400'],
                    ['label' => 'Anticipos digitales',   'val' => 'S/ 0.00',   'sub' => 'Cobrados por app',    'icon' => 'ph-credit-card',     'color' => 'text-slate-500'],
                ] as $item)
                <div class="flex items-center justify-between py-3 border-b border-white/5 last:border-0">
                    <div class="flex items-center gap-3">
                        <i class="ph-fill {{ $item['icon'] }} {{ $item['color'] }} text-xl"></i>
                        <div>
                            <p class="text-sm font-semibold text-slate-200">{{ $item['label'] }}</p>
                            <p class="text-[10px] text-slate-500">{{ $item['sub'] }}</p>
                        </div>
                    </div>
                    <span class="font-bold text-white">{{ $item['val'] }}</span>
                </div>
                @endforeach

                <div class="flex justify-between items-center pt-3 border-t border-slate-100 dark:border-white/10">
                    <span class="font-bold text-slate-900 dark:text-white">Total efectivo a entregar</span>
                    <span class="text-2xl font-black text-brand-500">S/ 400.00</span>
                </div>
            </div>

            {{-- Actividad del turno --}}
            <div class="card-dark rounded-2xl p-5">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">
                    <i class="ph-fill ph-list-bullets text-brand-500 mr-1"></i> Actividad
                </p>
                <div class="space-y-3">
                    @foreach([
                        ['hora' => '07:04', 'desc' => 'Ingreso: Mario Quispe',   'cancha' => 'Cancha 1', 'tipo' => 'checkin'],
                        ['hora' => '07:58', 'desc' => 'Ingreso: Luis Torres',    'cancha' => 'Cancha 2', 'tipo' => 'checkin'],
                        ['hora' => '09:02', 'desc' => 'Presencial registrado',      'cancha' => 'Cancha 1', 'tipo' => 'walkin'],
                        ['hora' => '10:17', 'desc' => 'Falta: Carlos Mamani',   'cancha' => 'Cancha 3', 'tipo' => 'noshow'],
                        ['hora' => '14:30', 'desc' => 'Presencial registrado',      'cancha' => 'Cancha 2', 'tipo' => 'walkin'],
                    ] as $act)
                    <div class="flex items-center gap-3">
                        <span class="text-[10px] text-slate-500 w-10 shrink-0">{{ $act['hora'] }}</span>
                        <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0
                            {{ $act['tipo'] === 'checkin' ? 'bg-brand-500/20' : ($act['tipo'] === 'walkin' ? 'bg-blue-500/20' : 'bg-amber-500/20') }}">
                            <i class="ph-fill
                                {{ $act['tipo'] === 'checkin' ? 'ph-check text-brand-500' : ($act['tipo'] === 'walkin' ? 'ph-user-plus text-blue-400' : 'ph-x text-amber-400') }}
                                text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-slate-200">{{ $act['desc'] }}</p>
                            <p class="text-[10px] text-slate-500">{{ $act['cancha'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Cerrar turno --}}
            <button onclick="cerrarTurno()"
                    class="btn-action border-2 border-red-500/30 bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white hover:border-red-500">
                <i class="ph-bold ph-sign-out text-2xl"></i>
                Cerrar turno
            </button>

        </div>

    </main>

    {{-- ============================================================
         BOTTOM NAV
    ============================================================ --}}
    <nav class="fixed bottom-0 left-0 right-0 max-w-sm mx-auto z-50 bg-white/90 dark:bg-dark-950/95 backdrop-blur-xl border-t border-slate-200 dark:border-white/5 transition-colors duration-300">
        <div class="flex justify-around items-center py-3 px-4">
            <button onclick="irTab('inicio')" id="nav-inicio"
                    class="flex flex-col items-center gap-1 tab-active transition-colors">
                <i class="ph-fill ph-house text-2xl"></i>
                <span class="text-[10px] font-bold">Inicio</span>
            </button>
            <button onclick="irTab('qr')" id="nav-qr"
                    class="flex flex-col items-center gap-1 tab-inactive transition-colors">
                <i class="ph-bold ph-qr-code text-2xl"></i>
                <span class="text-[10px] font-medium">Escanear</span>
            </button>
            {{-- Botón central grande --}}
            <button onclick="irTab('walkin')"
                    class="relative -top-5 w-16 h-16 rounded-2xl bg-brand-500 text-white flex flex-col items-center justify-center shadow-xl shadow-brand-500/40 hover:bg-brand-600 active:scale-95 transition-all">
                <i class="ph-bold ph-user-plus text-2xl"></i>
                <span class="text-[9px] font-bold leading-none mt-0.5">Presencial</span>
            </button>
            <button onclick="irTab('caja')" id="nav-caja"
                    class="flex flex-col items-center gap-1 tab-inactive transition-colors">
                <i class="ph-bold ph-money text-2xl"></i>
                <span class="text-[10px] font-medium">Caja</span>
            </button>
            <a href="/partner" class="flex flex-col items-center gap-1 tab-inactive transition-colors">
                <i class="ph-bold ph-arrow-left text-2xl"></i>
                <span class="text-[10px] font-medium">Salir</span>
            </a>
        </div>
    </nav>

    <form id="form-logout-staff" action="/logout" method="POST" class="hidden">
        @csrf
    </form>

    <script>
        // Reloj en tiempo real
        function actualizarReloj() {
            const ahora = new Date();
            const h = String(ahora.getHours()).padStart(2,'0');
            const m = String(ahora.getMinutes()).padStart(2,'0');
            document.getElementById('reloj').textContent = `${h}:${m}`;
            const dias = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
            const meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
            document.getElementById('fecha-hoy').textContent =
                `${dias[ahora.getDay()]} ${ahora.getDate()} ${meses[ahora.getMonth()]}`;
        }
        actualizarReloj();
        setInterval(actualizarReloj, 1000);

        // Navegación tabs
        const tabs = ['inicio','qr','walkin','caja'];
        function irTab(tab) {
            tabs.forEach(t => {
                document.getElementById(`tab-${t}`)?.classList.add('hidden');
                const nav = document.getElementById(`nav-${t}`);
                if (nav) { nav.className = nav.className.replace('tab-active','tab-inactive'); }
            });
            document.getElementById(`tab-${tab}`)?.classList.remove('hidden');
            const navActive = document.getElementById(`nav-${tab}`);
            if (navActive) {
                navActive.className = navActive.className.replace('tab-inactive','tab-active');
            }
        }

        const csrfToken = document.querySelector('meta[name=csrf-token]')?.content
                       || '{{ csrf_token() }}';

        // Leer QR manual o simulado y validar contra BD
        function buscarQR(token) {
            if (!token) return;
            token = token.trim().toUpperCase();
            fetch('/staff/checkin', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ qr_token: token }),
            })
            .then(r => r.json())
            .then(res => {
                const box = document.getElementById('qr-resultado');
                box.classList.remove('hidden');
                if (res.ok) {
                    box.innerHTML = `
                        <div class="card-dark rounded-2xl p-4 border border-brand-500/30">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-xl bg-brand-500/20 flex items-center justify-center">
                                    <i class="ph-fill ph-check-circle text-brand-500 text-2xl"></i>
                                </div>
                                <div><p class="font-bold text-white text-sm">QR Válido — Ingreso registrado</p><p class="text-[10px] text-brand-400">${token}</p></div>
                            </div>
                            <div class="space-y-1.5 text-sm border-t border-white/5 pt-3">
                                <div class="flex justify-between"><span class="text-slate-400">Cliente</span><span class="font-semibold text-white">${res.cliente}</span></div>
                                <div class="flex justify-between"><span class="text-slate-400">Cancha</span><span class="font-semibold text-white">${res.cancha}</span></div>
                                <div class="flex justify-between"><span class="text-slate-400">Horario</span><span class="font-semibold text-white">${res.hora}</span></div>
                                <div class="flex justify-between border-t border-white/5 pt-2 mt-2"><span class="text-slate-400">Saldo cobrado</span><span class="font-extrabold text-brand-400 text-lg">S/ ${parseFloat(res.saldo).toFixed(2)}</span></div>
                            </div>
                            <button onclick="document.getElementById('qr-resultado').classList.add('hidden')"
                                    class="mt-3 w-full py-2.5 rounded-xl bg-brand-500 text-white font-bold text-sm">Listo</button>
                        </div>`;
                } else {
                    box.innerHTML = `<div class="card-dark rounded-2xl p-4 border border-red-500/30 text-center">
                        <i class="ph-fill ph-x-circle text-red-400 text-3xl mb-2"></i>
                        <p class="font-bold text-white">QR no válido</p>
                        <p class="text-slate-400 text-xs mt-1">${res.error || 'Reserva no encontrada'}</p>
                        <button onclick="document.getElementById('qr-resultado').classList.add('hidden')" class="mt-3 px-4 py-2 rounded-xl bg-red-500/20 text-red-400 font-bold text-sm">Cerrar</button>
                    </div>`;
                }
                box.scrollIntoView({ behavior: 'smooth' });
            })
            .catch(() => alert('Error de conexión'));
        }

        // Botón "Simular lectura QR" — en producción esto lo hace la cámara
        function simularQR() {
            // Toma el primer QR confirmado disponible para demo
            const tokenInput = document.querySelector('input[placeholder*="FUTGO"]');
            const token = tokenInput?.value || '{{ $proximas->first()?->qr_token ?? "FUTGO-DEMO" }}';
            buscarQR(token);
        }

        // Check-in desde lista de próximas reservas
        function simularCheckin(btn, qrToken) {
            btn.disabled = true;
            btn.innerHTML = '<i class="ph-bold ph-spinner-gap animate-spin text-lg"></i>';
            fetch('/staff/checkin', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ qr_token: qrToken }),
            })
            .then(r => r.json())
            .then(res => {
                if (res.ok) {
                    btn.className = btn.className.replace('bg-brand-500/10 border-brand-500/20 text-brand-400','bg-brand-500 text-white border-brand-500');
                    btn.innerHTML = '<i class="ph-fill ph-check-fat text-lg"></i>';
                } else {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="ph-bold ph-check-fat text-lg"></i>';
                    alert(res.error || 'No se pudo hacer check-in');
                }
            })
            .catch(() => { btn.disabled = false; btn.innerHTML = '<i class="ph-bold ph-check-fat text-lg"></i>'; });
        }

        // Presencial — llama a la BD real
        function registrarWalkin(e) {
            e.preventDefault();
            const form    = document.getElementById('form-walkin');
            const data    = new FormData(form);
            const btn     = form.querySelector('button[type=submit]');
            btn.disabled  = true;
            btn.innerHTML = '<i class="ph-bold ph-spinner-gap animate-spin text-2xl"></i> Registrando...';

            fetch('/staff/walkin', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': data.get('_token'), 'Accept': 'application/json' },
                body: data,
            })
            .then(r => r.json())
            .then(res => {
                if (res.ok) {
                    document.getElementById('walkin-ok').classList.remove('hidden');
                    document.getElementById('walkin-ok').scrollIntoView({ behavior: 'smooth' });
                    // Actualizar contador de presenciales en pantalla
                    const counter = document.querySelector('[data-stat="presenciales"]');
                    if (counter) counter.textContent = parseInt(counter.textContent || 0) + 1;
                } else {
                    alert('Error: ' + (res.error || 'No se pudo registrar'));
                }
            })
            .catch(() => alert('Error de conexión'))
            .finally(() => {
                btn.disabled  = false;
                btn.innerHTML = '<i class="ph-bold ph-check-fat text-2xl"></i> Registrar y cobrar';
            });
        }

        // Cerrar turno
        function cerrarTurno() {
            if (confirm('¿Estás seguro de cerrar el turno? Se registrará el cierre de caja.')) {
                document.getElementById('form-logout-staff').submit();
            }
        }
    </script>
</body>
</html>
