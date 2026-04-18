@extends('layouts.public')

@section('title', 'Maintenance')

@section('content')
    <div class="fx-container flex min-h-[60vh] flex-col items-center justify-center px-6 py-24 text-center">
        <div class="flex h-20 w-20 items-center justify-center rounded-3xl border border-white/10 bg-white/5 text-teal-400/50">
            <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h1 class="mt-6 font-display text-2xl font-bold text-white md:text-3xl">We will be right back</h1>
        <p class="mt-4 max-w-md text-zinc-500">Scheduled maintenance — thanks for your patience.</p>
        <div class="mt-10">
            <a href="{{ route('public.home') }}" class="fx-btn-secondary">Try again</a>
        </div>
    </div>
@endsection
