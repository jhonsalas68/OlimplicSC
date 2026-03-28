<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $edades = [0, 10, 15, 20, 99, 100, -1];
    foreach ($edades as $edad) {
        try {
            $cat = App\Models\Category::resolverPorEdad($edad);
            echo "Edad $edad -> Categoria: {$cat->nombre}\n";
        } catch (\Exception $e) {
            echo "Edad $edad -> ERROR: " . $e->getMessage() . "\n";
        }
    }
} catch (\Exception $e) {
    echo "Fatal: " . $e->getMessage() . "\n";
}
