<x-layouts.app>
    @php
        $activePrizes = $draw->prizes->where('active', true);
        $subtotal = $activePrizes->sum('price');
        $comboTotal = max(0, $subtotal - $draw->combo_discount);
    @endphp
    <a href="{{ route('draws.show', $draw) }}" class="text-sm text-cyan-300">← Volver al sorteo</a>
    <p class="mt-5 text-sm font-bold uppercase tracking-wider text-cyan-300">Registrar venta para</p>
    <h1 class="mt-1 text-3xl font-black">{{ $draw->title }}</h1>

    <form method="POST" enctype="multipart/form-data" action="{{ route('tickets.store', $draw) }}" class="mt-6 grid gap-6 lg:grid-cols-[1.1fr_.9fr]">
        @csrf
        <section class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
            <div class="flex items-center justify-between"><h2 class="text-lg font-black">Elige una opción</h2><span class="text-sm text-slate-400">1 premio o combo completo</span></div>
            <div class="mt-4 grid gap-3">
                @foreach($activePrizes as $prize)
                    <label class="prize-option flex cursor-pointer items-center justify-between rounded-xl border border-slate-700 bg-slate-950/60 p-4 transition hover:border-cyan-400">
                        <span class="flex items-center gap-3"><input class="prize-checkbox h-4 w-4 accent-cyan-400" type="checkbox" name="prize_ids[]" value="{{ $prize->id }}" @checked(in_array($prize->id, old('prize_ids', [])))><span><b class="block">{{ $prize->name }}</b><small class="text-slate-400">{{ $prize->description }}</small></span></span>
                        <b class="text-cyan-300">S/ {{ number_format($prize->price, 2) }}</b>
                    </label>
                @endforeach
            </div>
            @if($activePrizes->count() > 1)
                <button type="button" id="choose-combo" class="mt-4 flex w-full items-center justify-between rounded-xl border border-emerald-400/50 bg-emerald-400/10 p-4 text-left transition hover:bg-emerald-400/15">
                    <span><b class="block text-emerald-200">Combo completo</b><small class="text-emerald-100/70">Todos los premios · descuento S/ {{ number_format($draw->combo_discount, 2) }}</small></span>
                    <b class="text-lg text-emerald-200">S/ {{ number_format($comboTotal, 2) }}</b>
                </button>
            @endif
        </section>

        <section class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
            <h2 class="text-lg font-black">Datos de la venta</h2>
            <div class="mt-4 grid gap-4">
                <label class="text-sm font-semibold">Nombre y apellido<input name="full_name" required value="{{ old('full_name') }}" class="mt-1.5 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2.5 outline-none focus:border-cyan-400"></label>
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="text-sm font-semibold">DNI<input name="document_number" required value="{{ old('document_number') }}" class="mt-1.5 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2.5 outline-none focus:border-cyan-400"></label>
                    <label class="text-sm font-semibold">WhatsApp<input name="phone" required value="{{ old('phone') }}" class="mt-1.5 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2.5 outline-none focus:border-cyan-400"></label>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="text-sm font-semibold">Método de pago<select name="payment_method" class="mt-1.5 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2.5 outline-none focus:border-cyan-400"><option value="Yape">Yape</option><option value="Plin">Plin</option><option value="Transferencia">Transferencia</option><option value="Compra">Compra</option></select></label>
                    <label class="text-sm font-semibold">Estado<select name="status" class="mt-1.5 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2.5 outline-none focus:border-cyan-400"><option value="pendiente">Pendiente</option><option value="aprobado">Aprobado</option><option value="rechazado">Rechazado</option></select></label>
                </div>
                <label class="text-sm font-semibold">ID de transacción <span class="font-normal text-slate-400">(opcional)</span><input name="transaction_id" value="{{ old('transaction_id') }}" class="mt-1.5 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2.5 outline-none focus:border-cyan-400"></label>
                <label class="text-sm font-semibold">Voucher <span class="font-normal text-slate-400">(opcional)</span><input type="file" name="voucher" accept="image/*" class="mt-1.5 block w-full text-sm text-slate-300"></label>
                <button class="mt-2 w-full rounded-xl bg-cyan-400 py-3 font-black text-slate-950 transition hover:bg-cyan-300">Registrar venta y ticket(s)</button>
            </div>
        </section>
    </form>

    <script>
        document.getElementById('choose-combo')?.addEventListener('click', () => document.querySelectorAll('.prize-checkbox').forEach(input => input.checked = true));
        document.querySelectorAll('.prize-checkbox').forEach(input => input.addEventListener('change', () => {
            if (document.querySelectorAll('.prize-checkbox:checked').length > 1 && document.querySelectorAll('.prize-checkbox:checked').length < document.querySelectorAll('.prize-checkbox').length) {
                input.checked = false;
            }
        }));
    </script>
</x-layouts.app>
