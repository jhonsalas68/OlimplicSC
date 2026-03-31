<?php
/**
 * Migration to remove the id_alfanumerico_unico column from the athletes table.
 */
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
            if (Schema::hasColumn('athletes', 'id_alfanumerico_unico')) {
                $table->dropColumn('id_alfanumerico_unico');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('athletes', function (Blueprint $table) {
            $table->string('id_alfanumerico_unico')->unique()->nullable();
        });
    }
};
