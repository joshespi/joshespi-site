@props(['label', 'heading'])
{{-- The light hero the public content pages open with. The slot is the lede. Size and weight come from .page-masthead h1 in app.css. --}}
<section class="page-masthead px-6">
    <div class="studio-wrap">
        <p class="font-mono text-brand text-sm tracking-widest uppercase mb-4">{{ $label }}</p>
        <h1 class="mb-6">{{ $heading }}</h1>
        <p class="text-muted text-xl max-w-2xl">{{ $slot }}</p>
    </div>
</section>
