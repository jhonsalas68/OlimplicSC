<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable();
            $table->boolean('is_active')->default(false);
            $table->string('ci')->nullable();
            $table->string('last_name')->nullable();
        });
        
        // Ensure existing users have a username
        DB::statement("UPDATE users SET username = 'user_' || id WHERE username IS NULL");
        
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable(false)->change();
            $table->dropUnique('users_email_unique');
            $table->string('email')->nullable()->change();
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'is_active', 'ci', 'last_name']);
            $table->string('email')->nullable(false)->change();
            $table->unique('email');
        });
    }
};
