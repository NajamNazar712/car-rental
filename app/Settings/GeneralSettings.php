<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

final class GeneralSettings extends Settings
{
    public string $site_name;

    public string $tagline;

    public string $currency_code;

    public string $currency_symbol;

    public ?string $maintenance_banner;

    public ?string $logo_light;

    public ?string $logo_dark;

    public ?string $favicon;

    public string $primary_color;

    public string $default_phone_country;

    public static function group(): string
    {
        return 'general';
    }
}
