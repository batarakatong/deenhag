const CACHE_NAME = 'greenprinting-v2';
const CORE_ASSETS = [
  '/',
  '/offline.html',
  '/manifest.webmanifest',
  '/icons/icon.svg',
  '/icons/splash.svg'
];

self.addEventListener('install', event => {
  event.waitUntil(caches.open(CACHE_NAME).then(cache => cache.addAll(CORE_ASSETS)));
  self.skipWaiting();
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys => Promise.all(keys.filter(key => key !== CACHE_NAME).map(key => caches.delete(key))))
  );
  self.clients.claim();
});

self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') return;
  const url = new URL(event.request.url);
  const dynamicPrefixes = ['/login', '/logout', '/register', '/forgot-password', '/reset-password', '/two-factor-challenge', '/admin', '/account', '/orders', '/checkout', '/cart'];
  const isDynamic = url.origin === self.location.origin && dynamicPrefixes.some(prefix => url.pathname.startsWith(prefix));

  if (event.request.mode === 'navigate' || isDynamic) {
    event.respondWith(
      fetch(event.request)
        .catch(() => caches.match(event.request).then(cached => cached || caches.match('/offline.html')))
    );
    return;
  }

  event.respondWith(
    caches.match(event.request).then(cached => {
      if (cached) return cached;

      return fetch(event.request)
        .then(response => {
          const clone = response.clone();
          if (response.ok && event.request.url.startsWith(self.location.origin)) {
            caches.open(CACHE_NAME).then(cache => cache.put(event.request, clone));
          }
          return response;
        })
        .catch(() => {
          if (event.request.mode === 'navigate') {
            return caches.match('/offline.html');
          }
        });
    })
  );
});
