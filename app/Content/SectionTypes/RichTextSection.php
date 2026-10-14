<?php

declare(strict_types=1);

namespace App\Content\SectionTypes;

/** Heading + long rich text (legal pages, rental terms). */
final class RichTextSection extends SectionType
{
    public static function label(): string
    {
        return 'Rich text';
    }

    public static function form(): array
    {
        return [
            Fields::heading(required: false),
            Fields::richText('body')->required(),
        ];
    }
}
