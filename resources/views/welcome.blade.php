{{-- resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Renacer - Inicio de Sesión</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html,
        body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f3f4f6;
            /* bg-gray-100 */
        }

        .bg-dots-darker {
            background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
        }

        @media (prefers-color-scheme: dark) {
            body {
                background-color: #111827;
                /* bg-gray-900 */
            }

            .bg-dots-darker {
                background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
            }
        }
    </style>
</head>

<body class="bg-dots-darker bg-center bg-repeat dark:bg-dots-lighter">
    <div class="w-full px-4 sm:px-6 lg:px-12 xl:px-24 2xl:px-32">
        <div class="flex flex-col items-center justify-center min-h-screen w-full">

            {{-- LOGOS --}}
            <img src="{{ asset('images/LOGO RENACER LETRAS NEGRAS 2.png') }}" alt="Logo Renacer (Claro)"
                class="block dark:hidden h-48 w-auto px-4 object-contain mb-8">

            <img src="{{ asset('images/LOGO RENACER LETRAS BLANCAS.png') }}" alt="Logo Renacer (Oscuro)"
                class="hidden dark:block h-48 w-auto px-4 object-contain mb-8">

            {{-- Botón --}}
            <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'login-modal')"
                class="bg-blue-600 hover:bg-blue-700 active:bg-blue-900 focus:ring-blue-500 text-lg px-8 py-4">
                {{ __('Iniciar Sesión') }}
            </x-primary-button>
        </div>
    </div>

    {{-- MODAL LOGIN --}}
    <x-modal name="login-modal" :show="$errors->login->isNotEmpty()" focusable>
        <form method="POST" action="{{ route('login') }}" class="p-6">
            @csrf

            <div class="flex justify-center mb-4">
                <img src="{{ asset('images/LOGO RENACER LETRAS NEGRAS 2.png') }}" alt="Logo Renacer"
                    class="h-20 w-auto object-contain">
            </div>

            <h2 class="text-lg font-medium text-gray-900 mb-4 text-center">
                {{ __('Acceder a tu Cuenta') }}
            </h2>

            <div>
                <x-input-label for="email" :value="__('Correo Electrónico')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                    required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->login->get('email')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="password" :value="__('Contraseña')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                    autocomplete="current-password" />
                <x-input-error :messages="$errors->login->get('password')" class="mt-2" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Recordarme') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        href="{{ route('password.request') }}">
                        {{ __('¿Olvidaste tu contraseña?') }}
                    </a>
                @endif

                <x-primary-button class="ms-4 bg-blue-600 hover:bg-blue-700 active:bg-blue-900 focus:ring-blue-500">
                    {{ __('Iniciar Sesión') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    {{-- Script para abrir modal en caso de errores --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if ($errors->login->any())
                window.setTimeout(() => {
                    Livewire.dispatch('open-modal', 'login-modal');
                }, 0);
            @endif
        });
    </script>
</body>

</html>