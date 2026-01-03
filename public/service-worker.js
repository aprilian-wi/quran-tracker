const CACHE_NAME = 'quran-tracker-v1';
const ASSETS_TO_CACHE = [
    './index.php?page=dashboard',
    './assets/css/style.css',
    './assets/js/script.js',
    './assets/logo_pwa.png',
    './offline.html',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
    'https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Tajawal:wght@400;500;700&display=swap'
];

// Install Event: Cache assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('[Service Worker] Caching all: app shell and content');
            return cache.addAll(ASSETS_TO_CACHE);
        })
    );
});

// Activate Event: Clean up old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keyList) => {
            return Promise.all(keyList.map((key) => {
                if (key !== CACHE_NAME) {
                    console.log('[Service Worker] Removing old cache.', key);
                    return caches.delete(key);
                }
            }));
        })
    );
    return self.clients.claim();
});

// Fetch Event: Network First, falling back to cache, then offline page
self.addEventListener('fetch', (event) => {
    // Skip cross-origin requests like Google Fonts/Bootstrap CDN for simple offline handling logic here
    // But we DO cache them in install, so we should try to serve them from cache if network fails.

    event.respondWith(
        fetch(event.request)
            .catch(() => {
                return caches.match(event.request).then((response) => {
                    if (response) {
                        return response;
                    } else if (event.request.mode === 'navigate') {
                        // If it's a navigation request (HTML page) and network failed + not in cache
                        return caches.match('./offline.html');
                    }
                });
            })
    );
});
