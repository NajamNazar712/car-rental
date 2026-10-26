<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\RequiresFleetAccess;
use App\Models\Brand;
use Filament\Widgets\ChartWidget;

final class CarsByBrandChart extends ChartWidget
{
    use RequiresFleetAccess;

    protected static ?int $sort = 3;

    protected ?string $maxHeight = '280px';

    public function getHeading(): string
    {
        return __('admin.dashboard.by_brand');
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $brands = Brand::query()->withCount('cars')->get()->where('cars_count', '>', 0)->sortByDesc('cars_count')->take(10)->values();

        return [
            'datasets' => [[
                'label' => __('admin.dashboard.cars'),
                'data' => $brands->pluck('cars_count')->all(),
                'backgroundColor' => array_slice(array_pad(CarsByCategoryChart::PALETTE, $brands->count(), '#94a3b8'), 0, $brands->count()),
                'borderRadius' => 6,
                'maxBarThickness' => 40,
            ]],
            'labels' => $brands->pluck('name')->all(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => false]],
            'scales' => [
                'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]],
                'x' => ['grid' => ['display' => false]],
            ],
        ];
    }
}
