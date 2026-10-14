<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum BookingStatus: string implements HasColor, HasLabel
{
    case New = 'new';
    case Contacted = 'contacted';
    case Confirmed = 'confirmed';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Rejected = 'rejected';

    public function getLabel(): string
    {
        return __('enums.BookingStatus.'.$this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::New => 'info',
            self::Contacted => 'warning',
            self::Confirmed => 'primary',
            self::Completed => 'success',
            self::Cancelled, self::Rejected => 'danger',
        };
    }

    /**
     * Allowed next statuses: linear flow, plus cancel/reject from any non-final status.
     *
     * @return list<self>
     */
    public function allowedTransitions(): array
    {
        $next = match ($this) {
            self::New => [self::Contacted, self::Confirmed],
            self::Contacted => [self::Confirmed],
            self::Confirmed => [self::Completed],
            default => [],
        };

        if (in_array($this, [self::Completed, self::Cancelled, self::Rejected], true)) {
            return $next;
        }

        return [...$next, self::Cancelled, self::Rejected];
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c): array => [$c->value => $c->getLabel()])->all();
    }
}
