<?php

declare(strict_types=1);

namespace App\Content\SectionTypes;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

/** Call to action: heading, text, background image (first image), buttons. */
final class CtaSection extends SectionType
{
    public static function label(): string
    {
        return 'Call to action';
    }

    public static function form(): array
    {
        return [
            Fields::heading(),
            Textarea::make('text')->rows(3)->maxLength(400),
            Fields::button('primary', 'Primary button'),
            Toggle::make('show_whatsapp')->label('Show WhatsApp button')->default(true),
            TextInput::make('whatsapp_label')->label('WhatsApp button label')->maxLength(40),
        ];
    }

    public static function maxImages(): int
    {
        return 1;
    }

    public static function imagesHelp(): ?string
    {
        return 'Background image. Recommended 1920×800.';
    }
}
