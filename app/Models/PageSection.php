<?php

declare(strict_types=1);

namespace App\Models;

use App\Content\SectionTypeRegistry;
use App\Content\SectionTypes\SectionType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * A block on a CMS page. `content` is locale-keyed JSON: {"en": {...}, "ar": {...}}.
 * Images live in the `images` media collection (ordered).
 */
final class PageSection extends Model implements HasMedia
{
    use InteractsWithMedia, LogsActivity;

    protected $fillable = ['page_id', 'key', 'type', 'content', 'localized_content', 'sort_order', 'is_visible'];

    /** Exposed so admin forms can edit the current locale's content as a flat array. */
    protected $appends = ['localized_content'];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'is_visible' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Content value for the current locale, falling back to English, then $default.
     * Supports dot notation, e.g. get('slides.0.heading').
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $content = $this->content ?? [];
        $fallback = config('app.fallback_locale', 'en');

        $value = data_get($content[app()->getLocale()] ?? [], $key);

        if (blank($value)) {
            $value = data_get($content[$fallback] ?? [], $key);
        }

        return blank($value) ? $default : $value;
    }

    /**
     * Current locale content (falls back to English, then the section type defaults).
     * Writing replaces only the current locale's content.
     */
    protected function localizedContent(): Attribute
    {
        return Attribute::make(
            get: function (): array {
                $content = $this->content ?? [];

                return $content[app()->getLocale()]
                    ?? $content[config('app.fallback_locale', 'en')]
                    ?? $this->sectionType()?->defaults()
                    ?? [];
            },
            set: function (?array $value): array {
                $content = $this->content ?? [];
                $content[app()->getLocale()] = $value ?? [];

                return ['content' => json_encode($content)];
            },
        );
    }

    /** @return class-string<SectionType>|null */
    public function sectionType(): ?string
    {
        return app(SectionTypeRegistry::class)->get($this->type);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        foreach (['thumb' => 400, 'card' => 800, 'large' => 1920] as $name => $width) {
            $this->addMediaConversion($name)
                ->fit(Fit::Max, $width, $width * 2)
                ->format('webp')
                ->quality(80);
        }
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['key', 'type', 'content', 'sort_order', 'is_visible'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('page_section');
    }
}
