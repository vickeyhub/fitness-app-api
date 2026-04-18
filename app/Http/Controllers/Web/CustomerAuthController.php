<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Http\Controllers\Api\PasswordResetController as ApiPasswordResetController;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CustomerAuthController extends Controller
{
    /**
     * Staff (admin) must use /login — block from member portal.
     */
    protected function rejectIfAdmin(?User $user): bool
    {
        return $user && $user->user_type === 'admin';
    }

    protected function redirectWhenAuthenticated(): RedirectResponse
    {
        $user = Auth::user();

        return $this->rejectIfAdmin($user)
            ? redirect()->route('admin.dashboard')
            : redirect()->route('app.dashboard');
    }

    public function showLogin(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectWhenAuthenticated();
        }

        return view('auth.customer.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'remember' => 'sometimes|boolean',
        ]);

        $user = User::query()
            ->where('email', $request->email)
            ->where('status', '1')
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        if ($this->rejectIfAdmin($user)) {
            throw ValidationException::withMessages([
                'email' => __('Staff accounts use the team login page.'),
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('app.dashboard'));
    }

    public function showRegister(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectWhenAuthenticated();
        }

        return view('auth.customer.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $request->merge(['user_type' => 'user']);

        $response = app(ApiAuthController::class)->register($request);
        $status = $response->getStatusCode();
        $payload = json_decode($response->getContent(), true);

        if ($status === 201 || $status === 200) {
            return redirect()
                ->route('auth.verify-otp')
                ->with('auth_email', $request->email);
        }

        if ($status === 422) {
            $e = $payload['errors'] ?? [];
            if (is_array($e) && $e !== []) {
                return array_is_list($e)
                    ? back()->withErrors(['email' => $e])->withInput()
                    : back()->withErrors($e)->withInput();
            }
        }

        return back()->withErrors([
            'email' => $payload['message'] ?? __('Registration failed.'),
        ])->withInput();
    }

    public function showVerifyOtp(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectWhenAuthenticated();
        }

        $email = old('email', session('auth_email'));

        return view('auth.customer.verify-otp', ['email' => $email]);
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:4',
        ]);

        $response = app(ApiAuthController::class)->verifyOtp($request);
        $payload = json_decode($response->getContent(), true);

        if ($response->getStatusCode() !== 200) {
            $err = $payload['errors'] ?? null;
            if (is_array($err) && array_is_list($err)) {
                return back()->withErrors(['otp' => $err])->withInput();
            }

            return back()->withErrors([
                'otp' => $payload['message'] ?? __('Verification failed.'),
            ])->withInput();
        }

        $user = User::where('email', $request->email)->first();
        if (! $user || $this->rejectIfAdmin($user)) {
            return redirect()->route('auth.login')->with('status', __('Account could not be activated.'));
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('app.dashboard')->with('status', __('Welcome! Your email is verified.'));
    }

    public function showForgotPassword(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectWhenAuthenticated();
        }

        return view('auth.customer.forgot-password');
    }

    public function forgotPassword(Request $request): RedirectResponse
    {
        $response = app(ApiPasswordResetController::class)->forgotPassword($request);
        $payload = json_decode($response->getContent(), true);

        if ($response->getStatusCode() !== 200) {
            if ($response->getStatusCode() === 422 && ! empty($payload['errors'])) {
                return back()->withErrors($payload['errors'])->withInput();
            }

            return back()->withErrors([
                'email' => $payload['message'] ?? __('Request failed.'),
            ])->withInput();
        }

        return back()->with('status', $payload['message'] ?? __('Check your email for a reset code.'));
    }

    public function showResetPassword(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectWhenAuthenticated();
        }

        return view('auth.customer.reset-password', [
            'email' => old('email', $request->query('email')),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $response = app(ApiPasswordResetController::class)->resetPassword($request);
        $payload = json_decode($response->getContent(), true);

        if ($response->getStatusCode() !== 200) {
            if ($response->getStatusCode() === 422 && ! empty($payload['errors'])) {
                return back()->withErrors($payload['errors'])->withInput();
            }

            return back()->withErrors([
                'email' => $payload['message'] ?? __('Reset failed.'),
            ])->withInput();
        }

        return redirect()->route('auth.login')->with('status', $payload['message'] ?? __('Password updated. You can sign in now.'));
    }
}
