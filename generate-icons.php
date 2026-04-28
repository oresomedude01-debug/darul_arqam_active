<?php
// Generate PWA icon files

$sizes = [
    'icon-96x96.png' => [96, 96],
    'icon-192x192.png' => [192, 192],
    'icon-512x512.png' => [512, 512],
    'icon-maskable-192x192.png' => [192, 192],
    'icon-maskable-512x512.png' => [512, 512],
    'screenshot-540x720.png' => [540, 720],
    'screenshot-1280x720.png' => [1280, 720]
];

$outputDir = 'public/images';

foreach ($sizes as $filename => [$width, $height]) {
    $image = imagecreatetruecolor($width, $height);
    
    // Primary blue color from theme (#0284c7)
    $bgColor = imagecolorallocate($image, 2, 132, 199);
    $textColor = imagecolorallocate($image, 255, 255, 255);
    
    imagefill($image, 0, 0, $bgColor);
    
    // Add text label
    $label = 'Darul Arqam';
    $textX = max(10, ($width - (strlen($label) * 5)) / 2);
    $textY = ($height - 10) / 2;
    
    imagestring($image, 2, $textX, $textY, $label, $textColor);
    
    $filepath = $outputDir . '/' . $filename;
    imagepng($image, $filepath);
    imagedestroy($image);
    
    echo "✓ Created: $filename\n";
}

echo "\n✓ All PNG files created successfully!\n";
?>
