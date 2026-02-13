<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Renomme les rôles pour correspondre au cahier des charges :
     * - manager → gestionnaire
     * - magasinier → observateur
     */
    public function up(): void
    {
        // Renommer le rôle 'manager' en 'gestionnaire'
        $managerRole = Role::where('name', 'manager')->first();
        if ($managerRole) {
            DB::table('roles')
                ->where('id', $managerRole->id)
                ->update(['name' => 'gestionnaire']);
        }

        // Renommer le rôle 'magasinier' en 'observateur'
        $magasinierRole = Role::where('name', 'magasinier')->first();
        if ($magasinierRole) {
            DB::table('roles')
                ->where('id', $magasinierRole->id)
                ->update(['name' => 'observateur']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Renommer 'gestionnaire' en 'manager'
        $gestionnaireRole = Role::where('name', 'gestionnaire')->first();
        if ($gestionnaireRole) {
            DB::table('roles')
                ->where('id', $gestionnaireRole->id)
                ->update(['name' => 'manager']);
        }

        // Renommer 'observateur' en 'magasinier'
        $observateurRole = Role::where('name', 'observateur')->first();
        if ($observateurRole) {
            DB::table('roles')
                ->where('id', $observateurRole->id)
                ->update(['name' => 'magasinier']);
        }
    }
};
