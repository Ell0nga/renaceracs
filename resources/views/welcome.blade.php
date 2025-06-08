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
            /* Asegura que el body use el 100% del alto de la ventana para centrado vertical */
            html, body {
                height: 100%;
                margin: 0;
                overflow: hidden; /* Evita scroll de fondo al abrir modal */
            }
            body {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }
            /* Clases para el fondo de puntos que vienen por defecto en Laravel */
            .bg-dots-darker {
                background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
            }
            @media (prefers-color-scheme: dark) {
                .bg-dots-lighter {
                    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
                }
            }
        </style>
    </head>
    <body class="bg-gray-100 dark:bg-gray-900 bg-dots-darker dark:bg-dots-lighter">
        <div class="relative min-h-screen flex flex-col justify-center items-center pt-6 sm:pt-0">
            {{-- ELIMINAMOS EL BOTÓN DE LOGIN SUPERIOR DERECHO --}}
            {{-- @if (Route::has('login'))
                <div class="absolute top-0 right-0 mt-6 mr-6">
                    <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:outline-red-500">
                        {{ __('Log in') }}
                    </a>
                </div>
            @endif --}}

            <div class="flex flex-col items-center">
                {{-- LOGO PARA MODO CLARO (LETRAS NEGRAS) --}}
                <img src="{{ asset('images/LOGO RENACER LETRAS NEGRAS 2.png') }}"
                     alt="Logo Renacer (Modo Claro)"
                     class="block dark:hidden h-48 w-auto max-w-xs px-4 object-contain mb-8"> {{-- Añadido max-w-xs y px-4 --}}

                {{-- LOGO PARA MODO OSCURO (LETRAS BLANCAS) --}}
                <img src="{{ asset('images/LOGO RENACER LETRAS BLANCAS.png') }}"
                     alt="Logo Renacer (Modo Oscuro)"
                     class="hidden dark:block h-48 w-auto max-w-xs px-4 object-contain mb-8"> {{-- Añadido max-w-xs y px-4 --}}

                {{-- Botón de Login CENTRAL --}}
                <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'login-modal')"
                    class="bg-blue-600 hover:bg-blue-700 active:bg-blue-900 focus:ring-blue-500 text-lg px-8 py-4">
                    {{ __('Iniciar Sesión') }}
                </x-primary-button>
            </div>
        </div>

        {{-- MODAL DE LOGIN --}}
        <x-modal name="login-modal" :show="$errors->login->isNotEmpty()" focusable>
            <form method="POST" action="{{ route('login') }}" class="p-6">
                @csrf

                {{-- LOGO EN EL MODAL (NORMALMENTE SIEMPRE ES EL DE LETRAS NEGRAS O UNO NEUTRO EN EL MODAL) --}}
                <div class="flex justify-center mb-4">
                    <img src="{{ asset('images/LOGO RENACER LETRAS NEGRAS 2.png') }}"
                         alt="Logo Renacer"
                         class="h-20 w-auto object-contain">
                </div>

                <h2 class="text-lg font-medium text-gray-900 mb-4 text-center">
                    {{ __('Acceder a tu Cuenta') }}
                </h2>

                <div>
                    <x-input-label for="email" :value="__('Correo Electrónico')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->login->get('email')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="password" :value="__('Contraseña')" />
                    <x-text-input id="password" class="block mt-1 w-full"
                                    type="password"
                                    name="password"
                                    required autocomplete="current-password" />
                    <x-input-error :messages="$errors->login->get('password')" class="mt-2" />
                </div>

                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                        <span class="ms-2 text-sm text-gray-600">{{ __('Recordarme') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-end mt-4">
                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                            {{ __('¿Olvidaste tu contraseña?') }}
                        </a>
                    @endif

                    <x-primary-button class="ms-4 bg-blue-600 hover:bg-blue-700 active:bg-blue-900 focus:ring-blue-500">
                        {{ __('Iniciar Sesión') }}
                    </x-primary-button>
                </div>

                {{-- Mensaje de error general si las credenciales son incorrectas --}}
                @if ($errors->login->has('email') && !$errors->login->has('password'))
                    <div class="mt-4 text-red-500 text-sm">
                        {{ $errors->login->first('email') }}
                    </div>
                @endif
                @if ($errors->login->has('password') && !$errors->login->has('email'))
                    <div class="mt-4 text-red-500 text-sm">
                        {{ $errors->login->first('password') }}
                    </div>
                @endif
                @if ($errors->login->has('email') && $errors->login->has('password') && $errors->login->first('email') === __("auth.failed"))
                    <div class="mt-4 text-red-500 text-sm">
                        {{ __("auth.failed") }}
                    </div>
                @endif
            </form>
        </x-modal>

        {{-- Script para abrir el modal si hay errores de login --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Si la sesión tiene errores de login (por ejemplo, credenciales incorrectas),
                // o si el campo 'email' o 'password' del formulario de login tiene errores,
                // abrimos el modal de login automáticamente.
                @if ($errors->login->any())
                    window.setTimeout(() => {
                        Livewire.dispatch('open-modal', 'login-modal');
                    }, 0);
                @endif
            });
        </script>
    </body>
</html>