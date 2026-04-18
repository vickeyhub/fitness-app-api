@extends('layouts.public')

@section('title', 'Sessions')

@section('content')
    <section class="relative border-b border-white/[0.06] fx-hero-mesh">
        <div class="fx-container py-14 md:py-18">
            <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-xl">
                    <p class="fx-badge mb-4">Discover</p>
                    <h1 class="font-display text-4xl font-extrabold tracking-tight text-white md:text-5xl">Sessions</h1>
                    <p class="mt-4 text-lg text-zinc-400">Published sessions only. Sign in to book and pay.</p>
                </div>
                <form method="get" action="{{ route('public.sessions') }}" class="w-full max-w-md">
                    <label class="sr-only" for="session-q">Search sessions</label>
                    <div class="flex gap-2 rounded-2xl border border-white/10 bg-white/[0.04] p-1.5 pl-4 shadow-fx-soft backdrop-blur-sm focus-within:border-teal-500/40 focus-within:ring-2 focus-within:ring-teal-500/20">
                        <input
                            id="session-q"
                            type="search"
                            name="q"
                            value="{{ $q }}"
                            placeholder="Search by title…"
                            class="min-w-0 flex-1 bg-transparent py-2 text-sm text-white placeholder:text-zinc-600 outline-none"
                            autocomplete="off"
                        >
                        <button type="submit" class="fx-btn-primary shrink-0 px-5 py-2 text-sm">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section class="py-14 md:py-20">
        <div class="fx-container">
            @if ($sessions->isEmpty())
                <div class="fx-glass rounded-3xl px-8 py-20 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl border border-white/10 bg-white/[0.04] text-teal-400/80">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <p class="mt-6 font-display text-xl font-semibold text-white">No sessions match</p>
                    <p class="mt-2 text-zinc-500">Try another keyword or clear filters.</p>
                    <a href="{{ route('public.sessions') }}" class="mt-8 inline-flex fx-btn-secondary">Clear search</a>
                </div>
            @else
                <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($sessions as $session)
                        @php
                            $thumb = $session->session_thumbnail;
                            $thumbUrl = $thumb ? (str_starts_with($thumb, 'http') ? $thumb : asset('storage/'.ltrim($thumb, '/'))) : null;
                        @endphp
                        <article class="group fx-card-interactive">
                            <a href="{{ route('public.sessions.show', $session->id) }}" class="block">
                                <div class="relative aspect-[16/10] overflow-hidden bg-fx-850">
                                    @if ($thumbUrl)
                                        <img src="{{ $thumbUrl }}" alt="" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                    @else
                                        <div class="flex h-full items-center justify-center text-zinc-600">
                                            <span class="text-4xl opacity-40">◆</span>
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 bg-gradient-to-t from-fx-950 via-transparent to-transparent opacity-80"></div>
                                    @if ($session->price !== null)
                                        <span class="absolute bottom-3 right-3 rounded-full bg-fx-950/90 px-3 py-1 text-sm font-bold text-teal-300 backdrop-blur-sm">
                                            ${{ number_format((float) $session->price, 2) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="p-5">
                                    <h2 class="font-display text-lg font-bold leading-snug text-white line-clamp-2">{{ $session->session_title }}</h2>
                                    @if ($session->user)
                                        <p class="mt-2 text-sm text-zinc-500">
                                            {{ $session->user->first_name }} {{ $session->user->last_name }}
                                            @if ($session->user->user_type === 'trainer')
                                                <span class="text-teal-500/90"> · Trainer</span>
                                            @endif
                                        </p>
                                    @endif
                                    @if ($session->duration)
                                        <p class="mt-3 text-xs font-medium uppercase tracking-wider text-zinc-600">{{ $session->duration }} min</p>
                                    @endif
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>
                <div class="mt-12 flex justify-center">
                    {{ $sessions->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
