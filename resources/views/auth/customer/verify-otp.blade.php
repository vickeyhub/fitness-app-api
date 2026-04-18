@extends('layouts.auth')

@section('title', 'Verify email')

@section('content')
    <div class="text-center">
        <h1 class="font-display text-2xl font-bold text-white md:text-3xl">Check your inbox</h1>
        <p class="mt-2 text-sm text-zinc-500">Enter the 4-digit code we sent to your email.</p>
    </div>

    <div class="fx-glass-strong mt-8 rounded-3xl p-6 sm:p-8">
        <form method="post" action="{{ route('auth.verify-otp.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="fx-label" for="email">Email</label>
                <input class="fx-input" type="email" name="email" id="email" value="{{ old('email', $email) }}" required autocomplete="email">
                @error('email')
                    <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="fx-label" for="otp">Verification code</label>
                <input
                    class="fx-input text-center font-display text-2xl tracking-[0.5em] text-white"
                    type="text"
                    name="otp"
                    id="otp"
                    inputmode="numeric"
                    pattern="[0-9]{4}"
                    maxlength="4"
                    placeholder="0000"
                    value="{{ old('otp') }}"
                    required
                    autocomplete="one-time-code"
                >
                @error('otp')
                    <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="fx-btn-primary w-full justify-center py-3 text-base">Verify & continue</button>
        </form>
    </div>

    <p class="mt-8 text-center text-sm text-zinc-500">
        Wrong email?
        <a href="{{ route('auth.register') }}" class="font-semibold text-teal-400 hover:text-teal-300">Start over</a>
        ·
        <a href="{{ route('auth.login') }}" class="text-zinc-400 hover:text-white">Sign in</a>
    </p>
@endsection
