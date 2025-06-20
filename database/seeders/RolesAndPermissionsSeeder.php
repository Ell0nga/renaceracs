<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash; // Asegúrate de importar Hash para las contraseñas

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        // Esto es importante para que los cambios en roles y permisos se apliquen inmediatamente.
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Crear Permisos (si no existen)
        // Usamos findOrCreate para asegurarnos de que no se dupliquen.
        // Permisos de Finanzas
        Permission::findOrCreate('ver finanzas');
        Permission::findOrCreate('gestionar ingresos'); // Crear, editar, eliminar ingresos
        Permission::findOrCreate('gestionar gastos');   // Crear, editar, eliminar gastos
        // Permisos de Usuarios
        Permission::findOrCreate('crear usuarios');
        Permission::findOrCreate('editar usuarios');
        Permission::findOrCreate('eliminar usuarios');
        Permission::findOrCreate('asignar roles');
        // Permisos generales
        Permission::findOrCreate('acceder dashboard'); // Permiso para ver el dashboard general
        Permission::findOrCreate('ver mi perfil');


        // 2. Crear Roles y asignar Permisos (si no existen)
        // Usamos findOrCreate para asegurarnos de que no se dupliquen los roles.

        // Rol Administrador
        $adminRole = Role::findOrCreate('administrador');
        $adminRole->givePermissionTo(Permission::all()); // El administrador tiene todos los permisos

        // Rol Técnico
        $tecnicoRole = Role::findOrCreate('tecnico');
        $tecnicoRole->syncPermissions([ // syncPermissions asegura solo los permisos listados
            'acceder dashboard',
            'ver mi perfil',
            // Aquí podrías añadir permisos específicos para técnicos, ej. 'gestionar inventario'
        ]);

        // Rol Contador
        $contadorRole = Role::findOrCreate('contador');
        $contadorRole->syncPermissions([ // syncPermissions asegura solo los permisos listados
            'acceder dashboard',
            'ver mi perfil',
            'ver finanzas',
            'gestionar ingresos',
            'gestionar gastos',
        ]);

        // Rol Usuario Normal
        $userRole = Role::findOrCreate('usuario_normal');
        $userRole->syncPermissions([ // syncPermissions asegura solo los permisos listados
            'acceder dashboard',
            'ver mi perfil',
        ]);


        // 3. Asignar roles a usuarios existentes o crear usuarios si no existen (para pruebas y consistencia)
        // ¡IMPORTANTE!: Asegúrate de que cada usuario tenga un 'username' y un 'password' para poder iniciar sesión.
        // Usamos la lógica de 'firstOrCreate' o 'first' y luego asignamos/actualizamos el username y el rol.

        // Tu usuario principal (el del problema original del `username` en NULL)
        $hguardaUser = User::firstOrNew(['email' => 'hguarda@renacertv.cl']);
        if (!$hguardaUser->exists) {
            $hguardaUser->name = 'Harry';
            $hguardaUser->username = 'hguarda'; // Define un username
            $hguardaUser->password = Hash::make('password'); // Contraseña segura para desarrollo
            $hguardaUser->save();
        } elseif (is_null($hguardaUser->username)) { // Si ya existe pero no tiene username
            $hguardaUser->username = 'hguarda';
            $hguardaUser->save();
        }
        $hguardaUser->assignRole('administrador'); // Asigna el rol de administrador

        // Usuario 'test@example.com' (si lo tienes en tu DB)
        $testUser = User::firstOrNew(['email' => 'test@example.com']);
        if (!$testUser->exists) {
            $testUser->name = 'Test User';
            $testUser->username = 'testuser'; // Define un username
            $testUser->password = Hash::make('password');
            $testUser->save();
        } elseif (is_null($testUser->username)) {
            $testUser->username = 'testuser';
            $testUser->save();
        }
        // Puedes asignarle un rol si lo deseas, por ejemplo 'usuario_normal' o 'tecnico'
        // $testUser->assignRole('usuario_normal');

        // Usuario 'admin@renacertv.com' (que aparecía en tu lista de DB)
        $adminRenacerUser = User::firstOrNew(['email' => 'admin@renacertv.com']);
        if (!$adminRenacerUser->exists) {
            $adminRenacerUser->name = 'Admin RenacerTV';
            $adminRenacerUser->username = 'adminrenacer'; // Define un username
            $adminRenacerUser->password = Hash::make('password');
            $adminRenacerUser->save();
        } elseif (is_null($adminRenacerUser->username)) {
            $adminRenacerUser->username = 'adminrenacer';
            $adminRenacerUser->save();
        }
        $adminRenacerUser->assignRole('administrador'); // Asigna el rol de administrador

        // Usuario Técnico de prueba
        $tecnicoUser = User::firstOrNew(['email' => 'tecnico@renacertv.com']);
        if (!$tecnicoUser->exists) {
            $tecnicoUser->name = 'Tecnico RenacerTV';
            $tecnicoUser->username = 'tecnicouser'; // Define un username
            $tecnicoUser->password = Hash::make('password');
            $tecnicoUser->save();
        } elseif (is_null($tecnicoUser->username)) {
            $tecnicoUser->username = 'tecnicouser';
            $tecnicoUser->save();
        }
        $tecnicoUser->assignRole('tecnico');

        // Usuario Contador de prueba
        $contadorUser = User::firstOrNew(['email' => 'contador@renacertv.com']);
        if (!$contadorUser->exists) {
            $contadorUser->name = 'Contador RenacerTV';
            $contadorUser->username = 'contadoruser'; // Define un username
            $contadorUser->password = Hash::make('password');
            $contadorUser->save();
        } elseif (is_null($contadorUser->username)) {
            $contadorUser->username = 'contadoruser';
            $contadorUser->save();
        }
        $contadorUser->assignRole('contador');

        // Tu usuario 'harrygl@me.com' (el otro que aparecía en tu lista de DB)
        $harryglUser = User::firstOrNew(['email' => 'harrygl@me.com']);
        if (!$harryglUser->exists) {
            $harryglUser->name = 'Harry G.';
            $harryglUser->username = 'harrygl'; // Define un username
            $harryglUser->password = Hash::make('password');
            $harryglUser->save();
        } elseif (is_null($harryglUser->username)) {
            $harryglUser->username = 'harrygl';
            $harryglUser->save();
        }
        $harryglUser->assignRole('administrador'); // Asigna el rol de administrador
    }
}