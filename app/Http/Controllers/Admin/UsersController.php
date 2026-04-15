<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    public function index()
    {
        return $this->renderIndex(null);
    }

    public function trainers()
    {
        return $this->renderIndex('trainer');
    }

    public function gyms()
    {
        return $this->renderIndex('gym');
    }

    private function renderIndex(?string $scopeType)
    {
        $query = User::with('profile')->orderByDesc('id');
        if (in_array($scopeType, ['trainer', 'gym'], true)) {
            $query->where('user_type', $scopeType);
        }

        return view('admin.users.index', [
            'users' => $query->get(),
            'scopeType' => $scopeType,
            'scopeLabel' => $scopeType === 'trainer'
                ? 'Trainers'
                : ($scopeType === 'gym' ? 'Gyms' : 'Users'),
        ]);
    }

    public function show(User $user)
    {
        $user->load('profile');

        return response()->json(['user' => $user]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateUser($request, null);
        $profileData = $this->validatedProfile($request);

        $user = User::create($validated);
        $user->profile()->create($profileData);

        return response()->json([
            'message' => 'User created successfully.',
            'user' => $user->load('profile'),
        ], 201);
    }

    public function update(Request $request, User $user)
    {
        $validated = $this->validateUser($request, $user);
        $profileData = $this->validatedProfile($request);

        $user->update($validated);
        if ($user->profile) {
            $user->profile->update($profileData);
        } else {
            $user->profile()->create($profileData);
        }

        return response()->json([
            'message' => 'User updated successfully.',
            'user' => $user->fresh()->load('profile'),
        ]);
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return response()->json([
                'message' => 'You cannot delete your own account.',
            ], 422);
        }

        if ($user->profile) {
            $user->profile->delete();
        }
        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateUser(Request $request, ?User $user): array
    {
        if ($user && ! $request->filled('password')) {
            $request->merge([
                'password' => null,
                'password_confirmation' => null,
            ]);
        }

        $passwordRules = $user
            ? ['nullable', 'string', 'min:6', 'confirmed']
            : ['required', 'string', 'min:6', 'confirmed'];

        $rules = [
            'first_name' => ['required', 'string', 'max:150'],
            'last_name' => ['required', 'string', 'max:150'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
            'username' => ['nullable', 'string', 'max:255'],
            'mobile_number' => ['nullable', 'string', 'max:50'],
            'user_type' => ['required', Rule::in(['super_admin', 'admin', 'trainer', 'user', 'gym'])],
            'status' => ['required', Rule::in(['0', '1', '2'])],
            'password' => $passwordRules,
        ];

        $validated = $request->validate($rules);

        if (empty($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        return $validated;
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedProfile(Request $request): array
    {
        return $request->validate([
            'gender' => ['nullable', 'string', 'max:7'],
            'height' => ['nullable', 'string', 'max:50'],
            'height_parameter' => ['nullable', 'string', 'max:50'],
            'weight' => ['nullable', 'string', 'max:50'],
            'weight_parameter' => ['nullable', 'string', 'max:50'],
        ]);
    }
}
