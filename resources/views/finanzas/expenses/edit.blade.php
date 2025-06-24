<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Gasto') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('finanzas.expenses.update', $expense) }}">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="expense_category_id" :value="__('Categoría del Gasto')" />
                            <select id="expense_category_id" name="expense_category_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                @foreach($expenseCategories as $category)
                                    <option value="{{ $category->id }}" {{ old('expense_category_id', $expense->expense_category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('expense_category_id')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="amount" :value="__('Monto (CLP sin decimales)')" />
                            <x-text-input id="amount" class="block mt-1 w-full" type="number" name="amount" :value="old('amount', $expense->amount)" required />
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="transaction_date" :value="__('Fecha del Gasto')" />
                            <x-text-input id="transaction_date" class="block mt-1 w-full" type="date" name="transaction_date" :value="old('transaction_date', $expense->transaction_date_formatted)" required />
                            <x-input-error :messages="$errors->get('transaction_date')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="payment_method" :value="__('Método de Pago')" />
                            <select id="payment_method" name="payment_method" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                @foreach($paymentMethods as $method)
                                    <option value="{{ $method }}" {{ old('payment_method', $expense->payment_method) == $method ? 'selected' : '' }}>{{ $method }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="assigned_to" :value="__('Asignado a (Opcional)')" />
                            <x-text-input id="assigned_to" class="block mt-1 w-full" type="text" name="assigned_to" :value="old('assigned_to', $expense->assigned_to)" />
                            <x-input-error :messages="$errors->get('assigned_to')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="comment" :value="__('Comentario (Opcional)')" />
                            <textarea id="comment" name="comment" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('comment', $expense->comment) }}</textarea>
                            <x-input-error :messages="$errors->get('comment')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-secondary-button x-on:click="$dispatch('close')">Cancelar</x-secondary-button>
                            <x-primary-button class="ms-4">Actualizar Gasto</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>