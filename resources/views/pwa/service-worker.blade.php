const CACHE_NAME = @json($cacheName);
const OFFLINE_URL = @json($offlineUrl);
const SHELL_ASSETS = [
    OFFLINE_URL,
    @json($manifestUrl),
    @json($icon192Url),
];
const NETWORK_TIMEOUT_MS = 4500;

const fetchWithTimeout = (request) => Promise.race([
    fetch(request),
    new Promise((_, reject) => setTimeout(() => reject(new Error('Network timeout')), NETWORK_TIMEOUT_MS)),
]);

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(SHELL_ASSETS)).then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((key) => key !== CACHE_NAME)
                    .map((key) => caches.delete(key))
            )
        ).then(() => self.clients.claim())
    );
});

self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

self.addEventListener('push', (event) => {
    let payload = {};

    try {
        payload = event.data ? event.data.json() : {};
    } catch (error) {
        payload = { body: event.data ? event.data.text() : '' };
    }

    event.waitUntil(
        self.registration.showNotification(payload.title || 'Order update', {
            body: payload.body || 'There is an update waiting for you.',
            icon: payload.icon || @json($icon192Url),
            badge: payload.badge || @json($icon192Url),
            tag: payload.tag || 'store-notification',
            data: { url: payload.url || self.location.origin },
            vibrate: [100, 50, 100],
        })
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const targetUrl = event.notification.data?.url || self.location.origin;

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((windows) => {
            const matchingWindow = windows.find((windowClient) => windowClient.url === targetUrl);

            if (matchingWindow) {
                return matchingWindow.focus();
            }

            return clients.openWindow(targetUrl);
        })
    );
});

self.addEventListener('fetch', (event) => {
    const { request } = event;

    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    if (url.origin !== self.location.origin) {
        return;
    }

    if (request.mode === 'navigate') {
        event.respondWith(
            fetchWithTimeout(request)
                .then((response) => response.ok ? response : Promise.reject(new Error('Navigation failed')))
                .catch(() => caches.match(OFFLINE_URL))
        );

        return;
    }

    if (
        url.pathname.startsWith('/build/') ||
        url.pathname.startsWith('/images/') ||
        url.pathname.startsWith('/pwa/') ||
        url.pathname === '/favicon.ico'
    ) {
        event.respondWith(caches.open(CACHE_NAME).then(async (cache) => {
            const cached = await cache.match(request);
            const fresh = fetch(request).then((response) => {
                if (response.ok) {
                    const copy = response.clone();
                    cache.put(request, copy);
                }

                return response;
            }).catch(() => cached);

            return cached || fresh;
        }));
    }
});
