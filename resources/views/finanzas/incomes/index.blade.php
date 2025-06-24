{{-- resources/views/finanzas/incomes/index.blade.php --}}
<x-app-layout>
    <div class="py-1" x-data="incomesFilter()">
        <div class="w-full px-4 sm:px-6 lg:px-12 xl:px-20 2xl:px-32 mx-auto">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Filtros AJAX Dinámicos --}}
                    <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <x-input-label for="client_filter" :value="__('Cliente')" />
                            <x-text-input id="client_filter" x-model="filters.client_number"
                                @input.debounce.300ms="fetchIncomes()" class="w-full" placeholder="Buscar cliente" />
                        </div>
                        <div>
                            <x-input-label for="type_filter" :value="__('Tipo')" />
                            <select id="type_filter" x-model="filters.type" @change="fetchIncomes()"
                                class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Todos</option>
                                <option value="Mensualidad">Mensualidad</option>
                                <option value="Instalacion">Instalación</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="payment_filter" :value="__('Método de Pago')" />
                            <select id="payment_filter" x-model="filters.payment_method" @change="fetchIncomes()"
                                class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Todos</option>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Tarjeta Credito">Tarjeta Crédito</option>
                                <option value="Debito">Débito</option>
                                <option value="Transferencia">Transferencia</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="date_filter" :value="__('Fecha')" />
                            <x-text-input id="date_filter" class="w-full flatpickr-date" placeholder="YYYY-MM-DD"
                                x-model="filters.transaction_date" @change="fetchIncomes()" />
                        </div>
                    </div>

                    {{-- Botones principales --}}
                    <div class="mb-4 flex flex-wrap justify-between items-center gap-4">
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('finanzas.dashboard') }}"
                                class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 text-xs font-semibold">
                                Dashboard de Finanzas
                            </a>
                            <a href="{{ route('finanzas.expenses.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 text-xs font-semibold">
                                Ver Gastos
                            </a>
                        </div>
                        <x-primary-button x-on:click.prevent="$dispatch('open-modal', 'create-income')">
                            Registrar Nuevo Ingreso
                        </x-primary-button>
                    </div>

                    {{-- Tabla renderizada: inicialmente por Blade, luego solo el tbody vía AJAX --}}
                    <div id="incomes-table" x-ref="tableContainer">
                        @include('finanzas.incomes.partials.table', ['incomes' => $incomes])
                    </div>

                </div>
            </div>
        </div>
    </div>

    @include('finanzas.incomes.partials.modal')

    @push('scripts')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

        <script>
            function incomesFilter() {
                return {
                    filters: {
                        client_number: '',
                        type: '',
                        payment_method: '',
                        transaction_date: ''
                    },
                    fetchIncomes() {
                        const params = new URLSearchParams(this.filters).toString();
                        fetch(`{{ route('finanzas.incomes.index') }}?ajax=1&${params}`)
                            .then(res => res.text())
                            .then(html => {
                                // Solo inyectar el contenido de la tabla, no toda la respuesta
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');
                                const newTable = doc.querySelector('#incomes-table');
                                this.$refs.tableContainer.innerHTML = newTable ? newTable.innerHTML : html;
                            })
                            .catch(() => {
                                this.$refs.tableContainer.innerHTML = '<p class="text-red-500 text-center py-4">Error al cargar la tabla.</p>';
                            });
                    }
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                flatpickr(".flatpickr-date", {
                    dateFormat: "Y-m-d",
                    locale: "es"
                });
            });
        </script>
    @endpush
</x-app-layout>
