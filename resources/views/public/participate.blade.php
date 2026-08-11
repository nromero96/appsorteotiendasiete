<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Tienda Siete Sorteos | Participar en {{ $draw->title }}</title>
    <meta name="description" content="Registra tu participación en el sorteo de Tienda Siete y adjunta tu comprobante de pago.">
    <meta name="theme-color" content="#006b5e">
    <link rel="icon" type="image/png" href="{{ asset('brand/icono-tiendasiete.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('brand/tienda-siete.css') }}">
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <style>
        .filepond--root { margin-top: .75rem; font-family: inherit; }
        .filepond--panel-root { background-color: #020617; border: 1px dashed rgba(34, 211, 238, .55); border-radius: .85rem; }
        .filepond--drop-label { color: #cbd5e1; }
        .filepond--label-action { color: #67e8f9; text-decoration-color: #22d3ee; }
        .filepond--file { color: #f8fafc; }
        .filepond--file-status { color: #cbd5e1; }
    </style>
</head>
<body class="min-h-screen bg-slate-950 text-white selection:bg-cyan-300 selection:text-slate-950">
    @php
        $prizes = $draw->prizes;
        $subtotal = $prizes->sum('price');
        $comboTotal = max(0, $subtotal - $draw->combo_discount);
    @endphp
    <main class="mx-auto max-w-5xl px-5 py-8 sm:px-8 sm:py-12">
        <div class="flex items-center justify-between gap-4"><a href="{{ route('public.next-draw') }}"><img src="{{ asset('brand/perfil-log-blanco.png') }}" alt="Tienda Siete Market & Licorería" class="tienda-siete-wordmark h-10 sm:h-12"></a><a href="{{ route('public.next-draw') }}" class="inline-flex items-center gap-2 text-sm font-bold text-cyan-300 hover:text-cyan-200">← Volver al sorteo</a></div>
        <div class="mt-7 rounded-3xl border border-cyan-400/20 bg-gradient-to-r from-cyan-400/10 to-violet-500/10 p-6 sm:p-8"><p class="text-xs font-bold uppercase tracking-[.2em] text-cyan-300">Formulario de participación</p><h1 class="mt-2 text-3xl font-black sm:text-4xl">{{ $draw->title }}</h1><p class="mt-3 text-slate-300">Elige una opción, adjunta tu comprobante de pago y envía tu solicitud. El administrador revisará y aprobará tus tickets.</p></div>

        @if($errors->any())
            <div class="mt-6 rounded-2xl border border-red-400/30 bg-red-500/10 px-5 py-4 text-sm text-red-100"><p class="font-bold">Revisa la información ingresada.</p><ul class="mt-2 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif

        <form method="POST" enctype="multipart/form-data" action="{{ route('public.participation.store', $draw) }}" class="mt-7 grid gap-6 lg:grid-cols-[1.15fr_.85fr]">
            @csrf
            <section class="rounded-3xl border border-slate-800 bg-slate-900 p-6 sm:p-8">
                <div><p class="text-xs font-bold uppercase tracking-[.18em] text-cyan-400">Paso 1</p><h2 class="mt-2 text-2xl font-black">Selecciona cómo participar</h2><p class="mt-1 text-sm text-slate-400">Puedes comprar un ticket individual o el combo con todos los premios.</p></div>
                <div class="mt-6 grid gap-4">
                    @foreach($prizes as $prize)
                        @php($optionValue = 'prize:'.$prize->id)
                        <label class="group relative block cursor-pointer overflow-hidden rounded-2xl border border-slate-700 bg-slate-950/60 transition hover:border-cyan-400">
                            <input class="peer sr-only" required type="radio" name="option" value="{{ $optionValue }}" @checked(old('option') === $optionValue)>
                            <div class="absolute inset-y-0 left-0 w-1 bg-transparent peer-checked:bg-cyan-400"></div>
                            <div class="flex gap-4 p-4 sm:p-5">
                                @if($prize->image_path)<img src="{{ Storage::url($prize->image_path) }}" alt="{{ $prize->name }}" class="h-16 w-16 shrink-0 rounded-xl object-cover">@endif
                                <div class="min-w-0 flex-1"><p class="font-black text-slate-100">{{ $prize->name }}</p>@if($prize->description)<p class="mt-1 text-sm text-slate-400">{{ $prize->description }}</p>@endif</div>
                                <p class="shrink-0 font-black text-cyan-300">S/ {{ number_format($prize->price, 2) }}</p>
                            </div>
                            <div class="pointer-events-none absolute inset-0 rounded-2xl ring-2 ring-cyan-400 opacity-0 transition peer-checked:opacity-100"></div>
                        </label>
                    @endforeach
                    @if($prizes->count() > 1)
                        <label class="group relative block cursor-pointer overflow-hidden rounded-2xl border border-emerald-400/30 bg-emerald-400/10 transition hover:border-emerald-300">
                            <input class="peer sr-only" required type="radio" name="option" value="combo" @checked(old('option') === 'combo')>
                            <div class="absolute inset-y-0 left-0 w-1 bg-transparent peer-checked:bg-emerald-300"></div>
                            <div class="flex items-center justify-between gap-4 p-5"><div><p class="font-black text-emerald-100">Combo completo</p><p class="mt-1 text-sm text-emerald-100/75">Todos los premios · Ahorras S/ {{ number_format($draw->combo_discount, 2) }}</p></div><p class="shrink-0 text-xl font-black text-emerald-200">S/ {{ number_format($comboTotal, 2) }}</p></div>
                            <div class="pointer-events-none absolute inset-0 rounded-2xl ring-2 ring-emerald-300 opacity-0 transition peer-checked:opacity-100"></div>
                        </label>
                    @endif
                </div>
            </section>

            <section class="rounded-3xl border border-slate-800 bg-slate-900 p-6 sm:p-8">
                <p class="text-xs font-bold uppercase tracking-[.18em] text-cyan-400">Paso 2</p><h2 class="mt-2 text-2xl font-black">Tus datos y pago</h2>
                <div class="mt-6 space-y-4">
                    <label class="block text-sm font-bold text-slate-200">Nombre y apellidos<input required name="full_name" value="{{ old('full_name') }}" class="mt-2 block w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 outline-none transition focus:border-cyan-400" placeholder="Ej. María Pérez"></label>
                    <div class="grid gap-4 sm:grid-cols-2"><label class="block text-sm font-bold text-slate-200">DNI<input required name="document_number" value="{{ old('document_number') }}" class="mt-2 block w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 outline-none transition focus:border-cyan-400" placeholder="00000000"></label><label class="block text-sm font-bold text-slate-200">WhatsApp<input required name="phone" value="{{ old('phone') }}" class="mt-2 block w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 outline-none transition focus:border-cyan-400" placeholder="999 999 999"></label></div>
                    <label class="block text-sm font-bold text-slate-200">Método de pago<select id="payment-method" required name="payment_method" class="mt-2 block w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 outline-none transition focus:border-cyan-400"><option value="">Selecciona una opción</option>@foreach(['Yape', 'Plin', 'Transferencia', 'Compra'] as $method)<option value="{{ $method }}" @selected(old('payment_method') === $method)>{{ $method }}</option>@endforeach</select></label>
                    <section id="payment-instructions" class="hidden rounded-2xl border border-cyan-400/25 bg-cyan-400/5 p-4"><div class="flex items-start gap-3"><span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-cyan-400 font-black text-slate-950">$</span><div><p class="font-black text-cyan-100">Datos para realizar el pago</p><p id="payment-instructions-copy" class="mt-1 text-xs leading-relaxed text-slate-400">Selecciona un método de pago para ver las instrucciones.</p></div></div><div class="mt-4 space-y-2"><div data-payment-panel="Yape" class="hidden flex items-center justify-between gap-3 rounded-xl border border-slate-700 bg-slate-950/80 p-3"><div><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Yape</p><p class="mt-1 font-mono font-bold text-slate-100">991 077 380</p></div><button type="button" data-copy="991077380" class="copy-button rounded-lg border border-cyan-400/40 px-3 py-2 text-xs font-bold text-cyan-200 hover:bg-cyan-400/10">Copiar</button></div><div data-payment-panel="Transferencia" class="hidden space-y-2"><div class="flex items-center justify-between gap-3 rounded-xl border border-slate-700 bg-slate-950/80 p-3"><div><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Cuenta BCP</p><p class="mt-1 break-all font-mono font-bold text-slate-100">1917289760037</p></div><button type="button" data-copy="1917289760037" class="copy-button rounded-lg border border-cyan-400/40 px-3 py-2 text-xs font-bold text-cyan-200 hover:bg-cyan-400/10">Copiar</button></div><div class="flex items-center justify-between gap-3 rounded-xl border border-slate-700 bg-slate-950/80 p-3"><div><p class="text-xs font-bold uppercase tracking-wider text-slate-500">CCI BCP</p><p class="mt-1 break-all font-mono font-bold text-slate-100">00219100728976003754</p></div><button type="button" data-copy="00219100728976003754" class="copy-button rounded-lg border border-cyan-400/40 px-3 py-2 text-xs font-bold text-cyan-200 hover:bg-cyan-400/10">Copiar</button></div></div><div data-payment-panel="Plin" class="hidden flex items-center justify-between gap-3 rounded-xl border border-violet-400/30 bg-violet-400/10 p-3"><div><p class="text-xs font-bold uppercase tracking-wider text-violet-200/70">Plin</p><p class="mt-1 font-mono font-bold text-violet-50">991 077 380</p></div><button type="button" data-copy="991077380" class="copy-button rounded-lg border border-violet-300/40 px-3 py-2 text-xs font-bold text-violet-100 hover:bg-violet-400/10">Copiar</button></div><div data-payment-panel="Compra" class="hidden rounded-xl border border-amber-400/40 bg-amber-400/10 p-4 text-sm leading-relaxed text-amber-100"><b>Compra:</b> adjunta tu boleta o factura por una compra mayor a <b>S/ 180.00</b>. El administrador verificará el documento antes de aprobar los tickets.</div></div><p id="copy-feedback" class="mt-3 hidden text-xs font-bold text-emerald-300" role="status"></p></section>
                    <label class="block text-sm font-bold text-slate-200">ID de operación <span class="font-normal text-slate-400">(opcional)</span><input name="transaction_id" value="{{ old('transaction_id') }}" class="mt-2 block w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 outline-none transition focus:border-cyan-400" placeholder="Número de operación"></label>
                    <div class="rounded-2xl border border-dashed border-cyan-400/40 bg-cyan-400/5 p-4 text-sm font-bold text-cyan-100"><p id="voucher-title">Comprobante de pago</p><input id="voucher-upload" required type="file" name="voucher" accept="image/jpeg,image/png,image/webp,application/pdf"><span class="mt-2 block text-xs font-normal text-slate-400">JPG, PNG, WEBP o PDF · Máximo 5 MB · Las imágenes se muestran antes de enviar.</span></div>
                    <div class="rounded-xl bg-amber-400/10 px-4 py-3 text-xs leading-relaxed text-amber-100"><b>Importante:</b> tu solicitud quedará pendiente hasta que el administrador verifique el pago.</div>
                    <button class="w-full rounded-xl bg-cyan-400 px-5 py-4 font-black text-slate-950 shadow-lg shadow-cyan-400/20 transition hover:-translate-y-0.5 hover:bg-cyan-300">Enviar participación</button>
                </div>
            </section>
        </form>
        <x-public.footer />
    </main>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script>
        FilePond.registerPlugin(FilePondPluginImagePreview);

        FilePond.create(document.querySelector('#voucher-upload'), {
            allowMultiple: false,
            maxFiles: 1,
            storeAsFile: true,
            instantUpload: false,
            labelIdle: 'Arrastra tu comprobante o <span class="filepond--label-action">selecciónalo</span>',
            labelFileLoading: 'Cargando comprobante',
            labelFileLoadError: 'No se pudo cargar el archivo',
            labelFileProcessingComplete: 'Listo para enviar',
            labelTapToCancel: 'toca para cancelar',
            labelTapToRetry: 'toca para reintentar',
            labelTapToUndo: 'toca para eliminar',
        });

        const paymentMethod = document.getElementById('payment-method');
        const paymentInstructions = document.getElementById('payment-instructions');
        const paymentCopy = document.getElementById('payment-instructions-copy');
        const voucherTitle = document.getElementById('voucher-title');
        const instructionCopy = {
            Yape: 'Copia el número de Yape y luego adjunta tu comprobante.',
            Plin: 'Copia el número de Plin y luego adjunta tu comprobante.',
            Transferencia: 'Copia la cuenta BCP o el CCI para realizar tu transferencia.',
            Compra: 'Adjunta la boleta o factura de tu compra mayor a S/ 180.00.',
        };

        const syncPaymentInstructions = () => {
            const selectedMethod = paymentMethod.value;
            paymentInstructions.classList.toggle('hidden', !selectedMethod);
            paymentCopy.textContent = instructionCopy[selectedMethod] || '';
            document.querySelectorAll('[data-payment-panel]').forEach((panel) => panel.classList.toggle('hidden', panel.dataset.paymentPanel !== selectedMethod));
            voucherTitle.textContent = selectedMethod === 'Compra' ? 'Boleta o factura de compra' : 'Comprobante de pago';
        };

        paymentMethod.addEventListener('change', syncPaymentInstructions);
        syncPaymentInstructions();

        document.querySelectorAll('.copy-button').forEach((button) => {
            button.addEventListener('click', async () => {
                const value = button.dataset.copy;
                const feedback = document.getElementById('copy-feedback');
                try {
                    await navigator.clipboard.writeText(value);
                    button.textContent = '¡Copiado!';
                    feedback.textContent = `${value} copiado al portapapeles.`;
                    feedback.classList.remove('hidden');
                    setTimeout(() => { button.textContent = 'Copiar'; }, 1800);
                } catch (error) {
                    feedback.textContent = `Copia manualmente: ${value}`;
                    feedback.classList.remove('hidden');
                }
            });
        });
    </script>
</body>
</html>
