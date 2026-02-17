<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Ajouter la colonne expiration_date - nullable car tous les produits n'ont pas d'expiration
            $table->date('expiration_date')
                  ->nullable()
                  ->after('stock_optimal')
                  ->comment('Date d\'expiration du produit');

            // Ajouter un index pour les requêtes fréquentes sur expiration
            $table->index('expiration_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['expiration_date']);
            $table->dropColumn('expiration_date');
        });
    }
};
