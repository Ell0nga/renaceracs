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
        Schema::create('finanza_categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique(); // Nombre único de la categoría (ej: "Salario", "Arriendo")
            $table->text('descripcion')->nullable(); // Descripción opcional de la categoría
            $table->enum('tipo', ['ingreso', 'gasto']); // Para diferenciar si es un ingreso o un gasto
            $table->foreignId('parent_id')->nullable()->constrained('finanza_categorias')->onDelete('set null'); // Para subcategorías
            $table->timestamps(); // Columnas created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finanza_categorias');
    }
};