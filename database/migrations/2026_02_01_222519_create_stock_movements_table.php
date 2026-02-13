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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('product_id'); // lien vers produit
            $table->enum('type', ['entry', 'exit']); // entrée ou sortie
            $table->integer('quantity'); // quantité déplacée
            $table->string('reference')->nullable(); // numéro de facture ou bon
            $table->string('supplier')->nullable(); // fournisseur (si entrée)
            $table->string('customer')->nullable(); // client (si sortie)
            $table->timestamp('movement_date')->useCurrent(); // date du mouvement
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
