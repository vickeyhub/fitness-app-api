<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkoutLog;
use App\Models\ExerciseLog;
use App\Models\WorkoutPlan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class WorkoutLogController extends Controller
{
    public function log(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'workout_type' => ['required', 'string', Rule::in(WorkoutLog::allowedTypes())],
            'workout_id' => ['required', 'integer'],
            'start_time' => 'required',
            'end_time' => 'required',
            'duration_minutes' => 'nullable|integer|min:1',
            'calories_burned' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => array_values($validator->errors()->all())
            ], 422);
        }

        $plan = WorkoutPlan::query()
            ->where('id', (int) $request->workout_id)
            ->where('user_id', Auth::id())
            ->first();
        if (!$plan) {
            return response()->json([
                'success' => false,
                'errors' => ['Workout plan not found for the current user.']
            ], 422);
        }

        $duration = $request->duration_minutes
            ? (int) $request->duration_minutes
            : max(1, (int) round((strtotime((string) $request->end_time) - strtotime((string) $request->start_time)) / 60));

        $log = WorkoutLog::create([
            'user_id' => Auth::id(),
            'workout_type' => $request->workout_type,
            'workout_id' => (string) $request->workout_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration_minutes' => $duration,
            'calories_burned' => $request->calories_burned,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Workout logged successfully',
            'workout_id' => $log->workout_id,
        ], 201);
    }

    public function exercise_log(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'workout_id' => 'required|exists:workout_plan_exercises,workout_plan_id',
            'exercise_name' => 'required|string|max:255',
            'sets' => 'required|integer|min:1',
            'reps_per_set' => 'required|integer|min:1',
            'weight_kg' => 'nullable|numeric|min:0',
            'duration_seconds' => 'nullable|integer|min:0',
            'distance_km' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => array_values($validator->errors()->all()),
            ], 422);
        }

        $exercise = ExerciseLog::create($validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Execise logged successfully',
            'exercise_log_id' => $exercise->id
        ], 201);
    }

    public function getHistory(Request $request)
    {
        try {
            $validated = $request->validate([
                // 'user_id' => 'required|exists:users,id',
                'from_date' => 'nullable|date',
                'to_date' => 'nullable|date',
            ]);

            $query = WorkoutLog::where('user_id', Auth::id());

            if (!empty($validated['from_date'])) {
                $query->whereDate('start_time', '>=', $validated['from_date']);
            }

            if (!empty($validated['to_date'])) {
                $query->whereDate('end_time', '<=', $validated['to_date']);
            }

            $workouts = $query->orderByDesc('start_time')->get([
                'id as workout_id',
                'workout_type',
                'start_time',
                'end_time',
                'duration_minutes',
                'calories_burned',
            ]);

            return response()->json([
                'status' => "success",
                'data' => $workouts
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while fetching history.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
