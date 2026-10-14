<?php

declare(strict_types=1);

namespace App\Content\SectionTypes;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;

/** Stats strip: repeater {number, label}. */
final class StatsSection extends SectionType
{
    public static function label(): string
    {
        return 'Stats';
    }

    public static function form(): array
    {
        return [
            Fields::heading(required: false),
            Repeater::make('items')
                ->schema([
                    TextInput::make('number')->required()->maxLength(20)->placeholder('10,000+'),
                    TextInput::make('label')->required()->maxLength(60),
                ])
                ->columns(2)
                ->minItems(1)
                ->maxItems(6)
                ->reorderable(),
        ];
    }
}
