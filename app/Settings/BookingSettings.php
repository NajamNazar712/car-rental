<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

final class BookingSettings extends Settings
{
    public bool $show_estimated_price;

    public int $min_rental_days;

    public ?string $terms_page_url;

    public static function group(): string
    {
        return 'booking';
    }
}
