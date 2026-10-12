<x-mail.layout heading="New intake #{{ $intake->id }}: {{ $intake->service }}">

    {{-- Reply-To is the sender, so the address here is reference, not a link. --}}
    <x-mail.details-table :rows="[
        'Name'     => $intake->name,
        'Email'    => $intake->email,
        'Service'  => $intake->service,
        'Budget'   => $intake->budget,
        'Timeline' => $intake->timeline,
    ]" />

    <p style="margin: 0;">
        <a href="{{ route('admin.intakes.show', $intake) }}"
           style="display: inline-block; background: #B52929; color: #fff; text-decoration: none; font-weight: bold; padding: 12px 20px; border-radius: 4px;">
            Read the full intake &rarr;
        </a>
    </p>

    <x-slot:footer>
        <p style="color: #888; font-size: 13px; margin: 0;">Sent from joshespi.com intake form. Reply-To is set to the sender's email.</p>
    </x-slot:footer>
</x-mail.layout>
