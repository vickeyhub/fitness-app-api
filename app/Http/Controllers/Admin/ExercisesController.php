<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use App\Models\ExerciseCategory;
use Illuminate\Http\Request;

class ExercisesController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'exercise_category_id' => ['nullable', 'integer', 'exists:exercise_categories,id'],
            'per_page' => ['nullable', 'integer', 'in:10,15,25,50,100'],
        ]);

        $query = Exercise::query()->with('category:id,name')->latest('id');
        if (!empty($filters['q'])) {
            $q = trim((string) $filters['q']);
            $query->where(function ($inner) use ($q) {
                $inner->where('name', 'like', "%{$q}%")->orWhere('description', 'like', "%{$q}%");
            });
        }
        if (!empty($filters['exercise_category_id'])) {
            $query->where('exercise_category_id', (int) $filters['exercise_category_id']);
        }

        return view('admin.exercises.index', [
            'exercises' => $query->paginate((int) ($filters['per_page'] ?? 15))->appends($request->query()),
            'categories' => ExerciseCategory::query()->orderBy('name')->get(['id', 'name']),
            'filters' => $filters,
        ]);
    }

    public function show(Exercise $exercise)
    {
        return response()->json(['data' => $exercise->load('category:id,name')]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'exercise_category_id' => ['required', 'integer', 'exists:exercise_categories,id'],
            'description' => ['nullable', 'string'],
        ]);
        $exercise = Exercise::create($data);
        return response()->json(['message' => 'Exercise created.', 'data' => $exercise->load('category:id,name')], 201);
    }

    public function update(Request $request, Exercise $exercise)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'exercise_category_id' => ['required', 'integer', 'exists:exercise_categories,id'],
            'description' => ['nullable', 'string'],
        ]);
        $exercise->update($data);
        return response()->json(['message' => 'Exercise updated.', 'data' => $exercise->load('category:id,name')]);
    }

    public function destroy(Exercise $exercise)
    {
        $exercise->delete();
        return response()->json(['message' => 'Exercise deleted.']);
    }
}
