@extends('layouts.app')
@section('title', 'Recuperar contraseña | FutGo')

@section('content')

<div class="min-h-[calc(100vh-5rem)] flex items-center justify-center px-4 py-12 relative overflow-hidden">
    <div class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-brand-500/10 rounded-full blur-[100px] pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">

        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center mx-auto mb-4">
                <i class="ph-bold ph-lock-key-open text-amber-500 text-3xl"></i>
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">¿Olvidaste tu contraseña?</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm max-w-xs mx-auto">
                Ingresá tu email y te enviamos un link para crear una nueva contraseña.
            </p>
        </div>

        <div class="glass-card rounded-3xl p-8 shadow-2xl dark:shadow-black/50 border border-slate-200/60 dark:border-white/10">

            {{-- Formulario inicial --}}
            <div id="form-email">
                <form onsubmit="enviarLink(event)" class="space-y-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 block">
                            Correo electrónico
                        </label>
                        <div class="flex items-center gap-3 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3.5 bg-white/50 dark:bg-dark-800/50 focus-within:border-brand-500 focus-within:ring-4 focus-within:ring-brand-500/10 transition-all group">
                            <i class="ph-fill ph-envelope text-slate-400 dark:text-slate-500 group-focus-within:text-brand-500 transition-colors text-lg"></i>
                            <input type="email" placeholder="tucorreo@email.com" required
                                   class="flex-1 bg-transparent text-sm font-medium outline-none text-slate-900 dark:text-white placeholder-slate-400">
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full bg-brand-500 hover:bg-brand-600 text-white font-bold py-4 rounded-2xl flex items-center justify-center gap-2 transition-all hover:scale-[1.02] active:scale-95 shadow-lg shadow-brand-500/30 text-base">
                        <i class="ph-bold ph-paper-plane-tilt text-lg"></i> Enviar link de recuperación
                    </button>
                </form>
            </div>

            {{-- Confirmación (oculta) --}}
            <div id="form-ok" class="hidden text-center py-4">
                <div class="w-16 h-16 rounded-2xl bg-brand-500/10 flex items-center justify-center mx-auto mb-4">
                    <i class="ph-fill ph-check-circle text-brand-500 text-4xl"></i>
                </div>
                <h2 class="font-extrabold text-slate-900 dark:text-white text-xl mb-2">¡Link enviado!</h2>
                <p class="text-slate-500 dark:text-slate-400 text-sm mb-6">
                    Revisá tu bandeja de entrada. El link es válido por <strong class="text-slate-700 dark:text-white">30 minutos</strong>.
                </p>
                <p class="text-xs text-slate-400">¿No llegó? Revisá tu carpeta de spam o</p>
                <button onclick="reenviar()" class="text-brand-500 hover:text-brand-600 font-bold text-sm transition-colors mt-1">
                    reenviar el correo
                </button>
            </div>

        </div>

        <div class="text-center mt-6">
            <a href="/login" class="text-sm font-semibold text-slate-500 dark:text-slate-400 hover:text-brand-500 transition-colors flex items-center justify-center gap-1">
                <i class="ph-bold ph-arrow-left"></i> Volver al inicio de sesión
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function enviarLink(e) {
        e.preventDefault();
        document.getElementById('form-email').classList.add('hidden');
        document.getElementById('form-ok').classList.remove('hidden');
    }
    function reenviar() {
        const btn = event.target;
        btn.textContent = '¡Reenviado!';
        btn.disabled = true;
        setTimeout(() => { btn.textContent = 'reenviar el correo'; btn.disabled = false; }, 3000);
    }
</script>
@endpush

@endsection
