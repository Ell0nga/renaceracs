<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes de Fibra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #map {
            height: 400px;
            width: 100%;
            display: none;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">Reportes de Fibra</h1>
        <div class="mt-4">
            <button id="toggleMap" class="btn btn-info mb-3">Mostrar Mapa de Clientes</button>
            <div id="map"></div>
        </div>
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>Número del Cliente</th>
                    <th>Número de Precinto</th>
                    <th>Tipo de Conector</th>
                    <th>Latitud</th>
                    <th>Longitud</th>
                    <th>Fecha de Creación</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reports as $report)
                    <tr>
                        <td>{{ $report->client_number }}</td>
                        <td>{{ $report->seal_number }}</td>
                        <td>{{ $report->connector_type }}</td>
                        <td>{{ $report->latitude }}</td>
                        <td>{{ $report->longitude }}</td>
                        <td>{{ $report->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyALwIgNpQXdWNOKx7vR7aN3HWhF_AV4Y24&callback=initMap"
        async defer></script>
    <script>
        let map;
        async function initMap() {
            // Obtener reportes del servidor
            let reports = [];
            try {
                const response = await fetch('/fiber-reports-data', {
                    headers: {
                        'Accept': 'application/json',
                    },
                });
                if (!response.ok) {
                    throw new Error('Error al obtener los reportes');
                }
                reports = await response.json();
            } catch (error) {
                console.error('Error al cargar reportes:', error);
                return;
            }

            // Inicializar el mapa con el nuevo centro
            const mapOptions = {
                center: { lat: -36.012776454826366, lng: -71.65398229256235 }, // Nuevo centro
                zoom: 13,
            };
            map = new google.maps.Map(document.getElementById('map'), mapOptions);

            // Agregar pines para cada reporte
            reports.forEach(report => {
                const marker = new google.maps.Marker({
                    position: { lat: parseFloat(report.latitude), lng: parseFloat(report.longitude) },
                    map: map,
                    title: `Cliente: ${report.client_number}`,
                });
                const infoWindow = new google.maps.InfoWindow({
                    content: `
                        <div>
                            <h6>Cliente: ${report.client_number}</h6>
                            <p>Precinto: ${report.seal_number}</p>
                            <p>Conector: ${report.connector_type}</p>
                            <p>Coordenadas: ${report.latitude}, ${report.longitude}</p>
                        </div>
                    `,
                });
                marker.addListener('click', () => {
                    infoWindow.open(map, marker);
                });
            });
        }

        // Mostrar/Ocultar mapa
        const toggleMapBtn = document.getElementById('toggleMap');
        const mapDiv = document.getElementById('map');
        toggleMapBtn.addEventListener('click', () => {
            if (mapDiv.style.display === 'none') {
                mapDiv.style.display = 'block';
                toggleMapBtn.textContent = 'Ocultar Mapa';
                if (!map) {
                    initMap();
                }
            } else {
                mapDiv.style.display = 'none';
                toggleMapBtn.textContent = 'Mostrar Mapa de Clientes';
            }
        });
    </script>
</body>

</html>