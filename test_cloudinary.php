<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Creating test image...\n";
    $img = imagecreatetruecolor(200, 200);
    $bg = imagecolorallocate($img, 0, 153, 255);
    $text_color = imagecolorallocate($img, 255, 255, 255);
    imagefill($img, 0, 0, $bg);
    imagestring($img, 5, 20, 90, 'OlimpicSC Test', $text_color);
    imagepng($img, __DIR__ . '/public/test_image.png');
    imagedestroy($img);
    echo "Test image created.\n";

    echo "Uploading directly via Cloudinary SDK...\n";
    
    $cloudinary = app(\Cloudinary\Cloudinary::class);
    $response = $cloudinary->uploadApi()->upload(__DIR__ . '/public/test_image.png', [
        'folder' => 'test'
    ]);
    
    echo "Secure URL: " . $response['secure_url'] . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
