// Service Worker for ElectroZot PWA
const CACHE_NAME = 'electrozot-v3.5.0'; // UPDATED VERSION - FORCES CACHE REFRESH
const OFFLINE_URL = './offline.html';
const APP_VERSION = '3.5.0';

// DEVELOPMENT MODE - Set to true during development to disable caching
const DEV_MODE = false; // Change to false for production

// Files to cache for offline functionality (using relative paths)
const CACHE_URLS = [
  './offline.html',
  './splash.html',
  './splash-icon.png',
  './manifest.json',
  './vendor/bootstrap/css/bootstrap.min.css',
  './vendor/jquery/jquery.min.js',
  './vendor/bootstrap/js/bootstrap.bundle.min.js',
  './css/modern-business.css',
  './vendor/css/custom.css',
  './usr/vendor/fontawesome-free/css/all.min.css'
];

// URLs that should NEVER be cached (always fetch fresh from network)
const NEVER_CACHE = [
  'index.php',
  'admin/',
  'debug-popular.php',
  'check-popular-services.php',
  'about.php',
  'contact.php',
  'services.php',
  'gallery.php',
  'process-guest-booking.php',
  'test-',
  'fix-',
  'check-',
  '.php' // All PHP files in dev mode
];

// Install event - cache essential files
self.addEventListener('install', (event) => {
  console.log(`[Service Worker] Installing v${APP_VERSION}...`);
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('[Service Worker] Caching essential files');
        return cache.addAll(CACHE_URLS);
      })
      .then(() => {
        console.log('[Service Worker] Installation complete');
        return self.skipWaiting();
      })
  );
});

// Listen for skip waiting message
self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    console.log('[Service Worker] Skipping waiting...');
    self.skipWaiting();
  }
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
  console.log('[Service Worker] Activating...');
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheName !== CACHE_NAME) {
            console.log('[Service Worker] Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', (event) => {
  // Skip non-GET requests
  if (event.request.method !== 'GET') {
    return;
  }

  // Skip chrome extensions and other protocols
  if (!event.request.url.startsWith('http')) {
    return;
  }

  event.respondWith(
    (async () => {
      // IN DEVELOPMENT MODE: Always fetch fresh from network
      if (DEV_MODE) {
        console.log('[Service Worker] DEV MODE: Fetching fresh from network:', event.request.url);
        try {
          const response = await fetch(event.request);
          return response;
        } catch (error) {
          // Only use cache as fallback in dev mode
          const cachedResponse = await caches.match(event.request);
          if (cachedResponse) {
            return cachedResponse;
          }
          return caches.match(OFFLINE_URL);
        }
      }

      // PRODUCTION MODE: Normal caching strategy
      // Check if this URL should never be cached
      const url = new URL(event.request.url);
      const shouldNeverCache = NEVER_CACHE.some(path => url.pathname.includes(path));
      
      if (shouldNeverCache) {
        // Always fetch fresh from network for these URLs
        try {
          return await fetch(event.request);
        } catch (error) {
          return caches.match(OFFLINE_URL);
        }
      }
      
      // For other URLs, try cache first
      const cachedResponse = await caches.match(event.request);
      if (cachedResponse) {
        return cachedResponse;
      }

      // Otherwise fetch from network
      return fetch(event.request)
          .then((response) => {
            // Don't cache if not a valid response
            if (!response || response.status !== 200 || response.type !== 'basic') {
              return response;
            }

            // Clone the response
            const responseToCache = response.clone();

            // Cache the fetched response for future use
            caches.open(CACHE_NAME)
              .then((cache) => {
                cache.put(event.request, responseToCache);
              });

            return response;
          })
          .catch(() => {
            // If both cache and network fail, show offline page
            return caches.match(OFFLINE_URL);
          });
    })()
  );
});

// Background sync for offline bookings (future enhancement)
self.addEventListener('sync', (event) => {
  if (event.tag === 'sync-bookings') {
    event.waitUntil(syncBookings());
  }
});

// Push notification support (future enhancement)
self.addEventListener('push', (event) => {
  const options = {
    body: event.data ? event.data.text() : 'New notification from ElectroZot',
    icon: '/vendor/img/icons/icon-192x192.png',
    badge: '/vendor/img/icons/icon-72x72.png',
    vibrate: [200, 100, 200],
    data: {
      dateOfArrival: Date.now(),
      primaryKey: 1
    },
    actions: [
      {
        action: 'view',
        title: 'View',
        icon: '/vendor/img/icons/icon-96x96.png'
      },
      {
        action: 'close',
        title: 'Close',
        icon: '/vendor/img/icons/icon-96x96.png'
      }
    ]
  };

  event.waitUntil(
    self.registration.showNotification('ElectroZot', options)
  );
});

// Notification click handler
self.addEventListener('notificationclick', (event) => {
  event.notification.close();

  if (event.action === 'view') {
    event.waitUntil(
      clients.openWindow('/')
    );
  }
});

// Helper function for background sync (placeholder)
async function syncBookings() {
  // This would sync offline bookings when connection is restored
  console.log('[Service Worker] Syncing bookings...');
  return Promise.resolve();
}
