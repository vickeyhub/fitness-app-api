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
        Schema::table('user_profile', function (Blueprint $table) {
            // Rename the column to the correct spelling ('specialties')
            if (Schema::hasColumn('user_profile', 'specialty')) {
                $table->renameColumn('specialty', 'specialties');
            }

            // Add new columns (use 'text' for trainer_services if not using MySQL JSON)
            if (!Schema::hasColumn('user_profile', 'trainer_services')) {
                $table->text('trainer_services')->nullable();
            }
            if (!Schema::hasColumn('user_profile', 'user_description')) {
                $table->text('user_description')->nullable();
            }
            if (!Schema::hasColumn('user_profile', 'experience_level')) {
                $table->enum('experience_level', ['beginner', 'intermediate', 'advanced'])->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profile', function (Blueprint $table) {
            // Revert the column name if it exists
            if (Schema::hasColumn('user_profile', 'specialties')) {
                $table->renameColumn('specialties', 'specility');
            }

            // Drop added columns
            if (Schema::hasColumn('user_profile', 'trainer_services')) {
                $table->dropColumn('trainer_services');
            }
            if (Schema::hasColumn('user_profile', 'user_description')) {
                $table->dropColumn('user_description');
            }
            if (Schema::hasColumn('user_profile', 'experience_level')) {
                $table->dropColumn('experience_level');
            }
        });
    }

};
