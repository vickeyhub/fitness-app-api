<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Api\UserController as ApiUserController;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AppPortalController extends Controller
{
    public function dashboard(Request $request): View
    {
        return view('app.dashboard', [
            'user' => $request->user(),
        ]);
    }

    public function profile(Request $request): View
    {
        $user = User::with('profile')->findOrFail($request->user()->id);

        return view('app.profile', ['user' => $user]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        if ($request->has('experience_level')) {
            $request->merge(['experiance_level' => $request->input('experience_level')]);
        }

        $response = app(ApiUserController::class)->updateProfile($request);
        $payload = json_decode($response->getContent(), true);

        if ($response->getStatusCode() !== 200) {
            if ($response->getStatusCode() === 422 && ! empty($payload['errors'])) {
                return back()->withErrors($payload['errors'])->withInput();
            }

            return back()->withErrors([
                'email' => $payload['message'] ?? __('Could not update profile.'),
            ])->withInput();
        }

        return redirect()->route('app.profile')->with('status', $payload['message'] ?? __('Profile updated.'));
    }

    public function settings(Request $request): View
    {
        return view('app.settings', ['user' => $request->user()]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('app.settings')->with('status', __('Password updated successfully.'));
    }

    public function notifications(): View
    {
        return view('app.notifications');
    }
}
