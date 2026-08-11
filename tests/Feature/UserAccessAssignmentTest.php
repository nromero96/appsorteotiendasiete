<?php

namespace Tests\Feature;

use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserAccessAssignmentTest extends TestCase
{
    public function test_user_creation_assigns_roles_and_permissions_selected_by_id(): void
    {
        $role = Role::query()->firstOrCreate(['name' => 'rol de prueba', 'guard_name' => 'web']);
        $permission = Permission::query()->firstOrCreate(['name' => 'permiso de prueba', 'guard_name' => 'web']);
        $email = 'prueba-usuarios-'.uniqid().'@sorteos.local';

        try {
            app(UserController::class)->store(Request::create('/admin/usuarios', 'POST', [
                'name' => 'Usuario de prueba',
                'email' => $email,
                'password' => 'Temporal123',
                'password_confirmation' => 'Temporal123',
                'is_active' => '1',
                'roles' => [$role->id],
                'permissions' => [$permission->id],
            ]));

            $user = User::query()->where('email', $email)->firstOrFail();

            $this->assertTrue(Hash::check('Temporal123', $user->password));
            $this->assertTrue($user->hasRole($role));
            $this->assertTrue($user->hasDirectPermission($permission));
        } finally {
            User::query()->where('email', $email)->delete();
            $role->delete();
            $permission->delete();
        }
    }
}
