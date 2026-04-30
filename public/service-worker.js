/**
 * Darul Arqam PWA Service Worker
 * Implements intelligent caching strategies and offline support
 */

const CACHE_VERSION = 'v1';
const STATIC_CACHE = `static-${CACHE_VERSION}`;
const DYNAMIC_CACHE = `dynamic-${CACHE_VERSION}`;
const IMAGE_CACHE = `images-${CACHE_VERSION}`;
const API_CACHE = `api-${CACHE_VERSION}`;

// Static assets to cache on install
const STATIC_ASSETS = [
    '/',
    '/offline',
    '/css/app.css',
    '/js/app.js',
    '/js/animations.js',
    '/images/icon-192x192.png',
    '/images/icon-512x512.png',
    'https://cdn.tailwindcss.com',
    'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
    'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js'
];

// Routes that should be network-first (dynamic data)
const NETWORK_FIRST_ROUTES = [
    '/api/',
    '/students',
    '/teachers',
    '/dashboard',
    '/admin',
    '/student-portal',
    '/results',
    '/attendance',
    '/grades',
    '/blog' // Blog API should be network-first
];

// Routes for cache-first strategy (static pages)
const CACHE_FIRST_ROUTES = [
    '/about',
    '/help',
    '/privacy',
    '/terms'
];

// Blog-specific caching configuration
const BLOG_CACHE_CONFIG = {
    cache: 'blog-content-v1',
    strategy: 'stale-while-revalidate', // Serve stale content while fetching fresh
    ttl: 3600, // 1 hour in seconds
    routes: [
        '/blog',
        '/blog/*'
    ]
};

// ========================================
// INSTALL EVENT - Cache static assets
// ========================================
self.addEventListener('install', event => {
    console.log('[Service Worker] Installing...');
    
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then(cache => {
                console.log('[Service Worker] Caching static assets...');
                return cache.addAll(STATIC_ASSETS).catch(err => {
                    console.warn('[Service Worker] Some assets failed to cache:', err);
                    // Don't fail install if some optional assets can't be cached
                });
            })
            .then(() => self.skipWaiting())
    );
});

