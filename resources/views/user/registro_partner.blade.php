@extends('layouts.app')
@section('title', 'Registrar mi complejo | FutGo')

@section('content')

<div class="max-w-2xl mx-auto px-4 py-12">

    {{-- Header --}}
    <div class="text-center mb-10">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-brand-500 to-emerald-600 flex items-center justify-center mx-auto mb-4 shadow-lg shadow-brand-500/30">
            <i class="ph-bold ph-buildings text-white text-3xl"></i>
        </div>
        <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-2">
            Registrá tu complejo en FutGo
        </h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm max-w-md mx-auto leading-relaxed">
            Digitalizá tu complejo deportivo sin costo. Revisamos tu solicitud en menos de 48 horas y te contactamos para activar tu cuenta.
        </p>
    </div>

    {{-- Beneficios rápidos --}}
    <div class="grid grid-cols-3 gap-3 mb-8">
        @foreach([
            ['icon' => 'ph-currency-circle-dollar', 'color' => 'text-brand-500', 'bg' => 'bg-brand-500/10', 'label' => 'Sin costo de alta'],
            ['icon' => 'ph-chart-line-up',           'color' => 'text-blue-500',  'bg' => 'bg-blue-500/10',  'label' => 'Más reservas'],
            ['icon' => 'ph-clock',                   'color' => 'text-purple-500','bg' => 'bg-purple-500/10','label' => 'Activación en 48h'],
        ] as $b)
        <div class="glass-card rounded-2xl p-4 text-center">
            <div class="w-10 h-10 rounded-xl {{ $b['bg'] }} flex items-center justify-center mx-auto mb-2">
                <i class="ph-fill {{ $b['icon'] }} {{ $b['color'] }} text-xl"></i>
            </div>
            <p class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $b['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Formulario --}}
    <form action="/registro-partner" method="POST" class="glass-card rounded-3xl p-8 shadow-xl space-y-6">
        @csrf

        {{-- SECCIÓN 1: Datos del complejo --}}
        <div>
            <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-4 pb-3 border-b border-slate-100 dark:border-white/10">
                <i class="ph-fill ph-soccer-ball text-brand-500"></i> Datos del complejo
            </h2>
            <div class="space-y-4">

                <div>
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Nombre del complejo *</label>
                    <div class="flex items-center gap-3 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3.5 bg-white dark:bg-white/5 focus-within:border-brand-500 focus-within:ring-4 focus-within:ring-brand-500/10 transition-all group">
                        <i class="ph-fill ph-buildings text-slate-400 group-focus-within:text-brand-500 transition-colors text-lg shrink-0"></i>
                        <input type="text" name="nombre_complejo" placeholder="Ej: Complejo Deportivo El 10" required
                               class="flex-1 bg-transparent text-sm font-medium outline-none text-slate-900 dark:text-white placeholder-slate-400">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Ciudad *</label>
                        <div class="flex items-center gap-3 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3.5 bg-white dark:bg-white/5 focus-within:border-brand-500 focus-within:ring-4 focus-within:ring-brand-500/10 transition-all group">
                            <i class="ph-fill ph-map-pin text-slate-400 group-focus-within:text-brand-500 transition-colors text-lg shrink-0"></i>
                            <input type="text" name="ciudad" placeholder="Ej: Cusco" required
                                   class="flex-1 bg-transparent text-sm font-medium outline-none text-slate-900 dark:text-white placeholder-slate-400">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Distrito *</label>
                        <div class="flex items-center gap-3 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3.5 bg-white dark:bg-white/5 focus-within:border-brand-500 focus-within:ring-4 focus-within:ring-brand-500/10 transition-all group">
                            <i class="ph-fill ph-map-trifold text-slate-400 group-focus-within:text-brand-500 transition-colors text-lg shrink-0"></i>
                            <input type="text" name="distrito" placeholder="Ej: Wanchaq" required
                                   class="flex-1 bg-transparent text-sm font-medium outline-none text-slate-900 dark:text-white placeholder-slate-400">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Dirección exacta *</label>
                    <div class="flex items-center gap-3 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3.5 bg-white dark:bg-white/5 focus-within:border-brand-500 focus-within:ring-4 focus-within:ring-brand-500/10 transition-all group">
                        <i class="ph-fill ph-navigation-arrow text-slate-400 group-focus-within:text-brand-500 transition-colors text-lg shrink-0"></i>
                        <input type="text" name="direccion" placeholder="Ej: Av. Los Incas 342" required
                               class="flex-1 bg-transparent text-sm font-medium outline-none text-slate-900 dark:text-white placeholder-slate-400">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Cantidad de canchas *</label>
                        <div class="flex items-center gap-3 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3.5 bg-white dark:bg-white/5 focus-within:border-brand-500 focus-within:ring-4 focus-within:ring-brand-500/10 transition-all group">
                            <i class="ph-fill ph-hash text-slate-400 group-focus-within:text-brand-500 transition-colors text-lg shrink-0"></i>
                            <input type="number" name="canchas" placeholder="Ej: 4" min="1" max="20" required
                                   class="flex-1 bg-transparent text-sm font-medium outline-none text-slate-900 dark:text-white placeholder-slate-400">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Tipo principal</label>
                        <div class="relative flex items-center gap-3 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3.5 bg-white dark:bg-white/5 focus-within:border-brand-500 transition-all group">
                            <i class="ph-fill ph-soccer-ball text-slate-400 group-focus-within:text-brand-500 transition-colors text-lg shrink-0"></i>
                            <select name="tipo" class="flex-1 bg-transparent text-sm font-medium outline-none text-slate-900 dark:text-white appearance-none cursor-pointer">
                                <option value="futbol5" class="bg-white dark:bg-dark-900">Fútbol 5</option>
                                <option value="futbol7" class="bg-white dark:bg-dark-900">Fútbol 7</option>
                                <option value="futbol11" class="bg-white dark:bg-dark-900">Fútbol 11</option>
                                <option value="mixto" class="bg-white dark:bg-dark-900">Mixto</option>
                            </select>
                            <i class="ph-bold ph-caret-down text-slate-400 text-xs pointer-events-none absolute right-4"></i>
                        </div>
                    </div>
                </div>

                {{-- Comodidades --}}
                <div>
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3 block">Comodidades disponibles</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach(['Techada', 'Pasto sintético', 'Iluminación LED', 'Vestuarios', 'Estacionamiento', 'Bar / Cafetería', 'WiFi', 'Duchas'] as $com)
                        <label class="flex items-center gap-2.5 cursor-pointer p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-white/5 transition-colors border border-transparent hover:border-slate-200 dark:hover:border-white/10 group">
                            <div class="relative flex items-center shrink-0">
                                <input type="checkbox" name="comodidades[]" value="{{ $com }}"
                                       class="peer appearance-none w-5 h-5 border-2 border-slate-300 dark:border-white/20 rounded-md bg-white dark:bg-white/5 checked:bg-brand-500 checked:border-brand-500 transition-all cursor-pointer">
                                <i class="ph-bold ph-check text-white absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 opacity-0 peer-checked:opacity-100 pointer-events-none text-xs"></i>
                            </div>
                            <span class="text-sm text-slate-600 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">{{ $com }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

        {{-- SECCIÓN 2: Datos del responsable --}}
        <div>
            <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-4 pb-3 border-b border-slate-100 dark:border-white/10">
                <i class="ph-fill ph-user-circle text-brand-500"></i> Tus datos de contacto
            </h2>
            <div class="space-y-4">

                <div>
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Nombre completo *</label>
                    <div class="flex items-center gap-3 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3.5 bg-white dark:bg-white/5 focus-within:border-brand-500 focus-within:ring-4 focus-within:ring-brand-500/10 transition-all group">
                        <i class="ph-fill ph-user text-slate-400 group-focus-within:text-brand-500 transition-colors text-lg shrink-0"></i>
                        <input type="text" name="nombre" placeholder="Tu nombre completo" required
                               class="flex-1 bg-transparent text-sm font-medium outline-none text-slate-900 dark:text-white placeholder-slate-400">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Email *</label>
                        <div class="flex items-center gap-3 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3.5 bg-white dark:bg-white/5 focus-within:border-brand-500 focus-within:ring-4 focus-within:ring-brand-500/10 transition-all group">
                            <i class="ph-fill ph-envelope text-slate-400 group-focus-within:text-brand-500 transition-colors text-lg shrink-0"></i>
                            <input type="email" name="email" placeholder="tucorreo@email.com" required
                                   class="flex-1 bg-transparent text-sm font-medium outline-none text-slate-900 dark:text-white placeholder-slate-400">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">WhatsApp *</label>
                        <div class="flex items-center gap-3 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3.5 bg-white dark:bg-white/5 focus-within:border-brand-500 focus-within:ring-4 focus-within:ring-brand-500/10 transition-all group">
                            <i class="ph-fill ph-whatsapp-logo text-slate-400 group-focus-within:text-brand-500 transition-colors text-lg shrink-0"></i>
                            <input type="tel" name="whatsapp" placeholder="+51 987 000 000" required
                                   class="flex-1 bg-transparent text-sm font-medium outline-none text-slate-900 dark:text-white placeholder-slate-400">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">
                        ¿Algo más que quieras contarnos? <span class="text-slate-300 dark:text-slate-600 normal-case font-normal">(opcional)</span>
                    </label>
                    <textarea name="mensaje" rows="3" placeholder="Horarios, cantidad de canchas por tipo, si ya tenés sistema de reservas actualmente..."
                              class="w-full border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3.5 bg-white dark:bg-white/5 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 outline-none text-sm text-slate-900 dark:text-white placeholder-slate-400 resize-none transition-all"></textarea>
                </div>

            </div>
        </div>

        {{-- Aviso --}}
        <div class="bg-brand-500/5 border border-brand-500/20 rounded-2xl px-4 py-3 flex items-start gap-2.5">
            <i class="ph-fill ph-info text-brand-500 shrink-0 mt-0.5"></i>
            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                Al enviar esta solicitud, nuestro equipo la revisará y te contactará por WhatsApp en menos de <strong class="text-slate-800 dark:text-white">48 horas hábiles</strong> para verificar los datos y activar tu cuenta. El registro es <strong class="text-slate-800 dark:text-white">completamente gratuito</strong>.
            </p>
        </div>

        {{-- Botón --}}
        <button type="submit"
                class="w-full bg-brand-500 hover:bg-brand-600 text-white font-bold py-4 rounded-2xl flex items-center justify-center gap-2 transition-all hover:scale-[1.02] active:scale-95 shadow-lg shadow-brand-500/30 text-base">
            <i class="ph-bold ph-paper-plane-tilt text-lg"></i> Enviar solicitud
        </button>

    </form>

    <p class="text-center text-sm text-slate-400 mt-6">
        ¿Sos jugador?
        <a href="/registro" class="text-brand-500 font-bold hover:text-brand-600 transition-colors">
            Registrate como jugador acá
        </a>
    </p>

</div>

@endsection
