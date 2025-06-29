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
    public function index_old()
    {
        $posts = Post::with([
            'user:id,first_name,last_name,email,user_type',
            'user.profile:id,user_id,profile_picture,gender,specialties',
            'tags',
            'likes', //send counts / current user
            // 'comments' send total counts of comments
        ])
            ->latest()
            ->cursorPaginate(2);


        return response()->json([
            'status' => 'success',
            'message' => 'All post fetched',
            'data' => $posts->items(),
            'pagination' => [
                'per_page' => $posts->perPage(),
                'next_cursor' => optional($posts->nextCursor())?->encode(),
                'next_page_url' => $posts->nextPageUrl(),
                'prev_cursor' => optional($posts->previousCursor())?->encode(),
                'prev_page_url' => $posts->previousPageUrl(),
                'path' => $posts->path(),
            ]
        ], 200);

    }

    public function index()
    {
        try {
            $userId = Auth::id();

            $posts = Post::with([
                'user:id,first_name,last_name,email,user_type',
                'user.profile:id,user_id,profile_picture,gender,specialties',
                'tags',
                'likes:id,post_id,user_id', // include likes for processing
            ])->withCount('comments') // for total comments
                ->latest()
                ->cursorPaginate(2);

            $data = $posts->items();

            $formatted = array_map(function ($post) use ($userId) {
                $likes = $post->likes ?? [];

                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'description' => $post->description,
                    'thumbnail' => $post->thumbnail ? asset('storage/' . $post->thumbnail) : null,
                    'time_ago' => $post->created_at->diffForHumans(),
                    'user' => $post->user,
                    // 'profile' => $post->user->profile ?? null,
                    'tags' => $post->tags,
                    'likes_count' => count($likes),
                    'liked_by_current_user' => collect($likes)->contains('user_id', $userId),
                    'comments_count' => $post->comments_count,
                    'created_at' => $post->created_at->toDateTimeString(),
                ];
            }, $data);

            return response()->json([
                'status' => 'success',
                'message' => 'All posts fetched',
                'data' => $formatted,
                'pagination' => [
                    'per_page' => $posts->perPage(),
                    'next_cursor' => optional($posts->nextCursor())?->encode(),
                    'next_page_url' => $posts->nextPageUrl(),
                    'prev_cursor' => optional($posts->previousCursor())?->encode(),
                    'prev_page_url' => $posts->previousPageUrl(),
                    'path' => $posts->path(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ], 500);
        }
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
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Message ' . $e->getMessage(),

            ], 500);
        }
    }
}
