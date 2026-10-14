<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

final class IntegrationSettings extends Settings
{
    public ?string $ga4_id;

    public ?string $gtm_id;

    public ?string $meta_pixel_id;

    public ?string $turnstile_site_key;

    public ?string $turnstile_secret_key;

    /** @return list<string> */
    public static function encrypted(): array
    {
        return ['turnstile_secret_key'];
    }

    public static function group(): string
    {
        return 'integrations';
    }
}
