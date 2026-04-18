@extends('layouts.auth')

@section('title', 'New password')

@section('content')
    <div class="text-center">
        <h1 class="font-display text-2xl font-bold text-white md:text-3xl">Choose a new password</h1>
        <p class="mt-2 text-sm text-zinc-500">Use the code from your email with the account address below.</p>
    </div>

    <div class="fx-glass-strong mt-8 rounded-3xl p-6 sm:p-8">
        <form method="post" action="{{ route('auth.reset-password.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="fx-label" for="email">Email</label>
                <input class="fx-input" type="email" name="email" id="email" value="{{ old('email', $email) }}" required autocomplete="email">
                @error('email')
                    <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="fx-label" for="token">Reset code</label>
                <input class="fx-input text-center font-mono tracking-wider" type="text" name="token" id="token" value="{{ old('token') }}" required autocomplete="one-time-code">
                @error('token')
                    <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="fx-label" for="password">New password</label>
                <input class="fx-input" type="password" name="password" id="password" required autocomplete="new-password" minlength="6">
                @error('password')
                    <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="fx-label" for="password_confirmation">Confirm password</label>
                <input class="fx-input" type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password" minlength="6">
            </div>
            <button type="submit" class="fx-btn-primary w-full justify-center py-3 text-base">Update password</button>
        </form>
    </div>

    <p class="mt-8 text-center text-sm text-zinc-500">
        <a href="{{ route('auth.login') }}" class="font-semibold text-teal-400 hover:text-teal-300">← Back to sign in</a>
    </p>
@endsection
