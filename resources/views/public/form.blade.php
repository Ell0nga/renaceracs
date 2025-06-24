<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario Técnico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .connector-image {
            width: 100px;
            height: 100px;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .connector-image.selected {
            border-color: blue;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">Formulario Técnico</h1>
        <form id="fiberForm" class="mt-4">
            <div class="mb-3">
                <label for="client_number" class="form-label">Número del Cliente</label>
                <input type="text" class="form-control" id="client_number" name="client_number" required>
            </div>
            <div class="mb-3">
                <label for="seal_number" class="form-label">Número de Precinto</label>
                <input type="text" class="form-control" id="seal_number" name="seal_number" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Tipo de Conector de Fibra</label>
                <div class="d-flex">
                    <div class="me-3">
                        <img src="{{ asset('images/sc-apc.jpg') }}" alt="SC/APC" class="connector-image"
                            data-type="SC/APC">
                        <p class="text-center">SC/APC</p>
                    </div>
                    <div>
                        <img src="{{ asset('images/sc-upc.jpg') }}" alt="SC/UPC" class="connector-image"
                            data-type="SC/UPC">
                        <p class="text-center">SC/UPC</p>
                    </div>
                </div>
                <input type="hidden" id="connector_type" name="connector_type" required>
            </div>
            <div class="mb-3">
                <button type="button" id="getLocation" class="btn btn-secondary">Obtener Ubicación</button>
                <p id="locationStatus" class="mt-2"></p>
                <input type="hidden" id="latitude" name="latitude" required>
                <input type="hidden" id="longitude" name="longitude" required>
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>
    </div>

    <script>
        // Selección de conector
        const images = document.querySelectorAll('.connector-image');
        const connectorTypeInput = document.getElementById('connector_type');
        images.forEach(img => {
            img.addEventListener('click', () => {
                images.forEach(i => i.classList.remove('selected'));
                img.classList.add('selected');
                connectorTypeInput.value = img.dataset.type;
            });
        });

        // Obtener ubicación
        const getLocationBtn = document.getElementById('getLocation');
        const locationStatus = document.getElementById('locationStatus');
        const latitudeInput = document.getElementById('latitude');
        const longitudeInput = document.getElementById('longitude');

        getLocationBtn.addEventListener('click', () => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    position => {
                        latitudeInput.value = position.coords.latitude;
                        longitudeInput.value = position.coords.longitude;
                        locationStatus.textContent = 'Ubicación obtenida con éxito';
                        locationStatus.style.color = 'green';
                    },
                    error => {
                        locationStatus.textContent = 'Error al obtener la ubicación';
                        locationStatus.style.color = 'red';
                    },
                    { enableHighAccuracy: true }
                );
            } else {
                locationStatus.textContent = 'Geolocalización no soportada';
                locationStatus.style.color = 'red';
            }
        });

        // Manejo del formulario
        const form = document.getElementById('fiberForm');
        form.addEventListener('submit', async e => {
            e.preventDefault();
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);

            // Guardar en localStorage si no hay conexión
            if (!navigator.onLine) {
                const pendingForms = JSON.parse(localStorage.getItem('pendingForms') || '[]');
                pendingForms.push(data);
                localStorage.setItem('pendingForms', JSON.stringify(pendingForms));
                alert('Formulario guardado. Se enviará cuando haya conexión.');
                form.reset();
                return;
            }

            // Enviar al servidor
            try {
                const response = await fetch('/public-form', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(data),
                });
                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`Error en la respuesta del servidor: ${response.status} - ${errorText}`);
                }
                const result = await response.json();
                alert(result.message);
                form.reset();
            } catch (error) {
                console.error('Error al enviar:', error);
                alert('Error al enviar el formulario. Se guardará localmente.');
                const pendingForms = JSON.parse(localStorage.getItem('pendingForms') || '[]');
                pendingForms.push(data);
                localStorage.setItem('pendingForms', JSON.stringify(pendingForms));
                form.reset();
            }
        });

        // Registrar Service Worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/service-worker.js')
                .then(() => console.log('Service Worker registrado'))
                .catch(err => console.error('Error al registrar Service Worker:', err));
        }
    </script>
</body>

</html>