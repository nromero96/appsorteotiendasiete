<x-layouts.app>
    <section class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
            <p class="text-xs font-bold uppercase tracking-[.2em] text-cyan-400">Administración</p>
            <h1 class="mt-2 text-3xl font-black">Usuarios y accesos</h1>
            <p class="mt-1 text-slate-400">Crea cuentas y delega solo los permisos necesarios para cada persona.</p>
        </div>
        <a href="{{ route('users.create') }}" class="rounded-xl bg-cyan-400 px-4 py-2.5 text-center font-bold text-slate-950 transition hover:bg-cyan-300">+ Nuevo usuario</a>
    </section>

    <section class="mt-8 overflow-hidden rounded-2xl border border-slate-800 bg-slate-900">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead class="border-b border-slate-800 bg-slate-900/80 text-xs uppercase tracking-wider text-slate-400">
                    <tr>
                        <th class="px-5 py-4">Usuario</th>
                        <th class="px-5 py-4">Rol</th>
                        <th class="px-5 py-4">Permisos directos</th>
                        <th class="px-5 py-4">Estado</th>
                        <th class="px-5 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($users as $managedUser)
                        <tr class="transition hover:bg-slate-800/40">
                            <td class="px-5 py-4">
                                <p class="font-bold text-slate-100">{{ $managedUser->name }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ $managedUser->email }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse($managedUser->roles as $role)
                                        <span class="rounded-full border border-violet-400/30 bg-violet-400/10 px-2.5 py-1 text-xs font-bold text-violet-200">{{ ucfirst($role->name) }}</span>
                                    @empty
                                        <span class="text-slate-500">Personalizado</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-5 py-4 text-slate-300">{{ $managedUser->permissions_count }} asignado(s)</td>
                            <td class="px-5 py-4">
                                @if($managedUser->is_active)
                                    <span class="rounded-full border border-emerald-400/30 bg-emerald-400/10 px-2.5 py-1 text-xs font-bold text-emerald-200">Activo</span>
                                @else
                                    <span class="rounded-full border border-red-400/30 bg-red-400/10 px-2.5 py-1 text-xs font-bold text-red-200">Desactivado</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('users.edit', $managedUser) }}" class="rounded-lg border border-slate-700 px-3 py-1.5 text-xs font-bold text-cyan-200 transition hover:border-cyan-400">Editar</a>
                                    @if(! $managedUser->is(auth()->user()))
                                        <form method="POST" action="{{ route('users.status.toggle', $managedUser) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="rounded-lg border px-3 py-1.5 text-xs font-bold transition {{ $managedUser->is_active ? 'border-amber-400/40 text-amber-200 hover:bg-amber-400/10' : 'border-emerald-400/40 text-emerald-200 hover:bg-emerald-400/10' }}">{{ $managedUser->is_active ? 'Desactivar' : 'Activar' }}</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-slate-400">No hay usuarios registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="mt-6">{{ $users->links() }}</div>
</x-layouts.app>
