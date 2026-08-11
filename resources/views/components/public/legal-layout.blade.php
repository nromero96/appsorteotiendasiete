@props(['title', 'description'])
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Tienda Siete Sorteos | {{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="theme-color" content="#006b5e">
    <link rel="icon" type="image/png" href="{{ asset('brand/icono-tiendasiete.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('brand/tienda-siete.css') }}">
</head>
<body class="min-h-screen bg-slate-950 text-white selection:bg-cyan-300 selection:text-slate-950">
    <main class="relative mx-auto max-w-5xl px-5 py-8 sm:px-8 sm:py-12">
        <nav class="flex items-center justify-between gap-4"><a href="{{ route('public.next-draw') }}"><img src="{{ asset('brand/perfil-log-blanco.png') }}" alt="Tienda Siete Market & Licorería" class="tienda-siete-wordmark h-10 sm:h-12"></a><a href="{{ route('public.next-draw') }}" class="text-sm font-bold text-cyan-300 hover:text-cyan-200">← Volver al inicio</a></nav>
        <section class="mt-10 overflow-hidden rounded-[2rem] border border-slate-800 bg-slate-900 shadow-2xl shadow-cyan-950/20"><header class="border-b border-slate-800 bg-gradient-to-r from-cyan-400/15 to-violet-500/10 p-7 sm:p-10"><p class="text-xs font-bold uppercase tracking-[.2em] text-cyan-300">Información legal</p><h1 class="mt-3 text-3xl font-black sm:text-4xl">{{ $title }}</h1><p class="mt-3 text-sm text-slate-300">Última actualización: 10 de agosto de 2026</p></header><article class="space-y-7 p-7 text-sm leading-7 text-slate-300 sm:p-10">{{ $slot }}</article></section>
        <x-public.footer />
    </main>
</body>
</html>
