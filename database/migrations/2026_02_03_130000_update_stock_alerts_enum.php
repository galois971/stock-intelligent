<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Met à jour l'enum pour inclure les nouveaux types d'alertes
     */
    public function up(): void
    {
        // Pour MySQL/Postgres: modifier directement la colonne enum
        try {
            $driver = DB::getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        } catch (\Exception $e) {
            $driver = null;
        }

        if ($driver === 'sqlite' || $driver === null) {
            // SQLite ne supporte pas ALTER ... MODIFY; rien à faire (la table créée précédemment contient déjà les valeurs nécessaires)
            return;
        }

        DB::statement("ALTER TABLE stock_alerts MODIFY COLUMN alert_type ENUM('low_stock', 'overstock', 'risk_of_rupture', 'expiration') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            $driver = DB::getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        } catch (\Exception $e) {
            $driver = null;
        }

        if ($driver === 'sqlite' || $driver === null) {
            return;
        }

        DB::statement("ALTER TABLE stock_alerts MODIFY COLUMN alert_type ENUM('low_stock', 'overstock') NOT NULL");
    }
};
