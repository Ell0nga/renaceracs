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
        Schema::table('users', function (Blueprint $table) {
            // Añade la columna 'username'
            // La hacemos 'nullable()' temporalmente para que los usuarios existentes no den error.
            // Después de la migración, si quieres que sea obligatorio para nuevos usuarios,
            // podrías considerar ejecutar otra migración para quitar nullable()
            // y asegurarte de que todos los usuarios existentes tengan un username.
            $table->string('username')->unique()->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Si haces un rollback de la migración, eliminará la columna 'username'.
            $table->dropColumn('username');
        });
    }
};