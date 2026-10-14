<?php

declare(strict_types=1);

namespace App\Content\SectionTypes;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;

/**
 * Reusable field builders for section forms.
 */
final class Fields
{
    public const RICH_TOOLBAR = [['bold', 'italic', 'link'], ['h2', 'h3'], ['bulletList', 'orderedList'], ['undo', 'redo']];

    public static function heading(bool $required = true): TextInput
    {
        return TextInput::make('heading')->maxLength(150)->required($required);
    }

    public static function subheading(): Textarea
    {
        return Textarea::make('subheading')->rows(2)->maxLength(300);
    }

    public static function richText(string $name = 'body'): RichEditor
    {
        return RichEditor::make($name)->toolbarButtons(self::RICH_TOOLBAR);
    }

    public static function button(string $prefix, string $label): Grid
    {
        return Grid::make(2)->schema([
            TextInput::make("{$prefix}_label")->label("{$label} label")->maxLength(40),
            TextInput::make("{$prefix}_url")
                ->label("{$label} link")
                ->maxLength(255)
                ->placeholder('/fleet')
                ->helperText('Internal path without language (e.g. /fleet) or a full URL.'),
        ]);
    }

    public static function icon(): TextInput
    {
        return TextInput::make('icon')
            ->maxLength(100)
            ->placeholder('heroicon-o-shield-check')
            ->helperText('Heroicon name');
    }
}
