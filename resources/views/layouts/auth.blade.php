<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Account') · FitX</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="fx-public min-h-full">
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="fx-orb -left-40 top-20 h-80 w-80 bg-teal-500/20"></div>
        <div class="fx-orb -right-20 top-1/3 h-96 w-96 bg-lime-400/10"></div>
    </div>

    <div class="relative z-10 flex min-h-full flex-col">
        <header class="border-b border-white/[0.06] bg-fx-950/80 backdrop-blur-xl">
            <div class="fx-container flex h-14 items-center justify-between">
                <a href="{{ route('public.home') }}" class="font-display text-lg font-bold text-white">
                    <span class="mr-2 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-teal-400 to-emerald-600 text-xs font-black text-fx-950">FX</span>
                    Fit<span class="text-teal-400">X</span>
                </a>
                <a href="{{ route('public.home') }}" class="text-sm font-medium text-zinc-500 hover:text-white">← Back to site</a>
            </div>
        </header>

        <main class="flex flex-1 items-center justify-center px-4 py-12 sm:px-6">
            <div class="w-full max-w-[420px]">
                @if (session('status'))
                    <div class="mb-6 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-center text-sm text-emerald-200">
                        {{ session('status') }}
                    </div>
                @endif
                @yield('content')
            </div>
        </main>

        <footer class="border-t border-white/[0.06] py-6 text-center text-xs text-zinc-600">
            <p>&copy; {{ date('Y') }} FitX</p>
        </footer>
    </div>
</body>
</html>
