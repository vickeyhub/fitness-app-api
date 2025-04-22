<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tag;

class TagController extends Controller
{
    public function index()
    {

        return response()->json([
            'status' => 'success',
            'message' => 'All tags fetched',
            'data' => Tag::all(),
            // $post->load('tags')
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|unique:tags,name|string|max:50',
            ]);

            $tag = Tag::create($data);
            return response()->json([
                'status' => 'success',
                'message' => 'tag generated.',
                'data' => $tag,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
