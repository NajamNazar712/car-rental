<?php

namespace App\Filament\Pages\Settings;

use App\Settings\SocialSettings;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageSocialSettings extends BaseSettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShare;

    protected static ?string $navigationLabel = 'Social media';

    protected static ?string $title = 'Social media';

    protected static ?int $navigationSort = 4;

    protected static string $settings = SocialSettings::class;

    public function form(Schema $schema): Schema
    {
        $url = fn (string $name, string $label) => TextInput::make($name)->label($label)->url()->maxLength(255);

        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        $url('facebook', 'Facebook'),
                        $url('instagram', 'Instagram'),
                        $url('tiktok', 'TikTok'),
                        $url('x', 'X (Twitter)'),
                        $url('youtube', 'YouTube'),
                        $url('linkedin', 'LinkedIn'),
                    ]),
            ]);
    }
}
