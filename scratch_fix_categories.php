<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;
use App\Models\Athlete;

$cLibre = Category::where('nombre', 'Libre')->first();
$cAscenso = Category::where('nombre', 'Ascenso')->first();

if ($cLibre && $cAscenso) {
    echo "Fusionando Libre en Ascenso...\n";
    Athlete::where('category_id', $cLibre->id)->update(['category_id' => $cAscenso->id]);
    $cLibre->delete();
    echo "Listo. Libre eliminada.\n";
} elseif ($cLibre) {
    echo "Renombrando Libre a Ascenso...\n";
    $cLibre->update(['nombre' => 'Ascenso']);
    echo "Listo. Renombrado.\n";
} else {
    echo "No se encontró la categoría Libre.\n";
}
