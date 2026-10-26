<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Dashboard;
use App\Support\Branding;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->passwordReset()
            ->profile(isSimple: false)
            ->brandName(fn (): string => Branding::siteName())
            ->brandLogo(fn (): ?string => Branding::logoUrl())
            ->darkModeBrandLogo(fn (): ?string => Branding::logoUrl(dark: true))
            ->brandLogoHeight('2.25rem')
            ->font('Plus Jakarta Sans', provider: GoogleFontProvider::class)
            ->sidebarWidth('16rem')
            ->renderHook(PanelsRenderHook::HEAD_END, fn (): View => view('filament.admin.styles'))
            ->renderHook(PanelsRenderHook::SIMPLE_LAYOUT_START, fn (): View => view('filament.admin.auth-hero'))
            ->renderHook(PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, fn (): View => view('filament.admin.auth-footer'))
            ->favicon(fn (): ?string => Branding::faviconUrl())
            ->colors(fn (): array => [
                'primary' => Color::hex(Branding::primaryColor()),
            ])
            ->navigationGroups([
                NavigationGroup::make('Fleet'),
                NavigationGroup::make('Bookings'),
                NavigationGroup::make('Website Content'),
                NavigationGroup::make('Settings'),
                NavigationGroup::make('Users & Access'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->plugins([
                FilamentShieldPlugin::make()
                    ->navigationGroup('Users & Access'),
            ])
            ->databaseNotifications(false)
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
