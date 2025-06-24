<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        h1 { text-align: center; }
    </style>
</head>
<body>
    <h1>Reporte Financiero</h1>
    <p><strong>Período:</strong> {{ $periodo }}</p>
    <p><strong>Tipo de Reporte:</strong> {{ $reportType }}</p>

    <!-- Resumen Financiero -->
    <h2>Resumen</h2>
    <p><strong>Ingresos Totales:</strong> ${{ number_format($totalIncomes, 0, ',', '.') }} CLP</p>
    <p><strong>Mensualidades:</strong> ${{ number_format($monthlyIncomes, 0, ',', '.') }} CLP</p>
    <p><strong>Instalaciones:</strong> ${{ number_format($installationIncomes, 0, ',', '.') }} CLP</p>
    <p><strong>Gastos Totales:</strong> ${{ number_format($totalExpenses, 0, ',', '.') }} CLP</p>
    <p><strong>Ingreso Neto:</strong> ${{ number_format($netIncome, 0, ',', '.') }} CLP</p>
    @if($installationDeficit > 0)
        <p><strong>Déficit de Instalaciones:</strong> ${{ number_format($installationDeficit, 0, ',', '.') }} CLP</p>
    @endif

    <!-- Detalles de Ingresos -->
    @if($includeDetails && ($reportType === 'incomes' || $reportType === 'both'))
        <h2>Detalles de Ingresos</h2>
        <table>
            <thead>
                <tr>
                    <th>Número de Cliente</th>
                    <th>Monto</th>
                    <th>Tipo</th>
                    <th>Método de Pago</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach($incomeDetails as $income)
                    <tr>
                        <td>{{ $income->client_number ?? 'N/A' }}</td>
                        <td>{{ number_format($income->amount, 0, ',', '.') }} CLP</td>
                        <td>{{ $income->type }}</td>
                        <td>{{ $income->payment_method }}</td>
                        <td>{{ $income->transaction_date->format('d-m-Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Detalles de Gastos (si aplica) -->
    @if($includeDetails && ($reportType === 'expenses' || $reportType === 'both'))
        <h2>Detalles de Gastos</h2>
        <table>
            <thead>
                <tr>

                    <th>Categoría</th>
                    <th>Monto</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expenseDetails as $expense)
                    <tr>
                        <td>{{ $expense->category->name ?? 'Sin Categoría' }}</td>
                        <td>{{ number_format($expense->amount, 0, ',', '.') }} CLP</td>
                        <td>{{ $expense->description }}</td>
                        <td>{{ $expense->transaction_date->format('d-m-Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>