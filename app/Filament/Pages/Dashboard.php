<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

final class Dashboard extends BaseDashboard
{
    public function getTitle(): string
    {
        return __('admin.dashboard.welcome', ['name' => auth()->user()?->name]);
    }

    public function getSubheading(): string
    {
        return now()->translatedFormat('l, j F Y');
    }

    /** @return int|array<string, int|null> */
    public function getColumns(): int|array
    {
        return ['default' => 1, 'lg' => 2];
    }
}
