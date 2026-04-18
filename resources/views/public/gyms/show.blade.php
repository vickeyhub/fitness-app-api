@extends('layouts.public')

@section('title', $gym->first_name.' '.$gym->last_name)

@section('content')
    @php
        $p = $gym->profile;
        $pic = $p?->profile_picture;
        $picUrl = $pic ? (str_starts_with($pic, 'http') ? $pic : asset('storage/'.ltrim($pic, '/'))) : null;
    @endphp

    <section class="relative border-b border-white/[0.06] fx-hero-mesh">
        <div class="fx-container py-8 md:py-10">
            <nav class="text-sm text-zinc-500" aria-label="Breadcrumb">
                <a href="{{ route('public.gyms') }}" class="font-medium text-teal-400/90 hover:text-teal-300">Gyms</a>
                <span class="mx-2 text-zinc-700">/</span>
                <span class="text-zinc-400">{{ $gym->first_name }} {{ $gym->last_name }}</span>
            </nav>
        </div>
    </section>

    <article class="fx-container py-12 md:py-16 lg:max-w-3xl">
        <div class="flex flex-col gap-10 md:flex-row md:items-start md:gap-12">
            <div class="mx-auto flex h-44 w-44 shrink-0 overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-fx-850 to-rose-950/40 shadow-fx-card md:mx-0 md:h-52 md:w-52">
                @if ($picUrl)
                    <img src="{{ $picUrl }}" alt="" class="h-full w-full object-cover" loading="lazy">
                @else
                    <div class="flex h-full w-full items-center justify-center font-display text-6xl font-bold text-zinc-700">G</div>
                @endif
            </div>
            <div class="flex-1">
                <p class="text-xs font-semibold uppercase tracking-widest text-rose-300/80">Gym</p>
                <h1 class="mt-2 font-display text-3xl font-extrabold text-white md:text-4xl">{{ $gym->first_name }} {{ $gym->last_name }}</h1>
                @if ($p?->location)
                    <p class="mt-4 text-zinc-400">{{ $p->location }}</p>
                @endif
                @if ($p?->rating)
                    <p class="mt-3 text-lg font-medium text-teal-300/90">Rating {{ $p->rating }}</p>
                @endif
            </div>
        </div>

        @if ($p?->specialties)
            <div class="fx-glass-strong mt-12 rounded-2xl p-6 md:p-8">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Focus</h2>
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
            <a href="{{ route('auth.login') }}" class="fx-btn-primary px-8">Log in to book</a>
            <a href="{{ route('public.gyms') }}" class="fx-btn-secondary">All gyms</a>
        </div>
    </article>
@endsection
