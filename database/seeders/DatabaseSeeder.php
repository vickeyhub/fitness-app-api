<?php

namespace Database\Seeders;

use App\Models\User;
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
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);


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
