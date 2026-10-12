{{-- Back-office shell. Rendered two ways — as the #[Layout] of the admin
     Livewire pages, and as <x-layouts::admin> by the login view — so it reads
     $title with a fallback instead of declaring @props. --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ isset($title) ? "{$title} — " : '' }}Admin · Josh Espinoza</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    @vite('resources/css/app.css')
    @livewireStyles
</head>
<body class="bg-canvas text-ink antialiased min-h-screen">

    <a href="#main"
       class="sr-only focus:not-sr-only focus:absolute focus:top-3 focus:left-3 focus:z-[60] focus:bg-white focus:text-ink focus:font-semibold focus:px-4 focus:py-2 focus:rounded focus:ring-2 focus:ring-brand">
        Skip to content
    </a>

    <header class="bg-ink text-white border-b border-white/10">
        <div class="max-w-6xl mx-auto px-6 flex items-center justify-between h-14">
            <a href="{{ auth()->check() ? route('admin.intakes.index') : '/' }}" class="flex items-center gap-3">
                <img src="/logo-light.svg" alt="Josh Espinoza" width="140" height="28" class="h-7 w-auto">
                <span class="font-mono text-xs uppercase tracking-widest text-white/60">Admin</span>
            </a>

            @auth
            <div class="flex items-center gap-6 text-sm">
                <a href="/" class="text-white/70 hover:text-white transition-colors">View site</a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="text-white/70 hover:text-white transition-colors">Log out</button>
                </form>
            </div>
            @endauth
        </div>
    </header>

    <main id="main" class="max-w-6xl mx-auto px-6 py-10">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
