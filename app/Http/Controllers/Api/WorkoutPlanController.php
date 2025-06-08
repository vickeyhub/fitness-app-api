<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanExercise;
use App\Models\Exercise;
use Illuminate\Support\Facades\Validator;

class WorkoutPlanController extends Controller
{
    public function index()
    {
        try {
            $plans = WorkoutPlan::with([
                'exercises' => function ($query) {
                    $query->select('id', 'workout_plan_id', 'exercise_id', 'sets', 'reps', 'rest_seconds', 'weight');
                }
            ])
                ->where('user_id', auth()->id())
                ->select('id', 'name')
                ->paginate(10); // adjust per page as needed

            $formattedPlans = $plans->getCollection()->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'exercises' => $plan->exercises->map(function ($exercise) {
                        return [
                            'exerciseId' => $exercise->exercise_id,
                            'sets' => $exercise->sets,
                            'reps' => $exercise->reps,
                            'restSeconds' => $exercise->rest_seconds,
                            'weight' => $exercise->weight,
                        ];
                    })
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Workout plans fetched successfully',
                'data' => $formattedPlans,
                'pagination' => [
                    'current_page' => $plans->currentPage(),
                    'total' => $plans->total(),
                    'per_page' => $plans->perPage(),
                    'last_page' => $plans->lastPage(),
                ]
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch workout plans',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'exercises' => 'required|array|min:1',
            'exercises.*.exerciseId' => 'required|integer|exists:exercises,id',
            'exercises.*.sets' => 'required|integer|min:1',
            'exercises.*.reps' => 'required|integer|min:1',
            'exercises.*.restSeconds' => 'required|integer|min:0',
            'exercises.*.weight' => 'nullable|numeric|min:0',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid exercise data',
                'errors' => $validated->errors()
            ], 400);
        }

        try {
            $plan = WorkoutPlan::create([
                'user_id' => auth()->id(),
                'name' => $request->name
            ]);

            foreach ($request->exercises as $item) {
                WorkoutPlanExercise::create([
                    'workout_plan_id' => $plan->id,
                    'exercise_id' => $item['exerciseId'],
                    'sets' => $item['sets'],
                    'reps' => $item['reps'],
                    'rest_seconds' => $item['restSeconds'],
                    'weight' => $item['weight'] ?? null,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'exercises' => $request->exercises
                ]
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $plan = WorkoutPlan::with(['exercises.exercise.category'])
                ->where('user_id', auth()->id())
                ->find($id);

            if (!$plan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Workout plan not found',
                ], 404);
            }

            $formatted = [
                'id' => $plan->id,
                'name' => $plan->name,
                'exercises' => $plan->exercises->map(function ($item) {
                    return [
                        'exerciseId' => $item->exercise_id,
                        'name' => $item->exercise->name ?? '',
                        'category' => $item->exercise->category->name ?? '',
                        'description' => $item->exercise->description ?? '',
                        'sets' => $item->sets,
                        'reps' => $item->reps,
                        'restSeconds' => $item->rest_seconds,
                        'weight' => $item->weight,
                    ];
                }),
            ];

            return response()->json([
                'status' => 'success',
                'data' => $formatted,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
