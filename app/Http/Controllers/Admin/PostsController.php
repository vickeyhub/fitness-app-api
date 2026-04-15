<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AuditTrailLogger;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostsController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'created_from' => ['nullable', 'date'],
            'created_to' => ['nullable', 'date'],
            'per_page' => ['nullable', 'integer', 'in:10,15,25,50,100'],
        ]);

        $query = Post::query()
            ->with(['user:id,first_name,last_name,email', 'tags:id,name'])
            ->withCount(['comments', 'likes'])
            ->latest('id');

        if (!empty($filters['q'])) {
            $q = trim((string) $filters['q']);
            $query->where(function ($inner) use ($q) {
                $inner->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', (int) $filters['user_id']);
        }

        if (!empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }

        if (!empty($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }

        $posts = $query->paginate((int) ($filters['per_page'] ?? 15))->appends($request->query());
        $users = User::query()->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'email']);

        return view('admin.posts.index', [
            'posts' => $posts,
            'users' => $users,
            'filters' => $filters,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:5120'],
            'tags' => ['nullable', 'string', 'max:500'],
        ]);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $post = Post::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'thumbnail' => $data['thumbnail'] ?? null,
            'user_id' => Auth::id(),
        ]);

        $tagNames = collect(explode(',', (string) ($data['tags'] ?? '')))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->unique()
            ->values();

        if ($tagNames->isNotEmpty()) {
            $tagIds = $tagNames->map(fn ($name) => Tag::firstOrCreate(['name' => $name])->id);
            $post->tags()->sync($tagIds);
        }
        AuditTrailLogger::log('posts', 'create', $post, ['title' => $post->title, 'user_id' => $post->user_id]);

        return response()->json([
            'message' => 'Post created successfully.',
            'data' => $post->load('tags'),
        ], 201);
    }

    public function show(Post $post)
    {
        $post->load([
            'user:id,first_name,last_name,email',
            'tags:id,name',
            'comments' => fn ($q) => $q->with('user:id,first_name,last_name,email')->latest()->limit(25),
            'likes' => fn ($q) => $q->with('user:id,first_name,last_name,email')->latest()->limit(20),
        ])->loadCount(['comments', 'likes']);

        return response()->json([
            'data' => $post,
            'meta' => [
                'liked_by_current_user' => Like::query()
                    ->where('post_id', $post->id)
                    ->where('user_id', Auth::id())
                    ->where('type', 'like')
                    ->exists(),
            ],
        ]);
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:5120'],
            'remove_thumbnail' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'string', 'max:500'],
        ]);

        if (!empty($data['remove_thumbnail'])) {
            $post->thumbnail = null;
        }

        if ($request->hasFile('thumbnail')) {
            $post->thumbnail = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $post->title = $data['title'];
        $post->description = $data['description'];
        $post->save();

        $tagNames = collect(explode(',', (string) ($data['tags'] ?? '')))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->unique()
            ->values();

        if ($tagNames->isNotEmpty()) {
            $tagIds = $tagNames->map(fn ($name) => Tag::firstOrCreate(['name' => $name])->id);
            $post->tags()->sync($tagIds);
        } else {
            $post->tags()->detach();
        }
        AuditTrailLogger::log('posts', 'update', $post, ['title' => $post->title, 'user_id' => $post->user_id]);

        return response()->json([
            'message' => 'Post updated successfully.',
            'data' => $post->load('tags'),
        ]);
    }

    public function like(Post $post)
    {
        $userId = Auth::id();
        $existing = Like::query()
            ->where('post_id', $post->id)
            ->where('user_id', $userId)
            ->where('type', 'like')
            ->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            Like::create([
                'post_id' => $post->id,
                'user_id' => $userId,
                'type' => 'like',
            ]);
            $liked = true;
        }

        return response()->json([
            'message' => $liked ? 'Post liked.' : 'Like removed.',
            'liked' => $liked,
            'likes_count' => Like::query()->where('post_id', $post->id)->where('type', 'like')->count(),
        ]);
    }

    public function comment(Request $request, Post $post)
    {
        $data = $request->validate([
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'comment' => $data['comment'],
        ])->load('user:id,first_name,last_name,email');

        return response()->json([
            'message' => 'Comment added.',
            'data' => $comment,
        ], 201);
    }

    public function destroy(Post $post)
    {
        AuditTrailLogger::log('posts', 'delete', $post, ['title' => $post->title, 'user_id' => $post->user_id]);
        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully.',
        ]);
    }
}
