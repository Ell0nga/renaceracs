<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create(); // Si esta línea está descomentada y crea usuarios, también coméntala.

        // COMENTA O ELIMINA LA SIGUIENTE SECCIÓN para evitar duplicados:
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Asegúrate de llamar a tu RolesAndPermissionsSeeder aquí.
        // Si ya lo llamas explícitamente con `php artisan db:seed --class=RolesAndPermissionsSeeder`,
        // entonces esta llamada no es estrictamente necesaria aquí si no quieres que se ejecute con db:seed general.
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);
    }
}