<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'Full-stack WordPress, Laravel, and DevOps work from a 15-year engineer. Fixed prices. No calls required.')">
    <title>@yield('title', 'Josh Espinoza') — Full Stack Engineer</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-surface text-ink antialiased">

    <header class="bg-ink text-white sticky top-0 z-50 border-b border-white/10">
        <div class="max-w-6xl mx-auto px-6 flex items-center justify-between h-16">
            <a href="/" class="flex items-center gap-3 shrink-0">
                <img src="/logo-light.svg" alt="Josh Espinoza" class="h-8 w-auto">
            </a>
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-white/80">
                <a href="/" class="hover:text-white transition-colors">Home</a>
                <a href="/services" class="hover:text-white transition-colors">Services</a>
                <a href="/work" class="hover:text-white transition-colors">Work</a>
                <a href="/about" class="hover:text-white transition-colors">About</a>
            </nav>
            <a href="/services#intake"
               class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-4 py-2 rounded transition-colors shrink-0">
                Get a Quote
            </a>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="bg-ink text-white/60 mt-24">
        <div class="max-w-6xl mx-auto px-6 py-12 flex flex-col md:flex-row items-center justify-between gap-6 text-sm">
            <p>&copy; {{ date('Y') }} Josh Espinoza. All rights reserved.</p>
            <nav class="flex gap-6">
                <a href="/services" class="hover:text-white transition-colors">Services</a>
                <a href="/work" class="hover:text-white transition-colors">Work</a>
                <a href="/about" class="hover:text-white transition-colors">About</a>
                <a href="/privacy" class="hover:text-white transition-colors">Privacy</a>
                <a href="/terms" class="hover:text-white transition-colors">Terms</a>
            </nav>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
