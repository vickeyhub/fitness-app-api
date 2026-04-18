@extends('layouts.public')

@section('title', 'Gyms')

@section('content')
    <section class="relative border-b border-white/[0.06] fx-hero-mesh">
        <div class="fx-container py-14 md:py-18">
            <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-xl">
                    <p class="fx-badge mb-4">Venues</p>
                    <h1 class="font-display text-4xl font-extrabold tracking-tight text-white md:text-5xl">Gyms</h1>
                    <p class="mt-4 text-lg text-zinc-400">Partner gyms and studios on the platform.</p>
                </div>
                <form method="get" action="{{ route('public.gyms') }}" class="w-full max-w-lg">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        <label class="sr-only" for="gym-q">Name</label>
                        <input id="gym-q" type="search" name="q" value="{{ $q }}" placeholder="Name" class="fx-input sm:flex-1">
                        <label class="sr-only" for="gym-loc">Location</label>
                        <input id="gym-loc" type="search" name="location" value="{{ $location }}" placeholder="Location" class="fx-input sm:flex-1">
                        <button type="submit" class="fx-btn-primary shrink-0 px-6">Filter</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section class="py-14 md:py-20">
        <div class="fx-container">
            @if ($gyms->isEmpty())
                <div class="fx-glass rounded-3xl px-8 py-20 text-center">
                    <p class="font-display text-xl font-semibold text-white">No gyms match</p>
                    <p class="mt-2 text-zinc-500">Adjust filters and try again.</p>
                </div>
            @else
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($gyms as $g)
                        @php
                            $p = $g->profile;
                            $pic = $p?->profile_picture;
                            $picUrl = $pic ? (str_starts_with($pic, 'http') ? $pic : asset('storage/'.ltrim($pic, '/'))) : null;
                        @endphp
                        <article class="group fx-card-interactive">
                            <a href="{{ route('public.gyms.show', $g->id) }}" class="flex flex-col sm:flex-row sm:items-stretch">
                                <div class="relative flex h-40 shrink-0 items-center justify-center overflow-hidden bg-gradient-to-br from-fx-850 to-rose-950/30 sm:h-auto sm:w-32">
                                    @if ($picUrl)
                                        <img src="{{ $picUrl }}" alt="" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                    @else
                                        <span class="font-display text-3xl font-bold text-zinc-700">G</span>
                                    @endif
                                </div>
                                <div class="flex flex-1 flex-col justify-center p-5">
                                    <h2 class="font-display text-lg font-bold text-white">{{ $g->first_name }} {{ $g->last_name }}</h2>
                                    @if ($p?->location)
                                        <p class="mt-1 text-sm text-zinc-500">{{ $p->location }}</p>
                                    @endif
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>
                <div class="mt-12 flex justify-center">{{ $gyms->links() }}</div>
            @endif
        </div>
    </section>
@endsection
