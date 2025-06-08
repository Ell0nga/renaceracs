<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IncomeController; // Importamos el nuevo controlador
use App\Http\Controllers\ExpenseController; // Asegúrate de que esta línea también esté aquí

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // Asegúrate de que esta línea también esté aquí para Auth::check()

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Redirige la ruta raíz a /dashboard si el usuario está autenticado
// De lo contrario, muestra la página de bienvenida de Laravel (o la de login si no hay bienvenida)
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome'); // O return redirect()->route('login'); si quieres que lo primero sea el login
});

// Rutas protegidas por autenticación y verificación de email
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard personalizado
    Route::get('/dashboard', [IncomeController::class, 'dashboard'])->name('dashboard');

    // Rutas para Ingresos (CRUD básico)
    // AÑADE ESTA LÍNEA para el formulario de creación de ingresos
    Route::get('/incomes/create', [IncomeController::class, 'create'])->name('incomes.create'); // Formulario para crear
    Route::get('/incomes', [IncomeController::class, 'index'])->name('incomes.index'); // Lista de ingresos
    Route::post('/incomes', [IncomeController::class, 'store'])->name('incomes.store'); // Guardar nuevo ingreso
    Route::get('/incomes/{income}/edit', [IncomeController::class, 'edit'])->name('incomes.edit'); // Formulario para editar
    Route::put('/incomes/{income}', [IncomeController::class, 'update'])->name('incomes.update'); // Actualizar ingreso
    Route::delete('/incomes/{income}', [IncomeController::class, 'destroy'])->name('incomes.destroy'); // Eliminar ingreso

    // Rutas para Gastos (CRUD básico)
    // AÑADE ESTA LÍNEA para el formulario de creación de gastos
    Route::get('/expenses/create', [App\Http\Controllers\ExpenseController::class, 'create'])->name('expenses.create'); // Formulario para crear
    Route::get('/expenses', [App\Http\Controllers\ExpenseController::class, 'index'])->name('expenses.index'); // Lista de gastos
    Route::post('/expenses', [App\Http\Controllers\ExpenseController::class, 'store'])->name('expenses.store'); // Guardar nuevo gasto
    Route::get('/expenses/{expense}/edit', [App\Http\Controllers\ExpenseController::class, 'edit'])->name('expenses.edit'); // Formulario para editar
    Route::put('/expenses/{expense}', [App\Http\Controllers\ExpenseController::class, 'update'])->name('expenses.update'); // Actualizar gasto
    Route::delete('/expenses/{expense}', [App\Http\Controllers\ExpenseController::class, 'destroy'])->name('expenses.destroy'); // Eliminar gasto

    // Rutas del perfil de usuario (gestionadas por Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas de autenticación generadas por Breeze (login, register, etc.)
require __DIR__ . '/auth.php';