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
        Schema::table('user_profile', function (Blueprint $table) {
            // New fields for trainers
            $table->string('dob')->after('age')->nullable();
            $table->string('location')->nullable();  // Location of the trainer
            $table->float('rating')->nullable();     // Rating of the trainer
            $table->text('specialty')->nullable();   // Specialty or expertise of the trainer
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profile', function (Blueprint $table) {
            $table->dropColumn(['dob', 'location', 'rating', 'specialty']);
        });
    }
};
