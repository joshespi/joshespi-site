<?php

namespace App\Console\Commands;

use App\Enums\IntakeEmail;
use App\Models\Intake;

class IntakeResend extends RetryIntakes
{
    protected $signature = 'intake:resend
        {ids?* : Intake IDs. Omit to retry every intake with an email that never went out}
        {--only= : Just one email: "notification" or "confirmation"}
        {--force : Send even if that email already went out}';

    protected $description = 'Retry intake emails that failed, or resend specific ones';

    public function handle(): int
    {
        $only = IntakeEmail::tryFrom($this->option('only') ?? '');
        if ($this->option('only') !== null && ! $only) {
            $this->error('--only must be "notification" or "confirmation".');

            return self::INVALID;
        }

        $emails = $only ? [$only] : IntakeEmail::cases();
        $force = $this->option('force');
        $ids = $this->argument('ids');

        return $this->retryIntakes(
            $ids,
            Intake::undelivered(),
            static function (Intake $intake) use ($emails, $force): iterable {
                foreach ($emails as $email) {
                    if ($intake->wasSent($email) && ! $force) {
                        continue;
                    }

                    $sent = $intake->deliver($email);
                    yield ["#{$intake->id} {$email->value}", $sent, $intake->emailError($email)];
                }
            },
            'sent',
            $ids
                ? 'Nothing to send — those already went out. Add --force to send them again.'
                : 'Nothing to retry — every intake email has gone out.',
        );
    }
}
