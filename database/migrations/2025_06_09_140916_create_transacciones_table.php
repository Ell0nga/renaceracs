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
        Schema::create('transacciones', function (Blueprint $table) {
            $table->id(); // Columna para el ID autoincremental
            $table->decimal('monto', 10, 2); // Por ejemplo, 1500.50
            $table->string('descripcion')->nullable(); // Para una descripción, opcional
            // Esta es la columna clave para tu enum
            $table->string('tipo_categoria', 50); // Almacena el valor string del enum (ej. 'ingreso')
            $table->timestamps(); // Columnas created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transacciones');
    }
};