<?php

namespace App\Filament\Pages\Settings;

use App\Settings\GeneralSettings;
use BackedEnum;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageGeneralSettings extends BaseSettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'General';

    protected static ?string $title = 'General settings';

    protected static ?int $navigationSort = 1;

    protected static string $settings = GeneralSettings::class;

    public function form(Schema $schema): Schema
    {
        $image = fn (string $name, string $label) => FileUpload::make($name)
            ->label($label)
            ->disk('public')
            ->directory('branding')
            ->image()
            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml', 'image/x-icon', 'image/vnd.microsoft.icon'])
            ->maxSize(2048);

        return $schema
            ->components([
                Section::make('Site')
                    ->columns(2)
                    ->schema([
                        TextInput::make('site_name')->required()->maxLength(80),
                        TextInput::make('tagline')->required()->maxLength(160),
                        TextInput::make('currency_code')->required()->length(3)->placeholder('AED'),
                        TextInput::make('currency_symbol')->required()->maxLength(8),
                        TextInput::make('default_phone_country')
                            ->label('Default phone country code')
                            ->required()
                            ->regex('/^\+\d{1,4}$/')
                            ->placeholder('+971'),
                        ColorPicker::make('primary_color')->label('Admin primary colour')->required(),
                        Textarea::make('maintenance_banner')
                            ->helperText('Optional notice shown at the top of every page. Leave empty to hide.')
                            ->rows(2)
                            ->maxLength(300)
                            ->columnSpanFull(),
                    ]),
                Section::make('Branding')
                    ->columns(3)
                    ->schema([
                        $image('logo_light', 'Logo (for light backgrounds)'),
                        $image('logo_dark', 'Logo (for dark backgrounds)'),
                        $image('favicon', 'Favicon')->maxSize(512),
                    ]),
            ]);
    }
}
