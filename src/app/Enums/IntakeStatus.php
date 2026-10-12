<?php

namespace App\Enums;

enum IntakeStatus: string
{
    case New        = 'new';
    case Reviewing  = 'reviewing';
    case Quoted     = 'quoted';
    case Accepted   = 'accepted';
    case InProgress = 'in_progress';
    case Done       = 'done';
    case Declined   = 'declined';
    case Spam       = 'spam';

    public function label(): string
    {
        return match ($this) {
            self::InProgress => 'In progress',
            default          => ucfirst($this->value),
        };
    }

    /** Tailwind classes for the admin badge. */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::New                        => 'bg-brand text-white',
            self::Reviewing, self::Quoted    => 'bg-amber-100 text-amber-900',
            self::Accepted, self::InProgress => 'bg-blue-100 text-blue-900',
            self::Done                       => 'bg-green-100 text-green-900',
            self::Declined, self::Spam       => 'bg-gray-200 text-gray-700',
        };
    }
}
