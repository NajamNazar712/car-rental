<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum AddonPriceType: string implements HasLabel
{
    case PerDay = 'per_day';
    case PerBooking = 'per_booking';

    public function getLabel(): string
    {
        return __('enums.AddonPriceType.'.$this->value);
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c): array => [$c->value => $c->getLabel()])->all();
    }
}
