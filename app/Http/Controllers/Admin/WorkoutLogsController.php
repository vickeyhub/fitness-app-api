<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AuditTrailLogger;
use App\Models\User;
use App\Models\WorkoutLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WorkoutLogsController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'workout_type' => ['nullable', 'string', 'max:255'],
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date'],
            'per_page' => ['nullable', 'integer', 'in:10,15,25,50,100'],
        ]);

        $query = WorkoutLog::query()
            ->with('user:id,first_name,last_name,email')
            ->latest('id');

        if (!empty($filters['user_id'])) {
            $query->where('user_id', (int) $filters['user_id']);
        }
        if (!empty($filters['workout_type'])) {
            $query->where('workout_type', 'like', '%' . trim((string) $filters['workout_type']) . '%');
        }
        if (!empty($filters['from_date'])) {
            $query->whereDate('start_time', '>=', $filters['from_date']);
        }
        if (!empty($filters['to_date'])) {
            $query->whereDate('end_time', '<=', $filters['to_date']);
        }

        return view('admin.workout-logs.index', [
            'logs' => $query->paginate((int) ($filters['per_page'] ?? 15))->appends($request->query()),
            'users' => User::query()->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'email']),
            'filters' => $filters,
        ]);
    }

    public function show(WorkoutLog $workout_log)
    {
        return response()->json(['data' => $workout_log->load('user:id,first_name,last_name,email')]);
    }

    public function store(Request $request)
    {
        $data = $this->validatePayload($request);
        $data['duration_minutes'] = $this->resolveDurationMinutes($data);
        $log = WorkoutLog::create($data);
        AuditTrailLogger::log('workout_logs', 'create', $log, ['workout_id' => $log->workout_id, 'user_id' => $log->user_id]);
        return response()->json([
            'message' => 'Workout log created.',
            'data' => $log->load('user:id,first_name,last_name,email'),
        ], 201);
    }

    public function update(Request $request, WorkoutLog $workout_log)
    {
        $data = $this->validatePayload($request);
        $data['duration_minutes'] = $this->resolveDurationMinutes($data);
        $workout_log->update($data);
        AuditTrailLogger::log('workout_logs', 'update', $workout_log, ['workout_id' => $workout_log->workout_id, 'user_id' => $workout_log->user_id]);
        return response()->json([
            'message' => 'Workout log updated.',
            'data' => $workout_log->fresh()->load('user:id,first_name,last_name,email'),
        ]);
    }

    public function destroy(WorkoutLog $workout_log)
    {
        AuditTrailLogger::log('workout_logs', 'delete', $workout_log, ['workout_id' => $workout_log->workout_id, 'user_id' => $workout_log->user_id]);
        $workout_log->delete();
        return response()->json(['message' => 'Workout log deleted.']);
    }

    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'workout_id' => ['required', 'integer', 'exists:workout_plans,id'],
            'workout_type' => ['required', 'string', Rule::in(WorkoutLog::allowedTypes())],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after_or_equal:start_time'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'calories_burned' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function resolveDurationMinutes(array $data): int
    {
        if (!empty($data['duration_minutes'])) {
            return (int) $data['duration_minutes'];
        }

        return max(
            1,
            (int) round((strtotime((string) $data['end_time']) - strtotime((string) $data['start_time'])) / 60)
        );
    }
}
