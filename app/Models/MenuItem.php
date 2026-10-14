<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MenuLocation;
use App\Models\Concerns\HasLocalizedTranslations;
use App\Services\MenuService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

final class MenuItem extends Model
{
    use HasLocalizedTranslations;

    /** @var list<string> */
    public array $translatable = ['label'];

    protected $fillable = ['location', 'label', 'url', 'sort_order', 'is_active', 'opens_new_tab'];

    protected function casts(): array
    {
        return [
            'location' => MenuLocation::class,
            'is_active' => 'boolean',
            'opens_new_tab' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        self::saved(fn () => MenuService::flush());
        self::deleted(fn () => MenuService::flush());
    }

    public function isExternal(): bool
    {
        return Str::startsWith($this->url, ['http://', 'https://', 'mailto:', 'tel:', '#']);
    }

    /** Internal paths are stored without locale ("/fleet") and get the current locale prefixed. */
    public function href(?string $locale = null): string
    {
        if ($this->isExternal()) {
            return $this->url;
        }

        $locale ??= app()->getLocale();

        return url($locale.'/'.ltrim($this->url, '/'));
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
