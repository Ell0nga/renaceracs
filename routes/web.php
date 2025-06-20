<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\AgendaEvent;
use Carbon\Carbon;




Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
});

Route::get('/finanzas', function () {
    return redirect()->route('finanzas.dashboard');
})->name('finanzas');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Dashboard de Finanzas
    Route::get('/finanzas/dashboard', [IncomeController::class, 'dashboard'])->name('finanzas.dashboard');

    // Finanzas: Ingresos, Gastos y Categorías
    Route::prefix('finanzas')->name('finanzas.')->group(function () {
        Route::resource('incomes', IncomeController::class)->except(['show']);
        Route::resource('expenses', ExpenseController::class)->except(['create', 'show']);
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');

        // Ruta para generar reportes en PDF
        Route::get('/reportes/generar', [IncomeController::class, 'generateReportPdf'])->name('reportes.generar');
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
});

require __DIR__ . '/auth.php';
