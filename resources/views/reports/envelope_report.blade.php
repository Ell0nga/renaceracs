<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Impresión de Sobre - Renacer Telecomunicaciones</title>
    <style>
        /* Usamos variables para que sea más fácil ajustar las medidas en el futuro */
        :root {
            --ancho-hoja: 21.6cm; /* Ancho estándar de carta/oficio para sobre */
            --alto-hoja: 33cm;  /* <--- ¡AJUSTADO! Era 33.9cm. Este es el cambio CLAVE. */
            --aleta-lateral: 1.5cm; /* Ancho de las aletas laterales del sobre */
            --alto-panel-central: 11cm; /* <--- AJUSTADO */
            --alto-panel-superior: 5cm; /* <--- AJUSTADO */
            --color-principal: #3498db; /* Un azul para detalles, puedes ajustarlo al de tu logo */
            --color-secundario: #f2f2f2; /* Gris claro para fondos, usado en la tabla */
        }

        /* Configuración de la página para impresión */
        @page {
            size: legal; /* Usar tamaño legal para aprovechar la altura */
            margin: 0; /* Eliminar márgenes predeterminados de la página */
        }

        html, body {
            margin: 0;
            padding: 0;
            overflow: hidden; /* Prevenir cualquier desbordamiento visual */
        }

        body {
            font-family: Arial, sans-serif;
            width: var(--ancho-hoja);
            height: var(--alto-hoja);
            position: relative;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact; /* Fuerza la impresión de colores de fondo y bordes */
            print-color-adjust: exact;
        }

        /* Contenedor para el contenido que se centra dentro de cada panel */
        .contenido-centrado {
            padding: 0.5cm 0.7cm; /* Padding general para el contenido */
            margin: 0 var(--aleta-lateral); /* Margen para las aletas laterales del sobre */
            box-sizing: border-box;
            height: calc(100% - 1cm); /* 100% menos (0.5cm padding-top + 0.5cm padding-bottom) */
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center; /* Centrar horizontalmente los items dentro de este flex */
            text-align: center; /* Centrar texto por defecto en este contenedor */
        }

        /* --- PANELES PRINCIPALES DEL SOBRE --- */
        #panel-superior {
            height: var(--alto-panel-superior);
            position: relative;
            overflow: hidden;
            background-color: transparent; /* Fondo transparente para ahorrar tinta */
            display: flex; /* Añadir flex para controlar .contenido-centrado */
            align-items: flex-start; /* Alinear el contenido-centrado al inicio */
            justify-content: center;
        }

        /* Ajuste para bajar el contenido del panel superior (el encabezado) */
        #panel-superior .contenido-centrado {
            padding-top: 2cm; /* Mantiene este valor para bajar el contenido */
            height: 100%; /* Asegura que ocupe todo el espacio vertical para que el padding-top funcione */
        }


        #panel-central {
            height: var(--alto-panel-central); 
            position: relative;
            overflow: hidden;
            background-color: white; /* Fondo blanco */
            
            /* ¡CLAVE PARA EL CENTRADO VERTICAL DEL FRENTE! */
            display: flex; /* Activa flexbox para este panel */
            align-items: center; /* Centra verticalmente el .contenido-centrado hijo */
            justify-content: center; /* Asegura que si el .contenido-centrado no ocupa todo, esté centrado horizontalmente dentro del panel */
        }
        
        /* Ajuste específico para el contenido del panel central (el frente) */
        #panel-central .contenido-centrado {
            padding-top: 3cm; /* Mantiene este valor, "subiendo" un poco el logo y liberando espacio */
            justify-content: flex-start; /* Aseguramos que los items internos se alineen al inicio de su propio bloque */
            height: 100%; /* Asegura que el contenido-centrado ocupe toda la altura de su padre flex */
        }


        #panel-inferior {
            /* IMPORTANTE: La altura de este panel se ajustará automáticamente */
            height: calc(var(--alto-hoja) - var(--alto-panel-central) - var(--alto-panel-superior));
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: flex-start; /* Alineamos a la izquierda para poder controlar el h2 */
            justify-content: flex-start; /* Alinea los elementos al inicio verticalmente */
            background-color: white; /* Asegura un fondo blanco para el contenido inferior */
        }

        /* Ajuste para bajar el contenido del panel inferior (la tabla) */
        #panel-inferior .contenido-centrado {
            padding-top: 1.2cm; /* Mantiene este valor para que la tabla conserve su altura deseada */
            height: 100%; /* Asegura que ocupe todo el espacio vertical */
            justify-content: flex-start; /* Asegura que los items internos se alineen al inicio de su propio bloque */
        }
        
        /* Rota el contenido de los paneles que van en el reverso del sobre */
        #panel-superior .contenido-centrado,
        #panel-inferior .contenido-centrado {
            transform: rotate(180deg);
        }

        /* --- LÍNEAS GUÍA (OCULTAS POR COMPLETO) --- */
        .linea-guia {
            border: 1px dashed #ccc;
            position: absolute;
            z-index: 10;
            display: none !important; /* Estarán ocultas por defecto y siempre */
        }
        #linea-superior { top: var(--alto-panel-superior); width: 100%; border-style: dashed none none none; }
        #linea-inferior { top: calc(var(--alto-panel-superior) + var(--alto-panel-central)); width: 100%; border-style: dashed none none none; }
        #aleta-izquierda { left: var(--aleta-lateral); height: 100%; border-style: none none none dashed; }
        #aleta-derecha { right: var(--aleta-lateral); height: 100%; border-style: none none none dashed; }

        /* --- ESTILOS ESPECÍFICOS DEL CONTENIDO --- */
        .logo {
            max-height: 40px;
            margin-bottom: 10px;
        }
        
        h2 { 
            margin: 8px 0;
            font-size: 16pt;
            color: #333;
        }
        /* Alineamos el h2 de "Mensualidades Pagadas" */
        #panel-inferior .contenido-centrado h2 {
            text-align: left; /* Alinea el texto a la izquierda */
            width: 100%; /* Asegura que ocupe todo el ancho para el text-align */
            margin-left: 1cm; /* Coincide con el margin-left de la tabla */
            box-sizing: border-box; /* Incluir padding/border en el ancho */
        }

        p { 
            margin: 4px 0;
            font-size: 10pt;
            line-height: 1.3; 
            color: #555;
        }

        .decorative-line {
            width: 80%;
            height: 1px;
            background-color: var(--color-principal);
            margin: 15px auto; 
        }

        /* Estilos para la tabla de mensualidades */
        table {
            width: 69%; 
            margin-top: 10px;
            margin-left: 1cm; 
            margin-right: auto;
            margin-bottom: 0; /* Asegurarse de que no haya margen inferior excesivo */
            border-collapse: collapse;
            font-size: 7.5pt;
            flex-grow: 1; 
            overflow: hidden;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 3px 6px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis; 
            color: #444;
        }
        th { 
            background-color: var(--color-secundario);
            text-align: center; 
            font-weight: bold;
        }
        tfoot td { 
            font-weight: bold; 
            background-color: #e8e8e8;
            color: #222;
        }
        
        /* === AJUSTES DE ANCHO DE COLUMNAS DE LA TABLA === */
        table th:first-child, table td:first-child {
             width: 30%;
             text-align: left;
        }
        table th:last-child, table td:last-child {
             width: 50%;
             text-align: right;
        }
        tfoot td:first-child {
            text-align: left;
        }

        /* Mensaje de cierre en el panel inferior */
        .closing-message {
            margin-top: 5px; 
            font-size: 9pt;
            color: #777;
            text-align: center;
            width: 80%;
            line-height: 1.4;
            margin-bottom: 0; /* Asegurarse de que no haya margen inferior */
        }
    </style>
