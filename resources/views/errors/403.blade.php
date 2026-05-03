<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sin permiso | FutGo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: { colors: { brand: { 500: '#22c55e', 600: '#16a34a' }, dark: { 900: '#0f172a', 950: '#020617' } } } }
        }
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        }
    </script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Outfit', sans-serif; } </style>
</head>
<body class="min-h-screen bg-slate-50 dark:bg-dark-950 flex items-center justify-center px-4">
    <div class="text-center max-w-md">

        {{-- Número error animado --}}
        <div class="relative mb-6 inline-block">
            <div class="text-[120px] font-black text-slate-100 dark:text-white/5 leading-none select-none">403</div>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-24 h-24 rounded-3xl bg-red-500/10 border-2 border-red-500/20 flex items-center justify-center">
                    <i class="ph-fill ph-shield-warning text-red-500 text-5xl"></i>
                </div>
            </div>
        </div>

        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white mb-2">Acceso denegado</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mb-8 leading-relaxed">
            No tenés permiso para ver esta página. Si creés que es un error, contactá al administrador.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="/"
               class="flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-brand-500 hover:bg-brand-600 text-white font-bold transition-all hover:scale-105 shadow-lg shadow-brand-500/20">
                <i class="ph-bold ph-house"></i> Ir al inicio
            </a>
            <a href="/login"
               class="flex items-center justify-center gap-2 px-6 py-3 rounded-2xl border border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-300 font-semibold hover:border-brand-500 hover:text-brand-500 transition-colors">
                <i class="ph-bold ph-sign-in"></i> Iniciar sesión
            </a>
        </div>

        <p class="mt-8 text-xs text-slate-400">
            <a href="/" class="font-bold text-brand-500">Fut<span class="text-slate-900 dark:text-white">Go</span></a>
            · Error 403
        </p>
    </div>
</body>
</html>
