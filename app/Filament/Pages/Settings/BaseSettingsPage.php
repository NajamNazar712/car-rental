<?php

declare(strict_types=1);

namespace App\Filament\Pages\Settings;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\SettingsPage;
use UnitEnum;

/**
 * Shared behaviour for all settings pages: grouped under "Settings", permission-guarded by Shield.
 * Saving is logged and caches are cleared by the SettingsSaved listener in AppServiceProvider.
 */
abstract class BaseSettingsPage extends SettingsPage
{
    use HasPageShield;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';
}
