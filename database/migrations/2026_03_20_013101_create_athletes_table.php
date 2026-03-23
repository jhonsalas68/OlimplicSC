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
        Schema::create('athletes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->string('ci')->unique();
            $table->string('nombre');
            $table->string('foto')->nullable();
            $table->date('fecha_nacimiento');
            $table->boolean('habilitado_booleano')->default(false);
            $table->string('id_alfanumerico_unico')->unique();
            $table->text('contactos_padres')->nullable();
            $table->string('seguro_medico')->nullable();
            $table->text('alergias')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('athletes');
    }
};
