<?php

declare(strict_types=1);

namespace App\Content\SectionTypes;

/** Page intro: heading, rich text, optional image (first image). */
final class IntroSection extends SectionType
{
    public static function label(): string
    {
        return 'Intro';
    }

    public static function form(): array
    {
        return [
            Fields::heading(),
            Fields::richText('body'),
        ];
    }

    public static function maxImages(): int
    {
        return 1;
    }

    public static function imagesHelp(): ?string
    {
        return 'Optional image shown beside the text.';
    }
}
