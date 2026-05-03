@extends('layouts.app')
@section('title', 'Crear cuenta | FutGo')

@section('content')

<div class="min-h-[calc(100vh-5rem)] flex items-center justify-center px-4 py-12 relative overflow-hidden">
    <div class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-brand-500/10 rounded-full blur-[100px] pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center gap-2 mb-4 group">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-brand-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-brand-500/30 group-hover:scale-105 transition-transform">
                    <i class="ph-bold ph-soccer-ball text-white text-3xl"></i>
                </div>
            </a>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Crear cuenta gratis</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm">Reservá tu primera cancha en menos de 2 minutos</p>
        </div>

        <div class="glass-card rounded-3xl p-8 shadow-2xl dark:shadow-black/50 border border-slate-200/60 dark:border-white/10">

            {{-- Google --}}
            <button class="w-full flex items-center justify-center gap-3 py-3 px-4 rounded-2xl border border-slate-200 dark:border-white/10 bg-white/50 dark:bg-dark-800/50 hover:bg-slate-50 dark:hover:bg-white/10 transition-colors font-bold text-slate-700 dark:text-white text-sm mb-6 shadow-sm">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Registrarse con Google
            </button>

            <div class="flex items-center gap-3 mb-6">
                <div class="flex-1 h-px bg-slate-200 dark:bg-white/10"></div>
                <span class="text-xs text-slate-400 font-bold uppercase tracking-widest">o con tu email</span>
                <div class="flex-1 h-px bg-slate-200 dark:bg-white/10"></div>
            </div>

            <form action="/registro" method="POST" class="space-y-4">
                @csrf

                {{-- Nombre --}}
                <div>
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Nombre completo</label>
                    <div class="flex items-center gap-3 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3.5 bg-white/50 dark:bg-dark-800/50 focus-within:border-brand-500 focus-within:ring-4 focus-within:ring-brand-500/10 transition-all group">
                        <i class="ph-fill ph-user text-slate-400 dark:text-slate-500 group-focus-within:text-brand-500 transition-colors text-lg"></i>
                        <input type="text" name="nombre" placeholder="Carlos Pérez" required
                               class="flex-1 bg-transparent text-sm font-medium outline-none text-slate-900 dark:text-white placeholder-slate-400">
                    </div>
                </div>

                {{-- Email --}}
                <div>
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Correo electrónico</label>
                    <div class="flex items-center gap-3 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3.5 bg-white/50 dark:bg-dark-800/50 focus-within:border-brand-500 focus-within:ring-4 focus-within:ring-brand-500/10 transition-all group">
                        <i class="ph-fill ph-envelope text-slate-400 dark:text-slate-500 group-focus-within:text-brand-500 transition-colors text-lg"></i>
                        <input type="email" name="email" placeholder="tucorreo@email.com" required
                               class="flex-1 bg-transparent text-sm font-medium outline-none text-slate-900 dark:text-white placeholder-slate-400">
                    </div>
                </div>

                {{-- Teléfono --}}
                <div>
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Teléfono <span class="text-slate-300 dark:text-slate-600 normal-case font-normal">(opcional)</span></label>
                    <div class="flex items-center gap-3 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3.5 bg-white/50 dark:bg-dark-800/50 focus-within:border-brand-500 focus-within:ring-4 focus-within:ring-brand-500/10 transition-all group">
                        <i class="ph-fill ph-phone text-slate-400 dark:text-slate-500 group-focus-within:text-brand-500 transition-colors text-lg"></i>
                        <input type="tel" name="telefono" placeholder="+51 987 000 000"
                               class="flex-1 bg-transparent text-sm font-medium outline-none text-slate-900 dark:text-white placeholder-slate-400">
                    </div>
                </div>

                {{-- Contraseña --}}
                <div>
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Contraseña</label>
                    <div class="flex items-center gap-3 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3.5 bg-white/50 dark:bg-dark-800/50 focus-within:border-brand-500 focus-within:ring-4 focus-within:ring-brand-500/10 transition-all group">
                        <i class="ph-fill ph-lock-key text-slate-400 dark:text-slate-500 group-focus-within:text-brand-500 transition-colors text-lg"></i>
                        <input type="password" name="password" id="password" placeholder="Mínimo 8 caracteres" required
                               class="flex-1 bg-transparent text-sm font-medium outline-none text-slate-900 dark:text-white placeholder-slate-400">
                        <button type="button" onclick="togglePass()"
                                class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
                            <i class="ph-bold ph-eye text-lg" id="eye-icon"></i>
                        </button>
                    </div>
                    {{-- Indicador fortaleza --}}
                    <div class="flex gap-1 mt-2">
                        @for($i = 0; $i < 4; $i++)
                        <div class="h-1 flex-1 rounded-full bg-slate-200 dark:bg-white/10" id="strength-{{ $i }}"></div>
                        @endfor
                    </div>
                </div>

                {{-- Términos --}}
                <label class="flex items-start gap-3 cursor-pointer group">
                    <div class="relative flex items-center mt-0.5">
                        <input type="checkbox" name="terminos" required
                               class="peer appearance-none w-5 h-5 border-2 border-slate-300 dark:border-white/20 rounded-md bg-white dark:bg-dark-800 checked:bg-brand-500 checked:border-brand-500 transition-all cursor-pointer shrink-0">
                        <i class="ph-bold ph-check text-white absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 opacity-0 peer-checked:opacity-100 pointer-events-none text-xs"></i>
                    </div>
                    <span class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                        Acepto los <a href="#" class="text-brand-500 font-bold hover:underline">Términos y condiciones</a>
                        y la <a href="#" class="text-brand-500 font-bold hover:underline">Política de privacidad</a>
                    </span>
                </label>

                <button type="submit"
                        onclick="this.disabled=true;this.innerHTML='<i class=\'ph-bold ph-spinner-gap animate-spin text-lg\'></i> Creando cuenta...';this.form.submit();"
                        class="w-full bg-brand-500 hover:bg-brand-600 text-white font-bold py-4 rounded-2xl flex items-center justify-center gap-2 transition-all hover:scale-[1.02] active:scale-95 shadow-lg shadow-brand-500/30 mt-2 text-base">
                    Crear mi cuenta <i class="ph-bold ph-arrow-right text-lg"></i>
                </button>
            </form>
        </div>

        <p class="text-center text-sm font-medium text-slate-500 dark:text-slate-400 mt-6">
            ¿Ya tenés cuenta?
            <a href="/login" class="text-brand-600 dark:text-brand-400 hover:text-brand-700 font-bold transition-colors ml-1">
                Ingresá acá
            </a>
        </p>
    </div>
</div>

@push('scripts')
<script>
    function togglePass() {
        const input = document.getElementById('password');
        const icon  = document.getElementById('eye-icon');
        input.type  = input.type === 'password' ? 'text' : 'password';
        icon.className = input.type === 'password' ? 'ph-bold ph-eye text-lg' : 'ph-bold ph-eye-slash text-lg';
    }

    document.getElementById('password').addEventListener('input', function() {
        const val = this.value;
        const bars = [0,1,2,3].map(i => document.getElementById('strength-'+i));
        const colors = ['bg-red-500','bg-amber-500','bg-yellow-500','bg-brand-500'];
        let score = 0;
        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        bars.forEach((b, i) => {
            b.className = 'h-1 flex-1 rounded-full transition-colors ' +
                (i < score ? colors[score-1] : 'bg-slate-200 dark:bg-white/10');
        });
    });
</script>
@endpush

@endsection
