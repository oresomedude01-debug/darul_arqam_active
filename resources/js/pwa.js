/**
 * PWA Registration & Install Handler
 * Manages service worker registration, app installation, and push notifications
 */

class PWAManager {
    constructor() {
        this.deferredPrompt = null;
        this.isInstalled = false;
        this.swRegistration = null;
        this.isOnline = navigator.onLine;
        
        this.init();
    }

    /**
     * Initialize PWA functionality
     */
    async init() {
        console.log('[PWA] Initializing PWA Manager...');
        
        // Register service worker
        this.registerServiceWorker();
        
        // Listen for install prompt
        this.setupInstallPrompt();
        
        // Check if app is already installed
        this.checkIfInstalled();
        
        // Handle online/offline status
        this.setupOnlineOfflineListeners();
        
        // Request notification permission
        this.setupPushNotifications();
    }

    /**
     * Register the service worker
     */
    async registerServiceWorker() {
        if (!('serviceWorker' in navigator)) {
            console.warn('[PWA] Service Workers not supported');
            return;
        }

        try {
            this.swRegistration = await navigator.serviceWorker.register('/service-worker.js', {
                scope: '/',
                updateViaCache: 'none'
            });

            console.log('[PWA] Service Worker registered successfully:', this.swRegistration);

            // Listen for service worker updates
            this.swRegistration.addEventListener('updatefound', () => {
                const newWorker = this.swRegistration.installing;
                console.log('[PWA] Service Worker update found');

                newWorker.addEventListener('statechange', () => {
                    if (newWorker.state === 'activated') {
                        console.log('[PWA] Service Worker updated');
                        this.notifyUpdate();
                    }
                });
            });

            // Periodically check for updates (every hour)
            setInterval(() => {
                this.swRegistration.update();
            }, 60 * 60 * 1000);

        } catch (error) {
            console.error('[PWA] Service Worker registration failed:', error);
        }
    }

    /**
     * Setup install prompt listener
     */
    setupInstallPrompt() {
        window.addEventListener('beforeinstallprompt', (event) => {
            console.log('[PWA] Install prompt available');
            
            // Prevent the mini-infobar from appearing
            event.preventDefault();
            
            // Store the event for later use
            this.deferredPrompt = event;
            
            // Show install button
            this.showInstallButton();
        });

        // Listen for app installation
        window.addEventListener('appinstalled', () => {
            console.log('[PWA] App installed successfully');
            this.onAppInstalled();
        });
    }

    /**
     * Show install button
     */
    showInstallButton() {
        const installBtn = document.getElementById('pwa-install-btn');
        if (!installBtn) return;

        installBtn.style.display = 'flex';
        
        installBtn.addEventListener('click', () => this.installApp());
    }

    /**
     * Handle app installation
     */
    async installApp() {
        if (!this.deferredPrompt) {
            console.warn('[PWA] Install prompt not available');
            return;
        }

        const installBtn = document.getElementById('pwa-install-btn');
        if (installBtn) {
            installBtn.disabled = true;
            installBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Installing...';
        }

        try {
            // Show the install prompt
            this.deferredPrompt.prompt();
            
            // Wait for user response
            const { outcome } = await this.deferredPrompt.userChoice;
            
            console.log('[PWA] User response:', outcome);
            
            // Reset the deferred prompt
            this.deferredPrompt = null;
            
            if (outcome === 'accepted') {
                this.onAppInstalled();
            } else {
                // User declined
                if (installBtn) {
                    installBtn.innerHTML = '<i class="fas fa-download mr-2"></i> Install App';
                    installBtn.disabled = false;
                }
            }
        } catch (error) {
            console.error('[PWA] Installation failed:', error);
            if (installBtn) {
                installBtn.innerHTML = '<i class="fas fa-download mr-2"></i> Install App';
                installBtn.disabled = false;
            }
        }
    }

    /**
     * Handle successful app installation
     */
    onAppInstalled() {
        console.log('[PWA] App installed');
        this.isInstalled = true;
        
        // Hide install button
        const installBtn = document.getElementById('pwa-install-btn');
        if (installBtn) {
            installBtn.style.display = 'none';
        }
        
        // Show success notification
        this.showNotification('App Installed', 'You can now access the app from your home screen!');
    }

    /**
     * Check if app is already installed
     */
    checkIfInstalled() {
        // Check if running in standalone mode (installed PWA)
        if (window.navigator.standalone === true) {
            this.isInstalled = true;
            console.log('[PWA] App is running in standalone mode (installed)');
        }
        
        // Check if running in PWA mode (display-mode: standalone)
        if (window.matchMedia('(display-mode: standalone)').matches) {
            this.isInstalled = true;
            console.log('[PWA] App is running in display-mode: standalone');
        }

        // Hide install button if already installed
        if (this.isInstalled) {
            const installBtn = document.getElementById('pwa-install-btn');
            if (installBtn) {
                installBtn.style.display = 'none';
            }
        }
    }

