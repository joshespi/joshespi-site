<?php

namespace App\Console\Commands;

use App\Models\Intake;
use Illuminate\Console\Command;

class IntakePush extends Command
{
    protected $signature = 'intake:push
        {ids?* : Intake IDs. Omit to push every intake the tracker has not received}';

    protected $description = 'Push intakes to Tradesman Tracker that failed, or push specific ones again';

    public function handle(): int
    {
        if (! Intake::trackerConfigured()) {
            $this->error('Set TRACKER_URL and TRACKER_TOKEN first.');

            return self::FAILURE;
        }

        $ids = $this->argument('ids');
        // Pushing again by id is safe: the tracker de-duplicates on the intake id.
        $intakes = $ids
            ? Intake::findMany($ids)
            : Intake::untracked()->oldest('id')->get();

        if ($ids && $intakes->count() !== count(array_unique($ids))) {
            $missing = array_diff($ids, $intakes->modelKeys());
            $this->warn('No intake with ID: ' . implode(', ', $missing));
        }

        if ($intakes->isEmpty()) {
            $this->info('Nothing to push.');

            return self::SUCCESS;
        }

        $sent = $failed = 0;
        foreach ($intakes as $intake) {
            if ($intake->pushToTracker()) {
                $sent++;
                $this->line("  #{$intake->id}: <info>pushed</info>");
            } else {
                $failed++;
                $this->line("  #{$intake->id}: <error>failed</error> — {$intake->tracker_error}");
            }
        }

        $this->newLine();
        $this->info("{$sent} pushed, {$failed} failed.");

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
