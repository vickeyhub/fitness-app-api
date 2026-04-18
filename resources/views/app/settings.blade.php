@extends('layouts.app')

@section('title', 'Settings')
@section('heading', 'Settings')

@section('content')
    <div class="mx-auto max-w-lg">
        <div class="fx-glass-strong rounded-3xl p-6 sm:p-8">
            <h2 class="font-display text-lg font-bold text-white">Security</h2>
            <p class="mt-2 text-sm text-zinc-500">Change the password you use to sign in to FitX on the web.</p>

            <form method="post" action="{{ route('app.settings.password') }}" class="mt-8 space-y-5">
                @csrf
                <div>
                    <label class="fx-label" for="current_password">Current password</label>
                    <input class="fx-input" type="password" name="current_password" id="current_password" required autocomplete="current-password">
                    @error('current_password')
                        <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="fx-label" for="password">New password</label>
                    <input class="fx-input" type="password" name="password" id="password" required minlength="6" autocomplete="new-password">
                    @error('password')
                        <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="fx-label" for="password_confirmation">Confirm new password</label>
                    <input class="fx-input" type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password">
                </div>
                <button type="submit" class="fx-btn-primary w-full justify-center py-3">Update password</button>
            </form>
        </div>

        <p class="mt-8 text-center text-sm text-zinc-600">
            Preference toggles (email, marketing) can be added here when the API supports them.
        </p>
    </div>
@endsection