    /**
     * Setup online/offline listeners
     */
    setupOnlineOfflineListeners() {
        window.addEventListener('online', () => {
            console.log('[PWA] Online');
            this.isOnline = true;
            this.onOnlineStatusChanged(true);
        });

        window.addEventListener('offline', () => {
            console.log('[PWA] Offline');
            this.isOnline = false;
            this.onOnlineStatusChanged(false);
        });
    }

    /**
     * Handle online status change
     */
    onOnlineStatusChanged(isOnline) {
        // Dispatch custom event
        window.dispatchEvent(new CustomEvent('pwa:online-status-changed', {
            detail: { isOnline }
        }));

        if (!isOnline) {
            // Show offline indicator
            this.showOfflineIndicator();
        } else {
            // Hide offline indicator
            this.hideOfflineIndicator();
            
            // Trigger background sync
            if ('serviceWorker' in navigator && 'SyncManager' in window) {
                navigator.serviceWorker.ready.then(registration => {
                    registration.sync.register('sync-notifications').catch(err => {
                        console.warn('[PWA] Background sync registration failed:', err);
                    });
                });
            }
        }
    }

    /**
     * Setup push notifications
     */
    async setupPushNotifications() {
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
            console.warn('[PWA] Push notifications not supported');
            return;
        }

        try {
            // Wait for service worker to be ready
            const registration = await navigator.serviceWorker.ready;
            
            // Check existing subscription
            const subscription = await registration.pushManager.getSubscription();
            
            if (subscription) {
                console.log('[PWA] Already subscribed to push notifications');
            } else {
                console.log('[PWA] Ready to subscribe to push notifications');
            }
        } catch (error) {
            console.warn('[PWA] Push notification setup failed:', error);
        }
    }

    /**
     * Request notification permission
     */
    async requestNotificationPermission() {
        if (!('Notification' in window)) {
            console.warn('[PWA] Notifications not supported');
            return false;
        }

        if (Notification.permission === 'granted') {
            return true;
        }

        if (Notification.permission !== 'denied') {
            try {
                const permission = await Notification.requestPermission();
                return permission === 'granted';
            } catch (error) {
                console.error('[PWA] Failed to request notification permission:', error);
                return false;
            }
        }

        return false;
    }

    /**
     * Subscribe to push notifications
     */
    async subscribeToPushNotifications(vapidPublicKey) {
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
            console.warn('[PWA] Push notifications not supported');
            return null;
        }

        try {
            const registration = await navigator.serviceWorker.ready;
            
            // Check for existing subscription
            let subscription = await registration.pushManager.getSubscription();
            
            if (!subscription) {
                // Create new subscription
                subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: this.urlBase64ToUint8Array(vapidPublicKey)
                });
                
                console.log('[PWA] Subscribed to push notifications');
                
                // Send subscription to server
                await this.savePushSubscription(subscription);
            }
            
            return subscription;
        } catch (error) {
            console.error('[PWA] Push notification subscription failed:', error);
            return null;
        }
    }

    /**
     * Convert VAPID public key from base64
     */
    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/\-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }

        return outputArray;
    }

    /**
     * Save push subscription to server
     */
    async savePushSubscription(subscription) {
        try {
            const response = await fetch('/api/push-subscriptions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify(subscription)
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            console.log('[PWA] Push subscription saved to server');
        } catch (error) {
            console.error('[PWA] Failed to save push subscription:', error);
        }
    }

    /**
     * Show offline indicator
     */
    showOfflineIndicator() {
        let indicator = document.getElementById('pwa-offline-indicator');
        
        if (!indicator) {
            indicator = document.createElement('div');
            indicator.id = 'pwa-offline-indicator';
            indicator.className = 'fixed top-0 left-0 right-0 bg-yellow-500 text-white px-4 py-3 text-center z-50';
            indicator.innerHTML = '<i class="fas fa-wifi-slash mr-2"></i> You are currently offline. Some features may be limited.';
            document.body.appendChild(indicator);
        } else {
            indicator.style.display = 'block';
        }
    }

    /**
     * Hide offline indicator
     */
    hideOfflineIndicator() {
        const indicator = document.getElementById('pwa-offline-indicator');
        if (indicator) {
            indicator.style.display = 'none';
        }
    }

    /**
     * Show notification
     */
    showNotification(title, message) {
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification(title, {
                body: message,
                icon: '/images/icon-192x192.png'
            });
        }
    }

    /**
     * Notify user about service worker update
     */
    notifyUpdate() {
        const notification = document.createElement('div');
        notification.className = 'fixed bottom-4 right-4 bg-blue-600 text-white px-6 py-4 rounded-lg shadow-lg z-50 max-w-sm';
        notification.innerHTML = `
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h3 class="font-semibold">App Updated</h3>
                    <p class="text-sm text-blue-100">A new version is available</p>
                </div>
                <button onclick="location.reload()" class="px-4 py-1 bg-white text-blue-600 rounded hover:bg-blue-50 transition text-sm font-medium">
                    Reload
                </button>
            </div>
        `;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 8000);
    }
}

// Initialize PWA when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.pwaManager = new PWAManager();
    });
} else {
    window.pwaManager = new PWAManager();
}

// Export for external use
window.PWAManager = PWAManager;
