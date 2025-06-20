{{-- resources/views/agenda/index.blade.php --}}
<x-app-layout>
    <div class="py-6" x-data="{ motivo: 'Instalación', estado: 'Pendiente' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Título --}}
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Agenda de Instalaciones y Reparaciones</h2>

            {{-- Formulario de Fecha --}}
            <form method="GET" action="{{ route('agenda.index') }}" class="mb-6 flex items-center gap-4">
                <label for="fecha" class="font-semibold text-gray-700">Seleccionar fecha:</label>
                <input type="date" id="fecha" name="fecha" value="{{ $fecha }}" class="border rounded px-3 py-1">
                <x-primary-button>Ver eventos</x-primary-button>
            </form>

            {{-- Alertas --}}
            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            {{-- FORMULARIO DE NUEVA ACTIVIDAD --}}
            <div class="bg-white p-6 rounded shadow mb-6">
                <h3 class="text-xl font-semibold mb-4">Nueva actividad</h3>

                <form method="POST" action="{{ route('agenda.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf

                    {{-- Motivo --}}
                    <div>
                        <x-input-label for="motivo" :value="'Motivo'" />
                        <select name="motivo" id="motivo" x-model="motivo" class="w-full border rounded px-2 py-1" required>
                            <option value="Instalación">Instalación</option>
                            <option value="Reparación">Reparación</option>
                            <option value="Comercial">Comercial</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>

                    {{-- Plan contratado (Title) --}}
                    <div>
                        <x-input-label for="title" :value="'Plan contratado'" />
                        <select name="title" id="title" class="w-full border rounded px-2 py-1" required>
                            <option value="DUO 200">DUO 200</option>
                            <option value="DUO 400">DUO 400</option>
                            <option value="DUO 600">DUO 600</option>
                            <option value="DUO 800">DUO 800</option>
                            <option value="NET 200">NET 200</option>
                            <option value="NET 400">NET 400</option>
                            <option value="NET 600">NET 600</option>
                            <option value="NET 800">NET 800</option>
                            <option value="CATV">CATV</option>
                        </select>
                    </div>

                    {{-- Fecha --}}
                    <div>
                        <x-input-label for="date" :value="'Fecha'" />
                        <x-text-input type="date" name="date" id="date" value="{{ $fecha }}" required />
                    </div>

                    {{-- Horario --}}
                    <div>
                        <x-input-label for="time_slot" :value="'Horario'" />
                        <select name="time_slot" id="time_slot" class="w-full border rounded px-2 py-1" required>
                            <option value="AM">AM</option>
                            <option value="PM">PM</option>
                        </select>
                    </div>

                    {{-- Estado (siempre Pendiente) --}}
                    <div>
                        <x-input-label for="status" :value="'Estado'" />
                        <select name="status" id="status" class="w-full border rounded px-2 py-1 bg-gray-100" disabled>
                            <option value="Pendiente" selected>Pendiente</option>
                        </select>
                        <input type="hidden" name="status" value="Pendiente">
                    </div>

                    {{-- Prioridad --}}
                    <div>
                        <x-input-label for="priority" :value="'Prioridad'" />
                        <select name="priority" id="priority" class="w-full border rounded px-2 py-1" required>
                            <option value="Alta">Alta</option>
                            <option value="Media" selected>Media</option>
                            <option value="Baja">Baja</option>
                        </select>
                    </div>

                    {{-- Número de cliente / orden --}}
                    <div x-show="motivo !== 'Instalación'">
                        <x-input-label for="client_number" :value="'Número de cliente'" />
                        <x-text-input type="text" name="client_number" id="client_number" class="w-full" />
                    </div>

                    <div x-show="motivo === 'Instalación'">
                        <x-input-label for="numero_orden" :value="'Número de orden provisorio'" />
                        <x-text-input type="text" name="numero_orden" id="numero_orden" class="w-full" />
                    </div>

                    {{-- Costo --}}
                    <div>
                        <x-input-label for="costo" :value="'Costo Actividad'" />
                        <x-text-input type="number" name="costo" id="costo" placeholder="Ej: 25000" class="w-full" />
                    </div>

                    {{-- Descripción --}}
                    <div class="md:col-span-2">
                        <x-input-label for="description" :value="'Descripción'" />
                        <textarea name="description" id="description" rows="3" class="w-full border rounded px-2 py-1"></textarea>
                    </div>

                    {{-- Botón --}}
                    <div class="md:col-span-2 text-center">
                        <x-primary-button class="mt-4 w-full md:w-1/2">Guardar actividad</x-primary-button>
                    </div>
                </form>
            </div>

            {{-- Tabla de actividades --}}
            @foreach ($diasConEventos as $fecha => $eventos)
    @php
        $pendientes = collect($eventos)->whereIn('status', ['Pendiente', 'En ejecución']);
    @endphp

    @if ($pendientes->count())
        <div class="bg-white shadow rounded-lg p-4 border border-gray-200 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
                {{ \Carbon\Carbon::parse($fecha)->isoFormat('dddd D [de] MMMM') }}
            </h3>

            <div class="space-y-3">
    @foreach ($pendientes as $evento)
        <div class="bg-white border shadow-sm p-3 rounded-md border-l-4
            @if($evento->status === 'Pendiente') border-yellow-400
            @elseif($evento->status === 'En ejecución') border-blue-400
            @endif
        ">
            <div class="font-semibold text-gray-800 text-sm">
                {{ $evento->title }}
            </div>
            <div class="text-xs text-gray-600">
                @if($evento->motivo === 'Instalación')
                    Orden: {{ $evento->orden_number ?? '—' }}
                @else
                    Cliente: {{ $evento->client_number ?? '—' }}
                @endif
                |
                {{ $evento->time_slot }} |
                {{ $evento->motivo }} |
                <span class="italic">{{ $evento->priority }}</span>
            </div>
            @if ($evento->description)
                <div class="text-xs mt-1 text-gray-500">
                    {{ $evento->description }}
                </div>
            @endif
        </div>
    @endforeach
</div>
        </div>
    @endif
@endforeach

        </div>
    </div>
</x-app-layout>
