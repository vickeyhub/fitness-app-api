<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExerciseCategory;
use App\Models\Exercise;

class ExerciseController extends Controller
{
    public function categories()
    {
        try {
            $categories = ExerciseCategory::pluck('name');
            return response()->json([
                'status' => 'success',
                'data' => $categories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch categories'
            ], 500);
        }
    }

    public function getByCategory(Request $request)
    {
        $category = $request->query('category');

        $categoryModel = ExerciseCategory::where('name', $category)->first();
        if (!$categoryModel) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid category'
            ], 400);
        }

        $exercises = Exercise::where('exercise_category_id', $categoryModel->id)->get()
            ->map(function ($exercise) use ($category) {
                return [
                    'id' => $exercise->id,
                    'name' => $exercise->name,
                    'category' => $category,
                    'description' => $exercise->description,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $exercises
        ]);
    }
}
