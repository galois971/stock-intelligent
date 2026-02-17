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
        Schema::create('stock_alerts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id'); // lien vers produit
            $table->string('alert_type'); // type d’alerte (low_stock, overstock, risk_of_rupture, expiration)
            $table->integer('current_quantity'); // quantité actuelle
            $table->string('message'); // message d’alerte
            $table->boolean('resolved')->default(false); // statut de l’alerte
            $table->timestamps();

            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_alerts');
    }
};