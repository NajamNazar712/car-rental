<?php

declare(strict_types=1);

namespace App\Content;

use App\Content\SectionTypes\CtaSection;
use App\Content\SectionTypes\FeatureListSection;
use App\Content\SectionTypes\GallerySection;
use App\Content\SectionTypes\HeadingSection;
use App\Content\SectionTypes\HeroSection;
use App\Content\SectionTypes\IntroSection;
use App\Content\SectionTypes\RichTextSection;
use App\Content\SectionTypes\SectionType;
use App\Content\SectionTypes\StatsSection;
use App\Content\SectionTypes\StepsSection;
use App\Content\SectionTypes\TextSection;

/**
 * Maps a section `type` (stored on page_sections and matching the Blade component
 * resources/views/components/sections/{type}.blade.php) to its admin form definition.
 */
final class SectionTypeRegistry
{
    /** @var array<string, class-string<SectionType>> */
    private const TYPES = [
        'hero' => HeroSection::class,
        'quick_search' => HeadingSection::class,
        'featured_cars' => HeadingSection::class,
        'categories' => HeadingSection::class,
        'why_us' => FeatureListSection::class,
        'how_it_works' => StepsSection::class,
        'brands' => HeadingSection::class,
        'testimonials' => HeadingSection::class,
        'cta' => CtaSection::class,
        'intro' => IntroSection::class,
        'stats' => StatsSection::class,
        'text' => TextSection::class,
        'gallery' => GallerySection::class,
        'services_grid' => HeadingSection::class,
        'faq_list' => HeadingSection::class,
        'contact_form' => HeadingSection::class,
        'rich_text' => RichTextSection::class,
    ];

    /** @return class-string<SectionType>|null */
    public function get(string $type): ?string
    {
        return self::TYPES[$type] ?? null;
    }

    /** @return array<string, string> type => label */
    public function options(): array
    {
        return collect(self::TYPES)
            ->map(fn (string $class, string $type): string => $class::label().' ('.$type.')')
            ->all();
    }
}
