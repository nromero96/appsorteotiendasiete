<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Tienda Siete Sorteos | Verificar tickets</title>
    <meta name="description" content="Consulta tus tickets de Tienda Siete usando tu número de DNI.">
    <meta name="theme-color" content="#006b5e">
    <link rel="icon" type="image/png" href="{{ asset('brand/icono-tiendasiete.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('brand/tienda-siete.css') }}">
</head>
<body class="min-h-screen bg-slate-950 text-white selection:bg-cyan-300 selection:text-slate-950">
    <div class="pointer-events-none fixed inset-0 overflow-hidden"><div class="absolute -left-24 top-20 h-80 w-80 rounded-full bg-cyan-500/15 blur-3xl"></div><div class="absolute -right-20 bottom-0 h-96 w-96 rounded-full bg-violet-500/15 blur-3xl"></div></div>
    <main class="relative mx-auto max-w-4xl px-5 py-8 sm:px-8 sm:py-12">
        <nav class="flex items-center justify-between gap-4"><a href="{{ route('public.next-draw') }}"><img src="{{ asset('brand/perfil-log-blanco.png') }}" alt="Tienda Siete Market & Licorería" class="tienda-siete-wordmark h-10 sm:h-12"></a><a href="{{ route('public.next-draw') }}" class="text-sm font-bold text-cyan-300 hover:text-cyan-200">← Volver</a></nav>

        <section class="mt-12 overflow-hidden rounded-[2rem] border border-slate-800 bg-slate-900 shadow-2xl shadow-cyan-950/20">
            <div class="bg-gradient-to-r from-cyan-400/15 to-violet-500/10 p-7 sm:p-10"><p class="text-xs font-bold uppercase tracking-[.2em] text-cyan-300">Consulta pública</p><h1 class="mt-3 text-3xl font-black sm:text-4xl">Verifica tus tickets</h1><p class="mt-3 max-w-xl text-slate-300">Ingresa tu DNI para conocer el sorteo, premio y estado de tus participaciones.</p></div>
            <form method="GET" action="{{ route('public.ticket-verification') }}" class="p-7 sm:p-10"><div class="grid gap-4 sm:grid-cols-[1fr_auto]"><label class="block text-sm font-bold text-slate-200">Número de DNI<input required name="document_number" value="{{ old('document_number', request('document_number')) }}" inputmode="numeric" class="mt-2 block w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 outline-none transition placeholder:text-slate-600 focus:border-cyan-400" placeholder="Ej. 12345678"></label><button class="self-end rounded-xl bg-cyan-400 px-6 py-3 font-black text-slate-950 transition hover:bg-cyan-300">Buscar mis tickets</button></div></form>
        </section>

        @if($errors->any())
            <div class="mt-6 rounded-2xl border border-red-400/30 bg-red-500/10 px-5 py-4 text-sm text-red-100">{{ $errors->first() }}</div>
        @endif

        @if($searched)
            @php
                $currentTickets = $tickets->filter(fn ($ticket) => ! $ticket->draw->isPast());
                $pastTickets = $tickets->filter(fn ($ticket) => $ticket->draw->isPast());
            @endphp
            <section class="mt-8">
                <div class="flex items-center justify-between"><h2 class="text-xl font-black">Resultado de búsqueda</h2><span class="rounded-full border border-slate-700 px-3 py-1 text-xs font-bold text-slate-300">{{ $tickets->count() }} encontrado(s)</span></div>
                @if($tickets->isEmpty())
                    <div class="mt-4 rounded-2xl border border-slate-800 bg-slate-900 p-8 text-center"><p class="text-lg font-black">No encontramos tickets activos o pendientes con este DNI.</p><p class="mt-2 text-sm text-slate-400">Verifica que el número de documento esté escrito correctamente.</p></div>
                @else
                    @if($currentTickets->isNotEmpty())
                        <div class="mt-4 space-y-4">@foreach($currentTickets as $ticket) @include('public.partials.ticket-card', ['ticket' => $ticket, 'past' => false]) @endforeach</div>
                    @endif
                    @if($pastTickets->isNotEmpty())
                        <div class="mt-9 flex items-center gap-3"><span class="h-px flex-1 bg-slate-600"></span><p class="text-xs font-black uppercase tracking-[.18em] text-slate-400">Eventos finalizados</p><span class="h-px flex-1 bg-slate-600"></span></div>
                        <div class="mt-4 space-y-4">@foreach($pastTickets as $ticket) @include('public.partials.ticket-card', ['ticket' => $ticket, 'past' => true]) @endforeach</div>
                    @endif
                @endif
            </section>
        @endif
        <x-public.footer />
    </main>
</body>
</html>
