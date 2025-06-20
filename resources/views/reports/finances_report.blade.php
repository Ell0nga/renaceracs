<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Financiero</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #333;
            margin-bottom: 5px;
        }
        .summary-box {
            border: 1px solid #eee;
            padding: 15px 20px;
            border-radius: 8px;
            background-color: #f9f9f9;
            margin-bottom: 30px;
        }
        .summary-box h2 {
            color: #555;
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 1.2em;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .summary-item {
            margin-bottom: 8px;
            font-size: 1em;
        }
        .total-incomes { color: #28a745; font-weight: bold; } /* Verde para ingresos */
        .total-expenses { color: #dc3545; font-weight: bold; } /* Rojo para gastos */
        .net-income { color: #007bff; font-weight: bold; font-size: 1.1em; } /* Azul para neto global */
        .category-income { color: #218838; } /* Verde oscuro para sub-ingresos */
        .deficit { color: #dc3545; font-size: 0.9em; } /* Rojo para déficit */
        .info-text { font-size: 0.85em; color: #666; margin-top: 5px; }

        .details h2 {
            color: #555;
            margin-top: 20px;
            margin-bottom: 15px;
            font-size: 1.1em;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 0.9em;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .break-before {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte Financiero</h1>
        <p>Período: {{ $periodo }}</p>
    </div>

    <div class="summary-box">
        <h2>Resumen del Período</h2>

        <div class="summary-item total-incomes">
            Total Ingresos Brutos: ${{ number_format($totalIncomes, 0, ',', '.') }} CLP
        </div>
        <div class="summary-item category-income">
            - Mensualidades (Bruto): ${{ number_format($monthlyIncomes, 0, ',', '.') }} CLP
        </div>
        <div class="summary-item category-income">
            - Instalaciones (Bruto): ${{ number_format($installationIncomes, 0, ',', '.') }} CLP
        </div>

        <div class="summary-item total-expenses" style="margin-top: 15px;">
            Total Gastos: ${{ number_format($totalExpenses, 0, ',', '.') }} CLP
        </div>

        @if ($installationDeficit > 0)
            <div class="summary-item deficit">
                Déficit de Instalaciones: ${{ number_format($installationDeficit, 0, ',', '.') }} CLP
            </div>
            <p class="info-text">
                (Este déficit se cubre con ingresos por Mensualidades.)
            </p>
        @endif

        <div class="summary-item net-income" style="margin-top: 15px; border-top: 1px dashed #ccc; padding-top: 10px;">
            Ingreso Neto Global: ${{ number_format($netIncome, 0, ',', '.') }} CLP
        </div>
        <div class="summary-item info-text">
            (Total Ingresos Brutos - Total Gastos)
        </div>
        <div class="summary-item info-text">
            Mensualidades Netas (después de cubrir déficit): ${{ number_format($finalMonthlyIncome, 0, ',', '.') }} CLP
        </div>
        <div class="summary-item info-text">
    Instalaciones Netas: ${{ number_format(max(0, $installationIncomes - $totalExpenses), 0, ',', '.') }} CLP
    {{-- max(0, X) asegura que no muestre un valor negativo aquí, si hubo déficit, sería 0. --}}
</div>
    </div>

    @if ($includeDetails)
        @if ($reportType === 'incomes' || $reportType === 'both')
            <div class="details @if($reportType === 'both' && !empty($expenseDetails)) break-before @endif">
                <h2>Detalle de Ingresos</h2>
                @if ($incomeDetails->isEmpty())
                    <p>No hay ingresos para el período seleccionado.</p>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Tipo</th>
                                <th>Método de Pago</th>
                                <th class="text-right">Monto (CLP)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($incomeDetails as $income)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($income->transaction_date)->format('d-m-Y') }}</td>
                                    <td>{{ $income->client_description ?? 'N/A' }}</td>
                                    <td>{{ $income->type }}</td>
                                    <td>{{ $income->payment_method }}</td>
                                    <td class="text-right">${{ number_format($income->amount, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        @endif

        @if ($reportType === 'expenses' || $reportType === 'both')
            <div class="details @if($reportType === 'both' && !empty($incomeDetails)) break-before @endif">
                <h2>Detalle de Gastos</h2>
                @if ($expenseDetails->isEmpty())
                    <p>No hay gastos para el período seleccionado.</p>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Descripción</th>
                                <th>Categoría</th>
                                <th>Método de Pago</th>
                                <th class="text-right">Monto (CLP)</th>
                                <th>Asignado a</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($expenseDetails as $expense)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($expense->transaction_date)->format('d-m-Y') }}</td>
                                    <td>{{ $expense->description }}</td>
                                    <td>{{ $expense->category->name ?? 'N/A' }}</td>
                                    <td>{{ $expense->payment_method }}</td>
                                    <td class="text-right">${{ number_format($expense->amount, 0, ',', '.') }}</td>
                                    <td>{{ $expense->assigned_to ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        @endif
    @endif
</body>
</html>