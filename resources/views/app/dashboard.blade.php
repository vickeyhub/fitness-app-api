@extends('layouts.app')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <div class="fx-glass-strong rounded-3xl p-8">
                <p class="fx-badge mb-4">Overview</p>
                <h2 class="font-display text-2xl font-bold text-white">Hello, {{ $user->first_name ?: 'there' }}</h2>
                <p class="mt-3 text-zinc-400">
                    Browse published sessions, book a slot, and track bookings from the app. Profile, security, and notifications are in the sidebar. Stripe checkout on web is still to come — paid sessions create pending bookings until payment is wired.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('app.sessions.index') }}" class="fx-btn-primary">Browse sessions</a>
                    <a href="{{ route('app.bookings.index') }}" class="fx-btn-secondary">My bookings</a>
                    <a href="{{ route('app.profile') }}" class="fx-btn-secondary">Edit profile</a>
                </div>
            </div>
        </div>
        <div class="space-y-4">
            <a href="{{ route('app.profile') }}" class="fx-glass block rounded-2xl p-5 transition hover:border-teal-500/30">
                <p class="text-xs font-semibold uppercase tracking-wider text-zinc-600">Account</p>
                <p class="mt-1 font-medium text-white">Profile &amp; photo</p>
                <p class="mt-2 text-sm text-zinc-500">Name, body stats, bio</p>
            </a>
            <a href="{{ route('app.settings') }}" class="fx-glass block rounded-2xl p-5 transition hover:border-teal-500/30">
                <p class="text-xs font-semibold uppercase tracking-wider text-zinc-600">Security</p>
                <p class="mt-1 font-medium text-white">Password</p>
                <p class="mt-2 text-sm text-zinc-500">Update your sign-in password</p>
            </a>
            <a href="{{ route('app.notifications') }}" class="fx-glass block rounded-2xl p-5 transition hover:border-teal-500/30">
                <p class="text-xs font-semibold uppercase tracking-wider text-zinc-600">Activity</p>
                <p class="mt-1 font-medium text-white">Notifications</p>
                <p class="mt-2 text-sm text-zinc-500">Alerts and updates</p>
            </a>
        </div>
    </div>
@endsection
