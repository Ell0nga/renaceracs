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
        Schema::create('fuel_expenses', function (Blueprint $table) {
            $table->id();
            // foreignId for expense_id will be added in a later migration
            // This is a 1-to-1 relationship with an Expense where the expense is of category 'Combustible'
            $table->foreignId('expense_id')->unique()->constrained('expenses')->onDelete('cascade'); // Asegura que cada gasto de combustible tiene un gasto principal
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade'); // Asociado a un vehículo
            $table->decimal('liters', 8, 2)->nullable(); // Cantidad de litros cargados
            $table->integer('odometer_reading')->nullable(); // Lectura del odómetro al momento de la carga
            $table->text('notes')->nullable(); // Notas específicas del gasto de combustible
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_expenses');
    }
};