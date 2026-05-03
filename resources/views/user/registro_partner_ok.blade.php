@extends('layouts.app')
@section('title', 'Solicitud enviada | FutGo')
@section('content')
<div class="min-h-[calc(100vh-5rem)] flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full text-center">
        <div class="w-20 h-20 rounded-3xl bg-brand-500/10 border-2 border-brand-500/20 flex items-center justify-center mx-auto mb-6">
            <i class="ph-fill ph-check-circle text-brand-500 text-5xl"></i>
        </div>
        <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white mb-3">¡Solicitud enviada!</h1>
        <p class="text-slate-500 dark:text-slate-400 mb-6 leading-relaxed">
            Recibimos tu solicitud. Nuestro equipo la revisará y te contactará por
            <strong class="text-slate-700 dark:text-white">WhatsApp</strong>
            en menos de <strong class="text-slate-700 dark:text-white">48 horas hábiles</strong>
            para verificar los datos y activar tu cuenta.
        </p>
        <div class="glass-card rounded-2xl p-5 mb-6 text-left space-y-2">
            <p class="text-sm font-semibold text-slate-700 dark:text-white flex items-center gap-2">
                <i class="ph-fill ph-check text-brand-500"></i> Complejo registrado en estado pendiente
            </p>
            <p class="text-sm font-semibold text-slate-700 dark:text-white flex items-center gap-2">
                <i class="ph-fill ph-check text-brand-500"></i> El Admin recibirá una notificación
            </p>
            <p class="text-sm font-semibold text-slate-700 dark:text-white flex items-center gap-2">
                <i class="ph-fill ph-check text-brand-500"></i> Al aprobar, recibirás acceso por email
            </p>
        </div>
        <a href="/" class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white font-bold px-8 py-3 rounded-xl transition-colors shadow-lg shadow-brand-500/20">
            <i class="ph-bold ph-house"></i> Volver al inicio
        </a>
    </div>
</div>
@endsection
