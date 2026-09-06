@extends('layouts.app')

@section('title', 'About Josh Espinoza')
@section('meta_description', '15 years in web development. PHP, Laravel, WordPress, Docker, Linux. Full Stack Engineer at Provo City School District.')

@section('content')

<section class="bg-ink text-white py-20 px-6">
    <div class="max-w-4xl mx-auto">
        <p class="font-mono text-brand text-sm tracking-widest uppercase mb-4 text-center">About</p>
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
            <a href="https://github.com/joshespi" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center gap-2 mt-6 font-mono text-sm text-brand hover:text-brand-dark transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.203 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.02 10.02 0 0022 12.017C22 6.484 17.523 2 12 2z"/>
                </svg>
                See my work on GitHub
            </a>
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
