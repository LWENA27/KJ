const CACHE_NAME = 'kj-healthcare-v1.0';
const urlsToCache = [
    '/KJ/',
    '/KJ/views/layouts/main.php',
    '/KJ/assets/css/style.css',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css',
    'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap'
];

// Install event - cache resources
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(urlsToCache))
    );
});

// Fetch event - serve from cache when offline
self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                // Return cached version or fetch from network
                return response || fetch(event.request);
            })
            .catch(() => {
                // Return offline page for navigation requests
                if (event.request.mode === 'navigate') {
                    return caches.match('/KJ/offline.html');
                }
            })
    );
});

// Activate event - clean up old caches
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
});

// Background sync for offline data
self.addEventListener('sync', event => {
    if (event.tag === 'background-sync') {
        event.waitUntil(
            // Sync offline data when connection is restored
            syncOfflineData()
        );
    }
});

// Push notifications
self.addEventListener('push', event => {
    const options = {
        body: event.data ? event.data.text() : 'New healthcare notification',
        icon: '/KJ/assets/icons/icon-192x192.png',
        badge: '/KJ/assets/icons/badge-72x72.png',
        vibrate: [100, 50, 100],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: '2'
        },
        actions: [
            {
                action: 'explore',
                title: 'View Details',
                icon: '/KJ/assets/icons/checkmark.png'
            },
            {
                action: 'close',
                title: 'Close notification',
                icon: '/KJ/assets/icons/xmark.png'
            }
        ]
    };

    event.waitUntil(
        self.registration.showNotification('KJ Healthcare', options)
    );
});

// Notification click handler
self.addEventListener('notificationclick', event => {
    event.notification.close();

    if (event.action === 'explore') {
        event.waitUntil(
            clients.openWindow('/KJ/')
        );
    }
});

async function syncOfflineData() {
    // Implement offline data synchronization
    try {
        const offlineData = await getOfflineData();
        if (offlineData.length > 0) {
            await sendOfflineDataToServer(offlineData);
            await clearOfflineData();
        }
    } catch (error) {
        console.error('Failed to sync offline data:', error);
    }
}

async function getOfflineData() {
    // Get data stored offline
    return [];
}

async function sendOfflineDataToServer(data) {
    // Send offline data to server
    return fetch('/KJ/api/sync-offline-data', {
        method: 'POST',
        body: JSON.stringify(data),
        headers: {
            'Content-Type': 'application/json'
        }
    });
}

async function clearOfflineData() {
    // Clear offline data after successful sync
}
