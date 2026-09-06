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
    <div class="max-w-6xl mx-auto grid md:grid-cols-2 lg:grid-cols-3 gap-5">

        @foreach([
            [
                'tag' => 'Web App',
                'title' => 'TV Tracker',
                'body' => 'A free, ad-free way to track what you\'re watching. Search and follow any show, mark episodes and whole seasons watched, and get an email when something new airs.',
                'url' => 'https://tvtime.joshespi.com/',
                'github' => 'https://github.com/joshespi/tvtime',
                'image' => 'tvtime',
            ],
            [
                'tag' => 'Web App',
                'title' => 'Complete Finance Tracker',
                'body' => 'A free, self-hosted personal finance app that combines investment portfolio tracking (stocks, crypto, real estate) with envelope budgeting for everyday cash. No subscriptions, no selling your data.',
                'url' => 'https://finance.espifam.com/',
                'github' => 'https://github.com/joshespi/personal-finance-tracker',
                'image' => 'finance',
            ],
            [
                'tag' => 'Game',
                'title' => 'Dungeon Crawler Carl — Fan Game',
                'body' => 'A browser-based dungeon crawler built for fun, inspired by the Dungeon Crawler Carl book series. All assets are generated at runtime — no image files required.',
                'url' => 'https://dcc.joshespi.com/',
                'github' => 'https://github.com/joshespi/dcc-game',
                'image' => 'dcc',
            ],
            [
                'tag' => 'Podcast',
                'title' => 'Crawlers in Waiting',
                'body' => 'A fan-made podcast reading chapters from the Dungeon Crawler Carl series aloud, for fans of the books who want an audio version.',
                'url' => 'https://crawlersinwaiting.com/',
                'github' => 'https://github.com/joshespi/crawlers-in-waiting',
                'image' => 'crawlers',
            ],
            [
                'tag' => 'Web App',
                'title' => 'Soundboard',
                'body' => 'Build your own soundboards from your uploaded audio clips, then play them from a big-button touch grid. Made for my nephew so he doesn\'t need to download sketchy soundboard apps.',
                'url' => 'https://soundboard.joshespi.com/',
                'github' => 'https://github.com/joshespi/soundboard',
                'image' => 'soundboard',
            ],
            [
                'tag' => 'Family Site',
                'title' => 'Espi Family',
                'body' => 'A personal site for my family — a hub for our creative projects and a home base for our YouTube channel.',
                'url' => 'https://espifam.com/',
                'github' => 'https://github.com/joshespi/espifam-site',
                'image' => 'espifam',
            ],
            [
                'tag' => 'Web App',
                'title' => "Espi's Tools",
                'body' => 'A small collection of randomization utilities — weighted picker, passphrase generator, tip calculator, dice roller, coin flip, and a meme builder.',
                'url' => 'https://tools.joshespi.com/',
                'github' => 'https://github.com/joshespi/random-tools',
                'image' => 'tools',
            ],
        ] as $project)
        <div class="bg-surface rounded-lg border border-border flex flex-col overflow-hidden">
            <img src="/images/work/{{ $project['image'] }}.webp" alt="Screenshot of {{ $project['title'] }}" loading="lazy"
                 class="w-full h-36 object-cover object-top border-b border-border">
            <div class="p-6 flex flex-col flex-1">
                <p class="font-mono text-brand text-xs tracking-widest uppercase mb-2">{{ $project['tag'] }}</p>
                <h3 class="text-lg font-bold mb-2">{{ $project['title'] }}</h3>
                <p class="text-muted text-sm mb-4 flex-1">{{ $project['body'] }}</p>
                <div class="flex items-center gap-5">
                    <a href="{{ $project['url'] }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 font-mono text-sm text-brand hover:text-brand-dark transition-colors">
                        Visit site
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                    <a href="{{ $project['github'] }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 font-mono text-sm text-muted hover:text-ink transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.203 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.02 10.02 0 0022 12.017C22 6.484 17.523 2 12 2z"/>
                        </svg>
                        GitHub
                    </a>
                </div>
            </div>
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
