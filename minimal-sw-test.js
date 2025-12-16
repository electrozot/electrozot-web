// Minimal Service Worker for Testing
const CACHE_NAME = 'test-sw-v1';

// Install event - minimal caching
self.addEventListener('install', (event) => {
  console.log('Test SW: Install event');
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(() => {
        console.log('Test SW: Cache opened');
        return self.skipWaiting();
      })
  );
});

// Activate event - take control immediately
self.addEventListener('activate', (event) => {
  console.log('Test SW: Activate event');
  event.waitUntil(
    self.clients.claim().then(() => {
      console.log('Test SW: Clients claimed');
    })
  );
});

// Fetch event - minimal handling
self.addEventListener('fetch', (event) => {
  // Just pass through all requests
  event.respondWith(fetch(event.request));
});

// Message handling for testing
self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'TEST_SW') {
    event.ports[0].postMessage({
      type: 'SW_RESPONSE',
      status: 'active',
      message: 'Test service worker is running'
    });
  }
});

console.log('Test SW: Script loaded');