<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ExpenseController;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Redirige la ruta raíz a /dashboard si el usuario está autenticado
// De lo contrario, muestra la página de bienvenida de Laravel (o la de login si no hay bienvenida)
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard'); // Ahora apunta al NUEVO dashboard de botones
    }
    return view('welcome');
});

// Rutas protegidas por autenticación y verificación de email
Route::middleware(['auth', 'verified'])->group(function () {
    // *******************************************************************
    // NUEVA RUTA PARA EL DASHBOARD PRINCIPAL (CON BOTONES)
    // Este cargará resources/views/dashboard.blade.php
    // *******************************************************************
    Route::get('/dashboard', function () {
        return view('dashboard'); // Esta vista es la nueva con los botones
    })->name('dashboard');

    // *******************************************************************
    // RUTA PARA EL DASHBOARD DE FINANZAS (Ingresos y Gastos)
    // Este es tu dashboard anterior, ahora con una URL y nombre dedicados.
    // *******************************************************************
    Route::get('/finanzas/dashboard', [IncomeController::class, 'dashboard'])->name('finanzas.dashboard');

    // *******************************************************************
    // NUEVO GRUPO DE RUTAS PARA INGRESOS Y GASTOS BAJO /finanzas/
    // Esto asegura URLs como /finanzas/incomes y nombres como finanzas.incomes.index
    // *******************************************************************
    Route::prefix('finanzas')->name('finanzas.')->group(function () {
        // Rutas para Ingresos (CRUD básico)
        // Usamos Route::resource para simplificar las rutas de IncomeController
        // Excluimos 'show' si no tienes una vista específica para mostrar un solo ingreso.
        Route::resource('incomes', IncomeController::class)->except(['show']);

        // Rutas para Gastos (CRUD básico)
        // Usamos Route::resource para simplificar las rutas de ExpenseController
        // Excluimos 'create' porque el formulario está en un modal en el dashboard
        // Excluimos 'show' si no tienes una vista específica para mostrar un solo gasto.
        Route::resource('expenses', ExpenseController::class)->except(['create', 'show']);
    });


    // Rutas del perfil de usuario (gestionadas por Breeze) - SIN CAMBIOS
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas de autenticación generadas por Breeze (login, register, etc.) - SIN CAMBIOS
require __DIR__ . '/auth.php';