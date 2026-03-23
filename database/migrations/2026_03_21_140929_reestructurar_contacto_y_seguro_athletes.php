<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('athletes', function (Blueprint $table) {
            // Seguro médico: booleano + datos de la aseguradora
            $table->boolean('tiene_seguro')->default(false)->after('seguro_medico');
            $table->string('seguro_compania')->nullable()->after('tiene_seguro');
            $table->string('seguro_contacto')->nullable()->after('seguro_compania');

            // Relación del contacto de emergencia con el atleta
            $table->string('relacion_contacto')->nullable()->after('telefono_padre');
            // Para mayores de edad: nombre, teléfono y relación de su contacto
            $table->string('contacto_nombre')->nullable()->after('relacion_contacto');
            $table->string('contacto_telefono')->nullable()->after('contacto_nombre');
            $table->string('contacto_relacion')->nullable()->after('contacto_telefono');
        });
    }

    public function down(): void
    {
        Schema::table('athletes', function (Blueprint $table) {
            $table->dropColumn([
                'tiene_seguro',
                'seguro_compania',
                'seguro_contacto',
                'relacion_contacto',
                'contacto_nombre',
                'contacto_telefono',
                'contacto_relacion',
            ]);
        });
    }
};
