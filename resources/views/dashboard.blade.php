<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            {{-- Título y botones para abrir modales --}}
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold text-gray-800">{{ __("¡Bienvenido a tu panel de ingresos y gastos!") }}</h3>
                <div class="flex space-x-2">
                    <a href="#" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-income-modal')"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Ingresar Ingreso') }}
                    </a>
                    <a href="#" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-expense-modal')"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Ingresar Gasto') }}
                    </a>
                </div>
            </div>

            <div class="mt-6 p-4 bg-gray-50 rounded-lg shadow-inner">
                <h4 class="font-semibold text-md mb-3 text-gray-700">Filtrar Datos</h4>
                <form method="GET" action="{{ route('dashboard') }}"
                    class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <x-input-label for="start_date" :value="__('Fecha Inicio')" />
                        <x-text-input id="start_date" class="block mt-1 w-full" type="text" name="start_date"
                            :value="$startDateInput" placeholder="dd-mm-yyyy" />
                    </div>
                    <div>
                        <x-input-label for="end_date" :value="__('Fecha Fin')" />
                        <x-text-input id="end_date" class="block mt-1 w-full" type="text" name="end_date"
                            :value="$endDateInput" placeholder="dd-mm-yyyy" />
                    </div>
                    <div>
                        <x-input-label for="type" :value="__('Tipo de Ingreso')" />
                        <select id="type" name="type"
                            class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Todos los tipos</option>
                            <option value="Mensualidad" {{ $filterType == 'Mensualidad' ? 'selected' : '' }}>Mensualidad</option>
                            <option value="Instalacion" {{ $filterType == 'Instalacion' ? 'selected' : '' }}>Instalación</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="expense_category_id" :value="__('Categoría de Gasto')" />
                        <select id="expense_category_id" name="expense_category_id"
                            class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Todas las categorías</option>
                            @foreach($expenseCategories as $category)
                                <option value="{{ $category->id }}" {{ $filterExpenseCategory == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-1 md:col-span-4 flex justify-end">
                        <x-primary-button class="ms-4">
                            {{ __('Aplicar Filtros') }}
                        </x-primary-button>
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 ml-2">
                            {{ __('Limpiar Filtros') }}
                        </a>
                    </div>
                </form>
            </div>
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-indigo-100 p-4 rounded-lg shadow">
                    <h3 class="text-md font-semibold text-indigo-800">Total Ingresos</h3>
                    <p class="text-2xl font-bold text-indigo-900"> ${{ number_format($totalIncomes, 0, ',', '.') }} CLP</p>
                    <p class="text-sm text-indigo-700">Mensualidades: ${{ number_format($totalMonthlyIncomes, 0, ',', '.') }} CLP</p>
                    <p class="text-sm text-indigo-700">Instalaciones: ${{ number_format($totalInstallationIncomes, 0, ',', '.') }} CLP</p>
                </div>
                <div class="bg-red-100 p-4 rounded-lg shadow">
                    <h3 class="text-md font-semibold text-red-800">Total Gastos</h3>
                    <p class="text-2xl font-bold text-red-900">${{ number_format($totalExpenses, 0, ',', '.') }} CLP</p>
                </div>
                <div class="bg-green-100 p-4 rounded-lg shadow">
                    <h3 class="text-md font-semibold text-green-800">Ingreso Neto</h3>
                    <p class="text-2xl font-bold text-green-900">${{ number_format($netIncome, 0, ',', '.') }} CLP</p>
                    <p class="text-sm text-green-700">(Ingresos - Gastos)</p>
                </div>
            </div>
            <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">Ingresos Diarios</h3>
                    <canvas id="incomeChart"></canvas>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">Gastos Diarios</h3>
                    <canvas id="expenseChart"></canvas>
                </div>
            </div>

            ---

            {{-- NUEVA SECCIÓN: Últimos Registros con Filtros --}}
            {{-- Aquí guardamos los datos de Blade en elementos script con IDs para que Alpine.js los lea de forma segura --}}
            <script id="all-transactions-data" type="application/json">@json($recentTransactions)</script>
            <script id="income-transactions-data" type="application/json">@json($recentIncomes)</script>
            <script id="expense-transactions-data" type="application/json">@json($recentExpenses)</script>

            <div class="mt-8" x-data="transactionData()" x-cloak>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Últimos Registros</h3>
                    <div class="flex space-x-2">
                        <button @click="activeTab = 'all'; filterTransactions()"
                            :class="{ 'bg-blue-600 text-white': activeTab === 'all', 'bg-gray-200 text-gray-800 hover:bg-gray-300': activeTab !== 'all' }"
                            class="px-4 py-2 rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Ver Todo') }}
                        </button>
                        <button @click="activeTab = 'incomes'; filterTransactions()"
                            :class="{ 'bg-green-600 text-white': activeTab === 'incomes', 'bg-gray-200 text-gray-800 hover:bg-gray-300': activeTab !== 'incomes' }"
                            class="px-4 py-2 rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Ver Ingresos') }}
                        </button>
                        <button @click="activeTab = 'expenses'; filterTransactions()"
                            :class="{ 'bg-red-600 text-white': activeTab === 'expenses', 'bg-gray-200 text-gray-800 hover:bg-gray-300': activeTab !== 'expenses' }"
                            class="px-4 py-2 rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Ver Gastos') }}
                        </button>
                    </div>
                </div>

                <a href="{{ route('incomes.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mb-4 ml-2">
                    Ver Todos los Ingresos
                </a>
                <a href="{{ route('expenses.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mb-4 ml-2">
                    Ver Todos los Gastos
                </a>

                <div x-show="dataToShow.length > 0" class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría/Método</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente/Descripción</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="item in dataToShow" :key="item.id + '-' + item.type_label">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                                        :class="{ 'text-green-600': item.type_label === 'Ingreso', 'text-red-600': item.type_label === 'Gasto' }">
                                        <span x-text="item.type_label"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        $<span x-text="new Intl.NumberFormat('es-CL', { maximumFractionDigits: 0 }).format(item.amount)"></span> CLP
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="new Date(item.transaction_date).toLocaleDateString('es-CL')"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span x-text="item.type_label === 'Ingreso' ? item.payment_method : item.category_name"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{-- Usamos 'description' para gastos y 'client_number' para ingresos --}}
                                        <span x-text="item.type_label === 'Ingreso' ? (item.client_number || 'N/A') : (item.description || 'N/A')"></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div x-show="dataToShow.length === 0" class="mt-4 text-gray-600">
                    <p>No hay registros para mostrar en esta vista.</p>
                </div>
            </div>
            {{-- FIN DE NUEVA SECCIÓN --}}

        </div>
    </div>

    {{-- MODALES PARA INGRESOS Y GASTOS --}}

    <x-modal name="add-income-modal" :show="$errors->incomeCreation->isNotEmpty()" focusable>
        <form method="post" action="{{ route('incomes.store') }}" class="p-6">
            @csrf

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Registrar Nuevo Ingreso') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Completa los campos para añadir un nuevo registro de ingreso.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="income_client_number" :value="__('Número de Cliente (Opcional)')" />
                <x-text-input id="income_client_number" name="client_number" type="text" class="mt-1 block w-full" :value="old('client_number')" autofocus />
                <x-input-error :messages="$errors->incomeCreation->get('client_number')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="income_amount" :value="__('Monto')" />
                <x-text-input id="income_amount" name="amount" type="number" class="mt-1 block w-full" :value="old('amount')" required />
                <x-input-error :messages="$errors->incomeCreation->get('amount')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="income_transaction_date" :value="__('Fecha de Transacción')" />
                <x-text-input id="income_transaction_date" name="transaction_date" type="text" class="mt-1 block w-full flatpickr-input" :value="old('transaction_date', $currentDate ?? \Carbon\Carbon::now()->format('Y-m-d'))" required />
                <x-input-error :messages="$errors->incomeCreation->get('transaction_date')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="income_type" :value="__('Tipo de Ingreso')" />
                <select id="income_type" name="type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                    <option value="">Selecciona un tipo</option>
                    @foreach($incomeTypes as $typeOption)
                        <option value="{{ $typeOption }}" {{ old('type') == $typeOption ? 'selected' : '' }}>{{ $typeOption }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->incomeCreation->get('type')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="income_payment_method" :value="__('Método de Pago')" />
                <select id="income_payment_method" name="payment_method" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                    <option value="">Selecciona un método</option>
                    @foreach($paymentMethods as $methodOption)
                        <option value="{{ $methodOption }}" {{ old('payment_method') == $methodOption ? 'selected' : '' }}>{{ $methodOption }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->incomeCreation->get('payment_method')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="income_comment" :value="__('Comentario (Opcional)')" />
                <textarea id="income_comment" name="comment" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('comment') }}</textarea>
                <x-input-error :messages="$errors->incomeCreation->get('comment')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-primary-button class="ms-3">
                    {{ __('Guardar Ingreso') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <x-modal name="add-expense-modal" :show="$errors->expenseCreation->isNotEmpty()" focusable>
        <form method="post" action="{{ route('expenses.store') }}" class="p-6">
            @csrf

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Registrar Nuevo Gasto') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Completa los campos para añadir un nuevo registro de gasto.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="expense_description" :value="__('Descripción')" />
                <x-text-input id="expense_description" name="description" type="text" class="mt-1 block w-full" :value="old('description')" required />
                <x-input-error :messages="$errors->expenseCreation->get('description')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="expense_amount" :value="__('Monto')" />
                <x-text-input id="expense_amount" name="amount" type="number" class="mt-1 block w-full" :value="old('amount')" required />
                <x-input-error :messages="$errors->expenseCreation->get('amount')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="expense_transaction_date" :value="__('Fecha de Transacción')" />
                <x-text-input id="expense_transaction_date" name="transaction_date" type="text" class="mt-1 block w-full flatpickr-input" :value="old('transaction_date', $currentDate ?? \Carbon\Carbon::now()->format('Y-m-d'))" required />
                <x-input-error :messages="$errors->expenseCreation->get('transaction_date')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="expense_category_id_modal" :value="__('Categoría')" />
                <select id="expense_category_id_modal" name="expense_category_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                    <option value="">Selecciona una categoría</option>
                    @foreach($expenseCategories as $category)
                        <option value="{{ $category->id }}" {{ old('expense_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->expenseCreation->get('expense_category_id')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="expense_payment_method" :value="__('Método de Pago')" />
                <select id="expense_payment_method" name="payment_method" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                    <option value="">Selecciona un método</option>
                    @foreach($expensePaymentMethods as $methodOption)
                        <option value="{{ $methodOption }}" {{ old('payment_method') == $methodOption ? 'selected' : '' }}>{{ $methodOption }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->expenseCreation->get('payment_method')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="expense_assigned_to" :value="__('Asignado a (Opcional)')" />
                <x-text-input id="expense_assigned_to" name="assigned_to" type="text" class="mt-1 block w-full" :value="old('assigned_to')" />
                <x-input-error :messages="$errors->expenseCreation->get('assigned_to')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="expense_comment" :value="__('Comentario (Opcional)')" />
                <textarea id="expense_comment" name="comment" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('comment') }}</textarea>
                <x-input-error :messages="$errors->expenseCreation->get('comment')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-primary-button class="ms-3">
                    {{ __('Guardar Gasto') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    {{-- FIN DE MODALES --}}

    @push('scripts')
        {{-- Incluir Flatpickr CSS y JS --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        {{-- Incluir el paquete de idiomas de Flatpickr (para español) --}}
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>


        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Datos para el gráfico de Ingresos
            const incomeLabels = @json($incomeChartLabels);
            const incomeData = @json($incomeChartData);
            const incomeCtx = document.getElementById('incomeChart').getContext('2d');
            new Chart(incomeCtx, {
                type: 'line', // Puedes cambiar a 'bar' si prefieres barras
                data: {
                    labels: incomeLabels,
                    datasets: [{
                        label: 'Ingresos Diarios (CLP)',
                        data: incomeData,
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Monto (CLP)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Fecha'
                            }
                        }
                    }
                }
            });

            // Datos para el gráfico de Gastos
            const expenseLabels = @json($expenseChartLabels);
            const expenseData = @json($expenseChartData);
            const expenseCtx = document.getElementById('expenseChart').getContext('2d');
            new Chart(expenseCtx, {
                type: 'bar', // Gráfico de barras para gastos
                data: {
                    labels: expenseLabels,
                    datasets: [{
                        label: 'Gastos Diarios (CLP)',
                        data: expenseData,
                        backgroundColor: 'rgb(255, 99, 132)',
                        borderColor: 'rgb(255, 99, 132)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Monto (CLP)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Fecha'
                            }
                        }
                    }
                }
            });
        </script>

        <script>
            function transactionData() {
                return {
                    activeTab: 'all',
                    allTransactions: [],
                    incomeTransactions: [],
                    expenseTransactions: [],
                    dataToShow: [],

                    init() {
                        try {
                            // Obtener el contenido del script y parsearlo
                            let rawAllTransactions = JSON.parse(document.getElementById('all-transactions-data').textContent);

                            // Si rawAllTransactions no es un array, convertimos sus valores a un array.
                            // Esto maneja el caso de {0: {...}, 1: {...}} y lo convierte en [{...}, {...}].
                            if (!Array.isArray(rawAllTransactions)) {
                                console.warn('rawAllTransactions no es un array. Convirtiendo a array.');
                                rawAllTransactions = Object.values(rawAllTransactions);
                            }

                            // A partir de aquí, rawAllTransactions SIEMPRE será un array.
                            this.allTransactions = rawAllTransactions;

                            // Filtrar ingresos y gastos a partir de todas las transacciones
                            this.incomeTransactions = rawAllTransactions.filter(item => item.type_label === 'Ingreso');
                            this.expenseTransactions = rawAllTransactions.filter(item => item.type_label === 'Gasto'); 

                        } catch (e) {
                            console.error('Error al parsear JSON de transacciones o al filtrar:', e);
                            // En caso de error, inicializamos con arrays vacíos para evitar fallos.
                            this.allTransactions = [];
                            this.incomeTransactions = [];
                            this.expenseTransactions = [];
                        }

                        // Inicializa dataToShow con todas las transacciones al cargar la página
                        this.filterTransactions();

                        // Usa $watch para reaccionar cuando activeTab cambie y filtrar
                        this.$watch('activeTab', () => this.filterTransactions());
                    },

                    filterTransactions() {
                        if (this.activeTab === 'all') {
                            this.dataToShow = this.allTransactions;
                        } else if (this.activeTab === 'incomes') {
                            this.dataToShow = this.incomeTransactions;
                        } else { // activeTab === 'expenses'
                            this.dataToShow = this.expenseTransactions;
                        }
                    }
                }
            }
        </script>

        <script>
            // Inicializar Flatpickr para los campos de fecha en el formulario de filtros del dashboard
            flatpickr("#start_date", {
                dateFormat: "d-m-Y", // Formato de fecha para mostrar al usuario (día-mes-año)
                locale: "es", // Establecer el idioma a español
                allowInput: true, // Permite escribir la fecha manualmente también
            });

            flatpickr("#end_date", {
                dateFormat: "d-m-Y",
                locale: "es",
                allowInput: true,
            });

            // Inicializar Flatpickr para los campos de fecha en los modales de Ingresos y Gastos
            flatpickr("#income_transaction_date", {
                dateFormat: "Y-m-d", // Formato para el backend (año-mes-día)
                locale: "es",
                allowInput: true,
            });

            flatpickr("#expense_transaction_date", {
                dateFormat: "Y-m-d",
                locale: "es",
                allowInput: true,
            });
        </script>
    @endpush
</x-app-layout>