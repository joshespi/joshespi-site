@extends('layouts.app')

@section('title', 'Services & Pricing')
@section('meta_description', 'Fixed-price WordPress, Laravel, and DevOps services. Hacked site cleanup, speed optimization, migrations, custom plugins, Dockerizing, and more.')

@section('content')

{{-- Hero --}}
<section class="bg-ink text-white py-20 px-6">
    <div class="max-w-4xl mx-auto">
        <p class="font-mono text-brand text-sm tracking-widest uppercase mb-4">Services</p>
        <h1 class="text-4xl md:text-5xl font-black leading-tight mb-6 text-center">
            Fixed-price work. No calls required.
        </h1>
        <p class="text-white/70 text-xl max-w-2xl mx-auto text-center">
            Pick a service below or fill the intake form. I'll respond with a written scope and price within 1 business day.
        </p>
    </div>
</section>

{{-- How it works --}}
<section class="py-16 px-6 bg-canvas border-b border-border">
    <div class="max-w-4xl mx-auto">
        <p class="font-mono text-brand text-xs tracking-widest uppercase mb-3">Process</p>
        <h2 class="text-2xl font-black mb-10">How it works</h2>
        <div class="grid md:grid-cols-3 gap-10">
            @foreach([
                ['01', 'Pick a service or fill the form', 'Browse the menu below or describe your project in the intake form at the bottom of this page.'],
                ['02', 'Written quote within 1 business day', 'I send a clear scope + fixed price by email. No surprise add-ons, no "it depends."'],
                ['03', 'Pay 50% to start, 50% on delivery', 'Approve the quote, send the first payment, I do the work. Final payment when you have what you ordered.'],
            ] as [$num, $title, $body])
            <div class="flex gap-4">
                <span class="font-mono text-brand text-3xl font-black shrink-0">{{ $num }}</span>
                <div>
                    <h3 class="font-bold mb-1">{{ $title }}</h3>
                    <p class="text-muted text-sm leading-relaxed">{{ $body }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Tier 1: WordPress --}}
<section class="py-16 px-6 bg-surface">
    <div class="max-w-4xl mx-auto">
        <p class="font-mono text-brand text-xs tracking-widest uppercase mb-2">Tier 1</p>
        <h2 class="text-2xl font-black mb-2">WordPress — Fixed Price</h2>
        <p class="text-muted mb-8">Scoped, priced, and done. No hourly billing surprises.</p>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="border-b-2 border-ink text-left">
                        <th class="pb-3 pr-6 font-bold">Service</th>
                        <th class="pb-3 pr-6 font-bold text-right whitespace-nowrap">Price</th>
                        <th class="pb-3 font-bold whitespace-nowrap">Turnaround</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach([
                        ['Stuck Site Diagnostic', 'Written report: what\'s wrong and what to do', '$149', '2 business days'],
                        ['Speed Optimization', 'Caching, image opt, asset cleanup, before/after PageSpeed report', '$349', '3–5 days'],
                        ['Hacked Site Cleanup', 'Malware removal, hardening, post-cleanup report', '$499', '1–2 days'],
                        ['Site Migration', 'Move WordPress to a new host, zero downtime', '$249', '2 days'],
                        ['Maintenance Retainer — Basic', 'Weekly updates, daily backups, uptime monitoring, 30 min/mo small fixes', '$99/mo', 'Ongoing'],
                        ['Maintenance Retainer — Plus', 'Everything in Basic + security scans, monthly perf check, 2 hr/mo included work', '$199/mo', 'Ongoing'],
                    ] as [$name, $desc, $price, $turnaround])
                    <tr>
                        <td class="py-4 pr-6">
                            <span class="font-semibold">{{ $name }}</span>
                            <span class="block text-muted text-xs mt-0.5">{{ $desc }}</span>
                        </td>
                        <td class="py-4 pr-6 text-right font-mono font-bold text-brand whitespace-nowrap">{{ $price }}</td>
                        <td class="py-4 text-muted whitespace-nowrap">{{ $turnaround }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- Tier 2: Custom Dev --}}
