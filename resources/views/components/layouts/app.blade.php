<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ config('app.name') }} | Administración</title>
    <meta name="description" content="Panel administrativo de sorteos de Tienda Siete Market & Licorería.">
    <meta name="theme-color" content="#006b5e">
    <link rel="icon" type="image/png" href="{{ asset('brand/icono-tiendasiete.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('brand/tienda-siete.css') }}">
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <header class="border-b border-slate-800 bg-slate-900/90 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-5">
            <div class="flex items-center gap-5">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3"><img src="{{ asset('brand/icono-tiendasiete.png') }}" alt="Tienda Siete" class="tienda-siete-icon h-10 w-10"><span class="text-sm font-black uppercase tracking-[.12em] text-white">Tienda <span class="text-cyan-300">Siete</span><small class="mt-0.5 block text-[10px] font-bold tracking-widest text-slate-300">PANEL DE SORTEOS</small></span></a>
                <a href="{{ route('draws.index') }}" class="text-sm font-semibold text-slate-300 hover:text-white">Sorteos</a>
                <a href="{{ route('tickets.lookup') }}" class="text-sm font-semibold text-slate-300 hover:text-white">Consultar ticket</a>
                @can('ver ventas')
                    <a href="{{ route('sales.index') }}" class="text-sm font-semibold text-slate-300 hover:text-white">Ventas</a>
                @endcan
                @can('gestionar usuarios')
                    <a href="{{ route('users.index') }}" class="text-sm font-semibold text-slate-300 hover:text-white">Usuarios</a>
                @endcan
            </div>
            <div class="flex items-center gap-3">
                @can('gestionar sorteos')
                    <a href="{{ route('draws.create') }}" class="rounded-lg border border-cyan-400/40 px-3 py-1.5 text-sm font-bold text-cyan-200">Nuevo sorteo</a>
                @endcan
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-sm text-slate-300 hover:text-white">Cerrar sesión</button>
                </form>
            </div>
        </div>
    </header>
    <main class="mx-auto max-w-6xl px-6 py-8">
        @if(session('success'))
            <div class="mb-6 rounded-xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-6 rounded-xl border border-red-400/30 bg-red-400/10 px-4 py-3 text-sm text-red-200">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-6 rounded-xl border border-red-400/30 bg-red-400/10 px-4 py-3 text-sm text-red-200">
                <p class="font-bold">Revisa los datos del formulario.</p>
                <ul class="mt-1 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        {{ $slot }}
    </main>
</body>
</html>
