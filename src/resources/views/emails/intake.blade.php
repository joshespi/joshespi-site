<x-mail.layout heading="New intake: {{ $service }}">

    {{-- Reply-To is the sender, so the address here is reference, not a link. --}}
    <x-mail.details-table :rows="array_merge([
        'Name'    => $name,
        'Email'   => $email,
        'Service' => $service,
    ], $details)" />

    <h3 style="margin-bottom: 8px;">Project details</h3>
    <p style="background: #f8f9fa; padding: 16px; border-radius: 4px; white-space: pre-wrap; margin: 0;">{{ $body }}</p>

    <x-slot:footer>
        <p style="color: #888; font-size: 13px; margin: 0;">Sent from joshespi.com intake form. Reply-To is set to the sender's email.</p>
    </x-slot:footer>
</x-mail.layout>
