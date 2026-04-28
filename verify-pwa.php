<?php
echo "=== PWA Diagnostic Report ===\n\n";

// Check manifest.json
echo "1. Manifest.json Status:\n";
$manifestPath = 'public/manifest.json';
if (file_exists($manifestPath)) {
    $manifest = json_decode(file_get_contents($manifestPath), true);
    if ($manifest) {
        echo "   ✓ Valid JSON\n";
        echo "   ✓ App Name: " . ($manifest['name'] ?? 'N/A') . "\n";
        echo "   ✓ Icons: " . count($manifest['icons'] ?? []) . "\n";
        echo "   ✓ Screenshots: " . count($manifest['screenshots'] ?? []) . "\n";
    } else {
        echo "   ✗ Invalid JSON format\n";
    }
} else {
    echo "   ✗ File not found\n";
}

// Check required icon files
echo "\n2. Required Icon Files:\n";
$requiredIcons = [
    'public/images/icon-96x96.png',
    'public/images/icon-192x192.png',
    'public/images/icon-512x512.png',
    'public/images/icon-maskable-192x192.png',
    'public/images/icon-maskable-512x512.png',
    'public/images/screenshot-540x720.png',
    'public/images/screenshot-1280x720.png'
];

$allIconsExist = true;
foreach ($requiredIcons as $icon) {
    if (file_exists($icon)) {
        $size = filesize($icon);
        echo "   ✓ " . basename($icon) . " (" . $size . " bytes)\n";
    } else {
        echo "   ✗ " . basename($icon) . " - MISSING\n";
        $allIconsExist = false;
    }
}

// Check service worker
echo "\n3. Service Worker:\n";
$swPath = 'public/service-worker.js';
if (file_exists($swPath)) {
    echo "   ✓ File exists (" . filesize($swPath) . " bytes)\n";
    $content = file_get_contents($swPath);
    if (strpos($content, 'CACHE_VERSION') !== false) {
        echo "   ✓ Contains cache version\n";
    }
    if (strpos($content, 'addEventListener') !== false) {
        echo "   ✓ Contains event listeners\n";
    }
} else {
    echo "   ✗ File not found\n";
}

// Check PWA JS
echo "\n4. PWA Manager Script:\n";
$pwaPath = 'resources/js/pwa.js';
if (file_exists($pwaPath)) {
    echo "   ✓ File exists (" . filesize($pwaPath) . " bytes)\n";
    $content = file_get_contents($pwaPath);
    if (strpos($content, 'PWAManager') !== false) {
        echo "   ✓ Contains PWAManager class\n";
    }
    if (strpos($content, 'beforeinstallprompt') !== false) {
        echo "   ✓ Handles install prompt\n";
    }
} else {
    echo "   ✗ File not found\n";
}

// Check offline route
echo "\n5. Offline Route:\n";
$routesPath = 'routes/web.php';
if (file_exists($routesPath)) {
    $content = file_get_contents($routesPath);
    if (strpos($content, '/offline') !== false && strpos($content, 'offline.blade.php') !== false) {
        echo "   ✓ Offline route configured\n";
    } else {
        echo "   ✗ Offline route not found\n";
    }
} else {
    echo "   ✗ Routes file not found\n";
}

echo "\n=== Diagnostic Complete ===\n";
echo ($allIconsExist ? "\n✓ All PWA requirements are satisfied!\n" : "\n✗ Some PWA files are missing. Please check above.\n");
?>
