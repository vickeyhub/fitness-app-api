@extends('layouts.app')

@section('title', 'Sessions')
@section('heading', 'Find sessions')

@section('content')
    <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <form method="get" action="{{ route('app.sessions.index') }}" class="flex w-full max-w-xl flex-col gap-3 sm:flex-row sm:items-center">
            <input type="search" name="q" value="{{ $q }}" placeholder="Search title…" class="fx-input flex-1">
            <select name="sort" class="fx-input w-full sm:w-44" onchange="this.form.submit()">
                <option value="newest" @selected($sort === 'newest')>Newest</option>
                <option value="price_asc" @selected($sort === 'price_asc')>Price ↑</option>
                <option value="price_desc" @selected($sort === 'price_desc')>Price ↓</option>
                <option value="title" @selected($sort === 'title')>Title A–Z</option>
            </select>
            <button type="submit" class="fx-btn-primary shrink-0 px-5">Apply</button>
        </form>
        <a href="{{ route('app.bookings.index') }}" class="fx-btn-secondary text-sm">My bookings</a>
    </div>

    @if ($sessions->isEmpty())
        <div class="fx-glass rounded-3xl px-8 py-16 text-center text-zinc-500">
            <p>No sessions match your filters.</p>
            <a href="{{ route('app.sessions.index') }}" class="mt-4 inline-block text-teal-400 hover:underline">Clear filters</a>
        </div>
    @else
        <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($sessions as $session)
                @php
                    $thumb = $session->session_thumbnail;
                    $thumbUrl = $thumb ? (str_starts_with($thumb, 'http') ? $thumb : asset('storage/'.ltrim($thumb, '/'))) : null;
                @endphp
                <article class="group fx-card-interactive overflow-hidden">
                    <a href="{{ route('app.sessions.show', $session->id) }}" class="block">
                        <div class="relative aspect-[16/10] bg-fx-850">
                            @if ($thumbUrl)
                                <img src="{{ $thumbUrl }}" alt="" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            @else
                                <div class="flex h-full items-center justify-center text-zinc-600">No image</div>
                            @endif
                            @if ($session->price !== null)
                                <span class="absolute bottom-2 right-2 rounded-full bg-fx-950/90 px-2 py-1 text-xs font-bold text-teal-300">${{ number_format((float) $session->price, 2) }}</span>
                            @endif
                        </div>
                        <div class="p-5">
                            <h2 class="font-display text-lg font-bold text-white line-clamp-2">{{ $session->session_title }}</h2>
                            @if ($session->user)
                                <p class="mt-1 text-sm text-zinc-500">{{ $session->user->first_name }} {{ $session->user->last_name }}</p>
                            @endif
                            @if ($session->duration)
                                <p class="mt-2 text-xs uppercase tracking-wider text-zinc-600">{{ $session->duration }} min</p>
                            @endif
                        </div>
                    </a>
                </article>
            @endforeach
        </div>
        <div class="mt-10 flex justify-center">{{ $sessions->links() }}</div>
    @endif
@endsection
