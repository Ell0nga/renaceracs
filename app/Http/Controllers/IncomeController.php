<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class IncomeController extends Controller
{
    /**
     * Muestra el dashboard con resumen de ingresos y gastos.
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        // Se mantiene la variable $incomes por si alguna otra parte de la vista la usa,
        // aunque para "Últimos Registros" usaremos $recentIncomes y $recentExpenses directamente
        $incomes = $user->incomes()->latest()->limit(5)->get(); // Esto es independiente de los "Últimos Registros" de la tabla

        // --- Lógica de Filtros para Gráficos y Totales (Tu código existente) ---
        $startDate = $request->input('start_date') ? Carbon::createFromFormat('d-m-Y', $request->input('start_date'))->startOfDay() : null;
        $endDate = $request->input('end_date') ? Carbon::createFromFormat('d-m-Y', $request->input('end_date'))->endOfDay() : null;
        $filterType = $request->input('type'); // Para ingresos
        $filterExpenseCategory = $request->input('expense_category_id'); // Para gastos

        // Calcular Ingresos Totales
        $totalIncomesQuery = $user->incomes();
        if ($startDate) {
            $totalIncomesQuery->whereDate('transaction_date', '>=', $startDate);
        }
        if ($endDate) {
            $totalIncomesQuery->whereDate('transaction_date', '<=', $endDate);
        }
        if ($filterType) {
            $totalIncomesQuery->where('type', $filterType);
        }
        $totalIncomes = $totalIncomesQuery->sum('amount');

        // Ingresos por Categoría
        $monthlyIncomesQuery = $user->incomes()->where('type', 'Mensualidad');
        $installationIncomesQuery = $user->incomes()->where('type', 'Instalacion');

        if ($startDate) {
            $monthlyIncomesQuery->whereDate('transaction_date', '>=', $startDate);
            $installationIncomesQuery->whereDate('transaction_date', '>=', $startDate);
        }
        if ($endDate) {
            $monthlyIncomesQuery->whereDate('transaction_date', '<=', $endDate);
            $installationIncomesQuery->whereDate('transaction_date', '<=', $endDate);
        }

        $totalMonthlyIncomes = $monthlyIncomesQuery->sum('amount');
        $totalInstallationIncomes = $installationIncomesQuery->sum('amount');

        // Calcular Gastos Totales
        $totalExpensesQuery = $user->expenses();
        if ($startDate) {
            $totalExpensesQuery->whereDate('transaction_date', '>=', $startDate);
        }
        if ($endDate) {
            $totalExpensesQuery->whereDate('transaction_date', '<=', $endDate);
        }
        if ($filterExpenseCategory) {
            $totalExpensesQuery->where('expense_category_id', $filterExpenseCategory);
        }
        $totalExpenses = $totalExpensesQuery->sum('amount');

        $netIncome = $totalIncomes - $totalExpenses;

        // Datos para el gráfico de Ingresos Diarios
        $dailyIncomes = $user->incomes()
            ->select(DB::raw('DATE(transaction_date) as date'), DB::raw('SUM(amount) as total_amount'))
            ->when($startDate, fn($query) => $query->whereDate('transaction_date', '>=', $startDate))
            ->when($endDate, fn($query) => $query->whereDate('transaction_date', '<=', $endDate))
            ->when($filterType, fn($query) => $query->where('type', $filterType))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $incomeChartLabels = $dailyIncomes->pluck('date')->map(fn($date) => Carbon::parse($date)->format('d-m-Y'));
        $incomeChartData = $dailyIncomes->pluck('total_amount');

        // Datos para el gráfico de Gastos Diarios
        $dailyExpenses = $user->expenses()
            ->select(DB::raw('DATE(transaction_date) as date'), DB::raw('SUM(amount) as total_amount'))
            ->when($startDate, fn($query) => $query->whereDate('transaction_date', '>=', $startDate))
            ->when($endDate, fn($query) => $query->whereDate('transaction_date', '<=', $endDate))
            ->when($filterExpenseCategory, fn($query) => $query->where('expense_category_id', $filterExpenseCategory))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $expenseChartLabels = $dailyExpenses->pluck('date')->map(fn($date) => Carbon::parse($date)->format('d-m-Y'));
        $expenseChartData = $dailyExpenses->pluck('total_amount');

        // =========================================================================
        // AÑADE ESTAS LÍNEAS TEMPORALMENTE PARA DEPURAR LOS GRÁFICOS
        // =========================================================================
        //dump('Income Chart Labels:');
        //dump($incomeChartLabels->toArray());
        //dump('Income Chart Data:');
        //dump($incomeChartData->toArray());
        //dump('Expense Chart Labels:');
        //dd('Expense Chart Data:', $expenseChartData->toArray());
        // =========================================================================

        // Obtener todas las categorías de gasto
        $expenseCategories = ExpenseCategory::all();

        // Preparar las fechas para los campos de entrada de filtro
        $startDateInput = $startDate ? $startDate->format('d-m-Y') : null;
        $endDateInput = $endDate ? $endDate->format('d-m-Y') : null;

        // Datos para el formulario del modal de Ingresos
        $currentDate = Carbon::now()->format('Y-m-d');
        $incomeTypes = ['Mensualidad', 'Instalacion'];
        $paymentMethods = ['Efectivo', 'Tarjeta Credito', 'Debito', 'Transferencia'];

        // Datos para el formulario del modal de Gastos (métodos de pago para gastos)
        $expensePaymentMethods = ['Efectivo', 'Transferencia'];

        // --- Lógica: Últimos Registros (mostrar 3 ingresos y 3 gastos por separado) ---
        // Se mantiene orderBy('transaction_date', 'desc') asuming Opción 1 de solución de fechas será implementada
        $recentIncomes = $user->incomes()->orderBy('transaction_date', 'desc')->take(3)->get()->map(function ($item) {
            $item->type_label = 'Ingreso';
            $item->category_name = null; // No hay categoría para ingresos
            $item->client_description = $item->client_number; // Usamos client_number como descripción para ingresos
            return $item;
        });

        // Se mantiene orderBy('transaction_date', 'desc') asuming Opción 1 de solución de fechas será implementada
        $recentExpenses = $user->expenses()->with('category')->orderBy('transaction_date', 'desc')->take(3)->get()->map(function ($item) {
            $item->type_label = 'Gasto';
            $item->category_name = $item->category ? $item->category->name : 'Sin Categoría';
            $item->client_description = $item->assigned_to ?? $item->description; // Usamos assigned_to o description para gastos
            return $item;
        });

        // Pasa todos los datos necesarios a la vista de finanzas
        return view('finanzas.finanzas', compact(
            'incomes',
            'totalIncomes',
            'totalMonthlyIncomes',
            'totalInstallationIncomes',
            'totalExpenses',
            'netIncome',
            'incomeChartLabels',
            'incomeChartData',
            'expenseChartLabels',
            'expenseChartData',
            'expenseCategories',
            'startDateInput',
            'endDateInput',
            'filterType',
            'filterExpenseCategory',
            'currentDate',
            'incomeTypes',
            'paymentMethods',
            'expensePaymentMethods',
            // Pasa las colecciones separadas para la tabla principal
            'recentIncomes',
            'recentExpenses'
        ));
    }

    /**
     * Muestra una lista de todos los ingresos.
     */
    public function index()
    {
        $incomes = Auth::user()->incomes()->latest()->paginate(10);
        return view('finanzas.incomes.index', compact('incomes'));
    }

    /**
     * Almacena un nuevo ingreso en la base de datos.
     */
    public function store(Request $request)
    {
        try {
            $request->validateWithBag('incomeCreation', [
                'client_number' => 'nullable|string|max:255',
                'amount' => 'required|integer|min:0',
                'transaction_date' => 'required|date_format:Y-m-d',
                'type' => 'required|in:Mensualidad,Instalacion',
                'payment_method' => 'required|in:Efectivo,Tarjeta Credito,Debito,Transferencia',
                'comment' => 'nullable|string|max:1000',
            ]);
        } catch (ValidationException $e) {
            return redirect()->route('finanzas.dashboard')
                ->withInput()
                ->withErrors($e->errors(), 'incomeCreation');
        }

        // Modificación para guardar la hora actual en transaction_date
        $transactionDate = Carbon::createFromFormat('Y-m-d', $request->transaction_date)
            ->setTime(Carbon::now()->hour, Carbon::now()->minute, Carbon::now()->second);


        Auth::user()->incomes()->create([
            'client_number' => $request->client_number,
            'amount' => $request->amount,
            'transaction_date' => $transactionDate,
            'type' => $request->type,
            'payment_method' => $request->payment_method,
            'comment' => $request->comment,
        ]);

        // Redirigir al dashboard después de crear un ingreso
        return redirect()->route('finanzas.dashboard')->with('success', 'Ingreso registrado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un ingreso existente.
     */
    public function edit(Income $income)
    {
        if ($income->user_id !== Auth::id()) {
            abort(403);
        }

        $incomeTypes = ['Mensualidad', 'Instalacion'];
        $paymentMethods = ['Efectivo', 'Tarjeta Credito', 'Debito', 'Transferencia'];

        $income->transaction_date_formatted = Carbon::parse($income->transaction_date)->format('Y-m-d');

        return view('finanzas.incomes.edit', compact('income', 'incomeTypes', 'paymentMethods'));
    }

    /**
     * Actualiza un ingreso existente en la base de datos.
     */
    public function update(Request $request, Income $income)
    {
        if ($income->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'client_number' => 'nullable|string|max:255',
            'amount' => 'required|integer|min:0',
            'transaction_date' => 'required|date_format:Y-m-d',
            'type' => 'required|in:Mensualidad,Instalacion',
            'payment_method' => 'required|in:Efectivo,Tarjeta Credito,Debito,Transferencia',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Modificación para guardar la hora actual en transaction_date
        $transactionDate = Carbon::createFromFormat('Y-m-d', $request->transaction_date)
            ->setTime(Carbon::now()->hour, Carbon::now()->minute, Carbon::now()->second);

        $income->update([
            'client_number' => $request->client_number,
            'amount' => $request->amount,
            'transaction_date' => $transactionDate,
            'type' => $request->type,
            'payment_method' => $request->payment_method,
            'comment' => $request->comment,
        ]);

        return redirect()->route('finanzas.incomes.index')->with('success', 'Ingreso actualizado exitosamente.');
    }

    /**
     * Elimina un ingreso de la base de datos.
     */
    public function destroy(Income $income)
    {
        if ($income->user_id !== Auth::id()) {
            abort(403);
        }

        $income->delete();

        return redirect()->route('finanzas.incomes.index')->with('success', 'Ingreso eliminado exitosamente.');
    }
}