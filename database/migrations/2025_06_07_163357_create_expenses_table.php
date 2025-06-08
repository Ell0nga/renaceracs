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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Para relacionar con el usuario logueado
            $table->foreignId('expense_category_id')->constrained()->onDelete('cascade'); // Categoría del gasto
            $table->bigInteger('amount'); // Valor en CLP (sin decimales)
            $table->date('transaction_date'); // Fecha del gasto
            $table->enum('payment_method', ['Efectivo', 'Transferencia']); // Formato de pago
            $table->string('assigned_to')->nullable(); // A quién se asigna el gasto
            $table->text('comment')->nullable(); // Comentario
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
