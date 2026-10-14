<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Transmission: string implements HasLabel
{
    case Automatic = 'automatic';
    case Manual = 'manual';

    public function getLabel(): string
    {
        return __('enums.Transmission.'.$this->value);
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c): array => [$c->value => $c->getLabel()])->all();
    }
}
