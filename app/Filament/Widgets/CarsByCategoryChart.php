<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\RequiresFleetAccess;
use App\Models\Category;
use Filament\Widgets\ChartWidget;

final class CarsByCategoryChart extends ChartWidget
{
    use RequiresFleetAccess;

    /** Shared chart palette (brand blue first). */
    public const PALETTE = ['#0f4c81', '#2a9d8f', '#e9c46a', '#f4a261', '#e76f51', '#6d597a', '#457b9d', '#8ab17d', '#b56576', '#264653'];

    protected static ?int $sort = 2;

    protected ?string $maxHeight = '280px';

    public function getHeading(): string
    {
        return __('admin.dashboard.by_category');
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $categories = Category::query()->ordered()->withCount('cars')->get()->where('cars_count', '>', 0)->values();

        return [
            'datasets' => [[
                'data' => $categories->pluck('cars_count')->all(),
                'backgroundColor' => array_slice(array_pad(self::PALETTE, $categories->count(), '#94a3b8'), 0, $categories->count()),
                'borderWidth' => 0,
            ]],
            'labels' => $categories->map(fn (Category $c): string => (string) $c->name)->all(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'cutout' => '65%',
            'plugins' => ['legend' => ['position' => 'bottom']],
            'scales' => ['x' => ['display' => false], 'y' => ['display' => false]],
        ];
    }
}
