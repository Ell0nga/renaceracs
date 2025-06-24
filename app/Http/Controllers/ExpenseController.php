<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory; // Importamos el modelo de categoría de gasto
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException; // ¡Importa esto para manejar los errores!

class ExpenseController extends Controller
{
    /**
     * Muestra una lista de todos los gastos.
     */
    public function index()
    {
        $expenses = Auth::user()->expenses()->with('category')->latest()->paginate(10); // Pagina los gastos y carga la categoría
        $expenseCategories = ExpenseCategory::all(); // Asegúrate de que esto siga siendo necesario para esta vista si no usas el dashboard
        return view('finanzas.expenses.index', compact('expenses', 'expenseCategories'));
    }

    /**
     * Almacena un nuevo gasto en la base de datos.
     */
    public function store(Request $request)
    {
        try {
            // Modificamos la validación para usar un errorBag específico
            $request->validateWithBag('expenseCreation', [
                'expense_category_id' => 'required|exists:expense_categories,id',
                'amount' => 'required|integer|min:0',
                'transaction_date' => 'required|date_format:Y-m-d', // Cambiado a Y-m-d
                'payment_method' => 'required|in:Efectivo,Transferencia',
                'assigned_to' => 'nullable|string|max:255',
                'comment' => 'nullable|string|max:1000',
            ]);
        } catch (ValidationException $e) {
            // Si hay errores de validación, redirigimos de nuevo a la página anterior
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors(), 'expenseCreation');
        }

        // Si el input type="date" ya envía YYYY-MM-DD, no necesitas esta conversión
        $transactionDate = $request->transaction_date; // Ya debería venir en Y-m-d

        Auth::user()->expenses()->create([
            'expense_category_id' => $request->expense_category_id,
            'amount' => $request->amount,
            'transaction_date' => $transactionDate,
            'payment_method' => $request->payment_method,
            'assigned_to' => $request->assigned_to,
            'comment' => $request->comment,
        ]);

        // Redirigir hacia atrás para permanecer en la página actual
        return redirect()->back()->with('success', 'Gasto registrado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un gasto existente.
     */
    public function edit(Expense $expense)
    {
        if ($expense->user_id !== Auth::id()) {
            abort(403);
        }

        $expenseCategories = ExpenseCategory::all();
        $paymentMethods = ['Efectivo', 'Transferencia'];

        // Formatear la fecha para el campo de entrada (type="date" necesita YYYY-MM-DD)
        $expense->transaction_date_formatted = Carbon::parse($expense->transaction_date)->format('Y-m-d');

        return view('finanzas.expenses.edit', compact('expense', 'expenseCategories', 'paymentMethods'));
    }

    /**
     * Actualiza un gasto existente en la base de datos.
     */
    public function update(Request $request, Expense $expense)
    {
        if ($expense->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|integer|min:0',
            'transaction_date' => 'required|date_format:Y-m-d', // Cambiado a Y-m-d
            'payment_method' => 'required|in:Efectivo,Transferencia',
            'assigned_to' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Ya debería venir en YYYY-MM-DD si es type="date"
        $transactionDate = $request->transaction_date;

        $expense->update([
            'expense_category_id' => $request->expense_category_id,
            'amount' => $request->amount,
            'transaction_date' => $transactionDate,
            'payment_method' => $request->payment_method,
            'assigned_to' => $request->assigned_to,
            'comment' => $request->comment,
        ]);

        return redirect()->route('finanzas.expenses.index')->with('success', 'Gasto actualizado exitosamente.');
    }

    /**
     * Elimina un gasto de la base de datos.
     */
    public function destroy(Expense $expense)
    {
        if ($expense->user_id !== Auth::id()) {
            abort(403);
        }

        $expense->delete();

        return redirect()->route('finanzas.expenses.index')->with('success', 'Gasto eliminado exitosamente.');
    }
}