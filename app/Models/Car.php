<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CarStatus;
use App\Enums\FuelType;
use App\Enums\Transmission;
use App\Models\Concerns\HasLocalizedTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

final class Car extends Model implements HasMedia
{
    use HasLocalizedTranslations, InteractsWithMedia, LogsActivity, SoftDeletes;

    public const GALLERY_MAX = 15;

    /** @var list<string> */
    public array $translatable = [
        'name', 'short_description', 'description', 'rental_terms', 'meta_title', 'meta_description',
    ];

    protected $fillable = [
        'category_id', 'brand_id', 'car_model_id', 'name', 'slug', 'year', 'transmission', 'fuel_type',
        'seats', 'doors', 'luggage', 'engine', 'colour', 'mileage_limit_per_day',
        'price_day', 'price_week', 'price_month', 'offer_price_day', 'deposit',
        'short_description', 'description', 'rental_terms', 'status', 'is_featured', 'sort_order',
        'meta_title', 'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'status' => CarStatus::class,
            'transmission' => Transmission::class,
            'fuel_type' => FuelType::class,
            'is_featured' => 'boolean',
            'year' => 'integer',
            'seats' => 'integer',
            'doors' => 'integer',
            'luggage' => 'integer',
            'mileage_limit_per_day' => 'integer',
            'sort_order' => 'integer',
            'price_day' => 'decimal:2',
            'price_week' => 'decimal:2',
            'price_month' => 'decimal:2',
            'offer_price_day' => 'decimal:2',
            'deposit' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function carModel(): BelongsTo
    {
        return $this->belongsTo(CarModel::class);
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('main')->singleFile();
        $this->addMediaCollection('gallery');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        foreach (['thumb' => 400, 'card' => 800, 'large' => 1600] as $name => $width) {
            $this->addMediaConversion($name)
                ->fit(Fit::Max, $width, $width * 2)
                ->format('webp')
                ->quality(80)
                ->performOnCollections('main', 'gallery');
        }
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('car');
    }

    /** Price customers actually pay per day. */
    public function effectiveDayPrice(): string
    {
        return $this->offer_price_day ?? $this->price_day;
    }

    public function isBookable(): bool
    {
        return $this->status === CarStatus::Active;
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('status', CarStatus::Active);
    }

    /** Publicly visible: active or in maintenance (detail page shows a notice for maintenance). */
    public function scopePubliclyVisible(Builder $query): void
    {
        $query->whereIn('status', [CarStatus::Active, CarStatus::Maintenance]);
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderByDesc('id');
    }
}
