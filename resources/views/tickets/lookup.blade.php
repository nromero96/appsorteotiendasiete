<x-layouts.app>
    <section class="max-w-4xl">
        <p class="text-xs font-bold uppercase tracking-[.2em] text-cyan-400">Consulta interna</p>
        <h1 class="mt-2 text-3xl font-black">Buscar ticket</h1>
        <p class="mt-2 text-slate-400">Consulta un ticket por su número y confirma los datos de su participante.</p>

        <form method="GET" action="{{ route('tickets.lookup') }}" class="mt-7 flex flex-col gap-3 rounded-2xl border border-slate-800 bg-slate-900 p-5 sm:flex-row sm:items-end">
            <label class="block flex-1 text-sm font-bold text-slate-200">
                Número de ticket
                <input autofocus required inputmode="numeric" name="ticket_number" value="{{ $ticketNumber }}" class="mt-2 block w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-lg font-bold tracking-wide text-slate-100 outline-none transition placeholder:text-slate-600 focus:border-cyan-400" placeholder="Ej. 7000">
            </label>
            <button class="rounded-xl bg-cyan-400 px-6 py-3 font-black text-slate-950 transition hover:bg-cyan-300">Buscar</button>
        </form>

        @if($searched && ! $ticket)
            <section class="mt-6 rounded-2xl border border-amber-400/30 bg-amber-400/10 p-6 text-amber-100">
                <p class="font-black">No encontramos el ticket #{{ $ticketNumber ?: 'indicado' }}.</p>
                <p class="mt-1 text-sm text-amber-100/75">Verifica el número e inténtalo nuevamente.</p>
            </section>
        @endif

        @if($ticket)
            @php
                $statusClass = match($ticket->status) {
                    'aprobado' => 'border-emerald-400/30 bg-emerald-400/10 text-emerald-200',
                    'rechazado' => 'border-red-400/30 bg-red-400/10 text-red-200',
                    default => 'border-amber-400/30 bg-amber-400/10 text-amber-200',
                };
            @endphp
            <section class="mt-6 overflow-hidden rounded-2xl border border-slate-800 bg-slate-900 shadow-xl shadow-slate-950/20">
                <header class="flex flex-col gap-4 border-b border-slate-800 bg-slate-900/80 p-6 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[.2em] text-cyan-400">Ticket encontrado</p>
                        <h2 class="mt-2 text-4xl font-black">#{{ $ticket->ticket_number }}</h2>
                    </div>
                    <span class="inline-flex w-fit rounded-full border px-3 py-1.5 text-xs font-black uppercase tracking-wide {{ $statusClass }}">{{ $ticket->status }}</span>
                </header>

                <div class="grid gap-6 p-6 md:grid-cols-2">
                    <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-5">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Participante</p>
                        <p class="mt-2 text-xl font-black text-white">{{ $ticket->customer?->full_name ?? 'No disponible' }}</p>
                        <p class="mt-4 text-xs font-bold uppercase tracking-wider text-slate-500">Teléfono / WhatsApp</p>
                        <a href="tel:{{ $ticket->customer?->phone }}" class="mt-2 inline-block text-lg font-bold text-cyan-300 hover:text-cyan-200">{{ $ticket->customer?->phone ?? 'No disponible' }}</a>
                        <p class="mt-4 text-xs font-bold uppercase tracking-wider text-slate-500">DNI</p>
                        @php
                            $documentNumber = $ticket->customer?->document_number;
                            $maskedDocument = $documentNumber
                                ? str_repeat('*', max(0, mb_strlen($documentNumber) - 3)).mb_substr($documentNumber, -3)
                                : 'No disponible';
                        @endphp
                        <p class="mt-2 font-mono font-bold text-slate-200">{{ $maskedDocument }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-5">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Sorteo</p>
                        <p class="mt-2 text-xl font-black text-slate-100">{{ $ticket->draw?->title ?? 'No disponible' }}</p>
                        <p class="mt-5 text-xs font-bold uppercase tracking-wider text-slate-500">Fecha del sorteo</p>
                        <p class="mt-2 font-semibold text-cyan-200">{{ $ticket->draw?->draw_date?->format('d/m/Y') ?? 'Por confirmar' }} {{ $ticket->draw?->draw_time ? '· '.$ticket->draw->draw_time : '' }}</p>
                    </div>
                </div>
            </section>
        @endif
    </section>
</x-layouts.app>
