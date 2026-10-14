<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

final class ContactSettings extends Settings
{
    /** @var list<string> */
    public array $phones;

    /** @var list<string> */
    public array $emails;

    public ?string $address;

    public ?string $map_embed_url;

    /** Items: {day, hours}. */
    public array $working_hours;

    public static function group(): string
    {
        return 'contact';
    }
}
