<x-layouts.app>
    <section class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
            <p class="text-xs font-bold uppercase tracking-[.2em] text-cyan-400">Administración</p>
            <h1 class="mt-2 text-3xl font-black">Dashboard</h1>
            <p class="mt-1 text-slate-400">Resumen general de sorteos y ventas.</p>
        </div>
        @can('gestionar sorteos')
            <a href="{{ route('draws.create') }}" class="rounded-xl bg-cyan-400 px-4 py-2.5 text-center font-bold text-slate-950 transition hover:bg-cyan-300">+ Crear sorteo</a>
        @endcan
    </section>

    <section class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-slate-800 bg-slate-900 p-5"><p class="text-sm text-slate-400">Sorteos creados</p><p class="mt-2 text-3xl font-black">{{ $stats['draws'] }}</p></article>
        <article class="rounded-2xl border border-slate-800 bg-slate-900 p-5"><p class="text-sm text-slate-400">Tickets emitidos</p><p class="mt-2 text-3xl font-black">{{ $stats['tickets'] }}</p></article>
        <article class="rounded-2xl border border-slate-800 bg-slate-900 p-5"><p class="text-sm text-slate-400">Ventas aprobadas</p><p class="mt-2 text-3xl font-black text-emerald-300">S/ {{ number_format($stats['approved_sales'], 2) }}</p></article>
        <article class="rounded-2xl border border-slate-800 bg-slate-900 p-5"><p class="text-sm text-slate-400">Tickets pendientes</p><p class="mt-2 text-3xl font-black text-amber-300">{{ $stats['pending_tickets'] }}</p></article>
    </section>

    <section class="mt-8 grid gap-6 lg:grid-cols-[1.1fr_.9fr]">
        <article class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900">
            <div class="flex items-center justify-between border-b border-slate-800 px-5 py-4"><h2 class="font-black">Últimos tickets</h2><a href="{{ route('draws.index') }}" class="text-sm text-cyan-300">Ver sorteos</a></div>
            <div class="divide-y divide-slate-800">
                @forelse($recentTickets as $ticket)
                    @php($statusClass = match($ticket->status) {'aprobado' => 'text-emerald-300', 'rechazado' => 'text-red-300', default => 'text-amber-300'})
                    <div class="flex items-center justify-between gap-4 px-5 py-4">
                        <div><p class="font-bold">#{{ $ticket->ticket_number }} · {{ $ticket->customer->full_name }}</p><p class="mt-1 text-sm text-slate-400">{{ $ticket->draw->title }} · {{ $ticket->prize?->name }}</p></div>
                        <div class="text-right"><p class="font-bold text-cyan-300">S/ {{ number_format($ticket->total_amount, 2) }}</p><p class="mt-1 text-xs font-bold uppercase {{ $statusClass }}">{{ $ticket->status }}</p></div>
                    </div>
                @empty
                    <p class="px-5 py-8 text-sm text-slate-400">Todavía no hay tickets registrados.</p>
                @endforelse
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900">
            <div class="flex items-center justify-between border-b border-slate-800 px-5 py-4"><h2 class="font-black">Próximos sorteos</h2><a href="{{ route('draws.index') }}" class="text-sm text-cyan-300">Ver todos</a></div>
            <div class="divide-y divide-slate-800">
                @forelse($upcomingDraws as $event)
                    <div class="flex gap-4 p-4">
                        @if($event->flyer_path)<img src="{{ Storage::url($event->flyer_path) }}" class="h-16 w-16 shrink-0 rounded-lg object-cover" alt="{{ $event->title }}">@endif
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-cyan-300">{{ $event->draw_date?->format('d/m/Y') }} · {{ $event->draw_time }}</p>
                            <h3 class="mt-1 truncate font-black">{{ $event->title }}</h3>
                            <p class="mt-1 text-xs text-slate-400">{{ $event->prizes_count }} premios · {{ $event->tickets_count }} tickets</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @can('registrar tickets')
                                    @if(! $event->isTicketRegistrationClosed())
                                        <a href="{{ route('tickets.create', $event) }}" class="rounded-md bg-cyan-400 px-2.5 py-1.5 text-xs font-bold text-slate-950">Crear ticket</a>
                                    @else
                                        <span class="rounded-md border border-red-400/40 px-2.5 py-1.5 text-xs font-bold text-red-200">Cerrado</span>
                                    @endif
                                @endcan
                                <a href="{{ route('draws.show', $event) }}" class="rounded-md border border-slate-700 px-2.5 py-1.5 text-xs font-bold text-cyan-300">Detalle</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="p-5 text-sm text-slate-400">No hay sorteos próximos programados.</p>
                @endforelse
            </div>
        </article>
    </section>
</x-layouts.app>
