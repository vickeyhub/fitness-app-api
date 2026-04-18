@extends('layouts.public')

@section('title', $trainer->first_name.' '.$trainer->last_name)

@section('content')
    @php
        $p = $trainer->profile;
        $pic = $p?->profile_picture;
        $picUrl = $pic ? (str_starts_with($pic, 'http') ? $pic : asset('storage/'.ltrim($pic, '/'))) : null;
    @endphp

    <section class="relative border-b border-white/[0.06] fx-hero-mesh">
        <div class="fx-container py-8 md:py-10">
            <nav class="text-sm text-zinc-500" aria-label="Breadcrumb">
                <a href="{{ route('public.trainers') }}" class="font-medium text-teal-400/90 hover:text-teal-300">Trainers</a>
                <span class="mx-2 text-zinc-700">/</span>
                <span class="text-zinc-400">{{ $trainer->first_name }} {{ $trainer->last_name }}</span>
            </nav>
        </div>
    </section>

    <article class="fx-container py-12 md:py-16 lg:max-w-3xl">
        <div class="flex flex-col gap-10 md:flex-row md:items-start md:gap-12">
            <div class="mx-auto flex h-44 w-44 shrink-0 overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-fx-850 to-fx-950 shadow-fx-card md:mx-0 md:h-52 md:w-52">
                @if ($picUrl)
                    <img src="{{ $picUrl }}" alt="" class="h-full w-full object-cover" loading="lazy">
                @else
                    <div class="flex h-full w-full items-center justify-center font-display text-6xl font-bold text-zinc-700">{{ substr($trainer->first_name, 0, 1) }}</div>
                @endif
            </div>
            <div class="flex-1">
                <p class="text-xs font-semibold uppercase tracking-widest text-teal-400/90">Trainer</p>
                <h1 class="mt-2 font-display text-3xl font-extrabold text-white md:text-4xl">{{ $trainer->first_name }} {{ $trainer->last_name }}</h1>
                @if ($p?->location)
                    <p class="mt-4 flex items-center gap-2 text-zinc-400">
                        <svg class="h-4 w-4 shrink-0 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $p->location }}
                    </p>
                @endif
                @if ($p?->rating)
                    <p class="mt-3 text-lg font-medium text-teal-300/90">Average rating {{ $p->rating }}</p>
                @endif
                @if ($p?->experience_level)
                    <p class="mt-2 text-sm text-zinc-500">Experience · {{ $p->experience_level }}</p>
                @endif
            </div>
        </div>

        @if ($p?->specialties)
            <div class="fx-glass-strong mt-12 rounded-2xl p-6 md:p-8">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Specialties</h2>
                <p class="mt-3 text-zinc-300">
                    @if (is_array($p->specialties))
                        {{ implode(', ', $p->specialties) }}
                    @else
                        {{ $p->specialties }}
                    @endif
                </p>
            </div>
        @endif

        @if ($p?->user_description)
            <div class="mt-8">
                <h2 class="font-display text-xl font-bold text-white">About</h2>
                <p class="mt-4 whitespace-pre-wrap leading-relaxed text-zinc-400">{{ $p->user_description }}</p>
            </div>
        @endif

        <div class="mt-12 flex flex-wrap gap-4 border-t border-white/10 pt-10">
            <a href="{{ route('auth.login') }}" class="fx-btn-primary px-8">Log in to connect</a>
            <a href="{{ route('public.trainers') }}" class="fx-btn-secondary">All trainers</a>
        </div>
    </article>
@endsection
