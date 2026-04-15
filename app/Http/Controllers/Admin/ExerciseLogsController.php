<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExerciseLog;
use App\Models\User;
use App\Models\WorkoutLog;
use Illuminate\Http\Request;

class ExerciseLogsController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'workout_id' => ['nullable', 'integer', 'exists:workout_logs,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date'],
            'per_page' => ['nullable', 'integer', 'in:10,15,25,50,100'],
        ]);

        $query = ExerciseLog::query()
            ->leftJoin('workout_logs', 'workout_logs.id', '=', 'exercise_logs.workout_id')
            ->leftJoin('users', 'users.id', '=', 'workout_logs.user_id')
            ->select([
                'exercise_logs.*',
                'workout_logs.user_id as workout_user_id',
                'workout_logs.start_time as workout_start_time',
                'workout_logs.end_time as workout_end_time',
                'users.first_name',
                'users.last_name',
                'users.email',
            ])
            ->orderByDesc('exercise_logs.id');

        if (!empty($filters['workout_id'])) {
            $query->where('exercise_logs.workout_id', (int) $filters['workout_id']);
        }
        if (!empty($filters['user_id'])) {
            $query->where('workout_logs.user_id', (int) $filters['user_id']);
        }
        if (!empty($filters['from_date'])) {
            $query->whereDate('workout_logs.start_time', '>=', $filters['from_date']);
        }
        if (!empty($filters['to_date'])) {
            $query->whereDate('workout_logs.start_time', '<=', $filters['to_date']);
        }

        return view('admin.exercise-logs.index', [
            'exerciseLogs' => $query->paginate((int) ($filters['per_page'] ?? 15))->appends($request->query()),
            'users' => User::query()->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'email']),
            'workoutLogs' => WorkoutLog::query()->orderByDesc('id')->limit(500)->get(['id', 'user_id', 'start_time']),
            'filters' => $filters,
        ]);
    }
}
