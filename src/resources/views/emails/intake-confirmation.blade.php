<x-mail.layout heading="Thanks, {{ $name }} — I've got your intake form.">

    <p style="line-height: 1.6;">
        I read every one of these personally. You'll get a written response within <strong>1 business day</strong> with a
        clear scope and a fixed price — or a follow-up question if I need more detail before I can quote it accurately.
    </p>

    <p style="line-height: 1.6;">No calls, no sales pitch. Just the scope and the number, in writing.</p>

    <h3 style="margin-bottom: 8px; margin-top: 32px;">What you sent</h3>

    <x-mail.details-table :rows="array_merge(['Service' => $service], $details)" />

    <p style="background: #f8f9fa; padding: 16px; border-radius: 4px; white-space: pre-wrap; margin: 0;">{{ $body }}</p>

    <p style="line-height: 1.6; margin-top: 24px;">
        Something missing or wrong? Just reply to this email — it comes straight to me.
    </p>

    <x-slot:footer>
        <p style="color: #888; font-size: 13px; margin: 0;">
            Josh Espinoza — Full Stack Engineer &middot; <a href="https://joshespi.com" style="color: #B52929;">joshespi.com</a>
        </p>
    </x-slot:footer>
</x-mail.layout>
