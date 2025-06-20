<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Agenda de Actividades</h2>

        {{-- 🔽 Formulario de creación rápida --}}
        <div x-data="{
        motivo: 'Instalación',
        isClientRequired() {
            return (this.motivo !== 'Otro');
        }
    }"
    class="bg-white shadow rounded-lg p-6 mb-8"
>
    <h2 class="text-xl font-semibold mb-4">Agregar nueva actividad</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 text-red-800 p-2 rounded mb-4">
            <ul class="list-disc pl-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('agenda.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4" x-data="{ motivo: '', status: 'Pendiente' }">
    @csrf

    {{-- Motivo y plan contratado --}}
    <div>
        <label class="block font-medium text-sm text-gray-700">Motivo</label>
        <select name="motivo" class="w-full border rounded px-2 py-1" required x-model="motivo">
            <option value="">Seleccionar...</option>
            <option value="Instalación">Instalación</option>
            <option value="Reparación">Reparación</option>
            <option value="Comercial">Comercial</option>
            <option value="Otro">Otro</option>
        </select>
    </div>

    <div>
        <label class="block font-medium text-sm text-gray-700">Plan contratado</label>
        <select name="title" class="w-full border rounded px-2 py-1" required>
            <option value="">Seleccionar...</option>
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

    {{-- Fecha y horario --}}
    <div>
        <label class="block font-medium text-sm text-gray-700">Fecha</label>
        <input type="date" name="date" class="w-full border rounded px-2 py-1" required>
    </div>

    <div>
        <label class="block font-medium text-sm text-gray-700">Horario</label>
        <select name="time_slot" class="w-full border rounded px-2 py-1" required>
            <option value="AM">AM</option>
            <option value="PM">PM</option>
        </select>
    </div>

    {{-- Estado (bloqueado a Pendiente) y Prioridad --}}
    <div>
        <label class="block font-medium text-sm text-gray-700">Estado</label>
        <input type="text" name="status" value="Pendiente" readonly class="w-full border rounded px-2 py-1 bg-gray-100 text-gray-500 cursor-not-allowed">
    </div>

    <div>
        <label class="block font-medium text-sm text-gray-700">Prioridad</label>
        <select name="priority" class="w-full border rounded px-2 py-1" required>
            <option value="Alta">Alta</option>
            <option value="Media" selected>Media</option>
            <option value="Baja">Baja</option>
        </select>
    </div>

    {{-- Número de cliente u orden (condicional) --}}
    <div x-show="motivo !== 'Instalación'" class="transition-all">
        <label class="block font-medium text-sm text-gray-700">Número de cliente</label>
        <input type="text" name="client_number" class="w-full border rounded px-2 py-1">
    </div>

    <div x-show="motivo === 'Instalación'" class="transition-all">
        <label class="block font-medium text-sm text-gray-700">Número de orden</label>
        <input type="text" name="order_number" class="w-full border rounded px-2 py-1">
    </div>

    {{-- Costo --}}
    <div>
        <label class="block font-medium text-sm text-gray-700">Costo Actividad</label>
        <input type="number" step="0.01" name="costo" class="w-full border rounded px-2 py-1">
    </div>

    {{-- Descripción --}}
    <div class="md:col-span-2">
        <label class="block font-medium text-sm text-gray-700">Descripción</label>
        <textarea name="description" rows="2" class="w-full border rounded px-2 py-1"></textarea>
    </div>

    {{-- Botón --}}
    <div class="md:col-span-2 text-center">
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            Guardar actividad
        </button>
    </div>
</form>

</div>


        {{-- 🔽 Tarjetas de días con actividades --}}
        @if($diasConEventos->isEmpty())
            <div class="bg-white p-6 rounded shadow text-center text-gray-500">
                No hay actividades pendientes ni en ejecución.
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach ($diasConEventos as $fecha => $eventos)
                    @php
                        $pendientes = collect($eventos)->whereIn('status', ['Pendiente', 'En ejecución']);
                    @endphp

                    @if ($pendientes->count())
                        <div class="bg-white shadow rounded-lg p-4 border-l-4 border-blue-500">
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">
                                {{ \Carbon\Carbon::parse($fecha)->isoFormat('dddd D [de] MMMM') }}
                            </h3>

                            <ul class="space-y-3">
                                @foreach ($pendientes as $evento)
                                    <li class="p-3 rounded border @if($evento->status === 'Pendiente') border-yellow-400 @elseif($evento->status === 'En ejecución') border-blue-400 @endif">
                                        <div class="font-semibold text-gray-800">{{ $evento->title }}</div>
                                        <div class="text-sm text-gray-600">
                                            Cliente: {{ $evento->client_number ?? '—' }} |
                                            {{ $evento->time_slot }} |
                                            {{ $evento->motivo }} |
                                            <span class="italic">{{ $evento->priority }}</span>
                                        </div>
                                        <div class="text-xs mt-1 text-gray-500">{{ $evento->description }}</div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
