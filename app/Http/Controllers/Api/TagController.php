<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tag;

class TagController extends Controller
{
    public function index()
    {
        return response()->json(Tag::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|unique:tags,name|string|max:50',
        ]);

        $tag = Tag::create($data);
        return response()->json($tag, 201);
    }
}
