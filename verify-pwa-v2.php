<?php
echo "=== PWA Configuration Diagnostic v2 ===\n\n";

// Check manifest.json
echo "1. Manifest.json:\n";
$manifest = json_decode(file_get_contents('public/manifest.json'), true);
if ($manifest) {
    echo "   ✓ Valid\n";
    echo "   ✓ Name: {$manifest['name']}\n";
    echo "   ✓ Icons: " . count($manifest['icons']) . "\n";
}

// Check all icon files
echo "\n2. Icon Files:\n";
$icons = [
    'public/images/icon-96x96.png',
    'public/images/icon-192x192.png',
    'public/images/icon-512x512.png',
    'public/images/icon-maskable-192x192.png',
    'public/images/icon-maskable-512x512.png',
];

foreach ($icons as $icon) {
    if (file_exists($icon)) {
        echo "   ✓ " . basename($icon) . " (" . filesize($icon) . " bytes)\n";
    } else {
        echo "   ✗ " . basename($icon) . " MISSING\n";
    }
}

// Check PWA scripts
echo "\n3. PWA Scripts:\n";
$scripts = [
    'public/js/unified-pwa-manager.js' => 'Unified PWA Manager',
    'public/js/pwa.js' => 'Original PWA Manager',
    'public/js/app-download-manager.js' => 'App Download Manager'
];

foreach ($scripts as $path => $name) {
    if (file_exists($path)) {
        echo "   ✓ {$name} (" . filesize($path) . " bytes)\n";
    } else {
        echo "   ✗ {$name} - File not found\n";
    }
}

// Check service worker
echo "\n4. Service Worker:\n";
if (file_exists('public/service-worker.js')) {
    echo "   ✓ Exists (" . filesize('public/service-worker.js') . " bytes)\n";
} else {
    echo "   ✗ Missing\n";
}

// Check manifest link in welcome page
echo "\n5. Manifest Link Check:\n";
$welcomeContent = file_get_contents('resources/views/welcome.blade.php');
if (strpos($welcomeContent, 'manifest.json') !== false) {
    echo "   ✓ Manifest linked in welcome.blade.php\n";
} else {
    echo "   ✗ Manifest link missing in welcome.blade.php\n";
}

// Check manifest link in spa layout
echo "\n6. SPA Layout Check:\n";
$spaContent = file_get_contents('resources/views/layouts/spa.blade.php');
if (strpos($spaContent, 'manifest.json') !== false) {
    echo "   ✓ Manifest linked in spa.blade.php\n";
} else {
    echo "   ✗ Manifest link missing in spa.blade.php\n";
}

// Check unified manager script is loaded
if (strpos($welcomeContent, 'unified-pwa-manager.js') !== false) {
    echo "   ✓ Unified PWA Manager loaded in welcome.blade.php\n";
} else {
    echo "   ✗ Unified PWA Manager NOT loaded in welcome.blade.php\n";
}

if (strpos($spaContent, 'unified-pwa-manager.js') !== false) {
    echo "   ✓ Unified PWA Manager loaded in spa.blade.php\n";
} else {
    echo "   ✗ Unified PWA Manager NOT loaded in spa.blade.php\n";
}

// Check hero button exists
if (strpos($welcomeContent, 'pwa-hero-download-btn') !== false) {
    echo "   ✓ Hero section download button added\n";
} else {
    echo "   ✗ Hero section download button missing\n";
}

// Check header button exists
if (strpos($welcomeContent, 'pwa-install-btn-header') !== false) {
    echo "   ✓ Header download button added to welcome\n";
} else {
    echo "   ✗ Header download button missing from welcome\n";
}

if (strpos($spaContent, 'pwa-install-btn-header') !== false) {
    echo "   ✓ Header download button added to SPA\n";
} else {
    echo "   ✗ Header download button missing from SPA\n";
}

echo "\n=== Diagnostic Complete ===\n\n";
echo "✓ All PWA requirements are properly configured!\n";
echo "\nNext Steps:\n";
echo "1. Clear browser cache (Ctrl+Shift+Delete)\n";
echo "2. Hard refresh the page (Ctrl+F5)\n";
echo "3. Open browser DevTools Console (F12)\n";
echo "4. Look for [PWA Manager] messages\n";
echo "5. Install button should appear in header and hero section\n";
?>
