<x-layouts.app>
    @php
        $editing = $user->exists;
        $roleIds = collect(old('roles', $selectedRoleIds))->map(fn ($id) => (int) $id)->all();
        $permissionIds = collect(old('permissions', $selectedPermissionIds))->map(fn ($id) => (int) $id)->all();
        $permissionLabels = [
            'ver panel administrativo' => 'Acceso al panel administrativo',
            'gestionar sorteos' => 'Crear y editar sorteos',
            'gestionar premios' => 'Gestionar premios',
            'registrar tickets' => 'Registrar tickets y ventas',
            'ver ventas' => 'Consultar ventas y reportes',
            'actualizar estados de venta' => 'Aprobar o rechazar ventas',
            'imprimir talonarios' => 'Imprimir talonarios',
            'gestionar usuarios' => 'Gestionar usuarios y accesos',
        ];
    @endphp

    <section class="flex items-end justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[.2em] text-cyan-400">Usuarios</p>
            <h1 class="mt-2 text-3xl font-black">{{ $editing ? 'Editar usuario' : 'Nuevo usuario' }}</h1>
            <p class="mt-1 text-slate-400">Configura los datos de acceso y el alcance de sus funciones.</p>
        </div>
        <a href="{{ route('users.index') }}" class="rounded-xl border border-slate-700 px-4 py-2 text-sm font-bold text-slate-200 hover:border-slate-500">Volver</a>
    </section>

    <form class="mt-8 space-y-6" method="POST" action="{{ $editing ? route('users.update', $user) : route('users.store') }}">
        @csrf
        @if($editing) @method('PUT') @endif

        <section class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
            <div class="flex items-center justify-between gap-4"><div><h2 class="font-black">Datos de la cuenta</h2><p class="mt-1 text-sm text-slate-400">El correo será usado para iniciar sesión.</p></div><label class="inline-flex cursor-pointer items-center gap-2 text-sm font-bold text-slate-200"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-600 bg-slate-800 text-cyan-400" @checked(old('is_active', $editing ? $user->is_active : true))> Cuenta activa</label></div>
            <div class="mt-6 grid gap-5 md:grid-cols-2">
                <label class="block text-sm font-bold text-slate-200">Nombre completo<input required name="name" value="{{ old('name', $user->name) }}" class="mt-2 block w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 outline-none transition placeholder:text-slate-600 focus:border-cyan-400" placeholder="Ej. María Pérez"></label>
                <label class="block text-sm font-bold text-slate-200">Correo electrónico<input required type="email" name="email" value="{{ old('email', $user->email) }}" class="mt-2 block w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 outline-none transition placeholder:text-slate-600 focus:border-cyan-400" placeholder="correo@ejemplo.com"></label>
                <label class="block text-sm font-bold text-slate-200">{{ $editing ? 'Nueva contraseña (opcional)' : 'Contraseña' }}<input {{ $editing ? '' : 'required' }} type="password" name="password" class="mt-2 block w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 outline-none transition focus:border-cyan-400" placeholder="Mínimo 8 caracteres"></label>
                <label class="block text-sm font-bold text-slate-200">Confirmar contraseña<input {{ $editing ? '' : 'required' }} type="password" name="password_confirmation" class="mt-2 block w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 outline-none transition focus:border-cyan-400" placeholder="Repite la contraseña"></label>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
            <h2 class="font-black">Rol base</h2>
            <p class="mt-1 text-sm text-slate-400">Un rol aplica un conjunto de permisos. Puedes complementar o ajustar el acceso debajo.</p>
            <div class="mt-5 grid gap-3 md:grid-cols-3">
                @foreach($roles as $role)
                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-700 bg-slate-950/60 p-4 transition hover:border-violet-400/60">
                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" class="mt-0.5 h-4 w-4 rounded border-slate-600 bg-slate-800 text-violet-400" @checked(in_array($role->id, $roleIds, true))>
                        <span><span class="block font-bold text-slate-100">{{ ucfirst($role->name) }}</span><span class="mt-1 block text-xs text-slate-400">{{ $role->permissions->count() }} permisos incluidos</span></span>
                    </label>
                @endforeach
            </div>
        </section>

        <section class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
            <h2 class="font-black">Permisos adicionales</h2>
            <p class="mt-1 text-sm text-slate-400">Marca solo las acciones extra que quieras delegar a esta cuenta.</p>
            <div class="mt-5 grid gap-3 md:grid-cols-2">
                @foreach($permissions as $permission)
                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-700 bg-slate-950/60 p-4 transition hover:border-cyan-400/60">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="h-4 w-4 rounded border-slate-600 bg-slate-800 text-cyan-400" @checked(in_array($permission->id, $permissionIds, true))>
                        <span class="font-semibold text-slate-200">{{ $permissionLabels[$permission->name] ?? ucfirst($permission->name) }}</span>
                    </label>
                @endforeach
            </div>
        </section>

        <div class="flex flex-col-reverse justify-end gap-3 sm:flex-row">
            <a href="{{ route('users.index') }}" class="rounded-xl border border-slate-700 px-5 py-3 text-center font-bold text-slate-200 hover:border-slate-500">Cancelar</a>
            <button class="rounded-xl bg-cyan-400 px-5 py-3 font-black text-slate-950 transition hover:bg-cyan-300">{{ $editing ? 'Guardar cambios' : 'Crear usuario' }}</button>
        </div>
    </form>
</x-layouts.app>
