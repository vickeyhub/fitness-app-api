<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExerciseCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExerciseCategoriesController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'in:10,15,25,50,100'],
        ]);

        $query = ExerciseCategory::query()->withCount('exercises')->latest('id');
        if (!empty($filters['q'])) {
            $query->where('name', 'like', '%' . trim((string) $filters['q']) . '%');
        }

        return view('admin.exercise-categories.index', [
            'categories' => $query->paginate((int) ($filters['per_page'] ?? 15))->appends($request->query()),
            'filters' => $filters,
        ]);
    }

    public function show(ExerciseCategory $exercise_category)
    {
        return response()->json(['data' => $exercise_category]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:exercise_categories,name'],
        ]);

        $category = ExerciseCategory::create($data);
        return response()->json(['message' => 'Category created.', 'data' => $category], 201);
    }

    public function update(Request $request, ExerciseCategory $exercise_category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('exercise_categories', 'name')->ignore($exercise_category->id)],
        ]);

        $exercise_category->update($data);
        return response()->json(['message' => 'Category updated.', 'data' => $exercise_category]);
    }

    public function destroy(ExerciseCategory $exercise_category)
    {
        if ($exercise_category->exercises()->exists()) {
            return response()->json(['message' => 'Cannot delete category with linked exercises.'], 422);
        }
        $exercise_category->delete();
        return response()->json(['message' => 'Category deleted.']);
    }
}
