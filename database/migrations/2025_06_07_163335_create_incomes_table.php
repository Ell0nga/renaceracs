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
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Para relacionar con el usuario logueado
            $table->string('client_number')->nullable(); // Número de cliente
            $table->bigInteger('amount'); // Valor en CLP (sin decimales, se usa bigInteger)
            $table->date('transaction_date'); // Fecha del ingreso
            $table->enum('type', ['Mensualidad', 'Instalacion']); // Tipo de ingreso
            $table->enum('payment_method', ['Efectivo', 'Tarjeta Credito', 'Debito', 'Transferencia']); // Formato de pago
            $table->text('comment')->nullable(); // Comentario
            $table->timestamps(); // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
