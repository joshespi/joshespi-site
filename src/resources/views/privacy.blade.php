@extends('layouts.app')

@section('title', 'Privacy Policy')
@section('meta_description', 'Privacy policy for joshespi.com.')

@section('content')

<section class="py-20 px-6">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-4xl font-black mb-2">Privacy Policy</h1>
        <p class="text-muted text-sm mb-12">Last updated: {{ date('F j, Y') }}</p>

        <div class="prose prose-sm max-w-none space-y-8 text-muted leading-relaxed">

            <div>
                <h2 class="text-xl font-bold text-ink mb-3">What I collect</h2>
                <p>When you submit the intake form, I collect the information you provide: your name, email address, and project details. I do not collect any other personal data automatically, and I do not use tracking cookies or analytics.</p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-ink mb-3">How I use it</h2>
                <p>Your information is used only to respond to your inquiry and scope the project you described. I do not sell, rent, or share your data with third parties. Email notifications are sent via Brevo (formerly Sendinblue); they process the email in transit and are subject to their own privacy policy.</p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-ink mb-3">Data retention</h2>
                <p>Intake submissions are retained in email only for as long as needed to complete the engagement or determine that no engagement will proceed. I do not maintain a marketing list and will not contact you unless you initiated the conversation.</p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-ink mb-3">Your rights</h2>
                <p>You can request that I delete any information you submitted by emailing <a href="mailto:intake@joshespi.com" class="text-brand underline">intake@joshespi.com</a>. I'll confirm deletion within 7 business days.</p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-ink mb-3">Contact</h2>
                <p>Questions about this policy: <a href="mailto:intake@joshespi.com" class="text-brand underline">intake@joshespi.com</a></p>
            </div>

        </div>
    </div>
</section>

@endsection
