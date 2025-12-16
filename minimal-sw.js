const CACHE_NAME = 'minimal-pwa-v1';

self.addEventListener('install', (event) => {
  console.log('SW: Install event');
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  console.log('SW: Activate event');
  event.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', (event) => {
  // Simple pass-through for now
  event.respondWith(fetch(event.request));
});