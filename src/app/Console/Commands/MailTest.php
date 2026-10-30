<?php

namespace App\Console\Commands;

use App\Mail\TestMessage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

class MailTest extends Command
{
    protected $signature = 'mail:test
        {to? : Recipient. Defaults to INTAKE_NOTIFY_ADDRESS}';

    protected $description = 'Send a test email through the configured mailer';

    public function handle(): int
    {
        $to = $this->argument('to') ?: config('mail.intake_notify_address');
        if (! $to) {
            $this->error('No recipient. Pass an address, or set INTAKE_NOTIFY_ADDRESS.');

            return self::INVALID;
        }

        $mailer = config('mail.default');
        $this->line("Mailer: {$mailer}");
        $this->line('From:   ' . config('mail.from.address'));
        $this->line("To:     {$to}");

        try {
            Mail::to($to)->send(new TestMessage);
        } catch (Throwable $e) {
            $this->error('Failed: ' . $e->getMessage());
            if ($e->getPrevious()) {
                $this->line('Cause:  ' . $e->getPrevious()->getMessage());
            }

            return self::FAILURE;
        }

        if ($mailer === 'log') {
            $this->warn('Written to the log, not delivered. Set MAIL_MAILER=smtp to send for real.');
        } else {
            $this->info('Sent.');
        }

        return self::SUCCESS;
    }
}
