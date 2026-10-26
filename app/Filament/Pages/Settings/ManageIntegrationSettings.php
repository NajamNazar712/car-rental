<?php

namespace App\Filament\Pages\Settings;

use App\Filament\Concerns\SuperAdminOnly;
use App\Settings\IntegrationSettings;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageIntegrationSettings extends BaseSettingsPage
{
    /** Super admins only: kept in code, not offered to the client (static website content). */
    use SuperAdminOnly;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPuzzlePiece;

    protected static ?string $navigationLabel = 'Integrations';

    protected static ?string $title = 'Integrations';

    protected static ?int $navigationSort = 6;

    protected static string $settings = IntegrationSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Analytics')
                    ->description('Scripts load only after the visitor accepts cookies.')
                    ->columns(3)
                    ->schema([
                        TextInput::make('ga4_id')->label('GA4 measurement ID')->placeholder('G-XXXXXXX')->regex('/^G-[A-Z0-9]+$/'),
                        TextInput::make('gtm_id')->label('Google Tag Manager ID')->placeholder('GTM-XXXXXX')->regex('/^GTM-[A-Z0-9]+$/'),
                        TextInput::make('meta_pixel_id')->label('Meta Pixel ID')->numeric(),
                    ]),
                Section::make('Cloudflare Turnstile (anti-spam)')
                    ->description('Leave both empty to disable the Turnstile check on forms.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('turnstile_site_key')->maxLength(100),
                        TextInput::make('turnstile_secret_key')->password()->revealable()->maxLength(100),
                    ]),
            ]);
    }
}
