<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NutritionTarget;
use App\Support\AuditTrailLogger;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NutritionTargetsController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'q' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'in:10,15,25,50,100'],
        ]);

        $query = NutritionTarget::query()->with('user:id,first_name,last_name,email')->latest('id');
        if (!empty($filters['user_id'])) {
            $query->where('user_id', (int) $filters['user_id']);
        }
        if (!empty($filters['q'])) {
            $q = trim((string) $filters['q']);
            $query->whereHas('user', function ($inner) use ($q) {
                $inner->where('first_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        return view('admin.nutrition.targets', [
            'targets' => $query->paginate((int) ($filters['per_page'] ?? 15))->appends($request->query()),
            'users' => User::query()->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'email']),
            'filters' => $filters,
        ]);
    }

    public function show(NutritionTarget $nutrition_target)
    {
        return response()->json(['data' => $nutrition_target->load('user:id,first_name,last_name,email')]);
    }

    public function store(Request $request)
    {
        $data = $this->validatePayload($request);
        $target = NutritionTarget::updateOrCreate(
            ['user_id' => $data['user_id']],
            $data
        );
        AuditTrailLogger::log('nutrition_targets', 'upsert', $target, ['user_id' => $target->user_id]);

        return response()->json(['message' => 'Nutrition target saved.', 'data' => $target->load('user:id,first_name,last_name,email')], 201);
    }

    public function update(Request $request, NutritionTarget $nutrition_target)
    {
        $data = $this->validatePayload($request, $nutrition_target->id);
        $nutrition_target->update($data);
        AuditTrailLogger::log('nutrition_targets', 'update', $nutrition_target, ['user_id' => $nutrition_target->user_id]);
        return response()->json(['message' => 'Nutrition target updated.', 'data' => $nutrition_target->fresh()->load('user:id,first_name,last_name,email')]);
    }

    public function destroy(NutritionTarget $nutrition_target)
    {
        AuditTrailLogger::log('nutrition_targets', 'delete', $nutrition_target, ['user_id' => $nutrition_target->user_id]);
        $nutrition_target->delete();
        return response()->json(['message' => 'Nutrition target deleted.']);
    }

    private function validatePayload(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id', Rule::unique('nutrition_targets', 'user_id')->ignore($ignoreId)],
            'calories' => ['required', 'integer', 'min:0'],
            'proteins' => ['required', 'integer', 'min:0'],
            'fats' => ['required', 'integer', 'min:0'],
            'carbs' => ['required', 'integer', 'min:0'],
        ], [
            'user_id.unique' => 'Nutrition target already exists for this user. Edit the existing target instead.',
        ]);
    }
}
