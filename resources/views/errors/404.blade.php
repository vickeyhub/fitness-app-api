@extends('layouts.public')

@section('title', 'Page not found')

@section('content')
    <div class="fx-container flex min-h-[60vh] flex-col items-center justify-center px-6 py-24 text-center">
        <p class="font-display text-8xl font-black tabular-nums text-white/10 md:text-9xl">404</p>
        <h1 class="mt-4 font-display text-2xl font-bold text-white md:text-3xl">This page does not exist</h1>
        <p class="mt-4 max-w-md text-zinc-500">The link may be outdated, or the content was moved.</p>
        <div class="mt-10 flex flex-wrap justify-center gap-4">
            <a href="{{ route('public.home') }}" class="fx-btn-primary px-8">Back home</a>
            <a href="{{ route('public.sessions') }}" class="fx-btn-secondary">Browse sessions</a>
        </div>
    </div>
@endsection
