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
        Schema::create('user_profile', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('profile_picture')->nullable();
            $table->string('age')->nullable();
            $table->string('weight')->nullable();
            $table->string('weight_parameter')->nullable();
            $table->string('height')->nullable();
            $table->string('height_parameter')->nullable();
            $table->string('gender', 7)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profile');
    }
};
