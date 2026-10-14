<?php

namespace App\Filament\Pages\Settings;

use App\Settings\ContactSettings;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageContactSettings extends BaseSettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhone;

    protected static ?string $navigationLabel = 'Contact';

    protected static ?string $title = 'Contact settings';

    protected static ?int $navigationSort = 2;

    protected static string $settings = ContactSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TagsInput::make('phones')->placeholder('+971 50 000 0000')->helperText('Press Enter after each number.'),
                        TagsInput::make('emails')->placeholder('info@example.com')->nestedRecursiveRules(['email']),
                        Textarea::make('address')->rows(2)->maxLength(300)->columnSpanFull(),
                        TextInput::make('map_embed_url')
                            ->label('Map embed URL')
                            ->url()
                            ->maxLength(1000)
                            ->helperText('Google Maps → Share → Embed a map → copy the src URL.')
                            ->columnSpanFull(),
                    ]),
                Section::make('Working hours')
                    ->schema([
                        Repeater::make('working_hours')
                            ->hiddenLabel()
                            ->schema([
                                TextInput::make('day')->required()->maxLength(40),
                                TextInput::make('hours')->required()->maxLength(40),
                            ])
                            ->columns(2)
                            ->reorderable()
                            ->defaultItems(0),
                    ]),
            ]);
    }
}
