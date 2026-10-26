<?php

namespace App\Filament\Pages\Settings;

use App\Settings\BookingSettings;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageBookingSettings extends BaseSettingsPage
{
    /** Hidden from navigation (scope change). Code and stored settings are kept. */
    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = 'Booking';

    protected static ?string $title = 'Booking settings';

    protected static ?int $navigationSort = 7;

    protected static string $settings = BookingSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        Toggle::make('show_estimated_price')->label('Show estimated price on the booking form')->columnSpanFull(),
                        TextInput::make('min_rental_days')->numeric()->minValue(1)->maxValue(365)->required(),
                        TextInput::make('terms_page_url')
                            ->label('Terms page link')
                            ->maxLength(255)
                            ->placeholder('/terms')
                            ->helperText('Internal path without language, or a full URL.'),
                    ]),
            ]);
    }
}
