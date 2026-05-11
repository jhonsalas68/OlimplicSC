<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;
use App\Models\Athlete;

return new class extends Migration
{
    public function up()
    {
        $cLibre = Category::where('nombre', 'Libre')->first();
        $cAscenso = Category::where('nombre', 'Ascenso')->first();

        if ($cLibre && $cAscenso) {
            // Mover atletas de Libre a Ascenso
            Athlete::where('category_id', $cLibre->id)->update(['category_id' => $cAscenso->id]);
            
            // Mover relación de coaches de Libre a Ascenso
            \DB::table('category_user')->where('category_id', $cLibre->id)->update(['category_id' => $cAscenso->id]);
            
            // Eliminar Libre
            $cLibre->delete();
        } elseif ($cLibre) {
            // Si solo existe Libre, simplemente renombrar
            $cLibre->update(['nombre' => 'Ascenso']);
        }
    }

    public function down()
    {
        // No es necesario revertir esto ya que es una limpieza de datos
    }
};
