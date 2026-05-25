@extends('layouts.app')

@section('title', 'About Josh Espinoza')
@section('meta_description', '15 years in web development. PHP, Laravel, WordPress, Docker, Linux. Full Stack Engineer at Provo City School District.')

@section('content')

<section class="bg-ink text-white py-20 px-6">
    <div class="max-w-4xl mx-auto">
        <p class="font-mono text-brand text-sm tracking-widest uppercase mb-4">About</p>
        <h1 class="text-4xl md:text-5xl font-black leading-tight mb-6 text-center">
            15 years building things that run.
        </h1>
        <p class="text-white/70 text-xl max-w-2xl mx-auto text-center">
            Full Stack Software Engineer at Provo City School District. Started in 2011 as a Web Development Engineer, now leading teams and managing containerized production deployments.
        </p>
    </div>
</section>

<section class="py-20 px-6 bg-canvas">
    <div class="max-w-4xl mx-auto grid md:grid-cols-2 gap-16">

        <div>
            <p class="font-mono text-brand text-xs tracking-widest uppercase mb-4">Background</p>
            <h2 class="text-2xl font-black mb-6">The work</h2>
            <div class="space-y-4 text-muted leading-relaxed">
                <p>
                    I've spent 15 years in web development — starting with WordPress and PHP, growing into Laravel, Docker, CI/CD pipelines, and server management on Linux/Ubuntu.
                </p>
                <p>
                    Most of my day job involves designing deployment architecture, managing containerized applications, and writing the kind of code that doesn't need to be rewritten every year.
                </p>
                <p>
                    The freelance work here is the same standard. I don't ship and disappear. I write clear scope before starting and document what I deliver.
                </p>
            </div>
        </div>

        <div>
            <p class="font-mono text-brand text-xs tracking-widest uppercase mb-4">Stack</p>
            <h2 class="text-2xl font-black mb-6">What I work in</h2>
            <div class="grid grid-cols-2 gap-3">
                @foreach([
                    'PHP', 'Laravel', 'WordPress', 'MySQL / MariaDB',
                    'Docker', 'Linux / Ubuntu', 'Bash', 'JavaScript',
                    'HTML / CSS', 'Tailwind CSS', 'Git / CI/CD', 'App Security',
                ] as $skill)
                <div class="bg-surface rounded px-3 py-2 text-sm font-mono font-medium">
                    {{ $skill }}
                </div>
                @endforeach
            </div>
        </div>

    </div>
</section>

<section class="py-20 px-6 bg-surface">
    <div class="max-w-4xl mx-auto">
        <p class="font-mono text-brand text-xs tracking-widest uppercase mb-4">Why async</p>
        <h2 class="text-2xl font-black mb-6">No calls required — and that's a feature.</h2>
        <div class="max-w-2xl space-y-4 text-muted leading-relaxed">
            <p>
                Written communication produces a better record than a phone call. You get a scope document, not a memory of a conversation. I get to think carefully before I respond, not on the spot.
            </p>
            <p>
                Every service on this site is designed around written intake, written quotes, and email-based delivery. Clients who hate sales calls tend to appreciate this.
            </p>
        </div>
        <div class="mt-10">
            <a href="/services#intake"
               class="inline-block bg-brand hover:bg-brand-dark text-white font-semibold px-8 py-4 rounded transition-colors">
                Send Intake Form
            </a>
        </div>
    </div>
</section>

@endsection