<section class="py-16 px-6 bg-canvas">
    <div class="max-w-4xl mx-auto">
        <p class="font-mono text-brand text-xs tracking-widest uppercase mb-2">Tier 2</p>
        <h2 class="text-2xl font-black mb-2">Custom Development</h2>
        <p class="text-muted mb-8">Quoted per project after intake form review.</p>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="border-b-2 border-ink text-left">
                        <th class="pb-3 pr-6 font-bold">Service</th>
                        <th class="pb-3 font-bold text-right whitespace-nowrap">Starting at</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach([
                        ['Custom WordPress plugin', '$750'],
                        ['Laravel application', '$2,500'],
                        ['Third-party API integration', '$400'],
                        ['Internal admin tool / dashboard', 'Quoted'],
                    ] as [$name, $price])
                    <tr>
                        <td class="py-4 pr-6 font-semibold">{{ $name }}</td>
                        <td class="py-4 text-right font-mono font-bold text-brand">{{ $price }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- Tier 3: DevOps / Security --}}
<section class="py-16 px-6 bg-surface">
    <div class="max-w-4xl mx-auto">
        <p class="font-mono text-brand text-xs tracking-widest uppercase mb-2">Tier 3</p>
        <h2 class="text-2xl font-black mb-2">DevOps &amp; Security</h2>
        <p class="text-muted mb-8">Server work, containers, pipelines, and security audits.</p>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="border-b-2 border-ink text-left">
                        <th class="pb-3 pr-6 font-bold">Service</th>
                        <th class="pb-3 font-bold text-right whitespace-nowrap">Starting at</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach([
                        ['Dockerize an existing application', '$750'],
                        ['WordPress security audit + hardening', '$349'],
                        ['Server setup & hardening (Linux/Ubuntu)', '$499'],
                        ['CI/CD pipeline setup', 'Quoted'],
                    ] as [$name, $price])
                    <tr>
                        <td class="py-4 pr-6 font-semibold">{{ $name }}</td>
                        <td class="py-4 text-right font-mono font-bold text-brand">{{ $price }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- FAQ --}}
<section class="py-16 px-6 bg-canvas">
    <div class="max-w-3xl mx-auto">
        <p class="font-mono text-brand text-xs tracking-widest uppercase mb-3">FAQ</p>
        <h2 class="text-2xl font-black mb-10">Common questions</h2>
        <div class="space-y-8">
            @foreach([
                [
                    'Do I have to get on a call?',
                    'No. Everything runs through the intake form and email. That\'s a deliberate choice — it produces a written record and better communication for both sides.',
                ],
                [
                    'How do I pay?',
                    '50% upfront via Stripe, 50% on delivery. You\'ll receive a Stripe payment link in your quote email.',
                ],
                [
                    'What if the scope changes?',
                    'Scope changes are handled in writing. If you add work, I\'ll send an updated quote before proceeding. No surprise charges.',
                ],
                [
                    'Do you offer ongoing support?',
                    'Yes — the Maintenance Retainer plans cover that. The Basic plan ($99/mo) includes weekly updates, daily backups, and uptime monitoring.',
                ],
                [
                    'What if I\'m not sure which service I need?',
                    'Fill the intake form and describe the situation. I\'ll read it and tell you what makes sense.',
                ],
            ] as [$q, $a])
            <div class="border-b border-border pb-8">
                <h3 class="font-bold mb-2">{{ $q }}</h3>
                <p class="text-muted text-sm leading-relaxed">{{ $a }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Intake form --}}
<section id="intake" class="py-20 px-6 bg-ink text-white">
    <div class="max-w-3xl mx-auto">
        <p class="font-mono text-brand text-sm tracking-widest uppercase mb-4">Get started</p>
        <h2 class="text-3xl font-black mb-3">Send an intake form</h2>
        <p class="text-white/70 mb-10">Fill this out and I'll respond by email within 1 business day with a scope and price — or a follow-up question if I need more detail.</p>
        <div class="bg-white text-ink rounded-lg p-8">
            @livewire('intake-form')
        </div>
    </div>
</section>

@endsection
