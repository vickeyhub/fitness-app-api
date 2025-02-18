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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('session_title');
            $table->text('description');
            $table->integer('duration'); // in minutes
            $table->integer('total_duration'); // in weeks
            $table->integer('calories');
            $table->json('steps');
            $table->json('muscles_involved');
            $table->json('schedule'); // MON, WED, FRI
            $table->string('user_id'); // Trainer ID
            $table->decimal('price', 8, 2);
            $table->string('session_thumbnail');
            $table->float('session_avrage_rating')->default(0);
            $table->string('session_timing'); // Time Range (e.g., 04:00-05:00)
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
