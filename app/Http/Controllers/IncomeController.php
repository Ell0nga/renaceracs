<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF; // Asegúrate de que esta línea esté correcta para tu configuración de PDF
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Redirect;

class IncomeController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        // Definición de variables para los modales
        $currentDate = Carbon::now()->format('Y-m-d');
        $incomeTypes = ['Mensualidad', 'Instalacion'];
        $paymentMethods = ['Efectivo', 'Tarjeta Credito', 'Debito', 'Transferencia'];
        $expensePaymentMethods = ['Efectivo', 'Transferencia'];
        $expenseCategories = ExpenseCategory::all();

        // Obtener fechas y filtros de la request
        $startDate = $request->input('start_date') ? Carbon::createFromFormat('d-m-Y', $request->input('start_date'))->startOfDay() : null;
        $endDate = $request->input('end_date') ? Carbon::createFromFormat('d-m-Y', $request->input('end_date'))->endOfDay() : null;
        $filterType = $request->input('type');
        $filterExpenseCategory = $request->input('expense_category_id');

        // --- Consultas base para INCOMES y EXPENSES, con filtros de fecha ---
        $baseIncomesQuery = $user->incomes();
        $baseExpensesQuery = $user->expenses();

        if ($startDate) {
            $baseIncomesQuery->whereDate('transaction_date', '>=', $startDate);
            $baseExpensesQuery->whereDate('transaction_date', '>=', $startDate);
        }
        if ($endDate) {
            $baseIncomesQuery->whereDate('transaction_date', '<=', $endDate);
            $baseExpensesQuery->whereDate('transaction_date', '<=', $endDate);
        }

        // --- PAGINACIÓN PARA LAS TABLAS PRINCIPALES (INGRESOS Y GASTOS) ---
        // ESTO ES LO QUE ARREGLA EL ERROR links()
        $incomes = (clone $baseIncomesQuery)->latest()->paginate(10, ['*'], 'income_page');
        $expenses = (clone $baseExpensesQuery)->with('category')->latest()->paginate(10, ['*'], 'expense_page');

        // --- Lógica para calcular totales de ingresos (clonando la consulta base para aplicar filtros específicos) ---
        $totalIncomesQuery = (clone $baseIncomesQuery);
        if ($filterType)
            $totalIncomesQuery->where('type', $filterType);
        $totalIncomes = $totalIncomesQuery->sum('amount'); // Total REAL de todos los ingresos

        // Lógica para calcular ingresos por tipo (Mensualidad, Instalacion) - REALES
        $monthlyIncomesQuery = (clone $baseIncomesQuery)->where('type', 'Mensualidad');
        $installationIncomesQuery = (clone $baseIncomesQuery)->where('type', 'Instalacion');

        $totalMonthlyIncomes = $monthlyIncomesQuery->sum('amount'); // Total REAL de mensualidades
        $totalInstallationIncomes = $installationIncomesQuery->sum('amount'); // Total REAL de instalaciones

        // Lógica para calcular total de gastos - REALES
        $totalExpensesQuery = (clone $baseExpensesQuery);
        if ($filterExpenseCategory)
            $totalExpensesQuery->where('expense_category_id', $filterExpenseCategory);
        $totalExpenses = $totalExpensesQuery->sum('amount'); // Total REAL de gastos

        // Cálculo del ingreso neto global (REAL)
        $netIncome = $totalIncomes - $totalExpenses;

        // ***** NUEVA LÓGICA: CÁLCULOS AJUSTADOS PARA LA VISUALIZACIÓN DE LAS TARJETAS *****
        $displayTotalIncomes = $totalIncomes; // Esto es $totalMonthlyIncomes + $totalInstallationIncomes
        $displayMonthlyIncomes = $totalMonthlyIncomes; // Valor REAL de mensualidades
        $displayInstallationIncomes = $totalInstallationIncomes; // Valor REAL de instalaciones

        // Calcular el déficit o superávit de instalaciones vs gastos
        $installationCoverageBalance = $totalInstallationIncomes - $totalExpenses;

        // Determinar el monto del déficit de instalaciones a mostrar en la tarjeta de gastos (solo si hay déficit)
        $displayInstallationDeficit = 0;
        if ($installationCoverageBalance < 0) {
            $displayInstallationDeficit = abs($installationCoverageBalance); // El valor absoluto del déficit
        }

        // ************************************************************************************************

        // ***** LÓGICA ESPECÍFICA PARA LA TARJETA "INGRESO NETO GLOBAL" *****
        $displayNetMonthlyIncomes = 0;
        $displayNetInstallationIncomes = 0;

        if ($installationCoverageBalance >= 0) {
            // Caso 1: Instalaciones cubrieron los gastos (o hay superávit)
            $displayNetMonthlyIncomes = $totalMonthlyIncomes; // Mensualidades quedan intactas
            $displayNetInstallationIncomes = $installationCoverageBalance; // Es el remanente de instalaciones
        } else {
            // Caso 2: Gastos excedieron las instalaciones (hay déficit)
            $excessExpensesNotCoveredByInstallations = abs($installationCoverageBalance); // El monto que falta de instalaciones
            $displayNetMonthlyIncomes = $totalMonthlyIncomes - $excessExpensesNotCoveredByInstallations; // Mensualidades cubren el resto
            $displayNetInstallationIncomes = 0; // Las instalaciones se consumieron completamente
        }

        // ********************************************************************

        // Lógica para gráficos de ingresos diarios (clonando la consulta base)
        $dailyIncomes = (clone $baseIncomesQuery)
            ->select(DB::raw('DATE(transaction_date) as date'), DB::raw('SUM(amount) as total_amount'))
            ->when($filterType, fn($query) => $query->where('type', $filterType))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $incomeChartLabels = $dailyIncomes->pluck('date')->map(fn($date) => Carbon::parse($date)->format('d-m-Y'));
        $incomeChartData = $dailyIncomes->pluck('total_amount');

        // Lógica para gráficos de gastos diarios (clonando la consulta base)
        $dailyExpenses = (clone $baseExpensesQuery)
            ->select(DB::raw('DATE(transaction_date) as date'), DB::raw('SUM(amount) as total_amount'))
            ->when($filterExpenseCategory, fn($query) => $query->where('expense_category_id', $filterExpenseCategory))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $expenseChartLabels = $dailyExpenses->pluck('date')->map(fn($date) => Carbon::parse($date)->format('d-m-Y'));
        $expenseChartData = $dailyExpenses->pluck('total_amount');

        // Datos para los inputs de filtro de fecha (mantienen el formato d-m-Y para la vista)
        $startDateInput = $startDate ? $startDate->format('d-m-Y') : null;
        $endDateInput = $endDate ? $endDate->format('d-m-Y') : null;

        // Últimos 3 ingresos (estos NO necesitan paginación, son solo para "recientes")
        $recentIncomes = $user->incomes()->orderBy('transaction_date', 'desc')->take(3)->get()->map(function ($item) {
            $item->type_label = 'Ingreso';
            $item->category_name = null;
            $item->client_description = $item->client_number;
            return $item;
        });

        // Últimos 3 gastos (estos NO necesitan paginación, son solo para "recientes")
        $recentExpenses = $user->expenses()->with('category')->orderBy('transaction_date', 'desc')->take(3)->get()->map(function ($item) {
            $item->type_label = 'Gasto';
            $item->category_name = $item->category ? $item->category->name : 'Sin Categoría';
            $item->client_description = $item->assigned_to ?? $item->description;
            return $item;
        });

        // Retornar la vista con todas las variables necesarias
        return view('finanzas.finanzas', compact(
            'incomes', // AHORA ES UN OBJETO PAGINADO
            'expenses', // AHORA ES UN OBJETO PAGINADO
            'totalIncomes',
            'totalMonthlyIncomes',
            'totalInstallationIncomes',
            'totalExpenses',
            'netIncome',
            'displayNetMonthlyIncomes',
            'displayNetInstallationIncomes',
            'displayTotalIncomes',
            'displayMonthlyIncomes',
            'displayInstallationIncomes',
            'displayInstallationDeficit',
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
            'recentIncomes', // Estos siguen siendo colecciones simples, lo cual es correcto para "recientes"
            'recentExpenses' // Estos siguen siendo colecciones simples, lo cual es correcto para "recientes"
        ));
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $query = $user->incomes()->latest();

        if ($request->filled('client_number')) {
            $query->where('client_number', 'like', '%' . $request->client_number . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('transaction_date')) {
            $query->whereDate('transaction_date', Carbon::createFromFormat('d-m-Y', $request->transaction_date)->format('Y-m-d'));
        }

        $incomes = $query->paginate(10); // Esta paginación está correcta para finanzas.incomes.index

        $incomeTypes = ['Mensualidad', 'Instalacion'];
        $paymentMethods = ['Efectivo', 'Tarjeta Credito', 'Debito', 'Transferencia'];

        if ($request->ajax()) {
            return response()->view('finanzas.incomes.partials.table', compact('incomes'));
        }

        return view('finanzas.incomes.index', compact('incomes', 'incomeTypes', 'paymentMethods'));
    }

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
            ], [], [
                'client_number' => __('Número de Cliente'),
                'amount' => __('Monto'),
                'transaction_date' => __('Fecha de Transacción'),
                'type' => __('Tipo de Ingreso'),
                'payment_method' => __('Método de Pago'),
                'comment' => __('Comentario'),
            ]);
        } catch (ValidationException $e) {
            return Redirect::back()->withErrors($e->errors(), 'incomeCreation')->withInput();
        }

        Auth::user()->incomes()->create([
            'client_number' => $request->client_number,
            'amount' => $request->amount,
            'transaction_date' => Carbon::createFromFormat('Y-m-d', $request->transaction_date)
                ->setTime(Carbon::now()->hour, Carbon::now()->minute, Carbon::now()->second),
            'type' => $request->type,
            'payment_method' => $request->payment_method,
            'comment' => $request->comment,
        ]);

        $previousUrl = url()->previous();
        $dashboardUrl = route('finanzas.dashboard');

        if (str_contains($previousUrl, $dashboardUrl)) {
            return Redirect::route('finanzas.dashboard')->with('success', 'Ingreso registrado exitosamente.');
        } else {
            return Redirect::back()->with('success', 'Ingreso registrado exitosamente.');
        }
    }

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

        $income->update([
            'client_number' => $request->client_number,
            'amount' => $request->amount,
            'transaction_date' => Carbon::createFromFormat('Y-m-d', $request->transaction_date)
                ->setTime(Carbon::now()->hour, Carbon::now()->minute, Carbon::now()->second),
            'type' => $request->type,
            'payment_method' => $request->payment_method,
            'comment' => $request->comment,
        ]);

        return redirect()->route('finanzas.incomes.index')->with('success', 'Ingreso actualizado exitosamente.');
    }

    public function destroy(Income $income)
    {
        if ($income->user_id !== Auth::id()) {
            abort(403);
        }

        $income->delete();

        return redirect()->route('finanzas.incomes.index')->with('success', 'Ingreso eliminado exitosamente.');
    }

    public function generateReportPdf(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::createFromFormat('d-m-Y', $request->input('start_date'))->startOfDay() : null;
        $endDate = $request->input('end_date') ? Carbon::createFromFormat('d-m-Y', $request->input('end_date'))->endOfDay() : null;
        $reportType = $request->input('report_type', 'both'); // 'both', 'incomes', 'expenses'
        $includeDetails = $request->boolean('include_details', true); // Por defecto, incluir detalles

        // --- Calcular Ingresos Brutos ---
        $queryIncomes = Income::query();
        if ($startDate) {
            $queryIncomes->where('transaction_date', '>=', $startDate);
        }
        if ($endDate) {
            $queryIncomes->where('transaction_date', '<=', $endDate);
        }

        $totalIncomes = $queryIncomes->sum('amount');
        $monthlyIncomes = (clone $queryIncomes)->where('type', 'Mensualidad')->sum('amount');
        $installationIncomes = (clone $queryIncomes)->where('type', 'Instalacion')->sum('amount');

        // --- Calcular Gastos ---
        $queryExpenses = Expense::query();
        if ($startDate) {
            $queryExpenses->where('transaction_date', '>=', $startDate);
        }
        if ($endDate) {
            $queryExpenses->where('transaction_date', '<=', $endDate);
        }
        $totalExpenses = $queryExpenses->sum('amount');

        // --- Calcular Ingresos Netos y Déficits (Lógica actual) ---
        $installationNetIncome = $installationIncomes - $totalExpenses;
        $monthlyNetIncome = 0;
        $installationDeficit = 0;

        if ($installationNetIncome < 0) {
            $installationDeficit = abs($installationNetIncome);
            $monthlyNetIncome = $monthlyIncomes - $installationDeficit;
            $netIncome = $totalIncomes - $totalExpenses; // TotalIncomes - TotalExpenses
        } else {
            $monthlyNetIncome = $monthlyIncomes; // Mensualidades no afectadas si Instalaciones cubren gastos
            $netIncome = $totalIncomes - $totalExpenses; // TotalIncomes - TotalExpenses
        }

        // --- Preparar datos para detalles (si se solicitan) ---
        $incomeDetails = [];
        $expenseDetails = [];

        if ($includeDetails) {
            if ($reportType === 'incomes' || $reportType === 'both') {
                $incomeDetails = (clone $queryIncomes)->orderBy('transaction_date', 'asc')->get();
            }
            if ($reportType === 'expenses' || $reportType === 'both') {
                $expenseDetails = (clone $queryExpenses)->with('category')->orderBy('transaction_date', 'asc')->get();
            }
        }

        // --- Formatear fechas para el encabezado del reporte ---
        $periodo = '';
        if ($startDate && $endDate) {
            $periodo = $startDate->format('d-m-Y') . ' al ' . $endDate->format('d-m-Y');
        } elseif ($startDate) {
            $periodo = $startDate->format('d-m-Y') . ' al Fin';
        } elseif ($endDate) {
            $periodo = 'Inicio al ' . $endDate->format('d-m-Y');
        } else {
            $periodo = 'Todo el Período';
        }

        $data = [
            'periodo' => $periodo,
            'reportType' => $reportType,
            'includeDetails' => $includeDetails,

            // Ingresos Brutos
            'totalIncomes' => $totalIncomes,
            'monthlyIncomes' => $monthlyIncomes,
            'installationIncomes' => $installationIncomes,

            // Gastos
            'totalExpenses' => $totalExpenses,

            // Cálculos Netos
            'netIncome' => $netIncome, // Ingreso Neto Global (Bruto Total - Gasto Total)
            'installationDeficit' => $installationDeficit, // Déficit de instalaciones (si existe)
            'finalMonthlyIncome' => $monthlyIncomes - ($installationDeficit > 0 ? $installationDeficit : 0), // Mensualidades después de cubrir déficit

            // Detalles de transacciones
            'incomeDetails' => $incomeDetails,
            'expenseDetails' => $expenseDetails,
        ];

        $pdf = PDF::loadView('reports.finances_report', $data); // Asegúrate de que la vista exista
        return $pdf->download('reporte_financiero_' . Carbon::now()->format('Ymd_His') . '.pdf');
    }
}