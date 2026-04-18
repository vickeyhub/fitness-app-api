@extends('layouts.public')

@section('title', 'Home')
@section('meta_description', 'Book fitness sessions with trusted trainers and gyms. FitX — train smarter.')

@section('content')
    <section class="relative overflow-hidden fx-hero-mesh">
        <div class="fx-container relative pb-20 pt-14 md:pb-28 md:pt-20 lg:pt-24">
            <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">
                <div>
                    <p class="fx-badge mb-6">Train smarter · Book anywhere</p>
                    <h1 class="font-display text-4xl font-extrabold leading-[1.1] tracking-tight text-white sm:text-5xl lg:text-6xl">
                        Your next session is
                        <span class="bg-gradient-to-r from-teal-300 via-emerald-300 to-lime-300 bg-clip-text text-transparent">one tap away</span>
                    </h1>
                    <p class="mt-6 max-w-lg text-lg text-zinc-400">
                        Discover published sessions, compare trainers and gyms, then book and pay when you sign in — same data as our mobile app.
                    </p>
                    <div class="mt-10 flex flex-wrap items-center gap-4">
                        <a href="{{ route('public.sessions') }}" class="fx-btn-primary px-8 py-3 text-base">Browse sessions</a>
                        <a href="{{ route('public.pricing') }}" class="fx-btn-secondary">View plans</a>
                    </div>
                    <dl class="mt-14 grid grid-cols-3 gap-6 border-t border-white/10 pt-10">
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-600">Journey</dt>
                            <dd class="mt-1 font-display text-2xl font-bold text-white">Book</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-600">Coaches</dt>
                            <dd class="mt-1 font-display text-2xl font-bold text-white">Verified</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-600">Access</dt>
                            <dd class="mt-1 font-display text-2xl font-bold text-white">Web + app</dd>
                        </div>
                    </dl>
                </div>
                <div class="relative">
                    <div class="fx-glass-strong absolute -right-6 -top-6 h-24 w-24 animate-fx-float rounded-3xl sm:h-28 sm:w-28"></div>
                    <div class="relative overflow-hidden rounded-3xl border border-white/10 shadow-fx-card">
                        <div class="absolute inset-0 bg-gradient-to-t from-fx-950/80 via-transparent to-transparent"></div>
                        <img
                            src="{{ asset('images/hero.jpg') }}"
                            alt="Athletes training in a modern gym"
                            class="aspect-[4/5] w-full object-cover sm:aspect-[5/6] lg:max-h-[520px]"
                            loading="eager"
                            fetchpriority="high"
                        >
                        <div class="absolute bottom-0 left-0 right-0 p-6 sm:p-8">
                            <p class="text-sm font-medium text-white/90">“Clear schedules, fair pricing, zero guesswork.”</p>
                            <p class="mt-1 text-xs text-zinc-500">What members tell us matters — we design for trust.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="relative border-y border-white/[0.06] bg-fx-900/40 py-16 md:py-24">
        <div class="fx-container">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="fx-section-title">How it works</h2>
                <p class="fx-prose mt-4">Three simple steps from discovery to training — built for busy schedules.</p>
            </div>
            <div class="mt-14 grid gap-6 md:grid-cols-3">
                @foreach ([
                    ['step' => '01', 'title' => 'Discover', 'body' => 'Search sessions by title and explore full details before you commit.'],
                    ['step' => '02', 'title' => 'Book', 'body' => 'Sign in to choose a slot, reserve your place, and pay securely.'],
                    ['step' => '03', 'title' => 'Train', 'body' => 'Use workouts, nutrition, and social tools in your account — web or app.'],
                ] as $block)
                    <article class="group fx-card-interactive p-8">
                        <span class="font-display text-5xl font-extrabold text-teal-500/30">{{ $block['step'] }}</span>
                        <h3 class="mt-4 font-display text-xl font-bold text-white">{{ $block['title'] }}</h3>
                        <p class="mt-3 text-sm leading-relaxed text-zinc-500">{{ $block['body'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-16 md:py-24">
        <div class="fx-container">
            <div class="grid items-stretch gap-8 lg:grid-cols-2 lg:gap-12">
                <div class="fx-glass flex flex-col justify-center rounded-3xl p-8 md:p-10">
                    <h2 class="font-display text-2xl font-bold text-white md:text-3xl">Built for outcomes, not noise</h2>
                    <p class="mt-4 text-zinc-400">
                        Modern fitness products win on clarity: transparent pricing, obvious next steps, and fast paths to book. We keep the surface calm so you can focus on training.
                    </p>
                    <ul class="mt-8 space-y-4">
                        @foreach (['Role-aware flows for members, trainers, and gyms', 'Server-backed validation — no web-only rules', 'Responsive layouts for phones and desktops'] as $line)
                            <li class="flex gap-3 text-sm text-zinc-300">
                                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-teal-500/20 text-teal-400">
                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                </span>
                                {{ $line }}
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <article class="fx-glass rounded-2xl p-6">
                        <p class="text-sm font-semibold text-teal-400">Social proof</p>
                        <p class="mt-3 text-lg font-medium text-white">“Finally a schedule I can stick to.”</p>
                        <p class="mt-4 text-xs text-zinc-600">— Member feedback, anonymized</p>
                    </article>
                    <article class="fx-glass rounded-2xl p-6 sm:col-span-2 lg:col-span-1 lg:row-span-1">
                        <p class="text-sm font-semibold text-lime-300/90">Trainers</p>
                        <p class="mt-3 text-lg font-medium text-white">Showcase your sessions to new clients on the open web.</p>
                        <a href="{{ route('public.trainers') }}" class="mt-4 inline-flex text-sm font-semibold text-teal-400 hover:text-teal-300">Meet trainers →</a>
                    </article>
                    <article class="fx-glass rounded-2xl p-6">
                        <p class="text-sm font-semibold text-rose-300/90">Gyms</p>
                        <p class="mt-3 text-lg font-medium text-white">Venues stay discoverable with clear profiles.</p>
                        <a href="{{ route('public.gyms') }}" class="mt-4 inline-flex text-sm font-semibold text-teal-400 hover:text-teal-300">Find gyms →</a>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section class="border-t border-white/[0.06] bg-gradient-to-b from-fx-900/80 to-fx-950 py-16 md:py-24">
        <div class="fx-container text-center">
            <h2 class="fx-section-title">Ready to start?</h2>
            <p class="fx-prose mx-auto mt-4 max-w-xl">
                Log in with your member account or reach out — we respond to every message.
            </p>
            <div class="mt-10 flex flex-wrap justify-center gap-4">
                <a href="{{ route('auth.login') }}" class="fx-btn-primary px-8">Member login</a>
                <a href="{{ route('public.contact') }}" class="fx-btn-secondary">Contact us</a>
            </div>
        </div>
    </section>
@endsection
