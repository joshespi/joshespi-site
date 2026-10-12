<?php

namespace App\Enums;

/** The two emails every intake sends. Each has its own pair of columns. */
enum IntakeEmail: string
{
    case Notification = 'notification';
    case Confirmation = 'confirmation';

    public function label(): string
    {
        return match ($this) {
            self::Notification => 'Notification to you',
            self::Confirmation => 'Confirmation to client',
        };
    }

    public function sentAtColumn(): string
    {
        return "{$this->value}_sent_at";
    }

    public function errorColumn(): string
    {
        return "{$this->value}_error";
    }
}
