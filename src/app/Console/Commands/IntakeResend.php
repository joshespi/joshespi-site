<?php

namespace App\Console\Commands;

use App\Enums\IntakeEmail;
use App\Models\Intake;
use Illuminate\Console\Command;

class IntakeResend extends Command
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

        $ids = $this->argument('ids');
        $intakes = $ids
            ? Intake::findMany($ids)
            : Intake::undelivered()->oldest('id')->get();

        if ($ids && $intakes->count() !== count(array_unique($ids))) {
            $missing = array_diff($ids, $intakes->modelKeys());
            $this->warn('No intake with ID: ' . implode(', ', $missing));
        }

        $emails = $only ? [$only] : IntakeEmail::cases();
        $sent = $failed = 0;

        foreach ($intakes as $intake) {
            foreach ($emails as $email) {
                if ($intake->wasSent($email) && ! $this->option('force')) {
                    continue;
                }

                if ($intake->deliver($email)) {
                    $sent++;
                    $this->line("  #{$intake->id} {$email->value}: <info>sent</info>");
                } else {
                    $failed++;
                    $this->line("  #{$intake->id} {$email->value}: <error>failed</error> — {$intake->emailError($email)}");
                }
            }
        }

        if ($sent + $failed === 0) {
            $this->info($ids
                ? 'Nothing to send — those already went out. Add --force to send them again.'
                : 'Nothing to retry — every intake email has gone out.');

            return self::SUCCESS;
        }

        $this->newLine();
        $this->info("{$sent} sent, {$failed} failed.");

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
