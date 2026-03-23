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
            $table->string('apellido_paterno')->after('nombre')->nullable();
            $table->string('apellido_materno')->after('apellido_paterno')->nullable();
            
            $table->string('nombre_padre')->after('alergias')->nullable();
            $table->string('apellido_paterno_padre')->after('nombre_padre')->nullable();
            $table->string('apellido_materno_padre')->after('apellido_paterno_padre')->nullable();
            $table->string('telefono_padre')->after('apellido_materno_padre')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('athletes', function (Blueprint $table) {
            $table->dropColumn([
                'apellido_paterno',
                'apellido_materno',
                'nombre_padre',
                'apellido_paterno_padre',
                'apellido_materno_padre',
                'telefono_padre'
            ]);
        });
    }
};
