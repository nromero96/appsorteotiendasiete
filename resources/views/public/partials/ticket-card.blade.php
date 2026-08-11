@php
    $statusClass = $ticket->status === 'aprobado'
        ? 'border-emerald-400/40 bg-emerald-400/10 text-emerald-200'
        : 'border-amber-400/40 bg-amber-400/10 text-amber-200';
    $isActive = $ticket->status === 'aprobado';
    $cardClass = $past
        ? 'border-slate-300 bg-white text-slate-950 shadow-lg shadow-black/10'
        : ($isActive
            ? 'border-emerald-400/40 bg-emerald-400/10 text-white shadow-lg shadow-emerald-950/20'
            : 'border-amber-400/40 bg-amber-400/10 text-white shadow-lg shadow-amber-950/20');
@endphp

<article class="rounded-2xl border p-5 sm:p-6 {{ $cardClass }}">
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <p class="text-xs font-bold uppercase tracking-[.18em] {{ $past ? 'text-slate-600' : 'text-cyan-400' }}">{{ $ticket->draw->title }}</p>
                @if($past)<span class="rounded-full border border-slate-400 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-slate-700">Sorteo finalizado</span>@endif
            </div>
            <h3 class="mt-2 text-2xl font-black">Ticket #{{ $ticket->ticket_number }}</h3>
            <p class="mt-1 {{ $past ? 'text-slate-600' : 'text-slate-400' }}">Premio: <span class="font-bold {{ $past ? 'text-slate-900' : 'text-slate-200' }}">{{ $ticket->prize?->name ?? 'Sin premio asignado' }}</span></p>
        </div>
        <span class="inline-flex w-fit rounded-full border px-3 py-1.5 text-xs font-black uppercase {{ $statusClass }}">{{ $isActive ? 'Activo' : 'Pendiente' }}</span>
    </div>
    <div class="mt-5 grid gap-3 border-t pt-4 text-sm sm:grid-cols-3 {{ $past ? 'border-slate-300' : 'border-slate-800' }}">
        <div><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Fecha de sorteo</p><p class="mt-1 font-semibold">{{ $ticket->draw->draw_date?->format('d/m/Y') }} {{ $ticket->draw->draw_time }}</p></div>
        <div><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Tipo de compra</p><p class="mt-1 font-semibold">{{ $ticket->purchase_type === 'combo' ? 'Combo completo' : 'Ticket individual' }}</p></div>
        <div><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Verificación</p><p class="mt-1 font-semibold {{ $isActive ? 'text-emerald-500' : 'text-amber-500' }}">{{ $isActive ? 'Ticket activo' : 'Pago en revisión' }}</p></div>
    </div>
</article>
