<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->with(['roles', 'permissions'])
            ->withCount('permissions')
            ->latest()
            ->paginate(12);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return $this->form(new User());
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request, true);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->syncAccess($user, $data);

        return redirect()->route('users.index')->with('success', 'Usuario creado y permisos asignados.');
    }

    public function edit(User $user)
    {
        $user->load(['roles', 'permissions']);

        return $this->form($user);
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validatedData($request);
        $isActive = $request->boolean('is_active');

        if ($user->is(auth()->user()) && ! $isActive) {
            return back()->withInput()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'is_active' => $isActive,
        ]);

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();
        $this->syncAccess($user, $data);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function toggleStatus(User $user)
    {
        if ($user->is(auth()->user())) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', $user->is_active ? 'Usuario activado.' : 'Usuario desactivado.');
    }

    private function form(User $user)
    {
        return view('users.form', [
            'user' => $user,
            'roles' => Role::query()->with('permissions')->orderBy('name')->get(),
            'permissions' => Permission::query()->orderBy('name')->get(),
            'selectedRoleIds' => $user->roles->pluck('id')->map(fn ($id) => (int) $id)->all(),
            'selectedPermissionIds' => $user->permissions->pluck('id')->map(fn ($id) => (int) $id)->all(),
        ]);
    }

    private function validatedData(Request $request, bool $creating = false): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'.($creating ? '' : ','.$request->route('user')->id)],
            'password' => [$creating ? 'required' : 'nullable', 'confirmed', 'min:8'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', 'exists:roles,id'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);
    }

    private function syncAccess(User $user, array $data): void
    {
        // El formulario envía IDs. Laravel Permission sincroniza roles y
        // permisos por nombre cuando se reciben valores simples, por eso se
        // obtienen los modelos antes de asignarlos.
        $roles = Role::query()->whereIn('id', $data['roles'] ?? [])->get();
        $permissions = Permission::query()->whereIn('id', $data['permissions'] ?? [])->get();

        $user->syncRoles($roles);
        $user->syncPermissions($permissions);
    }
}
