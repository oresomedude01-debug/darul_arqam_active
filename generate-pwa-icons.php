<?php
/**
 * PWA Icon Generator
 * Generates optimized icon variations from a source image
 * Sizes: 96x96, 192x192, 256x256, 384x384, 512x512
 * Plus maskable versions for adaptive icons
 */

$sourceImage = __DIR__ . '/public/images/icon_no_bg_light.png';
$outputDir = __DIR__ . '/public/images';

if (!file_exists($sourceImage)) {
    die("Source image not found: $sourceImage\n");
}

// Define icon sizes needed for PWA
$sizes = [
    192,  // Android home screen
    512,  // App stores and splash screens
    96,   // Favicon
    144,  // Older Android devices
    256,  // Medium resolution displays
    384,  // High resolution displays
];

$maskableSizes = [192, 512]; // Maskable icons for adaptive icon support

echo "🎨 PWA Icon Generator Started\n";
echo "Source: " . basename($sourceImage) . "\n";
echo "Output Directory: $outputDir\n\n";

// Load the source image
$image = imagecreatefrompng($sourceImage);
if (!$image) {
    die("Failed to load source image\n");
}

// Get original dimensions
$srcWidth = imagesx($image);
$srcHeight = imagesy($image);
echo "Source dimensions: {$srcWidth}x{$srcHeight}\n\n";

$generated = [];
$errors = [];

// Generate regular icons
foreach ($sizes as $size) {
    $outputFile = "$outputDir/icon-{$size}x{$size}.png";
    
    $resized = imagecreatetruecolor($size, $size);
    
    // Preserve transparency
    imagesavealpha($resized, true);
    $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
    imagefill($resized, 0, 0, $transparent);
    
    // Resize with high quality
    if (imagecopyresampled($resized, $image, 0, 0, 0, 0, $size, $size, $srcWidth, $srcHeight)) {
        // Optimize compression
        if (imagepng($resized, $outputFile, 6)) {
            $fileSize = filesize($outputFile);
            $generated[] = "✓ icon-{$size}x{$size}.png ({$fileSize} bytes)";
        } else {
            $errors[] = "✗ Failed to save icon-{$size}x{$size}.png";
        }
        imagedestroy($resized);
    } else {
        $errors[] = "✗ Failed to resize to {$size}x{$size}";
    }
}

echo "Regular Icons:\n";
foreach ($generated as $msg) {
    echo "  $msg\n";
}

// Generate maskable icons (for adaptive icons on Android)
$maskableGenerated = [];
foreach ($maskableSizes as $size) {
    $outputFile = "$outputDir/icon-maskable-{$size}x{$size}.png";
    
    $resized = imagecreatetruecolor($size, $size);
    
    // Preserve transparency
    imagesavealpha($resized, true);
    $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
    imagefill($resized, 0, 0, $transparent);
    
    // Resize
    if (imagecopyresampled($resized, $image, 0, 0, 0, 0, $size, $size, $srcWidth, $srcHeight)) {
        if (imagepng($resized, $outputFile, 6)) {
            $fileSize = filesize($outputFile);
            $maskableGenerated[] = "✓ icon-maskable-{$size}x{$size}.png ({$fileSize} bytes)";
        } else {
            $errors[] = "✗ Failed to save icon-maskable-{$size}x{$size}.png";
        }
        imagedestroy($resized);
    } else {
        $errors[] = "✗ Failed to resize maskable to {$size}x{$size}";
    }
}

echo "\nMaskable Icons (for adaptive icon support):\n";
foreach ($maskableGenerated as $msg) {
    echo "  $msg\n";
}

if (!empty($errors)) {
    echo "\n⚠️  Errors:\n";
    foreach ($errors as $error) {
        echo "  $error\n";
    }
}

imagedestroy($image);

echo "\n✅ PWA Icon generation complete!\n";
echo "\nGenerated " . (count($generated) + count($maskableGenerated)) . " icon files.\n";
?>
