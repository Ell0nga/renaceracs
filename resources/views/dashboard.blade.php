<x-app-layout>
    {{-- Mantén el slot del header si lo estás usando en tu app.blade.php --}}
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Principal') }}
        </h2>
    </x-slot> --}}

    {{-- Aumentamos el padding vertical del div principal para dar más espacio --}}
    <div class="py-8">
        {{-- CAMBIADO: Eliminar max-w-7xl para que tome el ancho completo del layout principal,
                     o puedes cambiarlo a max-w-full o max-w-screen-2xl si quieres mantener un límite aquí --}}
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8"> {{-- ¡Este es el cambio clave! --}}
            {{-- Eliminamos 'overflow-hidden' del contenedor para permitir que los tooltips se muestren --}}
            <div class="bg-white shadow-sm sm:rounded-lg"> 
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-semibold mb-6">{{ __("¡Portal Interno RENACER TELECOMUNICACIONES") }}</h3>
                    <p class="mb-8 text-gray-700">{{ __("Selecciona una opción para comenzar a gestionar tu aplicación.") }}</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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
                                     group">
                            <img src="{{ asset('images/finanzas.png') }}" 
                                    alt="Finanzas" 
                                    class="w-full h-full object-contain mb-0">
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
                                             z-50">
                                Gestión de Finanzas
                            </div>
                        </a>

                        <a href="{{ route('fiber.reports.index') }}"
                            x-data="{ tooltip: false }" 
                            x-on:mouseenter="tooltip = true" 
                            x-on:mouseleave="tooltip = false" 
                            class="relative flex flex-col items-center justify-center 
                                     p-2          
                                     w-20 h-20         
                                     bg-blue-600 hover:bg-blue-700 active:bg-blue-900 focus:ring-blue-500 
                                     rounded-lg shadow-md text-white font-bold text-xl 
                                     transition duration-150 ease-in-out transform hover:scale-105
                                     group">
                            <svg class="w-full h-full text-white" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"/>
                            </svg>
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
                                             z-50">
                                Reportes de Fibra
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>