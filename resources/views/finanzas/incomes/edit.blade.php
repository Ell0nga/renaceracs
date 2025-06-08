<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Ingreso') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('incomes.update', $income) }}">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="client_number" :value="__('Número de Cliente (Opcional)')" />
                            <x-text-input id="client_number" class="block mt-1 w-full" type="text" name="client_number" :value="old('client_number', $income->client_number)" autofocus />
                            <x-input-error :messages="$errors->get('client_number')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="amount" :value="__('Monto (CLP sin decimales)')" />
                            <x-text-input id="amount" class="block mt-1 w-full" type="number" name="amount" :value="old('amount', $income->amount)" required />
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="transaction_date" :value="__('Fecha del Ingreso')" />
                            <x-text-input id="transaction_date" class="block mt-1 w-full" type="text" name="transaction_date" :value="old('transaction_date', $income->transaction_date_formatted)" required />
                            <x-input-error :messages="$errors->get('transaction_date')" class="mt-2" />
                            <small class="text-gray-500">Formato: dd-mm-yyyy</small>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="type" :value="__('Tipo de Ingreso')" />
                            <select id="type" name="type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                @foreach($incomeTypes as $type)
                                    <option value="{{ $type }}" {{ old('type', $income->type) == $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="payment_method" :value="__('Método de Pago')" />
                            <select id="payment_method" name="payment_method" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                @foreach($paymentMethods as $method)
                                    <option value="{{ $method }}" {{ old('payment_method', $income->payment_method) == $method ? 'selected' : '' }}>{{ $method }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="comment" :value="__('Comentario (Opcional)')" />
                            <textarea id="comment" name="comment" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('comment', $income->comment) }}</textarea>
                            <x-input-error :messages="$errors->get('comment')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Actualizar Ingreso') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
