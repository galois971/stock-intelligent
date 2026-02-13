<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Ajoute les champs manquants selon le cahier des charges :
     * - subtype : sous-type de mouvement (achat, retour, correction, vente, perte, casse, expiration)
     * - user_id : utilisateur qui a effectué le mouvement
     * - motif : raison du mouvement
     */
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            // Ajouter le sous-type de mouvement
            $table->enum('subtype', [
                // Entrées
                'achat', 'retour', 'correction',
                // Sorties
                'vente', 'perte', 'casse', 'expiration'
            ])->nullable()->after('type');
            
            // Ajouter l'utilisateur qui a fait le mouvement
            $table->unsignedBigInteger('user_id')->nullable()->after('product_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            // Ajouter le motif/raison du mouvement
            $table->text('motif')->nullable()->after('subtype');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['subtype', 'user_id', 'motif']);
        });
    }
};
