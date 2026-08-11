<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Tienda Siete Sorteos | Solicitud enviada</title>
    <meta name="description" content="Tu solicitud de participación fue recibida por Tienda Siete para su validación.">
    <meta name="theme-color" content="#006b5e">
    <link rel="icon" type="image/png" href="{{ asset('brand/icono-tiendasiete.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('brand/tienda-siete.css') }}">
</head>
<body class="min-h-screen bg-slate-950 text-white">
    <main class="mx-auto flex min-h-screen max-w-2xl items-center px-5 py-12">
        <section class="w-full overflow-hidden rounded-[2rem] border border-emerald-400/25 bg-slate-900 shadow-2xl shadow-emerald-950/20">
            <div class="bg-gradient-to-r from-emerald-400/20 to-cyan-400/10 p-8 text-center sm:p-10"><img src="{{ asset('brand/perfil-log-blanco.png') }}" alt="Tienda Siete Market & Licorería" class="tienda-siete-wordmark mx-auto h-10"><div class="mx-auto mt-6 grid h-16 w-16 place-items-center rounded-full bg-emerald-400 text-3xl font-black text-slate-950">✓</div><p class="mt-6 text-xs font-bold uppercase tracking-[.2em] text-emerald-300">Solicitud recibida</p><h1 class="mt-3 text-3xl font-black">¡Gracias por participar!</h1><p class="mt-3 text-slate-300">Tu comprobante fue enviado para revisión. Tus tickets serán aprobados cuando el administrador confirme el pago.</p></div>
            <div class="p-6 sm:p-8"><div class="rounded-2xl border border-slate-700 bg-slate-950/70 p-5"><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Sorteo</p><p class="mt-1 text-lg font-black">{{ $sale->draw->title }}</p><div class="mt-5 border-t border-slate-800 pt-4"><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Opciones solicitadas</p><div class="mt-3 space-y-2">@foreach($sale->tickets as $ticket)<div class="flex items-center justify-between gap-3 text-sm"><span>{{ $ticket->prize?->name }}</span><span class="font-bold text-cyan-300">Ticket pendiente</span></div>@endforeach</div></div><div class="mt-5 flex items-end justify-between border-t border-slate-800 pt-4"><span class="text-sm text-slate-400">Total solicitado</span><span class="text-2xl font-black text-emerald-300">S/ {{ number_format($sale->total_amount, 2) }}</span></div></div><div class="mt-6 grid gap-3 sm:grid-cols-2"><a href="{{ route('public.ticket-verification') }}" class="rounded-xl bg-cyan-400 px-5 py-3 text-center font-bold text-slate-950 transition hover:bg-cyan-300">Verificar tickets</a><a href="{{ route('public.next-draw') }}" class="rounded-xl border border-slate-700 px-5 py-3 text-center font-bold text-slate-200 transition hover:border-cyan-400 hover:text-cyan-200">Volver al inicio</a></div></div>
        </section>
    </main>
    <div class="mx-auto max-w-2xl px-5 pb-8"><x-public.footer /></div>
</body>
</html>
