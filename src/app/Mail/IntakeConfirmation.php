<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IntakeConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $name,
        public readonly string $service,
        public readonly string $body,
        public readonly array $details = [],
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Got your intake form — {$this->service}",
            replyTo: [config('mail.intake_notify_address')],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.intake-confirmation',
        );
    }
}
