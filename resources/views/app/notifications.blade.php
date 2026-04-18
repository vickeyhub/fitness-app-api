@extends('layouts.app')

@section('title', 'Notifications')
@section('heading', 'Notifications')

@section('content')
    <div class="fx-glass-strong mx-auto max-w-2xl rounded-3xl p-10 text-center">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-zinc-500" aria-hidden="true">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        </div>
        <h2 class="mt-6 font-display text-xl font-bold text-white">No notifications yet</h2>
        <p class="mx-auto mt-3 max-w-md text-sm leading-relaxed text-zinc-500">
            In-app notification history is not stored for the web panel yet. Time-sensitive updates are still sent through the mobile app (for example push via OneSignal) where configured.
        </p>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ route('app.dashboard') }}" class="fx-btn-secondary">Back to dashboard</a>
            <a href="{{ route('public.sessions') }}" class="fx-btn-primary">Browse sessions</a>
        </div>
    </div>
@endsection
