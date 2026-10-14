<?php

declare(strict_types=1);

namespace App\Content\SectionTypes;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

/** "How it works": heading + 3–4 steps {title, text}. */
final class StepsSection extends SectionType
{
    public static function label(): string
    {
        return 'How it works';
    }

    public static function form(): array
    {
        return [
            Fields::heading(),
            Fields::subheading(),
            Repeater::make('steps')
                ->schema([
                    TextInput::make('title')->required()->maxLength(80),
                    Textarea::make('text')->rows(2)->maxLength(250),
                ])
                ->minItems(3)
                ->maxItems(4)
                ->reorderable()
                ->collapsible()
                ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
        ];
    }
}
