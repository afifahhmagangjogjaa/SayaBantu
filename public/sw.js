const CACHE_NAME = 'sayabantu-v2';
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

// Fetch Event
self.addEventListener('fetch', (event) => {
  // Hanya proses GET request
  if (event.request.method !== 'GET') return;

  const url = new URL(event.request.url);

  // PENTING: Jangan interupsi atau cache halaman navigasi dinamis / Livewire
  // agar perpindahan halaman Livewire (wire:navigate) instan & tidak konflik dengan cache lama.
  if (
    event.request.mode === 'navigate' ||
    event.request.headers.get('X-Livewire-Navigate') ||
    url.pathname.startsWith('/customer') ||
    url.pathname.startsWith('/mitra') ||
    url.pathname.startsWith('/admin') ||
    url.pathname.startsWith('/superadmin') ||
    url.pathname.startsWith('/livewire') ||
    url.pathname.startsWith('/api') ||
    url.pathname.startsWith('/chat') ||
    url.pathname.startsWith('/profile') ||
    url.pathname.startsWith('/settings')
  ) {
    return; // Browser & Livewire menangani langsung via jaringan secara native
  }

  // Untuk aset statis (gambar, icon, css build, js build), gunakan cache-first fallback network
  event.respondWith(
    caches.match(event.request).then((cachedResponse) => {
      if (cachedResponse) {
        return cachedResponse;
      }
      return fetch(event.request).then((networkResponse) => {
        if (networkResponse && networkResponse.status === 200 && networkResponse.type === 'basic') {
          const responseToCache = networkResponse.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, responseToCache);
          });
        }
        return networkResponse;
      });
    }).catch(() => {
      return caches.match('/');
    })
  );
});
