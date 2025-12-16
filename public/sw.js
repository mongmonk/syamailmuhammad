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
          try {
            const url = new URL(u, self.location);
            
            // Cache both same-origin and essential external resources
            if (url.origin === self.location.origin ||
                u.includes('fonts.googleapis.com')) {
              await cache.add(u);
            } else {
              console.log('Skipping non-essential external resource:', u);
            }
          } catch (err) {
            console.warn('Failed to cache', u, err.message);
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
                
                // Cache criteria:
                // 1. Same-origin OR essential external resources
                // 2. GET requests only
                // 3. Exclude sensitive paths
                const isCacheable = (
                  (url.origin === self.location.origin ||
                   url.href.includes('fonts.googleapis.com')) &&
                  event.request.method === 'GET' &&
                  !url.pathname.startsWith('/api/') &&
                  !url.pathname.startsWith('/admin/') &&
                  !url.pathname.startsWith('/auth/login') &&
                  !url.protocol.startsWith('chrome-extension')
                );
                
                if (isCacheable) {
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