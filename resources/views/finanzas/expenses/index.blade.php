<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4 flex justify-between items-center">
                        <!-- Contenedor para los botones de navegación -->
                        <div class="flex space-x-2">
                            <a href="{{ route('finanzas.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:bg-blue-600 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Dashboard de Finanzas') }}
                            </a>
                            <a href="{{ route('finanzas.incomes.index') }}" class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 focus:bg-green-600 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Ver Ingresos') }}
                            </a>
                        </div>
                        <!-- Botón para abrir el modal de nuevo gasto -->
                        <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'create-expense')" class="bg-red-600 hover:bg-red-700">
                            {{ __('Registrar Nuevo Gasto') }}
                        </x-primary-button>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mt-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                            <button type="button" x-on:click="$dispatch('close-modal', 'create-expense')" class="absolute top-0 right-0 mt-2 mr-2 text-green-700 hover:text-green-900">×</button>
                        </div>
                    @endif

                    <!-- Modal para crear gasto -->
                    <x-modal name="create-expense" :show="$errors->expenseCreation->isNotEmpty()" focusable>
                        <form method="post" action="{{ route('finanzas.expenses.store') }}" class="p-6" id="expense-form">
                            @csrf

                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Registrar Nuevo Gasto') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('Completa los campos para añadir un nuevo gasto.') }}
                            </p>

                            <div class="mt-6">
                                <x-input-label for="expense_category_id_modal" :value="__('Categoría del Gasto')" />
                                <select id="expense_category_id_modal" name="expense_category_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Selecciona una categoría</option>
                                    @foreach($expenseCategories as $category)
                                        <option value="{{ $category->id }}" {{ old('expense_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->expenseCreation->get('expense_category_id')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="amount_modal_expense" :value="__('Monto (CLP sin decimales)')" />
                                <x-text-input id="amount_modal_expense" class="block mt-1 w-full" type="number" name="amount" :value="old('amount')" required />
                                <x-input-error :messages="$errors->expenseCreation->get('amount')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="transaction_date_modal_expense" :value="__('Fecha del Gasto')" />
                                <x-text-input id="transaction_date_modal_expense" class="block mt-1 w-full flatpickr-input" type="text" name="transaction_date" :value="old('transaction_date', \Carbon\Carbon::now()->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->expenseCreation->get('transaction_date')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="payment_method_modal_expense" :value="__('Método de Pago')" />
                                <select id="payment_method_modal_expense" name="payment_method" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="Efectivo" {{ old('payment_method') == 'Efectivo' ? 'selected' : '' }}>Efectivo</option>
                                    <option value="Transferencia" {{ old('payment_method') == 'Transferencia' ? 'selected' : '' }}>Transferencia</option>
                                </select>
                                <x-input-error :messages="$errors->expenseCreation->get('payment_method')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="assigned_to_modal" :value="__('Asignado a (Opcional)')" />
                                <x-text-input id="assigned_to_modal" class="block mt-1 w-full" type="text" name="assigned_to" :value="old('assigned_to')" />
                                <x-input-error :messages="$errors->expenseCreation->get('assigned_to')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="comment_modal_expense" :value="__('Comentario (Opcional)')" />
                                <textarea id="comment_modal_expense" name="comment" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('comment') }}</textarea>
                                <x-input-error :messages="$errors->expenseCreation->get('comment')" class="mt-2" />
                            </div>

                            <div class="mt-6 flex justify-end">
                                <x-secondary-button x-on:click="$dispatch('close')">
                                    {{ __('Cancelar') }}
                                </x-secondary-button>
                                <x-primary-button class="ms-3 bg-red-600 hover:bg-red-700">
                                    {{ __('Registrar Gasto') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </x-modal>

                    @if($expenses->isEmpty())
                        <p class="text-gray-600">No hay gastos registrados aún.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Método</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asignado a</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comentario</th>
                                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Acciones</span></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($expenses as $expense)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $expense->category->name ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ number_format($expense->amount, 0, ',', '.') }} CLP</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($expense->transaction_date)->format('d-m-Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $expense->payment_method }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $expense->assigned_to ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ Str::limit($expense->comment, 50) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('finanzas.expenses.edit', $expense) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Editar</a>
                                                <form action="{{ route('finanzas.expenses.destroy', $expense) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Estás seguro de que quieres eliminar este gasto?');">Eliminar</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $expenses->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                flatpickr("#transaction_date_modal_expense", {
                    dateFormat: "Y-m-d",
                    locale: "es",
                    allowInput: true,
                    defaultDate: "{{ \Carbon\Carbon::now()->format('Y-m-d') }}",
                    onOpen: function (selectedDates, dateStr, instance) {}
                });

                // Escuchar el envío del formulario y cerrar el modal si hay éxito
                const form = document.getElementById('expense-form');
                if (form) {
                    form.addEventListener('submit', function (event) {
                        // Esto se ejecuta antes de que el formulario se envíe
                        // No bloqueamos el envío, solo observamos
                    });
                }

                // Cerrar el modal si hay un mensaje de éxito
                @if(session('success'))
                    setTimeout(() => {
                        $dispatch('close-modal', 'create-expense');
                    }, 100);
                @endif
            });
        </script>
    @endpush
</x-app-layout>