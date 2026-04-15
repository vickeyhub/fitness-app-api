<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class CommentsController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'post_id' => ['nullable', 'integer', 'exists:posts,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'created_from' => ['nullable', 'date'],
            'created_to' => ['nullable', 'date'],
            'per_page' => ['nullable', 'integer', 'in:10,15,25,50,100'],
        ]);

        $query = Comment::query()
            ->with([
                'post:id,title',
                'user:id,first_name,last_name,email',
            ])
            ->latest('id');

        if (!empty($filters['q'])) {
            $query->where('comment', 'like', '%' . trim((string) $filters['q']) . '%');
        }

        if (!empty($filters['post_id'])) {
            $query->where('post_id', (int) $filters['post_id']);
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

        return view('admin.comments.index', [
            'comments' => $query->paginate((int) ($filters['per_page'] ?? 15))->appends($request->query()),
            'posts' => Post::query()->latest('id')->limit(200)->get(['id', 'title']),
            'users' => User::query()->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'email']),
            'filters' => $filters,
        ]);
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully.',
        ]);
    }
}
