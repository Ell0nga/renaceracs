<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Lista de Ingresos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4 flex justify-between items-center">
                        {{-- Contenedor para los botones de navegación --}}
                        <div class="flex space-x-2">
                            <a href="{{ route('finanzas.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:bg-blue-600 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Dashboard de Finanzas') }}
                            </a>
                            <a href="{{ route('finanzas.expenses.index') }}" class="inline-flex items-center px-4 py-2 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-600 focus:bg-red-600 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Ver Gastos') }}
                            </a>
                        </div>
                        {{-- Botón existente para Registrar Nuevo Ingreso --}}
                        <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'create-income')">
                            {{ __('Registrar Nuevo Ingreso') }}
                        </x-primary-button>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mt-4"
                            role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    {{-- Modal para crear ingreso --}}
                    <x-modal name="create-income" :show="$errors->incomeCreation->isNotEmpty()" focusable>
                        <form method="post" action="{{ route('finanzas.incomes.store') }}" class="p-6">
                            @csrf

                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Registrar Nuevo Ingreso') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('Completa los campos para añadir un nuevo ingreso.') }}
                            </p>

                            <div class="mt-6">
                                <x-input-label for="client_number_modal" :value="__('Número de Cliente (Opcional)')" />
                                <x-text-input id="client_number_modal" class="block mt-1 w-full" type="text"
                                    name="client_number" :value="old('client_number')" autofocus />
                                <x-input-error :messages="$errors->incomeCreation->get('client_number')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="amount_modal" :value="__('Monto (CLP sin decimales)')" />
                                <x-text-input id="amount_modal" class="block mt-1 w-full" type="number" name="amount"
                                    :value="old('amount')" required />
                                <x-input-error :messages="$errors->incomeCreation->get('amount')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="transaction_date_modal" :value="__('Fecha del Ingreso')" />
                                <x-text-input id="transaction_date_modal" class="block mt-1 w-full flatpickr-input"
                                    type="text" name="transaction_date"
                                    value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required />
                                <x-input-error :messages="$errors->incomeCreation->get('transaction_date')"
                                    class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="type_modal" :value="__('Tipo de Ingreso')" />
                                <select id="type_modal" name="type"
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required>
                                    <option value="Mensualidad" {{ old('type') == 'Mensualidad' ? 'selected' : '' }}>
                                        Mensualidad</option>
                                    <option value="Instalacion" {{ old('type') == 'Instalacion' ? 'selected' : '' }}>
                                        Instalación</option>
                                </select>
                                <x-input-error :messages="$errors->incomeCreation->get('type')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="payment_method_modal" :value="__('Método de Pago')" />
                                <select id="payment_method_modal" name="payment_method"
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required>
                                    <option value="Efectivo" {{ old('payment_method') == 'Efectivo' ? 'selected' : '' }}>
                                        Efectivo</option>
                                    <option value="Tarjeta Credito" {{ old('payment_method') == 'Tarjeta Credito' ? 'selected' : '' }}>Tarjeta Crédito</option>
                                    <option value="Debito" {{ old('payment_method') == 'Debito' ? 'selected' : '' }}>
                                        Débito</option>
                                    <option value="Transferencia" {{ old('payment_method') == 'Transferencia' ? 'selected' : '' }}>Transferencia</option>
                                </select>
                                <x-input-error :messages="$errors->incomeCreation->get('payment_method')"
                                    class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="comment_modal" :value="__('Comentario (Opcional)')" />
                                <textarea id="comment_modal" name="comment" rows="3"
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('comment') }}</textarea>
                                <x-input-error :messages="$errors->incomeCreation->get('comment')" class="mt-2" />
                            </div>

                            <div class="mt-6 flex justify-end">
                                <x-secondary-button x-on:click="$dispatch('close')">
                                    {{ __('Cancelar') }}
                                </x-secondary-button>

                                <x-primary-button class="ms-3">
                                    {{ __('Registrar Ingreso') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </x-modal>

                    @if($incomes->isEmpty())
                        <p class="text-gray-600">No hay ingresos registrados aún.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Cliente
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
                                            Tipo
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Método
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
                                    @foreach($incomes as $income)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $income->client_number ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                ${{ number_format($income->amount, 0, ',', '.') }} CLP
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($income->transaction_date)->format('d-m-Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $income->type }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $income->payment_method }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ Str::limit($income->comment, 50) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('finanzas.incomes.edit', $income) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 mr-2">Editar</a>
                                                <form action="{{ route('finanzas.incomes.destroy', $income) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                                        onclick="return confirm('¿Estás seguro de que quieres eliminar este ingreso?');">Eliminar</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $incomes->links() }}
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
                flatpickr("#transaction_date_modal", {
                    dateFormat: "Y-m-d",
                    locale: "es",
                    allowInput: true,
                    onOpen: function (selectedDates, dateStr, instance) {
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>