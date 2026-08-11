<x-layouts.app>
    <section class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div><p class="text-xs font-bold uppercase tracking-[.2em] text-cyan-400">Reportes</p><h1 class="mt-2 text-3xl font-black">Registro de ventas</h1><p class="mt-1 text-slate-400">Cada venta conserva su subtotal, descuento y total pagado.</p></div>
    </section>
    <section class="mt-7 grid gap-4 md:grid-cols-3">
        <article class="rounded-2xl border border-slate-800 bg-slate-900 p-5"><p class="text-sm text-slate-400">Ventas aprobadas</p><p class="mt-2 text-2xl font-black text-emerald-300">S/ {{ number_format($totals['approved'], 2) }}</p></article>
        <article class="rounded-2xl border border-slate-800 bg-slate-900 p-5"><p class="text-sm text-slate-400">Por cobrar / pendientes</p><p class="mt-2 text-2xl font-black text-amber-300">S/ {{ number_format($totals['pending'], 2) }}</p></article>
        <article class="rounded-2xl border border-slate-800 bg-slate-900 p-5"><p class="text-sm text-slate-400">Descuentos otorgados</p><p class="mt-2 text-2xl font-black text-cyan-300">S/ {{ number_format($totals['discounts'], 2) }}</p></article>
    </section>
    <div class="mt-7 overflow-x-auto rounded-2xl border border-slate-800">
        <table class="w-full min-w-[900px] text-left text-sm">
            <thead class="bg-slate-900 text-slate-400"><tr><th class="p-4">Fecha / sorteo</th><th>Cliente</th><th>Compra</th><th>Importes</th><th>Pago</th><th>Estado</th></tr></thead>
            <tbody>
                @forelse($sales as $sale)
                    <tr class="border-t border-slate-800 align-top">
                        <td class="p-4"><p class="font-semibold">{{ $sale->created_at->format('d/m/Y H:i') }}</p><p class="mt-1 text-slate-400">{{ $sale->draw->title }}</p></td>
                        <td><p class="font-semibold">{{ $sale->customer->full_name }}</p><p class="mt-1 text-slate-400">{{ $sale->customer->phone }}</p></td>
                        <td><p class="font-bold uppercase text-cyan-300">{{ $sale->purchase_type }}</p><p class="mt-1 text-slate-400">{{ $sale->tickets->pluck('prize.name')->filter()->join(', ') }}</p><p class="mt-1 text-xs text-slate-500">{{ $sale->tickets->count() }} ticket(s)</p></td>
                        <td><p>Subtotal: <b>S/ {{ number_format($sale->subtotal_amount, 2) }}</b></p><p class="mt-1 text-emerald-300">Descuento: - S/ {{ number_format($sale->discount_amount, 2) }}</p><p class="mt-1 font-black text-cyan-300">Total: S/ {{ number_format($sale->total_amount, 2) }}</p></td>
                        <td><p>{{ $sale->payment_method }}</p><p class="mt-1 text-slate-400">{{ $sale->transaction_id ?: 'Sin ID registrada' }}</p>@if($sale->voucher_path)<a class="mt-1 inline-block text-cyan-300" href="{{ Storage::url($sale->voucher_path) }}" target="_blank">Ver voucher</a>@endif</td>
                        <td>
                            @php($statusClass = match($sale->status) {
                                'aprobado' => 'border-emerald-400/40 bg-emerald-400/15 text-emerald-200',
                                'rechazado' => 'border-red-400/40 bg-red-400/15 text-red-200',
                                default => 'border-amber-400/40 bg-amber-400/15 text-amber-200',
                            })
                            <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-bold uppercase {{ $statusClass }}">{{ $sale->status }}</span>
                            @can('actualizar estados de venta')
                                <form method="POST" action="{{ route('sales.status.update', $sale) }}" class="mt-3 space-y-2">
                                    @csrf @method('PUT')
                                    <label class="block text-xs font-bold text-slate-400">ID de transacción <span class="text-amber-300">(obligatoria al aprobar)</span><input name="transaction_id" value="{{ $sale->approval_transaction_id ?: $sale->transaction_id }}" class="mt-1 block w-full rounded-lg border border-slate-700 bg-slate-950 px-2 py-1.5 text-xs text-slate-200 outline-none focus:border-cyan-400 disabled:cursor-not-allowed disabled:text-slate-500" placeholder="Ej. 123456789" {{ $sale->approval_transaction_id ? 'readonly' : '' }}></label>
                                    @if($sale->approval_transaction_id)<p class="text-[11px] font-semibold text-emerald-300">Referencia validada y bloqueada.</p>@endif
                                    <div class="flex gap-2"><select name="status" class="min-w-0 flex-1 rounded-lg border border-slate-700 bg-slate-950 px-2 py-1 text-xs text-slate-200">
                                        <option value="pendiente" @selected($sale->status === 'pendiente')>Pendiente</option>
                                        <option value="aprobado" @selected($sale->status === 'aprobado')>Aprobado</option>
                                        <option value="rechazado" @selected($sale->status === 'rechazado')>Rechazado</option>
                                    </select>
                                    <button class="rounded-lg border border-cyan-400/40 px-2 py-1 text-xs font-bold text-cyan-200">Actualizar</button></div>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-6 text-center text-slate-400">No hay ventas registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-5">{{ $sales->links() }}</div>
</x-layouts.app>
