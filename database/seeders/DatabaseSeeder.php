<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ExerciseCategory;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);

        ExerciseCategory::insert([
            ['name' => 'Chest'],
            ['name' => 'Back'],
            ['name' => 'Legs'],
            ['name' => 'Arms'],
            ['name' => 'Core'],
            ['name' => 'Others'],
        ]);
        $this->call(ExerciseSeeder::class);
    }
}