// ========================================
// ACTIVATE EVENT - Clean up old caches
// ========================================
self.addEventListener('activate', event => {
    console.log('[Service Worker] Activating...');
    
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    // Delete old cache versions
                    if (cacheName !== STATIC_CACHE && 
                        cacheName !== DYNAMIC_CACHE && 
                        cacheName !== IMAGE_CACHE && 
                        cacheName !== API_CACHE) {
                        console.log('[Service Worker] Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// ========================================
// FETCH EVENT - Smart caching strategies
// ========================================
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip cross-origin requests, WebSocket, etc.
    if (url.origin !== location.origin) {
        return;
    }

    // Determine caching strategy based on request type and route
    if (request.method !== 'GET') {
        // Don't cache non-GET requests (POST, PUT, DELETE, etc.)
        event.respondWith(
            fetch(request)
                .catch(() => {
                    // Return offline error response for failed non-GET requests
                    return createOfflineResponse();
                })
        );
        return;
    }

    // Strategy 1: Stale-while-revalidate for blog content (fresher than cache-first, better UX than network-first)
    if (isBlogRoute(url.pathname)) {
        event.respondWith(staleWhileRevalidateStrategy(request, BLOG_CACHE_CONFIG.cache));
        return;
    }

    // Strategy 2: Cache-first for images
    if (isImageRequest(request)) {
        event.respondWith(cacheFirstStrategy(request, IMAGE_CACHE));
        return;
    }

    // Strategy 3: Cache-first for static pages
    if (isCacheFirstRoute(url.pathname)) {
        event.respondWith(cacheFirstStrategy(request, STATIC_CACHE));
        return;
    }

    // Strategy 4: Network-first for API and dynamic data
    if (isNetworkFirstRoute(url.pathname)) {
        event.respondWith(networkFirstStrategy(request, DYNAMIC_CACHE));
        return;
    }

    // Strategy 5: Network-first with cache fallback for HTML pages
    if (request.headers.get('accept')?.includes('text/html')) {
        event.respondWith(networkFirstStrategy(request, DYNAMIC_CACHE));
        return;
    }

    // Default: Stale-while-revalidate for other assets
    event.respondWith(staleWhileRevalidateStrategy(request, DYNAMIC_CACHE));
});

// ========================================
// BACKGROUND SYNC - Sync failed requests
// ========================================
self.addEventListener('sync', event => {
    console.log('[Service Worker] Background sync:', event.tag);
    
    if (event.tag === 'sync-notifications') {
        event.waitUntil(syncNotifications());
    }
});

// ========================================
// PUSH NOTIFICATIONS
// ========================================
self.addEventListener('push', event => {
    console.log('[Service Worker] Push received:', event);
    
    if (!event.data) return;

    try {
        const data = event.data.json();
        const options = {
            body: data.body || 'New notification',
            icon: '/images/icon-192x192.png',
            badge: '/images/icon-96x96.png',
            tag: data.tag || 'notification',
            requireInteraction: data.requireInteraction || false,
            actions: data.actions || [
                { action: 'open', title: 'Open' },
                { action: 'close', title: 'Close' }
            ],
            data: data.data || {}
        };

        event.waitUntil(
            self.registration.showNotification(data.title || 'Darul Arqam', options)
        );
    } catch (error) {
        console.error('[Service Worker] Error parsing push data:', error);
        event.waitUntil(
            self.registration.showNotification('Darul Arqam', {
                body: event.data.text(),
                icon: '/images/icon-192x192.png'
            })
        );
    }
});

// ========================================
// NOTIFICATION CLICK
// ========================================
self.addEventListener('notificationclick', event => {
    console.log('[Service Worker] Notification clicked:', event.action);
    
    event.notification.close();

    if (event.action === 'close') return;

    const urlToOpen = event.notification.data.url || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(clientList => {
                // Check if window is already open
                for (const client of clientList) {
                    if (client.url === urlToOpen && 'focus' in client) {
                        return client.focus();
                    }
                }
                // Open new window if not found
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});

// ========================================
// CACHING STRATEGIES
// ========================================

/**
 * Cache-first strategy: Use cache, fall back to network
 */
async function cacheFirstStrategy(request, cacheName) {
    try {
        const cached = await caches.match(request);
        if (cached) {
            console.log('[Service Worker] Cache hit:', request.url);
            return cached;
        }

        const response = await fetch(request);
        
        // Cache successful responses
        if (response && response.status === 200) {
            const cache = await caches.open(cacheName);
            cache.put(request, response.clone());
        }
        
        return response;
    } catch (error) {
        console.warn('[Service Worker] Cache-first failed:', error);
        return createOfflineResponse();
    }
}

/**
 * Network-first strategy: Try network, fall back to cache
 */
async function networkFirstStrategy(request, cacheName) {
    try {
        const response = await fetch(request);
        
        // Cache successful responses
        if (response && response.status === 200) {
            const cache = await caches.open(cacheName);
            cache.put(request, response.clone());
        }
        
        return response;
    } catch (error) {
        console.warn('[Service Worker] Network failed, using cache:', request.url);
        
        const cached = await caches.match(request);
        if (cached) {
            return cached;
        }
        
        return createOfflineResponse();
    }
}

/**
 * Stale-while-revalidate: Return cache immediately, update in background
 */
async function staleWhileRevalidateStrategy(request, cacheName) {
    const cached = await caches.match(request);
    
    const fetchPromise = fetch(request).then(response => {
        if (response && response.status === 200) {
            const cache = caches.open(cacheName);
            cache.then(c => c.put(request, response.clone()));
        }
        return response;
    }).catch(() => createOfflineResponse());

    return cached || fetchPromise;
}

// ========================================
// HELPER FUNCTIONS
// ========================================

/**
 * Check if request is for an image
 */
function isImageRequest(request) {
    return request.headers.get('accept')?.includes('image') ||
           request.url.match(/\.(png|jpg|jpeg|gif|webp|svg)$/i);
}

/**
 * Check if route should use cache-first strategy
 */
function isCacheFirstRoute(pathname) {
    return CACHE_FIRST_ROUTES.some(route => pathname.startsWith(route));
}

/**
 * Check if route should use network-first strategy
 */
function isNetworkFirstRoute(pathname) {
    return NETWORK_FIRST_ROUTES.some(route => pathname.startsWith(route));
}

/**
 * Check if route is blog-related (for stale-while-revalidate strategy)
 * This ensures blog content is fresh while maintaining excellent offline experience
 */
function isBlogRoute(pathname) {
    return BLOG_CACHE_CONFIG.routes.some(route => {
        if (route.endsWith('*')) {
            return pathname.startsWith(route.slice(0, -1));
        }
        return pathname === route || pathname.startsWith(route + '/');
    });
}

/**
 * Create offline response page
 */
function createOfflineResponse() {
    return new Response(
        `<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Offline - Darul Arqam</title>
            <script src="https://cdn.tailwindcss.com"></script>
        </head>
        <body class="bg-gray-50">
            <div class="min-h-screen flex items-center justify-center px-4">
                <div class="text-center max-w-md">
                    <div class="mb-6">
                        <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16H5m13 0h3m-11-4V8m0 0H5m3 0h9m11 0a2 2 0 01-2 2H5a2 2 0 01-2-2m14-4V5m0 0H5m3 0h9V5m11 0a2 2 0 00-2-2H5a2 2 0 00-2 2"></path>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">You're Offline</h1>
                    <p class="text-gray-600 mb-6">It looks like you've lost your internet connection. Some features may not be available right now.</p>
                    <p class="text-sm text-gray-500">We've cached some pages for you. Please check your connection and try again.</p>
                    <button onclick="location.reload()" class="mt-6 px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                        Retry
                    </button>
                </div>
            </div>
        </body>
        </html>`,
        { 
            status: 503, 
            statusText: 'Service Unavailable',
            headers: { 'Content-Type': 'text/html' }
        }
    );
}

/**
 * Sync notifications from server
 */
async function syncNotifications() {
    try {
        const response = await fetch('/api/notifications/pending');
        const notifications = await response.json();
        
        notifications.forEach(notif => {
            self.registration.showNotification(notif.title, {
                body: notif.body,
                icon: '/images/icon-192x192.png',
                tag: notif.id
            });
        });
    } catch (error) {
        console.error('[Service Worker] Error syncing notifications:', error);
    }
}
