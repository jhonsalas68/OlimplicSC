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
            $table->string('ci_anverso')->nullable()->after('foto');
            $table->string('ci_reverso')->nullable()->after('ci_anverso');
            $table->boolean('tiene_carnet_atleta')->default(false)->after('ci_reverso');
            $table->string('carnet_atleta_anverso')->nullable()->after('tiene_carnet_atleta');
            $table->string('carnet_atleta_reverso')->nullable()->after('carnet_atleta_anverso');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('athletes', function (Blueprint $table) {
            $table->dropColumn([
                'ci_anverso',
                'ci_reverso',
                'tiene_carnet_atleta',
                'carnet_atleta_anverso',
                'carnet_atleta_reverso',
            ]);
        });
    }
};
