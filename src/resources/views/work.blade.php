@extends('layouts.app')

@section('title', 'My Work')
@section('meta_description', 'Apps and projects built by Josh Espinoza — a TV show tracker, a self-hosted finance app, and a few side projects for fun.')

@section('content')

<section class="bg-ink text-white py-20 px-6">
    <div class="max-w-4xl mx-auto">
        <p class="font-mono text-brand text-sm tracking-widest uppercase mb-4">My Work</p>
        <h1 class="text-4xl md:text-5xl font-black leading-tight mb-6 text-center">
            Things I've built and shipped.
        </h1>
        <p class="text-white/70 text-xl max-w-2xl mx-auto text-center">
            A mix of client work, personal tools, and projects I built purely for fun. Everything here is live.
        </p>
    </div>
</section>

<section class="py-20 px-6 bg-canvas">
    <div class="max-w-4xl mx-auto grid md:grid-cols-2 gap-6">

        @foreach([
            [
                'tag' => 'Web App',
                'title' => 'TV Tracker',
                'body' => 'A free, ad-free way to track what you\'re watching. Search and follow any show, mark episodes and whole seasons watched, and get an email when something new airs.',
                'url' => 'https://tvtime.joshespi.com/',
            ],
            [
                'tag' => 'Web App',
                'title' => 'Complete Finance Tracker',
                'body' => 'A free, self-hosted personal finance app that combines investment portfolio tracking (stocks, crypto, real estate) with envelope budgeting for everyday cash. No subscriptions, no selling your data.',
                'url' => 'https://finance.espifam.com/',
            ],
            [
                'tag' => 'Game',
                'title' => 'Dungeon Crawler Carl — Fan Game',
                'body' => 'A browser-based dungeon crawler built for fun, inspired by the Dungeon Crawler Carl book series. All assets are generated at runtime — no image files required.',
                'url' => 'https://dcc.joshespi.com/',
            ],
            [
                'tag' => 'Podcast',
                'title' => 'Crawlers in Waiting',
                'body' => 'A fan-made podcast reading chapters from the Dungeon Crawler Carl series aloud, for fans of the books who want an audio version.',
                'url' => 'https://crawlersinwaiting.com/',
            ],
            [
                'tag' => 'Family Site',
                'title' => 'Espi Family',
                'body' => 'A personal site for my family — a hub for our creative projects and a home base for our YouTube channel.',
                'url' => 'https://espifam.com/',
            ],
        ] as $project)
        <div class="bg-surface rounded-lg p-8 border border-border flex flex-col {{ $loop->last && $loop->count % 2 !== 0 ? 'md:col-span-2' : '' }}">
            <p class="font-mono text-brand text-xs tracking-widest uppercase mb-3">{{ $project['tag'] }}</p>
            <h3 class="text-xl font-bold mb-3">{{ $project['title'] }}</h3>
            <p class="text-muted text-sm mb-6 flex-1">{{ $project['body'] }}</p>
            <a href="{{ $project['url'] }}" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center gap-2 font-mono text-sm text-brand hover:text-brand-dark transition-colors">
                Visit site
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>
        @endforeach

    </div>
</section>

<section class="py-20 px-6 bg-brand text-white">
    <div class="max-w-3xl mx-auto text-center">
        <h2 class="text-3xl font-black mb-4">Want something built like this?</h2>
        <p class="text-white/80 mb-8 text-lg">Fill the intake form. I'll respond in writing with a clear scope and price.</p>
        <a href="/services#intake"
           class="inline-block bg-white text-brand hover:bg-surface font-bold px-10 py-4 rounded text-lg transition-colors">
            Start Your Project
        </a>
    </div>
</section>

@endsection