</head>
<body>
    <div class="linea-guia" id="linea-superior"></div>
    <div class="linea-guia" id="linea-inferior"></div>
    <div class="linea-guia" id="aleta-izquierda"></div>
    <div class="linea-guia" id="aleta-derecha"></div>

    <div id="panel-superior">
        <div class="contenido-centrado">
            </div>
    </div>

    <div id="panel-central">
        <div class="contenido-centrado">
            <img src="{{ public_path('images/LOGO RENACER LETRAS NEGRAS 2.png') }}" alt="Logo Renacer" class="logo">
            <h2>Entrega de Mensualidades</h2>
            <div class="decorative-line"></div>
            <p><strong>Fecha de Entrega:</strong> {{ $deliveryDate }}</p>
            <p><strong>Monto a Entregar:</strong> ${{ number_format($amountToDeliver ?? 0, 0, ',', '.') }} CLP</p>
        </div>
    </div>

    <div id="panel-inferior">
        <div class="contenido-centrado">
             <h2>Mensualidades Pagadas</h2>
             <table>
                <thead>
                    <tr>
                        <th>Número de Cliente</th>
                        <th>Monto</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Usamos @forelse para manejar el caso de no haber registros --}}
                    @forelse($monthlyDetails ?? [] as $income)
                        <tr>
                            <td>{{ $income->client_number ?? 'N/A' }}</td>
                            <td>${{ number_format($income->amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" style="text-align: center;">No hay registros para este período.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td><strong>Total:</strong></td>
                        <td><strong>${{ number_format($totalMonthlyIncomes ?? 0, 0, ',', '.') }} CLP</strong></td>
                    </tr>
                </tfoot>
            </table>
            <p class="closing-message">
                Agradecemos su preferencia y confianza en nuestros servicios. <br>¡Renacer Telecomunicaciones, siempre a su servicio!
            </p>
        </div>
    </div>

</body>
</html>