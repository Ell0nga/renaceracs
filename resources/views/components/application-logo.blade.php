{{--
    Este componente muestra el logo de la aplicación,
    con soporte para modo claro y oscuro.

    Usa LOGO RENACER LETRAS NEGRAS 2.jpg para modo claro
    y LOGO RENACER LETRAS BLANCAS.jpg para modo oscuro.
--}}

<img src="{{ asset('images/LOGO RENACER LETRAS NEGRAS 2.png') }}"
     alt="Logo Renacer (Modo Claro)"
     {{ $attributes->merge(['class' => 'block dark:hidden h-20 w-auto object-contain']) }}>

<img src="{{ asset('images/LOGO RENACER LETRAS NEGRAS 2.png') }}"
     alt="Logo Renacer (Modo Oscuro)"
     {{ $attributes->merge(['class' => 'hidden dark:block h-20 w-auto object-contain']) }}>