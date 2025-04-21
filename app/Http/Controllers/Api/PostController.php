<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index()
    {
        $post = Post::with([
            'user:id,first_name,last_name,email,user_type',
            'user.profile:id,user_id,profile_picture,gender,specialty',
            'tags',
            'likes',
            'comments'
            ])->latest()->paginate(20);

            return response()->json([
                'status' => 'success',
                'message' => 'All post fetched',
                'data' => $post->items(),
                'pagination' => [
                    'current_page' => $post->currentPage(),
                    'total' => $post->total(),
                    'per_page' => $post->perPage(),
                    'last_page' => $post->lastPage(),
                ]
            ], 200);

    }

    public function show($id)
{
    $post = Post::with(['user', 'tags', 'likes', 'comments.user'])->findOrFail($id);
    return response()->json([
        'status' => 'success',
        'message' => 'post fetched',
        'data' => $post,
    ], 200);
}

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'thumbnail' => 'nullable|image',
            'tags' => 'array',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        $data = $validator->validated();

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $post = Auth::user()->posts()->create($data);

        if ($request->tags) {
            // $post->tags()->sync($request->tags);
            $tags = $request->input('tags'); // ["first tag", "second tags", "third tags"]

            foreach ($tags as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $post->tags()->attach($tag->id);
            }
        }

        // return response()->json($post->load('tags'), 201);
        return response()->json([
            'status' => 'success',
            'message' => 'All post fetched',
            'data' => $post->load('tags'),
            // $post->load('tags')
        ], 201);
    }

    public function destroy(Post $post)
    {
        try {
            if ($post->user_id !== Auth::id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $post->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'post removed successfully',
                // $post->load('tags')
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Message ' . $e->getMessage(),

            ], 500);
        }
    }
}
