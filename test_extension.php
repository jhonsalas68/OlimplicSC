<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

// Create a temp file without an extension to simulate Laravel's getRealPath()
$tempFile = sys_get_temp_dir() . '/phpxyz123';
file_put_contents($tempFile, '%PDF-1.4\n1 0 obj <</Type /Catalog /Pages 2 0 R>> endobj\n2 0 obj <</Type /Pages /Kids [] /Count 0>> endobj\nxref\n0 3\n0000000000 65535 f \n0000000009 00000 n \n0000000052 00000 n \ntrailer <</Size 3 /Root 1 0 R>>\nstartxref\n101\n%%EOF');

try {
    echo "Uploading temp file with raw AND strict public_id...\n";
    $res1 = Cloudinary::uploadApi()->upload($tempFile, [
        'folder' => 'trainings', 
        'resource_type' => 'raw',
        'public_id' => 'testplan_' . time() . '.pdf'
    ]);
    echo "Raw URL with extension: " . $res1['secure_url'] . "\n\n";
} catch (\Exception $e) { echo "ERROR: " . $e->getMessage() . "\n"; }

unlink($tempFile);
