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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // El nombre del método de pago (ej. "Efectivo", "Transferencia")
            $table->timestamps();
        });

        // Opcional: Insertar métodos de pago predeterminados
        // Esto ayudará a poblar tu tabla PaymentMethods inicialmente.
        \DB::table('payment_methods')->insert([
            ['name' => 'Efectivo', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tarjeta Credito', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Débito', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Transferencia', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cheque', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mercado Pago', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};