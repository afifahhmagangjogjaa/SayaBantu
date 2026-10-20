const CACHE_NAME = 'sayabantu-v1';
const ASSETS_TO_CACHE = [
  '/',
  '/manifest.json',
  '/icons/icon-192x192.png',
  '/icons/icon-512x512.png',
  '/icons/icon.svg'
];

// Install Event
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(ASSETS_TO_CACHE);
    }).then(() => self.skipWaiting())
  );
});

// Activate Event
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.map((key) => {
          if (key !== CACHE_NAME) {
            return caches.delete(key);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});

// Fetch Event (Network-First strategy: selalu prioritaskan update server terbaru)
self.addEventListener('fetch', (event) => {
  // Hanya proses GET request untuk navigasi dan aset statis
  if (event.request.method !== 'GET') return;

  event.respondWith(
    fetch(event.request)
      .then((networkResponse) => {
        // Jika respons valid, update cache di background
        if (networkResponse && networkResponse.status === 200 && networkResponse.type === 'basic') {
          const responseToCache = networkResponse.clone();
          caches.open(CACHE_NAME).then((cache) => {
            // Hindari caching request dinamis seperti livewire updates
            const url = event.request.url;
            if (!url.includes('/livewire/') && !url.includes('/admin/')) {
              cache.put(event.request, responseToCache);
            }
          });
        }
        return networkResponse;
      })
      .catch(() => {
        // Jika offline atau jaringan terputus, gunakan fallback cache
        return caches.match(event.request);
      })
  );
});
