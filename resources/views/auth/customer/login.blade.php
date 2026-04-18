@extends('layouts.auth')

@section('title', 'Sign in')

@section('content')
    <div class="text-center">
        <h1 class="font-display text-2xl font-bold text-white md:text-3xl">Welcome back</h1>
        <p class="mt-2 text-sm text-zinc-500">Sign in to book sessions and manage your account.</p>
    </div>

    <div class="fx-glass-strong mt-8 rounded-3xl p-6 sm:p-8">
        <form method="post" action="{{ route('auth.login.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="fx-label" for="email">Email</label>
                <input class="fx-input" type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                @error('email')
                    <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <div class="flex items-center justify-between gap-2">
                    <label class="fx-label mb-0" for="password">Password</label>
                    <a href="{{ route('auth.forgot-password') }}" class="text-xs font-medium text-teal-400 hover:text-teal-300">Forgot password?</a>
                </div>
                <input class="fx-input mt-1.5" type="password" name="password" id="password" required autocomplete="current-password">
                @error('password')
                    <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                @enderror
            </div>
            <label class="flex cursor-pointer items-center gap-2 text-sm text-zinc-400">
                <input type="checkbox" name="remember" value="1" class="rounded border-white/20 bg-white/5 text-teal-500 focus:ring-teal-500/50" {{ old('remember') ? 'checked' : '' }}>
                Remember me
            </label>
            <button type="submit" class="fx-btn-primary w-full justify-center py-3 text-base">Sign in</button>
        </form>
    </div>

    <p class="mt-8 text-center text-sm text-zinc-500">
        No account?
        <a href="{{ route('auth.register') }}" class="font-semibold text-teal-400 hover:text-teal-300">Create one</a>
    </p>
    <p class="mt-4 text-center text-xs text-zinc-600">
        Staff admin?
        <a href="{{ route('web-login') }}" class="text-zinc-500 hover:text-zinc-400">Team login</a>
    </p>
@endsection
