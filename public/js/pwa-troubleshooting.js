/**
 * PWA TROUBLESHOOTING GUIDE
 * 
 * If the install button doesn't appear or installation fails,
 * use these steps to diagnose and fix the problem.
 */

// ==========================================
// STEP 1: Check PWA Manager Status
// ==========================================
console.log('=== PWA TROUBLESHOOTING ===');
console.log('Checking PWA Manager...');

if (window.unifiedPWAManager) {
    console.log('✓ Unified PWA Manager loaded');
    console.log('Device Info:', window.unifiedPWAManager.getDeviceInfo());
} else {
    console.error('✗ Unified PWA Manager NOT loaded');
    console.error('Check: Is unified-pwa-manager.js being loaded?');
}

// ==========================================
// STEP 2: Check Manifest
// ==========================================
console.log('\n=== CHECKING MANIFEST ===');
fetch('/manifest.json')
    .then(r => r.json())
    .then(manifest => {
        console.log('✓ Manifest accessible');
        console.log('Name:', manifest.name);
        console.log('Icons count:', manifest.icons ? manifest.icons.length : 0);
        
        // Check each icon
        if (manifest.icons) {
            manifest.icons.forEach((icon, i) => {
                fetch(icon.src, { method: 'HEAD' })
                    .then(r => {
                        console.log(`  ✓ Icon ${i+1}: ${icon.src}`);
                    })
                    .catch(() => {
                        console.error(`  ✗ Icon ${i+1} NOT FOUND: ${icon.src}`);
                    });
            });
        }
    })
    .catch(err => {
        console.error('✗ Manifest NOT accessible:', err);
    });

// ==========================================
// STEP 3: Check Service Worker
// ==========================================
console.log('\n=== CHECKING SERVICE WORKER ===');
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.ready
        .then(registration => {
            console.log('✓ Service Worker registered');
            console.log('Scope:', registration.scope);
        })
        .catch(err => {
            console.error('✗ Service Worker registration failed:', err);
        });
} else {
    console.error('✗ Service Workers not supported');
}

// ==========================================
// STEP 4: Check beforeinstallprompt Event
// ==========================================
console.log('\n=== CHECKING beforeinstallprompt ===');

let promptCaptured = false;
const checkPrompt = () => {
    if (window.unifiedPWAManager && window.unifiedPWAManager.deferredPrompt) {
        console.log('✓ beforeinstallprompt event CAPTURED');
        promptCaptured = true;
    } else {
        console.warn('⚠ beforeinstallprompt NOT captured yet');
    }
};

// Check immediately
checkPrompt();

// Check again after a delay
setTimeout(() => {
    checkPrompt();
}, 2000);

// ==========================================
// STEP 5: Check Install Buttons
// ==========================================
console.log('\n=== CHECKING INSTALL BUTTONS ===');

const buttons = [
    'pwa-install-btn',
    'pwa-hero-download-btn',
    'pwa-install-btn-header',
    'pwa-desktop-btn'
];

buttons.forEach(btnId => {
    const btn = document.getElementById(btnId);
    if (btn) {
        const isVisible = btn.style.display !== 'none' && btn.offsetParent !== null;
        console.log(`${isVisible ? '✓' : '⚠'} ${btnId}: ${isVisible ? 'VISIBLE' : 'hidden'}`);
    } else {
        console.warn(`⚠ ${btnId}: NOT FOUND in DOM`);
    }
});

// ==========================================
// STEP 6: Detailed Browser Info
// ==========================================
console.log('\n=== BROWSER INFORMATION ===');
console.log('User Agent:', navigator.userAgent);
console.log('Standalone?', window.navigator.standalone);
console.log('Display Mode:', window.matchMedia('(display-mode: standalone)').matches ? 'standalone' : 'browser');

// ==========================================
// HELPFUL COMMANDS
// ==========================================
console.log('\n=== HELPFUL COMMANDS ===');
console.log('Run these in the console for more info:');
console.log('');
console.log('1. Check device info:');
console.log('   window.unifiedPWAManager.getDeviceInfo()');
console.log('');
console.log('2. Manually trigger install:');
console.log('   window.unifiedPWAManager.handleInstallClick()');
console.log('');
console.log('3. Check manifest:');
console.log('   fetch("/manifest.json").then(r => r.json()).then(console.log)');
console.log('');
console.log('4. Check service workers:');
console.log('   navigator.serviceWorker.getRegistrations().then(console.log)');
console.log('');
console.log('5. Clear service worker cache:');
console.log('   navigator.serviceWorker.getRegistrations().then(regs => {');
console.log('     regs.forEach(r => r.unregister());');
console.log('   });');
console.log('');

console.log('=== END TROUBLESHOOTING ===\n');

// ==========================================
// COMMON ISSUES & SOLUTIONS
// ==========================================
/*

ISSUE: Install button doesn't appear
SOLUTIONS:
1. Check manifest.json is linked: <link rel="manifest" href="/manifest.json">
2. Check icons exist in public/images/
3. Check unified-pwa-manager.js is loaded FIRST
4. Clear browser cache (Ctrl+Shift+Delete)
5. Hard refresh (Ctrl+F5)
6. Check browser console for errors

ISSUE: beforeinstallprompt event not captured
SOLUTIONS:
1. Make sure you're on a secure HTTPS connection (PWAs require HTTPS)
2. Ensure manifest.json has proper display mode: "standalone"
3. Service worker must be registered successfully
4. Check that icons are accessible and properly sized
5. Some browsers (like Firefox) may not show the install prompt

ISSUE: Installation fails
SOLUTIONS:
1. Check browser console for specific error message
2. Try with a different browser
3. Ensure manifest.json is valid JSON
4. Check icons are real PNG files (not broken)
5. Verify display-mode is "standalone"

ISSUE: App works offline but features are limited
SOLUTIONS:
1. Check service-worker.js is caching the right routes
2. Verify the service worker's CACHE_VERSION
3. Check Network-first routes include your API endpoints
4. Open DevTools > Application > Cache Storage to see cached items

*/
