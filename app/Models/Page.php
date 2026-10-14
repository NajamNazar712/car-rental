<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasLocalizedTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

final class Page extends Model implements HasMedia
{
    use HasLocalizedTranslations, InteractsWithMedia;

    public const KEYS = ['home', 'about', 'services', 'faq', 'contact', 'fleet', 'booking', 'privacy', 'terms'];

    /** @var list<string> */
    public array $translatable = ['title', 'meta_title', 'meta_description'];

    protected $fillable = ['key', 'title', 'meta_title', 'meta_description'];

    public function sections(): HasMany
    {
        return $this->hasMany(PageSection::class)->orderBy('sort_order');
    }

    public function visibleSections(): HasMany
    {
        return $this->sections()->where('is_visible', true);
    }

    public function section(string $key): ?PageSection
    {
        return $this->sections->firstWhere('key', $key);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('og_image')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('og')->fit(Fit::Crop, 1200, 630)->format('jpg');
    }

    /** Load a page by key with its visible sections and their media. */
    public static function findByKey(string $key): ?self
    {
        return self::query()
            ->where('key', $key)
            ->with(['visibleSections.media', 'media'])
            ->first();
    }
}
