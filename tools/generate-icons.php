<?php
$src = __DIR__ . '/../public/masjid.jpg';
if (!file_exists($src)) {
    echo "Source image not found: $src\n";
    exit(1);
}
$sizes = [192, 512];
foreach ($sizes as $size) {
    $img = imagecreatefromjpeg($src);
    if (!$img) {
        echo "Failed to create image from source\n";
        continue;
    }
    $w = imagesx($img);
    $h = imagesy($img);

    // create square crop from center
    $min = min($w, $h);
    $src_x = (int)(($w - $min) / 2);
    $src_y = (int)(($h - $min) / 2);

    $dst = imagecreatetruecolor($size, $size);
    // Preserve alpha
    imagealphablending($dst, false);
    imagesavealpha($dst, true);

    imagecopyresampled($dst, $img, 0, 0, $src_x, $src_y, $size, $size, $min, $min);
    $out = __DIR__ . "/../public/icon-{$size}.png";
    imagepng($dst, $out, 6);
    imagedestroy($dst);
    imagedestroy($img);
    echo "Wrote $out\n";
}
