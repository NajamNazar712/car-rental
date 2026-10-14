<?php

declare(strict_types=1);

namespace App\Content\SectionTypes;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

final class HeroSection extends SectionType
{
    public static function label(): string
    {
        return 'Hero slider';
    }

    public static function form(): array
    {
        return [
            Repeater::make('slides')
                ->schema([
                    TextInput::make('heading')->required()->maxLength(120),
                    Textarea::make('subheading')->rows(2)->maxLength(250),
                    Fields::button('primary', 'Primary button'),
                    Toggle::make('show_whatsapp')->label('Show WhatsApp button')->default(true),
                    TextInput::make('whatsapp_label')->label('WhatsApp button label')->maxLength(40),
                ])
                ->minItems(1)
                ->maxItems(5)
                ->reorderable()
                ->collapsible()
                ->itemLabel(fn (array $state): ?string => $state['heading'] ?? null)
                ->helperText('Slide images are taken from "Images" below in the same order (image 1 → slide 1).'),
        ];
    }

    public static function maxImages(): int
    {
        return 5;
    }

    public static function imagesHelp(): ?string
    {
        return 'One image per slide, in slide order. Recommended 1920×1080.';
    }

    public static function defaults(): array
    {
        return [
            'slides' => [[
                'heading' => 'Rent your perfect car',
                'subheading' => 'Premium and economy cars delivered to your door.',
                'primary_label' => 'Browse fleet',
                'primary_url' => '/fleet',
                'show_whatsapp' => true,
                'whatsapp_label' => 'Chat on WhatsApp',
            ]],
        ];
    }
}
