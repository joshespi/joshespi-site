<?php

namespace App\Console\Commands;

use App\Enums\IntakeEmail;
use App\Models\Intake;
use Illuminate\Console\Command;

class IntakeList extends Command
{
    protected $signature = 'intake:list
        {--failed : Only intakes with an email that never went out}
        {--limit=20 : How many of the newest to show}';

    protected $description = 'List recent intake submissions and whether their emails went out';

    public function handle(): int
    {
        $intakes = Intake::query()
            ->when($this->option('failed'), fn ($q) => $q->undelivered())
            ->latest('id')
            ->limit((int) $this->option('limit'))
            ->get();

        if ($intakes->isEmpty()) {
            $this->info($this->option('failed') ? 'Every intake email has gone out.' : 'No intakes yet.');

            return self::SUCCESS;
        }

        $this->table(
            ['#', 'Received', 'Name', 'Email', 'Service', 'Status', 'Notification', 'Confirmation'],
            $intakes->map(fn (Intake $intake) => [
                $intake->id,
                $intake->created_at->format('Y-m-d H:i'),
                $intake->name,
                $intake->email,
                $intake->service,
                $intake->status->label(),
                ...array_map(fn (IntakeEmail $email) => match (true) {
                    $intake->wasSent($email)             => 'sent',
                    $intake->emailError($email) !== null => '<fg=red>FAILED</>',
                    default                              => 'not sent',
                }, IntakeEmail::cases()),
            ]),
        );

        return self::SUCCESS;
    }
}
