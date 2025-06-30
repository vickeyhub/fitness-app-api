<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkoutLog;
use App\Models\ExerciseLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WorkoutLogController extends Controller
{
    public function log(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'workout_type' => 'required|string',
            'workout_id' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required',
            'duration_minutes' => 'required|integer|min:1',
            'calories_burned' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => array_values($validator->errors()->all())
            ], 422);
        }

        $log = WorkoutLog::create([
            'user_id' => Auth::id(),
            'workout_type' => $request->workout_type,
            'workout_id' => $request->workout_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration_minutes' => $request->duration_minutes,
            'calories_burned' => $request->calories_burned,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Workout logged successfully',
            'workout_id' => $log->workout_id,
        ],201);
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
        ],201);
    }
}
