<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Concerns;

use App\Models\Car;

/**
 * Fleet widgets show only to users allowed to list cars (their role's "ViewAny:Car" permission;
 * super admins always). Avoids separate widget permissions in the role editor.
 */
trait RequiresFleetAccess
{
    public static function canView(): bool
    {
        return (bool) auth()->user()?->can('viewAny', Car::class);
    }
}
