<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\NutritionTarget;

class NutritionController extends Controller
{
    public function addOrUpdateMeal(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'mealType' => 'required|string',
            'proteins' => 'required|integer|min:0',
            'fats' => 'required|integer|min:0',
            'carbs' => 'required|integer|min:0',
            'calories' => 'required|integer|min:0',
        ]);

        // $meal = Meal::updateOrCreate(
        //     ['user_id' => Auth::id(), 'date' => $request->date, 'meal_type' => $request->mealType],
        //     $request->only('proteins', 'fats', 'carbs', 'calories')
        // );
        $userId = Auth::id();

        $meal = Meal::updateOrCreate(
            [
                'user_id' => $userId,
                'date' => $request->date,
                'meal_type' => $request->mealType,
            ],
            [
                'proteins' => (int) $request->proteins,
                'fats' => (int) $request->fats,
                'carbs' => (int) $request->carbs,
                'calories' => (int) $request->calories,
            ]
        );

        return response()->json(['success' => true, 'message' => 'Meal saved successfully.', 'meal' => $meal]);
    }

    // Get Daily Summary
    public function getNutritionByDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        $userId = Auth::id();
        $date = $request->date;

        // Fetch all meals for the user on this date
        $meals = Meal::where('user_id', $userId)
            ->whereDate('date', $date)
            ->get();

        $consumed = [
            'calories' => $meals->sum('calories'),
            'proteins' => $meals->sum('proteins'),
            'fats' => $meals->sum('fats'),
            'carbs' => $meals->sum('carbs')
        ];

        // Fetch dynamic targets
        $target = $this->getUserNutritionTargets($userId);

        return response()->json([
            'success' => true,
            'date' => $date,
            'targetCalories' => $target->calories,
            'consumedCalories' => $consumed['calories'],
            'macros' => [
                'proteins' => $target->proteins,
                'fats' => $target->fats,
                'carbs' => $target->carbs
            ],
            'meals' => $meals
        ]);
    }
    private function getUserNutritionTargets($userId)
    {
        return NutritionTarget::where('user_id', $userId)->first([
            'calories',
            'proteins',
            'fats',
            'carbs'
        ]) ?? (object) [
                'calories' => 2000,
                'proteins' => 100,
                'fats' => 70,
                'carbs' => 250
            ];
    }


    // Delete Meal Entry
    public function deleteMeal(Request $request)
    {
        try {
            $request->validate([
                'date' => 'required|date',
                'mealType' => 'required|string'
            ]);

            $deleted = Meal::where([
                'user_id' => Auth::id(),
                'date' => $request->date,
                'meal_type' => $request->mealType
            ])->delete();

            return response()->json([
                'success' => true,
                'message' => $deleted ? 'Meal deleted successfully.' : 'Meal not found.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Set or Update User's Target
    public function setTargets(Request $request)
    {
        $request->validate([
            'calories' => 'required|integer',
            'proteins' => 'required|integer',
            'fats' => 'required|integer',
            'carbs' => 'required|integer',
        ]);

        $userId = Auth::id();

        $target = NutritionTarget::updateOrCreate(
            ['user_id' => $userId],
            $request->only('calories', 'proteins', 'fats', 'carbs')
        );

        return response()->json([
            'success' => true,
            'message' => 'Targets set successfully',
            'targets' => $target
        ]);
    }

    public function getTargets()
    {
        $userId = Auth::id();

        $target = NutritionTarget::where('user_id', $userId)->first();

        if (!$target) {
            return response()->json([
                'success' => false,
                'message' => 'No target set yet.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'targets' => $target
        ]);
    }
}
