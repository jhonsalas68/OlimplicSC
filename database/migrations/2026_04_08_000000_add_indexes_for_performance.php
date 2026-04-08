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
            $table->index('ci');
            $table->index('nombre');
            $table->index('apellido_paterno');
            $table->index('category_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index('athlete_id');
            $table->index('estado_pago');
            $table->index('metodo_pago');
            $table->index('concepto');
            $table->index('created_at');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index('edad_min');
            $table->index('edad_max');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('athletes', function (Blueprint $table) {
            $table->dropIndex(['ci']);
            $table->dropIndex(['nombre']);
            $table->dropIndex(['apellido_paterno']);
            $table->dropIndex(['category_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['athlete_id']);
            $table->dropIndex(['estado_pago']);
            $table->dropIndex(['metodo_pago']);
            $table->dropIndex(['concepto']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['edad_min']);
            $table->dropIndex(['edad_max']);
        });
    }
};
