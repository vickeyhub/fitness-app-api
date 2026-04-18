@extends('layouts.auth')

@section('title', 'Create account')

@section('content')
    <div class="text-center">
        <h1 class="font-display text-2xl font-bold text-white md:text-3xl">Join FitX</h1>
        <p class="mt-2 text-sm text-zinc-500">Create a member account — we will email you a code to verify.</p>
    </div>

    <div class="fx-glass-strong mt-8 rounded-3xl p-6 sm:p-8">
        <form method="post" action="{{ route('auth.register.store') }}" class="space-y-5">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="fx-label" for="first_name">First name</label>
                    <input class="fx-input" type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required autocomplete="given-name">
                    @error('first_name')
                        <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="fx-label" for="last_name">Last name</label>
                    <input class="fx-input" type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required autocomplete="family-name">
                    @error('last_name')
                        <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div>
                <label class="fx-label" for="email">Email</label>
                <input class="fx-input" type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email">
                @error('email')
                    <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="fx-label" for="password">Password</label>
                <input class="fx-input" type="password" name="password" id="password" required autocomplete="new-password" minlength="6">
                @error('password')
                    <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="fx-label" for="password_confirmation">Confirm password</label>
                <input class="fx-input" type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password" minlength="6">
            </div>
            <button type="submit" class="fx-btn-primary w-full justify-center py-3 text-base">Continue</button>
        </form>
    </div>

    <p class="mt-8 text-center text-sm text-zinc-500">
        Already have an account?
        <a href="{{ route('auth.login') }}" class="font-semibold text-teal-400 hover:text-teal-300">Sign in</a>
    </p>
@endsection
