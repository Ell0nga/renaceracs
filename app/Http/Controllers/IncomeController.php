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

        // Obtener fechas y filtros de la request para el dashboard
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
        $incomes = (clone $baseIncomesQuery)->latest()->paginate(10, ['*'], 'income_page');
        $expenses = (clone $baseExpensesQuery)->with('category')->latest()->paginate(10, ['*'], 'expense_page');

        // --- Lógica para calcular totales de ingresos ---
        $totalIncomesQuery = (clone $baseIncomesQuery);
        if ($filterType)
            $totalIncomesQuery->where('type', $filterType);
        $totalIncomes = $totalIncomesQuery->sum('amount');

        $monthlyIncomesQuery = (clone $baseIncomesQuery)->where('type', 'Mensualidad');
        $installationIncomesQuery = (clone $baseIncomesQuery)->where('type', 'Instalacion');

        $totalMonthlyIncomes = $monthlyIncomesQuery->sum('amount');
        $totalInstallationIncomes = $installationIncomesQuery->sum('amount');

        // Lógica para calcular total de gastos
        $totalExpensesQuery = (clone $baseExpensesQuery);
        if ($filterExpenseCategory)
            $totalExpensesQuery->where('expense_category_id', $filterExpenseCategory);
        $totalExpenses = $totalExpensesQuery->sum('amount');

        // Cálculo del ingreso neto global
        $netIncome = $totalIncomes - $totalExpenses;

        // ***** CÁLCULOS AJUSTADOS PARA LAS TARJETAS *****
        // Estas variables son específicamente para las tarjetas del dashboard
        $displayTotalIncomes = $totalIncomes;
        $displayMonthlyIncomes = $totalMonthlyIncomes;
        $displayInstallationIncomes = $totalInstallationIncomes;

        $installationCoverageBalance = $totalInstallationIncomes - $totalExpenses;
        $displayInstallationDeficit = $installationCoverageBalance < 0 ? abs($installationCoverageBalance) : 0;

        $displayNetMonthlyIncomes = 0;
        $displayNetInstallationIncomes = 0;

        if ($installationCoverageBalance >= 0) {
            $displayNetMonthlyIncomes = $totalMonthlyIncomes;
            $displayNetInstallationIncomes = $installationCoverageBalance;
        } else {
            $excessExpensesNotCoveredByInstallations = abs($installationCoverageBalance);
            $displayNetMonthlyIncomes = $totalMonthlyIncomes - $excessExpensesNotCoveredByInstallations;
            $displayNetInstallationIncomes = 0;
        }

        // Lógica para gráficos de ingresos diarios
        $dailyIncomes = (clone $baseIncomesQuery)
            ->select(DB::raw('DATE(transaction_date) as date'), DB::raw('SUM(amount) as total_amount'))
            ->when($filterType, fn($query) => $query->where('type', $filterType))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $incomeChartLabels = $dailyIncomes->pluck('date')->map(fn($date) => Carbon::parse($date)->format('d-m-Y'));
        $incomeChartData = $dailyIncomes->pluck('total_amount');

        // Lógica para gráficos de gastos diarios
        $dailyExpenses = (clone $baseExpensesQuery)
            ->select(DB::raw('DATE(transaction_date) as date'), DB::raw('SUM(amount) as total_amount'))
            ->when($filterExpenseCategory, fn($query) => $query->where('expense_category_id', $filterExpenseCategory))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $expenseChartLabels = $dailyExpenses->pluck('date')->map(fn($date) => Carbon::parse($date)->format('d-m-Y'));
        $expenseChartData = $dailyExpenses->pluck('total_amount');

        $startDateInput = $startDate ? $startDate->format('d-m-Y') : null;
        $endDateInput = $endDate ? $endDate->format('d-m-Y') : null;

        $recentIncomes = $user->incomes()->orderBy('transaction_date', 'desc')->take(3)->get()->map(function ($item) {
            $item->type_label = 'Ingreso';
            $item->category_name = null;
            $item->client_description = $item->client_number;
            return $item;
        });

        $recentExpenses = $user->expenses()->with('category')->orderBy('transaction_date', 'desc')->take(3)->get()->map(function ($item) {
            $item->type_label = 'Gasto';
            $item->category_name = $item->category ? $item->category->name : 'Sin Categoría';
            $item->client_description = $item->assigned_to ?? $item->description;
            return $item;
        });

        return view('finanzas.finanzas', compact(
            'incomes',
            'expenses',
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
            'recentIncomes',
            'recentExpenses'
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

        $incomes = $query->paginate(10);

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
        set_time_limit(60); // Aumenta el tiempo de ejecución a 60 segundos si es necesario

        $user = Auth::user();

        // 1. Obtener los parámetros del request (filtros) para el reporte
        // Asegúrate que los inputs 'start_date' y 'end_date' vengan en formato 'Y-m-d'
        $startDateParam = $request->input('start_date');
        $endDateParam = $request->input('end_date');
        $reportCategory = $request->input('report_category', 'both'); // 'all', 'incomes', 'expenses', 'both'
        $includeDetails = $request->boolean('include_details', true);
        $generateEnvelope = $request->boolean('generate_envelope', false); // Para el sobre, si se genera desde aquí

        // Convertir fechas de string a Carbon objects
        $startDate = $startDateParam ? Carbon::createFromFormat('Y-m-d', $startDateParam)->startOfDay() : null;
        $endDate = $endDateParam ? Carbon::createFromFormat('Y-m-d', $endDateParam)->endOfDay() : null;

        // 2. --- Consultas base para INCOMES y EXPENSES, con filtros de fecha ---
        // Se usarán para calcular los totales y obtener los detalles
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

        // 3. --- Lógica para calcular TOTALES del REPORTE (Alineada con tu dashboard) ---
        // Calcula TODOS los ingresos del período (mensualidades + instalaciones)
        $totalIncomes = (clone $baseIncomesQuery)->sum('amount');

        // Calcula ingresos por tipo
        $monthlyIncomes = (clone $baseIncomesQuery)->where('type', 'Mensualidad')->sum('amount');
        $installationIncomes = (clone $baseIncomesQuery)->where('type', 'Instalacion')->sum('amount');

        // Calcula gastos totales
        $totalExpenses = (clone $baseExpensesQuery)->sum('amount');

        // Cálculo del ingreso neto global
        $netIncome = $totalIncomes - $totalExpenses;

        // Cálculo del déficit de instalaciones (si aplica)
        $installationCoverageBalance = $installationIncomes - $totalExpenses;
        $installationDeficit = $installationCoverageBalance < 0 ? abs($installationCoverageBalance) : 0;

        // 4. Definir el Período y Tipo de Reporte para la vista
        $periodo = '';
        if ($startDate && $endDate) {
            $periodo = $startDate->format('d/m/Y') . ' al ' . $endDate->format('d/m/Y');
        } elseif ($startDate) {
            $periodo = 'Desde ' . $startDate->format('d/m/Y');
        } elseif ($endDate) {
            $periodo = 'Hasta ' . $endDate->format('d/m/Y');
        } else {
            // Si no hay fechas, podría ser "Mes Actual" o "Año Actual" por defecto, o "Todo el Período"
            // Aquí puedes ajustar la lógica para que coincida con lo que tu dashboard muestra por defecto
            $periodo = 'Todo el Período';
        }

        $reportTypeLabel = ''; // Variable que la vista 'finances_report.blade.php' espera como $reportType
        if ($reportCategory === 'incomes') {
            $reportTypeLabel = 'Solo Ingresos';
        } elseif ($reportCategory === 'expenses') {
            $reportTypeLabel = 'Solo Gastos';
        } elseif ($reportCategory === 'both') {
            $reportTypeLabel = 'Ingresos y Gastos';
        } else {
            $reportTypeLabel = 'General';
        }

        // 5. Preparar datos de DETALLE (solo si includeDetails es true y según la categoría del reporte)
        $incomeDetails = collect();
        $expenseDetails = collect();

        if ($includeDetails) {
            if ($reportCategory === 'incomes' || $reportCategory === 'both') {
                $incomeDetails = (clone $baseIncomesQuery)->get();
            }
            if ($reportCategory === 'expenses' || $reportCategory === 'both') {
                $expenseDetails = (clone $baseExpensesQuery)->with('category')->get(); // Incluye categoría para gastos
            }
        }

        // 6. Preparar datos específicos para el SOBRE (si generateEnvelope es true)
        // La lógica del sobre suele ser para un día específico o mensualidades de un período.
        // Aquí se usa el $monthlyIncomes calculado arriba para el monto a entregar.
        $deliveryDate = $startDate ? $startDate->format('d/m/Y') : Carbon::now()->format('d/m/Y');
        $amountToDeliver = $monthlyIncomes; // Monto total de mensualidades para el sobre

        // Detalles de mensualidades para el sobre (limitado, como lo tenías para caber en el sobre)
        $monthlyDetailsForEnvelope = $includeDetails ? (clone $user->incomes()->where('type', 'Mensualidad')->when($startDate, fn($q) => $q->whereDate('transaction_date', '>=', $startDate))->when($endDate, fn($q) => $q->whereDate('transaction_date', '<=', $endDate)))->orderBy('transaction_date', 'asc')->take(8)->get() : collect();


        // 7. Preparar el array de datos que se pasará a la VISTA DEL PDF
        $data = [
            // Variables para el REPORTE FINANCIERO GENERAL (finances_report.blade.php)
            'periodo' => $periodo,
            'reportType' => $reportTypeLabel, // Se pasa como $reportType a la vista
            'totalIncomes' => $totalIncomes,
            'monthlyIncomes' => $monthlyIncomes, // Total de ingresos por Mensualidad
            'installationIncomes' => $installationIncomes, // Total de ingresos por Instalacion
            'totalExpenses' => $totalExpenses,
            'netIncome' => $netIncome, // Ingreso neto global (total ingresos - total gastos)
            'installationDeficit' => $installationDeficit,
            'includeDetails' => $includeDetails,
            'incomeDetails' => $incomeDetails, // Detalles de ingresos si se solicitan
            'expenseDetails' => $expenseDetails, // Detalles de gastos si se solicitan

            // Variables específicas para el SOBRE (envelope_report.blade.php)
            // Se incluyen aquí porque si generateEnvelope=true, se usa este $data
            'deliveryDate' => $deliveryDate,
            'amountToDeliver' => $amountToDeliver,
            'monthlyDetails' => $monthlyDetailsForEnvelope,
            'totalMonthlyIncomes' => $monthlyIncomes, // Reutiliza el cálculo de monthlyIncomes para el total del sobre
        ];

        // 8. Generar el PDF basado en el tipo de solicitud
        if ($generateEnvelope) {
            $pdf = PDF::loadView('reports.envelope_report', $data)->setPaper('legal', 'portrait');
            return $pdf->download('sobre_mensualidades_' . Carbon::now()->format('Ymd_His') . '.pdf');
        } else {
            // Este es el caso cuando se genera el REPORTE FINANCIERO
            $pdf = PDF::loadView('reports.finances_report', $data)->setPaper('letter', 'portrait');
            return $pdf->download('reporte_financiero_' . Carbon::now()->format('Ymd_His') . '.pdf');
        }
    }
}