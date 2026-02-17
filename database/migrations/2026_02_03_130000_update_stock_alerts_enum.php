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
        Schema::table('stock_alerts', function (Blueprint $table) {
            // Corrige la colonne alert_type pour PostgreSQL
            $table->string('alert_type')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_alerts', function (Blueprint $table) {
            // Rollback simple : garde alert_type en string
            $table->string('alert_type')->change();
        });
    }
};