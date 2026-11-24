<?php

namespace App\Console\Commands;

use App\Models\Intake;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

abstract class RetryIntakes extends Command
{
    /**
     * @param  array<int|string>|null  $ids  Explicit IDs, or empty to use $pending
     * @param  Builder<Intake>  $pending
     * @param  callable(Intake): iterable<array{string, bool, ?string}>  $attempts
     */
    protected function retryIntakes(?array $ids, Builder $pending, callable $attempts, string $successVerb, string $emptyMessage): int
    {
        $intakes = $ids ? Intake::findMany($ids) : $pending->oldest('id')->get();

        if ($ids && $intakes->count() !== count(array_unique($ids))) {
            $missing = array_diff($ids, $intakes->modelKeys());
            $this->warn('No intake with ID: ' . implode(', ', $missing));
        }

        $succeeded = $failed = 0;
        foreach ($intakes as $intake) {
            foreach ($attempts($intake) as [$label, $success, $error]) {
                if ($success) {
                    $succeeded++;
                    $this->line("  {$label}: <info>{$successVerb}</info>");
                } else {
                    $failed++;
                    $this->line("  {$label}: <error>failed</error> — {$error}");
                }
            }
        }

        if ($succeeded + $failed === 0) {
            $this->info($emptyMessage);

            return self::SUCCESS;
        }

        $this->newLine();
        $this->info("{$succeeded} {$successVerb}, {$failed} failed.");

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
