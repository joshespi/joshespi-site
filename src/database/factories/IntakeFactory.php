<?php

namespace Database\Factories;

use App\Livewire\IntakeForm;
use App\Models\Intake;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Intake>
 */
class IntakeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'                 => fake()->name(),
            'email'                => fake()->safeEmail(),
            'url'                  => fake()->url(),
            'service'              => IntakeForm::OTHER_SERVICE,
            'budget'               => fake()->randomElement(IntakeForm::BUDGETS),
            'timeline'             => fake()->randomElement(IntakeForm::TIMELINES),
            'message'              => fake()->paragraph(),
            'notification_sent_at' => now(),
            'confirmation_sent_at' => now(),
        ];
    }

    /** Both emails failed, the way a dead SMTP relay leaves a row. */
    public function undelivered(): static
    {
        return $this->state([
            'notification_sent_at' => null,
            'notification_error'   => 'Connection refused',
            'confirmation_sent_at' => null,
            'confirmation_error'   => 'Connection refused',
        ]);
    }
}
