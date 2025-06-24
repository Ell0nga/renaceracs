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
            $table->foreignId('assignment_id')
                ->nullable() // Puede ser nulo
                ->after('comment') // Asume que 'comment' es una buena posición, ajusta si es necesario
                ->constrained('assignments')
                ->onDelete('set null'); // Si se elimina una asignación, el id se pone a NULL

            // Eliminar la columna de texto 'assigned_to' antigua
            $table->dropColumn('assigned_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Revertir: Eliminar la clave foránea
            $table->dropConstrainedForeignId('assignment_id');
            // Revertir: Volver a añadir la columna de texto
            $table->string('assigned_to')->nullable()->after('comment');
        });
    }
};