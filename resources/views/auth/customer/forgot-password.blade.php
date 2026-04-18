@extends('layouts.auth')

@section('title', 'Reset password')

@section('content')
    <div class="text-center">
        <h1 class="font-display text-2xl font-bold text-white md:text-3xl">Forgot password</h1>
        <p class="mt-2 text-sm text-zinc-500">We will email you a short code to reset your password.</p>
    </div>

    <div class="fx-glass-strong mt-8 rounded-3xl p-6 sm:p-8">
        <form method="post" action="{{ route('auth.forgot-password.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="fx-label" for="email">Email</label>
                <input class="fx-input" type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                @error('email')
                    <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="fx-btn-primary w-full justify-center py-3 text-base">Send reset code</button>
        </form>
    </div>

    <p class="mt-8 text-center text-sm text-zinc-500">
        <a href="{{ route('auth.login') }}" class="font-semibold text-teal-400 hover:text-teal-300">← Back to sign in</a>
    </p>
@endsection
