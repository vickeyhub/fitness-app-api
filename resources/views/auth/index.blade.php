@extends('layouts.auth')

@section('title', 'Admin Sign in')

@section('content')
    <div class="text-center">
        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-teal-400 to-emerald-600 shadow-fx-glow">
            <span class="text-xl font-black text-fx-950">FX</span>
        </div>
        <h1 class="font-display text-2xl font-bold text-white md:text-3xl">Admin panel</h1>
        <p class="mt-2 text-sm text-zinc-500">Sign in to manage your fitness platform.</p>
    </div>

    <div class="fx-glass-strong mt-8 rounded-3xl p-6 sm:p-8">
        <form method="post" action="{{ route('post-login') }}" class="space-y-5">
            @csrf
            <div>
                <label class="fx-label" for="email">Email</label>
                <input class="fx-input" type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                @error('email')
                    <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="fx-label" for="password">Password</label>
                <input class="fx-input" type="password" name="password" id="password" required autocomplete="current-password">
                @error('password')
                    <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="fx-btn-primary w-full justify-center py-3 text-base">Sign in</button>
        </form>
    </div>

    <p class="mt-8 text-center text-xs text-zinc-600">
        Member sign-in?
        <a href="{{ route('auth.login') }}" class="font-semibold text-teal-400 hover:text-teal-300">Customer portal</a>
    </p>
@endsection
