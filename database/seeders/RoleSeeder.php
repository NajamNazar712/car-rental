<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Keeps roles and permissions in sync with the admin screens. It never assigns permissions:
 * - creates permissions for every admin screen (Shield),
 * - deletes permissions of super-admin-only screens (Shield `exclude` lists),
 * - makes sure the super_admin role exists (the only hardcoded role).
 * Permissions are granted to roles manually in Users & Access → Roles.
 * super_admin passes every check via Shield's gate bypass and needs no permissions.
 */
final class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Artisan::call('shield:generate', ['--all' => true, '--panel' => 'admin', '--option' => 'permissions', '--no-interaction' => true]);

        $this->deleteSuperAdminOnlyPermissions();

        Role::findOrCreate(User::ROLE_SUPER_ADMIN, 'web');

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Removes permissions for resources/pages excluded from Shield (super-admin-only),
     * so no role keeps them and they never show in the role editor.
     */
    private function deleteSuperAdminOnlyPermissions(): void
    {
        $subjects = [
            ...array_map(
                fn (string $resource): string => class_basename($resource::getModel()),
                config('filament-shield.resources.exclude', []),
            ),
            ...array_map('class_basename', config('filament-shield.pages.exclude', [])),
        ];

        Permission::query()
            ->get()
            ->filter(fn (Permission $permission): bool => $this->matchesSubject($permission->name, $subjects))
            ->each(fn (Permission $permission) => $permission->delete());
    }

    /**
     * Shield v4 permission names look like "ViewAny:Car"; older style "view_any_car". Support both.
     *
     * @param  list<string>  $subjects
     */
    private function matchesSubject(string $permission, array $subjects): bool
    {
        foreach ($subjects as $subject) {
            if (str_ends_with($permission, ':'.$subject)) {
                return true;
            }

            $snake = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $subject));
            if (preg_match('/_'.preg_quote($snake, '/').'$/', $permission)) {
                return true;
            }
        }

        return false;
    }
}
