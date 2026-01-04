const CACHE_NAME = 'quran-tracker-v2';
const ASSETS_TO_CACHE = [
    // We can't cache everything dynamic easily in this simple setup
    // Cache specific offline fallback page if we had one
    // For now, let's just cache the basics to enable PWA installability criteria
    './',
    './index.php',
    'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap',
    'https://fonts.googleapis.com/icon?family=Material+Icons+Round'
];

// Install Event
self.addEventListener('install', (event) => {
    // console.log('Service Worker: Installing...');
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            // console.log('Service Worker: Caching Files');
            // return cache.addAll(ASSETS_TO_CACHE); // Optional: disabled for dev to avoid staleness
            return Promise.resolve();
        })
    );
});

// Activate Event
self.addEventListener('activate', (event) => {
    // console.log('Service Worker: Activated');
    // Remove old caches
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cache) => {
                    if (cache !== CACHE_NAME) {
                        // console.log('Service Worker: Clearing Old Cache');
                        return caches.delete(cache);
                    }
                })
            );
        })
    );
});

// Fetch Event (Network First Strategy for dynamic content)
self.addEventListener('fetch', (event) => {
    // For dynamic PHP app, usually Network Only or Network First is safest
    // We don't want to serve stale data for progress updates
    event.respondWith(
        fetch(event.request).catch(() => {
            return caches.match(event.request);
        })
    );
});

// Listen for messages from client (if needed)
self.addEventListener('message', (event) => {
    // Handle messages
});
