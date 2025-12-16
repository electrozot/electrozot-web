<?php
/**
 * PWA Icon Generator and Fixer
 * This script creates all required PWA icons from the existing splash-icon.png
 */

// Check if GD extension is available
if (!extension_loaded('gd')) {
    die('GD extension is required for image processing');
}

// Source image
$sourceImage = 'splash-icon.png';
if (!file_exists($sourceImage)) {
    die('Source image splash-icon.png not found');
}

// Create icons directory if it doesn't exist
$iconDir = 'vendor/img/icons';
if (!is_dir($iconDir)) {
    mkdir($iconDir, 0755, true);
}

// Icon sizes needed for PWA
$iconSizes = [
    72, 96, 128, 144, 152, 192, 384, 512
];

// Apple icon sizes
$appleIconSizes = [
    57, 60, 72, 76, 114, 120, 144, 152, 180
];

// Android icon sizes
$androidIconSizes = [
    36, 48, 72, 96, 144, 192
];

// Microsoft icon sizes
$msIconSizes = [
    70, 150, 310
];

// Favicon sizes
$faviconSizes = [
    16, 32, 96
];

function createIcon($sourceImage, $outputPath, $size) {
    // Get source image info
    $imageInfo = getimagesize($sourceImage);
    $sourceWidth = $imageInfo[0];
    $sourceHeight = $imageInfo[1];
    $sourceType = $imageInfo[2];
    
    // Create source image resource
    switch ($sourceType) {
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($sourceImage);
            break;
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($sourceImage);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($sourceImage);
            break;
        default:
            return false;
    }
    
    // Create new image
    $newImage = imagecreatetruecolor($size, $size);
    
    // Preserve transparency for PNG
    imagealphablending($newImage, false);
    imagesavealpha($newImage, true);
    $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
    imagefill($newImage, 0, 0, $transparent);
    
    // Resize image
    imagecopyresampled(
        $newImage, $source,
        0, 0, 0, 0,
        $size, $size,
        $sourceWidth, $sourceHeight
    );
    
    // Save as PNG
    $result = imagepng($newImage, $outputPath);
    
    // Clean up
    imagedestroy($source);
    imagedestroy($newImage);
    
    return $result;
}

echo "Creating PWA icons...\n";

// Create PWA icons
foreach ($iconSizes as $size) {
    $outputPath = "{$iconDir}/icon-{$size}x{$size}.png";
    if (createIcon($sourceImage, $outputPath, $size)) {
        echo "✓ Created {$outputPath}\n";
    } else {
        echo "✗ Failed to create {$outputPath}\n";
    }
}

// Create Apple icons
foreach ($appleIconSizes as $size) {
    $outputPath = "{$iconDir}/apple-icon-{$size}x{$size}.png";
    if (createIcon($sourceImage, $outputPath, $size)) {
        echo "✓ Created {$outputPath}\n";
    } else {
        echo "✗ Failed to create {$outputPath}\n";
    }
}

// Create Android icons
foreach ($androidIconSizes as $size) {
    $outputPath = "{$iconDir}/android-icon-{$size}x{$size}.png";
    if (createIcon($sourceImage, $outputPath, $size)) {
        echo "✓ Created {$outputPath}\n";
    } else {
        echo "✗ Failed to create {$outputPath}\n";
    }
}

// Create Microsoft icons
foreach ($msIconSizes as $size) {
    $outputPath = "{$iconDir}/ms-icon-{$size}x{$size}.png";
    if (createIcon($sourceImage, $outputPath, $size)) {
        echo "✓ Created {$outputPath}\n";
    } else {
        echo "✗ Failed to create {$outputPath}\n";
    }
}

// Create favicons
foreach ($faviconSizes as $size) {
    $outputPath = "{$iconDir}/favicon-{$size}x{$size}.png";
    if (createIcon($sourceImage, $outputPath, $size)) {
        echo "✓ Created {$outputPath}\n";
    } else {
        echo "✗ Failed to create {$outputPath}\n";
    }
}

// Create favicon.ico (using 32x32 as base)
$favicon32 = "{$iconDir}/favicon-32x32.png";
if (file_exists($favicon32)) {
    copy($favicon32, "{$iconDir}/favicon.ico");
    echo "✓ Created favicon.ico\n";
}

echo "\nAll PWA icons created successfully!\n";
echo "Icons are now available in: {$iconDir}/\n";
?>