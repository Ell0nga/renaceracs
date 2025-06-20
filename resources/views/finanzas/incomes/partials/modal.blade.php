{{-- resources/views/finanzas/incomes/partials/modal.blade.php --}}

<x-modal name="create-income" :show="$errors->incomeCreation->isNotEmpty()" focusable>
    <form method="POST" action="{{ route('finanzas.incomes.store') }}" class="p-6">
        @csrf
        <h2 class="text-lg font-medium text-gray-900">Registrar Nuevo Ingreso</h2>
        <p class="mt-1 text-sm text-gray-600">Completa los campos para añadir un nuevo ingreso.</p>

        <div class="mt-6">
            <x-input-label for="client_number_modal" :value="__('Número de Cliente (Opcional)')" />
            <x-text-input id="client_number_modal" name="client_number" type="text" class="w-full mt-1" value="{{ old('client_number') }}" />
            <x-input-error :messages="$errors->incomeCreation->get('client_number')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="amount_modal" :value="__('Monto (CLP sin decimales)')" />
            <x-text-input id="amount_modal" name="amount" type="number" class="w-full mt-1" value="{{ old('amount') }}" required />
            <x-input-error :messages="$errors->incomeCreation->get('amount')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="transaction_date_modal" :value="__('Fecha del Ingreso')" />
            <x-text-input id="transaction_date_modal" name="transaction_date" type="text" class="w-full mt-1 flatpickr-date" value="{{ old('transaction_date', now()->format('Y-m-d')) }}" required />
            <x-input-error :messages="$errors->incomeCreation->get('transaction_date')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="type_modal" :value="__('Tipo de Ingreso')" />
            <select id="type_modal" name="type" class="w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                <option value="Mensualidad" {{ old('type') == 'Mensualidad' ? 'selected' : '' }}>Mensualidad</option>
                <option value="Instalacion" {{ old('type') == 'Instalacion' ? 'selected' : '' }}>Instalación</option>
            </select>
            <x-input-error :messages="$errors->incomeCreation->get('type')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="payment_method_modal" :value="__('Método de Pago')" />
            <select id="payment_method_modal" name="payment_method" class="w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                <option value="Efectivo" {{ old('payment_method') == 'Efectivo' ? 'selected' : '' }}>Efectivo</option>
                <option value="Tarjeta Credito" {{ old('payment_method') == 'Tarjeta Credito' ? 'selected' : '' }}>Tarjeta Crédito</option>
                <option value="Debito" {{ old('payment_method') == 'Debito' ? 'selected' : '' }}>Débito</option>
                <option value="Transferencia" {{ old('payment_method') == 'Transferencia' ? 'selected' : '' }}>Transferencia</option>
            </select>
            <x-input-error :messages="$errors->incomeCreation->get('payment_method')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="comment_modal" :value="__('Comentario (Opcional)')" />
            <textarea id="comment_modal" name="comment" rows="3" class="w-full mt-1 border-gray-300 rounded-md shadow-sm">{{ old('comment') }}</textarea>
            <x-input-error :messages="$errors->incomeCreation->get('comment')" class="mt-2" />
        </div>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">Cancelar</x-secondary-button>
            <x-primary-button class="ml-3">Registrar Ingreso</x-primary-button>
        </div>
    </form>
</x-modal>
