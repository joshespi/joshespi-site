<?php

namespace App\Console\Commands;

use App\Models\Intake;

class IntakePush extends RetryIntakes
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

        // Pushing again by id is safe: the tracker de-duplicates on the intake id.
        return $this->retryIntakes(
            $this->argument('ids'),
            Intake::untracked(),
            static function (Intake $intake): iterable {
                $pushed = $intake->pushToTracker();

                return [["#{$intake->id}", $pushed, $intake->tracker_error]];
            },
            'pushed',
            'Nothing to push.',
        );
    }
}
