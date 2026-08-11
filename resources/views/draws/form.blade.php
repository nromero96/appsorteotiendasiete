<x-layouts.app>
    <a href="{{ route('draws.index') }}" class="text-sm text-cyan-300">← Volver a sorteos</a>
    <h1 class="mt-4 text-3xl font-black">{{ $draw->exists ? 'Editar sorteo' : 'Nuevo sorteo' }}</h1>

    <form method="POST" enctype="multipart/form-data"
        action="{{ $draw->exists ? route('draws.update', $draw) : route('draws.store') }}"
        class="mt-6 max-w-2xl space-y-5 rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-xl shadow-slate-950/30">
        @csrf
        @if($draw->exists) @method('PUT') @endif
        <label class="block text-sm font-semibold">Nombre del sorteo
            <input name="title" required value="{{ old('title', $draw->title) }}" class="mt-1.5 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2.5 outline-none focus:border-cyan-400">
        </label>
        <div class="grid gap-4 md:grid-cols-2">
            <label class="text-sm font-semibold">Fecha
                <input type="date" name="draw_date" value="{{ old('draw_date', $draw->draw_date?->format('Y-m-d')) }}" class="mt-1.5 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2.5 outline-none focus:border-cyan-400">
            </label>
            <label class="text-sm font-semibold">Hora
                <input type="time" name="draw_time" value="{{ old('draw_time', $draw->draw_time) }}" class="mt-1.5 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2.5 outline-none focus:border-cyan-400">
            </label>
        </div>
        <label class="block text-sm font-semibold">Canal de transmisión
            <input name="transmission" value="{{ old('transmission', $draw->transmission) }}" placeholder="Facebook Live, TikTok, YouTube..." class="mt-1.5 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2.5 outline-none focus:border-cyan-400">
        </label>
        <label class="block text-sm font-semibold">Flyer del sorteo
            <input type="file" name="flyer" accept="image/*" class="mt-1.5 block w-full text-sm text-slate-300">
        </label>
        @if($draw->flyer_path)
            <img src="{{ Storage::url($draw->flyer_path) }}" alt="Flyer actual" class="h-36 w-full rounded-xl border border-slate-700 object-cover">
        @endif
        <label class="block text-sm font-semibold">Descuento del combo (S/)
            <input type="number" step=".01" min="0" name="combo_discount" value="{{ old('combo_discount', $draw->combo_discount ?? 0) }}" class="mt-1.5 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2.5 outline-none focus:border-cyan-400">
        </label>
        <button class="w-full rounded-xl bg-cyan-400 py-3 font-black text-slate-950 transition hover:bg-cyan-300">Guardar sorteo</button>
    </form>
</x-layouts.app>
