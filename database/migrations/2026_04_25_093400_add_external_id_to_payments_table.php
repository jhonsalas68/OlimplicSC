<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->uuid('external_id')->nullable()->unique();
        });

        // Populate existing payments with UUIDs
        $payments = \DB::table('payments')->whereNull('external_id')->get();
        foreach ($payments as $payment) {
            \DB::table('payments')->where('id', $payment->id)->update([
                'external_id' => (string) Str::uuid()
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('external_id');
        });
    }
};
