<?php

declare(strict_types=1);

namespace App\Filament\Concerns;

use App\Models\User;

/**
 * Restricts a Filament resource or page to super admins (navigation + direct URL access).
 * Used for features kept in code but not offered to the client (static website content).
 * These classes are also excluded from Shield (config/filament-shield.php), so no role can
 * be granted their permissions; super admins pass via Shield's gate bypass.
 * To offer a feature to the client again: remove this trait and its Shield exclusion,
 * then run `php artisan db:seed --class=RoleSeeder --force`.
 */
trait SuperAdminOnly
{
    public static function shouldRegisterNavigation(): bool
    {
        return User::currentIsSuperAdmin();
    }

    public static function canAccess(): bool
    {
        return User::currentIsSuperAdmin();
    }
}
