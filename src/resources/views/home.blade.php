@extends('layouts.app')

@section('title', 'Josh Espinoza')
@section('meta_description', 'Full-stack WordPress, Laravel, and DevOps work from a 15-year engineer. Fixed prices. No calls required. Written quotes within 1 business day.')

@section('content')

<section class="studio-hero">
    <div class="studio-wrap studio-intro">
        <div>
            <p class="studio-label availability">Available for freelance work</p>
            <h1>Full-stack<br><em>engineering.</em></h1>
            <p class="intro-copy">I’m Josh Espinoza. I bring 15 years of experience to WordPress, Laravel, and DevOps projects. You get a fixed price and a written quote within one business day.</p>
            <div class="intro-actions">
                <a class="studio-button" href="/services#intake">Start a project</a>
                <a class="studio-link" href="/services">Services &amp; pricing</a>
            </div>
        </div>
        <a class="feature-print" href="/work#tvtime">
            <div class="print-art">
                <div class="screen-frame"><img src="/images/work/tvtime.webp" alt="TV Tracker application interface" width="1000" height="625" fetchpriority="high"></div>
            </div>
            <div class="print-caption"><span><small class="studio-label">Featured project</small><strong>TV Tracker</strong></span><span class="print-category">Web app</span></div>
        </a>
    </div>
</section>
{{-- Service teasers --}}
<section class="py-20 px-6 bg-surface">
    <div class="studio-wrap">
        <p class="font-mono text-brand text-xs tracking-widest uppercase mb-3">Services</p>
        <h2 class="mb-12">Engineering services</h2>
        <div class="grid md:grid-cols-3 gap-6">

            <div class="service-summary">
                <p class="font-mono text-brand text-xs tracking-widest uppercase mb-3">WordPress</p>
                <h3 class="text-xl font-bold mb-3">Fixed-price WordPress</h3>
                <p class="text-muted text-base leading-relaxed mb-6">Diagnostics, speed optimization, hacked site cleanup, migrations, and maintenance retainers. Scoped, priced, and done.</p>
                <p class="text-sm font-semibold">From <span class="text-brand text-lg">$99/mo</span> or <span class="text-brand text-lg">$149</span> one-time</p>
            </div>

            <div class="service-summary">
                <p class="font-mono text-brand text-xs tracking-widest uppercase mb-3">Custom Dev</p>
                <h3 class="text-xl font-bold mb-3">Laravel &amp; WordPress plugins</h3>
                <p class="text-muted text-base leading-relaxed mb-6">Custom plugins, Laravel applications, API integrations, and internal tools. Quoted per project.</p>
                <p class="text-sm font-semibold">From <span class="text-brand text-lg">$400</span></p>
            </div>

            <div class="service-summary">
                <p class="font-mono text-brand text-xs tracking-widest uppercase mb-3">DevOps / Security</p>
                <h3 class="text-xl font-bold mb-3">Servers, Docker, CI/CD</h3>
                <p class="text-muted text-base leading-relaxed mb-6">Dockerize an app, harden a server, set up a CI/CD pipeline, or get a WordPress security audit.</p>
                <p class="text-sm font-semibold">From <span class="text-brand text-lg">$349</span></p>
            </div>

        </div>
        <div class="mt-10 text-center">
            <a href="/services" class="inline-block border border-ink hover:bg-ink hover:text-white font-semibold px-8 py-3 rounded transition-colors">
                View full pricing
            </a>
        </div>
    </div>
</section>

{{-- How it works --}}
<section class="py-20 px-6 bg-canvas">
    <div class="studio-wrap">
        <p class="font-mono text-brand text-xs tracking-widest uppercase mb-3">Process</p>
        <h2 class="mb-12">How it works</h2>
        <div class="grid md:grid-cols-3 gap-10">
            @foreach([
                ['01', 'Pick a service', 'Browse the fixed-price menu or fill the intake form with your project details.'],
                ['02', 'Get a written quote', 'I send scope + price in writing within 1 business day. No surprise add-ons.'],
                ['03', 'Pay half, I deliver', 'Approve and pay 50% to start. Final payment on delivery.'],
            ] as [$num, $title, $body])
            <div>
                <span class="font-mono text-brand text-4xl font-black">{{ $num }}</span>
                <h3 class="text-lg font-bold mt-3 mb-2">{{ $title }}</h3>
                <p class="text-muted text-sm leading-relaxed">{{ $body }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-20 px-6 bg-brand text-white">
    <div class="max-w-3xl mx-auto text-center">
        <h2 class="mb-4">No calls. No pitches. Just work.</h2>
        <p class="text-white/80 mb-8 text-lg">Fill the intake form. I'll respond in writing with a clear scope and price.</p>
        <a href="/services#intake"
           class="inline-block bg-white text-brand hover:bg-surface font-bold px-10 py-4 rounded text-lg transition-colors">
            Start Your Project
        </a>
    </div>
</section>

@endsection
