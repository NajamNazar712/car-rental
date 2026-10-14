<?php

namespace App\Filament\Pages\Settings;

use App\Settings\WhatsAppSettings;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageWhatsAppSettings extends BaseSettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $navigationLabel = 'WhatsApp';

    protected static ?string $title = 'WhatsApp settings';

    protected static ?int $navigationSort = 3;

    protected static string $settings = WhatsAppSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Toggle::make('enabled')->label('Show WhatsApp buttons on the website'),
                        Repeater::make('numbers')
                            ->schema([
                                TextInput::make('label')->required()->maxLength(40)->placeholder('Bookings'),
                                TextInput::make('number')
                                    ->required()
                                    ->tel()
                                    ->regex('/^\+?[0-9 ()-]{7,20}$/')
                                    ->placeholder('+971500000000'),
                            ])
                            ->columns(2)
                            ->reorderable()
                            ->helperText('With more than one number, visitors choose which one to chat with.'),
                    ]),
                Section::make('Message templates')
                    ->description('Placeholders: {car}, {year}, {reference}, {url}')
                    ->schema([
                        Textarea::make('template_general')->label('General enquiry')->rows(2)->required()->maxLength(500),
                        Textarea::make('template_car')->label('Car enquiry')->rows(2)->required()->maxLength(500),
                        Textarea::make('template_booking')->label('After booking')->rows(2)->required()->maxLength(500),
                    ]),
            ]);
    }
}
