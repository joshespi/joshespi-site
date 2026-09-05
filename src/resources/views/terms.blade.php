@extends('layouts.app')

@section('title', 'Terms of Service')
@section('meta_description', 'Terms of service for joshespi.com — fixed-price WordPress, Laravel, and DevOps engagements.')

@section('content')

<section class="py-20 px-6">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-4xl font-black mb-2">Terms of Service</h1>
        <p class="text-muted text-sm mb-12">Last updated: {{ date('F j, Y') }}</p>

        <div class="prose prose-sm max-w-none space-y-8 text-muted leading-relaxed">

            <div>
                <h2 class="text-xl font-bold text-ink mb-3">Agreement</h2>
                <p>These terms apply to any project you engage me (Josh Espinoza, "I", "me") to perform through joshespi.com. Submitting the intake form, approving a written quote, or making a payment means you agree to these terms for that engagement.</p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-ink mb-3">Quotes &amp; scope</h2>
                <p>After you submit an intake form, I respond in writing with a scope of work and a fixed price. Before any deposit is requested, you're welcome to ask questions and request adjustments — the goal is to get scope and price right while it's still just words on a page. Once you approve a quote, that's the definitive scope for the engagement. Work outside that scope is treated as a change request: I'll send an updated quote in writing before doing it, and no additional charges apply without your written approval.</p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-ink mb-3">Payment</h2>
                <p>Fixed-price projects are billed 50% upfront to begin work and 50% on delivery, via a Stripe payment link included in your quote email. Work starts once the first payment clears. Maintenance retainers are billed monthly in advance and can be cancelled anytime effective at the end of the current billing period.</p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-ink mb-3">Timelines</h2>
                <p>Turnaround estimates given in a quote are good-faith estimates, not guarantees. Delays caused by missing access, credentials, content, or approvals needed from you will extend the timeline accordingly.</p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-ink mb-3">Refunds &amp; cancellation</h2>
                <p>The 50% deposit confirms your approved quote and reserves time on my schedule; it is non-refundable once paid. Because scope and price are worked out in writing before the deposit is requested, changing your mind about the project after paying doesn't entitle you to a refund. If I'm unable to deliver the scope we agreed to, you're entitled to a full refund of the deposit.</p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-ink mb-3">Ownership</h2>
                <p>Once the final payment is received, you own the deliverables built specifically for your project (custom code, configuration, content produced for you). Any third-party software, plugins, themes, or libraries used remain licensed under their own terms — I don't grant rights I don't hold. I may keep and reuse general-purpose code, scripts, and techniques that aren't specific to your project.</p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-ink mb-3">Warranty &amp; liability</h2>
                <p>I stand behind the work I deliver and will fix defects in what I built at no charge if reported within 14 days of delivery. Beyond that, work is provided as-is. I'm not liable for issues caused by third-party services (hosting, plugins, APIs), changes you or others make after delivery, or events outside my control. My total liability for any engagement is limited to the amount you paid for that engagement, except for damages caused by gross negligence or willful misconduct.</p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-ink mb-3">Changes to these terms</h2>
                <p>I may update these terms from time to time; the version in effect when you approve a quote applies to that engagement.</p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-ink mb-3">Contact</h2>
                <p>Questions about these terms: <a href="mailto:intake@joshespi.com" class="text-brand underline">intake@joshespi.com</a></p>
            </div>

        </div>
    </div>
</section>

@endsection
