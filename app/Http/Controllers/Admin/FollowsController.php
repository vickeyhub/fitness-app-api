<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;

class FollowsController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'follower_id' => ['nullable', 'integer', 'exists:users,id'],
            'following_id' => ['nullable', 'integer', 'exists:users,id'],
            'created_from' => ['nullable', 'date'],
            'created_to' => ['nullable', 'date'],
            'per_page' => ['nullable', 'integer', 'in:10,15,25,50,100'],
        ]);

        $query = Follow::query()
            ->with([
                'follower:id,first_name,last_name,email',
                'following:id,first_name,last_name,email',
            ])
            ->latest('id');

        if (!empty($filters['q'])) {
            $q = trim((string) $filters['q']);
            $query->where(function ($inner) use ($q) {
                $inner->whereHas('follower', function ($u) use ($q) {
                    $u->where('first_name', 'like', "%{$q}%")
                        ->orWhere('last_name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                })->orWhereHas('following', function ($u) use ($q) {
                    $u->where('first_name', 'like', "%{$q}%")
                        ->orWhere('last_name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            });
        }

        if (!empty($filters['follower_id'])) {
            $query->where('follower_id', (int) $filters['follower_id']);
        }

        if (!empty($filters['following_id'])) {
            $query->where('following_id', (int) $filters['following_id']);
        }

        if (!empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }

        if (!empty($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }

        return view('admin.follows.index', [
            'follows' => $query->paginate((int) ($filters['per_page'] ?? 15))->appends($request->query()),
            'users' => User::query()->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'email']),
            'filters' => $filters,
        ]);
    }

    public function destroy(Follow $follow)
    {
        $follow->delete();

        return response()->json([
            'message' => 'Follow link removed successfully.',
        ]);
    }
}
