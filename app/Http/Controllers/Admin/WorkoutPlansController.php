<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AuditTrailLogger;
use App\Models\Exercise;
use App\Models\User;
use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanExercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkoutPlansController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'per_page' => ['nullable', 'integer', 'in:10,15,25,50,100'],
        ]);

        $query = WorkoutPlan::query()
            ->with(['user:id,first_name,last_name,email', 'exercises.exercise:id,name'])
            ->withCount('exercises')
            ->latest('id');

        if (!empty($filters['q'])) {
            $query->where('name', 'like', '%' . trim((string) $filters['q']) . '%');
        }
        if (!empty($filters['user_id'])) {
            $query->where('user_id', (int) $filters['user_id']);
        }

        return view('admin.workout-plans.index', [
            'plans' => $query->paginate((int) ($filters['per_page'] ?? 15))->appends($request->query()),
            'users' => User::query()->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'email']),
            'exercises' => Exercise::query()->orderBy('name')->get(['id', 'name']),
            'filters' => $filters,
        ]);
    }

    public function show(WorkoutPlan $workout_plan)
    {
        $workout_plan->load([
            'user:id,first_name,last_name,email',
            'exercises.exercise:id,name',
        ]);

        return response()->json(['data' => $workout_plan]);
    }

    public function store(Request $request)
    {
        $data = $this->validatePayload($request);

        $plan = DB::transaction(function () use ($data) {
            $plan = WorkoutPlan::create([
                'user_id' => $data['user_id'],
                'name' => $data['name'],
            ]);

            foreach ($data['exercises'] as $line) {
                WorkoutPlanExercise::create([
                    'workout_plan_id' => $plan->id,
                    'exercise_id' => (int) $line['exercise_id'],
                    'sets' => (int) $line['sets'],
                    'reps' => (int) $line['reps'],
                    'rest_seconds' => (int) $line['rest_seconds'],
                    'weight' => isset($line['weight']) && $line['weight'] !== '' ? (float) $line['weight'] : null,
                ]);
            }

            return $plan;
        });

        AuditTrailLogger::log('workout_plans', 'create', $plan, ['name' => $plan->name, 'user_id' => $plan->user_id]);
        return response()->json([
            'message' => 'Workout plan created.',
            'data' => $plan->load(['user:id,first_name,last_name,email', 'exercises.exercise:id,name']),
        ], 201);
    }

    public function update(Request $request, WorkoutPlan $workout_plan)
    {
        $data = $this->validatePayload($request);

        DB::transaction(function () use ($data, $workout_plan) {
            $workout_plan->update([
                'user_id' => $data['user_id'],
                'name' => $data['name'],
            ]);

            $workout_plan->exercises()->delete();
            foreach ($data['exercises'] as $line) {
                WorkoutPlanExercise::create([
                    'workout_plan_id' => $workout_plan->id,
                    'exercise_id' => (int) $line['exercise_id'],
                    'sets' => (int) $line['sets'],
                    'reps' => (int) $line['reps'],
                    'rest_seconds' => (int) $line['rest_seconds'],
                    'weight' => isset($line['weight']) && $line['weight'] !== '' ? (float) $line['weight'] : null,
                ]);
            }
        });

        AuditTrailLogger::log('workout_plans', 'update', $workout_plan, ['name' => $workout_plan->name, 'user_id' => $workout_plan->user_id]);
        return response()->json([
            'message' => 'Workout plan updated.',
            'data' => $workout_plan->fresh()->load(['user:id,first_name,last_name,email', 'exercises.exercise:id,name']),
        ]);
    }

    public function destroy(WorkoutPlan $workout_plan)
    {
        AuditTrailLogger::log('workout_plans', 'delete', $workout_plan, ['name' => $workout_plan->name, 'user_id' => $workout_plan->user_id]);
        DB::transaction(function () use ($workout_plan) {
            $workout_plan->exercises()->delete();
            $workout_plan->delete();
        });

        return response()->json(['message' => 'Workout plan deleted.']);
    }

    private function validatePayload(Request $request): array
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'exercises' => ['required', 'array', 'min:1'],
            'exercises.*.exercise_id' => ['required', 'integer', 'exists:exercises,id'],
            'exercises.*.sets' => ['required', 'integer', 'min:1'],
            'exercises.*.reps' => ['required', 'integer', 'min:1'],
            'exercises.*.rest_seconds' => ['required', 'integer', 'min:0'],
            'exercises.*.weight' => ['nullable', 'numeric', 'min:0'],
        ]);

        return $data;
    }
}
