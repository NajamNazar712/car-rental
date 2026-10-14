<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

final class SocialSettings extends Settings
{
    public ?string $facebook;

    public ?string $instagram;

    public ?string $tiktok;

    public ?string $x;

    public ?string $youtube;

    public ?string $linkedin;

    public static function group(): string
    {
        return 'social';
    }
}
