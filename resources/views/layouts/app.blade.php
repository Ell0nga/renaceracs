<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} - PORTAL RENACER</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- INCLUYE ESTA LÍNEA: CSS para Flatpickr --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    {{-- Esto carga tu CSS de Tailwind (app.css) y tu app.js (que probablemente incluye Alpine.js) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-repeat" style="background-image: url('{{ asset('images/wsp.png') }}'); background-color: #ECE5DD;">
    <div class="min-h-screen">
        @include('layouts.navigation')

        @if (isset($header))
            <header class="bg-white shadow w-full">
                <div class="max-w-screen-2xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <main class="w-full">
            <div class="max-w-screen-2xl mx-auto py-6 px-4 sm:px-6 lg:px-8"> 
                {{ $slot }}
            </div>
        </main>
    </div>

    {{-- INCLUYE ESTAS LÍNEAS: Scripts CDN para Chart.js y Flatpickr --}}
    {{-- DEBEN CARGARSE AQUÍ, ANTES DE @stack('scripts') --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

    {{-- Aquí se inyectan los scripts específicos de cada vista que usan @push('scripts') --}}
    @stack('scripts')
</body>

</html>