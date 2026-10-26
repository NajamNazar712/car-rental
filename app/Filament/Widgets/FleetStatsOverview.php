<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\CarStatus;
use App\Filament\Resources\Cars\CarResource;
use App\Filament\Widgets\Concerns\RequiresFleetAccess;
use App\Models\Brand;
use App\Models\Car;
use App\Models\Category;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

final class FleetStatsOverview extends StatsOverviewWidget
{
    use RequiresFleetAccess;

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = null;

    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        $byStatus = Car::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $total = (int) $byStatus->sum();
        $active = (int) ($byStatus[CarStatus::Active->value] ?? 0);
        $maintenance = (int) ($byStatus[CarStatus::Maintenance->value] ?? 0);
        $featured = Car::query()->where('is_featured', true)->count();

        return [
            Stat::make(__('admin.dashboard.total_cars'), $total)
                ->description(__('admin.dashboard.total_cars_hint', ['inactive' => (int) ($byStatus[CarStatus::Inactive->value] ?? 0)]))
                ->descriptionIcon(Heroicon::OutlinedTruck)
                ->color('primary')
                ->url(CarResource::getUrl()),
            Stat::make(__('admin.dashboard.active_cars'), $active)
                ->description(__('admin.dashboard.active_share', ['percent' => $total > 0 ? round($active / $total * 100) : 0]))
                ->descriptionIcon(Heroicon::OutlinedCheckCircle)
                ->color('success')
                ->chart($this->trend($active, $total))
                ->url($this->carsUrl(['status' => ['value' => CarStatus::Active->value]])),
            Stat::make(__('admin.dashboard.maintenance_cars'), $maintenance)
                ->description($maintenance > 0 ? __('admin.dashboard.maintenance_hint') : __('admin.dashboard.all_good'))
                ->descriptionIcon(Heroicon::OutlinedWrenchScrewdriver)
                ->color($maintenance > 0 ? 'warning' : 'gray')
                ->url($this->carsUrl(['status' => ['value' => CarStatus::Maintenance->value]])),
            Stat::make(__('admin.dashboard.featured_cars'), $featured)
                ->description(__('admin.dashboard.featured_hint'))
                ->descriptionIcon(Heroicon::OutlinedStar)
                ->color('info')
                ->url($this->carsUrl(['is_featured' => ['value' => '1']])),
            Stat::make(__('admin.dashboard.categories'), Category::query()->where('is_active', true)->count())
                ->description(__('admin.dashboard.categories_hint'))
                ->descriptionIcon(Heroicon::OutlinedSquares2x2)
                ->color('gray'),
            Stat::make(__('admin.dashboard.brands'), Brand::query()->where('is_active', true)->count())
                ->description(__('admin.dashboard.brands_hint'))
                ->descriptionIcon(Heroicon::OutlinedTag)
                ->color('gray'),
        ];
    }

    /** @param array<string, mixed> $filters */
    private function carsUrl(array $filters): string
    {
        return CarResource::getUrl().'?'.http_build_query(['filters' => $filters]);
    }

    /**
     * Small decorative sparkline rising to the active share.
     *
     * @return list<int>
     */
    private function trend(int $value, int $total): array
    {
        if ($total === 0) {
            return [0, 0];
        }

        $share = (int) round($value / $total * 10);

        return [max(0, $share - 3), max(0, $share - 2), max(0, $share - 2), max(0, $share - 1), $share];
    }
}
