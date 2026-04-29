/**
 * Unified PWA Manager v2
 * Handles PWA installation across all pages
 * Fixes beforeinstallprompt event capture and display
 */

class UnifiedPWAManager {
    constructor() {
        this.deferredPrompt = null;
        this.isInstalled = this.checkIfInstalled();
        this.platform = this.detectPlatform();
        this.promptListenerAdded = false;
        this.debug = true;
        
        console.log('[PWA] UnifiedPWAManager initialized', {
            isInstalled: this.isInstalled,
            platform: this.platform
        });
        
        this.init();
    }

    /**
     * Initialize PWA manager
     */
    init() {
        // Set up install prompt listener EARLY and GLOBALLY
        this.setupBeforeInstallPrompt();
        
        // Set up app installed listener
        this.setupAppInstalledListener();
        
        // Try to show install buttons after a short delay
        setTimeout(() => {
            this.showInstallButtons();
        }, 500);
        
        // DOM ready setup
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.onDOMReady());
        } else {
            this.onDOMReady();
        }
    }

    /**
     * Setup beforeinstallprompt listener ASAP
     */
    setupBeforeInstallPrompt() {
        if (this.promptListenerAdded) return;
        
        window.addEventListener('beforeinstallprompt', (event) => {
            event.preventDefault();
            this.deferredPrompt = event;
            this.log('beforeinstallprompt captured', event);
            this.showInstallButtons();
        });
        
        this.promptListenerAdded = true;
        this.log('beforeinstallprompt listener registered');
    }

    /**
     * Setup app installed listener
     */
    setupAppInstalledListener() {
        window.addEventListener('appinstalled', () => {
            this.log('App installed successfully');
            this.isInstalled = true;
            this.hideInstallButtons();
            this.showSuccessMessage();
            
            // Clear the deferred prompt
            this.deferredPrompt = null;
        });
    }

    /**
     * When DOM is ready
     */
    onDOMReady() {
        this.setupAllButtons();
        this.setupHeaderButton();
        this.setupHeroButton();
    }

    /**
     * Setup all install buttons on the page
     */
    setupAllButtons() {
        // Welcome page buttons
        const installBtn = document.getElementById('pwa-install-btn');
        const desktopBtn = document.getElementById('pwa-desktop-btn');
        
        if (installBtn) {
            installBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleInstallClick();
            });
        }
        
        if (desktopBtn) {
            desktopBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleInstallClick();
            });
        }
    }

    /**
     * Setup header install button (for SPA layout)
     */
    setupHeaderButton() {
        const headerBtn = document.getElementById('pwa-install-btn-header');
        if (headerBtn) {
            headerBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleInstallClick();
            });
        }
    }

    /**
     * Setup hero section install button (for welcome page)
     */
    setupHeroButton() {
        const heroBtn = document.getElementById('pwa-hero-download-btn');
        if (heroBtn) {
            heroBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleInstallClick();
            });
        }
    }

    /**
     * Handle install click
     */
    async handleInstallClick() {
        this.log('Install button clicked, deferredPrompt:', this.deferredPrompt);
        
        if (!this.deferredPrompt) {
            this.showError('Installation not available on this device. Try using a modern browser.');
            return;
        }

        try {
            // Show install prompt
            this.deferredPrompt.prompt();
            
            // Wait for user response
            const { outcome } = await this.deferredPrompt.userChoice;
            
            this.log('Install prompt outcome:', outcome);
            
            if (outcome === 'accepted') {
                this.log('User accepted installation');
            } else {
                this.log('User declined installation');
            }
            
            // Clear deferred prompt
            this.deferredPrompt = null;
            
        } catch (error) {
            this.log('Error during installation:', error);
            this.showError('Installation failed. Please try again.');
        }
    }

    /**
     * Show install buttons
     */
    showInstallButtons() {
        if (this.isInstalled) {
            this.hideInstallButtons();
            return;
        }
        
        // Welcome page buttons
        const installBtn = document.getElementById('pwa-install-btn');
        const desktopBtn = document.getElementById('pwa-desktop-btn');
        const heroBtn = document.getElementById('pwa-hero-download-btn');
        const headerBtn = document.getElementById('pwa-install-btn-header');
        
        // Show with fade animation
        [installBtn, desktopBtn, heroBtn, headerBtn].forEach(btn => {
            if (btn) {
                btn.classList.remove('hidden');
                setTimeout(() => {
                    btn.style.opacity = '1';
                }, 10);
            }
        });
        
        this.log('Install buttons shown');
    }

    /**
     * Hide install buttons
     */
    hideInstallButtons() {
        const installBtn = document.getElementById('pwa-install-btn');
        const desktopBtn = document.getElementById('pwa-desktop-btn');
        const heroBtn = document.getElementById('pwa-hero-download-btn');
        const headerBtn = document.getElementById('pwa-install-btn-header');
        const appDownloadSection = document.getElementById('app-download-section');
        
        [installBtn, desktopBtn, heroBtn, headerBtn].forEach(btn => {
            if (btn) btn.classList.add('hidden');
        });
        
        // Hide entire section if it exists
        if (appDownloadSection && this.isInstalled) {
            appDownloadSection.classList.add('hidden');
        }
    }

    /**
     * Show success message
     */
    showSuccessMessage() {
        const message = document.createElement('div');
        message.className = 'fixed top-20 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2';
        message.innerHTML = '<i class="fas fa-check-circle"></i> App installed successfully!';
        document.body.appendChild(message);

        setTimeout(() => {
            message.remove();
        }, 3000);
    }

    /**
     * Show error message
     */
    showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'fixed top-20 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2';
        errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
        document.body.appendChild(errorDiv);

        setTimeout(() => {
            errorDiv.remove();
        }, 4000);
    }

    /**
     * Detect device platform
     */
    detectPlatform() {
        const ua = navigator.userAgent.toLowerCase();
        
        if (/iphone|ipad|ipot/.test(ua)) return 'iOS';
        if (/android/.test(ua)) return 'Android';
        if (/windows|win32/.test(ua)) return 'Windows';
        if (/macintosh|mac os x/.test(ua)) return 'Mac';
        if (/linux/.test(ua) && !/android/.test(ua)) return 'Linux';
        
        return 'Desktop';
    }

    /**
     * Check if app is already installed
     */
    checkIfInstalled() {
        if (window.navigator.standalone === true) return true;
        if (window.matchMedia('(display-mode: standalone)').matches) return true;
        if (window.matchMedia('(display-mode: fullscreen)').matches) return true;
        if (window.matchMedia('(display-mode: minimal-ui)').matches) return true;
        
        return false;
    }

    /**
     * Logging helper
     */
    log(...args) {
        if (this.debug) {
            console.log('[PWA Manager]', ...args);
        }
    }

    /**
     * Get device info for debugging
     */
    getDeviceInfo() {
        return {
            platform: this.platform,
            isInstalled: this.isInstalled,
            hasDeferredPrompt: !!this.deferredPrompt,
            userAgent: navigator.userAgent,
            isStandalone: window.navigator.standalone
        };
    }
}

// Initialize IMMEDIATELY (before DOM is loaded)
window.unifiedPWAManager = new UnifiedPWAManager();

// Also keep backward compatibility with old names
window.appDownloadManager = window.unifiedPWAManager;
window.pwaManager = window.unifiedPWAManager;

// Log status
window.addEventListener('load', () => {
    console.log('[PWA] Device Info:', window.unifiedPWAManager.getDeviceInfo());
});
