<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Créer les rôles
        $adminRole = Role::create(['name' => 'admin']);
        $managerRole = Role::create(['name' => 'manager']);
        $userRole = Role::create(['name' => 'user']);

        // Exemple de permissions
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'manage products']);
        Permission::create(['name' => 'view dashboard']);

        // Associer les permissions aux rôles
        $adminRole->givePermissionTo(Permission::all());
        $managerRole->givePermissionTo(['manage products', 'view dashboard']);
        $userRole->givePermissionTo(['view dashboard']);
    }
}