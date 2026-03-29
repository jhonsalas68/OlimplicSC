<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

$tempFile = sys_get_temp_dir() . '/phpxyz123';
file_put_contents($tempFile, '%PDF-1.4\n1 0 obj <</Type /Catalog /Pages 2 0 R>> endobj\n2 0 obj <</Type /Pages /Kids [] /Count 0>> endobj\nxref\n0 3\n0000000000 65535 f \n0000000009 00000 n \n0000000052 00000 n \ntrailer <</Size 3 /Root 1 0 R>>\nstartxref\n101\n%%EOF');

try {
    $resImage = Cloudinary::uploadApi()->upload($tempFile, [
        'folder' => 'trainings',
        'resource_type' => 'image',
        'public_id' => 'testplan_image_' . time() . '.pdf'
    ]);
    echo "Image URL: " . $resImage['secure_url'] . "\n\n";
    
    // Check headers
    $headers = get_headers($resImage['secure_url'], 1);
    echo "Headers for Image:\n";
    print_r($headers);
} catch (\Exception $e) { echo "ERROR Image: " . $e->getMessage() . "\n"; }

unlink($tempFile);
