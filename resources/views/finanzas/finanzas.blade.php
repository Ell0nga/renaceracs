<x-app-layout>
    <div class="py-1">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Título y botones para abrir modales --}}
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold text-gray-800">
                            {{ __("¡Bienvenido a tu panel de ingresos y gastos!") }}
                        </h3>
                        <div class="flex space-x-2">
                            <a href="#" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-income-modal')"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Ingresar Ingreso') }}
                            </a>
                            <a href="#" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-expense-modal')"
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Ingresar Gasto') }}
                            </a>
                            {{-- NUEVO BOTÓN PARA ABRIR MODAL DE REPORTES --}}
                            <a href="#" x-data="" x-on:click.prevent="$dispatch('open-modal', 'select-report-period-modal')"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Generar Reporte') }}
                            </a>
                        </div>
                    </div>

                    {{-- Controles de Interacción (Filtros, Reportes, etc.) --}}
                    <div x-data="{ showFilters: false }" class="mt-6">
                        <div class="flex space-x-2 mb-4">
                            <x-primary-button x-on:click="showFilters = !showFilters"
                                class="inline-flex items-center px-4 py-2">
                                <span x-text="showFilters ? 'Ocultar Filtros' : 'Mostrar Filtros'"></span>
                            </x-primary-button>
                        </div>

                        {{-- Panel de Filtros Ocultable --}}
                        <div x-show="showFilters" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform -translate-y-3" x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0"
                            x-transition:leave-end="opacity-0 transform -translate-y-3"
                            class="p-4 bg-gray-50 rounded-lg shadow-inner" style="display: none;">
                            <h4 class="font-semibold text-md mb-3 text-gray-700">Filtrar Datos</h4>
                            <form method="GET" action="{{ route('finanzas.dashboard') }}"
                                class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                                <div>
                                    <x-input-label for="start_date" :value="__('Fecha Inicio')" />
                                    <x-text-input id="start_date" class="block mt-1 w-full flatpickr-filter" type="text"
                                        name="start_date" :value="$startDateInput" placeholder="dd-mm-yyyy" />
                                </div>
                                <div>
                                    <x-input-label for="end_date" :value="__('Fecha Fin')" />
                                    <x-text-input id="end_date" class="block mt-1 w-full flatpickr-filter" type="text"
                                        name="end_date" :value="$endDateInput" placeholder="dd-mm-yyyy" />
                                </div>
                                <div>
                                    <x-input-label for="type" :value="__('Tipo de Ingreso')" />
                                    <select id="type" name="type"
                                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Todos los tipos</option>
                                        <option value="Mensualidad" @if($filterType == 'Mensualidad') selected @endif>
                                            Mensualidad</option>
                                        <option value="Instalacion" @if($filterType == 'Instalacion') selected @endif>
                                            Instalación</option>
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="expense_category_id" :value="__('Categoría de Gasto')" />
                                    <select id="expense_category_id" name="expense_category_id"
                                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Todas las categorías</option>
                                        @foreach($expenseCategories as $category)
                                            <option value="{{ $category->id }}" @if($filterExpenseCategory == $category->id) selected @endif>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-1 md:col-span-4 flex justify-end">
                                    <x-primary-button class="ms-4">
                                        {{ __('Aplicar Filtros') }}
                                    </x-primary-button>
                                    <a href="{{ route('finanzas.dashboard') }}"
                                        class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 ml-2">
                                        {{ __('Limpiar Filtros') }}
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- INICIO DE LAS TARJETAS DE RESUMEN --}}
                    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Tarjeta de Total Ingresos --}}
                        <div class="bg-indigo-100 p-4 rounded-lg shadow">
                            <h3 class="text-md font-semibold text-indigo-800">Total Ingresos</h3>
                            <p class="text-2xl font-bold text-indigo-900">
                                ${{ number_format($displayTotalIncomes, 0, ',', '.') }} CLP</p>
                            <p class="text-sm text-indigo-700">Mensualidades:
                                ${{ number_format($displayMonthlyIncomes, 0, ',', '.') }} CLP</p>
                            <p class="text-sm text-indigo-700">Instalaciones:
                                ${{ number_format($displayInstallationIncomes, 0, ',', '.') }} CLP</p>
                        </div>

                        {{-- Tarjeta de Total Gastos (CON DÉFICIT CONDICIONAL) --}}
                        <div class="bg-red-100 p-4 rounded-lg shadow">
                            <h3 class="text-md font-semibold text-red-800">Total Gastos</h3>
                            <p class="text-2xl font-bold text-red-900">${{ number_format($totalExpenses, 0, ',', '.') }}
                                CLP</p>

                            @if ($displayInstallationDeficit > 0)
                                <p class="text-sm text-red-700 mt-2">
                                    Déficit de Instalaciones: ${{ number_format($displayInstallationDeficit, 0, ',', '.') }} CLP
                                </p>
                                <p class="text-xs text-red-600 mt-1">
                                    (Gastos exceden ingresos de Instalaciones, cubierto con Mensualidades)
                                </p>
                            @else
                                <p class="text-sm text-green-700 mt-2">
                                    (Gastos cubiertos por Instalaciones)
                                </p>
                            @endif
                        </div>

                        {{-- Tarjeta de Ingreso Neto Global (CON DESGLOSE DETALLADO) --}}
                        <div class="bg-green-100 p-4 rounded-lg shadow">
                            <h3 class="text-md font-semibold text-green-800">Ingreso Neto Global</h3>
                            <p class="text-2xl font-bold text-green-900">${{ number_format($netIncome, 0, ',', '.') }}
                                CLP</p>
                            <p class="text-sm text-green-700">Mensualidades:
                                ${{ number_format($displayNetMonthlyIncomes, 0, ',', '.') }} CLP</p>
                            <p class="text-sm text-green-700">Instalaciones:
                                ${{ number_format($displayNetInstallationIncomes, 0, ',', '.') }} CLP</p>
                        </div>
                    </div>
                    {{-- FIN DE LAS TARJETAS DE RESUMEN --}}

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

                    {{-- SECCIÓN: Últimos Registros --}}
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold mb-4">Últimos Registros</h3>

                        <div class="mb-6">
                            <h4 class="text-md font-semibold text-green-700 mb-2">Últimos 3 Ingresos</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[10%]">
                                                Número de Cliente</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[15%]">
                                                Monto</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[18%]">
                                                Fecha</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Método de Pago</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[15%]">
                                                    Tipo</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($recentIncomes as $item)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $item->client_description ?? 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 w-[15%]">
                                                    ${{ number_format($item->amount, 0, ',', '.') }} CLP
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 w-[18%]">
                                                    {{ \Carbon\Carbon::parse($item->transaction_date)->format('d-m-Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $item->payment_method }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600 w-[15%]">
                                                    {{ $item->type_label }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5"
                                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No
                                                    hay ingresos recientes para mostrar.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4 text-right">
                                <a href="{{ route('finanzas.incomes.index') }}"
                                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    {{ __('Ver Todos los Ingresos') }}
                                </a>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h4 class="text-md font-semibold text-red-700 mb-2">Últimos 3 Gastos</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[15%]">
                                                Tipo</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[15%]">
                                                Monto</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[18%]">
                                                Fecha</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Categoría</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Descripción/Asignado</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($recentExpenses as $item)
                                            <tr>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600 w-[15%]">
                                                    {{ $item->type_label }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 w-[15%]">
                                                    ${{ number_format($item->amount, 0, ',', '.') }} CLP
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 w-[18%]">
                                                    {{ \Carbon\Carbon::parse($item->transaction_date)->format('d-m-Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $item->category_name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $item->client_description ?? 'N/A' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5"
                                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No
                                                    hay gastos recientes para mostrar.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4 text-right">
                                <a href="{{ route('finanzas.expenses.index') }}"
                                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    Ver Todos los Gastos
                                </a>
                            </div>
                        </div>
                    </div>
                    {{-- FIN DE SECCIÓN ÚLTIMOS REGISTROS --}}
                </div>
            </div>
        </div>
    </div>

    {{-- MODALES PARA INGRESOS Y GASTOS --}}

    <x-modal name="add-income-modal" :show="$errors->incomeCreation->isNotEmpty()" focusable>
        <form method="post" action="{{ route('finanzas.incomes.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Registrar Nuevo Ingreso') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                {{ __('Completa los campos para añadir un nuevo registro de ingreso.') }}
            </p>
            <div class="mt-6">
                <x-input-label for="income_client_number" :value="__('Número de Cliente')" />
                <x-text-input id="income_client_number" name="client_number" type="text" class="mt-1 block w-full"
                    :value="old('client_number')" required />
                <x-input-error :messages="$errors->incomeCreation->get('client_number')" class="mt-2" />
            </div>
            <div class="mt-4">
                <x-input-label for="income_amount" :value="__('Monto')" />
                <x-text-input id="income_amount" name="amount" type="number" class="mt-1 block w-full"
                    :value="old('amount')" required />
                <x-input-error :messages="$errors->incomeCreation->get('amount')" class="mt-2" />
            </div>
            <div class="mt-4">
                <x-input-label for="income_transaction_date" :value="__('Fecha de Transacción')" />
                <x-text-input id="income_transaction_date" name="transaction_date" type="text"
                    class="mt-1 block w-full flatpickr-modal" :value="old('transaction_date', $currentDate ?? \Carbon\Carbon::now()->format('Y-m-d'))" required />
                <x-input-error :messages="$errors->incomeCreation->get('transaction_date')" class="mt-2" />
            </div>
            <div class="mt-4">
                <x-input-label for="income_type" :value="__('Tipo de Ingreso')" />
                <select id="income_type" name="type"
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    required>
                    <option value="">Selecciona un tipo</option>
                    @foreach($incomeTypes as $typeOption)
                        <option value="{{ $typeOption }}" @if(old('type', 'Mensualidad') == $typeOption) selected @endif>
                            {{ $typeOption }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->incomeCreation->get('type')" class="mt-2" />
            </div>
            <div class="mt-4">
                <x-input-label for="income_payment_method" :value="__('Método de Pago')" />
                <select id="income_payment_method" name="payment_method"
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    required>
                    <option value="">Selecciona un método</option>
                    @foreach($paymentMethods as $methodOption)
                        <option value="{{ $methodOption }}" @if(old('payment_method', 'Efectivo') == $methodOption) selected @endif>{{ $methodOption }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->incomeCreation->get('payment_method')" class="mt-2" />
            </div>
            <div class="mt-4">
                <x-input-label for="income_comment" :value="__('Comentario (Opcional)')" />
                <textarea id="income_comment" name="comment" rows="3"
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('comment') }}</textarea>
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
        <form method="post" action="{{ route('finanzas.expenses.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Registrar Nuevo Gasto') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                {{ __('Completa los campos para añadir un nuevo registro de gasto.') }}
            </p>
            <div class="mt-6">
                <x-input-label for="expense_description_input" :value="__('Descripción del Gasto')" />
                <x-text-input id="expense_description_input" name="description" type="text" class="mt-1 block w-full"
                    :value="old('description')" required />
                <x-input-error :messages="$errors->expenseCreation->get('description')" class="mt-2" />
            </div>
            <div class="mt-4">
                <x-input-label for="expense_amount" :value="__('Monto')" />
                <x-text-input id="expense_amount" name="amount" type="number" class="mt-1 block w-full"
                    :value="old('amount')" required />
                <x-input-error :messages="$errors->expenseCreation->get('amount')" class="mt-2" />
            </div>
            <div class="mt-4">
                <x-input-label for="expense_transaction_date" :value="__('Fecha de Transacción')" />
                <x-text-input id="expense_transaction_date" name="transaction_date" type="text"
                    class="mt-1 block w-full flatpickr-modal" :value="old('transaction_date', $currentDate ?? \Carbon\Carbon::now()->format('Y-m-d'))" required />
                <x-input-error :messages="$errors->expenseCreation->get('transaction_date')" class="mt-2" />
            </div>
            <div class="mt-4">
                <x-input-label for="expense_category_id_modal" :value="__('Categoría')" />
                <select id="expense_category_id_modal" name="expense_category_id"
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    required>
                    <option value="">Selecciona una categoría</option>
                    @foreach($expenseCategories as $category)
                        <option value="{{ $category->id }}" @if(old('expense_category_id') == $category->id) selected @endif>{{ $category->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->expenseCreation->get('expense_category_id')" class="mt-2" />
                <button type="button" x-on:click="$dispatch('open-modal', 'add-category-modal')"
                    class="mt-2 text-blue-600 hover:text-blue-800 text-sm">
                    {{ __('Agregar Nueva Categoría') }}
                </button>
            </div>
            <div class="mt-4">
                <x-input-label for="expense_payment_method" :value="__('Método de Pago')" />
                <select id="expense_payment_method" name="payment_method"
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    required>
                    <option value="">Selecciona un método</option>
                    @foreach($expensePaymentMethods as $methodOption)
                        <option value="{{ $methodOption }}" @if(old('payment_method') == $methodOption) selected @endif>
                            {{ $methodOption }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->expenseCreation->get('payment_method')" class="mt-2" />
            </div>
            <div class="mt-4">
                <x-input-label for="expense_assigned_to" :value="__('Asignado a (Opcional)')" />
                <x-text-input id="expense_assigned_to" name="assigned_to" type="text" class="mt-1 block w-full"
                    :value="old('assigned_to')" />
                <x-input-error :messages="$errors->expenseCreation->get('assigned_to')" class="mt-2" />
            </div>
            <div class="mt-4">
                <x-input-label for="expense_comment" :value="__('Comentario (Opcional)')" />
                <textarea id="expense_comment" name="comment" rows="3"
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('comment') }}</textarea>
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

    {{-- Mini-modal para agregar categoría --}}
    <x-modal name="add-category-modal" focusable>
        <form id="add-category-form" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Agregar Nueva Categoría') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                {{ __('Ingresa el nombre de la nueva categoría de gasto.') }}
            </p>
            <div class="mt-6">
                <x-input-label for="category_name" :value="__('Nombre de la Categoría')" />
                <x-text-input id="category_name" name="name" type="text" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancelar') }}
                </x-secondary-button>
                <x-primary-button class="ms-3" type="submit">
                    {{ __('Guardar Categoría') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    {{-- NUEVO MODAL: Seleccionar Período del Reporte --}}
    <x-modal name="select-report-period-modal" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Seleccionar Período del Reporte') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                {{ __('Elige el rango de fechas para tu reporte financiero.') }}
            </p>

            <div class="mt-6 space-y-4" x-data="{ selectedPeriod: 'custom' }">
                <div>
                    <label class="inline-flex items-center">
                        <input type="radio" x-model="selectedPeriod" name="report_period_type" value="today"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ms-2 text-sm text-gray-600">{{ __('Hoy') }}</span>
                    </label>
                </div>
                <div>
                    <label class="inline-flex items-center">
                        <input type="radio" x-model="selectedPeriod" name="report_period_type" value="week"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ms-2 text-sm text-gray-600">{{ __('Últimos 7 días') }}</span>
                    </label>
                </div>
                <div>
                    <label class="inline-flex items-center">
                        <input type="radio" x-model="selectedPeriod" name="report_period_type" value="month"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ms-2 text-sm text-gray-600">{{ __('Últimos 30 días') }}</span>
                    </label>
                </div>
                <div>
                    <label class="inline-flex items-center">
                        <input type="radio" x-model="selectedPeriod" name="report_period_type" value="custom"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ms-2 text-sm text-gray-600">{{ __('Fechas Personalizadas') }}</span>
                    </label>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Cancelar') }}
                    </x-secondary-button>
                    <x-primary-button class="ms-3" x-on:click="handleReportPeriodSelection(selectedPeriod)">
                        {{ __('Continuar') }}
                    </x-primary-button>
                </div>
            </div>
        </div>
    </x-modal>

    {{-- MODAL PARA GENERAR REPORTES --}}
    <x-modal name="generate-report-modal" focusable>
        <form method="GET" action="{{ route('finanzas.reportes.generar') }}" class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Generar Reporte Financiero') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                {{ __('Selecciona el rango de fechas y el tipo de reporte.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="report_start_date" :value="__('Fecha Inicio')" />
                <x-text-input id="report_start_date" class="block mt-1 w-full flatpickr-modal-report" type="text"
                    name="start_date" placeholder="dd-mm-yyyy" />
            </div>
            <div class="mt-4">
                <x-input-label for="report_end_date" :value="__('Fecha Fin')" />
                <x-text-input id="report_end_date" class="block mt-1 w-full flatpickr-modal-report" type="text"
                    name="end_date" placeholder="dd-mm-yyyy" />
            </div>
            <div class="mt-4">
                <x-input-label for="report_type" :value="__('Tipo de Reporte')" />
                <select id="report_type" name="report_type"
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    required>
                    <option value="both">{{ __('Ingresos y Gastos') }}</option>
                    <option value="incomes">{{ __('Solo Ingresos') }}</option>
                    <option value="expenses">{{ __('Solo Gastos') }}</option>
                </select>
            </div>
            <div class="mt-4">
                <label for="include_details" class="flex items-center">
                    <input type="checkbox" id="include_details" name="include_details" value="1" checked
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Incluir detalles de transacciones') }}</span>
                </label>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancelar') }}
                </x-secondary-button>
                <x-primary-button class="ms-3">
                    {{ __('Generar PDF') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
    {{-- FIN DE MODALES --}}

    @push('scripts')
        {{-- Incluir Flatpickr CSS y JS --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css?v={{ now()->timestamp }}">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr?v={{ now()->timestamp }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js?v={{ now()->timestamp }}"></script>

        <script src="https://cdn.jsdelivr.net/npm/chart.js?v={{ now()->timestamp }}"></script>
        <script>
            // Datos para el gráfico de Ingresos
            const incomeLabels = @json($incomeChartLabels);
            const incomeData = @json($incomeChartData);
            const incomeCtx = document.getElementById('incomeChart').getContext('2d');
            new Chart(incomeCtx, {
                type: 'line',
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
                            ticks: {
                                callback: function (value, index, values) {
                                    return '$' + new Intl.NumberFormat('es-CL').format(value);
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Fecha'
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return context.dataset.label + ': $' + new Intl.NumberFormat('es-CL').format(context.raw);
                                }
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
                type: 'bar',
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
                            ticks: {
                                callback: function (value, index, values) {
                                    return '$' + new Intl.NumberFormat('es-CL').format(value);
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Fecha'
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return context.dataset.label + ': $' + new Intl.NumberFormat('es-CL').format(context.raw);
                                }
                            }
                        }
                    }
                }
            });

            // Configuración de Flatpickr para filtros
            flatpickr(".flatpickr-filter", {
                dateFormat: "d-m-Y",
                locale: "es",
                allowInput: true,
                onReady: function (selectedDates, dateStr, instance) {
                    if (!dateStr) {
                        instance.clear();
                    }
                }
            });

            // Configuración de Flatpickr para modales (fechas requeridas)
            flatpickr(".flatpickr-modal", {
                dateFormat: "Y-m-d", // Formato para el backend (ej. 2023-01-20)
                locale: "es",
                altInput: true, // Habilitar input alternativo para formato legible
                altFormat: "d-m-Y", // Formato legible para el usuario
                required: true,
                defaultDate: "{{ $currentDate }}", // Usar la fecha actual predeterminada
            });

            // Nueva inicialización para el modal de reportes
            flatpickr(".flatpickr-modal-report", {
                dateFormat: "d-m-Y",
                locale: "es", // Establece el idioma a español
                allowInput: true, // Permite la entrada manual
            });

            // Lógica para agregar nueva categoría de gasto vía AJAX
            document.getElementById('add-category-form').addEventListener('submit', function (event) {
                event.preventDefault();
                const form = event.target;
                const categoryNameInput = document.getElementById('category_name');
                const categoryName = categoryNameInput.value;

                if (!categoryName) {
                    alert('El nombre de la categoría no puede estar vacío.');
                    return;
                }

                fetch('/finanzas/expense-categories', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        name: categoryName
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw new Error(errorData.message || 'Error al guardar la categoría.');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const selectElement = document.getElementById('expense_category_id');
                        const selectModalElement = document.getElementById('expense_category_id_modal');

                        const newOption = new Option(data.category.name, data.category.id, true, true);
                        selectElement.add(newOption);
                        selectModalElement.add(new Option(data.category.name, data.category.id, true, true));

                        // Seleccionar la nueva opción
                        selectElement.value = data.category.id;
                        selectModalElement.value = data.category.id;

                        alert('Categoría agregada exitosamente.');
                        categoryNameInput.value = ''; // Limpiar el input
                        window.dispatchEvent(new CustomEvent('close-modal', { detail: 'add-category-modal' })); // Cierra el mini-modal
                    } else {
                        alert('Error al agregar categoría: ' + (data.message || 'Error desconocido.'));
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('Error al agregar categoría: ' + error.message);
                });
            });

            // Nueva función para manejar la selección del período del reporte
            function handleReportPeriodSelection(selectedPeriod) {
                let startDate = null;
                let endDate = null;
                const today = new Date();
                today.setHours(0, 0, 0, 0); // Para que sea el inicio del día

                if (selectedPeriod === 'today') {
                    startDate = today;
                    endDate = today;
                } else if (selectedPeriod === 'week') {
                    startDate = new Date(today);
                    startDate.setDate(today.getDate() - 6); // Hoy - 6 días = 7 días
                    endDate = today;
                } else if (selectedPeriod === 'month') {
                    startDate = new Date(today);
                    startDate.setDate(today.getDate() - 29); // Hoy - 29 días = 30 días
                    endDate = today;
                }

                // Formatear fechas a YYYY-MM-DD para pasarlas al siguiente modal
                const formatToYMD = (date) => {
                    if (!date) return '';
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                };

                // Disparar el evento con las fechas calculadas
                window.dispatchEvent(new CustomEvent('open-report-modal-with-dates', {
                    detail: {
                        startDate: formatToYMD(startDate),
                        endDate: formatToYMD(endDate)
                    }
                }));

                // Cerrar el modal actual
                window.dispatchEvent(new CustomEvent('close'));
            }

            // Listener para abrir el modal de reportes con las fechas pre-seleccionadas
            document.addEventListener('open-report-modal-with-dates', (event) => {
                const { startDate, endDate } = event.detail;

                // Rellenar los campos de fecha del segundo modal
                document.getElementById('report_start_date').value = startDate;
                document.getElementById('report_end_date').value = endDate;

                // Abrir el segundo modal
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'generate-report-modal' }));
            });
        </script>
    @endpush
</x-app-layout>