@php
    use Illuminate\Support\Facades\View;

    // View::getSection is the supported way to read a section into a variable;
    // $__env->yieldContent() is Blade internals and can move without notice.
    // Both are needed as variables because the title and description are reused
    // across <title>, the meta description, Open Graph, and Twitter tags.
    $pageTitle = trim(View::getSection('title', 'Josh Espinoza')) . ' — Full Stack Engineer';
    $pageDescription = trim(View::getSection('meta_description', 'Full-stack WordPress, Laravel, and DevOps work from a 15-year engineer. Fixed prices. No calls required.'));
    $canonical = url()->current();
    // "/" only matches exactly; the rest match their subpaths too.
    $isActive = fn (string $href) => $href === '/'
        ? request()->is('/')
        : request()->is(ltrim($href, '/') . '*');
    $pages = config('pages');
    $navLinks = array_filter($pages, fn ($page) => $page['nav']);
    $footerLinks = array_filter($pages, fn ($page) => $page['footer']);
@endphp
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $pageDescription }}">
    <title>{{ $pageTitle }}</title>
    <link rel="canonical" href="{{ $canonical }}">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Josh Espinoza">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ url('/og.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="Josh Espinoza — Full Stack Engineer">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ url('/og.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    @include('partials.schema')
    @stack('schema')
</head>
<body class="bg-surface text-ink antialiased">

    <a href="#main"
       class="sr-only focus:not-sr-only focus:absolute focus:top-3 focus:left-3 focus:z-[60] focus:bg-white focus:text-ink focus:font-semibold focus:px-4 focus:py-2 focus:rounded focus:ring-2 focus:ring-brand">
        Skip to content
    </a>

    <header x-data="{ open: false }" class="bg-ink text-white sticky top-0 z-50 border-b border-white/10">
        <div class="max-w-6xl mx-auto px-6 flex items-center justify-between h-16">
            <a href="/" class="flex items-center gap-3 shrink-0">
                <img src="/logo-light.svg" alt="Josh Espinoza" width="160" height="32" class="h-8 w-auto">
            </a>

            <nav aria-label="Main" class="hidden md:flex items-center gap-8 text-sm font-medium text-white/80">
                @foreach($navLinks as $page)
                    @php $active = $isActive($page['path']); @endphp
                    <a href="{{ $page['path'] }}"
                       @if($active) aria-current="page" @endif
                       class="transition-colors {{ $active ? 'text-white border-b-2 border-brand pb-0.5' : 'hover:text-white' }}">
                        {{ $page['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="flex items-center gap-2">
                <a href="/services#intake"
                   class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-4 py-2 rounded transition-colors shrink-0">
                    Get a Quote
                </a>
                <button type="button"
                        x-on:click="open = !open"
                        :aria-expanded="open ? 'true' : 'false'"
                        aria-controls="mobile-nav"
                        aria-label="Toggle navigation menu"
                        class="md:hidden p-2 -mr-2 text-white/80 hover:text-white transition-colors">
                    <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="open" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <nav id="mobile-nav" x-show="open" x-cloak x-collapse aria-label="Mobile"
             class="md:hidden border-t border-white/10 bg-ink">
            <div class="max-w-6xl mx-auto px-6 py-2 flex flex-col">
                @foreach($navLinks as $page)
                    @php $active = $isActive($page['path']); @endphp
                    <a href="{{ $page['path'] }}"
                       @if($active) aria-current="page" @endif
                       class="py-3 text-sm font-medium border-b border-white/5 last:border-0 transition-colors {{ $active ? 'text-brand' : 'text-white/80 hover:text-white' }}">
                        {{ $page['label'] }}
                    </a>
                @endforeach
            </div>
        </nav>
    </header>

    <main id="main">
        @yield('content')
    </main>

    <footer class="bg-ink text-white/60 mt-24">
        <div class="max-w-6xl mx-auto px-6 py-12 flex flex-col md:flex-row items-center justify-between gap-6 text-sm">
            <p>&copy; {{ date('Y') }} Josh Espinoza. All rights reserved.</p>
            <nav aria-label="Footer" class="flex flex-wrap justify-center gap-x-6 gap-y-2">
                @foreach($footerLinks as $page)
                    <a href="{{ $page['path'] }}" class="hover:text-white transition-colors">{{ $page['label'] }}</a>
                @endforeach
            </nav>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
