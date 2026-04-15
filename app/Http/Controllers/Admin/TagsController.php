<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TagsController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'in:10,15,25,50,100'],
        ]);

        $query = Tag::query()->withCount('posts')->latest('id');

        if (!empty($filters['q'])) {
            $query->where('name', 'like', '%' . trim((string) $filters['q']) . '%');
        }

        return view('admin.tags.index', [
            'tags' => $query->paginate((int) ($filters['per_page'] ?? 15))->appends($request->query()),
            'filters' => $filters,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:tags,name'],
        ]);

        $tag = Tag::create($data);

        return response()->json([
            'message' => 'Tag created successfully.',
            'data' => $tag,
        ], 201);
    }

    public function update(Request $request, Tag $tag)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', Rule::unique('tags', 'name')->ignore($tag->id)],
        ]);

        $tag->update($data);

        return response()->json([
            'message' => 'Tag updated successfully.',
            'data' => $tag,
        ]);
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        return response()->json([
            'message' => 'Tag deleted successfully.',
        ]);
    }
}
