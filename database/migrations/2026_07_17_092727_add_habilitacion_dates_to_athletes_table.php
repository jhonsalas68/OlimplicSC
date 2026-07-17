<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('athletes', function (Blueprint $table) {
            $table->date('fecha_pago_habilitacion')->nullable()->after('habilitado_booleano');
            $table->date('fecha_vencimiento_habilitacion')->nullable()->after('fecha_pago_habilitacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('athletes', function (Blueprint $table) {
            $table->dropColumn(['fecha_pago_habilitacion', 'fecha_vencimiento_habilitacion']);
        });
    }
};
