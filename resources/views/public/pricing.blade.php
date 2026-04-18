@extends('layouts.public')

@section('title', 'Pricing')

@section('content')
    <section class="relative border-b border-white/[0.06] fx-hero-mesh">
        <div class="fx-container py-16 text-center md:py-20">
            <p class="fx-badge mb-4">Membership</p>
            <h1 class="fx-section-title">Simple, transparent tiers</h1>
            <p class="fx-prose mx-auto mt-4 max-w-2xl">
                Illustrative pricing — live session rates appear in your account after sign-in. No hidden fees on the essentials.
            </p>
        </div>
    </section>

    <section class="py-16 md:py-24">
        <div class="fx-container">
            <div class="grid gap-6 lg:grid-cols-3 lg:items-stretch">
                @php
                    $tiers = [
                        ['name' => 'Starter', 'price' => '29', 'desc' => 'Core session access and basic scheduling.', 'highlight' => false],
                        ['name' => 'Pro', 'price' => '59', 'desc' => 'More sessions, bookmarks, and full history.', 'highlight' => true],
                        ['name' => 'Elite', 'price' => '99', 'desc' => 'Priority booking and deeper coaching where offered.', 'highlight' => false],
                    ];
                @endphp
                @foreach ($tiers as $tier)
                    <article class="relative flex flex-col rounded-3xl border p-8 {{ $tier['highlight'] ? 'border-teal-500/40 bg-gradient-to-b from-teal-500/10 to-fx-900/40 shadow-fx-glow md:scale-[1.02]' : 'fx-glass border-white/[0.08]' }}">
                        @if ($tier['highlight'])
                            <span class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full border border-teal-500/40 bg-fx-950 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-teal-300">Most popular</span>
                        @endif
                        <h2 class="font-display text-lg font-bold text-white">{{ $tier['name'] }}</h2>
                        <p class="mt-6 flex items-baseline gap-1">
                            <span class="text-sm font-medium text-zinc-500">$</span>
                            <span class="font-display text-5xl font-extrabold text-white">{{ $tier['price'] }}</span>
                            <span class="text-zinc-500">/mo</span>
                        </p>
                        <p class="mt-6 flex-1 text-sm leading-relaxed text-zinc-500">{{ $tier['desc'] }}</p>
                        <a href="{{ route('auth.login') }}" class="mt-8 {{ $tier['highlight'] ? 'fx-btn-primary w-full justify-center' : 'fx-btn-secondary w-full justify-center' }}">Get started</a>
                    </article>
                @endforeach
            </div>
            <p class="mt-12 text-center text-sm text-zinc-600">
                See location-specific rates and session bundles after you log in, or
                <a href="{{ route('public.sessions') }}" class="font-medium text-teal-400 hover:text-teal-300">browse published sessions</a>.
            </p>
        </div>
    </section>
@endsection
