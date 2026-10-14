<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Spatie\Translatable\HasTranslations;

/**
 * spatie/laravel-translatable, but array/JSON serialisation returns the value for the current
 * locale (with fallback) instead of every translation. This lets Filament forms fill and save
 * translatable JSON columns per-locale without a translatable plugin; writes already go to the
 * current locale via setAttribute(). Use getTranslations() when all locales are needed.
 */
trait HasLocalizedTranslations
{
    use HasTranslations;

    public function attributesToArray(): array
    {
        $attributes = parent::attributesToArray();

        foreach ($this->getTranslatableAttributes() as $key) {
            if (array_key_exists($key, $attributes)) {
                $attributes[$key] = $this->getTranslation($key, $this->getLocale(), $this->useFallbackLocale());
            }
        }

        return $attributes;
    }
}
