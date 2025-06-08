<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Cambiar el tipo de columna a datetime
            // Esto requiere el paquete doctrine/dbal
            $table->dateTime('transaction_date')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Revertir a date si es necesario (menos preciso)
            $table->date('transaction_date')->change();
        });
    }
};