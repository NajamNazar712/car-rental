<?php

declare(strict_types=1);

namespace App\Content\SectionTypes;

/**
 * Heading + subheading; the rest of the section is driven by records
 * (featured cars, categories, brands, testimonials, services, FAQs, quick search, contact form).
 */
final class HeadingSection extends SectionType
{
    public static function label(): string
    {
        return 'Heading';
    }

    public static function form(): array
    {
        return [
            Fields::heading(),
            Fields::subheading(),
        ];
    }

    public static function defaults(): array
    {
        return ['heading' => '', 'subheading' => ''];
    }
}
