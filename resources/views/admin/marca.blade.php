@extends('layouts.admin')
@section('title', 'Configuración de Marca | FutGo Admin')
@section('page-title', 'Configuración de Marca')
@section('page-subtitle', 'Solo el Super Admin puede modificar la identidad visual de la plataforma')

@section('content')

@if(session('success'))
<div class="mb-4 bg-brand-500/10 border border-brand-500/20 rounded-2xl px-5 py-3 flex items-center gap-3 text-brand-700 dark:text-brand-300">
    <i class="ph-fill ph-check-circle text-brand-500 text-xl shrink-0"></i>
    <span class="font-semibold text-sm">{{ session('success') }}</span>
</div>
@endif

<form action="/admin/marca" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf

    {{-- NOMBRE Y ESLOGAN --}}
    <div class="glass-card rounded-2xl p-6">
        <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-5">
            <i class="ph-fill ph-text-aa text-admin-500"></i> Nombre y texto
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">
                    Nombre del sitio <span class="text-red-400">*</span>
                </label>
                <div class="flex items-center gap-3 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 bg-white dark:bg-white/5 focus-within:border-admin-500 focus-within:ring-2 focus-within:ring-admin-500/20 transition-all">
                    <i class="ph-fill ph-globe text-slate-400 shrink-0"></i>
                    <input type="text" name="site_name"
                           value="{{ $settings['site_name'] ?? 'FutGo' }}"
                           placeholder="Ej: FutGo, MiCancha, SportApp"
                           class="flex-1 bg-transparent text-sm font-medium outline-none text-slate-900 dark:text-white placeholder-slate-400"
                           required>
                </div>
                <p class="text-[10px] text-slate-400 mt-1">Aparece en el navbar, tabs del navegador y emails.</p>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Eslogan</label>
                <div class="flex items-center gap-3 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 bg-white dark:bg-white/5 focus-within:border-admin-500 focus-within:ring-2 focus-within:ring-admin-500/20 transition-all">
                    <i class="ph-fill ph-quotes text-slate-400 shrink-0"></i>
                    <input type="text" name="site_tagline"
                           value="{{ $settings['site_tagline'] ?? 'Reservá tu cancha' }}"
                           placeholder="Ej: Reservá tu cancha en segundos"
                           class="flex-1 bg-transparent text-sm font-medium outline-none text-slate-900 dark:text-white placeholder-slate-400">
                </div>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Color principal</label>
                <div class="flex items-center gap-3 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 bg-white dark:bg-white/5 focus-within:border-admin-500 transition-all">
                    <input type="color" name="site_color"
                           value="{{ $settings['site_color'] ?? '#22c55e' }}"
                           class="w-8 h-8 rounded-lg border-0 bg-transparent cursor-pointer shrink-0">
                    <input type="text" id="color-hex" value="{{ $settings['site_color'] ?? '#22c55e' }}"
                           class="flex-1 bg-transparent text-sm font-mono outline-none text-slate-900 dark:text-white"
                           oninput="document.querySelector('[name=site_color]').value=this.value">
                </div>
                <p class="text-[10px] text-slate-400 mt-1">Afecta botones, links y acentos (requiere redespliegue del CSS).</p>
            </div>
        </div>
    </div>

    {{-- LOGOS E ICONOS --}}
    <div class="glass-card rounded-2xl p-6">
        <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-2">
            <i class="ph-fill ph-image-square text-admin-500"></i> Logos e iconos
        </h2>
        <p class="text-xs text-slate-400 mb-5">
            Por ahora se usa el ícono de fútbol por defecto. Subí tus archivos para reemplazarlo.
            Formatos aceptados: PNG, SVG, ICO. Máx. 2MB.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

            {{-- Logo principal --}}
            <div class="space-y-3">
                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Logo principal</label>
                <div class="border-2 border-dashed border-slate-200 dark:border-white/10 rounded-2xl p-6 text-center hover:border-admin-500 transition-colors cursor-pointer relative"
                     onclick="document.getElementById('logo-input').click()">
                    @if(!empty($settings['site_logo']))
                    <img src="{{ $settings['site_logo'] }}" alt="Logo" class="h-16 mx-auto object-contain mb-2">
                    @else
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-admin-500 to-purple-600 flex items-center justify-center mx-auto mb-2 shadow-lg">
                        <i class="ph-bold ph-soccer-ball text-white text-2xl"></i>
                    </div>
                    <p class="text-[10px] text-slate-400 font-semibold">Ícono por defecto</p>
                    @endif
                    <p class="text-xs text-admin-500 font-bold mt-2">Clic para cambiar</p>
                    <p class="text-[10px] text-slate-400">PNG o SVG · Recomendado: 200×200px</p>
                    <input type="file" id="logo-input" name="site_logo" accept="image/*" class="hidden"
                           onchange="previewImage(this, 'logo-preview')">
                </div>
                <img id="logo-preview" src="" alt="" class="hidden h-12 mx-auto object-contain rounded-xl border border-slate-200 dark:border-white/10 p-1">
            </div>

            {{-- Logo modo oscuro --}}
            <div class="space-y-3">
                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Logo modo oscuro</label>
                <div class="border-2 border-dashed border-slate-200 dark:border-white/10 rounded-2xl p-6 text-center hover:border-admin-500 transition-colors cursor-pointer bg-slate-900/5 dark:bg-white/5"
                     onclick="document.getElementById('logo-dark-input').click()">
                    @if(!empty($settings['site_logo_dark']))
                    <img src="{{ $settings['site_logo_dark'] }}" alt="Logo dark" class="h-16 mx-auto object-contain mb-2">
                    @else
                    <div class="w-16 h-16 rounded-2xl bg-slate-800 flex items-center justify-center mx-auto mb-2 shadow-lg border border-white/10">
                        <i class="ph-bold ph-soccer-ball text-white text-2xl"></i>
                    </div>
                    <p class="text-[10px] text-slate-400 font-semibold">Ícono por defecto</p>
                    @endif
                    <p class="text-xs text-admin-500 font-bold mt-2">Clic para cambiar</p>
                    <p class="text-[10px] text-slate-400">Versión blanca/clara para fondos oscuros</p>
                    <input type="file" id="logo-dark-input" name="site_logo_dark" accept="image/*" class="hidden"
                           onchange="previewImage(this, 'logo-dark-preview')">
                </div>
                <img id="logo-dark-preview" src="" alt="" class="hidden h-12 mx-auto object-contain rounded-xl border border-slate-200 dark:border-white/10 p-1">
            </div>

            {{-- Favicon --}}
            <div class="space-y-3">
                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Favicon</label>
                <div class="border-2 border-dashed border-slate-200 dark:border-white/10 rounded-2xl p-6 text-center hover:border-admin-500 transition-colors cursor-pointer"
                     onclick="document.getElementById('favicon-input').click()">
                    @if(!empty($settings['site_favicon']))
                    <img src="{{ $settings['site_favicon'] }}" alt="Favicon" class="h-16 mx-auto object-contain mb-2">
                    @else
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-brand-500 to-emerald-600 flex items-center justify-center mx-auto mb-2">
                        <i class="ph-bold ph-soccer-ball text-white text-lg"></i>
                    </div>
                    <p class="text-[10px] text-slate-400 font-semibold">Ícono por defecto</p>
                    @endif
                    <p class="text-xs text-admin-500 font-bold mt-2">Clic para cambiar</p>
                    <p class="text-[10px] text-slate-400">ICO o PNG · 32×32px o 64×64px</p>
                    <input type="file" id="favicon-input" name="site_favicon" accept=".ico,.png,.svg" class="hidden"
                           onchange="previewImage(this, 'favicon-preview')">
                </div>
                <img id="favicon-preview" src="" alt="" class="hidden h-10 mx-auto object-contain rounded-xl border border-slate-200 dark:border-white/10 p-1">
            </div>
        </div>

        <div class="mt-4 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 rounded-xl px-4 py-3 flex items-start gap-2">
            <i class="ph-fill ph-info text-amber-500 shrink-0 mt-0.5"></i>
            <p class="text-xs text-amber-700 dark:text-amber-400">
                Las imágenes se guardan en el servidor. Para producción se recomienda configurar un bucket S3/R2 en el `.env`.
                Los cambios se aplican inmediatamente en el sitio.
            </p>
        </div>
    </div>

    {{-- CONTACTO --}}
    <div class="glass-card rounded-2xl p-6">
        <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-5">
            <i class="ph-fill ph-envelope text-admin-500"></i> Información de contacto
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach([
                ['key' => 'site_email',    'label' => 'Email de contacto', 'icon' => 'ph-envelope', 'placeholder' => 'hola@miapp.com'],
                ['key' => 'site_phone',    'label' => 'Teléfono',          'icon' => 'ph-phone',    'placeholder' => '+51 999 000 000'],
                ['key' => 'site_country',  'label' => 'País de operación', 'icon' => 'ph-map-pin',  'placeholder' => 'Perú'],
                ['key' => 'site_currency', 'label' => 'Símbolo de moneda', 'icon' => 'ph-currency-circle-dollar', 'placeholder' => 'S/'],
            ] as $f)
            <div>
                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">{{ $f['label'] }}</label>
                <div class="flex items-center gap-3 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 bg-white dark:bg-white/5 focus-within:border-admin-500 focus-within:ring-2 focus-within:ring-admin-500/20 transition-all">
                    <i class="ph-fill {{ $f['icon'] }} text-slate-400 shrink-0"></i>
                    <input type="text" name="{{ $f['key'] }}"
                           value="{{ $settings[$f['key']] ?? '' }}"
                           placeholder="{{ $f['placeholder'] }}"
                           class="flex-1 bg-transparent text-sm font-medium outline-none text-slate-900 dark:text-white placeholder-slate-400">
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- PREVIEW --}}
    <div class="glass-card rounded-2xl p-6">
        <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-4">
            <i class="ph-fill ph-eye text-admin-500"></i> Preview del navbar
        </h2>
        <div class="bg-white dark:bg-dark-900 border border-slate-200 dark:border-white/10 rounded-xl p-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-admin-500 to-purple-600 flex items-center justify-center shadow-md shrink-0" id="preview-icon">
                <i class="ph-bold ph-soccer-ball text-white text-lg"></i>
            </div>
            <div>
                <span class="font-bold text-lg text-slate-900 dark:text-white" id="preview-name">{{ $settings['site_name'] ?? 'FutGo' }}</span>
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest -mt-0.5" id="preview-tagline">{{ $settings['site_tagline'] ?? 'Reservá tu cancha' }}</span>
            </div>
        </div>
    </div>

    {{-- GUARDAR --}}
    <div class="flex justify-end gap-3">
        <a href="/admin" class="px-6 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-500 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
            Cancelar
        </a>
        <button type="submit"
                class="px-8 py-2.5 rounded-xl bg-admin-500 hover:bg-admin-600 text-white font-bold text-sm transition-all hover:scale-105 shadow-lg shadow-admin-500/20 flex items-center gap-2">
            <i class="ph-bold ph-floppy-disk"></i> Guardar configuración
        </button>
    </div>
</form>

@push('scripts')
<script>
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Preview en tiempo real del nombre
    document.querySelector('[name=site_name]').addEventListener('input', function() {
        document.getElementById('preview-name').textContent = this.value || 'FutGo';
    });
    document.querySelector('[name=site_tagline]').addEventListener('input', function() {
        document.getElementById('preview-tagline').textContent = this.value || 'Reservá tu cancha';
    });
    // Sincronizar color picker y texto
    document.querySelector('[name=site_color]').addEventListener('input', function() {
        document.getElementById('color-hex').value = this.value;
    });
</script>
@endpush

@endsection
