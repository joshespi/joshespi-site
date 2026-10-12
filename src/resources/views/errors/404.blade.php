@extends('layouts.app')

@section('title', 'Page Not Found')
@section('meta_description', "That page doesn't exist — here's where to go instead.")

@section('content')

<x-error-hero code="404" heading="That page doesn't exist." primary-href="/services" primary-label="Services & Pricing">
    The link may be out of date, or the URL might have a typo. Here's where you probably meant to go.
</x-error-hero>

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
