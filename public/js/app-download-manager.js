/**
 * App Download Manager
 * Detects device platform and PWA installation status
 * Shows platform-specific download/install buttons
 * Uses school logo as app icon from settings
 */

class AppDownloadManager {
    constructor() {
        this.userAgent = navigator.userAgent;
        this.isInstalled = this.checkIfInstalled();
        this.platform = this.detectPlatform();
        this.installPrompt = null;
        this.appSettings = null;
        this.init();
    }

    /**
     * Fetch PWA settings from server (school logo, app name, etc)
     */
    async fetchAppSettings() {
        try {
            // First try to fetch from API endpoint
            const response = await fetch('/api/pwa/settings');
            if (response.ok) {
                const data = await response.json();
                this.appSettings = {
                    appName: data.appName || data.school_name || 'School Management System',
                    shortName: data.shortName || 'School',
                    logoUrl: data.logoUrl || '/images/icon-192x192.png'
                };
                return;
            }
        } catch (error) {
            console.warn('Failed to fetch PWA settings from API:', error);
        }

        // Fallback: fetch from manifest.json
        try {
            const response = await fetch('/manifest.json');
            if (response.ok) {
                const manifest = await response.json();
                this.appSettings = {
                    appName: manifest.name || 'School Management System',
                    shortName: manifest.short_name || 'School',
                    logoUrl: manifest.icons[0]?.src || '/images/icon-192x192.png'
                };
                return;
            }
        } catch (error) {
            console.warn('Failed to fetch manifest:', error);
        }

        // Final fallback
        this.appSettings = {
            appName: 'School Management System',
            shortName: 'School',
            logoUrl: '/images/icon-192x192.png'
        };
    }

    /**
     * Get app icon URL (school logo or PWA icon)
     */
    getSchoolLogo() {
        return this.appSettings?.logoUrl || '/images/icon-192x192.png';
    }

    /**
     * Get app name
     */
    getAppName() {
        return this.appSettings?.appName || 'School Management System';
    }

    /**
     * Get short app name
     */
    getShortName() {
        return this.appSettings?.shortName || 'School';
    }

    /**
     * Detect device platform
     */
    detectPlatform() {
        const ua = this.userAgent.toLowerCase();
        
        if (/iphone|ipad|ipot/.test(ua)) return 'iOS';
        if (/android/.test(ua)) return 'Android';
        if (/windows|win32/.test(ua)) return 'Windows';
        if (/macintosh|mac os x/.test(ua)) return 'Mac';
        if (/linux/.test(ua) && !/android/.test(ua)) return 'Linux';
        
        return 'Desktop';
    }

    /**
     * Check if PWA is already installed
     */
    checkIfInstalled() {
        // Check if running as standalone app
        if (window.navigator.standalone === true) return true;
        
        // Check CSS media query for display-mode
        if (window.matchMedia('(display-mode: standalone)').matches) return true;
        if (window.matchMedia('(display-mode: fullscreen)').matches) return true;
        if (window.matchMedia('(display-mode: window-controls-overlay)').matches) return true;
        
        // Check for iOS app mode
        if (window.matchMedia('(display-mode: minimal-ui)').matches) return true;
        
        return false;
    }

    /**
     * Initialize download manager
     */
    async init() {
        // Fetch app settings first
        await this.fetchAppSettings();

        // Hide download section if app is already installed
        const downloadSection = document.getElementById('app-download-section');
        if (!downloadSection) return;

        if (this.isInstalled) {
            downloadSection.style.display = 'none';
            return;
        }

        // Setup platform-specific content
        this.setupPlatformContent();

        // Listen for install prompt
        window.addEventListener('beforeinstallprompt', (e) => {
            this.installPrompt = e;
            this.showInstallButton();
        });

        // Listen for app installed
        window.addEventListener('appinstalled', () => {
            this.handleAppInstalled();
        });

        // Show appropriate buttons based on platform
        this.showPlatformButtons();
    }

    /**
     * Setup platform-specific content
     */
    setupPlatformContent() {
        const content = document.getElementById('platform-specific-content');
        if (!content) return;

        let html = '';

        switch (this.platform) {
            case 'iOS':
                html = this.getIOSContent();
                break;
            case 'Android':
                html = this.getAndroidContent();
                break;
            case 'Windows':
            case 'Mac':
            case 'Linux':
            case 'Desktop':
                html = this.getDesktopContent();
                break;
        }

        content.innerHTML = html;
    }

