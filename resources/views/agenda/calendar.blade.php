<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

            {{-- Navegación del Calendario (Estilo Mac) --}}
            <div class="flex items-center justify-between mb-6 border-b pb-4">
                {{-- Botón Anterior --}}
                <a href="{{ route('agenda.calendar', ['mes' => $fechaSeleccionada->copy()->subMonth()->format('Y-m')]) }}"
                   class="p-2 rounded-full hover:bg-gray-100 transition duration-150 ease-in-out">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>

                {{-- Mes y Año --}}
                <h2 class="text-2xl font-semibold text-gray-800">
                    {{ $fechaSeleccionada->isoFormat('MMMM YYYY') }} {{-- Asegúrate de tener Carbon y isoFormat para nombres de mes localizados --}}
                </h2>

                {{-- Botón Siguiente --}}
                <a href="{{ route('agenda.calendar', ['mes' => $fechaSeleccionada->copy()->addMonth()->format('Y-m')]) }}"
                   class="p-2 rounded-full hover:bg-gray-100 transition duration-150 ease-in-out">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-7 gap-px border border-gray-200 bg-gray-200 rounded-md overflow-hidden">
                @php
                    $diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo']; // Nombres completos
                @endphp

                {{-- Encabezados de días (Nombres de días de la semana) --}}
                @foreach ($diasSemana as $dia)
                    <div class="text-center font-medium text-gray-500 bg-white py-3">
                        {{ $dia }}
                    </div>
                @endforeach

                {{-- Relleno de días del mes anterior --}}
                @php
                    // Primer día visible en la cuadrícula (el lunes de la primera semana del mes)
                    $primerDiaVisibleEnCuadricula = $fechaSeleccionada->copy()->startOfMonth()->startOfWeek(Carbon\Carbon::MONDAY);
                @endphp

                @while ($primerDiaVisibleEnCuadricula < $fechaSeleccionada->copy()->startOfMonth())
                    <div class="bg-gray-50 h-28 lg:h-32 p-2 text-gray-400 border-b border-r border-gray-200">
                        <div class="text-xs text-right">{{ $primerDiaVisibleEnCuadricula->format('j') }}</div>
                    </div>
                    @php $primerDiaVisibleEnCuadricula->addDay(); @endphp
                @endwhile

                {{-- Celdas del calendario (Días del mes actual) --}}
                @php
                    $fechaIteracion = $fechaSeleccionada->copy()->startOfMonth(); // Empezamos a iterar desde el primer día del mes actual
                @endphp

                @while ($fechaIteracion <= $fechaSeleccionada->copy()->endOfMonth())
                    @php
                        $esHoy = $fechaIteracion->isSameDay($hoy);
                        $formatoFecha = $fechaIteracion->format('Y-m-d');
                        $resumenEventos = $eventos->get($formatoFecha) ?? collect();
                    @endphp

                    <div class="bg-white h-28 lg:h-32 p-2 relative hover:bg-blue-50 cursor-pointer border-b border-r border-gray-200"
                         @click="alert('Abrir modal para {{ $formatoFecha }}')"
                         style="border-right-width: {{ $fechaIteracion->dayOfWeekIso === 7 ? '0' : '1' }}px;">
                        <div class="absolute top-2 right-2 text-sm font-semibold {{ $esHoy ? 'text-red-600 bg-red-100 rounded-full w-6 h-6 flex items-center justify-center' : 'text-gray-700' }}">
                            {{ $fechaIteracion->format('j') }}
                        </div>

                        <div class="mt-8 space-y-1 text-xs text-gray-700 overflow-y-auto max-h-[calc(100%-40px)] custom-scrollbar">
                            @forelse ($resumenEventos as $tipo) {{-- Aquí $tipo es el objeto (motivo, total) --}}
                                <div class="truncate px-1 py-0.5 rounded-md text-white
                                    @if ($tipo->motivo === 'Mensualidad') bg-green-500
                                    @elseif ($tipo->motivo === 'Instalacion') bg-blue-500
                                    @else bg-purple-500 @endif
                                ">
                                    <span class="font-medium">{{ $tipo->motivo }}</span>: {{ $tipo->total }}
                                </div>
                            @empty
                                {{-- <p class="text-gray-400">No hay eventos</p> --}}
                            @endforelse
                        </div>
                    </div>

                    @php $fechaIteracion->addDay(); @endphp
                @endwhile

                {{-- Relleno de días del mes siguiente --}}
                @php
                    // El último día visible en la cuadrícula (el domingo de la última semana del mes)
                    $ultimoDiaVisibleEnCuadricula = $fechaSeleccionada->copy()->endOfMonth()->endOfWeek(Carbon\Carbon::SUNDAY);
                @endphp
                @while ($fechaIteracion <= $ultimoDiaVisibleEnCuadricula)
                    <div class="bg-gray-50 h-28 lg:h-32 p-2 text-gray-400 border-b border-r border-gray-200"
                         style="border-right-width: {{ $fechaIteracion->dayOfWeekIso === 7 ? '0' : '1' }}px;">
                        <div class="text-xs text-right">{{ $fechaIteracion->format('j') }}</div>
                    </div>
                    @php $fechaIteracion->addDay(); @endphp
                @endwhile
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    /* Estilos personalizados para la barra de desplazamiento en los eventos */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
        height: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent; /* Color del fondo de la barra de desplazamiento */
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2); /* Color del "pulgar" de la barra de desplazamiento */
        border-radius: 20px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background-color: rgba(0, 0, 0, 0.3);
    }
</style>