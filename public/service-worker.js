const CACHE_NAME = 'fiber-form-v1';

self.addEventListener('install', event => {
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.filter(name => name !== CACHE_NAME)
                    .map(name => caches.delete(name))
            );
        })
    );
});

self.addEventListener('fetch', event => {
    // Evitar cachear recursos externos
    if (!event.request.url.startsWith(self.location.origin)) {
        event.respondWith(fetch(event.request));
        return;
    }
    event.respondWith(
        caches.match(event.request).then(response => {
            return response || fetch(event.request);
        })
    );
});

self.addEventListener('sync', event => {
    if (event.tag === 'sync-forms') {
        event.waitUntil(syncForms());
    }
});

async function syncForms() {
    const pendingForms = JSON.parse(localStorage.getItem('pendingForms') || '[]');
    if (pendingForms.length === 0) return;

    let csrfToken = '';
    try {
        const response = await fetch('/csrf-token', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
            },
        });
        const data = await response.json();
        csrfToken = data.csrf_token;
    } catch (error) {
        console.error('Error al obtener el token CSRF:', error);
        return;
    }

    for (const formData of pendingForms) {
        try {
            const response = await fetch('/public-form', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(formData),
            });
            if (response.ok) {
                const updatedForms = pendingForms.filter(f => f !== formData);
                localStorage.setItem('pendingForms', JSON.stringify(updatedForms));
            }
        } catch (error) {
            console.error('Error al sincronizar formulario:', error);
        }
    }
}