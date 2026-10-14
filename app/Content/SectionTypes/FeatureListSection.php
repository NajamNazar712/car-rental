<?php

declare(strict_types=1);

namespace App\Content\SectionTypes;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

/** "Why us": heading + 3–6 items {icon, title, text}. */
final class FeatureListSection extends SectionType
{
    public static function label(): string
    {
        return 'Why choose us';
    }

    public static function form(): array
    {
        return [
            Fields::heading(),
            Fields::subheading(),
            Repeater::make('items')
                ->schema([
                    Fields::icon(),
                    TextInput::make('title')->required()->maxLength(80),
                    Textarea::make('text')->rows(2)->maxLength(250),
                ])
                ->minItems(3)
                ->maxItems(6)
                ->reorderable()
                ->collapsible()
                ->grid(2)
                ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
        ];
    }
}
