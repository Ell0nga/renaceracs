<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Lista de Gastos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4 flex justify-between items-center">
                        <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'create-expense')"
                            class="bg-red-600 hover:bg-red-700">
                            {{ __('Registrar Nuevo Gasto') }}
                        </x-primary-button>

                        @if(session('success'))
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                                role="alert">
                                <span class="block sm:inline">{{ session('success') }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Modal para crear gasto --}}
                    <x-modal name="create-expense" :show="$errors->expenseCreation->isNotEmpty()" focusable>
                        <form method="post" action="{{ route('expenses.store') }}" class="p-6">
                            @csrf

                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Registrar Nuevo Gasto') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('Completa los campos para añadir un nuevo gasto.') }}
                            </p>

                            <div class="mt-6">
                                <x-input-label for="expense_category_id_modal" :value="__('Categoría del Gasto')" />
                                <select id="expense_category_id_modal" name="expense_category_id"
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required>
                                    <option value="">Selecciona una categoría</option>
                                    {{-- Usamos $expenseCategories que ya se pasa a la vista index --}}
                                    @foreach($expenseCategories as $category)
                                        <option value="{{ $category->id }}" {{ old('expense_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->expenseCreation->get('expense_category_id')"
                                    class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="amount_modal_expense" :value="__('Monto (CLP sin decimales)')" />
                                <x-text-input id="amount_modal_expense" class="block mt-1 w-full" type="number"
                                    name="amount" :value="old('amount')" required />
                                <x-input-error :messages="$errors->expenseCreation->get('amount')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="transaction_date_modal_expense" :value="__('Fecha del Gasto')" />
                                {{-- CAMBIO: type="text" y añadir clase flatpickr-input --}}
                                <x-text-input id="transaction_date_modal_expense"
                                    class="block mt-1 w-full flatpickr-input" type="text" name="transaction_date"
                                    value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required />
                                <x-input-error :messages="$errors->expenseCreation->get('transaction_date')"
                                    class="mt-2" />
                                {{-- <small class="text-gray-500">Formato: dd-mm-yyyy</small> ELIMINAR ESTO YA NO ES
                                NECESARIO --}}
                            </div>

                            <div class="mt-4">
                                <x-input-label for="payment_method_modal_expense" :value="__('Método de Pago')" />
                                <select id="payment_method_modal_expense" name="payment_method"
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required>
                                    <option value="Efectivo" {{ old('payment_method') == 'Efectivo' ? 'selected' : '' }}>
                                        Efectivo</option>
                                    <option value="Transferencia" {{ old('payment_method') == 'Transferencia' ? 'selected' : '' }}>Transferencia</option>
                                </select>
                                <x-input-error :messages="$errors->expenseCreation->get('payment_method')"
                                    class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="assigned_to_modal" :value="__('Asignado a (Opcional)')" />
                                <x-text-input id="assigned_to_modal" class="block mt-1 w-full" type="text"
                                    name="assigned_to" :value="old('assigned_to')" />
                                <x-input-error :messages="$errors->expenseCreation->get('assigned_to')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="comment_modal_expense" :value="__('Comentario (Opcional)')" />
                                <textarea id="comment_modal_expense" name="comment" rows="3"
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('comment') }}</textarea>
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
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Categoría
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Monto
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Fecha
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Método
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Asignado a
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Comentario
                                        </th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Acciones</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($expenses as $expense)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $expense->category->name ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                ${{ number_format($expense->amount, 0, ',', '.') }} CLP
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($expense->transaction_date)->format('d-m-Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $expense->payment_method }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $expense->assigned_to ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ Str::limit($expense->comment, 50) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('expenses.edit', $expense) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 mr-2">Editar</a>
                                                <form action="{{ route('expenses.destroy', $expense) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                                        onclick="return confirm('¿Estás seguro de que quieres eliminar este gasto?');">Eliminar</button>
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
        {{-- Flatpickr CSS y JS --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Inicializar Flatpickr para el campo de fecha en el modal de gastos
                flatpickr("#transaction_date_modal_expense", {
                    dateFormat: "Y-m-d", // Formato para el backend (año-mes-día)
                    locale: "es", // Establecer el idioma a español
                    allowInput: true, // Permite escribir la fecha manualmente
                    onOpen: function (selectedDates, dateStr, instance) {
                        // Puedes añadir lógica aquí si es necesario para reposicionar el modal
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>