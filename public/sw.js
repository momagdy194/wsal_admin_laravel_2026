/**
 * Minimal service worker for PWA: offline fallback and caching of static assets.
 * Scope: same-origin. Register from your app (e.g. app.blade.php or app.js).
 */
const CACHE_NAME = 'admin-panel-v1';

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll([
        '/',
        '/manifest.json'
      ]).catch(() => {});
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.filter((k) => k !== CACHE_NAME).map((k) => caches.delete(k))
      );
    })
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  if (event.request.mode !== 'navigate') return;
  event.respondWith(
    fetch(event.request).catch(() => {
      return caches.match(event.request).then((cached) => {
        return cached || caches.match('/').then((r) => r || new Response('Offline', { status: 503, statusText: 'Service Unavailable' }));
      });
    })
  );
});
