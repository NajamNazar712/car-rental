<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

final class NotificationSettings extends Settings
{
    /** @var list<string> */
    public array $booking_recipients;

    public bool $send_customer_confirmation;

    public static function group(): string
    {
        return 'notifications';
    }
}
