@extends('layouts.app')
@section('title', 'Ingresar | FutGo')

@section('content')

<div class="min-h-[calc(100vh-5rem)] flex items-center justify-center px-4 py-12 relative overflow-hidden">
    
    {{-- Background Glows --}}
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-brand-500/20 rounded-full blur-[100px] pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">

        {{-- Logo centrado --}}
        <div class="text-center mb-8 animate-float">
            <a href="/" class="inline-flex items-center gap-2 mb-4 group">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-brand-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-brand-500/30 group-hover:scale-105 transition-transform">
                    <i class="ph-bold ph-soccer-ball text-white text-3xl"></i>
                </div>
            </a>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Bienvenido de vuelta</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm font-medium">Ingresa para reservar tu cancha en segundos</p>
        </div>

        {{-- Card --}}
        <div class="glass-card rounded-3xl p-8 shadow-2xl dark:shadow-black/50 border border-slate-200/60 dark:border-white/10">

            {{-- Botón Google --}}
            <button class="w-full flex items-center justify-center gap-3 py-3 px-4 rounded-2xl border border-slate-200 dark:border-white/10 bg-white/50 dark:bg-dark-800/50 hover:bg-slate-50 dark:hover:bg-white/10 transition-colors font-bold text-slate-700 dark:text-white text-sm mb-6 shadow-sm">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Continuar con Google
            </button>

            {{-- Divisor --}}
            <div class="flex items-center gap-3 mb-6">
                <div class="flex-1 h-px bg-slate-200 dark:bg-white/10"></div>
                <span class="text-xs text-slate-400 font-bold uppercase tracking-widest">o usa tu email</span>
                <div class="flex-1 h-px bg-slate-200 dark:bg-white/10"></div>
            </div>

            {{-- Formulario --}}
            <form action="/login" method="POST" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 block">
                        Correo Electrónico
                    </label>
                    <div class="flex items-center gap-3 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3.5 bg-white/50 dark:bg-dark-800/50 focus-within:border-brand-500 focus-within:ring-4 focus-within:ring-brand-500/10 transition-all group">
                        <i class="ph-fill ph-envelope text-slate-400 dark:text-slate-500 group-focus-within:text-brand-500 transition-colors text-lg"></i>
                        <input type="email" name="email" placeholder="tucorreo@email.com" required
                               class="flex-1 bg-transparent text-sm font-medium outline-none text-slate-900 dark:text-white placeholder-slate-400">
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Contraseña
                        </label>
                        <a href="/recuperar" class="text-xs text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 font-bold transition-colors">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>
                    <div class="flex items-center gap-3 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3.5 bg-white/50 dark:bg-dark-800/50 focus-within:border-brand-500 focus-within:ring-4 focus-within:ring-brand-500/10 transition-all group">
                        <i class="ph-fill ph-lock-key text-slate-400 dark:text-slate-500 group-focus-within:text-brand-500 transition-colors text-lg"></i>
                        <input type="password" name="password" id="password" placeholder="••••••••" required
                               class="flex-1 bg-transparent text-sm font-medium outline-none text-slate-900 dark:text-white placeholder-slate-400">
                        <button type="button" onclick="togglePassword()"
                                class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors focus:outline-none">
                            <i class="ph-bold ph-eye text-lg" id="eye-icon"></i>
                        </button>
                    </div>
                </div>

                {{-- Recordarme --}}
                <label class="flex items-center gap-3 cursor-pointer group w-max">
                    <div class="relative flex items-center">
                        <input type="checkbox" name="remember" class="peer appearance-none w-5 h-5 border-2 border-slate-300 dark:border-white/20 rounded-md bg-white dark:bg-dark-800 checked:bg-brand-500 checked:border-brand-500 transition-all cursor-pointer">
                        <i class="ph-bold ph-check text-white absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 opacity-0 peer-checked:opacity-100 pointer-events-none text-xs"></i>
                    </div>
                    <span class="text-sm font-medium text-slate-600 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">Recordarme en este equipo</span>
                </label>

                {{-- Botón --}}
                <button type="submit"
                        class="w-full bg-brand-500 hover:bg-brand-600 text-white font-bold py-4 rounded-2xl flex items-center justify-center gap-2 transition-all hover:scale-[1.02] active:scale-95 shadow-lg shadow-brand-500/30 mt-4 text-base">
                    Ingresar <i class="ph-bold ph-arrow-right text-lg"></i>
                </button>
            </form>
        </div>

        {{-- Registro --}}
        <p class="text-center text-sm font-medium text-slate-500 dark:text-slate-400 mt-8">
            ¿Aún no tienes cuenta?
            <a href="/registro" class="text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 font-bold transition-colors ml-1">
                Regístrate gratis
            </a>
        </p>

    </div>
</div>

@push('scripts')
<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon  = document.getElementById('eye-icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'ph-bold ph-eye-slash text-lg';
        } else {
            input.type = 'password';
            icon.className = 'ph-bold ph-eye text-lg';
        }
    }
</script>
@endpush

@endsection
