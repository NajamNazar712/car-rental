<?php

declare(strict_types=1);

namespace App\Content\SectionTypes;

use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Component;

/**
 * A page section type: its admin form (fields stored in content[locale]), defaults,
 * and whether it uses the section's ordered `images` media collection.
 * The public Blade component is resources/views/components/sections/{type}.blade.php.
 */
abstract class SectionType
{
    abstract public static function label(): string;

    /** @return array<int, Component|Field> */
    abstract public static function form(): array;

    /** @return array<string, mixed> */
    public static function defaults(): array
    {
        return [];
    }

    /** Max images in the `images` collection; 0 = section has no images. */
    public static function maxImages(): int
    {
        return 0;
    }

    public static function imagesHelp(): ?string
    {
        return null;
    }
}
