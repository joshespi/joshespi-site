@extends('layouts.app')

@section('title', 'Josh Espinoza')
@section('meta_description', 'Full-stack WordPress, Laravel, and DevOps work from a 15-year engineer. Fixed prices. No calls required. Written quotes within 1 business day.')

@section('content')

{{-- Hero --}}
<section class="bg-ink text-white py-24 px-6">
    <div class="max-w-4xl mx-auto">
        <p class="font-mono text-brand text-sm tracking-widest uppercase mb-6 text-center">// Available for hire</p>
        <h1 class="text-4xl md:text-6xl font-black leading-tight mb-6 text-center">
            Fixed-price WordPress, Laravel, and DevOps work from a 15-year engineer.
        </h1>
        <p class="text-white/70 text-xl mb-10 max-w-2xl mx-auto text-center">
            No calls required. Written quote within 1 business day. You approve, pay 50%, I deliver, you pay the rest.
        </p>
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="/services#intake"
               class="bg-brand hover:bg-brand-dark text-white font-semibold px-8 py-4 rounded text-lg transition-colors">
                Send Intake Form
            </a>
            <a href="/services"
               class="border border-white/30 hover:border-white text-white font-semibold px-8 py-4 rounded text-lg transition-colors">
                See Services &amp; Pricing
            </a>
        </div>
    </div>
</section>

{{-- How it works --}}
<section class="py-20 px-6 bg-canvas">
    <div class="max-w-4xl mx-auto">
        <p class="font-mono text-brand text-xs tracking-widest uppercase mb-3">Process</p>
        <h2 class="text-3xl font-black mb-12">How it works</h2>
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

{{-- Service teasers --}}
<section class="py-20 px-6 bg-surface">
    <div class="max-w-4xl mx-auto">
        <p class="font-mono text-brand text-xs tracking-widest uppercase mb-3">Services</p>
        <h2 class="text-3xl font-black mb-12">What I do</h2>
        <div class="grid md:grid-cols-3 gap-6">

            <div class="bg-canvas rounded-lg p-8 border border-border">
                <p class="font-mono text-brand text-xs tracking-widest uppercase mb-3">WordPress</p>
                <h3 class="text-xl font-bold mb-3">Fixed-price WordPress</h3>
                <p class="text-muted text-sm mb-6">Diagnostics, speed optimization, hacked site cleanup, migrations, and maintenance retainers. Scoped, priced, and done.</p>
                <p class="text-sm font-semibold">From <span class="text-brand text-lg">$99/mo</span> or <span class="text-brand text-lg">$149</span> one-time</p>
            </div>

            <div class="bg-canvas rounded-lg p-8 border border-border">
                <p class="font-mono text-brand text-xs tracking-widest uppercase mb-3">Custom Dev</p>
                <h3 class="text-xl font-bold mb-3">Laravel &amp; WordPress plugins</h3>
                <p class="text-muted text-sm mb-6">Custom plugins, Laravel applications, API integrations, and internal tools. Quoted per project.</p>
                <p class="text-sm font-semibold">From <span class="text-brand text-lg">$400</span></p>
            </div>

            <div class="bg-canvas rounded-lg p-8 border border-border">
                <p class="font-mono text-brand text-xs tracking-widest uppercase mb-3">DevOps / Security</p>
                <h3 class="text-xl font-bold mb-3">Servers, Docker, CI/CD</h3>
                <p class="text-muted text-sm mb-6">Dockerize an app, harden a server, set up a CI/CD pipeline, or get a WordPress security audit.</p>
                <p class="text-sm font-semibold">From <span class="text-brand text-lg">$349</span></p>
            </div>

        </div>
        <div class="mt-10 text-center">
            <a href="/services" class="inline-block border border-ink hover:bg-ink hover:text-white font-semibold px-8 py-3 rounded transition-colors">
                Full pricing &rarr;
            </a>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-20 px-6 bg-brand text-white">
    <div class="max-w-3xl mx-auto text-center">
        <h2 class="text-3xl font-black mb-4">No calls. No pitches. Just work.</h2>
        <p class="text-white/80 mb-8 text-lg">Fill the intake form. I'll respond in writing with a clear scope and price.</p>
        <a href="/services#intake"
           class="inline-block bg-white text-brand hover:bg-surface font-bold px-10 py-4 rounded text-lg transition-colors">
            Start Your Project
        </a>
    </div>
</section>

@endsection
