<?php

namespace App\Mail;

use App\Models\Intake;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * The receipt the client gets. It goes to whatever address was typed into the
 * form, so it carries no free text from the submission — only fixed copy and
 * values from the form's allowlists. Otherwise anyone could use the form to
 * send their own words, from this domain, to an address of their choosing.
 */
class IntakeConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Intake $intake,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Got your intake form — {$this->intake->service}",
            replyTo: array_filter([config('mail.intake_notify_address')]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.intake-confirmation',
        );
    }
}
