<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\Support\AuditTrailLogger;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NutritionMealsController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'date' => ['nullable', 'date'],
            'meal_type' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'in:10,15,25,50,100'],
        ]);

        $query = Meal::query()->with('user:id,first_name,last_name,email')->latest('id');

        if (!empty($filters['user_id'])) {
            $query->where('user_id', (int) $filters['user_id']);
        }
        if (!empty($filters['date'])) {
            $query->whereDate('date', $filters['date']);
        }
        if (!empty($filters['meal_type'])) {
            $query->where('meal_type', 'like', '%' . trim((string) $filters['meal_type']) . '%');
        }

        return view('admin.nutrition.meals', [
            'meals' => $query->paginate((int) ($filters['per_page'] ?? 15))->appends($request->query()),
            'users' => User::query()->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'email']),
            'filters' => $filters,
        ]);
    }

    public function show(Meal $nutrition_meal)
    {
        return response()->json(['data' => $nutrition_meal->load('user:id,first_name,last_name,email')]);
    }

    public function store(Request $request)
    {
        $data = $this->validatePayload($request);
        $meal = Meal::create($data);
        AuditTrailLogger::log('nutrition_meals', 'create', $meal, ['user_id' => $meal->user_id, 'meal_type' => $meal->meal_type, 'date' => $meal->date]);
        return response()->json(['message' => 'Meal created.', 'data' => $meal->load('user:id,first_name,last_name,email')], 201);
    }

    public function update(Request $request, Meal $nutrition_meal)
    {
        $data = $this->validatePayload($request, $nutrition_meal->id);
        $nutrition_meal->update($data);
        AuditTrailLogger::log('nutrition_meals', 'update', $nutrition_meal, ['user_id' => $nutrition_meal->user_id, 'meal_type' => $nutrition_meal->meal_type, 'date' => $nutrition_meal->date]);
        return response()->json(['message' => 'Meal updated.', 'data' => $nutrition_meal->fresh()->load('user:id,first_name,last_name,email')]);
    }

    public function destroy(Meal $nutrition_meal)
    {
        AuditTrailLogger::log('nutrition_meals', 'delete', $nutrition_meal, ['user_id' => $nutrition_meal->user_id, 'meal_type' => $nutrition_meal->meal_type, 'date' => $nutrition_meal->date]);
        $nutrition_meal->delete();
        return response()->json(['message' => 'Meal deleted.']);
    }

    private function validatePayload(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
                Rule::unique('meals', 'user_id')
                    ->ignore($ignoreId)
                    ->where('date', (string) $request->input('date'))
                    ->where('meal_type', (string) $request->input('meal_type')),
            ],
            'date' => ['required', 'date'],
            'meal_type' => ['required', 'string', 'max:255'],
            'proteins' => ['required', 'integer', 'min:0'],
            'fats' => ['required', 'integer', 'min:0'],
            'carbs' => ['required', 'integer', 'min:0'],
            'calories' => ['required', 'integer', 'min:0'],
        ], [
            'user_id.unique' => 'A meal for this user, date, and meal type already exists. Use edit instead of creating a duplicate.',
        ]);
    }
}
