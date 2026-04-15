<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('session_catalog_items', function (Blueprint $table) {
            $table->id();
            $table->string('type', 32);
            $table->string('name');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['type', 'name']);
        });

        $now = now();
        $rows = [];

        foreach (['Chest', 'Back', 'Shoulders', 'Arms', 'Legs', 'Core', 'Glutes', 'Full body'] as $i => $name) {
            $rows[] = ['type' => 'muscle', 'name' => $name, 'sort_order' => $i, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now];
        }
        foreach (['Weight loss', 'Muscle gain', 'Endurance', 'Mobility', 'General fitness', 'Sports performance'] as $i => $name) {
            $rows[] = ['type' => 'fitness_goal', 'name' => $name, 'sort_order' => $i, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now];
        }
        foreach (['HIIT', 'Strength', 'Yoga', 'Pilates', 'Cardio', 'CrossFit', 'Stretching', 'Boxing'] as $i => $name) {
            $rows[] = ['type' => 'session_type', 'name' => $name, 'sort_order' => $i, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now];
        }
        foreach (['Beginner friendly', 'Low impact', 'High intensity', 'Equipment required', 'No equipment', 'Outdoor', 'Indoor', 'Group class'] as $i => $name) {
            $rows[] = ['type' => 'keyword', 'name' => $name, 'sort_order' => $i, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now];
        }

        DB::table('session_catalog_items')->insert($rows);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_catalog_items');
    }
};
