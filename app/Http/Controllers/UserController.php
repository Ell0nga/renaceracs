<?php

namespace App\Http\Controllers;

use App\Models\User; // Importa el modelo User
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role; // Importa el modelo Role de Spatie
use Illuminate\Support\Facades\Hash; // Para encriptar contraseñas
use Illuminate\Validation\Rule; // Para la validación de unicidad en 'update'

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     * Muestra una lista paginada de usuarios.
     */
    public function index()
    {
        // Paginar los resultados para no cargar todos los usuarios a la vez, lo cual es eficiente.
        $users = User::paginate(10); // Puedes ajustar el número por página
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     * Muestra el formulario para crear un nuevo usuario.
     */
    public function create()
    {
        // Obtener todos los roles disponibles para la selección en el formulario.
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     * Guarda un nuevo usuario creado en la base de datos.
     */
    public function store(Request $request)
    {
        // Validar los datos del formulario.
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // 'username' debe ser requerido, string, max 255 y único en la tabla 'users'
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            // 'email' debe ser requerido, string, email válido, max 255 y único en la tabla 'users'
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            // 'password' debe ser requerido, string, mínimo 8 caracteres y confirmado (password_confirmation debe coincidir)
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            // 'roles' debe ser un array y cada elemento del array debe existir en la tabla 'roles'
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,name'],
        ]);

        // Crear el nuevo usuario.
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username, // Guardamos el nombre de usuario
            'email' => $request->email,
            'password' => Hash::make($request->password), // Encriptar la contraseña
        ]);

        // Asignar los roles seleccionados al usuario.
        // syncRoles() elimina los roles actuales y asigna los nuevos.
        $user->syncRoles($request->roles);

        // Redirigir de vuelta a la lista de usuarios con un mensaje de éxito.
        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Show the form for editing the specified user.
     * Muestra el formulario para editar un usuario existente.
     */
    public function edit(User $user)
    {
        // Obtener todos los roles disponibles.
        $roles = Role::all();
        // Obtener los nombres de los roles que el usuario actual ya tiene.
        $userRoles = $user->roles->pluck('name')->toArray();
        return view('users.edit', compact('user', 'roles', 'userRoles'));
    }

    /**
     * Update the specified user in storage.
     * Actualiza un usuario existente en la base de datos.
     */
    public function update(Request $request, User $user)
    {
        // Validar los datos del formulario para la actualización.
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // 'username' debe ser único, pero ignorar el username del usuario actual
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            // 'email' debe ser único, pero ignorar el email del usuario actual
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            // 'password' es opcional en la actualización. Si se envía, debe ser mínimo 8 y confirmado.
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,name'],
        ]);

        // Actualizar los campos del usuario.
        $user->name = $request->name;
        $user->username = $request->username; // Actualizamos el nombre de usuario
        $user->email = $request->email;

        // Si se proporcionó una nueva contraseña, encriptarla y guardarla.
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        // Sincronizar los roles del usuario.
        $user->syncRoles($request->roles);

        // Redirigir de vuelta a la lista de usuarios con un mensaje de éxito.
        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Remove the specified user from storage.
     * Elimina un usuario de la base de datos.
     */
    public function destroy(User $user)
    {
        // Eliminar el usuario.
        $user->delete();
        // Redirigir de vuelta a la lista de usuarios con un mensaje de éxito.
        return redirect()->route('users.index')->with('success', 'Usuario eliminado exitosamente.');
    }
}