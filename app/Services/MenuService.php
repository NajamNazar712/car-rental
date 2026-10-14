<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\MenuLocation;
use App\Models\MenuItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Cached menu lookups. Cache is cleared whenever a menu item is saved or deleted.
 */
final class MenuService
{
    private const CACHE_KEY = 'menus.all';

    /** @return Collection<int, MenuItem> */
    public function for(MenuLocation $location): Collection
    {
        return $this->all()->where('location', $location)->values();
    }

    /** @return Collection<int, MenuItem> */
    public function all(): Collection
    {
        return Cache::rememberForever(self::CACHE_KEY, fn (): Collection => MenuItem::query()->active()->ordered()->get());
    }

    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
