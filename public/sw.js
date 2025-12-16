const CACHE_NAME = 'syamail-v1';
const urlsToCache = [
  '/',
  '/offline',
  '/css/app.css',
  '/workbox-239d0d27.js',
  '/icon-192.png',
  '/icon-512.png',
  '/manifest.json',
  'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap'
];

// Install event - cache files
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(async cache => {
        console.log('Cache opened');
        // Add items one-by-one and tolerate failures so install doesn't fail entirely
        await Promise.all(urlsToCache.map(async (u) => {
          // Skip invalid URLs or external resources that might fail
          if (!u.startsWith('http') || u.startsWith(window.location.origin)) {
            try {
              await cache.add(u);
            } catch (err) {
              console.warn('Failed to cache', u, err && err.message ? err.message : err);
            }
          }
        }));
      })
  );
  self.skipWaiting();
});

// Activate event - clean old caches
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  self.clients.claim();
});

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        // Cache hit - return response
        if (response) {
          return response;
        }

        return fetch(event.request).then(
          response => {
            // Check if we received a valid response
            if (!response || response.status !== 200 || response.type !== 'basic') {
              return response;
            }

            // Clone the response
            const responseToCache = response.clone();

            caches.open(CACHE_NAME)
              .then(cache => {
                // Don't cache if the URL has certain patterns
                const url = new URL(event.request.url);
                if (url.protocol.startsWith('http') &&
                    !event.request.url.includes('/api/') &&
                    !event.request.url.includes('/admin/') &&
                    !event.request.url.includes('/auth/login') &&
                    event.request.method === 'GET' &&
                    !url.protocol.startsWith('chrome-extension')) {
                 cache.put(event.request, responseToCache);
               }
              });

            return response;
          }
        ).catch(() => {
          // Network failed, try to serve offline page
          if (event.request.mode === 'navigate') {
            return caches.match('/offline');
          }
        });
      })
  );
});