    /**
     * Get iOS-specific content
     */
    getIOSContent() {
        const logoUrl = this.getSchoolLogo();
        const appName = this.getAppName();
        
        return `
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <img src="${logoUrl}" alt="${appName}" class="w-12 h-12 rounded-lg object-cover" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-900 mb-2">Add App to Home Screen</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Get instant access to the app from your home screen. It works just like a native app!
                        </p>
                        <div class="bg-white rounded-lg p-4 mb-4 text-xs text-gray-700">
                            <p class="font-semibold mb-2">🔧 How to install:</p>
                            <ol class="list-decimal list-inside space-y-1 text-gray-600">
                                <li>Tap the <span class="font-mono bg-gray-100 px-2 py-1 rounded">Share</span> button</li>
                                <li>Select <span class="font-mono bg-gray-100 px-2 py-1 rounded">Add to Home Screen</span></li>
                                <li>Choose a name and tap <span class="font-mono bg-gray-100 px-2 py-1 rounded">Add</span></li>
                            </ol>
                        </div>
                        <button onclick="window.scrollTo({ top: 0, behavior: 'smooth' })" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg transition-all hover:shadow-lg">
                            <i class="fas fa-arrow-up"></i>
                            See Install Guide
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Get Android-specific content
     */
    getAndroidContent() {
        const logoUrl = this.getSchoolLogo();
        const appName = this.getAppName();
        
        return `
            <div class="space-y-4">
                <div class="bg-green-50 border border-green-200 rounded-xl p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <img src="${logoUrl}" alt="${appName}" class="w-12 h-12 rounded-lg object-cover" />
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-900 mb-2">Install App</h3>
                            <p class="text-sm text-gray-600 mb-4">
                                Get the app directly on your Android device. Fast, reliable, and offline-ready.
                            </p>
                            <button id="install-app-btn" onclick="appDownloadManager.installApp()" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-lg transition-all hover:shadow-lg">
                                <i class="fas fa-download"></i>
                                Install Now
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-xl p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <img src="${logoUrl}" alt="${appName}" class="w-12 h-12 rounded-lg object-cover" />
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-900 mb-2">Google Play Store</h3>
                            <p class="text-sm text-gray-600 mb-4">
                                Get automatic updates and additional features from the official Play Store.
                            </p>
                            <a href="https://play.google.com/store/apps/details?id=com.darularqam.pwa" target="_blank" rel="noopener" class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-900 text-white font-semibold px-6 py-2 rounded-lg transition-all hover:shadow-lg">
                                <i class="fab fa-google-play"></i>
                                Open Play Store
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Get Desktop-specific content
     */
    getDesktopContent() {
        const logoUrl = this.getSchoolLogo();
        const appName = this.getAppName();
        
        return `
            <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <img src="${logoUrl}" alt="${appName}" class="w-12 h-12 rounded-lg object-cover" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-900 mb-2">Install as App</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Install the app on your computer for a native app experience. Works offline and keeps your data synced.
                        </p>
                        <button id="install-app-btn" onclick="appDownloadManager.installApp()" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded-lg transition-all hover:shadow-lg">
                            <i class="fas fa-download"></i>
                            Install App
                        </button>
                        <p class="text-xs text-gray-500 mt-3">
                            💡 <strong>Tip:</strong> Look for the install icon in your browser's address bar if it doesn't appear above.
                        </p>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Show install button when install prompt is available
     */
    showInstallButton() {
        const installBtn = document.getElementById('install-app-btn');
        if (installBtn) {
            installBtn.style.display = 'inline-flex';
        }
    }

    /**
     * Handle app install
     */
    async installApp() {
        if (!this.installPrompt) {
            console.log('Install prompt not available');
            alert('App installation is not available on your device at this time.');
            return;
        }

        this.installPrompt.prompt();
        const { outcome } = await this.installPrompt.userChoice;

        if (outcome === 'accepted') {
            console.log('App installed successfully');
            this.handleAppInstalled();
        } else {
            console.log('App installation cancelled');
        }

        this.installPrompt = null;
    }

    /**
     * Handle app installed event
     */
    handleAppInstalled() {
        const downloadSection = document.getElementById('app-download-section');
        if (downloadSection) {
            downloadSection.classList.add('fade-out');
            setTimeout(() => {
                downloadSection.style.display = 'none';
            }, 500);
        }

        // Show success message
        this.showInstallSuccess();
    }

    /**
     * Show installation success message
     */
    showInstallSuccess() {
        const successDiv = document.createElement('div');
        successDiv.className = 'fixed top-20 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2 animate-bounce';
        successDiv.innerHTML = '<i class="fas fa-check-circle"></i> App installed successfully!';
        document.body.appendChild(successDiv);

        setTimeout(() => {
            successDiv.remove();
        }, 4000);
    }

    /**
     * Show platform buttons
     */
    showPlatformButtons() {
        const downloadSection = document.getElementById('app-download-section');
        if (downloadSection) {
            downloadSection.style.opacity = '0';
            setTimeout(() => {
                downloadSection.style.transition = 'opacity 0.5s ease-in-out';
                downloadSection.style.opacity = '1';
            }, 100);
        }
    }

    /**
     * Get device info for debugging
     */
    getDeviceInfo() {
        return {
            platform: this.platform,
            isInstalled: this.isInstalled,
            userAgent: this.userAgent,
            isStandalone: window.navigator.standalone
        };
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.appDownloadManager = new AppDownloadManager();
});
