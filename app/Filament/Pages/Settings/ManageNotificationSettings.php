<?php

namespace App\Filament\Pages\Settings;

use App\Settings\NotificationSettings;
use BackedEnum;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageNotificationSettings extends BaseSettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBell;

    protected static ?string $navigationLabel = 'Notifications';

    protected static ?string $title = 'Notification settings';

    protected static ?int $navigationSort = 5;

    protected static string $settings = NotificationSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TagsInput::make('booking_recipients')
                            ->label('Booking & contact form recipients')
                            ->placeholder('bookings@example.com')
                            ->nestedRecursiveRules(['email'])
                            ->helperText('New booking requests and contact messages are emailed to these addresses.'),
                        Toggle::make('send_customer_confirmation')->label('Email customers a confirmation when they submit a booking'),
                    ]),
            ]);
    }
}
