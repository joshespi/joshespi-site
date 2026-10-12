{{-- Only fixed copy and allowlisted values belong here; see IntakeConfirmation. --}}
<x-mail.layout heading="Thanks — I've got your intake form.">

    <p style="line-height: 1.6;">
        I read every one of these personally. You'll get a written response within <strong>1 business day</strong> with a
        clear scope and a fixed price — or a follow-up question if I need more detail before I can quote it accurately.
    </p>

    <p style="line-height: 1.6;">No calls, no sales pitch. Just the scope and the number, in writing.</p>

    <h3 style="margin-bottom: 8px; margin-top: 32px;">What you asked about</h3>

    <x-mail.details-table :rows="[
        'Service'  => $intake->service,
        'Budget'   => $intake->budget,
        'Timeline' => $intake->timeline,
    ]" />

    <p style="line-height: 1.6;">
        Something missing or wrong? Just reply to this email — it comes straight to me.
    </p>

    <x-slot:footer>
        <p style="color: #888; font-size: 13px; margin: 0;">
            Josh Espinoza — Full Stack Engineer &middot; <a href="https://joshespi.com" style="color: #B52929;">joshespi.com</a>
        </p>
    </x-slot:footer>
</x-mail.layout>
