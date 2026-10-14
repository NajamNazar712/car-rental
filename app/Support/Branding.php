<?php

declare(strict_types=1);

namespace App\Support;

use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Safe accessors for branding settings (falls back to config before settings are migrated).
 */
final class Branding
{
    public static function settings(): ?GeneralSettings
    {
        try {
            return app(GeneralSettings::class);
        } catch (Throwable) {
            return null;
        }
    }

    public static function siteName(): string
    {
        return self::settings()?->site_name ?: (string) config('app.name');
    }

    public static function primaryColor(): string
    {
        return self::settings()?->primary_color ?: '#0f4c81';
    }

    public static function logoUrl(bool $dark = false): ?string
    {
        $settings = self::settings();
        $path = $dark ? ($settings?->logo_dark ?: $settings?->logo_light) : $settings?->logo_light;

        return $path ? Storage::disk('public')->url($path) : null;
    }

    public static function faviconUrl(): ?string
    {
        $path = self::settings()?->favicon;

        return $path ? Storage::disk('public')->url($path) : null;
    }
}
