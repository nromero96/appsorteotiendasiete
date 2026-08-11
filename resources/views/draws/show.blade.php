<x-layouts.app>
    <a href="{{ route('draws.index') }}" class="text-sm text-cyan-300">← Volver a sorteos</a>

    <section class="mt-5 rounded-2xl border border-slate-800 bg-slate-900 p-6">
        <p class="text-cyan-300">{{ $draw->draw_date?->format('d/m/Y') }} · {{ $draw->draw_time }}</p>
        <h1 class="mt-1 text-3xl font-black">{{ $draw->title }}</h1>
        <p class="mt-2 text-slate-400">{{ $draw->transmission }}</p>
        @php($registrationDeadline = $draw->ticketRegistrationDeadline())
        @if($registrationDeadline)
            @if($draw->isTicketRegistrationClosed())
                <p class="mt-3 inline-flex rounded-lg border border-red-400/40 bg-red-400/10 px-3 py-2 text-sm font-bold text-red-200">Registro de tickets cerrado desde {{ $registrationDeadline->format('d/m/Y H:i') }}</p>
            @else
                <p class="mt-3 inline-flex rounded-lg border border-emerald-400/40 bg-emerald-400/10 px-3 py-2 text-sm font-bold text-emerald-200">Registro habilitado hasta {{ $registrationDeadline->format('d/m/Y H:i') }}</p>
            @endif
        @endif
        @if($draw->flyer_path)
            <img src="{{ Storage::url($draw->flyer_path) }}" class="mt-5 h-48 w-full rounded-xl border border-slate-700 object-cover md:h-64" alt="Flyer de {{ $draw->title }}">
        @endif
        <div class="mt-5 flex gap-3">
            @can('registrar tickets')
                @if(! $draw->isTicketRegistrationClosed())
                    <a href="{{ route('tickets.create', $draw) }}" class="inline-block rounded-xl bg-cyan-400 px-4 py-2 font-bold text-slate-950">+ Registrar tickets</a>
                @endif
            @endcan
            @can('ver ventas')<a href="{{ route('draws.report', $draw) }}" class="inline-block rounded-xl border border-emerald-400/40 px-4 py-2 font-bold text-emerald-200">Reporte de ventas</a>@endcan
            @can('imprimir talonarios')<a href="{{ route('tickets.print', $draw) }}" target="_blank" class="inline-block rounded-xl border border-slate-300 px-4 py-2 font-bold text-white">Imprimir todos ({{ $draw->tickets->where('status', 'aprobado')->count() }})</a>@endcan
            @can('gestionar sorteos')<a href="{{ route('draws.edit', $draw) }}" class="inline-block rounded-xl border border-slate-700 px-4 py-2 font-bold text-slate-200">Editar sorteo</a>@endcan
        </div>
    </section>

    <div class="mt-8 flex items-center justify-between">
        <h2 class="text-xl font-black">Premios</h2>
        @can('gestionar premios')<a href="{{ route('prizes.create', $draw) }}" class="rounded-lg bg-emerald-400 px-3 py-2 text-sm font-bold text-slate-950">+ Agregar premio</a>@endcan
    </div>
    <div class="mt-4 grid gap-4 md:grid-cols-3">
        @forelse($draw->prizes as $prize)
            <article class="rounded-xl border border-slate-800 bg-slate-900 p-4">
                @if($prize->image_path)<img src="{{ Storage::url($prize->image_path) }}" class="mb-3 aspect-square w-full rounded-lg object-cover" alt="{{ $prize->name }}">@endif
                <h3 class="font-bold">{{ $prize->name }}</h3>
                <p class="mt-1 text-sm text-slate-400">{{ $prize->description }}</p>
                <p class="mt-3 font-black text-cyan-300">S/ {{ number_format($prize->price, 2) }}</p>
                <div class="mt-3 flex flex-wrap gap-3">
                    @can('imprimir talonarios')<a href="{{ route('tickets.print', ['draw' => $draw, 'prize_id' => $prize->id]) }}" target="_blank" class="text-xs font-bold text-slate-200">Imprimir talonarios ({{ $draw->tickets->where('status', 'aprobado')->where('prize_id', $prize->id)->count() }})</a>@endcan
                    @can('gestionar premios')<a href="{{ route('prizes.edit', [$draw, $prize]) }}" class="text-xs text-cyan-300">Editar</a><form method="POST" action="{{ route('prizes.destroy', [$draw, $prize]) }}">@csrf @method('DELETE')<button class="text-xs text-red-300">Eliminar</button></form>@endcan
                </div>
            </article>
        @empty
            <p class="text-sm text-slate-400">Todavía no hay premios registrados.</p>
        @endforelse
    </div>

    <h2 class="mt-8 text-xl font-black">Registro de ventas</h2>
    <div class="mt-4 overflow-x-auto rounded-xl border border-slate-800">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-900 text-slate-400"><tr><th class="p-3">Ticket</th><th>Premio</th><th>Cliente</th><th>Pago / transacción</th><th>Voucher</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
                @forelse($draw->tickets as $ticket)
                    <tr class="border-t border-slate-800">
                        <td class="p-3 font-bold">#{{ $ticket->ticket_number }}<small class="mt-1 block font-normal text-slate-500">{{ strtoupper($ticket->purchase_type) }}</small></td>
                        <td><span class="font-semibold">{{ $ticket->prize?->name ?? 'Premio no disponible' }}</span><small class="mt-1 block text-slate-500">{{ $ticket->prize_id }}</small></td>
                        <td>{{ $ticket->customer->full_name }}<small class="block text-slate-400">{{ $ticket->customer->phone }}</small></td>
                        <td>{{ $ticket->payment_method }}<small class="block text-slate-400">{{ $ticket->transaction_id }}</small></td>
                        <td>@if($ticket->voucher_path)<a class="text-cyan-300" target="_blank" href="{{ Storage::url($ticket->voucher_path) }}">Ver voucher</a>@else — @endif</td>
                        <td>@php($statusClass = match($ticket->status) {'aprobado' => 'border-emerald-400/40 bg-emerald-400/15 text-emerald-200', 'rechazado' => 'border-red-400/40 bg-red-400/15 text-red-200', default => 'border-amber-400/40 bg-amber-400/15 text-amber-200'})<span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-bold uppercase {{ $statusClass }}">{{ $ticket->status }}</span></td>
                        <td>@if($ticket->status === 'aprobado')<div class="flex flex-col items-start gap-2"><a href="{{ route('tickets.share-image', $ticket) }}" class="inline-flex rounded-lg bg-emerald-400 px-3 py-2 text-xs font-bold text-slate-950 hover:bg-emerald-300">Descargar JPG</a>@can('imprimir talonarios')<a href="{{ route('tickets.print.single', [$draw, $ticket]) }}" target="_blank" class="inline-flex rounded-lg border border-slate-500 px-3 py-2 text-xs font-bold text-white">Imprimir</a>@endcan</div>@else — @endif</td>
                    </tr>
                @empty
                    <tr><td class="p-5 text-slate-400" colspan="7">No hay tickets registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.app>
