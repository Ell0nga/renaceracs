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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('patent')->unique(); // Patente del vehículo
            $table->string('brand')->nullable(); // Marca (ej. Ford)
            $table->string('model')->nullable(); // Modelo (ej. F-150)
            $table->integer('year')->nullable(); // Año (ej. 2020)
            $table->text('comment')->nullable(); // Cualquier comentario adicional
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};