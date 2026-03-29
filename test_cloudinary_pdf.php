<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

$file = __DIR__ . '/dummy.pdf';

try {
    echo "Uploading with raw...\n";
    $res1 = Cloudinary::uploadApi()->upload($file, ['folder' => 'test', 'resource_type' => 'raw']);
    echo "Raw URL: " . $res1['secure_url'] . "\n\n";
} catch (\Exception $e) { echo "ERROR: " . $e->getMessage() . "\n"; }

try {
    echo "Uploading with auto...\n";
    $res2 = Cloudinary::uploadApi()->upload($file, ['folder' => 'test', 'resource_type' => 'auto']);
    echo "Auto URL: " . $res2['secure_url'] . "\n\n";
} catch (\Exception $e) { echo "ERROR: " . $e->getMessage() . "\n"; }

try {
    echo "Uploading with image (default)...\n";
    $res3 = Cloudinary::uploadApi()->upload($file, ['folder' => 'test']);
    echo "Image URL: " . $res3['secure_url'] . "\n\n";
} catch (\Exception $e) { echo "ERROR: " . $e->getMessage() . "\n"; }
