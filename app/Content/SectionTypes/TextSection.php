<?php

declare(strict_types=1);

namespace App\Content\SectionTypes;

use Filament\Forms\Components\Textarea;

/** Heading + plain text (e.g. mission). */
final class TextSection extends SectionType
{
    public static function label(): string
    {
        return 'Heading and text';
    }

    public static function form(): array
    {
        return [
            Fields::heading(),
            Textarea::make('text')->rows(4)->maxLength(1500),
        ];
    }
}
