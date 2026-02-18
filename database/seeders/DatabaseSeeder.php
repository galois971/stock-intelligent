<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Nettoyer les tables pivot
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();

        // Nettoyer les rôles et permissions pour éviter les doublons
        Role::truncate();
        Permission::truncate();

        // Permissions
        $permissions = [
            'manage products',
            'manage categories',
            'manage inventories',
            'manage stock movements',
            'view alerts',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Rôles selon le cahier des charges
        $roles = [
            'admin' => $permissions, // accès complet
            'gestionnaire' => [
                'manage products',
                'manage categories',
                'manage inventories',
                'manage stock movements',
                'view alerts',
            ],
            'observateur' => ['view alerts'], // lecture seule
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }

        // Utilisateur admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'), // mot de passe par défaut
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // Utilisateur gestionnaire
        $manager = User::firstOrCreate(
            ['email' => 'gestionnaire@example.com'],
            [
                'name' => 'Gestionnaire User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $manager->assignRole('gestionnaire');

        // Utilisateur observateur
        $observer = User::firstOrCreate(
            ['email' => 'observateur@example.com'],
            [
                'name' => 'Observateur User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $observer->assignRole('observateur');

        // Appeler les autres seeders de ressources
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            StockMovementSeeder::class,
            InventorySeeder::class,
        ]);
    }
}