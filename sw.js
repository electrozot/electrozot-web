// Service Worker for ElectroZot PWA
const CACHE_NAME = 'electrozot-v3.9.0'; // UPDATED VERSION - FORCES CACHE REFRESH
const OFFLINE_URL = './offline.html';
const APP_VERSION = '3.9.0';

// Service Worker for ElectroZot PWA

// Immediate self-test
self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'SW_TEST') {
    event.ports[0].postMessage({
      type: 'SW_TEST_RESPONSE',
      version: APP_VERSION,
      status: 'active'
    });
  }
});

// DEVELOPMENT MODE - Set to true during development to disable caching
const DEV_MODE = false; // Change to false for production

// Files to cache for offline functionality (using relative paths)
const CACHE_URLS = [
  './splash.html',
  './vendor/EZlogonew.png',
  './offline.html',
  './vendor/img/icons/icon-192x192.png',
  './vendor/img/icons/icon-512x512.png',
  './manifest.json'
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
  console.log('SW: Install event triggered');
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(async (cache) => {
        console.log('SW: Caching files...');
        // Cache files individually to avoid failing on missing files
        const cachePromises = CACHE_URLS.map(async (url) => {
          try {
            await cache.add(url);
            console.log('SW: Cached:', url);
          } catch (error) {
            console.warn('SW: Failed to cache:', url, error);
          }
        });
        await Promise.allSettled(cachePromises);
        console.log('SW: Caching completed');
        return self.skipWaiting();
      })
      .catch((error) => {
        console.error('SW: Install failed:', error);
        // Don't throw error, allow SW to install even if caching fails
        return self.skipWaiting();
      })
  );
});

// Listen for skip waiting message
self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'SKIP_WAITING') {

    self.skipWaiting();
  }
  
  // Handle install trigger from main thread
  if (event.data && event.data.type === 'TRIGGER_INSTALL') {

    // Notify all clients that install is ready
    self.clients.matchAll().then(clients => {
      clients.forEach(client => {
        client.postMessage({ type: 'INSTALL_READY' });
      });
    });
  }
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
  console.log('SW: Activate event triggered');
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      console.log('SW: Cleaning old caches...');
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheName !== CACHE_NAME) {
            console.log('SW: Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    }).then(() => {
      console.log('SW: Taking control of all clients');
      return self.clients.claim();
    }).catch((error) => {
      console.error('SW: Activate failed:', error);
      throw error;
    })
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
    icon: './vendor/img/icons/icon-192x192.png',
    badge: './vendor/img/icons/icon-192x192.png',
    vibrate: [200, 100, 200],
    data: {
      dateOfArrival: Date.now(),
      primaryKey: 1
    },
    actions: [
      {
        action: 'view',
        title: 'View',
        icon: './vendor/img/icons/icon-192x192.png'
      },
      {
        action: 'close',
        title: 'Close',
        icon: './vendor/img/icons/icon-192x192.png'
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

  return Promise.resolve();
}
