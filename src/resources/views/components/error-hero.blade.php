@props(['code', 'heading', 'primaryHref', 'primaryLabel'])
{{-- The dark hero every error page opens with. The slot is the lede. --}}
<section class="bg-ink text-white py-24 px-6">
    <div class="max-w-3xl mx-auto text-center">
        <p class="font-mono text-brand text-sm tracking-widest uppercase mb-6">Error {{ $code }}</p>
        <h1 class="text-4xl md:text-5xl font-black leading-tight mb-6">{{ $heading }}</h1>
        <p class="text-white/70 text-xl mb-10 max-w-xl mx-auto">{{ $slot }}</p>
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="{{ $primaryHref }}"
               class="bg-brand hover:bg-brand-dark text-white font-semibold px-8 py-4 rounded text-lg transition-colors">
                {{ $primaryLabel }}
            </a>
            <a href="/"
               class="border border-white/30 hover:border-white text-white font-semibold px-8 py-4 rounded text-lg transition-colors">
                Back Home
            </a>
        </div>
    </div>
</section>
