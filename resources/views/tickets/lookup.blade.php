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
                $phone = $ticket->customer?->phone;
                $maskedPhone = $phone
                    ? str_repeat('*', max(0, mb_strlen($phone) - 3)).mb_substr($phone, -3)
                    : 'No disponible';
                $documentNumber = $ticket->customer?->document_number;
                $maskedDocument = $documentNumber
                    ? str_repeat('*', max(0, mb_strlen($documentNumber) - 3)).mb_substr($documentNumber, -3)
                    : 'No disponible';
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
                        <div class="mt-2 flex items-center gap-2">
                            <span data-phone-display class="font-mono text-lg font-bold text-cyan-300">{{ $maskedPhone }}</span>
                            @if($phone)
                                <button type="button" data-phone-toggle data-phone="{{ $phone }}" data-masked-phone="{{ $maskedPhone }}" aria-label="Mostrar número completo" aria-pressed="false" title="Mostrar número completo" class="inline-grid h-9 w-9 place-items-center rounded-lg border border-slate-700 text-slate-300 transition hover:border-cyan-400 hover:text-cyan-200">
                                    <svg aria-hidden="true" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.5-6 9.75-6 9.75 6 9.75 6-3.5 6-9.75 6S2.25 12 2.25 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            @endif
                        </div>
                        <p class="mt-4 text-xs font-bold uppercase tracking-wider text-slate-500">DNI</p>
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
            <script>
                document.querySelectorAll('[data-phone-toggle]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const isVisible = button.getAttribute('aria-pressed') === 'true';
                        const display = button.parentElement.querySelector('[data-phone-display]');
                        display.textContent = isVisible ? button.dataset.maskedPhone : button.dataset.phone;
                        button.setAttribute('aria-pressed', String(!isVisible));
                        button.setAttribute('aria-label', isVisible ? 'Mostrar número completo' : 'Ocultar número completo');
                        button.setAttribute('title', isVisible ? 'Mostrar número completo' : 'Ocultar número completo');
                    });
                });
            </script>
        @endif
    </section>
</x-layouts.app>
