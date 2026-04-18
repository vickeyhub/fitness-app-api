<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'FitX — book sessions with trainers and gyms, train smarter.')">
    <title>@yield('title', 'FitX') · Train smarter</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="fx-public min-h-full">
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="fx-orb -left-40 top-20 h-80 w-80 bg-teal-500/20"></div>
        <div class="fx-orb -right-20 top-1/3 h-96 w-96 bg-lime-400/10"></div>
        <div class="fx-orb bottom-0 left-1/3 h-64 w-64 bg-rose-500/10"></div>
    </div>

    <header class="sticky top-0 z-50 border-b border-white/[0.06] bg-fx-950/75 backdrop-blur-xl supports-[backdrop-filter]:bg-fx-950/60">
        @php
            $nav = [
                ['route' => 'public.sessions', 'label' => 'Sessions', 'pattern' => 'public.sessions*'],
                ['route' => 'public.trainers', 'label' => 'Trainers', 'pattern' => 'public.trainers*'],
                ['route' => 'public.gyms', 'label' => 'Gyms', 'pattern' => 'public.gyms*'],
                ['route' => 'public.about', 'label' => 'About', 'pattern' => 'public.about'],
                ['route' => 'public.pricing', 'label' => 'Pricing', 'pattern' => 'public.pricing'],
                ['route' => 'public.contact', 'label' => 'Contact', 'pattern' => 'public.contact'],
            ];
        @endphp
        <div class="fx-container">
            <div class="flex h-16 items-center justify-between gap-4 lg:h-[4.25rem]">
                <a href="{{ route('public.home') }}" class="group flex shrink-0 items-center gap-2 font-display text-xl font-extrabold tracking-tight text-white">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-teal-400 to-emerald-600 text-sm font-black text-fx-950 shadow-fx-glow transition group-hover:scale-105">FX</span>
                    <span>Fit<span class="bg-gradient-to-r from-teal-300 to-lime-300 bg-clip-text text-transparent">X</span></span>
                </a>

                <nav class="hidden items-center gap-1 lg:flex" aria-label="Primary">
                    @foreach ($nav as $item)
                        <a
                            href="{{ route($item['route']) }}"
                            class="fx-nav-link rounded-lg px-3 py-2 {{ request()->routeIs($item['pattern']) ? 'fx-nav-link-active' : '' }}"
                        >{{ $item['label'] }}</a>
                    @endforeach
                </nav>

                <div class="flex items-center gap-2 sm:gap-3">
                    @auth
                        <a href="{{ route('app.dashboard') }}" class="fx-btn-ghost hidden sm:inline-flex">Dashboard</a>
                        <form method="post" action="{{ route('logout') }}" class="hidden sm:inline">
                            @csrf
                            <button type="submit" class="fx-btn-ghost text-sm">Sign out</button>
                        </form>
                    @else
                        <a href="{{ route('auth.login') }}" class="fx-btn-ghost hidden sm:inline-flex">Member login</a>
                    @endauth
                    <a href="{{ route('public.sessions') }}" class="fx-btn-primary hidden text-xs sm:inline-flex sm:text-sm">Explore sessions</a>
                    <button
                        type="button"
                        id="fx-nav-toggle"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-white lg:hidden"
                        aria-expanded="false"
                        aria-controls="fx-mobile-nav"
                        aria-label="Open menu"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="border-t border-white/[0.06] lg:hidden">
            <nav id="fx-mobile-nav" class="fx-container hidden flex flex-col gap-1 py-4" aria-label="Mobile">
                @foreach ($nav as $item)
                    <a
                        href="{{ route($item['route']) }}"
                        class="rounded-xl px-4 py-3 text-sm font-medium {{ request()->routeIs($item['pattern']) ? 'bg-white/10 text-white' : 'text-zinc-400 hover:bg-white/5 hover:text-white' }}"
                    >{{ $item['label'] }}</a>
                @endforeach
                <hr class="my-2 border-white/10">
                @auth
                    <a href="{{ route('app.dashboard') }}" class="rounded-xl px-4 py-3 text-sm font-medium text-zinc-300 hover:bg-white/5">Dashboard</a>
                    <form method="post" action="{{ route('logout') }}" class="px-4">
                        @csrf
                        <button type="submit" class="w-full rounded-xl px-4 py-3 text-left text-sm font-medium text-zinc-300 hover:bg-white/5">Sign out</button>
                    </form>
                @else
                    <a href="{{ route('auth.login') }}" class="rounded-xl px-4 py-3 text-sm font-medium text-zinc-300 hover:bg-white/5">Member login</a>
                @endauth
                <a href="{{ route('public.sessions') }}" class="fx-btn-primary mx-4 mt-2 text-center">Explore sessions</a>
            </nav>
        </div>
    </header>

    <main class="relative z-10">
        @yield('content')
    </main>

    <footer class="relative z-10 mt-20 border-t border-white/[0.06] bg-fx-900/50">
        <div class="fx-container py-14 lg:py-16">
            <div class="grid gap-12 lg:grid-cols-12 lg:gap-8">
                <div class="lg:col-span-4">
                    <a href="{{ route('public.home') }}" class="font-display text-lg font-bold text-white">Fit<span class="text-teal-400">X</span></a>
                    <p class="mt-4 max-w-sm text-sm leading-relaxed text-zinc-500">
                        Your hub for sessions, trainers, and gyms — book and pay in one place, on web or mobile.
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-8 sm:grid-cols-3 lg:col-span-8 lg:justify-end">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Discover</p>
                        <ul class="mt-4 space-y-2 text-sm text-zinc-400">
                            <li><a href="{{ route('public.sessions') }}" class="hover:text-teal-400">Sessions</a></li>
                            <li><a href="{{ route('public.trainers') }}" class="hover:text-teal-400">Trainers</a></li>
                            <li><a href="{{ route('public.gyms') }}" class="hover:text-teal-400">Gyms</a></li>
                        </ul>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Company</p>
                        <ul class="mt-4 space-y-2 text-sm text-zinc-400">
                            <li><a href="{{ route('public.about') }}" class="hover:text-teal-400">About</a></li>
                            <li><a href="{{ route('public.pricing') }}" class="hover:text-teal-400">Pricing</a></li>
                            <li><a href="{{ route('public.contact') }}" class="hover:text-teal-400">Contact</a></li>
                        </ul>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Members</p>
                        <ul class="mt-4 space-y-2 text-sm text-zinc-400">
                            @auth
                                <li><a href="{{ route('app.dashboard') }}" class="hover:text-teal-400">Dashboard</a></li>
                                <li><a href="{{ route('app.profile') }}" class="hover:text-teal-400">Profile</a></li>
                                <li>
                                    <form method="post" action="{{ route('logout') }}" class="inline">
                                        @csrf
                                        <button type="submit" class="hover:text-teal-400">Sign out</button>
                                    </form>
                                </li>
                            @else
                                <li><a href="{{ route('auth.login') }}" class="hover:text-teal-400">Log in</a></li>
                                <li><a href="{{ route('auth.register') }}" class="hover:text-teal-400">Sign up</a></li>
                            @endauth
                        </ul>
                    </div>
                </div>
            </div>
            <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-white/[0.06] pt-8 text-xs text-zinc-600 sm:flex-row">
                <p>&copy; {{ date('Y') }} FitX. All rights reserved.</p>
                <p class="text-zinc-600">Built for clarity, speed, and trust.</p>
            </div>
        </div>
    </footer>

    <script>
        (function () {
            var btn = document.getElementById('fx-nav-toggle');
            var panel = document.getElementById('fx-mobile-nav');
            if (!btn || !panel) return;
            btn.addEventListener('click', function () {
                panel.classList.toggle('hidden');
                var isOpen = !panel.classList.contains('hidden');
                btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                btn.setAttribute('aria-label', isOpen ? 'Close menu' : 'Open menu');
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
