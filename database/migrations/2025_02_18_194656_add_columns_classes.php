<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {

            $table->json('session_type');
            $table->json('session_keywords');
            $table->json('fitness_goal');
            $table->string('intensity');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn([
                'session_type',
                'session_keywords',
                'fitness_goal',
                'intensity'
            ]);
        });
    }
};
