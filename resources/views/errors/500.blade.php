@extends('layouts.public')

@section('title', 'Something went wrong')

@section('content')
    <div class="fx-container flex min-h-[60vh] flex-col items-center justify-center px-6 py-24 text-center">
        <p class="font-display text-8xl font-black tabular-nums text-white/10 md:text-9xl">500</p>
        <h1 class="mt-4 font-display text-2xl font-bold text-white md:text-3xl">Something went wrong</h1>
        <p class="mt-4 max-w-md text-zinc-500">We are on it. Please try again in a few minutes.</p>
        <div class="mt-10 flex flex-wrap justify-center gap-4">
            <a href="{{ route('public.home') }}" class="fx-btn-primary px-8">Back home</a>
            <a href="{{ route('public.contact') }}" class="fx-btn-secondary">Contact support</a>
        </div>
    </div>
@endsection
