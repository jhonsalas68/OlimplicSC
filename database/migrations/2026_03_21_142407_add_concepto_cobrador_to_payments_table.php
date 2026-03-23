<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // 'mensualidad' o 'articulo_deportivo'
            $table->string('concepto')->default('mensualidad')->after('estado_pago');
            $table->string('descripcion')->nullable()->after('concepto');
            $table->unsignedBigInteger('cobrado_por')->nullable()->after('descripcion');
            $table->foreign('cobrado_por')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['cobrado_por']);
            $table->dropColumn(['concepto', 'descripcion', 'cobrado_por']);
        });
    }
};
