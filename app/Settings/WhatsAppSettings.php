<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

final class WhatsAppSettings extends Settings
{
    public bool $enabled;

    /** Items: {label, number}. */
    public array $numbers;

    public string $template_general;

    public string $template_car;

    public string $template_booking;

    public static function group(): string
    {
        return 'whatsapp';
    }
}
