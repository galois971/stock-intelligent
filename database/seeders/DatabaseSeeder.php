<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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

        // Rôles selon le cahier des charges :
        // - Admin : accès complet (tous les modules)
        // - Gestionnaire : gère produits, catégories, inventaires, mouvements
        // - Observateur : lecture seule (consulte les alertes)
        $roles = [
            'admin' => ['manage products', 'manage categories', 'manage inventories', 'manage stock movements', 'view alerts'],
            'gestionnaire' => ['manage products', 'manage categories', 'manage inventories', 'manage stock movements', 'view alerts'],
            'observateur' => ['view alerts'],
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
                'password' => bcrypt('password'), // mot de passe par défaut
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // Utilisateur gestionnaire
        $manager = User::firstOrCreate(
            ['email' => 'gestionnaire@example.com'],
            [
                'name' => 'Gestionnaire User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $manager->assignRole('gestionnaire');

        // Utilisateur observateur
        $observer = User::firstOrCreate(
            ['email' => 'observateur@example.com'],
            [
                'name' => 'Observateur User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $observer->assignRole('observateur');

        // Call resource seeders
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            StockMovementSeeder::class,
            InventorySeeder::class,
        ]);
    }
}