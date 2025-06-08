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
        // Si necesitas pasar las categorías de gasto al index para OTRO modal de creación allí, déjalo.
        // Pero para el dashboard, ya las estamos pasando desde IncomeController@dashboard
        $expenseCategories = ExpenseCategory::all(); // Asegúrate de que esto siga siendo necesario para esta vista si no usas el dashboard
        // CAMBIO AQUÍ: La vista ahora está en 'finanzas.expenses.index'
        return view('finanzas.expenses.index', compact('expenses', 'expenseCategories'));
    }

    // ELIMINAMOS EL MÉTODO 'create()' YA QUE EL FORMULARIO ESTÁ EN UN MODAL EN EL DASHBOARD.
    // Su lógica (pasar currentDate, expenseCategories, paymentMethods) ahora la maneja IncomeController@dashboard
    /*
    public function create()
    {
        $currentDate = Carbon::now()->format('d-m-Y');
        $expenseCategories = ExpenseCategory::all();
        $paymentMethods = ['Efectivo', 'Transferencia'];

        return view('expenses.create', compact('currentDate', 'expenseCategories', 'paymentMethods'));
    }
    */

    /**
     * Almacena un nuevo gasto en la base de datos.
     */
    public function store(Request $request)
    {
        try {
            // Modificamos la validación para usar un errorBag específico
            $request->validateWithBag('expenseCreation', [ // <--- Aquí el cambio
                'expense_category_id' => 'required|exists:expense_categories,id',
                'amount' => 'required|integer|min:0',
                'transaction_date' => 'required|date_format:Y-m-d', // Cambiado a Y-m-d
                'payment_method' => 'required|in:Efectivo,Transferencia',
                'assigned_to' => 'nullable|string|max:255',
                'comment' => 'nullable|string|max:1000',
            ]);
        } catch (ValidationException $e) {
            // Si hay errores de validación, redirigimos de nuevo al dashboard
            // con los errores en el errorBag 'expenseCreation'
            // CAMBIO AQUÍ: Redirigir al dashboard de finanzas
            return redirect()->route('finanzas.dashboard')
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

        // Redireccionar al dashboard de finanzas después de guardar exitosamente
        // CAMBIO AQUÍ: Redirigir al dashboard de finanzas
        return redirect()->route('finanzas.dashboard')->with('success', 'Gasto registrado exitosamente.');
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

        // CAMBIO AQUÍ: La vista ahora está en 'finanzas.expenses.edit'
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

        // CAMBIO AQUÍ: Redirigir a la lista de gastos bajo finanzas
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

        // CAMBIO AQUÍ: Redirigir a la lista de gastos bajo finanzas
        return redirect()->route('finanzas.expenses.index')->with('success', 'Gasto eliminado exitosamente.');
    }
}