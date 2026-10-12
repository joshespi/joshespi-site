<?php

namespace App\Mail;

use App\Models\Intake;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * A heads-up, not the record. The full submission lives in the database and
 * is read in the admin, which this links to.
 */
class IntakeNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Intake $intake,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[joshespi.com] New intake #{$this->intake->id}: {$this->intake->service}",
            replyTo: [$this->intake->email],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.intake',
        );
    }
}
