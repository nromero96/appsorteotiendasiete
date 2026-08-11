<x-layouts.app>
    <a href="{{ route('draws.show', $draw) }}" class="text-sm text-cyan-300">← Volver al sorteo</a>
    <h1 class="mt-4 text-3xl font-black">{{ $prize->exists ? 'Editar premio' : 'Agregar premio' }}</h1>

    <form method="POST" enctype="multipart/form-data"
        action="{{ $prize->exists ? route('prizes.update', [$draw, $prize]) : route('prizes.store', $draw) }}"
        class="mt-6 max-w-xl space-y-5 rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-xl shadow-slate-950/30">
        @csrf
        @if($prize->exists) @method('PUT') @endif

        <label class="block text-sm font-semibold">Nombre
            <input name="name" required value="{{ old('name', $prize->name) }}"
                class="mt-1.5 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2.5 outline-none transition focus:border-cyan-400">
        </label>
        <label class="block text-sm font-semibold">Descripción
            <textarea name="description" rows="3"
                class="mt-1.5 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2.5 outline-none transition focus:border-cyan-400">{{ old('description', $prize->description) }}</textarea>
        </label>
        <label class="block text-sm font-semibold">Precio del ticket
            <input type="number" step=".01" min="0" name="price" required value="{{ old('price', $prize->price) }}"
                class="mt-1.5 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2.5 outline-none transition focus:border-cyan-400">
        </label>
        <label class="block text-sm font-semibold">Imagen del premio
            <input type="file" name="image" accept="image/*" class="mt-1.5 block w-full text-sm text-slate-300">
        </label>
        @if($prize->image_path)
            <img src="{{ Storage::url($prize->image_path) }}" class="h-24 w-24 rounded-xl border border-slate-700 object-cover" alt="Imagen actual">
        @endif
        <label class="flex items-center gap-2 text-sm font-semibold">
            <input type="checkbox" name="active" value="1" @checked(old('active', $prize->exists ? $prize->active : true))>
            Premio activo para la venta
        </label>
        <button class="w-full rounded-xl bg-emerald-400 py-3 font-black text-slate-950 transition hover:bg-emerald-300">
            {{ $prize->exists ? 'Guardar cambios' : 'Guardar premio' }}
        </button>
    </form>
</x-layouts.app>
