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
            // Añadir la nueva columna de clave foránea
            $table->foreignId('payment_method_id')
                ->nullable() // Puede ser nulo si no hay un método de pago asociado al inicio
                ->after('transaction_date')
                ->constrained('payment_methods')
                ->onDelete('set null'); // Si se elimina un método de pago, el id se pone a NULL

            // Eliminar la columna de texto 'payment_method' antigua
            $table->dropColumn('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Revertir: Eliminar la clave foránea
            $table->dropConstrainedForeignId('payment_method_id');
            // Revertir: Volver a añadir la columna de texto (aunque no tendrá los datos originales)
            $table->string('payment_method')->nullable()->after('transaction_date');
        });
    }
};