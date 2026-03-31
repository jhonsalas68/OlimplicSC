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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action'); // e.g., 'login', 'create_athlete', 'payment_registered'
            $table->text('description'); // e.g., 'Juan Perez inscrita nuevo atleta: Maria Garcia'
            $table->string('model_type')->nullable(); // Contexto del modelo (Atleta, Pago, etc.)
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('properties')->nullable(); // Datos adicionales (ej. cambios)
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            // Índices para búsquedas rápidas
            $table->index(['model_type', 'model_id']);
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
