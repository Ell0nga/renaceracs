<x-app-layout>
 

    <div class="py-1">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-semibold mb-6">{{ __("¡Portal Interno RENACER TELECOMUNICACIONES") }}</h3>
                    <p class="mb-8 text-gray-700">{{ __("Selecciona una opción para comenzar a gestionar tu aplicación.") }}</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {{-- BOTÓN: GESTIÓN DE FINANZAS CON TOOLTIP PERSONALIZADO --}}
                        <a href="{{ route('finanzas.dashboard') }}"
                           x-data="{ tooltip: false }" 
                           x-on:mouseenter="tooltip = true" 
                           x-on:mouseleave="tooltip = false" 
                           class="relative flex flex-col items-center justify-center 
                                  p-2                 
                                  w-20 h-20             
                                  bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-900 focus:ring-indigo-500 
                                  rounded-lg shadow-md text-white font-bold text-xl 
                                  transition duration-150 ease-in-out transform hover:scale-105
                                  group"> {{-- Añadido 'group' por si lo necesitas para estilos más avanzados --}}
                           
                           <img src="{{ asset('images/finanzas.png') }}" 
                                alt="Finanzas" 
                                class="w-full h-full object-contain mb-0">
                            
                            {{-- CONFIRMA QUE NO HAYA TEXTO "Gestión de Finanzas" AQUÍ --}}

                            {{-- EL TOOLTIP PERSONALIZADO --}}
                            <div x-cloak 
                                 x-show="tooltip" 
                                 x-transition:enter="transition ease-out duration-200" 
                                 x-transition:enter-start="opacity-0 scale-90" 
                                 x-transition:enter-end="opacity-100 scale-100" 
                                 x-transition:leave="transition ease-in duration-200" 
                                 x-transition:leave-start="opacity-100 scale-100" 
                                 x-transition:leave-end="opacity-0 scale-90" 
                                 class="absolute top-full left-1/2 -translate-x-1/2 mt-2 
                                        px-4 py-1 
                                        bg-gray-800 text-white text-xs 
                                        rounded-md shadow-lg whitespace-nowrap 
                                        z-50"> {{-- CAMBIO: z-50 para máxima prioridad --}}
                                Gestión de Finanzas
                            </div>
                        </a>

                        {{-- Puedes añadir más botones aquí para otras secciones de tu aplicación --}}
                        {{--
                        <a href="#"
                           class="flex flex-col items-center justify-center p-6 bg-gray-600 hover:bg-gray-700 active:bg-gray-900 focus:ring-gray-500 rounded-lg shadow-md text-white font-bold text-xl transition duration-150 ease-in-out transform hover:scale-105">
                            <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 21h7a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2h3m0 0l-1 1m1-1l1 1m-1-1v-4m0 0H7m3 0l-1-1m1 1l1-1m-1 1v-4m0 0H7m3 0l-1-1m1 1l1-1m-1 1v-4"></path></svg>
                            Otra Sección
                        </a>
                        --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>