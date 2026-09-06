@extends('layouts.app')

@section('title', 'Page Not Found')
@section('meta_description', "That page doesn't exist — here's where to go instead.")

@section('content')

<section class="bg-ink text-white py-24 px-6">
    <div class="max-w-3xl mx-auto text-center">
        <p class="font-mono text-brand text-sm tracking-widest uppercase mb-6 text-center">Error 404</p>
        <h1 class="text-4xl md:text-5xl font-black leading-tight mb-6">
            That page doesn't exist.
        </h1>
        <p class="text-white/70 text-xl mb-10 max-w-xl mx-auto">
            The link may be out of date, or the URL might have a typo. Here's where you probably meant to go.
        </p>
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="/services"
               class="bg-brand hover:bg-brand-dark text-white font-semibold px-8 py-4 rounded text-lg transition-colors">
                Services &amp; Pricing
            </a>
            <a href="/"
               class="border border-white/30 hover:border-white text-white font-semibold px-8 py-4 rounded text-lg transition-colors">
                Back Home
            </a>
        </div>
    </div>
</section>

<section class="py-20 px-6 bg-canvas">
    <div class="max-w-4xl mx-auto">
        <p class="font-mono text-brand text-xs tracking-widest uppercase mb-3">Everything else</p>
        <h2 class="text-2xl font-black mb-10">All pages</h2>
        <div class="grid md:grid-cols-3 gap-6">
            {{-- Driven off config/pages.php so this grid can't drift from the
                 real page list. Home is skipped — the hero above already links
                 there with its own button. --}}
            @foreach(array_filter(config('pages'), fn ($page) => $page['nav'] && $page['path'] !== '/') as $page)
            <a href="{{ $page['path'] }}" class="bg-surface rounded-lg p-6 border border-border hover:border-brand transition-colors block">
                <h3 class="font-bold mb-2">{{ $page['label'] }}</h3>
                <p class="text-muted text-sm">{{ $page['blurb'] }}</p>
            </a>
            @endforeach
        </div>
    </div>
</section>

@endsection
