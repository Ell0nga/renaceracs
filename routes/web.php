<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PublicFormController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\FiberReport;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
});

Route::get('/finanzas', function () {
    return redirect()->route('finanzas.dashboard');
})->name('finanzas');

// Ruta para obtener el token CSRF
Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

// Rutas públicas para el formulario de técnicos
Route::get('/public-form', [PublicFormController::class, 'show'])->name('public.form');
Route::post('/public-form', [PublicFormController::class, 'store'])->name('public.form.store');

// Ruta para obtener los reportes en formato JSON
Route::get('/fiber-reports-data', function () {
    return response()->json(FiberReport::all());
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Dashboard de Finanzas
    Route::get('/finanzas/dashboard', [IncomeController::class, 'dashboard'])->name('finanzas.dashboard');

    // Finanzas: Ingresos, Gastos y Categorías
    Route::prefix('finanzas')->name('finanzas.')->group(function () {
        // Rutas para ingresos
        Route::resource('incomes', IncomeController::class)->except(['show'])->names([
            'index' => 'incomes.index',
            'store' => 'incomes.store',
            'edit' => 'incomes.edit',
            'update' => 'incomes.update',
            'destroy' => 'incomes.destroy',
        ]);

        // Rutas para gastos
        Route::resource('expenses', ExpenseController::class)->except(['create', 'show'])->names([
            'index' => 'expenses.index',
            'store' => 'expenses.store',
            'edit' => 'expenses.edit',
            'update' => 'expenses.update',
            'destroy' => 'expenses.destroy',
        ]);

        // Ruta para crear categorías
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');

        // Ruta para generar reportes en PDF
        Route::post('/reportes/generar', [IncomeController::class, 'generateReportPdf'])->name('reportes.generar');
    });

    // Agenda
    Route::get('/agenda', [AgendaController::class, 'cards'])->name('agenda.cards');
    Route::post('/agenda', [AgendaController::class, 'store'])->name('agenda.store');

    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Gestión de usuarios para administrador
    Route::middleware('role:administrador')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Reportes de fibra
    Route::get('/fiber-reports', [ReportController::class, 'index'])->name('fiber.reports.index');
});

require __DIR__ . '/auth.php';