<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StatusesController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'type' => ['nullable', 'in:photo,video'],
            'created_from' => ['nullable', 'date'],
            'created_to' => ['nullable', 'date'],
            'per_page' => ['nullable', 'integer', 'in:10,15,25,50,100'],
        ]);

        $query = Status::query()
            ->with('user:id,first_name,last_name,email')
            ->latest('id');

        if (!empty($filters['q'])) {
            $query->where('caption', 'like', '%' . trim((string) $filters['q']) . '%');
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', (int) $filters['user_id']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }

        if (!empty($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }

        return view('admin.statuses.index', [
            'statuses' => $query->paginate((int) ($filters['per_page'] ?? 15))->appends($request->query()),
            'users' => User::query()->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'email']),
            'filters' => $filters,
        ]);
    }

    public function destroy(Status $status)
    {
        if (!empty($status->media)) {
            Storage::disk('public')->delete($status->media);
        }

        $status->delete();

        return response()->json([
            'message' => 'Status deleted successfully.',
        ]);
    }
}
