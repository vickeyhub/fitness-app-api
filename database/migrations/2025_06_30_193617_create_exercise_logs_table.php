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
        Schema::create('exercise_logs', function (Blueprint $table) {
            $table->id();
            $table->string('workout_id');
            $table->string('exercise_name');
            $table->integer('sets');
            $table->integer('reps_per_set');
            $table->float('weight_kg')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->float('distance_km')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // $table->foreign('workout_id')->references('id')->on('workouts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercise_logs');
    }
};
