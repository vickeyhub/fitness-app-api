<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ExerciseCategory;
use App\Models\Exercise;

class ExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Chest' => [
                ['name' => 'Bench Press', 'description' => 'Barbell chest press'],
                ['name' => 'Push-ups', 'description' => 'Bodyweight chest exercise'],
            ],
            'Back' => [
                ['name' => 'Pull-ups', 'description' => 'Bodyweight back exercise'],
                ['name' => 'Deadlift', 'description' => 'Barbell back and leg exercise'],
            ],
            'Legs' => [
                ['name' => 'Squats', 'description' => 'Barbell leg exercise'],
                ['name' => 'Lunges', 'description' => 'Bodyweight or dumbbell leg exercise'],
            ],
            'Arms' => [
                ['name' => 'Bicep Curls', 'description' => 'Dumbbell or barbell arm exercise'],
                ['name' => 'Tricep Dips', 'description' => 'Bodyweight tricep exercise'],
            ],
            'Core' => [
                ['name' => 'Plank', 'description' => 'Core stability exercise'],
                ['name' => 'Sit-ups', 'description' => 'Bodyweight core exercise'],
            ],
            'Others' => [
                ['name' => 'Jump Rope', 'description' => 'Cardio warm-up exercise'],
                ['name' => 'Burpees', 'description' => 'Full-body conditioning exercise'],
            ],
        ];

        foreach ($data as $category => $exercises) {
            $categoryModel = ExerciseCategory::where('name', $category)->first();
            if ($categoryModel) {
                foreach ($exercises as $exercise) {
                    Exercise::create([
                        'name' => $exercise['name'],
                        'description' => $exercise['description'],
                        'exercise_category_id' => $categoryModel->id,
                    ]);
                }
            }
        }
    }
}
