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
        Schema::table('incomes', function (Blueprint $table) {
            // Añadir la nueva columna de clave foránea
            $table->foreignId('payment_method_id')
                ->nullable() // Puede ser nulo
                ->after('type') // Asume que 'type' es una buena posición, ajusta si es necesario
                ->constrained('payment_methods')
                ->onDelete('set null');

            // Eliminar la columna de texto 'payment_method' antigua
            $table->dropColumn('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            // Revertir: Eliminar la clave foránea
            $table->dropConstrainedForeignId('payment_method_id');
            // Revertir: Volver a añadir la columna de texto
            $table->string('payment_method')->nullable()->after('type');
        });
    }
};