<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasLocalizedTranslations;
use App\Models\Concerns\PreventsDeletionWhenUsedByCars;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

final class Category extends Model implements HasMedia
{
    use HasLocalizedTranslations, InteractsWithMedia, PreventsDeletionWhenUsedByCars;

    /** @var list<string> */
    public array $translatable = ['name', 'description'];

    protected $fillable = ['name', 'slug', 'description', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('icon')->singleFile();
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }
}
