<?php

declare(strict_types=1);

namespace App\Content\SectionTypes;

/** Image gallery: heading + all images in the section's collection. */
final class GallerySection extends SectionType
{
    public static function label(): string
    {
        return 'Gallery';
    }

    public static function form(): array
    {
        return [
            Fields::heading(required: false),
        ];
    }

    public static function maxImages(): int
    {
        return 24;
    }

    public static function imagesHelp(): ?string
    {
        return 'Drag to reorder. Alt text uses the heading.';
    }
}
