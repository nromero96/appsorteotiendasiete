<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'ver panel administrativo',
            'gestionar sorteos',
            'gestionar premios',
            'registrar tickets',
            'ver ventas',
            'actualizar estados de venta',
            'imprimir talonarios',
            'gestionar usuarios',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $administrator = Role::findOrCreate('administrador', 'web');
        $administrator->syncPermissions($permissions);

        $drawManager = Role::findOrCreate('gestor de sorteos', 'web');
        $drawManager->syncPermissions([
            'ver panel administrativo',
            'gestionar sorteos',
            'gestionar premios',
            'registrar tickets',
            'imprimir talonarios',
        ]);

        $salesManager = Role::findOrCreate('gestor de ventas', 'web');
        $salesManager->syncPermissions([
            'ver panel administrativo',
            'registrar tickets',
            'ver ventas',
            'actualizar estados de venta',
            'imprimir talonarios',
        ]);

        $user = User::firstOrCreate(
            ['email' => 'admin@sorteos.local'],
            ['name' => 'Administrador', 'password' => Hash::make('Admin12345')]
        );
        $user->syncRoles([$administrator]);
    }
}
