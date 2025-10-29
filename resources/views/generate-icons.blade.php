<?php
// Pastikan GD atau Imagick terinstal
if (!extension_loaded('gd') && !extension_loaded('imagick')) {
    die('Neither GD nor Imagick is installed');
}

$sizes = [72, 96, 128, 144, 152, 192, 384, 512];
$sourceImage = public_path('icon.jpg');

if (!file_exists($sourceImage)) {
    die('Source image not found: ' . $sourceImage);
}

foreach ($sizes as $size) {
    $outputFile = public_path("icon-{$size}x{$size}.png");
    
    // Menggunakan Intervention Image jika terinstal
    if (class_exists('\Intervention\Image\Facades\Image')) {
        $img = \Intervention\Image\Facades\Image::make($sourceImage);
        $img->fit($size, $size);
        $img->save($outputFile, 100);
    } else {
        // Fallback ke GD
        $source = imagecreatefromjpeg($sourceImage);
        $width = imagesx($source);
        $height = imagesy($source);
        
        $square = min($width, $height);
        $x = ($width - $square) / 2;
        $y = ($height - $square) / 2;
        
        $dest = imagecreatetruecolor($size, $size);
        imagecopyresampled($dest, $source, 0, 0, $x, $y, $size, $size, $square, $square);
        imagepng($dest, $outputFile, 9);
        imagedestroy($dest);
        imagedestroy($source);
    }
    
    echo "Generated icon-{$size}x{$size}.png\n";
}

// Buat screenshot default jika tidak ada
if (!file_exists(public_path('screenshot1.png'))) {
    copy($sourceImage, public_path('screenshot1.png'));
    echo "Created default screenshot1.png\n";
}

echo "Done generating icons!\n";