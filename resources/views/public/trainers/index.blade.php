@extends('layouts.public')

@section('title', 'Trainers')

@section('content')
    <section class="relative border-b border-white/[0.06] fx-hero-mesh">
        <div class="fx-container py-14 md:py-18">
            <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-xl">
                    <p class="fx-badge mb-4">People</p>
                    <h1 class="font-display text-4xl font-extrabold tracking-tight text-white md:text-5xl">Trainers</h1>
                    <p class="mt-4 text-lg text-zinc-400">Public profiles — sign in to follow or book sessions.</p>
                </div>
                <form method="get" action="{{ route('public.trainers') }}" class="w-full max-w-md">
                    <label class="sr-only" for="trainer-q">Search trainers</label>
                    <div class="flex gap-2 rounded-2xl border border-white/10 bg-white/[0.04] p-1.5 pl-4 shadow-fx-soft backdrop-blur-sm focus-within:border-teal-500/40">
                        <input
                            id="trainer-q"
                            type="search"
                            name="q"
                            value="{{ $q }}"
                            placeholder="Search by name…"
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
            @if ($trainers->isEmpty())
                <div class="fx-glass rounded-3xl px-8 py-20 text-center">
                    <p class="font-display text-xl font-semibold text-white">No trainers found</p>
                    <p class="mt-2 text-zinc-500">Try another search.</p>
                </div>
            @else
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($trainers as $t)
                        @php
                            $p = $t->profile;
                            $pic = $p?->profile_picture;
                            $picUrl = $pic ? (str_starts_with($pic, 'http') ? $pic : asset('storage/'.ltrim($pic, '/'))) : null;
                        @endphp
                        <article class="group fx-card-interactive">
                            <a href="{{ route('public.trainers.show', $t->id) }}" class="flex flex-col sm:flex-row sm:items-stretch">
                                <div class="relative flex h-44 shrink-0 items-center justify-center overflow-hidden bg-gradient-to-br from-fx-850 to-fx-950 sm:h-auto sm:w-36">
                                    @if ($picUrl)
                                        <img src="{{ $picUrl }}" alt="" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                    @else
                                        <span class="font-display text-4xl font-bold text-zinc-700">{{ substr($t->first_name, 0, 1) }}</span>
                                    @endif
                                </div>
                                <div class="flex flex-1 flex-col justify-center p-5">
                                    <h2 class="font-display text-lg font-bold text-white">{{ $t->first_name }} {{ $t->last_name }}</h2>
                                    @if ($p?->location)
                                        <p class="mt-1 text-sm text-zinc-500">{{ $p->location }}</p>
                                    @endif
                                    @if ($p?->rating)
                                        <p class="mt-3 text-sm font-medium text-teal-400/90">★ {{ $p->rating }}</p>
                                    @endif
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>
                <div class="mt-12 flex justify-center">{{ $trainers->links() }}</div>
            @endif
        </div>
    </section>
@endsection
