<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Tienda Siete Sorteos{{ $nextDraw ? ' | '.$nextDraw->title : '' }}</title>
    <meta name="description" content="Participa en los sorteos de Tienda Siete Market & Licorería. Revisa premios, registra tu participación y verifica tus tickets.">
    <meta name="theme-color" content="#006b5e">
    <link rel="icon" type="image/png" href="{{ asset('brand/icono-tiendasiete.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('brand/tienda-siete.css') }}">
</head>
<body class="min-h-screen overflow-x-hidden bg-slate-950 text-white selection:bg-cyan-300 selection:text-slate-950">
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="absolute -left-28 top-10 h-80 w-80 rounded-full bg-cyan-500/20 blur-3xl"></div>
        <div class="absolute -right-24 top-80 h-96 w-96 rounded-full bg-violet-500/15 blur-3xl"></div>
    </div>

    <main class="relative mx-auto max-w-6xl px-5 py-6 sm:px-8 sm:py-10">
        <nav class="flex items-center justify-between gap-4">
            <a href="{{ route('public.next-draw') }}"><img src="{{ asset('brand/perfil-log-blanco.png') }}" alt="Tienda Siete Market & Licorería" class="tienda-siete-wordmark h-10 sm:h-12"></a>
            <a href="{{ route('public.ticket-verification') }}" class="rounded-lg border border-cyan-400/40 px-3 py-2 text-xs font-bold text-cyan-200 transition hover:bg-cyan-400/10">Verificar tickets</a>
        </nav>

        @if(session('error'))
            <div class="mt-6 rounded-2xl border border-red-400/30 bg-red-500/10 px-5 py-4 text-sm text-red-100">{{ session('error') }}</div>
        @endif

        @if($nextDraw)
            @php
                $activePrizes = $nextDraw->prizes;
                $subtotal = $activePrizes->sum('price');
                $comboTotal = max(0, $subtotal - $nextDraw->combo_discount);
                $registrationClosed = $nextDraw->isTicketRegistrationClosed();
            @endphp

            <section class="mt-10 grid overflow-hidden rounded-[2rem] border border-white/10 bg-slate-900/80 shadow-2xl shadow-cyan-950/30 lg:grid-cols-[1.1fr_.9fr]">
                <div class="relative min-h-[330px] overflow-hidden lg:min-h-[560px]">
                    @if($nextDraw->flyer_path)
                        <img src="{{ Storage::url($nextDraw->flyer_path) }}" alt="Flyer de {{ $nextDraw->title }}" class="absolute inset-0 h-full w-full object-cover">
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/20 to-cyan-950/30"></div>
                    <div class="absolute bottom-0 left-0 p-7 sm:p-10">
                        <span class="inline-flex rounded-full border border-cyan-300/40 bg-cyan-300/10 px-3 py-1 text-xs font-bold uppercase tracking-[.18em] text-cyan-200">Próximo sorteo</span>
                        <p class="mt-4 text-sm font-medium text-slate-200">Participa de forma segura. Tu comprobante será revisado antes de aprobar tus tickets.</p>
                    </div>
                </div>

                <div class="flex flex-col p-7 sm:p-10">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-[.2em] text-cyan-300">Estás participando en</p>
                        <h1 class="mt-3 text-4xl font-black leading-tight tracking-tight sm:text-5xl">{{ $nextDraw->title }}</h1>
                        <div class="mt-6 grid gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl border border-slate-700/80 bg-slate-950/60 p-4"><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Fecha del sorteo</p><p class="mt-1 font-bold text-slate-100">{{ $nextDraw->draw_date?->format('d/m/Y') }}</p><p class="text-sm text-cyan-200">{{ $nextDraw->draw_time }}</p></div>
                            <div class="rounded-2xl border border-slate-700/80 bg-slate-950/60 p-4"><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Premios activos</p><p class="mt-1 text-2xl font-black text-cyan-300">{{ $activePrizes->count() }}</p><p class="text-sm text-slate-400">Opciones disponibles</p></div>
                        </div>
                        @if($nextDraw->transmission)
                            <p class="mt-5 flex items-center gap-2 text-sm text-slate-300"><span class="h-2 w-2 rounded-full bg-red-400"></span> Transmisión: {{ $nextDraw->transmission }}</p>
                        @endif
                    </div>

                    <div class="mt-8">
                        @if($registrationClosed)
                            <div class="rounded-2xl border border-red-400/30 bg-red-500/10 px-5 py-4"><p class="font-black text-red-100">Registro cerrado</p><p class="mt-1 text-sm text-red-200/80">El registro finalizó 30 minutos antes del inicio del sorteo.</p></div>
                        @elseif($activePrizes->isEmpty())
                            <div class="rounded-2xl border border-amber-400/30 bg-amber-500/10 px-5 py-4 text-amber-100">Los premios estarán disponibles muy pronto.</div>
                        @else
                            <a href="{{ route('public.participation.create', $nextDraw) }}" class="flex w-full items-center justify-center gap-3 rounded-2xl bg-cyan-400 px-5 py-4 text-lg font-black text-slate-950 shadow-lg shadow-cyan-400/20 transition hover:-translate-y-0.5 hover:bg-cyan-300">Participar <span aria-hidden="true">→</span></a>
                            <p class="mt-3 text-center text-xs text-slate-400">Elige tu opción, completa tus datos y sube tu comprobante.</p>
                        @endif
                    </div>
                </div>
            </section>

            @if($activePrizes->isNotEmpty())
                <section class="mt-10">
                    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-end"><div><p class="text-xs font-bold uppercase tracking-[.2em] text-cyan-400">Opciones para participar</p><h2 class="mt-2 text-3xl font-black">Elige tu premio</h2></div><p class="text-sm text-slate-400">Cada opción genera un ticket para ese premio.</p></div>
                    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($activePrizes as $prize)
                            <article class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/90">
                                @if($prize->image_path)<img src="{{ Storage::url($prize->image_path) }}" alt="{{ $prize->name }}" class="aspect-square w-full object-cover">@endif
                                <div class="p-5"><p class="font-black text-lg">{{ $prize->name }}</p>@if($prize->description)<p class="mt-2 min-h-10 text-sm text-slate-400">{{ $prize->description }}</p>@endif<p class="mt-4 text-xl font-black text-cyan-300">S/ {{ number_format($prize->price, 2) }}</p></div>
                            </article>
                        @endforeach
                    </div>
                    @if($activePrizes->count() > 1)
                        <article class="mt-5 flex flex-col justify-between gap-4 rounded-2xl border border-emerald-400/30 bg-emerald-400/10 p-6 sm:flex-row sm:items-center"><div><p class="font-black text-emerald-100">Combo completo</p><p class="mt-1 text-sm text-emerald-100/75">Participa por todos los premios y recibe un descuento de S/ {{ number_format($nextDraw->combo_discount, 2) }}.</p></div><p class="text-2xl font-black text-emerald-200">S/ {{ number_format($comboTotal, 2) }}</p></article>
                    @endif
                </section>
            @endif
        @else
            <section class="mt-20 rounded-[2rem] border border-slate-800 bg-slate-900/70 px-7 py-20 text-center shadow-2xl shadow-cyan-950/20"><img src="{{ asset('brand/icono-tiendasiete.png') }}" alt="Tienda Siete" class="tienda-siete-icon mx-auto h-16 w-16"><p class="mt-7 text-xs font-bold uppercase tracking-[.2em] text-cyan-400">Sorteos Tienda Siete</p><h1 class="mt-3 text-3xl font-black sm:text-4xl">Muy pronto anunciaremos el siguiente sorteo.</h1><p class="mx-auto mt-4 max-w-lg text-slate-400">Regresa pronto para conocer los premios y participar.</p></section>
        @endif
        <x-public.footer />
    </main>
</body>
</html>
