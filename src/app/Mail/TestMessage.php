<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Sent by `php artisan mail:test` to prove the mail config works end to end.
 * Fixed copy only, and never queued: the command wants the real SMTP result.
 */
class TestMessage extends Mailable
{
    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Test email from ' . config('app.name'));
    }

    public function content(): Content
    {
        return new Content(
            htmlString: '<p>If you can read this, outbound mail from ' . e(config('app.url')) . ' is working.</p>',
        );
    }
}
